<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title><?= esc($pageTitle) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { darkMode: "class", theme: { extend: { colors: { primary: "#137fec" }, fontFamily: { sans: ["Montserrat","sans-serif"] } } } };</script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50 dark:bg-gray-900 font-sans text-gray-800 dark:text-gray-200 h-screen flex">
    <?= view('partials/sidebar') ?>
    <main class="flex-1 overflow-y-auto p-4 md:p-8">
        <div class="mx-auto max-w-3xl rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <h1 class="mb-2 text-xl font-bold uppercase">Dashboard Cards</h1>
            <p class="mb-6 text-xs text-gray-500 dark:text-gray-400">
                Choose which cards this client's Staff and Vendor dashboards have. A card you untick here is gone for
                everyone in that client. Each person can then hide more of the remaining cards for themselves with the
                <span class="font-semibold">Customize</span> button on their dashboard (they can never add back a card you removed).
            </p>

            <?php if ($m = session()->getFlashdata('success')): ?>
                <div class="mb-4 rounded-lg bg-emerald-50 px-4 py-2 text-sm text-emerald-700"><?= esc($m) ?></div>
            <?php endif; ?>
            <?php if ($m = session()->getFlashdata('error')): ?>
                <div class="mb-4 rounded-lg bg-red-50 px-4 py-2 text-sm text-red-700"><?= esc($m) ?></div>
            <?php endif; ?>

            <?php if ($platform): ?>
            <form method="get" action="<?= base_url('config/dashboard-cards') ?>" class="mb-6 flex flex-wrap items-end gap-2 border-b border-gray-100 pb-6 dark:border-gray-700">
                <div>
                    <label class="mb-1 block text-xs font-semibold" for="client_id">Client</label>
                    <select id="client_id" name="client_id" class="w-72 rounded border border-gray-300 px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-900" onchange="this.form.submit()">
                        <option value="">— choose a client —</option>
                        <?php foreach ($clients as $c): ?>
                        <option value="<?= (int) $c['id'] ?>" <?= $client && (int) $client['id'] === (int) $c['id'] ? 'selected' : '' ?>><?= esc($c['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </form>
            <?php endif; ?>

            <?php if (! $client): ?>
                <p class="text-sm text-gray-500"><?= $platform ? 'Choose a client above to set its dashboard cards.' : 'Your account is not linked to a client.' ?></p>
            <?php else: ?>
            <form method="post" action="<?= base_url('config/dashboard-cards/save') ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="client_id" value="<?= (int) $client['id'] ?>"/>

                <?php foreach ($registry as $dash => $def):
                    $byGroup = [];
                    foreach ($def['cards'] as $key => $card) { $byGroup[$card['group']][$key] = $card['label']; } ?>
                <h2 class="mb-2 mt-2 text-sm font-bold"><?= esc($def['title']) ?> <span class="font-normal text-gray-500">— <?= esc($client['name']) ?></span></h2>
                <div class="mb-6 rounded border border-gray-200 dark:border-gray-700">
                    <?php foreach ($byGroup as $group => $items): ?>
                    <div class="border-b border-gray-100 bg-gray-50 px-3 py-2 text-[11px] font-bold uppercase tracking-wide dark:border-gray-700 dark:bg-gray-700"><?= esc($group) ?></div>
                    <?php foreach ($items as $key => $label): $id = 'dc_' . $dash . '_' . $key; ?>
                    <label for="<?= esc($id, 'attr') ?>" class="flex cursor-pointer items-center gap-3 border-b border-gray-100 px-3 py-2.5 text-xs last:border-0 dark:border-gray-700">
                        <input id="<?= esc($id, 'attr') ?>" type="checkbox" name="cards[<?= esc($dash, 'attr') ?>][]" value="<?= esc($key, 'attr') ?>" <?= ($choices[$dash][$key] ?? true) ? 'checked' : '' ?> class="size-4 accent-primary"/>
                        <span class="font-semibold"><?= esc($label) ?></span>
                    </label>
                    <?php endforeach; endforeach; ?>
                </div>
                <?php endforeach; ?>

                <button type="submit" class="h-10 rounded-lg bg-primary px-5 text-sm font-semibold text-white">Save</button>
            </form>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
