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
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="font-sans text-gray-800 dark:text-gray-200 <?= !empty($embed) ? 'bg-transparent' : 'bg-gray-50 dark:bg-gray-900 h-screen flex' ?>">
<?php if (empty($embed)) echo view('partials/sidebar', ['current' => 'config/client-links']); ?>
<main class="<?= !empty($embed) ? 'p-2' : 'flex-1 overflow-y-auto p-4 md:p-8' ?>">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mx-auto max-w-5xl">
        <h1 class="text-xl font-bold uppercase mb-2">Site Sharing</h1>
        <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">
            Only needed when this installation serves more than one client. Everything stays in this one product; you still see and manage all clients.
        </p>
        <p class="text-xs text-gray-500 dark:text-gray-400 mb-6">
            <strong>Site group</strong>: clients that share one building (for example KSB and KPK) get the same group name.
            Their vendors can then pick locations of every client in the group, and a pass using locations of two clients is
            shown to — and approved by — both. Leave empty for a client that shares nothing.
            Which client owns which location is set under <a class="text-primary underline" href="<?= base_url('vendors/locations') ?>">Vendor Locations</a>.
        </p>

        <?php if (! empty($groups) || ! empty($ungrouped)): ?>
        <div class="mb-6 rounded-lg border border-gray-200 dark:border-gray-700 p-4 text-xs space-y-2">
            <p class="font-bold uppercase">Sharing status</p>
            <?php foreach ($groups as $g => $names): ?>
                <?php if (count($names) > 1): ?>
                <p class="text-emerald-700 dark:text-emerald-400"><strong><?= esc(implode(' + ', $names)) ?></strong> share one product (site group <strong><?= esc($g) ?></strong>).
                    A vendor who registers can choose the locations of both.</p>
                <?php else: ?>
                <p class="text-amber-700 dark:text-amber-400"><strong><?= esc($names[0]) ?></strong> is the only client in site group <strong><?= esc($g) ?></strong> - nothing is shared yet. Give another client the same group name to share.</p>
                <?php endif; ?>
            <?php endforeach; ?>
            <?php if (! empty($ungrouped) && count($ungrouped) < count($clients)): ?>
                <p class="text-amber-700 dark:text-amber-400">Not in any group (shares nothing): <strong><?= esc(implode(', ', $ungrouped)) ?></strong>.</p>
            <?php elseif (! empty($ungrouped)): ?>
                <p class="text-gray-500">No site groups yet. To share a building, type the same Site group (for example KLANG-1) for each client below and press Save.</p>
            <?php endif; ?>
            <p class="text-gray-600 dark:text-gray-400"><?= (int) $sharedPasses ?> vendor pass(es) currently belong to more than one client.</p>
        </div>
        <?php endif; ?>

        <div id="flashMsg" class="hidden mb-4 text-sm px-4 py-2 rounded-lg"></div>

        <div class="overflow-x-auto">
        <table class="w-full text-left text-xs border-collapse">
            <thead>
                <tr class="bg-gray-50 dark:bg-gray-700 font-bold uppercase">
                    <th class="p-3 border-b">Client</th>
                    <th class="p-3 border-b">Site group</th>
                    <th class="p-3 border-b">Locations</th>
                    <th class="p-3 border-b"></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($clients as $c): $id = (int) $c['id']; ?>
                <tr class="border-b border-gray-100 dark:border-gray-700" data-id="<?= $id ?>">
                    <td class="p-3 font-semibold"><?= esc($c['name']) ?><?= strtolower((string) $c['status']) !== 'active' ? ' <span class="text-gray-400 font-normal">(inactive)</span>' : '' ?></td>
                    <td class="p-3"><input class="grp border border-gray-300 dark:border-gray-600 dark:bg-gray-900 rounded px-2 py-1 w-32" value="<?= esc($c['site_group']) ?>" placeholder="e.g. KLANG-1"/></td>
                    <td class="p-3"><?= (int) ($locCounts[$id] ?? 0) ?></td>
                    <td class="p-3"><button type="button" class="px-3 py-1.5 rounded-lg bg-primary text-white font-semibold" onclick="saveClient(<?= $id ?>)">Save</button></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div>

        <h2 class="text-lg font-bold uppercase mt-10 mb-2">Gate locations (Staff &amp; Visitor)</h2>
        <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">
            The "Location Access" rows from Config &gt; Location Access Management. Each belongs to one client. A staff pass or a visitor
            invitation is seen by the client(s) owning its gate(s); Location Access is mandatory on both. Rows with no owner behave as before
            (visible as today) until you assign them.
        </p>
        <div class="overflow-x-auto">
        <table class="w-full text-left text-xs border-collapse">
            <thead><tr class="bg-gray-50 dark:bg-gray-700 font-bold uppercase">
                <th class="p-3 border-b">Branch</th><th class="p-3 border-b">Location access</th><th class="p-3 border-b">Owner client</th>
            </tr></thead>
            <tbody>
            <?php if (empty($gates)): ?>
                <tr><td colspan="3" class="p-6 text-center text-gray-500">No gate locations yet (run the migration, then add them under Config &gt; Location Access Management).</td></tr>
            <?php else: foreach ($gates as $g): ?>
                <tr class="border-b border-gray-100 dark:border-gray-700">
                    <td class="p-3"><?= esc($g['branch']) ?></td>
                    <td class="p-3 font-semibold"><?= esc($g['location_access']) ?><?= strtolower((string) $g['status']) !== 'active' ? ' <span class="text-gray-400 font-normal">(inactive)</span>' : '' ?></td>
                    <td class="p-3">
                        <select class="border border-gray-300 dark:border-gray-600 dark:bg-gray-900 rounded px-2 py-1" onchange="setGateOwner(<?= (int) $g['id'] ?>, this.value)">
                            <option value="">— not assigned —</option>
                            <?php foreach ($clients as $c): ?><option value="<?= (int) $c['id'] ?>" <?= (int) ($g['client_id'] ?? 0) === (int) $c['id'] ? 'selected' : '' ?>><?= esc($c['name']) ?></option><?php endforeach; ?>
                        </select>
                    </td>
                </tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table>
        </div>
    </div>
</main>
<script>
    const baseUrl = '<?= rtrim(base_url(), '/') ?>';
    const csrfToken = '<?= csrf_hash() ?>';
    const csrfName = '<?= csrf_token() ?>';

    function flash(m, ok) {
        const el = document.getElementById('flashMsg');
        el.textContent = m;
        el.className = 'mb-4 text-sm px-4 py-2 rounded-lg ' + (ok ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700');
    }
    function setGateOwner(id, clientId) {
        const body = new URLSearchParams({ client_id: clientId });
        body.set(csrfName, csrfToken);
        fetch(`${baseUrl}/config/client-links/gate-owner/${id}`, { method: 'POST', body }).then(r => r.json())
            .then(res => flash(res.message || 'Done.', !!res.success)).catch(() => flash('Network error.', false));
    }
    function saveClient(id) {
        const row = document.querySelector(`tr[data-id="${id}"]`);
        const body = new URLSearchParams({ site_group: row.querySelector('.grp').value });
        body.set(csrfName, csrfToken);
        fetch(`${baseUrl}/config/client-links/save/${id}`, { method: 'POST', body }).then(r => r.json()).then(res => {
            flash(res.message || 'Done.', !!res.success);
        }).catch(() => flash('Network error.', false));
    }
</script>
</body>
</html>
