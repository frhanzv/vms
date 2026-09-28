<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <title><?= esc($pageTitle) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { theme: { extend: { colors: { primary: "#137fec" }, fontFamily: { sans: ["Montserrat","sans-serif"] } } } };</script>
</head>
<body class="bg-gray-50 dark:bg-gray-900 font-sans text-gray-800 dark:text-gray-200 h-screen flex">
    <?= view('partials/sidebar') ?>
    <main class="flex-1 overflow-y-auto p-4 md:p-8">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mx-auto max-w-5xl">
            <h1 class="text-xl font-bold uppercase mb-6">Vendor Printing List</h1>
            <form method="get" class="mb-4">
                <input name="search" value="<?= esc($searchTerm ?? '') ?>" class="border border-gray-300 dark:border-gray-600 dark:bg-gray-900 rounded px-3 py-2 text-sm w-72" placeholder="Search name / app no / company"/>
            </form>
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700 font-bold uppercase">
                        <th class="p-3 border-b">No</th><th class="p-3 border-b">App No</th><th class="p-3 border-b">Full Name</th>
                        <th class="p-3 border-b">Vendor Company</th><th class="p-3 border-b">IC/Passport</th><th class="p-3 border-b">Card Type</th><th class="p-3 border-b">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($list)): ?>
                    <tr><td colspan="7" class="p-6 text-center text-gray-500">No cards awaiting printing.</td></tr>
                    <?php else: foreach ($list as $row): ?>
                    <tr class="border-b border-gray-100 dark:border-gray-700">
                        <td class="p-3"><?= $row['no'] ?></td>
                        <td class="p-3"><?= esc($row['app_no']) ?></td>
                        <td class="p-3 font-semibold"><?= esc($row['full_name']) ?></td>
                        <td class="p-3"><?= esc($row['vendor_company_name']) ?></td>
                        <td class="p-3"><?= esc($row['ic_passport_masked']) ?></td>
                        <td class="p-3"><?= esc($row['card_type']) ?></td>
                        <td class="p-3">
                            <?php if ($canIssue ?? false): ?>
                            <button onclick="markPrinted(<?= $row['id'] ?>)" class="text-primary hover:underline text-xs font-semibold">Mark Printed</button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </main>
    <script>
        function markPrinted(id) {
            if (!confirm('Mark this card as printed? It will move to the Issuance List.')) return;
            fetch('<?= base_url('vendors/printing-list/mark-printed/') ?>' + id, {
                method: 'POST', headers: { 'X-CSRF-TOKEN': '<?= csrf_hash() ?>' },
            }).then(r => r.json()).then(d => { alert(d.message); if (d.success) location.reload(); });
        }
    </script>
</body>
</html>
