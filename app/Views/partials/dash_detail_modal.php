<?php
/**
 * Pop-up for the summary cards on the Vendor and Vehicle dashboards.
 * Any element with data-dash-detail="<card key>" opens it; the rows come from
 * GET dashboard/cardDetail (Controllers\DashboardDetail).
 *
 * @var string $dash 'vendor' | 'vehicle'
 */
$dash = $dash ?? 'vendor';
?>
<div id="dd-modal" class="fixed inset-0 z-[100] hidden" role="dialog" aria-modal="true" aria-labelledby="dd-title">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" data-dd-close></div>
    <div class="relative mx-auto mt-[4vh] flex max-h-[92vh] w-[min(1100px,94vw)] flex-col overflow-hidden rounded-xl bg-white shadow-2xl dark:bg-slate-900">
        <div class="flex items-center justify-between border-b border-slate-200 bg-slate-50 px-6 py-4 dark:border-slate-700 dark:bg-slate-800">
            <div class="flex items-center gap-3">
                <div class="flex size-11 items-center justify-center rounded-lg bg-primary/10 text-primary"><span id="dd-icon" class="material-symbols-outlined">groups</span></div>
                <div>
                    <h3 id="dd-title" class="text-lg font-bold text-slate-900 dark:text-white"></h3>
                    <p id="dd-sub" class="text-xs text-slate-500"></p>
                </div>
            </div>
            <button type="button" data-dd-close class="flex size-9 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-200 hover:text-slate-600 dark:hover:bg-slate-700" aria-label="Close">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div class="min-h-0 flex-1 overflow-y-auto p-6">
            <p id="dd-count" class="mb-4 text-sm text-slate-500"></p>
            <div id="dd-wrap" class="overflow-hidden rounded-lg border border-slate-200 dark:border-slate-700">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 p-3 dark:border-slate-700">
                    <label class="flex items-center gap-2 text-sm text-slate-500">Show
                        <select id="dd-per" class="h-9 rounded border-slate-300 text-sm dark:border-slate-600 dark:bg-slate-800">
                            <option>10</option><option>25</option><option>50</option><option>100</option>
                        </select> entries
                    </label>
                    <label class="flex items-center gap-2 text-sm text-slate-500">Search:
                        <input id="dd-search" type="text" class="h-9 w-56 rounded border-slate-300 text-sm dark:border-slate-600 dark:bg-slate-800" autocomplete="off">
                    </label>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead id="dd-head" class="bg-slate-50 text-xs font-bold uppercase tracking-wide text-slate-600 dark:bg-slate-800 dark:text-slate-300"></thead>
                        <tbody id="dd-body"></tbody>
                    </table>
                </div>
                <div class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-200 p-3 text-sm text-slate-500 dark:border-slate-700">
                    <span id="dd-info"></span>
                    <div class="flex items-center gap-1">
                        <button type="button" id="dd-prev" class="rounded border px-3 py-1 hover:bg-slate-100 disabled:opacity-40 dark:hover:bg-slate-800">Previous</button>
                        <span id="dd-page" class="px-2"></span>
                        <button type="button" id="dd-next" class="rounded border px-3 py-1 hover:bg-slate-100 disabled:opacity-40 dark:hover:bg-slate-800">Next</button>
                    </div>
                </div>
            </div>
            <p id="dd-msg" class="py-10 text-center text-sm text-slate-500"></p>
            <p id="dd-trunc" class="mt-3 hidden text-xs text-amber-600">Showing the first 2,000 records only. Use the list page for the full set.</p>
            <a id="dd-link" href="#" class="mt-4 hidden text-sm font-semibold text-primary hover:underline"></a>
        </div>
    </div>
</div>
<script>
(function () {
    const DASH = <?= json_encode($dash) ?>, URL_BASE = <?= json_encode(base_url('dashboard/cardDetail')) ?>;
    const $ = id => document.getElementById(id);
    const modal = $('dd-modal');
    if (!modal) { return; }
    let cols = [], rows = [], urls = [], noun = 'records', view = [], sortCol = null, sortDir = 1, page = 1;

    const esc = s => String(s == null ? '' : s).replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
    const per = () => parseInt($('dd-per').value, 10) || 10;
    // Cells come as "21 Oct 2026" or "21 Oct 2026 06:00"; sort those by time, not text.
    const MON = { Jan: 0, Feb: 1, Mar: 2, Apr: 3, May: 4, Jun: 5, Jul: 6, Aug: 7, Sep: 8, Oct: 9, Nov: 10, Dec: 11 };
    const asTime = s => {
        const m = /^(\d{2}) ([A-Z][a-z]{2}) (\d{4})(?: (\d{2}):(\d{2}))?$/.exec(s);
        return (m && m[2] in MON) ? new Date(+m[3], MON[m[2]], +m[1], +(m[4] || 0), +(m[5] || 0)).getTime() : null;
    };
    const cmp = (x, y) => {
        const tx = asTime(x), ty = asTime(y);
        if (tx !== null && ty !== null) { return tx - ty; }
        if (tx !== null || ty !== null) { return tx === null ? 1 : -1; } // '-' sorts after dates
        return x.localeCompare(y, undefined, { numeric: true });
    };

    function close() { modal.classList.add('hidden'); document.body.style.overflow = ''; }
    modal.querySelectorAll('[data-dd-close]').forEach(el => el.addEventListener('click', close));
    document.addEventListener('keydown', e => { if (e.key === 'Escape' && !modal.classList.contains('hidden')) { close(); } });

    function show(msg) {
        $('dd-msg').textContent = msg || '';
        $('dd-msg').classList.toggle('hidden', !msg);
        $('dd-wrap').classList.toggle('hidden', !!msg);
        $('dd-count').textContent = '';
        $('dd-link').classList.add('hidden');
        $('dd-trunc').classList.add('hidden');
    }

    function render() {
        const q = $('dd-search').value.trim().toLowerCase();
        view = rows.map((r, i) => i).filter(i => !q || rows[i].join(' ').toLowerCase().includes(q));
        if (sortCol !== null) {
            view.sort((a, b) => cmp(String(rows[a][sortCol]), String(rows[b][sortCol])) * sortDir);
        }
        const n = per(), pages = Math.max(1, Math.ceil(view.length / n));
        page = Math.min(page, pages);
        const start = (page - 1) * n, slice = view.slice(start, start + n);

        $('dd-head').innerHTML = '<tr><th class="w-12 border-b border-slate-200 px-4 py-3 dark:border-slate-700">#</th>'
            + cols.map((c, i) => '<th data-col="' + i + '" class="cursor-pointer select-none whitespace-nowrap border-b border-slate-200 px-4 py-3 dark:border-slate-700">'
                + esc(c) + (sortCol === i ? (sortDir > 0 ? ' &#9650;' : ' &#9660;') : '') + '</th>').join('') + '</tr>';
        $('dd-body').innerHTML = slice.length ? slice.map((ri, k) => {
            const r = rows[ri], u = urls[ri];
            return '<tr class="border-b border-slate-100 odd:bg-white even:bg-slate-50/60 hover:bg-blue-50 dark:border-slate-800 dark:odd:bg-slate-900 dark:even:bg-slate-800/40 dark:hover:bg-slate-800">'
                + '<td class="px-4 py-3 text-slate-500">' + (start + k + 1) + '</td>'
                + r.map((c, i) => '<td class="px-4 py-3">' + (i === 0 && u
                    ? '<a class="font-semibold text-slate-900 hover:text-primary hover:underline dark:text-white" href="' + esc(u) + '">' + esc(c) + '</a>'
                    : (i === 0 ? '<span class="font-semibold">' + esc(c) + '</span>' : esc(c))) + '</td>').join('') + '</tr>';
        }).join('') : '<tr><td colspan="' + (cols.length + 1) + '" class="px-4 py-8 text-center text-slate-500">No matching records.</td></tr>';

        $('dd-info').textContent = view.length ? 'Showing ' + (start + 1) + ' to ' + Math.min(start + n, view.length) + ' of ' + view.length + ' entries' : 'Showing 0 entries';
        $('dd-page').textContent = 'Page ' + page + ' of ' + pages;
        $('dd-prev').disabled = page <= 1;
        $('dd-next').disabled = page >= pages;
    }

    $('dd-head').addEventListener('click', e => {
        const th = e.target.closest('th[data-col]');
        if (!th) { return; }
        const c = parseInt(th.dataset.col, 10);
        sortDir = (sortCol === c) ? -sortDir : 1;
        sortCol = c;
        render();
    });
    $('dd-search').addEventListener('input', () => { page = 1; render(); });
    $('dd-per').addEventListener('change', () => { page = 1; render(); });
    $('dd-prev').addEventListener('click', () => { page--; render(); });
    $('dd-next').addEventListener('click', () => { page++; render(); });

    function open(card, label) {
        $('dd-title').textContent = label || 'Details';
        $('dd-sub').textContent = '';
        $('dd-search').value = '';
        sortCol = null; page = 1;
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        show('Loading...');

        const p = new URLSearchParams(window.location.search);
        p.set('dash', DASH);
        p.set('card', card);
        fetch(URL_BASE + '?' + p.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' })
            .then(r => r.json().then(j => ({ ok: r.ok, j })))
            .then(({ ok, j }) => {
                if (!ok) { show(j.error || 'Could not load the details.'); return; }
                cols = j.columns || []; rows = j.rows || []; urls = j.urls || []; noun = j.noun || 'records';
                $('dd-title').textContent = j.title || label || 'Details';
                $('dd-sub').textContent = j.subtitle || '';
                $('dd-icon').textContent = j.icon || 'groups';
                show('');
                $('dd-count').textContent = rows.length.toLocaleString() + ' ' + noun;
                $('dd-trunc').classList.toggle('hidden', !j.truncated);
                const a = $('dd-link');
                if (j.link) { a.textContent = j.link.label + ' →'; a.href = j.link.url; a.classList.remove('hidden'); } else { a.classList.add('hidden'); }
                if (!rows.length) { show('Nothing to show for this card yet.'); return; }
                render();
            })
            .catch(() => show('Could not load the details.'));
    }

    document.addEventListener('click', e => {
        const el = e.target.closest('[data-dash-detail]');
        if (!el) { return; }
        e.preventDefault();
        open(el.dataset.dashDetail, el.dataset.dashLabel || '');
    });
    document.addEventListener('keydown', e => {
        if ((e.key === 'Enter' || e.key === ' ') && e.target.matches && e.target.matches('[data-dash-detail][role="button"]')) {
            e.preventDefault();
            e.target.click();
        }
    });
})();
</script>
