<!-- Issue Card (Process List / Printing List rows; there is no Staff Issuance List). Needs staffPost from _layout_bottom. -->
<div id="staffIssueModal" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg w-full max-w-sm p-6">
        <h2 class="text-base font-bold mb-1">Issue Card</h2>
        <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">Issuing to <span id="staffIssueName" class="font-semibold"></span>. Record who is physically collecting this card.</p>
        <label class="block text-xs font-semibold mb-1">Collector's Full Name <span class="text-red-500">*</span></label>
        <input id="staffIssueCollector" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-900 rounded px-3 py-2 text-sm mb-3" autocomplete="off"/>
        <label class="block text-xs font-semibold mb-1">Collector's IC / Passport No <span class="text-red-500">*</span></label>
        <input id="staffIssueIc" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-900 rounded px-3 py-2 text-sm mb-4" autocomplete="off"/>
        <div class="flex justify-end gap-2">
            <button onclick="document.getElementById('staffIssueModal').classList.add('hidden')" class="h-9 px-4 rounded-lg border border-gray-300 dark:border-gray-600 text-sm">Cancel</button>
            <button id="staffIssueBtn" onclick="staffIssueSubmit(this)" class="h-9 px-4 rounded-lg bg-indigo-600 text-white text-sm font-semibold">Confirm Issue</button>
        </div>
    </div>
</div>
<script>
    let staffIssueId = null;
    function staffIssueOpen(btn) {
        staffIssueId = btn.dataset.id;
        document.getElementById('staffIssueName').textContent = btn.dataset.name;
        document.getElementById('staffIssueCollector').value = '';
        document.getElementById('staffIssueIc').value = '';
        document.getElementById('staffIssueModal').classList.remove('hidden');
        setTimeout(() => document.getElementById('staffIssueCollector').focus(), 50);
    }
    function staffIssueSubmit(btn) {
        const name = document.getElementById('staffIssueCollector').value.trim();
        const ic = document.getElementById('staffIssueIc').value.trim();
        if (!name || !ic) { alert('Please enter the collector\'s name and IC/passport.'); return; }
        staffPostReload('<?= base_url('staffs/issue-card/') ?>' + staffIssueId, { collector_name: name, collector_ic_passport: ic }, btn);
    }
</script>
