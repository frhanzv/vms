<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title><?= esc($pageTitle ?? 'QR Scanner — SafeG') ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: '#137fec',
                        'primary-hover': '#0f66be',
                        success: '#10b981',
                        danger: '#ef4444',
                        warning: '#f59e0b',
                    },
                    fontFamily: { sans: ['Montserrat', 'sans-serif'] },
                },
            },
        };
    </script>
    <!-- html5-qrcode for camera scanning -->
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <style>
        #qr-reader { width: 100% !important; }
        #qr-reader video { border-radius: 0.75rem; }
        #qr-reader img { display: none; }
        .result-enter  { animation: slideIn .3s ease; }
        @keyframes slideIn { from { opacity:0; transform:translateY(12px); } to { opacity:1; transform:translateY(0); } }
    </style>
</head>
<body class="bg-slate-950 text-white font-sans min-h-screen flex flex-col">

<!-- ── Header ─────────────────────────────────────────────── -->
<header class="bg-slate-900 border-b border-slate-800 px-4 py-3 flex items-center justify-between">
    <div class="flex items-center gap-2">
        <span class="material-symbols-outlined text-primary text-2xl">shield_person</span>
        <span class="font-bold text-lg tracking-tight">SafeG <span class="text-slate-400 font-normal text-sm">QR Scanner</span></span>
    </div>

    <div class="flex items-center gap-3">
        <!-- Lane selector -->
        <select id="laneSelect"
                class="bg-slate-800 border border-slate-700 text-white text-sm rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary focus:outline-none">
            <option value="">— No lane —</option>
            <?php foreach ($lanes as $lane): ?>
            <option value="<?= esc($lane['id']) ?>"
                    data-scan-type="<?= esc($lane['scan_type'] ?? '') ?>">
                <?= esc($lane['lane']) ?>
                <?php if (!empty($lane['scan_type'])): ?>
                    (<?= esc(ucwords(str_replace('_', ' ', $lane['scan_type']))) ?>)
                <?php endif; ?>
            </option>
            <?php endforeach; ?>
        </select>

        <!-- Entry / Exit toggle -->
        <div class="flex rounded-lg overflow-hidden border border-slate-700 text-sm">
            <button id="btnEntry"
                    onclick="setLaneType('entry')"
                    class="px-3 py-2 bg-primary text-white font-semibold transition-colors">
                Entry
            </button>
            <button id="btnExit"
                    onclick="setLaneType('exit')"
                    class="px-3 py-2 bg-slate-800 text-slate-300 hover:bg-slate-700 transition-colors">
                Exit
            </button>
            <button id="btnAuto"
                    onclick="setLaneType('auto')"
                    class="px-3 py-2 bg-slate-800 text-slate-300 hover:bg-slate-700 transition-colors">
                Auto
            </button>
        </div>

        <!-- Clock -->
        <span id="clock" class="text-slate-400 text-sm tabular-nums w-20 text-right"></span>
    </div>
</header>

<!-- ── Main ───────────────────────────────────────────────── -->
<main class="flex-1 p-4 grid grid-cols-1 md:grid-cols-2 gap-4 max-w-6xl mx-auto w-full">

    <!-- ── LEFT: Scanner panel ──────────────────────────── -->
    <div class="flex flex-col gap-4">

        <!-- Mode tabs -->
        <div class="flex rounded-xl overflow-hidden border border-slate-700 text-sm font-semibold">
            <button id="tabCamera"
                    onclick="switchMode('camera')"
                    class="flex-1 flex items-center justify-center gap-2 py-2.5 bg-primary text-white transition-colors">
                <span class="material-symbols-outlined text-base">photo_camera</span> Camera
            </button>
            <button id="tabUsb"
                    onclick="switchMode('usb')"
                    class="flex-1 flex items-center justify-center gap-2 py-2.5 bg-slate-800 text-slate-300 hover:bg-slate-700 transition-colors">
                <span class="material-symbols-outlined text-base">usb</span> USB Scanner
            </button>
        </div>

        <!-- Camera panel -->
        <div id="cameraPanel" class="flex flex-col gap-3">
            <div id="qr-reader" class="rounded-xl overflow-hidden bg-slate-900 border border-slate-700 min-h-[260px]"></div>
            <p class="text-xs text-slate-500 text-center">Point the camera at the visitor's QR code</p>
        </div>

        <!-- USB HID panel -->
        <div id="usbPanel" class="hidden flex flex-col gap-3 items-center justify-center">
            <div class="rounded-xl bg-slate-900 border border-slate-700 w-full p-8 flex flex-col items-center gap-5">
                <span class="material-symbols-outlined text-6xl text-primary">qr_code_scanner</span>
                <p class="text-slate-300 font-semibold text-center">USB scanner ready</p>
                <p class="text-sm text-slate-500 text-center">Scan a visitor's QR code with the USB handheld scanner.<br>The code will be captured automatically.</p>
                <div class="w-full max-w-xs">
                    <input id="usbInput"
                           type="text"
                           placeholder="Or type QR code and press Enter"
                           class="w-full bg-slate-800 border border-slate-600 rounded-lg px-4 py-3 text-white text-sm focus:ring-2 focus:ring-primary focus:outline-none text-center"/>
                </div>
            </div>
        </div>

        <!-- Manual override button (always visible) -->
        <div class="flex gap-2">
            <input id="manualInput"
                   type="text"
                   placeholder="Manual entry: VIS-3 or IC number…"
                   class="flex-1 bg-slate-800 border border-slate-700 rounded-lg px-4 py-2.5 text-white text-sm focus:ring-2 focus:ring-primary focus:outline-none"/>
            <button onclick="submitManual()"
                    class="bg-primary hover:bg-primary-hover text-white text-sm font-semibold px-4 py-2.5 rounded-lg transition-colors flex items-center gap-1.5">
                <span class="material-symbols-outlined text-base">send</span> Scan
            </button>
        </div>
    </div>

    <!-- ── RIGHT: Result panel ───────────────────────────── -->
    <div class="flex flex-col gap-4">

        <!-- Status banner -->
        <div id="statusBanner"
             class="rounded-2xl border-2 border-slate-700 bg-slate-900 p-6 flex flex-col items-center gap-3 min-h-[180px] justify-center">
            <span class="material-symbols-outlined text-5xl text-slate-600">qr_code_2</span>
            <p class="text-slate-500 text-sm">Waiting for QR code…</p>
        </div>

        <!-- Visitor info card -->
        <div id="visitorCard" class="hidden rounded-2xl bg-slate-900 border border-slate-700 p-5 space-y-3 result-enter">
            <div class="flex items-start justify-between gap-3">
                <div class="size-12 rounded-full bg-primary/20 flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-2xl text-primary">person</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p id="vName"  class="font-bold text-lg leading-tight truncate"></p>
                    <p id="vCompany" class="text-sm text-slate-400 truncate"></p>
                </div>
                <span id="vTypeBadge" class="hidden text-xs font-semibold bg-primary/20 text-primary px-2 py-0.5 rounded-full whitespace-nowrap"></span>
            </div>

            <div class="grid grid-cols-2 gap-2 text-sm">
                <div class="bg-slate-800 rounded-lg px-3 py-2">
                    <p class="text-slate-500 text-xs mb-0.5">ID Document</p>
                    <p id="vId" class="font-medium truncate">—</p>
                </div>
                <div class="bg-slate-800 rounded-lg px-3 py-2">
                    <p class="text-slate-500 text-xs mb-0.5">Action</p>
                    <p id="vAction" class="font-semibold capitalize"></p>
                </div>
                <div class="bg-slate-800 rounded-lg px-3 py-2">
                    <p class="text-slate-500 text-xs mb-0.5">Time</p>
                    <p id="vTime" class="font-medium"></p>
                </div>
                <div class="bg-slate-800 rounded-lg px-3 py-2">
                    <p class="text-slate-500 text-xs mb-0.5">Duration</p>
                    <p id="vDuration" class="font-medium">—</p>
                </div>
            </div>
        </div>

        <!-- Recent scans log -->
        <div class="rounded-xl bg-slate-900 border border-slate-700 overflow-hidden">
            <div class="px-4 py-2.5 border-b border-slate-800 flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Recent Scans</span>
                <button onclick="clearLog()" class="text-xs text-slate-600 hover:text-slate-400 transition-colors">Clear</button>
            </div>
            <ul id="scanLog" class="divide-y divide-slate-800 max-h-60 overflow-y-auto text-sm">
                <li class="px-4 py-3 text-slate-600 text-center text-xs">No scans yet</li>
            </ul>
        </div>
    </div>

</main>

<!-- hidden USB input buffer (captures USB HID keyboard events site-wide) -->
<input id="hidBuffer" style="position:fixed;left:-9999px;opacity:0;" tabindex="-1" autocomplete="off"/>

<script>
// ── State ────────────────────────────────────────────────────
let currentMode     = 'camera';
let currentLaneType = 'entry';
let scanLock        = false;
let qrScanner       = null;
let hidTimer        = null;

const BASE_URL = '<?= base_url() ?>';

// ── Clock ────────────────────────────────────────────────────
function updateClock() {
    const now = new Date();
    document.getElementById('clock').textContent =
        now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' });
}
setInterval(updateClock, 1000);
updateClock();

// ── Lane type toggle ─────────────────────────────────────────
function setLaneType(type) {
    currentLaneType = type;
    ['entry','exit','auto'].forEach(t => {
        const btn = document.getElementById('btn' + t.charAt(0).toUpperCase() + t.slice(1));
        if (t === type) {
            btn.classList.add('bg-primary', 'text-white');
            btn.classList.remove('bg-slate-800', 'text-slate-300');
        } else {
            btn.classList.remove('bg-primary', 'text-white');
            btn.classList.add('bg-slate-800', 'text-slate-300');
        }
    });

    // Auto-detect from lane assignment
    if (type === 'auto') {
        const sel  = document.getElementById('laneSelect');
        const opt  = sel.options[sel.selectedIndex];
        const st   = opt ? opt.dataset.scanType : '';
        currentLaneType = st === 'check_out' ? 'exit' : 'entry';
    }
}

// ── Lane change → auto-detect type ──────────────────────────
document.getElementById('laneSelect').addEventListener('change', function () {
    if (currentLaneType === 'auto' || document.getElementById('btnAuto').classList.contains('bg-primary')) {
        const opt = this.options[this.selectedIndex];
        const st  = opt ? opt.dataset.scanType : '';
        currentLaneType = st === 'check_out' ? 'exit' : 'entry';
    }
});

// ── Mode switch ──────────────────────────────────────────────
function switchMode(mode) {
    currentMode = mode;

    document.getElementById('cameraPanel').classList.toggle('hidden', mode !== 'camera');
    document.getElementById('usbPanel').classList.toggle('hidden', mode !== 'usb');

    const camActive = mode === 'camera';
    document.getElementById('tabCamera').classList.toggle('bg-primary',   camActive);
    document.getElementById('tabCamera').classList.toggle('text-white',   camActive);
    document.getElementById('tabCamera').classList.toggle('bg-slate-800', !camActive);
    document.getElementById('tabCamera').classList.toggle('text-slate-300', !camActive);
    document.getElementById('tabUsb').classList.toggle('bg-primary',   !camActive);
    document.getElementById('tabUsb').classList.toggle('text-white',   !camActive);
    document.getElementById('tabUsb').classList.toggle('bg-slate-800', camActive);
    document.getElementById('tabUsb').classList.toggle('text-slate-300', camActive);

    if (mode === 'camera') {
        startCamera();
        document.getElementById('hidBuffer').blur();
    } else {
        stopCamera();
        document.getElementById('hidBuffer').focus();
    }
}

// ── Camera scanner ───────────────────────────────────────────
function startCamera() {
    if (qrScanner) return;
    qrScanner = new Html5Qrcode('qr-reader');

    Html5Qrcode.getCameras().then(devices => {
        if (!devices || devices.length === 0) {
            showBanner('error', 'No camera found', 'Connect a camera or switch to USB mode.');
            return;
        }
        const camId = devices[devices.length - 1].id; // prefer back camera on mobile
        qrScanner.start(
            camId,
            { fps: 10, qrbox: { width: 250, height: 250 } },
            onQrDetected,
            () => {}
        ).catch(err => {
            showBanner('error', 'Camera error', err.toString());
        });
    }).catch(err => {
        showBanner('error', 'Camera access denied', 'Please allow camera permission and refresh.');
    });
}

function stopCamera() {
    if (qrScanner) {
        qrScanner.stop().then(() => { qrScanner = null; }).catch(() => { qrScanner = null; });
    }
}

function onQrDetected(decodedText) {
    if (scanLock) return;
    processQr(decodedText.trim());
}

// ── USB HID keyboard capture ─────────────────────────────────
const hidBuf = document.getElementById('hidBuffer');

// Focus the hidden buffer on page load and whenever user clicks on non-input elements
document.addEventListener('click', function (e) {
    if (!e.target.closest('input, button, select')) {
        hidBuf.focus();
    }
});

hidBuf.addEventListener('keydown', function (e) {
    if (e.key === 'Enter') {
        const val = hidBuf.value.trim();
        hidBuf.value = '';
        clearTimeout(hidTimer);
        if (val.length > 0) processQr(val);
        return;
    }
    // Debounce: if characters stop coming in for 300 ms treat as complete
    clearTimeout(hidTimer);
    hidTimer = setTimeout(() => {
        const val = hidBuf.value.trim();
        hidBuf.value = '';
        if (val.length >= 3) processQr(val);
    }, 300);
});

// USB panel manual input
document.getElementById('usbInput').addEventListener('keydown', function (e) {
    if (e.key === 'Enter') {
        const val = this.value.trim();
        this.value = '';
        if (val.length > 0) processQr(val);
    }
});

// ── Manual entry ─────────────────────────────────────────────
function submitManual() {
    const inp = document.getElementById('manualInput');
    const val = inp.value.trim();
    if (val) { processQr(val); inp.value = ''; }
}
document.getElementById('manualInput').addEventListener('keydown', function (e) {
    if (e.key === 'Enter') submitManual();
});

// ── QR processing ─────────────────────────────────────────────
async function processQr(raw) {
    if (scanLock) return;
    scanLock = true;

    showBanner('loading', 'Scanning…', raw);

    const laneId   = document.getElementById('laneSelect').value;
    const laneType = currentLaneType === 'auto' ? 'entry' : currentLaneType;

    try {
        const params = new URLSearchParams({ qr_code: raw });
        if (laneId)   params.append('lane_id',   laneId);
        if (laneType) params.append('lane_type', laneType);

        const endpoint = laneId
            ? `${BASE_URL}api/qr/scan-lane`
            : `${BASE_URL}api/qr/scan`;

        const res  = await fetch(`${endpoint}?${params}`);
        const data = await res.json();

        renderResult(data);
        addToLog(data, raw);
        playBeep(data.access_granted !== false && data.success);

    } catch (err) {
        showBanner('error', 'Network error', err.toString());
        playBeep(false);
    }

    // Unlock after a short cooldown to avoid double-scans
    setTimeout(() => { scanLock = false; }, 2500);
}

// ── Render result ─────────────────────────────────────────────
function renderResult(data) {
    const banner    = document.getElementById('statusBanner');
    const card      = document.getElementById('visitorCard');
    const accessOk  = data.access_granted !== false;
    const isDenied  = data.action === 'denied';

    // ── Banner ──
    if (!data.success && isDenied) {
        showBanner('denied',
            '🚫 ACCESS DENIED',
            data.message || 'Visitor type not authorised for this lane.',
            data.visitor?.name);
    } else if (!data.success) {
        showBanner('error', 'Scan failed', data.message || 'Unknown error.');
        card.classList.add('hidden');
        return;
    } else if (data.action === 'checkin') {
        showBanner('checkin', '✅ CHECKED IN', `${data.visitor?.name} checked in successfully.`);
    } else if (data.action === 'checkout') {
        showBanner('checkout', '🔓 CHECKED OUT',
            data.duration ? `Visit duration: ${data.duration}` : `${data.visitor?.name} checked out.`);
    } else {
        showBanner('success', '✔ Processed', data.message || '');
    }

    if (!data.visitor) { card.classList.add('hidden'); return; }

    // ── Visitor card ──
    card.classList.remove('hidden');
    card.classList.add('result-enter');
    setTimeout(() => card.classList.remove('result-enter'), 400);

    document.getElementById('vName').textContent    = data.visitor.name    || '—';
    document.getElementById('vCompany').textContent = data.visitor.company || '—';
    document.getElementById('vId').textContent      = data.visitor.id_document || data.visitor.ic_passport || '—';
    document.getElementById('vTime').textContent    = data.time ? new Date(data.time).toLocaleTimeString([], {hour:'2-digit',minute:'2-digit'}) : '—';
    document.getElementById('vDuration').textContent = data.duration || '—';

    const actionEl = document.getElementById('vAction');
    if (isDenied) {
        actionEl.textContent  = 'Denied';
        actionEl.className    = 'font-semibold capitalize text-red-400';
    } else if (data.action === 'checkin') {
        actionEl.textContent  = 'Check In';
        actionEl.className    = 'font-semibold capitalize text-green-400';
    } else if (data.action === 'checkout') {
        actionEl.textContent  = 'Check Out';
        actionEl.className    = 'font-semibold capitalize text-blue-400';
    } else {
        actionEl.textContent  = data.action || '—';
        actionEl.className    = 'font-semibold capitalize';
    }

    const typeBadge = document.getElementById('vTypeBadge');
    if (data.visitor.visitor_type) {
        typeBadge.textContent = data.visitor.visitor_type;
        typeBadge.classList.remove('hidden');
    } else {
        typeBadge.classList.add('hidden');
    }
}

function showBanner(type, title, subtitle, extra) {
    const banner = document.getElementById('statusBanner');

    const styles = {
        loading: { border:'border-slate-700',  bg:'bg-slate-900',     icon:'hourglass_top',    iconColor:'text-slate-400',  titleColor:'text-slate-300' },
        checkin: { border:'border-green-500',  bg:'bg-green-950',     icon:'check_circle',     iconColor:'text-green-400',  titleColor:'text-green-400' },
        checkout:{ border:'border-blue-500',   bg:'bg-blue-950',      icon:'logout',           iconColor:'text-blue-400',   titleColor:'text-blue-400'  },
        denied:  { border:'border-red-500',    bg:'bg-red-950',       icon:'block',            iconColor:'text-red-400',    titleColor:'text-red-400'   },
        error:   { border:'border-orange-500', bg:'bg-orange-950',    icon:'error',            iconColor:'text-orange-400', titleColor:'text-orange-400'},
        success: { border:'border-green-500',  bg:'bg-green-950',     icon:'verified',         iconColor:'text-green-400',  titleColor:'text-green-400' },
    };

    const s = styles[type] || styles.loading;
    banner.className = `rounded-2xl border-2 ${s.border} ${s.bg} p-6 flex flex-col items-center gap-3 min-h-[180px] justify-center transition-all`;
    banner.innerHTML = `
        <span class="material-symbols-outlined text-5xl ${s.iconColor}">${s.icon}</span>
        <p class="text-lg font-bold ${s.titleColor} text-center">${title}</p>
        ${subtitle ? `<p class="text-sm text-slate-400 text-center">${subtitle}</p>` : ''}
        ${extra    ? `<p class="text-base font-semibold text-white text-center">${extra}</p>` : ''}
    `;
}

// ── Recent scan log ───────────────────────────────────────────
function addToLog(data, raw) {
    const log   = document.getElementById('scanLog');
    const empty = log.querySelector('.text-slate-600');
    if (empty) empty.remove();

    const time = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' });
    const name = data.visitor?.name || raw;

    let dotClass, label;
    if (data.action === 'denied' || data.access_granted === false) {
        dotClass = 'bg-red-400';  label = 'Denied';
    } else if (data.action === 'checkin')  {
        dotClass = 'bg-green-400'; label = 'In';
    } else if (data.action === 'checkout') {
        dotClass = 'bg-blue-400';  label = 'Out';
    } else {
        dotClass = 'bg-slate-400'; label = '?';
    }

    const li = document.createElement('li');
    li.className = 'px-4 py-2.5 flex items-center gap-3';
    li.innerHTML = `
        <span class="size-2 rounded-full flex-shrink-0 ${dotClass}"></span>
        <span class="flex-1 truncate text-slate-300">${name}</span>
        <span class="text-xs font-semibold ${label === 'Denied' ? 'text-red-400' : label === 'In' ? 'text-green-400' : 'text-blue-400'}">${label}</span>
        <span class="text-xs text-slate-600 tabular-nums">${time}</span>
    `;

    log.insertBefore(li, log.firstChild);

    // Keep max 20 entries
    while (log.children.length > 20) log.removeChild(log.lastChild);
}

function clearLog() {
    const log = document.getElementById('scanLog');
    log.innerHTML = '<li class="px-4 py-3 text-slate-600 text-center text-xs">No scans yet</li>';
}

// ── Audio feedback ────────────────────────────────────────────
let audioCtx = null;
function playBeep(success) {
    try {
        if (!audioCtx) audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        const osc = audioCtx.createOscillator();
        const gain = audioCtx.createGain();
        osc.connect(gain);
        gain.connect(audioCtx.destination);
        osc.type = 'sine';
        if (success) {
            osc.frequency.setValueAtTime(880, audioCtx.currentTime);
            osc.frequency.linearRampToValueAtTime(1100, audioCtx.currentTime + 0.1);
        } else {
            osc.frequency.setValueAtTime(300, audioCtx.currentTime);
            osc.frequency.linearRampToValueAtTime(200, audioCtx.currentTime + 0.25);
        }
        gain.gain.setValueAtTime(0.3, audioCtx.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + (success ? 0.3 : 0.5));
        osc.start(audioCtx.currentTime);
        osc.stop(audioCtx.currentTime + (success ? 0.3 : 0.5));
    } catch (e) { /* audio not available */ }
}

// ── Init ──────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    startCamera();
    hidBuf.focus();

    // Auto-set lane type from first lane's scan_type
    const sel = document.getElementById('laneSelect');
    if (sel.selectedIndex > 0) {
        const st = sel.options[sel.selectedIndex].dataset.scanType;
        if (st) setLaneType(st === 'check_out' ? 'exit' : 'entry');
    }
});
</script>
</body>
</html>
