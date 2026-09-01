import React, { useEffect, useMemo, useRef, useState } from "react";
import { createRoot } from "react-dom/client";
import "./styles.css";
import "./occupancy.css";

const COLORS = ["#ddebff", "#fff0c9", "#e8dfff", "#ddf4e5", "#ffe1e1", "#eee7ff"];
const ASSET_ICONS = { reader: "sensors", door: "door_open", camera: "videocam" };
const HAZARD_COLOR = "#f59e0b";
const uid = (prefix) => `${prefix}-${Date.now()}-${Math.random().toString(16).slice(2)}`;
const formatTime = (value) => value ? new Date(value.replace(" ", "T")).toLocaleTimeString("en-MY", { hour: "2-digit", minute: "2-digit", second: "2-digit" }) : "—";
const neonZoneColor = (hex) => {
  const value = String(hex || "").replace("#", "");
  if (!/^[0-9a-f]{6}$/i.test(value)) return "#22d3ee";
  const [r, g, b] = [0, 2, 4].map((offset) => parseInt(value.slice(offset, offset + 2), 16) / 255);
  const max = Math.max(r, g, b), min = Math.min(r, g, b), delta = max - min;
  let hue = 0;
  if (delta) {
    if (max === r) hue = 60 * (((g - b) / delta) % 6);
    else if (max === g) hue = 60 * ((b - r) / delta + 2);
    else hue = 60 * ((r - g) / delta + 4);
  }
  if (hue < 0) hue += 360;
  return `hsl(${Math.round(hue)} 100% 58%)`;
};

function EmapApp({ root }) {
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");
  const [mode, setMode] = useState("live");
  const [map, setMap] = useState(null);
  const [layout, setLayout] = useState({ width: 1000, height: 590, zones: [], walls: [], assets: [], labels: [] });
  const [references, setReferences] = useState({ locations: [], subLocations: [], lanes: [], devices: [] });
  const [visitors, setVisitors] = useState([]);
  const [logs, setLogs] = useState([]);
  const [permissions, setPermissions] = useState({ design: root.dataset.canDesign === "1", publish: root.dataset.canPublish === "1" });
  const [tool, setTool] = useState("select");
  const [selected, setSelected] = useState(null);
  const [hoveredVisitor, setHoveredVisitor] = useState(null);
  const [zoom, setZoom] = useState(1);
  const [notice, setNotice] = useState("");
  const [saving, setSaving] = useState(false);
  const svgRef = useRef(null);
  const drag = useRef(null);

  async function loadBootstrap() {
    try {
      const response = await fetch(root.dataset.bootstrapUrl, { credentials: "same-origin", headers: { Accept: "application/json" } });
      if (!response.ok) throw new Error(`Unable to load E-Map (${response.status})`);
      const json = await response.json();
      const data = json.data;
      setMap(data.map);
      setLayout(data.map.layout);
      setReferences(data.references);
      setVisitors(data.visitors || []);
      setLogs(data.movementLog || []);
      setPermissions(data.permissions || permissions);
      setError("");
    } catch (err) {
      setError(err.message || "Unable to load E-Map.");
    } finally {
      setLoading(false);
    }
  }

  async function refreshLive(silent = false) {
    try {
      const response = await fetch(root.dataset.liveUrl, { credentials: "same-origin", headers: { Accept: "application/json" } });
      if (!response.ok) throw new Error("Live movement refresh failed.");
      const json = await response.json();
      setVisitors(json.data.visitors || []);
      setLogs(json.data.movementLog || []);
      if (!silent) setNotice(`Live data refreshed at ${new Date().toLocaleTimeString("en-MY")}`);
    } catch (err) {
      if (!silent) setNotice(err.message);
    }
  }

  useEffect(() => { loadBootstrap(); }, []);
  useEffect(() => {
    if (mode !== "live") return undefined;
    const timer = window.setInterval(() => refreshLive(true), 10000);
    return () => window.clearInterval(timer);
  }, [mode]);

  const selectedZone = layout.zones.find((item) => item.id === selected);
  const selectedWall = layout.walls.find((item) => item.id === selected);
  const selectedAsset = layout.assets.find((item) => item.id === selected);
  const activeVisitor = visitors.find((item) => String(item.id) === String(hoveredVisitor));

  const visitorAssignments = useMemo(() => {
    const result = {};
    for (const visitor of visitors) {
      const zone = layout.zones.find((item) =>
        (visitor.subLocationId && Number(item.subLocationId) === Number(visitor.subLocationId)) ||
        (!visitor.subLocationId && visitor.locationId && Number(item.locationId) === Number(visitor.locationId))
      );
      if (!zone) continue;
      result[zone.id] ||= [];
      result[zone.id].push(visitor);
    }
    return result;
  }, [visitors, layout.zones]);

  function point(clientX, clientY) {
    const rect = svgRef.current?.getBoundingClientRect();
    if (!rect) return { x: 0, y: 0 };
    return {
      x: ((clientX - rect.left) / rect.width) * layout.width,
      y: ((clientY - rect.top) / rect.height) * layout.height,
    };
  }

  function pointerMove(event) {
    if (!drag.current || mode !== "designer") return;
    const p = point(event.clientX, event.clientY);
    const current = drag.current;
    if (current.kind === "zone-move") {
      setLayout((state) => ({ ...state, zones: state.zones.map((zone) => zone.id === current.id ? {
        ...zone,
        x: Math.max(0, Math.min(state.width - zone.w, p.x - current.dx)),
        y: Math.max(0, Math.min(state.height - zone.h, p.y - current.dy)),
      } : zone) }));
    } else if (current.kind === "zone-resize") {
      setLayout((state) => ({ ...state, zones: state.zones.map((zone) => zone.id === current.id ? {
        ...zone, w: Math.max(80, p.x - zone.x), h: Math.max(60, p.y - zone.y),
      } : zone) }));
    } else if (current.kind === "asset-move") {
      setLayout((state) => ({ ...state, assets: state.assets.map((asset) => asset.id === current.id ? { ...asset, x: p.x - current.dx, y: p.y - current.dy } : asset) }));
    } else if (current.kind === "wall-start" || current.kind === "wall-end") {
      setLayout((state) => ({ ...state, walls: state.walls.map((wall) => wall.id === current.id
        ? current.kind === "wall-start" ? { ...wall, x1: p.x, y1: p.y } : { ...wall, x2: p.x, y2: p.y }
        : wall) }));
    }
  }

  function addZone() {
    const id = uid("zone");
    const zone = { id, name: "NEW ZONE", x: 350, y: 220, w: 210, h: 130, color: COLORS[layout.zones.length % COLORS.length], locationId: null, subLocationId: null };
    setLayout((state) => ({ ...state, zones: [...state.zones, zone] }));
    setSelected(id); setTool("select"); setNotice("Zone added. Drag it or use the blue corner to resize.");
  }

  function addWall() {
    const id = uid("wall");
    setLayout((state) => ({ ...state, walls: [...state.walls, { id, x1: 370, y1: 290, x2: 570, y2: 290 }] }));
    setSelected(id); setTool("select"); setNotice("Wall added. Drag either endpoint.");
  }

  function addAsset(type) {
    const id = uid("asset");
    const labels = { reader: "RFID Reader", door: "Access Door", camera: "Camera / FR" };
    setLayout((state) => ({ ...state, assets: [...state.assets, {
      id, type, label: labels[type], x: 500, y: 280,
      laneId: null, deviceAssignmentId: null,
      fromZoneId: null, toZoneId: null, transitionMode: "bidirectional",
    }] }));
    setSelected(id); setTool("select"); setNotice(`${labels[type]} added.`);
  }

  function updateZone(patch) {
    setLayout((state) => ({ ...state, zones: state.zones.map((zone) => zone.id === selected ? { ...zone, ...patch } : zone) }));
  }

  function updateWall(patch) {
    setLayout((state) => ({ ...state, walls: state.walls.map((wall) => wall.id === selected ? { ...wall, ...patch } : wall) }));
  }

  function updateAsset(patch) {
    setLayout((state) => ({ ...state, assets: state.assets.map((asset) => asset.id === selected ? { ...asset, ...patch } : asset) }));
  }

  function devicesForLane(laneId) {
    if (!laneId) return references.devices;
    const lane = references.lanes.find((item) => Number(item.id) === Number(laneId));
    if (!lane) return [];

    if (lane.rfid_reader_ip) {
      return references.devices.filter((device) => device.ip_address === lane.rfid_reader_ip);
    }

    const subLocationIds = new Set(
      references.subLocations
        .filter((item) => Number(item.location_id) === Number(lane.location_id))
        .map((item) => Number(item.id))
    );
    return references.devices.filter((device) => subLocationIds.has(Number(device.location_id)));
  }

  function updateAssetLane(value) {
    const laneId = value ? Number(value) : null;
    const allowedDevices = devicesForLane(laneId);
    const deviceStillAllowed = allowedDevices.some((device) => Number(device.id) === Number(selectedAsset?.deviceAssignmentId));
    updateAsset({
      laneId,
      deviceAssignmentId: deviceStillAllowed ? selectedAsset.deviceAssignmentId : null,
    });
  }

  function removeSelected() {
    setLayout((state) => ({
      ...state,
      zones: state.zones.filter((item) => item.id !== selected),
      walls: state.walls.filter((item) => item.id !== selected),
      assets: state.assets.filter((item) => item.id !== selected),
    }));
    setSelected(null);
  }

  async function save(publish = false) {
    if (!map || !permissions.design) return;
    setSaving(true);
    setNotice("");
    try {
      const url = root.dataset.mapUrlTemplate.replace("__MAP_ID__", String(map.id));
      const response = await fetch(url, {
        method: "PUT",
        credentials: "same-origin",
        headers: { "Content-Type": "application/json", Accept: "application/json" },
        body: JSON.stringify({ layout, publish, name: map.name, premiseName: map.premiseName, floorName: map.floorName }),
      });
      const json = await response.json();
      if (!response.ok) throw new Error(json.message || "Unable to save map.");
      setMap(json.data.map);
      setLayout(json.data.map.layout);
      setNotice(json.message);
    } catch (err) {
      setNotice(err.message);
    } finally {
      setSaving(false);
    }
  }

  if (loading) return <div className="emap-state"><span className="material-symbols-outlined">map</span><b>Loading E-Map…</b></div>;
  if (error) return <div className="emap-state error"><span className="material-symbols-outlined">error</span><b>{error}</b><button onClick={loadBootstrap}>Try again</button></div>;

  return <div className="emap-app">
    <header className="emap-header">
      <div>
        <p>E-MAP / {map.premiseName.toUpperCase()}</p>
        <h1>{map.floorName}</h1>
      </div>
      <div className="emap-header-actions">
        <div className="emap-tabs">
          <button className={mode === "live" ? "active" : ""} onClick={() => setMode("live")}><span className="material-symbols-outlined">location_on</span>Live Movement</button>
          {permissions.design && <button className={mode === "designer" ? "active" : ""} onClick={() => setMode("designer")}><span className="material-symbols-outlined">draw</span>Map Designer</button>}
        </div>
        {mode === "live"
          ? <button className="emap-primary green" onClick={() => refreshLive(false)}><span className="material-symbols-outlined">refresh</span>Refresh live</button>
          : <><button className="emap-secondary" disabled={saving} onClick={() => save(false)}>Save draft</button>{permissions.publish && <button className="emap-primary" disabled={saving} onClick={() => save(true)}>Publish</button>}</>}
      </div>
    </header>

    <div className="emap-status"><span className="status-dot"/>{notice || (mode === "live" ? "Live movement refreshes every 10 seconds" : "Edit the floor layout, then save or publish")}<span>{map.status} · v{map.version}</span></div>

    <div className={`emap-grid ${mode}`}>
      {mode === "designer" && <aside className="emap-panel emap-tools">
        <div className="panel-heading"><h2>Map tools</h2><span>DESIGN</span></div>
        <button className={tool === "select" ? "active" : ""} onClick={() => setTool("select")}><i>↖</i><span><b>Select</b><small>Move and inspect objects</small></span></button>
        <button onClick={addZone}><i>⬚</i><span><b>Zone</b><small>Create a coloured area</small></span></button>
        <button onClick={addWall}><i>╱</i><span><b>Wall</b><small>Add a structural line</small></span></button>
        <div className="asset-buttons"><b>Add asset</b><button onClick={() => addAsset("door")}><i className="door"><span className="material-symbols-outlined">{ASSET_ICONS.door}</span></i>Access door</button><button onClick={() => addAsset("reader")}><i className="reader"><span className="material-symbols-outlined">{ASSET_ICONS.reader}</span></i>RFID reader</button><button onClick={() => addAsset("camera")}><i className="camera"><span className="material-symbols-outlined">{ASSET_ICONS.camera}</span></i>Camera / FR</button></div>
      </aside>}

      <section className="emap-panel map-card">
        <div className="map-topbar">
          <div><b>{map.name}</b><small>{mode === "live" ? `${visitors.length} visitor${visitors.length === 1 ? "" : "s"} currently mapped` : "Drag, resize and link map objects"}</small></div>
          <div className="zoom"><button onClick={() => setZoom(Math.max(.7, zoom - .1))}>−</button><span>{Math.round(zoom * 100)}%</span><button onClick={() => setZoom(Math.min(1.4, zoom + .1))}>+</button></div>
        </div>
        <div className="emap-canvas">
          <svg ref={svgRef} viewBox={`0 0 ${layout.width} ${layout.height}`} style={{ transform: `scale(${zoom})` }} onPointerMove={pointerMove} onPointerUp={() => drag.current = null} onPointerLeave={() => drag.current = null}>
            {layout.zones.map((zone) => { const zoneLineColor = zone.hazardous ? HAZARD_COLOR : neonZoneColor(zone.color); return <g key={zone.id} className={mode === "designer" ? "movable" : ""} onClick={() => mode === "designer" && setSelected(zone.id)} onPointerDown={(event) => {
              if (mode !== "designer") return;
              const p = point(event.clientX, event.clientY);
              drag.current = { id: zone.id, kind: "zone-move", dx: p.x - zone.x, dy: p.y - zone.y };
              setSelected(zone.id);
            }}>
              <rect
                className="zone-shape"
                x={zone.x}
                y={zone.y}
                width={zone.w}
                height={zone.h}
                rx="4"
                fill={zone.hazardous ? HAZARD_COLOR : "transparent"}
                fillOpacity={zone.hazardous ? .04 : 0}
                stroke={zoneLineColor}
                strokeDasharray={zone.hazardous ? "9 6" : undefined}
                strokeWidth={selected === zone.id && mode === "designer" ? 4 : 2.5}
                style={{ filter: `drop-shadow(0 0 2px ${zoneLineColor}) drop-shadow(0 0 ${selected === zone.id && mode === "designer" ? 7 : 4}px ${zoneLineColor})` }}
              />
              <text x={zone.x + 12} y={zone.y + 24} textAnchor="start" className="zone-name">{zone.name}</text>
              {zone.hazardous && <g className="hazard-label" transform={`translate(${zone.x + zone.w - 18} ${zone.y + 19})`}><text textAnchor="middle" className="hazard-symbol">warning</text><text x="-21" y="3" textAnchor="end" className="hazard-text">HAZARDOUS</text></g>}
              {mode === "designer" && selected === zone.id && <g transform={`translate(${zone.x + zone.w} ${zone.y + zone.h})`} className="handle" onPointerDown={(event) => { event.stopPropagation(); drag.current = { id: zone.id, kind: "zone-resize" }; }}><circle r="10"/><path d="M-4 4L4-4M0 4L4 0"/></g>}
            </g>; })}
            {layout.walls.map((wall) => <g key={wall.id} onClick={() => mode === "designer" && setSelected(wall.id)}>
              <line x1={wall.x1} y1={wall.y1} x2={wall.x2} y2={wall.y2} stroke="#515b68" strokeWidth="9"/>
              <line x1={wall.x1} y1={wall.y1} x2={wall.x2} y2={wall.y2} stroke="#f8fafc" strokeWidth="3"/>
              {mode === "designer" && selected === wall.id && <><circle className="wall-handle" cx={wall.x1} cy={wall.y1} r="10" onPointerDown={(event) => { event.stopPropagation(); drag.current = { id: wall.id, kind: "wall-start" }; }}/><circle className="wall-handle" cx={wall.x2} cy={wall.y2} r="10" onPointerDown={(event) => { event.stopPropagation(); drag.current = { id: wall.id, kind: "wall-end" }; }}/></>}
            </g>)}
            {layout.assets.map((asset) => <g key={asset.id} className="map-asset" transform={`translate(${asset.x} ${asset.y})`} onClick={() => mode === "designer" && setSelected(asset.id)} onPointerDown={(event) => {
              if (mode !== "designer") return;
              const p = point(event.clientX, event.clientY);
              drag.current = { id: asset.id, kind: "asset-move", dx: p.x - asset.x, dy: p.y - asset.y };
              setSelected(asset.id);
            }}>
              <rect x="-17" y="-17" width="34" height="34" rx="8" className={asset.type}/><text y="7" textAnchor="middle" className="asset-symbol">{ASSET_ICONS[asset.type] || "device_unknown"}</text>
            </g>)}
            {mode === "live" && Object.entries(visitorAssignments).flatMap(([zoneId, people]) => {
              const zone = layout.zones.find((item) => item.id === zoneId);
              return people.map((visitor, index) => {
                const x = zone.x + 45 + (index % 3) * 62;
                const y = zone.y + 75 + Math.floor(index / 3) * 70;
                return <g key={visitor.id} className="visitor-marker" transform={`translate(${x} ${y})`} onPointerEnter={() => setHoveredVisitor(String(visitor.id))} onPointerLeave={() => setHoveredVisitor(null)}>
                  <circle r="20"/><text y="5" textAnchor="middle">{visitor.initials}</text><rect x="-40" y="25" width="80" height="20" rx="10"/><text y="39" textAnchor="middle" className="visitor-label">{visitor.name}</text>
                </g>;
              });
            })}
            {mode === "live" && activeVisitor && (() => {
              const zone = layout.zones.find((item) => (visitorAssignments[item.id] || []).some((v) => String(v.id) === String(activeVisitor.id)));
              if (!zone) return null;
              const people = visitorAssignments[zone.id] || [];
              const index = people.findIndex((v) => String(v.id) === String(activeVisitor.id));
              const px = zone.x + 45 + (index % 3) * 62;
              const py = zone.y + 75 + Math.floor(index / 3) * 70;
              const x = px > layout.width - 300 ? px - 265 : px + 32;
              const y = Math.max(16, Math.min(layout.height - 170, py - 60));
              return <g className="visitor-popover" transform={`translate(${x} ${y})`} pointerEvents="none"><rect width="235" height="150" rx="10"/><circle cx="23" cy="23" r="12"/><text x="23" y="27" textAnchor="middle" className="initials">{activeVisitor.initials}</text><text x="43" y="20" className="name">{activeVisitor.name}</text><text x="43" y="34" className="company">{activeVisitor.company}</text><text x="14" y="63" className="label">TIME IN</text><text x="88" y="63" className="value">{formatTime(activeVisitor.timeIn)}</text><text x="14" y="85" className="label">HOST</text><text x="88" y="85" className="value">{activeVisitor.host}</text><text x="14" y="107" className="label">CURRENT ZONE</text><text x="88" y="107" className="value">{zone.name}</text><text x="14" y="129" className="label">LAST DETECTED</text><text x="88" y="129" className="value">{formatTime(activeVisitor.lastSeen)}</text><circle cx="18" cy="142" r="3" className="live-dot"/><text x="27" y="145" className="live-label">Currently in premise</text></g>;
            })()}
          </svg>
          {mode === "live" && visitors.length === 0 && <div className="canvas-empty"><span className="material-symbols-outlined">sensors_off</span><b>No active visitor positions yet</b><small>Visitors appear after a mapped RFID or QR movement event.</small></div>}
        </div>
        <div className="map-legend"><span><i className="reader"><span className="material-symbols-outlined">{ASSET_ICONS.reader}</span></i>RFID reader</span><span><i className="door"><span className="material-symbols-outlined">{ASSET_ICONS.door}</span></i>Access door</span><span><i className="camera"><span className="material-symbols-outlined">{ASSET_ICONS.camera}</span></i>Camera / FR</span><em>Visitor positions show the latest detected zone.</em></div>
      </section>

      {mode === "designer" ? <aside className="emap-panel inspector">
        <div className="panel-heading"><h2>Inspector</h2><span>PROPERTIES</span></div>
        {!selectedZone && !selectedWall && !selectedAsset && <div className="empty-inspector"><span className="material-symbols-outlined">touch_app</span>Select an object on the map</div>}
        {selectedZone && <><label>Zone name<input value={selectedZone.name} onChange={(event) => updateZone({ name: event.target.value.toUpperCase() })}/></label><label>Zone colour<input type="color" value={selectedZone.color} onChange={(event) => updateZone({ color: event.target.value })}/></label><label className="hazard-toggle"><span><input type="checkbox" checked={Boolean(selectedZone.hazardous)} onChange={(event) => updateZone({ hazardous: event.target.checked })}/>Hazardous zone</span><small>Shows an amber warning area in Live mode</small></label><label>Location<select value={selectedZone.locationId || ""} onChange={(event) => updateZone({ locationId: event.target.value ? Number(event.target.value) : null, subLocationId: null })}><option value="">Not linked</option>{references.locations.map((item) => <option key={item.id} value={item.id}>{item.location_access}</option>)}</select></label><label>Sub-location / zone<select value={selectedZone.subLocationId || ""} onChange={(event) => updateZone({ subLocationId: event.target.value ? Number(event.target.value) : null })}><option value="">Not linked</option>{references.subLocations.filter((item) => !selectedZone.locationId || Number(item.location_id) === Number(selectedZone.locationId)).map((item) => <option key={item.id} value={item.id}>{item.name}</option>)}</select></label><div className="field-grid"><label>Width<input type="number" value={Math.round(selectedZone.w)} onChange={(event) => updateZone({ w: Number(event.target.value) })}/></label><label>Height<input type="number" value={Math.round(selectedZone.h)} onChange={(event) => updateZone({ h: Number(event.target.value) })}/></label></div><button className="delete-button" onClick={removeSelected}>Delete zone</button></>}
        {selectedWall && <><div className="object-summary"><i>╱</i><span><b>Structural wall</b><small>Drag either blue endpoint</small></span></div><div className="field-grid"><label>Start X<input type="number" value={Math.round(selectedWall.x1)} onChange={(event) => updateWall({ x1: Number(event.target.value) })}/></label><label>Start Y<input type="number" value={Math.round(selectedWall.y1)} onChange={(event) => updateWall({ y1: Number(event.target.value) })}/></label><label>End X<input type="number" value={Math.round(selectedWall.x2)} onChange={(event) => updateWall({ x2: Number(event.target.value) })}/></label><label>End Y<input type="number" value={Math.round(selectedWall.y2)} onChange={(event) => updateWall({ y2: Number(event.target.value) })}/></label></div><button className="delete-button" onClick={removeSelected}>Delete wall</button></>}
        {selectedAsset && <><label>Asset label<input value={selectedAsset.label} onChange={(event) => updateAsset({ label: event.target.value })}/></label><label>Asset type<select value={selectedAsset.type} onChange={(event) => updateAsset({ type: event.target.value })}><option value="reader">RFID reader</option><option value="door">Access door</option><option value="camera">Camera / FR</option></select></label><label>Lane / door<select value={selectedAsset.laneId || ""} onChange={(event) => updateAssetLane(event.target.value)}><option value="">Not linked</option>{references.lanes.map((item) => <option key={item.id} value={item.id}>{item.lane}</option>)}</select></label><label>Device assignment<select value={selectedAsset.deviceAssignmentId || ""} onChange={(event) => updateAsset({ deviceAssignmentId: event.target.value ? Number(event.target.value) : null })}><option value="">Not linked</option>{devicesForLane(selectedAsset.laneId).length === 0 && selectedAsset.laneId && <option disabled>No devices found in this lane area</option>}{devicesForLane(selectedAsset.laneId).map((item) => <option key={item.id} value={item.id}>{item.device_id || item.ip_address} · {item.type}</option>)}</select></label>{selectedAsset.type !== "camera" && <><label>From zone<select value={selectedAsset.fromZoneId || ""} onChange={(event) => updateAsset({ fromZoneId: event.target.value || null })}><option value="">Not linked</option>{layout.zones.map((zone) => <option key={zone.id} value={zone.id}>{zone.name}</option>)}</select></label><label>To zone<select value={selectedAsset.toZoneId || ""} onChange={(event) => updateAsset({ toZoneId: event.target.value || null })}><option value="">Not linked</option>{layout.zones.map((zone) => <option key={zone.id} value={zone.id}>{zone.name}</option>)}</select></label><label>Movement mode<select value={selectedAsset.transitionMode || "bidirectional"} onChange={(event) => updateAsset({ transitionMode: event.target.value })}><option value="bidirectional">Bidirectional (A ↔ B)</option><option value="one_way">One way (A → B)</option></select></label></>}<button className="delete-button" onClick={removeSelected}>Delete asset</button></>}
      </aside> : <aside className="live-sidebar">
        <section className="emap-panel"><div className="panel-heading"><h2>Zone occupancy</h2><span>LIVE</span></div><div className="occupancy-list">{layout.zones.map((zone) => <div key={zone.id}><i style={{ background: zone.hazardous ? HAZARD_COLOR : neonZoneColor(zone.color) }}/><span>{zone.name}{zone.hazardous ? " · HAZARD" : ""}</span><b>{visitorAssignments[zone.id]?.length || 0}</b></div>)}</div></section>
        <section className="emap-panel"><div className="panel-heading"><h2>Mapped assets</h2><span>{layout.assets.length}</span></div><div className="mapped-assets">{layout.assets.slice(0, 6).map((asset) => <div key={asset.id}><i className={asset.type}><span className="material-symbols-outlined">{ASSET_ICONS[asset.type] || "device_unknown"}</span></i><span><b>{asset.label}</b><small>{asset.deviceAssignmentId ? "Linked device" : "Not linked"}</small></span></div>)}{layout.assets.length === 0 && <p>No assets placed on this map.</p>}</div></section>
      </aside>}
    </div>

    {mode === "live" && <section className="emap-panel movement-log zone-occupancy"><div className="panel-heading"><h2>Zone occupancy</h2><span>{visitors.length} PEOPLE ON MAP</span></div><div className="occupancy-cards">{layout.zones.map((zone) => { const count = visitorAssignments[zone.id]?.length || 0; const subLocation = references.subLocations.find((item) => Number(item.id) === Number(zone.subLocationId)); const zoneLineColor = zone.hazardous ? HAZARD_COLOR : neonZoneColor(zone.color); return <article className={`occupancy-card${zone.hazardous ? " hazardous" : ""}`} key={zone.id} style={{borderTopColor:zoneLineColor}}><div><span className="occupancy-colour" style={{background:zoneLineColor}}/><b>{zone.name}</b></div><strong>{count}</strong><small>{zone.hazardous ? "HAZARDOUS ZONE" : count === 1 ? "PERSON" : "PEOPLE"}</small><p>{subLocation?.name || "Not linked"}</p></article>; })}</div></section>}

    {mode === "live" && <section className="emap-panel movement-log"><div className="panel-heading"><h2>Live movement log</h2><span>LATEST {logs.length}</span></div><div className="log-head"><span>VISITOR</span><span>ZONE / DOOR</span><span>ACTION</span><span>TIME</span></div>{logs.slice(0, 6).map((item) => <div className="log-row" key={item.id}><span><i>{item.name.split(/\s+/).slice(0,2).map((part) => part[0]).join("")}</i><b>{item.name}</b></span><span>{item.zone}</span><span className={`action ${item.action}`}>{item.action.replaceAll("_", " ")}</span><span>{formatTime(item.time)}</span></div>)}{logs.length === 0 && <div className="empty-log">No RFID or QR movement has been recorded yet.</div>}</section>}
  </div>;
}

const rootElement = document.getElementById("emap-root");
if (rootElement) createRoot(rootElement).render(<EmapApp root={rootElement}/>);
