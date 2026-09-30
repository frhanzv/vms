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
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mx-auto max-w-6xl">
            <h1 class="text-xl font-bold uppercase mb-2">Vendor Process List</h1>
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-6">Approved passes that haven't been printed yet. A record stays here (and also shows in the Printing List) until it's printed from either page — printing is what moves it on to the Issuance List.</p>

            <form method="get" class="mb-4 flex flex-wrap gap-2 items-center">
                <input name="search" value="<?= esc($searchTerm ?? '') ?>" class="border border-gray-300 dark:border-gray-600 dark:bg-gray-900 rounded px-3 py-2 text-sm w-72" placeholder="IC / Passport / Full Name / App No / Company"/>
                <select name="card_type" class="border border-gray-300 dark:border-gray-600 dark:bg-gray-900 rounded px-3 py-2 text-sm" onchange="this.form.submit()">
                    <option value="" <?= ($cardType ?? '') === '' ? 'selected' : '' ?>>All Card Types</option>
                    <option value="unassigned" <?= ($cardType ?? '') === 'unassigned' ? 'selected' : '' ?>>Not Assigned</option>
                    <option value="Permanent" <?= ($cardType ?? '') === 'Permanent' ? 'selected' : '' ?>>Permanent</option>
                    <option value="Temporary" <?= ($cardType ?? '') === 'Temporary' ? 'selected' : '' ?>>Temporary</option>
                </select>
                <select name="sort_by" class="border border-gray-300 dark:border-gray-600 dark:bg-gray-900 rounded px-3 py-2 text-sm" onchange="this.form.submit()">
                    <option value="date_desc" <?= ($sortBy ?? '') === 'date_desc' ? 'selected' : '' ?>>Date (Newest First)</option>
                    <option value="date_asc" <?= ($sortBy ?? '') === 'date_asc' ? 'selected' : '' ?>>Date (Oldest First)</option>
                    <option value="name_asc" <?= ($sortBy ?? '') === 'name_asc' ? 'selected' : '' ?>>Full Name (A-Z)</option>
                    <option value="name_desc" <?= ($sortBy ?? '') === 'name_desc' ? 'selected' : '' ?>>Full Name (Z-A)</option>
                    <option value="company_asc" <?= ($sortBy ?? '') === 'company_asc' ? 'selected' : '' ?>>Company (A-Z)</option>
                    <option value="company_desc" <?= ($sortBy ?? '') === 'company_desc' ? 'selected' : '' ?>>Company (Z-A)</option>
                </select>
                <button type="submit" class="h-9 px-4 rounded-lg bg-primary text-white text-sm font-semibold">Search</button>
            </form>

            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700 font-bold uppercase">
                        <th class="p-3 border-b">No</th><th class="p-3 border-b">App No</th><th class="p-3 border-b">Full Name</th>
                        <th class="p-3 border-b">Vendor Company</th><th class="p-3 border-b">IC/Passport</th>
                        <th class="p-3 border-b">Card Type</th><th class="p-3 border-b">Pass Expiry</th><th class="p-3 border-b">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($list)): ?>
                    <tr><td colspan="8" class="p-6 text-center text-gray-500">No approved passes waiting to be printed.</td></tr>
                    <?php else: foreach ($list as $row): ?>
                    <tr class="border-b border-gray-100 dark:border-gray-700">
                        <td class="p-3"><?= $row['no'] ?></td>
                        <td class="p-3"><?= esc($row['app_no']) ?></td>
                        <td class="p-3 font-semibold"><?= esc($row['full_name']) ?></td>
                        <td class="p-3"><?= esc($row['vendor_company_name']) ?></td>
                        <td class="p-3"><?= esc($row['ic_passport_masked']) ?></td>
                        <td class="p-3"><?= esc($row['card_type']) ?></td>
                        <td class="p-3"><?= esc($row['pass_expiry']) ?></td>
                        <td class="p-3">
                            <div class="flex items-center gap-2">
                                <a href="<?= base_url('vendors/process-list/detail/view/' . $row['id']) ?>" class="text-primary hover:underline text-xs font-semibold">View Details</a>
                                <?php if ($canAssign ?? false): ?>
                                <span class="text-gray-300">|</span>
                                <div class="flex gap-1">
                                    <button onclick="assignCardType(<?= $row['id'] ?>, 'Permanent')" class="text-primary hover:underline text-xs font-semibold">Permanent</button>
                                    <span class="text-gray-300">|</span>
                                    <button onclick="assignCardType(<?= $row['id'] ?>, 'Temporary')" class="text-primary hover:underline text-xs font-semibold">Temporary</button>
                                </div>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>

            <?php if (($pagination['last_page'] ?? 1) > 1): ?>
            <div class="flex justify-end gap-1 mt-4">
                <?php for ($i = 1; $i <= $pagination['last_page']; $i++):
                    $qs = array_merge($_GET, ['page' => $i]);
                ?>
                <a href="?<?= http_build_query($qs) ?>" class="w-8 h-8 flex items-center justify-center border border-gray-300 dark:border-gray-600 rounded text-xs <?= $i === $pagination['current_page'] ? 'bg-primary text-white' : 'hover:bg-gray-50 dark:hover:bg-gray-800' ?>"><?= $i ?></a>
                <?php endfor; ?>
            </div>
            <?php endif; ?>
        </div>
    </main>
    <script>
        function assignCardType(id, cardType) {
            if (!confirm('Assign ' + cardType + ' card type?')) return;
            fetch('<?= base_url('vendors/process-list/assign-card-type/') ?>' + id, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '<?= csrf_hash() ?>' },
                body: JSON.stringify({ card_type: cardType }),
            })
                .then(r => {
                    if (!r.ok) throw new Error('http_' + r.status);
                    return r.json();
                })
                .then(d => { alert(d.message); if (d.success) location.reload(); })
                .catch(err => {
                    if (String(err.message).startsWith('http_')) {
                        alert('Something went wrong on the server. If this keeps happening, check that all migrations have been run and the route is registered.');
                    } else {
                        alert('Could not reach the server. Please check your connection and try again.');
                    }
                });
        }
    </script>
</body>
</html>
