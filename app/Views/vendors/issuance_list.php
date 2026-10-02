<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <title><?= esc($pageTitle) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { theme: { extend: { colors: { primary: "#137fec" }, fontFamily: { sans: ["Montserrat","sans-serif"] } } } };</script>
</head>
<body class="bg-gray-50 dark:bg-gray-900 font-sans text-gray-800 dark:text-gray-200 h-screen flex">
    <?= view('partials/sidebar') ?>
    <main class="flex-1 overflow-y-auto p-4 md:p-8">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mx-auto max-w-5xl">
            <h1 class="text-xl font-bold uppercase mb-2">Vendor Issuance List</h1>
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-6">Recording who collects the card — name and IC/passport — before it's activated and closed out.</p>
            <form method="get" class="mb-4 flex flex-wrap gap-2 items-center">
                <input name="search" value="<?= esc($searchTerm ?? '') ?>" class="border border-gray-300 dark:border-gray-600 dark:bg-gray-900 rounded px-3 py-2 text-sm w-72" placeholder="IC / Passport / Full Name / App No / Company / Receipt No"/>
                <select name="sort_by" class="border border-gray-300 dark:border-gray-600 dark:bg-gray-900 rounded px-3 py-2 text-sm" onchange="this.form.submit()">
                    <option value="date_desc" <?= ($sortBy ?? '') === 'date_desc' ? 'selected' : '' ?>>Date (Newest First)</option>
                    <option value="date_asc" <?= ($sortBy ?? '') === 'date_asc' ? 'selected' : '' ?>>Date (Oldest First)</option>
                    <option value="name_asc" <?= ($sortBy ?? '') === 'name_asc' ? 'selected' : '' ?>>Full Name (A-Z)</option>
                    <option value="name_desc" <?= ($sortBy ?? '') === 'name_desc' ? 'selected' : '' ?>>Full Name (Z-A)</option>
                    <option value="company_asc" <?= ($sortBy ?? '') === 'company_asc' ? 'selected' : '' ?>>Company (A-Z)</option>
                    <option value="company_desc" <?= ($sortBy ?? '') === 'company_desc' ? 'selected' : '' ?>>Company (Z-A)</option>
                </select>
                <button type="submit" class="h-9 px-4 rounded-lg border border-gray-300 dark:border-gray-600 text-sm font-semibold">Search</button>
            </form>
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700 font-bold uppercase">
                        <th class="p-3 border-b">No</th><th class="p-3 border-b">App No</th><th class="p-3 border-b">Receipt No</th>
                        <th class="p-3 border-b">Full Name</th><th class="p-3 border-b">Vendor Company</th><th class="p-3 border-b">Card Type</th><th class="p-3 border-b">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($list)): ?>
                    <tr><td colspan="7" class="p-6 text-center text-gray-500">No printed cards awaiting issuance.</td></tr>
                    <?php else: foreach ($list as $row): ?>
                    <tr class="border-b border-gray-100 dark:border-gray-700">
                        <td class="p-3"><?= $row['no'] ?></td>
                        <td class="p-3"><?= esc($row['app_no']) ?></td>
                        <td class="p-3 font-mono"><?= esc($row['receipt_no']) ?></td>
                        <td class="p-3 font-semibold"><?= esc($row['full_name']) ?></td>
                        <td class="p-3"><?= esc($row['vendor_company_name']) ?></td>
                        <td class="p-3"><?= esc($row['card_type']) ?></td>
                        <td class="p-3">
                            <?php if ($canIssue ?? false): ?>
                            <button onclick="openIssue(this)" data-id="<?= $row['id'] ?>" data-name="<?= esc($row['full_name'], 'attr') ?>"
                                class="text-emerald-600 hover:underline text-xs font-semibold">Issue Card</button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <!-- Issue Card Modal -->
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

            fetch('<?= base_url('vendors/issuance-list/issue/') ?>' + activeIssueId, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '<?= csrf_hash() ?>' },
                body: JSON.stringify({
                    collector_name: collectorName,
                    collector_ic_passport: collectorIc,
                }),
            })
                .then(r => {
                    if (!r.ok) throw new Error('http_' + r.status);
                    return r.json();
                })
                .then(d => {
                    alert(d.message);
                    if (d.success) {
                        location.reload();
                    } else {
                        btn.disabled = false;
                    }
                })
                .catch(err => {
                    btn.disabled = false;
                    if (String(err.message).startsWith('http_')) {
                        alert('Something went wrong on the server. If this keeps happening, check that the collector-fields migration has been run.');
                    } else {
                        alert('Could not reach the server. Please check your connection and try again.');
                    }
                });
        }
    </script>
</body>
</html>
