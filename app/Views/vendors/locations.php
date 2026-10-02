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
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mx-auto max-w-3xl">
            <h1 class="text-xl font-bold uppercase mb-2">Vendor Locations</h1>
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-6">
                The "Location Access" choices offered on the Vendor Pass request form, the detail page, Card Info, and
                Process Detail. Add one here and it appears everywhere immediately — no other page needs changing.
            </p>

            <div id="flashMsg" class="hidden mb-4 text-sm px-4 py-2 rounded-lg"></div>

            <form id="addLocationForm" class="mb-6 flex flex-wrap gap-2 items-end border-b border-gray-100 dark:border-gray-700 pb-6">
                <div>
                    <label class="block text-xs font-semibold mb-1">Location Name</label>
                    <input id="newLabel" name="label" class="border border-gray-300 dark:border-gray-600 dark:bg-gray-900 rounded px-3 py-2 text-sm w-64" placeholder="e.g. Warehouse 27" required/>
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1">Code <span class="text-gray-400 font-normal">(optional)</span></label>
                    <input id="newCode" name="code" class="border border-gray-300 dark:border-gray-600 dark:bg-gray-900 rounded px-3 py-2 text-sm w-40" placeholder="auto from name"/>
                </div>
                <button type="submit" class="h-9 px-4 rounded-lg bg-primary text-white text-sm font-semibold">Add Location</button>
            </form>

            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700 font-bold uppercase">
                        <th class="p-3 border-b">Name</th>
                        <th class="p-3 border-b">Code</th>
                        <th class="p-3 border-b">Status</th>
                        <th class="p-3 border-b">Action</th>
                    </tr>
                </thead>
                <tbody id="locationsBody">
                    <?php if (empty($locations)): ?>
                    <tr><td colspan="4" class="p-6 text-center text-gray-500">No locations yet — add the first one above.</td></tr>
                    <?php else: foreach ($locations as $loc): ?>
                    <tr class="border-b border-gray-100 dark:border-gray-700" data-id="<?= (int) $loc['id'] ?>">
                        <td class="p-3 font-semibold label-cell"><?= esc($loc['label']) ?></td>
                        <td class="p-3 font-mono text-gray-500"><?= esc($loc['code']) ?></td>
                        <td class="p-3">
                            <?php if ((int) $loc['is_active'] === 1): ?>
                            <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">Active</span>
                            <?php else: ?>
                            <span class="px-2 py-0.5 rounded-full bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400">Inactive</span>
                            <?php endif; ?>
                        </td>
                        <td class="p-3">
                            <button type="button" class="text-primary hover:underline mr-3" onclick="renameLocation(<?= (int) $loc['id'] ?>)">Rename</button>
                            <?php if ((int) $loc['is_active'] === 1): ?>
                            <button type="button" class="text-red-500 hover:underline" onclick="toggleLocation(<?= (int) $loc['id'] ?>, 0)">Deactivate</button>
                            <?php else: ?>
                            <button type="button" class="text-emerald-600 hover:underline" onclick="toggleLocation(<?= (int) $loc['id'] ?>, 1)">Reactivate</button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <script>
        const baseUrl = '<?= rtrim(base_url(), '/') ?>';
        const csrfToken = '<?= csrf_hash() ?>';
        const csrfName = '<?= csrf_token() ?>';

        function showFlash(message, ok) {
            const el = document.getElementById('flashMsg');
            el.textContent = message;
            el.className = 'mb-4 text-sm px-4 py-2 rounded-lg ' + (ok
                ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400'
                : 'bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-400');
            el.classList.remove('hidden');
        }

        function postForm(url, data) {
            const body = new URLSearchParams(data);
            body.set(csrfName, csrfToken);
            return fetch(url, { method: 'POST', body })
                .then(r => r.json());
        }

        document.getElementById('addLocationForm').addEventListener('submit', function (e) {
            e.preventDefault();
            const label = document.getElementById('newLabel').value.trim();
            const code = document.getElementById('newCode').value.trim();
            if (!label) return;
            postForm(`${baseUrl}/vendors/locations/create`, { label, code }).then(res => {
                showFlash(res.message || (res.success ? 'Added.' : 'Failed.'), res.success);
                if (res.success) location.reload();
            }).catch(() => showFlash('Network error while adding the location.', false));
        });

        function renameLocation(id) {
            const row = document.querySelector(`tr[data-id="${id}"]`);
            const current = row.querySelector('.label-cell').textContent.trim();
            const next = prompt('Rename location to:', current);
            if (next === null || next.trim() === '' || next.trim() === current) return;
            postForm(`${baseUrl}/vendors/locations/update/${id}`, { label: next.trim() }).then(res => {
                showFlash(res.message || (res.success ? 'Updated.' : 'Failed.'), res.success);
                if (res.success) location.reload();
            }).catch(() => showFlash('Network error while renaming.', false));
        }

        function toggleLocation(id, active) {
            const verb = active ? 'reactivate' : 'deactivate';
            if (!confirm(`Are you sure you want to ${verb} this location?`)) return;
            postForm(`${baseUrl}/vendors/locations/update/${id}`, { is_active: active }).then(res => {
                showFlash(res.message || (res.success ? 'Updated.' : 'Failed.'), res.success);
                if (res.success) location.reload();
            }).catch(() => showFlash('Network error while updating.', false));
        }
    </script>
</body>
</html>
