<!-- Issue Card Modal (used by the Process List and the Printing List; there is no separate Issuance List) -->
<div id="issueModal" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center z-50">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg w-full max-w-sm p-6">
        <h2 class="text-base font-bold mb-1">Issue Card</h2>
        <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">Issuing to <span id="issueVendorName" class="font-semibold"></span>. Record who is physically collecting this card.</p>

        <label class="block text-xs font-semibold mb-1">Collector's Full Name <span class="text-red-500">*</span></label>
        <input id="collectorName" type="text" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-900 rounded px-3 py-2 text-sm mb-3" placeholder="e.g. Ahmad bin Ismail"/>

        <label class="block text-xs font-semibold mb-1">Collector's IC / Passport No <span class="text-red-500">*</span></label>
        <input id="collectorIc" type="text" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-900 rounded px-3 py-2 text-sm mb-4" placeholder="e.g. 900101-10-1234"/>

        <div class="flex justify-end gap-2">
            <button onclick="closeIssue()" class="h-9 px-4 rounded-lg border border-gray-300 dark:border-gray-600 text-sm">Cancel</button>
            <button id="issueSubmitBtn" onclick="submitIssue()" class="h-9 px-4 rounded-lg bg-emerald-600 text-white text-sm font-semibold">Confirm Issue</button>
        </div>
    </div>
</div>

<script>
    let activeIssueId = null;

    function openIssue(btn) {
        activeIssueId = btn.dataset.id;
        document.getElementById('issueVendorName').textContent = btn.dataset.name;
        document.getElementById('collectorName').value = '';
        document.getElementById('collectorIc').value = '';
        document.getElementById('issueModal').classList.remove('hidden');
    }

    function closeIssue() {
        document.getElementById('issueModal').classList.add('hidden');
    }

    function submitIssue() {
        const collectorName = document.getElementById('collectorName').value.trim();
        const collectorIc = document.getElementById('collectorIc').value.trim();
        if (!collectorName || !collectorIc) {
            alert('Please enter the collector\'s name and IC/passport.');
            return;
        }
        const btn = document.getElementById('issueSubmitBtn');
        btn.disabled = true;

        fetch('<?= base_url('vendors/issue-card/') ?>' + activeIssueId, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '<?= csrf_hash() ?>' },
            body: JSON.stringify({ collector_name: collectorName, collector_ic_passport: collectorIc }),
        })
            .then(r => { if (!r.ok) throw new Error('http_' + r.status); return r.json(); })
            .then(d => {
                alert(d.message);
                if (d.success) { location.reload(); } else { btn.disabled = false; }
            })
            .catch(err => {
                btn.disabled = false;
                alert(String(err.message).startsWith('http_') ? 'Something went wrong on the server.' : 'Could not reach the server. Please check your connection and try again.');
            });
    }
</script>
