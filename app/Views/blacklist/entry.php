<!DOCTYPE html>
<?php $current = service('uri')->getPath(); ?>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title><?= esc($pageTitle ?? 'Blacklist Entry') ?></title>
    <link rel="icon" type="image/png" href="<?= base_url('assets/images/vms-icon.png') ?>"/>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800;900&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#137fec",
                        "primary-dark": "#0f66be",
                        "background-light": "#f6f7f8",
                        "background-dark": "#101922",
                        "surface-light": "#ffffff",
                        "surface-dark": "#1a2634",
                    },
                    fontFamily: { "sans": ["Montserrat", "sans-serif"] },
                },
            },
        }
    </script>
    <style>
        body { font-family: 'Montserrat', sans-serif; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-white overflow-hidden">
<div class="flex h-screen w-full">

    <!-- Sidebar -->
    <?= view('partials/sidebar') ?>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col h-screen overflow-hidden">
        <div class="flex-1 overflow-y-auto p-6 md:p-8 no-scrollbar">

            <div class="bg-surface-light dark:bg-surface-dark rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-6 space-y-6">

                <!-- Header -->
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-700">
                    <h2 class="text-lg font-semibold text-gray-700 uppercase tracking-tight">Blacklist Entry</h2>
                    <a href="<?= base_url('blacklist/blacklistrequest') ?>" class="text-slate-400 hover:text-slate-600 dark:hover:text-white transition-colors">
                        <span class="material-symbols-outlined text-[24px]">close</span>
                    </a>
                </div>

                <!-- Search Section -->
                <div>
                    <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-4">Search</h3>
                    <div class="flex items-end gap-3">
                        <div class="w-40">
                            <label class="block text-xs text-slate-400 mb-1.5 font-medium">Resident</label>
                            <input type="text" value="LOCAL" readonly
                                class="w-full h-10 px-4 text-sm bg-slate-50 border border-slate-200 rounded-lg text-slate-600 focus:outline-none cursor-not-allowed"/>
                        </div>
                        <div class="flex-1">
                            <label class="block text-xs text-slate-400 mb-1.5 font-medium">IC / Passport No</label>
                            <input type="text" id="searchInput"
                                placeholder="Enter IC Number, Passport No, or Full Name"
                                class="w-full h-10 px-4 text-sm bg-white border border-slate-300 rounded-lg text-slate-700 placeholder:text-slate-300 focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20"/>
                        </div>
                        <button type="button" onclick="doSearch()"
                            class="h-10 px-6 bg-primary hover:bg-primary-dark text-white text-sm font-semibold rounded-lg transition-colors shadow-sm flex items-center gap-2">
                            <span class="material-symbols-outlined text-[18px]">search</span>
                            Search
                        </button>
                    </div>

                    <!-- Error message -->
                    <div id="searchError" class="hidden mt-3 px-4 py-2.5 rounded-lg bg-red-50 border border-red-200 text-red-600 text-sm font-medium flex items-center gap-2">
                        <span class="material-symbols-outlined text-[16px]">error</span>
                        <span id="searchErrorMsg"></span>
                    </div>
                </div>

                <!-- Results Section (hidden until search) -->
                <div id="resultsSection" class="hidden">
                    <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-4">Results</h3>
                    <div class="overflow-x-auto w-full rounded-xl border border-slate-200 dark:border-slate-700">
                        <table class="w-full text-left border-collapse whitespace-nowrap">
                            <thead>
                                <tr class="border-b border-slate-200 bg-slate-50">
                                    <th class="px-4 py-3 text-xs font-bold text-slate-600 uppercase tracking-wider">No</th>
                                    <th class="px-4 py-3 text-xs font-bold text-slate-600 uppercase tracking-wider">Name</th>
                                    <th class="px-4 py-3 text-xs font-bold text-slate-600 uppercase tracking-wider">IC / Passport No</th>
                                    <th class="px-4 py-3 text-xs font-bold text-slate-600 uppercase tracking-wider">Contact</th>
                                    <th class="px-4 py-3 text-xs font-bold text-slate-600 uppercase tracking-wider">Company</th>
                                    <th class="px-4 py-3 text-xs font-bold text-slate-600 uppercase tracking-wider">Type</th>
                                    <th class="px-4 py-3 text-xs font-bold text-slate-600 uppercase tracking-wider">Action</th>
                                </tr>
                            </thead>
                            <tbody id="resultsBody" class="divide-y divide-slate-100 bg-white">
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </main>
</div>

<script>
const baseUrl = '<?= base_url() ?>';

function doSearch() {
    const ic = document.getElementById('searchInput').value.trim();
    const errorDiv = document.getElementById('searchError');
    const errorMsg = document.getElementById('searchErrorMsg');
    const resultsSection = document.getElementById('resultsSection');

    errorDiv.classList.add('hidden');
    resultsSection.classList.add('hidden');

    if (!ic) {
        errorMsg.textContent = 'Please enter an IC / Passport number or name.';
        errorDiv.classList.remove('hidden');
        return;
    }

    fetch(`${baseUrl}blacklist/entry/search?ic=${encodeURIComponent(ic)}`)
        .then(res => res.json())
        .then(data => {
            if (!data.success) {
                errorMsg.textContent = data.message;
                errorDiv.classList.remove('hidden');
                return;
            }

            const tbody = document.getElementById('resultsBody');
            tbody.innerHTML = '';

            data.data.forEach((row, index) => {
                const tr = document.createElement('tr');
                tr.className = 'hover:bg-slate-50 transition-colors';
                tr.innerHTML = `
                    <td class="px-4 py-3.5 text-sm text-slate-500 font-medium">${index + 1}</td>
                    <td class="px-4 py-3.5 text-sm font-semibold text-slate-900">${row.full_name}</td>
                    <td class="px-4 py-3.5 text-sm text-slate-600 font-mono">${row.ic_passport || '—'}</td>
                    <td class="px-4 py-3.5 text-sm text-slate-600">${row.contact || '—'}</td>
                    <td class="px-4 py-3.5 text-sm text-slate-600">${row.company || '—'}</td>
                    <td class="px-4 py-3.5">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold ${row.type === 'Staff' ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-slate-100 text-slate-600 border border-slate-200'}">
                            ${row.type}
                        </span>
                    </td>
                    <td class="px-4 py-3.5">
                        <a href="${baseUrl}blacklist/entry/proceed?invitation_id=${row.id}"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-primary hover:bg-primary-dark text-white text-xs font-semibold transition-colors">
                            <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                            Proceed
                        </a>
                    </td>
                `;
                tbody.appendChild(tr);
            });

            resultsSection.classList.remove('hidden');
        })
        .catch(() => {
            errorMsg.textContent = 'An error occurred. Please try again.';
            errorDiv.classList.remove('hidden');
        });
}

// Allow Enter key to trigger search
document.getElementById('searchInput').addEventListener('keydown', function(e) {
    if (e.key === 'Enter') doSearch();
});
</script>
</body>
</html>