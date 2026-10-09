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
</head>
<body class="bg-gray-50 dark:bg-gray-900 font-sans text-gray-800 dark:text-gray-200 h-screen flex">
    <?= view('partials/sidebar') ?>
    <main class="flex-1 overflow-y-auto p-4 md:p-8">
        <div class="mx-auto max-w-3xl rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <h1 class="mb-2 text-xl font-bold uppercase">List Columns</h1>
            <p class="mb-6 text-xs text-gray-500 dark:text-gray-400">
                Choose which columns of each list this client can see. Untick a column to hide it from the client's
                users (including their vendor company accounts) on screen and in the Export file.
                Columns marked <span class="font-semibold">Always shown</span> are needed for the list to work.
            </p>

            <?php if ($m = session()->getFlashdata('success')): ?>
                <div class="mb-4 rounded-lg bg-emerald-50 px-4 py-2 text-sm text-emerald-700"><?= esc($m) ?></div>
            <?php endif; ?>
            <?php if ($m = session()->getFlashdata('error')): ?>
                <div class="mb-4 rounded-lg bg-red-50 px-4 py-2 text-sm text-red-700"><?= esc($m) ?></div>
            <?php endif; ?>

            <?php if ($platform): ?>
            <form method="get" action="<?= base_url('config/list-columns') ?>" class="mb-6 flex flex-wrap items-end gap-2 border-b border-gray-100 pb-6 dark:border-gray-700">
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
                <p class="text-sm text-gray-500"><?= $platform ? 'Choose a client above to set its columns.' : 'Your account is not linked to a client.' ?></p>
            <?php else: ?>
            <form method="post" action="<?= base_url('config/list-columns/save') ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="client_id" value="<?= (int) $client['id'] ?>"/>

                <?php foreach ($registry as $listKey => $def): ?>
                <h2 class="mb-1 text-sm font-bold"><?= esc($def['title']) ?> <span class="font-normal text-gray-500">— <?= esc($client['name']) ?></span></h2>
                <table class="mb-6 w-full border-collapse text-left text-xs">
                    <thead>
                        <tr class="bg-gray-50 font-bold uppercase dark:bg-gray-700">
                            <th class="border-b p-3">Column</th>
                            <th class="w-40 border-b p-3 text-center">Client can see</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($def['columns'] as $key => $col):
                            $locked  = ! empty($col['locked']);
                            $checked = $locked || ($choices[$listKey][$key] ?? true);
                            $id      = 'c_' . $listKey . '_' . $key; ?>
                        <tr class="border-b border-gray-100 dark:border-gray-700">
                            <td class="p-3 font-semibold"><label for="<?= esc($id, 'attr') ?>"><?= esc($col['label']) ?></label></td>
                            <td class="p-3 text-center">
                                <?php if ($locked): ?>
                                    <span class="text-gray-400">Always shown</span>
                                <?php else: ?>
                                    <input id="<?= esc($id, 'attr') ?>" type="checkbox" name="cols[<?= esc($listKey, 'attr') ?>][]" value="<?= esc($key, 'attr') ?>" <?= $checked ? 'checked' : '' ?> class="size-4 accent-primary"/>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endforeach; ?>

                <button type="submit" class="h-10 rounded-lg bg-primary px-5 text-sm font-semibold text-white">Save</button>
            </form>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
