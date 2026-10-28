<?= view('staffs/_layout_top', ['pageTitle' => $pageTitle, 'activeTab' => '']) ?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>

<?php
$in  = 'w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-3 py-2.5 text-sm';
$btn = 'px-4 py-2.5 rounded bg-primary hover:bg-blue-600 text-white text-sm font-semibold';
?>

<div x-data="gateMon()" x-init="init()" class="-m-2">
    <h1 class="text-xl font-bold uppercase tracking-wide mb-4">Monitoring</h1>

    <!-- person search -->
    <div class="flex flex-wrap gap-3 mb-4">
        <form @submit.prevent="lookup(personQ)" class="flex w-full sm:w-[28rem]">
            <input x-model="personQ" placeholder="IC / PASSPORT / FULL NAME" class="<?= $in ?> uppercase rounded-r-none">
            <button type="submit" class="px-4 bg-primary text-white rounded-r hover:bg-blue-600"><span class="material-symbols-outlined text-[20px] align-middle">search</span></button>
        </form>
        <?php if (! empty($clients)): ?>
        <select x-model="clientId" @change="page = 1; load()" class="<?= $in ?> sm:w-56">
            <option value="">All clients</option>
            <?php foreach ($clients as $c): ?><option value="<?= (int) $c['id'] ?>"><?= esc($c['name']) ?></option><?php endforeach; ?>
        </select>
        <?php endif; ?>
        <button type="button" @click="gateStart()" class="<?= $btn ?> ml-auto flex items-center gap-1 bg-emerald-600 hover:bg-emerald-700"><span class="material-symbols-outlined text-[18px]">door_open</span> Open Gate</button>
    </div>

    <!-- Open Gate (KPK: choose a lane, then the reason; the barrier opens and a manual movement is recorded) -->
    <div x-show="gate.open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" @keydown.escape.window="gate.open = false">
        <div class="w-full max-w-3xl rounded-lg bg-white p-5 shadow-xl dark:bg-gray-900" @click.outside="gate.open = false">
            <div class="mb-3 flex items-center justify-between">
                <h2 class="text-sm font-bold uppercase" x-text="gate.step === 1 ? 'Please Choose Lane' : 'Open Gate'"></h2>
                <button type="button" @click="gate.open = false" class="text-gray-400 hover:text-gray-700"><span class="material-symbols-outlined">close</span></button>
            </div>
            <div x-show="gate.step === 1">
                <form @submit.prevent="gateLanes()" class="mb-3 flex"><input x-model="gate.q" placeholder="LANE / LOCATION / BARRIER NO" class="<?= $in ?> uppercase rounded-r-none">
                    <button class="px-4 bg-primary text-white rounded-r"><span class="material-symbols-outlined text-[20px] align-middle">search</span></button></form>
                <div class="max-h-80 overflow-y-auto rounded border border-gray-200 dark:border-gray-700">
                    <table class="w-full text-left text-xs">
                        <thead><tr class="bg-gray-50 font-bold dark:bg-gray-800"><th class="p-2"></th><th class="p-2">No.</th><th class="p-2">Lane</th><th class="p-2">Location Access</th><th class="p-2">Barrier No.</th><th class="p-2">Direction</th></tr></thead>
                        <tbody>
                            <template x-for="(l, i) in gate.lanes" :key="l.id">
                                <tr class="cursor-pointer border-t border-gray-100 hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800" :class="gate.laneId === l.id ? 'bg-primary/10' : ''" @click="gate.laneId = l.id">
                                    <td class="p-2"><input type="radio" :checked="gate.laneId === l.id"></td><td class="p-2" x-text="i + 1"></td><td class="p-2" x-text="l.lane"></td>
                                    <td class="p-2" x-text="l.location_access || '-'"></td><td class="p-2" x-text="l.barrier_no ?? '-'"></td><td class="p-2" x-text="l.direction"></td>
                                </tr>
                            </template>
                            <tr x-show="! gate.lanes.length"><td colspan="6" class="p-4 text-center text-gray-400">No lane found.</td></tr>
                        </tbody>
                    </table>
                </div>
                <div class="mt-3 flex justify-end"><button type="button" @click="gate.step = 2; gate.msg = ''" :disabled="! gate.laneId" class="<?= $btn ?> disabled:opacity-40">Open</button></div>
            </div>
            <div x-show="gate.step === 2">
                <label class="mb-1 block text-xs font-semibold">Remark *</label>
                <textarea x-model="gate.reason" maxlength="255" rows="3" class="<?= $in ?>"></textarea>
                <p x-show="gate.msg" class="mt-2 text-sm" :class="gate.ok ? 'text-emerald-600' : 'text-red-600'" x-text="gate.msg"></p>
                <div class="mt-3 flex justify-between">
                    <button type="button" @click="gate.step = 1" class="px-4 py-2.5 rounded border border-gray-300 text-sm">Back</button>
                    <button type="button" @click="gateSubmit()" :disabled="gate.busy || ! gate.reason.trim()" class="<?= $btn ?> bg-emerald-600 hover:bg-emerald-700 disabled:opacity-40" x-text="gate.busy ? 'Opening...' : 'OK'"></button>
                </div>
            </div>
        </div>
    </div>

    <div class="rounded-2xl bg-[#0b1f5c] p-4 grid grid-cols-1 xl:grid-cols-3 gap-4">
        <!-- left: person + transaction list -->
        <div class="xl:col-span-2 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="flex items-center justify-center min-h-[14rem]">
                    <img :src="person?.photo_url || '<?= base_url('assets/images/head_placeholder.png') ?>'" onerror="this.style.opacity=.35;this.removeAttribute('onerror')" alt="photo" class="max-h-56 rounded-lg object-contain bg-white/10">
                </div>
                <div class="md:col-span-2 bg-white dark:bg-gray-900 rounded-lg p-3 min-h-[14rem]">
                    <template x-if="person">
                        <div class="mb-2 text-sm"><span class="font-bold" x-text="person.name"></span> <span class="text-gray-500" x-text="person.ic"></span>
                            <span class="ml-2 text-xs px-2 py-0.5 rounded bg-primary/10 text-primary" x-text="person.type"></span></div>
                    </template>
                    <table class="w-full text-left text-xs">
                        <thead><tr class="uppercase font-bold"><th class="p-2">Location</th><th class="p-2">Time In</th><th class="p-2">Time Out</th><th class="p-2">Status</th></tr></thead>
                        <tbody>
                            <template x-for="m in movements" :key="m.time_in + m.status">
                                <tr class="border-t border-gray-100 dark:border-gray-700"><td class="p-2" x-text="m.location || '-'"></td><td class="p-2 whitespace-nowrap" x-text="fmt(m.time_in)"></td><td class="p-2 whitespace-nowrap" x-text="fmt(m.time_out)"></td><td class="p-2 uppercase" x-text="m.status"></td></tr>
                            </template>
                            <tr x-show="! movements.length"><td colspan="4" class="p-4 text-center text-gray-400" x-text="personMsg || 'No records available'"></td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-900 rounded-lg p-5">
                <p class="text-xs font-bold uppercase tracking-wide mb-3">Transaction List</p>
                <div class="flex flex-wrap gap-3 mb-3">
                    <form @submit.prevent="page = 1; load()" class="flex flex-1 min-w-[16rem]">
                        <input x-model="q" placeholder="IC/ PASSPORT/ NAME/ LOCATION/ LANE NO/ CARD ID" class="<?= $in ?> uppercase rounded-r-none">
                        <button type="submit" class="px-4 bg-primary text-white rounded-r hover:bg-blue-600"><span class="material-symbols-outlined text-[20px] align-middle">search</span></button>
                    </form>
                    <button type="button" @click="adv = ! adv" class="<?= $btn ?> flex items-center gap-1"><span class="material-symbols-outlined text-[18px]">filter_alt</span> Advanced Filter</button>
                    <a :href="exportUrl()" class="<?= $btn ?> flex items-center gap-1"><span class="material-symbols-outlined text-[18px]">download</span> Export</a>
                </div>
                <div class="flex flex-wrap gap-3 mb-3">
                    <button type="button" @click="tab = 'active'; page = 1; load()" :class="tab === 'active' ? 'ring-2 ring-offset-2 ring-primary' : ''" class="<?= $btn ?> w-40">Active</button>
                    <button type="button" @click="tab = 'reject'; page = 1; load()" :class="tab === 'reject' ? 'ring-2 ring-offset-2 ring-primary' : ''" class="<?= $btn ?> w-40">Reject</button>
                </div>
                <div x-show="adv" x-cloak class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 mb-3 p-3 rounded border border-gray-200 dark:border-gray-700">
                    <input x-model="from" type="date" title="Date From" class="<?= $in ?>">
                    <input x-model="to" type="date" title="Date To" class="<?= $in ?>">
                    <select x-model="status" class="<?= $in ?>"><option value="">Status</option><option>Gate In</option><option>Gate Out</option><option>Rejected</option></select>
                    <select x-model="vehicleType" class="<?= $in ?>"><option value="">Vehicle Type</option><template x-for="v in vehicleTypes" :key="v"><option :value="v" x-text="v"></option></template></select>
                    <select x-model="driverType" class="<?= $in ?>"><option value="">Type</option><option value="PASSENGER">PASSENGER</option><option value="DRIVER">DRIVER</option></select>
                    <select x-model="type" class="<?= $in ?>"><option value="">Type Of Vendor Pass</option><option value="VENDORPASS">VENDOR PASS</option><option value="STAFF">STAFF</option><option value="VISITOR">VISITOR</option></select>
                    <select x-model="sort" class="<?= $in ?>">
                        <option value="">Sort By</option><option value="la-z">Location A-Z</option><option value="lz-a">Location Z-A</option>
                        <option value="ln-a">Lane No ASC</option><option value="ln-d">Lane No DESC</option><option value="dt-a">Date Time ASC</option><option value="dt-d">Date Time DESC</option>
                        <option value="mr-a">Manual Reason A-Z</option><option value="mr-d">Manual Reason Z-A</option>
                    </select>
                    <div class="flex gap-2"><button type="button" @click="page = 1; load()" class="<?= $btn ?>">Apply</button>
                        <button type="button" @click="reset()" class="px-4 py-2.5 rounded border border-gray-300 text-sm">Clear</button></div>
                </div>

                <div class="overflow-x-auto rounded border border-gray-200 dark:border-gray-700">
                    <table class="w-full min-w-max text-left text-xs">
                        <thead><tr class="bg-gray-50 dark:bg-gray-800 font-bold">
                            <th class="p-3">No</th><th class="p-3"></th><th class="p-3">Name</th><th class="p-3">Type</th><th class="p-3">Type Of Vehicle</th><th class="p-3">Type Of Vendor</th><th class="p-3">IC/Passport No</th><th class="p-3">Location</th>
                            <th class="p-3">In Lane No</th><th class="p-3">Out Lane No</th><th class="p-3">Card ID</th><th class="p-3">Plate No</th><th class="p-3">Time In</th><th class="p-3">Time Out</th>
                            <th class="p-3">Mode For In</th><th class="p-3">Mode For Out</th><th class="p-3">Manual Reason</th><th class="p-3">Status</th>
                        </tr></thead>
                        <tbody>
                            <template x-for="(r, i) in rows" :key="r.tx_key">
                                <tr class="border-t border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 cursor-pointer" @click="lookup(r.ic || r.name)">
                                    <td class="p-3" x-text="(page - 1) * perPage + i + 1"></td>
                                    <td class="p-3 text-primary"><a :href="BASE + '/info/' + r.tx_key" @click.stop title="View Transaction Detail"><span class="material-symbols-outlined text-[18px]">search</span></a></td>
                                    <td class="p-3 uppercase" x-text="r.name"></td><td class="p-3" x-text="r.driver_type"></td><td class="p-3" x-text="r.vehicle_type"></td><td class="p-3" x-text="r.pass_type"></td>
                                    <td class="p-3" x-text="r.ic"></td><td class="p-3" x-text="r.location || r.out_location"></td>
                                    <td class="p-3" x-text="r.in_lane"></td><td class="p-3" x-text="r.out_lane"></td><td class="p-3" x-text="r.card_no"></td><td class="p-3" x-text="r.plate_no"></td>
                                    <td class="p-3 whitespace-nowrap" x-text="fmt(r.time_in)"></td><td class="p-3 whitespace-nowrap" x-text="fmt(r.time_out)"></td>
                                    <td class="p-3" x-text="r.mode_in"></td><td class="p-3" x-text="r.mode_out"></td><td class="p-3" x-text="r.manual_reason"></td>
                                    <td class="p-3 uppercase font-semibold" :class="r.status === 'Rejected' ? 'text-red-600' : (r.status === 'Gate In' ? 'text-green-600' : '')" x-text="r.status"></td>
                                </tr>
                            </template>
                            <tr x-show="! rows.length"><td colspan="18" class="p-8 text-center text-gray-500" x-text="loading ? 'Loading...' : (error || 'No transaction found.')"></td></tr>
                        </tbody>
                    </table>
                </div>
                <div class="flex flex-wrap items-center justify-between gap-3 mt-3">
                    <div class="flex">
                        <button type="button" @click="go(page - 1)" :disabled="page <= 1" class="px-3 py-1.5 border text-sm disabled:opacity-40">&laquo;</button>
                        <template x-for="p in pageList()" :key="p"><button type="button" @click="go(p)" :class="p === page ? 'bg-primary text-white border-primary' : ''" class="px-3 py-1.5 border text-sm" x-text="p"></button></template>
                        <button type="button" @click="go(page + 1)" :disabled="page >= pages" class="px-3 py-1.5 border text-sm disabled:opacity-40">&raquo;</button>
                    </div>
                    <select x-model.number="perPage" @change="page = 1; load()" class="<?= $in ?> w-48 uppercase">
                        <option value="10">10 items per page</option><option value="20">20 items per page</option><option value="30">30 items per page</option><option value="40">40 items per page</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- right: totals + charts -->
        <div class="space-y-4 text-white">
            <div class="rounded-lg bg-[#0a1647] p-4">
                <div class="flex items-center justify-between mb-3"><p class="text-xs font-bold uppercase tracking-wide">Total Transactions</p>
                    <button type="button" @click="load()" class="text-xs font-semibold px-3 py-1.5 rounded bg-primary flex items-center gap-1"><span class="material-symbols-outlined text-[16px]">refresh</span> Refresh</button></div>
                <div class="grid grid-cols-2 gap-y-2 gap-x-4 text-xs">
                    <p class="cursor-pointer hover:underline" @click="quick({ type: 'VISITOR', today: true })">Total Visitor: <b x-text="stats.visitor"></b></p><p class="cursor-pointer hover:underline" @click="quick({ tab: 'reject', today: true })">Denied Entry: <b x-text="stats.denied"></b></p>
                    <p class="cursor-pointer hover:underline" @click="quick({ type: 'STAFF', today: true })">Total Staff: <b x-text="stats.staff"></b></p><p class="cursor-pointer hover:underline" @click="quick({ oow: '1' })">Out Of Window: <b x-text="stats.out_of_window"></b></p>
                    <p class="cursor-pointer hover:underline" @click="quick({ type: 'VENDORPASS', today: true })">Total Vendor: <b x-text="stats.vendor"></b></p><p class="cursor-pointer hover:underline" @click="quick({ type: 'STAFF', status: 'Gate In' })">Total Staff In Premise: <b x-text="stats.in_staff"></b></p>
                    <p></p><p class="cursor-pointer hover:underline" @click="quick({ type: 'VENDORPASS', status: 'Gate In' })">Total Vendor In Premise: <b x-text="stats.in_vendor"></b></p>
                    <p></p><p class="cursor-pointer hover:underline" @click="quick({ type: 'VISITOR', status: 'Gate In' })">Total Visitor In Premise: <b x-text="stats.in_visitor"></b></p>
                    <p></p><p class="cursor-pointer hover:underline" @click="quick({ status: 'Gate In' })">Total All In Premise: <b x-text="stats.in_all"></b></p>
                </div>
            </div>
            <div class="rounded-lg bg-[#0a1647] p-4"><p class="text-xs font-bold uppercase tracking-wide mb-2">Current Transaction Per Lane</p><div class="h-48"><canvas id="chLane"></canvas></div></div>
            <div class="rounded-lg bg-[#0a1647] p-4">
                <div class="flex items-center justify-between mb-2"><p class="text-xs font-bold uppercase tracking-wide">Avg Transactions Per Hour (Per Lane)</p>
                    <select x-model="lane" @change="load()" class="text-xs text-gray-800 rounded px-2 py-1"><option value="">All lanes</option><template x-for="l in lanes" :key="l"><option :value="l" x-text="l"></option></template></select></div>
                <div class="h-48"><canvas id="chHour"></canvas></div>
            </div>
            <div class="rounded-lg bg-[#0a1647] p-4"><p class="text-xs font-bold uppercase tracking-wide mb-2">Avg Transactions Per Day</p><div class="h-48"><canvas id="chDay"></canvas></div></div>
            <div class="rounded-lg bg-[#0a1647] p-4">
                <div class="flex flex-wrap items-center justify-between gap-2 mb-2"><p class="text-xs font-bold uppercase tracking-wide">Total Transaction</p>
                    <div class="flex gap-1">
                        <button type="button" @click="period = 'month'; drawTotal()" :class="period === 'month' ? 'bg-primary text-white' : 'bg-white text-gray-800'" class="text-[11px] font-semibold px-2 py-1 rounded">BY MONTH</button>
                        <button type="button" @click="period = 'week'; drawTotal()" :class="period === 'week' ? 'bg-primary text-white' : 'bg-white text-gray-800'" class="text-[11px] font-semibold px-2 py-1 rounded">BY WEEK</button>
                        <select x-model="totalType" @change="drawTotal()" class="text-xs text-gray-800 rounded px-2 py-1"><option value="VISITOR">Visitor</option><option value="VENDORPASS">Vendor</option><option value="STAFF">Staff</option></select></div></div>
                <div class="h-48"><canvas id="chTotal"></canvas></div>
            </div>
        </div>
    </div>
</div>

<script>
function gateMon() {
    const BASE = '<?= base_url('monitoring') ?>';
    const charts = {};
    const axis = { ticks: { color: '#cbd5e1' }, grid: { color: 'rgba(255,255,255,.08)' } };
    const opts = { responsive: true, maintainAspectRatio: false, plugins: { legend: { labels: { color: '#fff', boxWidth: 10 } } }, scales: { x: axis, y: Object.assign({ beginAtZero: true }, axis) } };
    const draw = (id, type, labels, sets) => {
        if (charts[id]) { charts[id].destroy(); }
        charts[id] = new Chart(document.getElementById(id), { type, data: { labels, datasets: sets }, options: opts });
    };
    const IN = '#5b6ee1', OUT = '#22c58b';
    return {
        q: '', from: '', to: '', status: '', type: '', sort: '', tab: 'active', oow: '', adv: false, clientId: '', lane: '', BASE,
        driverType: '', vehicleType: '', vehicleTypes: [],
        gate: { open: false, step: 1, q: '', lanes: [], laneId: null, reason: '', msg: '', ok: false, busy: false },
        page: 1, pages: 1, perPage: 10, total: 0, rows: [], loading: false, error: '',
        stats: { visitor: 0, staff: 0, vendor: 0, denied: 0, out_of_window: 0, in_visitor: 0, in_staff: 0, in_vendor: 0, in_all: 0 },
        lanes: [], chartData: null, period: 'month', totalType: 'VISITOR',
        personQ: '', person: null, movements: [], personMsg: '',
        init() { this.load(); },
        fmt(t) { if (!t) return '-'; const d = new Date(t.replace(' ', 'T')); return isNaN(d) ? t : d.toLocaleString('en-GB', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit', hour12: false }).replace(',', ''); },
        query(extra) {
            const p = new URLSearchParams(Object.assign({ q: this.q, from: this.from, to: this.to, status: this.status, type: this.type, driver_type: this.driverType, vehicle_type: this.vehicleType, sort: this.sort, tab: this.tab, client_id: this.clientId, lane: this.lane, oow: this.oow, page: this.page, per_page: this.perPage }, extra || {}));
            return p.toString();
        },
        exportUrl() { return BASE + '/export?' + this.query(); },
        quick(o) {
            const today = new Date().toISOString().slice(0, 10);
            this.q = ''; this.sort = ''; this.page = 1;
            this.type = o.type || ''; this.status = o.status || ''; this.tab = o.tab || 'active'; this.oow = o.oow || '';
            this.from = o.today ? today : ''; this.to = o.today ? today : '';
            this.adv = !!(this.type || this.status || this.from || this.oow);
            this.load();
            document.querySelector('[x-data=\"gateMon()\"] table')?.scrollIntoView({ behavior: 'smooth' });
        },
        reset() { this.q = this.from = this.to = this.status = this.type = this.sort = this.oow = this.driverType = this.vehicleType = ''; this.page = 1; this.load(); },
        gateStart() { Object.assign(this.gate, { open: true, step: 1, q: '', laneId: null, reason: '', msg: '', ok: false }); this.gateLanes(); },
        gateLanes() {
            fetch(BASE + '/lanes?q=' + encodeURIComponent(this.gate.q) + '&client_id=' + encodeURIComponent(this.clientId), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(r => r.json()).then(res => { this.gate.lanes = res.success ? res.lanes : []; })
                .catch(() => { this.gate.lanes = []; });
        },
        gateSubmit() {
            const fd = new FormData();
            fd.append('lane_id', this.gate.laneId); fd.append('reason', this.gate.reason.trim()); fd.append('client_id', this.clientId);
            fd.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
            this.gate.busy = true; this.gate.msg = '';
            fetch(BASE + '/open-gate', { method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(r => r.json())
                .then(res => {
                    this.gate.busy = false; this.gate.ok = !!res.success; this.gate.msg = res.message || (res.success ? 'Successfully Open!' : 'Could not open the gate.');
                    if (res.success) { setTimeout(() => { this.gate.open = false; }, 900); this.page = 1; this.load(); }
                })
                .catch(() => { this.gate.busy = false; this.gate.ok = false; this.gate.msg = 'Could not reach the server.'; });
        },
        go(p) { if (p < 1 || p > this.pages) return; this.page = p; this.load(); },
        pageList() { const out = []; for (let p = Math.max(1, this.page - 2); p <= Math.min(this.pages, this.page + 2); p++) out.push(p); return out; },
        load() {
            this.loading = true; this.error = '';
            fetch(BASE + '/data?' + this.query(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(r => r.json())
                .then(res => {
                    this.loading = false;
                    if (!res.success) { this.rows = []; this.error = res.message || 'Could not load.'; return; }
                    this.rows = res.rows; this.total = res.total; this.pages = res.pages; this.page = res.page; this.stats = res.stats;
                    this.vehicleTypes = res.vehicle_types || [];
                    this.chartData = res.charts; this.lanes = res.charts.lanes;
                    this.$nextTick(() => this.drawCharts());
                })
                .catch(() => { this.loading = false; this.rows = []; this.error = 'Could not reach the server.'; });
        },
        drawCharts() {
            const c = this.chartData; if (!c || typeof Chart === 'undefined') return;
            const d = (label, data, color, fill) => ({ label, data, backgroundColor: color, borderColor: color, tension: .3, fill: !!fill });
            draw('chLane', 'bar', c.per_lane.labels, [d('In', c.per_lane.in, IN), d('Out', c.per_lane.out, OUT)]);
            draw('chHour', 'line', Array.from({ length: 24 }, (_, i) => i), [d('In', c.per_hour.in, IN), d('Out', c.per_hour.out, OUT)]);
            draw('chDay', 'line', c.per_day.labels, [d('Entry', c.per_day.in, IN), d('Exit', c.per_day.out, OUT)]);
            this.drawTotal();
        },
        drawTotal() {
            const c = this.chartData; if (!c || typeof Chart === 'undefined') return;
            const t = c.total, labels = this.period === 'month' ? t.months : t.days.map(x => x.slice(5));
            draw('chTotal', 'line', labels, [{ label: 'Transactions', data: t.series[this.totalType][this.period], borderColor: IN, backgroundColor: IN, tension: .3 }]);
        },
        lookup(term) {
            term = (term || '').trim(); if (!term) return;
            this.personQ = term; this.personMsg = '';
            fetch(BASE + '/person?q=' + encodeURIComponent(term) + '&client_id=' + encodeURIComponent(this.clientId), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(r => r.json())
                .then(res => {
                    if (!res.success || !res.found) { this.person = null; this.movements = []; this.personMsg = res.message || 'No record found.'; return; }
                    this.person = res.person; this.movements = res.rows;
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                })
                .catch(() => { this.person = null; this.movements = []; this.personMsg = 'Could not reach the server.'; });
        },
    };
}
</script>

<?= view('staffs/_layout_bottom') ?>
