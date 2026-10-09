<?php
/**
 * Colour tokens + chart styles + hover tooltip for the Staff / Vendor
 * dashboards. Include once inside <head> and once before </body>:
 *   <?= view('partials/dashboard_viz_assets', ['part' => 'head']) ?>
 *   <?= view('partials/dashboard_viz_assets', ['part' => 'script']) ?>
 *
 * Values are the validated default palette: categorical slots 1-3 (blue,
 * orange, aqua) and the blue ordinal ramp, with a separate dark-mode step set
 * (the app switches dark mode with a `dark` class on <html>).
 */
$part = $part ?? 'head';
?>
<?php if ($part === 'head'): ?>
<style>
    .viz-root {
        --v-ink: #0b0b0b; --v-ink2: #52514e; --v-muted: #898781;
        --v-grid: #e1e0d9; --v-base: #c3c2b7;
        --v-s1: #2a78d6; --v-s2: #eb6834; --v-s3: #1baf7a;
        --v-q1: #86b6ef; --v-q2: #5598e7; --v-q3: #2a78d6; --v-q4: #1c5cab;
    }
    html.dark .viz-root {
        --v-ink: #ffffff; --v-ink2: #c3c2b7; --v-muted: #898781;
        --v-grid: #2c2c2a; --v-base: #383835;
        --v-s1: #3987e5; --v-s2: #d95926; --v-s3: #199e70;
        --v-q1: #86b6ef; --v-q2: #5598e7; --v-q3: #3987e5; --v-q4: #2a78d6;
    }
    .viz-s1 { background: var(--v-s1); } .viz-s2 { background: var(--v-s2); } .viz-s3 { background: var(--v-s3); }

    /* horizontal bars */
    .viz-hbars { display: flex; flex-direction: column; }
    .viz-hbar-row { display: grid; grid-template-columns: minmax(6rem, 11rem) 1fr 2.75rem; align-items: center; gap: .75rem; padding: 6px 4px; border-radius: 6px; }
    .viz-hbar-row:hover, .viz-hbar-row:focus-visible { background: color-mix(in srgb, var(--v-grid) 55%, transparent); outline: none; }
    .viz-hbar-label { font-size: .8125rem; color: var(--v-ink2); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    a.viz-hbar-label { color: var(--v-ink); text-decoration: none; font-weight: 600; }
    a.viz-hbar-label:hover { text-decoration: underline; }
    .viz-hbar-track { height: 10px; background: var(--v-grid); border-radius: 4px; overflow: hidden; }
    .viz-hbar-fill { height: 100%; background: var(--v-s1); border-radius: 0 4px 4px 0; }
    .viz-hbar-fill.viz-tone-1 { background: var(--v-q1); } .viz-hbar-fill.viz-tone-2 { background: var(--v-q2); }
    .viz-hbar-fill.viz-tone-3 { background: var(--v-q3); } .viz-hbar-fill.viz-tone-4 { background: var(--v-q4); }
    .viz-hbar-value { font-size: .8125rem; font-weight: 700; color: var(--v-ink); text-align: right; font-variant-numeric: tabular-nums; }

    /* columns */
    .viz-cols { display: flex; align-items: stretch; gap: 12px; height: 190px; padding-top: 18px; }
    .viz-col { flex: 1 1 0; min-width: 0; display: flex; flex-direction: column; align-items: center; border-radius: 6px; }
    .viz-col:hover, .viz-col:focus-visible { background: color-mix(in srgb, var(--v-grid) 55%, transparent); outline: none; }
    .viz-col-area { position: relative; flex: 1; width: 100%; display: flex; align-items: flex-end; justify-content: center; border-bottom: 1px solid var(--v-base); }
    .viz-col-bar { width: min(100%, 38px); min-height: 2px; background: var(--v-s1); border-radius: 4px 4px 0 0; }
    .viz-col-val { position: absolute; left: 0; right: 0; text-align: center; font-size: .75rem; font-weight: 700; color: var(--v-ink); }
    .viz-col-label { margin-top: 6px; font-size: .6875rem; color: var(--v-muted); white-space: nowrap; }

    /* stacked bar + legend */
    .viz-stack { display: flex; gap: 2px; height: 14px; }
    .viz-stack-seg { min-width: 3px; border-radius: 3px; }
    .viz-stack-seg:focus-visible { outline: 2px solid var(--v-ink); outline-offset: 1px; }
    .viz-legend { display: flex; flex-wrap: wrap; gap: .5rem 1.25rem; margin-top: .85rem; padding: 0; list-style: none; font-size: .8125rem; color: var(--v-ink2); }
    .viz-legend li { display: inline-flex; align-items: center; gap: .4rem; }
    .viz-legend strong { color: var(--v-ink); }
    .viz-legend-pct { color: var(--v-muted); }
    .viz-swatch { width: 10px; height: 10px; border-radius: 2px; display: inline-block; }

    /* tooltip */
    #viz-tip { position: fixed; z-index: 120; pointer-events: none; opacity: 0; transition: opacity .08s; background: #0b0b0b; color: #fff; font-size: .75rem; line-height: 1.3; padding: 6px 10px; border-radius: 6px; box-shadow: 0 4px 14px rgba(0,0,0,.25); max-width: 260px; }
    html.dark #viz-tip { background: #f4f4f2; color: #0b0b0b; }
    .sr-only { position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0,0,0,0); white-space: nowrap; border: 0; }
</style>
<?php else: ?>
<div id="viz-tip" role="tooltip"></div>
<script>
(function () {
    var tip = document.getElementById('viz-tip');
    if (!tip) return;

    function place(x, y) {
        var pad = 14, w = tip.offsetWidth, h = tip.offsetHeight;
        var left = Math.min(x + pad, window.innerWidth - w - 8);
        var top = y - h - pad < 8 ? y + pad : y - h - pad;
        tip.style.left = Math.max(8, left) + 'px';
        tip.style.top = top + 'px';
    }
    function show(el, x, y) { tip.textContent = el.getAttribute('data-tip'); tip.style.opacity = 1; place(x, y); }
    function hide() { tip.style.opacity = 0; }

    document.addEventListener('mouseover', function (e) { var t = e.target.closest('[data-tip]'); if (t) show(t, e.clientX, e.clientY); });
    document.addEventListener('mousemove', function (e) { if (e.target.closest('[data-tip]')) place(e.clientX, e.clientY); });
    document.addEventListener('mouseout', function (e) { if (e.target.closest('[data-tip]')) hide(); });
    document.addEventListener('focusin', function (e) {
        var t = e.target.closest('[data-tip]');
        if (t) { var r = t.getBoundingClientRect(); show(t, r.left + r.width / 2, r.top); }
    });
    document.addEventListener('focusout', hide);
})();
</script>
<?php endif; ?>
