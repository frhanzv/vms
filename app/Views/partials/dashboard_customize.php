<?php
/**
 * "Customize" for the Staff / Vendor dashboards — the same slide-in drawer as
 * the Visitor dashboard: re-order cards (drag handle or arrows), hide / show
 * them with the eye button, Reset, Done.
 *
 *   - everyone arranges THEIR OWN view, from the cards the client allows
 *   - a client superadmin can switch to "Whole client" to set the default for
 *     everybody in that client (and which cards exist for them at all)
 *   - the platform superadmin gets a link to pick a client
 *
 * Usage: <?= view('partials/dashboard_customize', ['dash' => 'staff']) ?>
 */
helper(['dashboard_cards', 'role', 'feature']);

$reg = dash_card_registry()[$dash] ?? null;
if (! $reg) {
    return;
}

$userId   = (int) session()->get('user_id');
$clientId = (int) current_client_id();
$isClientAdmin = is_client_superadmin() && $clientId > 0;
$isPlatform    = is_platform_superadmin();

$build = static function (string $scopeType, int $scopeId, bool $onlyAllowed) use ($dash, $reg, $clientId, $userId) {
    $rows  = dash_card_rows($scopeType, $scopeId, $dash);
    $order = dash_card_order($dash, $clientId, $scopeType === 'client' ? 0 : $userId);
    $items = [];
    foreach ($order as $key) {
        if ($onlyAllowed && ! dash_card_allowed($dash, $key)) {
            continue;
        }
        $items[] = [
            'key'    => $key,
            'label'  => $reg['cards'][$key]['label'],
            'group'  => $reg['cards'][$key]['group'],
            'hidden' => isset($rows[$key]) ? ! $rows[$key]['visible'] : false,
        ];
    }

    return $items;
};

$data = [
    'dash'   => $dash,
    'me'     => $build('user', $userId, true),
    'client' => $isClientAdmin ? $build('client', $clientId, false) : [],
    'canClient' => $isClientAdmin,
    'url'    => base_url('dashboard/cards/mine'),
    'csrf'   => csrf_hash(),
    'csrfHeader' => csrf_header(),
];
if (! $data['me'] && ! $data['canClient']) {
    return;
}
?>
<button type="button" id="dc-open" class="flex h-10 items-center gap-1.5 rounded-lg border border-slate-200 bg-surface-light px-4 text-sm font-medium text-slate-600 shadow-sm transition-colors hover:bg-slate-50 dark:border-slate-700 dark:bg-surface-dark dark:text-slate-300 dark:hover:bg-slate-800">
    <span class="material-symbols-outlined text-[20px]">dashboard_customize</span>
    <span class="hidden sm:inline">Customize</span>
</button>

<div id="dc-overlay" class="fixed inset-0 z-40 hidden bg-black/30"></div>
<aside id="dc-drawer" class="fixed right-0 top-0 z-50 flex h-full w-full max-w-sm translate-x-full flex-col border-l border-slate-200 bg-surface-light shadow-2xl transition-transform duration-300 dark:border-slate-700 dark:bg-surface-dark" aria-label="Customize dashboard" aria-hidden="true">
    <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4 dark:border-slate-700">
        <div class="flex items-center gap-3">
            <span class="material-symbols-outlined text-primary">dashboard_customize</span>
            <h2 class="text-base font-bold leading-tight">Customize<br/>Dashboard</h2>
        </div>
        <button type="button" id="dc-reset" class="flex items-center gap-1 text-sm text-slate-500 hover:text-primary">
            <span class="material-symbols-outlined text-[18px]">restart_alt</span>Reset
        </button>
    </div>

    <div class="space-y-3 border-b border-slate-200 bg-primary/5 px-6 py-3 text-sm text-slate-600 dark:border-slate-700 dark:text-slate-300">
        <p class="flex items-start gap-2"><span class="material-symbols-outlined text-[18px] text-primary">drag_indicator</span>Drag the handle (or use the arrows) to re-order a card</p>
        <p class="flex items-start gap-2"><span class="material-symbols-outlined text-[18px] text-slate-400">visibility_off</span>Click the eye button on a card to hide it</p>
        <?php if ($isClientAdmin): ?>
        <div class="inline-flex rounded-lg border border-slate-200 bg-surface-light p-0.5 text-xs font-semibold dark:border-slate-600 dark:bg-slate-800" role="group" aria-label="Apply changes to">
            <button type="button" data-scope="me" class="dc-scope rounded-md px-3 py-1.5">Just me</button>
            <button type="button" data-scope="client" class="dc-scope rounded-md px-3 py-1.5">Whole client</button>
        </div>
        <p id="dc-scope-note" class="text-xs text-slate-500"></p>
        <?php endif; ?>
        <?php if ($isPlatform): ?>
        <a href="<?= base_url('config/dashboard-cards') ?>" class="inline-flex items-center gap-1 text-xs font-semibold text-primary hover:underline">
            <span class="material-symbols-outlined text-[16px]">tune</span>Set the cards for a whole client
        </a>
        <?php endif; ?>
    </div>

    <div id="dc-list" class="flex-1 space-y-1 overflow-y-auto px-6 py-4"></div>

    <div class="border-t border-slate-200 px-6 py-4 dark:border-slate-700">
        <p id="dc-error" class="mb-2 hidden text-xs text-red-600"></p>
        <button type="button" id="dc-done" class="flex h-11 w-full items-center justify-center gap-2 rounded-lg bg-primary text-sm font-bold text-white hover:bg-primary-dark">
            <span class="material-symbols-outlined text-[20px]">check</span>Done
        </button>
    </div>
</aside>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
(function () {
    var CFG = <?= json_encode($data, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;
    var scope = 'me';
    var state = { me: CFG.me.slice(), client: CFG.client.slice() };
    var drawer = document.getElementById('dc-drawer'), overlay = document.getElementById('dc-overlay'), list = document.getElementById('dc-list');

    function openDrawer() { drawer.classList.remove('translate-x-full'); overlay.classList.remove('hidden'); drawer.setAttribute('aria-hidden', 'false'); }
    function closeDrawer() { drawer.classList.add('translate-x-full'); overlay.classList.add('hidden'); drawer.setAttribute('aria-hidden', 'true'); }
    document.getElementById('dc-open').addEventListener('click', openDrawer);
    overlay.addEventListener('click', closeDrawer);
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') closeDrawer(); });

    function el(tag, cls, html) { var n = document.createElement(tag); if (cls) n.className = cls; if (html != null) n.innerHTML = html; return n; }
    function esc(s) { var d = document.createElement('div'); d.textContent = s; return d.innerHTML; }

    function render() {
        list.innerHTML = '';
        var groups = [];
        state[scope].forEach(function (it) { if (groups.indexOf(it.group) < 0) groups.push(it.group); });
        groups.forEach(function (g) {
            list.appendChild(el('p', 'mb-1 mt-3 text-[11px] font-bold uppercase tracking-wide text-slate-400', esc(g)));
            var box = el('div', 'space-y-2'); box.dataset.group = g;
            state[scope].filter(function (it) { return it.group === g; }).forEach(function (it) {
                var row = el('div', 'flex items-center gap-2 rounded-xl border border-slate-200 bg-surface-light px-3 py-2.5 dark:border-slate-700 dark:bg-slate-800' + (it.hidden ? ' opacity-60' : ''));
                row.dataset.key = it.key;
                row.innerHTML =
                    '<span class="dc-handle material-symbols-outlined cursor-grab text-[20px] text-slate-400" title="Drag to re-order">drag_indicator</span>' +
                    '<span class="flex flex-col"><button type="button" class="dc-up leading-none text-slate-400 hover:text-primary" aria-label="Move up"><span class="material-symbols-outlined text-[18px]">keyboard_arrow_up</span></button>' +
                    '<button type="button" class="dc-down leading-none text-slate-400 hover:text-primary" aria-label="Move down"><span class="material-symbols-outlined text-[18px]">keyboard_arrow_down</span></button></span>' +
                    '<span class="flex-1 text-sm font-medium' + (it.hidden ? ' line-through' : '') + '">' + esc(it.label) + '</span>' +
                    '<button type="button" class="dc-eye text-primary" aria-label="' + (it.hidden ? 'Show' : 'Hide') + ' ' + esc(it.label) + '"><span class="material-symbols-outlined text-[22px]">' + (it.hidden ? 'visibility_off' : 'visibility') + '</span></button>';
                box.appendChild(row);
            });
            list.appendChild(box);
            if (window.Sortable) {
                Sortable.create(box, { handle: '.dc-handle', animation: 150, onEnd: syncFromDom });
            }
        });
        var note = document.getElementById('dc-scope-note');
        if (note) note.textContent = scope === 'client' ? 'Applies to everyone in this client, and removes hidden cards from their dashboards.' : 'Only changes your own dashboard.';
        document.querySelectorAll('.dc-scope').forEach(function (b) {
            var on = b.dataset.scope === scope;
            b.classList.toggle('bg-primary', on); b.classList.toggle('text-white', on); b.classList.toggle('text-slate-500', !on);
        });
    }

    // Rebuild state[scope] from what is on screen (after a drag).
    function syncFromDom() {
        var byKey = {}; state[scope].forEach(function (it) { byKey[it.key] = it; });
        var next = [];
        list.querySelectorAll('[data-key]').forEach(function (row) { next.push(byKey[row.dataset.key]); });
        state[scope] = next;
    }

    list.addEventListener('click', function (e) {
        var row = e.target.closest('[data-key]'); if (!row) return;
        var arr = state[scope], i = arr.findIndex(function (it) { return it.key === row.dataset.key; });
        if (e.target.closest('.dc-eye')) { arr[i].hidden = !arr[i].hidden; render(); return; }
        var dir = e.target.closest('.dc-up') ? -1 : (e.target.closest('.dc-down') ? 1 : 0);
        if (!dir) return;
        // move only within the same group
        var j = i + dir;
        while (j >= 0 && j < arr.length && arr[j].group !== arr[i].group) j += dir;
        if (j < 0 || j >= arr.length) return;
        var t = arr[i]; arr[i] = arr[j]; arr[j] = t; render();
    });

    document.querySelectorAll('.dc-scope').forEach(function (b) {
        b.addEventListener('click', function () { scope = b.dataset.scope; render(); });
    });

    function post(payload) {
        var headers = { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' };
        headers[CFG.csrfHeader] = CFG.csrf;
        return fetch(CFG.url, { method: 'POST', headers: headers, body: JSON.stringify(payload) }).then(function (r) { return r.json(); });
    }
    function fail(msg) { var e = document.getElementById('dc-error'); e.textContent = msg || 'Could not save. Please try again.'; e.classList.remove('hidden'); }

    document.getElementById('dc-done').addEventListener('click', function () {
        var arr = state[scope];
        post({ dashboard: CFG.dash, scope: scope, order: arr.map(function (i) { return i.key; }), hidden: arr.filter(function (i) { return i.hidden; }).map(function (i) { return i.key; }) })
            .then(function (d) { d && d.success ? window.location.reload() : fail(d && d.message); })
            .catch(function () { fail(); });
    });
    document.getElementById('dc-reset').addEventListener('click', function () {
        if (!confirm(scope === 'client' ? 'Reset the order and hidden cards for everyone in this client?' : 'Reset your dashboard to the default cards and order?')) return;
        post({ dashboard: CFG.dash, scope: scope, reset: 1 })
            .then(function (d) { d && d.success ? window.location.reload() : fail(d && d.message); })
            .catch(function () { fail(); });
    });

    render();
})();
</script>
