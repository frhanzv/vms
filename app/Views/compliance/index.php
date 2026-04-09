<!DOCTYPE html>
<?php $current = service('uri')->getPath(); ?>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title><?= esc($pageTitle) ?></title>
    <link rel="icon" type="image/png" href="<?= base_url('assets/images/vms-icon.png') ?>"/>
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800;900&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script id="tailwind-config">
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
                    fontFamily: {
                        "display": ["Montserrat", "sans-serif"],
                        "sans": ["Montserrat", "sans-serif"]
                    },
                    borderRadius: {"DEFAULT": "0.375rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px"},
                },
            },
        }
    </script>
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
        }
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>

    <!-- Blacklist dropdown function-->

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

</head>
<body class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-white overflow-hidden">
<div class="flex h-screen">
    <!-- Sidebar -->
    <aside class="w-64 flex-shrink-0 border-r border-slate-200 dark:border-slate-800 bg-surface-light dark:bg-surface-dark flex flex-col justify-between p-4 hidden md:flex h-full">
        <div class="flex flex-col gap-8">
            <div class="flex items-center gap-3 px-2">
                <div class="bg-center bg-no-repeat bg-cover rounded-lg size-10 bg-primary/10 flex items-center justify-center text-primary">
                    <span class="material-symbols-outlined text-3xl">shield_person</span>
                </div>
                <h1 class="text-lg font-bold tracking-tight text-slate-900 dark:text-white">SafeG</h1>
            </div>
            <nav class="flex flex-col gap-2">
                <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary dark:hover:text-white transition-colors group" href="<?= base_url('dashboard') ?>">
                    <span class="material-symbols-outlined text-[22px] group-hover:scale-110 transition-transform">dashboard</span>
                    <p class="text-sm font-medium">Dashboard</p>
                </a>
                <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg bg-primary/10 text-primary group transition-colors" href="<?= base_url('compliance') ?>">
                    <span class="material-symbols-outlined text-[22px] font-medium fill-1 group-hover:scale-110 transition-transform">health_and_safety</span>
                    <p class="text-sm font-semibold">Compliance</p>
                </a>
                <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary dark:hover:text-white transition-colors group" href="<?= base_url('invitations') ?>">
                    <span class="material-symbols-outlined text-[22px] group-hover:scale-110 transition-transform">mail</span>
                    <p class="text-sm font-medium">Invitations</p>
                </a>
                <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary dark:hover:text-white transition-colors group" href="<?= base_url('requests') ?>">
                    <span class="material-symbols-outlined text-[22px] group-hover:scale-110 transition-transform">assignment</span>
                    <p class="text-sm font-medium">Request List</p>
                </a>
                <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary dark:hover:text-white transition-colors group" href="<?= base_url('staffs') ?>">
                    <span class="material-symbols-outlined text-[22px] group-hover:scale-110 transition-transform">badge</span>
                    <p class="text-sm font-medium">Staff Pass List</p>
                </a>
                <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary dark:hover:text-white transition-colors group" href="<?= base_url('visitors') ?>">
                    <span class="material-symbols-outlined text-[22px] group-hover:scale-110 transition-transform">group</span>
                    <p class="text-sm font-medium">Visitors List</p>
                </a>
                <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary dark:hover:text-white transition-colors group" href="<?= base_url('logbook') ?>">
                    <span class="material-symbols-outlined text-[22px] group-hover:scale-110 transition-transform">menu_book</span>
                    <p class="text-sm font-medium">Visitor Logbook</p>
                </a>
                <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary dark:hover:text-white transition-colors group" href="<?= base_url('workflow') ?>">
                    <span class="material-symbols-outlined text-[22px] group-hover:scale-110 transition-transform">account_tree</span>
                    <p class="text-sm font-medium">Visitor Workflow</p>
                </a>

                <div x-data="{ openBlacklist: <?= str_contains($current, 'blacklist') ? 'true' : 'false' ?>, openIndividual: <?= str_contains($current, 'blacklist') ? 'true' : 'false' ?> }">
    
                    <!-- Blacklist Parent Button -->
                    <button 
                        type="button"
                        @click="openBlacklist = !openBlacklist"
                        class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg <?= str_contains($current, 'blacklist') ? 'bg-primary/10 text-primary' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary' ?> transition-colors group"
                    >
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-[22px] group-hover:scale-110 transition-transform">person_cancel</span>
                            <p class="text-sm font-medium">Blacklist</p>
                        </div>
                        <span 
                            class="material-symbols-outlined text-[18px] transition-transform duration-200"
                            :class="openBlacklist ? 'rotate-180' : ''"
                        >expand_more</span>
                    </button>

                    <!-- Blacklist Dropdown -->
                    <div 
                        x-show="openBlacklist" 
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-1"
                        class="ml-4 mt-1 flex flex-col gap-1"
                    >
                        <!-- Individual Submenu Button -->
                        <button
                            type="button"
                            @click="openIndividual = !openIndividual"
                            class="w-full flex items-center justify-between px-3 py-2 rounded-lg <?= str_contains($current, 'blacklist') ? 'text-primary bg-primary/5' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary' ?> transition-colors group"
                        >
                            <div class="flex items-center gap-3">
                                <span class="material-symbols-outlined text-[18px] group-hover:scale-110 transition-transform">person</span>
                                <p class="text-sm font-medium">Individual</p>
                            </div>
                            <span 
                                class="material-symbols-outlined text-[16px] transition-transform duration-200"
                                :class="openIndividual ? 'rotate-180' : ''"
                            >expand_more</span>
                        </button>

                        <!-- Request List & Closed List Links -->
                        <div 
                            x-show="openIndividual"
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 -translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 -translate-y-1"
                            class="ml-4 mt-1 flex flex-col gap-1"
                        >   
                            <!-- Request List -->
                            <a href="<?= base_url('blacklist/blacklistrequest') ?>"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm
                            <?= $current == 'blacklist/blacklistrequest' 
                                ? 'bg-primary/10 text-primary font-medium' 
                                : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary font-medium' ?>">
                                <span class="w-1.5 h-1.5 rounded-full <?= $current == 'blacklist/blacklistrequest' ? 'bg-primary' : 'bg-slate-400' ?> flex-shrink-0"></span>
                                Request List
                            </a>

                            <!-- Closed List -->
                            <a href="<?= base_url('blacklist/closedlist') ?>"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm
                            <?= $current == 'blacklist/closedlist' 
                                ? 'bg-primary/10 text-primary font-medium' 
                                : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary font-medium' ?>">
                                <span class="w-1.5 h-1.5 rounded-full <?= $current == 'blacklist/closedlist' ? 'bg-primary' : 'bg-slate-400' ?> flex-shrink-0"></span>
                                Closed List
                            </a>
                        </div>
                    </div>
                </div>

                <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary dark:hover:text-white transition-colors group" href="<?= base_url('config') ?>">
                    <span class="material-symbols-outlined text-[22px] group-hover:scale-110 transition-transform">tune</span>
                    <p class="text-sm font-medium">Config</p>
                </a>
                <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary dark:hover:text-white transition-colors group" href="<?= base_url('settings') ?>">
                    <span class="material-symbols-outlined text-[22px] group-hover:scale-110 transition-transform">settings</span>
                    <p class="text-sm font-medium">Settings</p>
                </a>
            </nav>
        </div>
        <div class="border-t border-slate-200 dark:border-slate-700 pt-4 px-2">
            <div class="flex items-center gap-3">
                <?php if (!empty($userPhoto)): ?>
                    <img src="<?= base_url('assets/uploads/profiles/' . $userPhoto) ?>" 
                         alt="Profile" 
                         class="size-9 rounded-full object-cover shadow-sm ring-2 ring-white dark:ring-slate-900"/>
                <?php else: ?>
                    <div class="size-9 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold text-xs shadow-sm ring-2 ring-white dark:ring-slate-900">
                        <?= strtoupper(substr(session()->get('full_name') ?? $userName ?? 'U', 0, 2)) ?>
                    </div>
                <?php endif; ?>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-slate-900 dark:text-white truncate"><?= esc(session()->get('full_name') ?? $userName) ?></p>
                    <p class="text-xs text-slate-500 dark:text-slate-400 truncate"><?= esc(ucfirst(session()->get('role') ?? $userRole)) ?></p>
                </div>
                <a href="<?= base_url('auth/logout') ?>" class="text-slate-400 hover:text-slate-600 dark:hover:text-white p-1 rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                    <span class="material-symbols-outlined text-xl">logout</span>
                </a>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 p-6 lg:p-8 overflow-y-auto">
        <div class="grid grid-cols-12 gap-6">
            <!-- Left Column -->
            <div class="col-span-12 lg:col-span-3 space-y-8">
                <header class="flex justify-between items-center">
                    <div class="flex items-center gap-4">
                        <img alt="Safe-C logo" class="h-10 w-10" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDNuUL3JMaJNurglZ28LUTu97AIzYQ_yXCoYt2MXWyYg7zFqcjcMfgl3KFPEHk5W44EN4z-eY4jzeQqqQGSafKIqmCfBKqqPeHnirDxW9JxzBzb1_SocEg5qLkF3tPwDUquCjIrUZi25r0PM6-JU7963GWqgAVieDm64-4coJNmlFWnOkHK7Oe5Ity6_wZ4IkeIyMamlOet0v3W5tf3EnhTdAyHCjbiycgwgC954vad1_2QYZvNO8cUz7G5wINKE1Mvfi1l-2e_22c"/>
                        <h1 class="text-xl font-bold text-primary dark:text-white">COMPLIANCE DASHBOARD</h1>
                    </div>
                </header>
                
                <!-- Camera List -->
                <section class="bg-surface-light dark:bg-surface-dark p-6 rounded-xl shadow-sm">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Camera List</h2>
                        <button class="text-slate-500 dark:text-slate-400 hover:text-primary dark:hover:text-white">
                            <span class="material-symbols-outlined">sort_by_alpha</span>
                        </button>
                    </div>
                    <ul class="space-y-4">
                        <li class="flex items-center gap-4">
                            <div class="p-2 rounded-full bg-gray-800 dark:bg-gray-700 text-white">
                                <span class="material-symbols-outlined text-base">videocam</span>
                            </div>
                            <div>
                                <p class="font-medium text-sm">Camera ZA1</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Zone A</p>
                            </div>
                        </li>
                        <li class="flex items-center gap-4">
                            <div class="p-2 rounded-full bg-orange-500 text-white">
                                <span class="material-symbols-outlined text-base">videocam</span>
                            </div>
                            <div>
                                <p class="font-medium text-sm">Camera ZA2</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Zone A</p>
                            </div>
                        </li>
                        <li class="flex items-center gap-4">
                            <div class="p-2 rounded-full bg-green-500 text-white">
                                <span class="material-symbols-outlined text-base">videocam</span>
                            </div>
                            <div>
                                <p class="font-medium text-sm">Camera ZA3</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Zone A</p>
                            </div>
                        </li>
                    </ul>
                    <div class="text-center mt-4">
                        <a class="text-sm text-blue-600 dark:text-blue-400 font-medium" href="#">View More</a>
                    </div>
                </section>
                
                <!-- Workers In Zone -->
                <section>
                    <h2 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Workers In Zone</h2>
                    <div class="flex items-center gap-6">
                        <div class="text-center">
                            <img alt="Anisah Ali" class="w-16 h-16 rounded-full mx-auto object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCXBJSvt5i1zbvImXiifZKrt9EWwULAz7hPL-T1oPygG2S7odr6JxoxqHxQ-h6AkljzAXjWD4Z21WaQndTATLZ5-3zj63ESUSvhqIYUMTJN2Epze1RkvI9gABzMG9zIpkjlsdu9ysvaDe4ZIOaSklJq0nNDXua9WLP_VXCpw2KwiMklt4PaJYyYnz008G5Z7RsqJgcKs2NscnU_0UpmGYRib2EIMrGZ3XEntbuntol3fdjd4Lm70vYTrMb4ucQyxgx2YVA35SzGSeY"/>
                            <p class="mt-2 text-sm font-medium">Anisah Ali</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">C17 Project A</p>
                        </div>
                        <div class="text-center">
                            <img alt="Lisa Suriani" class="w-16 h-16 rounded-full mx-auto object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBZdfswzJqIIdZ7afkx-DMDFpitOk8yE_T57ZsLl54QpQ70qnT2ippUyxb0eCs1onf0X51NC710oZBzWmLg_KTOxI9xdUPnqexAnIWTzYf4SJB1euPMmY5Qx1I56HVYVSaOwgSzBpGWG0yb64NQjG9955JWZKakyR_Wd_yND8pWWXUrWkxsTNtZ35r3Fs0LThJLP53k8fYpG4WVpu2R2n3q8s6g3CBGR0qanpd-Uo7etuDvs8eXWRQPNqJMUumuA2z_Niflnxw_ImE"/>
                            <p class="mt-2 text-sm font-medium">Lisa Suriani</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">B11 Project A</p>
                        </div>
                        <div class="text-center">
                            <img alt="Nur Izzari" class="w-16 h-16 rounded-full mx-auto object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDAE9rm5bdfpJMbTziPIt8KewGhmnV-e5iyShpSiUvcxZjH47DJ57GVO4h0WmVZG3pakr2S4MP3Wsku4DJ9PiHsZyCGgUoBTXyoDWbd8dljg_Ip-SAuqKOhpfE9X9I4mA98nqjvsZ39XGXGHBeSjcUyTSPi8u0RdkF1qSlSvyjSrtxyngKJnfqLlhTUxioFMOrKaqKxUAxEtUVuWQJDu2kqbou1MirtfnQD9P91JlNWCsbc2V1k8yRthC8fMsuFsLMv1kvuhUQGsag"/>
                            <p class="mt-2 text-sm font-medium">Nur Izzari</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">B19 Project A</p>
                        </div>
                        <div class="text-center">
                            <img alt="Alisa" class="w-16 h-16 rounded-full mx-auto object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDmbNfSVlbcdzICSW0xJXvE59XEn5_Y1_Vkty3ijtO9hjktgyuIE-J0jHx8aL4C2cg-K7qSFxI7pXvN3qfaBsg3XVzk6pUNSeC7JhK3e0768sFPlzE_i-o0tGk-_G_hYQgCkXh_R1K1T6jF3QnVVnaDUszeEhGhzsrC5t6CK4be70Ya0j9eH_oW7Jrx1ifWAwGmJ07x3WiJZUtnyML0JJIIv1onB5nKTwVfaiPg3f50iNu1i03CkUul9RkZMoq4PXh2nZU2E5j5pqs"/>
                            <p class="mt-2 text-sm font-medium">Alisa</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">C21 Project A</p>
                        </div>
                    </div>
                </section>
            </div>

            <!-- Middle Column -->
            <div class="col-span-12 lg:col-span-5 space-y-8">
                <!-- Compliance List -->
                <section class="bg-surface-light dark:bg-surface-dark p-6 rounded-xl shadow-sm">
                    <h2 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Compliance List</h2>
                    <div class="grid grid-cols-2 gap-4">
                        <button class="flex items-center gap-3 p-4 rounded-lg bg-gray-100 dark:bg-gray-800/50 hover:bg-gray-200 dark:hover:bg-gray-700/50 border border-transparent">
                            <div class="p-2 bg-orange-100 dark:bg-orange-900/50 rounded-md">
                                <span class="material-symbols-outlined text-orange-500">health_and_safety</span>
                            </div>
                            <span class="font-medium text-sm">Compliance Detection</span>
                        </button>
                        <button class="flex items-center gap-3 p-4 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-300 dark:border-red-700">
                            <div class="p-2 bg-red-100 dark:bg-red-900/50 rounded-md">
                                <span class="material-symbols-outlined text-red-500">grid_view</span>
                            </div>
                            <span class="font-medium text-sm">Restricted Zone</span>
                        </button>
                        <button class="flex items-center gap-3 p-4 rounded-lg bg-gray-100 dark:bg-gray-800/50 hover:bg-gray-200 dark:hover:bg-gray-700/50 border border-transparent">
                            <div class="p-2 bg-gray-200 dark:bg-gray-700 rounded-md">
                                <span class="material-symbols-outlined text-gray-600 dark:text-gray-300">directions_car</span>
                            </div>
                            <span class="font-medium text-sm">Vehicle Access Control</span>
                        </button>
                        <button class="flex items-center gap-3 p-4 rounded-lg bg-gray-100 dark:bg-gray-800/50 hover:bg-gray-200 dark:hover:bg-gray-700/50 border border-transparent">
                            <div class="p-2 bg-teal-100 dark:bg-teal-900/50 rounded-md">
                                <span class="material-symbols-outlined text-teal-500">smartphone</span>
                            </div>
                            <span class="font-medium text-sm">Mobile Phone Detection</span>
                        </button>
                    </div>
                </section>

                <!-- Incidents -->
                <section class="bg-surface-light dark:bg-surface-dark p-6 rounded-xl shadow-sm">
                    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
                        <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Incidents</h2>
                        <div class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
                            <span class="material-symbols-outlined text-lg">filter_alt</span>
                            <span>Filters:</span>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-slate-500 dark:text-slate-400 mb-1" for="date-range">Date Range</label>
                            <div class="relative">
                                <input class="w-full pl-10 pr-4 py-2 text-sm bg-gray-100 dark:bg-gray-800/50 border border-slate-200 dark:border-slate-700 rounded-md focus:ring-blue-500 focus:border-blue-500" id="date-range" placeholder="Select dates" type="text"/>
                                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 dark:text-slate-400 text-lg">calendar_today</span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-500 dark:text-slate-400 mb-1" for="incident-type">Incident Type</label>
                            <select class="w-full text-sm bg-gray-100 dark:bg-gray-800/50 border border-slate-200 dark:border-slate-700 rounded-md focus:ring-blue-500 focus:border-blue-500" id="incident-type">
                                <option>All Types</option>
                                <option>No Helmet</option>
                                <option>Restricted Zone</option>
                                <option>Vehicle Access</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-500 dark:text-slate-400 mb-1" for="severity">Severity</label>
                            <select class="w-full text-sm bg-gray-100 dark:bg-gray-800/50 border border-slate-200 dark:border-slate-700 rounded-md focus:ring-blue-500 focus:border-blue-500" id="severity">
                                <option>All Severities</option>
                                <option>High</option>
                                <option>Medium</option>
                                <option>Low</option>
                            </select>
                        </div>
                    </div>
                    <div class="h-64 flex items-end gap-4">
                        <div class="flex-1 text-center">
                            <div class="h-full flex items-end justify-center">
                                <div class="w-10 bg-gray-200 dark:bg-gray-700 rounded-t-md" style="height: 30%;"></div>
                            </div>
                            <p class="text-xs mt-2 text-slate-500 dark:text-slate-400">Week 1</p>
                        </div>
                        <div class="flex-1 text-center">
                            <div class="h-full flex items-end justify-center">
                                <div class="w-10 bg-gray-200 dark:bg-gray-700 rounded-t-md" style="height: 15%;"></div>
                            </div>
                            <p class="text-xs mt-2 text-slate-500 dark:text-slate-400">Week 2</p>
                        </div>
                        <div class="flex-1 text-center">
                            <div class="h-full flex items-end justify-center">
                                <div class="w-10 bg-gray-200 dark:bg-gray-700 rounded-t-md" style="height: 50%;"></div>
                            </div>
                            <p class="text-xs mt-2 text-slate-500 dark:text-slate-400">Week 3</p>
                        </div>
                        <div class="flex-1 text-center">
                            <div class="h-full flex items-end justify-center">
                                <div class="w-10 bg-orange-400 rounded-t-md" style="height: 45%;"></div>
                            </div>
                            <p class="text-xs mt-2 text-slate-500 dark:text-slate-400">Week 4</p>
                        </div>
                        <div class="flex-1 text-center">
                            <div class="h-full flex items-end justify-center">
                                <div class="w-10 bg-blue-500 rounded-t-md" style="height: 60%;"></div>
                            </div>
                            <p class="text-xs mt-2 text-slate-500 dark:text-slate-400">Week 5</p>
                        </div>
                        <div class="flex-1 text-center">
                            <div class="h-full flex items-end justify-center">
                                <div class="w-10 bg-gray-200 dark:bg-gray-700 rounded-t-md" style="height: 25%;"></div>
                            </div>
                            <p class="text-xs mt-2 text-slate-500 dark:text-slate-400">Week 6</p>
                        </div>
                        <div class="flex-1 text-center">
                            <div class="h-full flex items-end justify-center">
                                <div class="w-10 bg-gray-200 dark:bg-gray-700 rounded-t-md" style="height: 0%;"></div>
                            </div>
                            <p class="text-xs mt-2 text-slate-500 dark:text-slate-400">Week 7</p>
                        </div>
                    </div>
                </section>

                <!-- Compliance Counter -->
                <section>
                    <h2 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Compliance Counter</h2>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="text-center">
                            <div class="relative rounded-lg overflow-hidden bg-gray-700">
                                <img alt="No helmet violation area" class="w-full h-32 object-cover opacity-60" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDbIpHYwUOOy04OJGK6TakvgDsbPr32-1K-T3Cp2uDDE3zb7siDZEcJecMMLpSo34_ZmZA0l4QUG7IPU2fJKBqVhN5qxtPc7HB1c2dFJKKEorI7t3ZQysBSKjMAevsm-3fz3ZGuVW_OeO5YZJs8VB0STY2LoazY5TU2UlYSM1y9PoXroDOwwQEQYtw8j62vafKg_1VAjKTgDEM3C3KtXKTElX8Ix_9y_tEPU2iksrHerlxNUtvIQZ_D0dBkxroWubekDnzVuZ9ke-U"/>
                                <span class="absolute bottom-2 right-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full">12</span>
                            </div>
                            <p class="mt-2 text-sm font-medium">No Helmet</p>
                        </div>
                        <div class="text-center">
                            <div class="relative rounded-lg overflow-hidden bg-gray-700">
                                <img alt="No boots violation area" class="w-full h-32 object-cover opacity-60" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCcq8Hr4dpa66mgrGYGvbeKshikYx33LNL9ADA76dCc5FF_PFR01HSwP1OAWJOY8qvV7Mo-jnmGSPiHfRugGSdP4QfA8JwXwapGL8A7x2zR5Udc2fsRDk6_Fy0PMNt4AE-V-T2wmJEESYGVVEywnTaIKA-p8XLYzX9Xj12WAOufcl6cTeq84weDuKhcon7njYKwJCje2WfzqAJrf3h2FdZgiCXdQ0xN4N_tLVsi3I87Q7Jel2d5mChDdBAqjzzIaIl5Qj32hnUuRRQ"/>
                                <span class="absolute bottom-2 right-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full">8</span>
                            </div>
                            <p class="mt-2 text-sm font-medium">No Boots</p>
                        </div>
                        <div class="text-center">
                            <div class="relative rounded-lg overflow-hidden bg-gray-700">
                                <img alt="No goggles violation area" class="w-full h-32 object-cover opacity-60" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCgOymrJi7WrmRzipuUH3lE6vAeVujITNh_3nLQyHLCA4o2TuBR61cGHxV3Yq1hUqcMxqU6AHBSbgFmA6qt-Fru0Gd5rF_HtPAukSDNMTjmnWpEOx_4OmoZqNIQujzT8HDd0Zn42swXzcwh2DruqZyzTVLdkTameEtI0j4xXCSbyhyhLtZbuLFzKqcndF1i_3YSiE8AwfbPV2AZX7M50GHM8Ua36fPsNbm0rz4vpLKljdTE6Z39XZX8YsUYvmmL4FrI-Ru1TIWhf1g"/>
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <div class="p-2 bg-green-500/80 rounded-full text-white">
                                        <span class="material-symbols-outlined">check</span>
                                    </div>
                                </div>
                            </div>
                            <p class="mt-2 text-sm font-medium">No Goggles</p>
                        </div>
                        <div class="text-center">
                            <div class="relative rounded-lg overflow-hidden bg-gray-700">
                                <img alt="No face mask violation area" class="w-full h-32 object-cover opacity-60" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDeqoTj5aCXpEhgZBH4ADExnY6sdzlFIqLa2gko7b5Wc7kR9Gy0I2Tzgw29I7fwOzEp4dRGlf1UmDUwcQbkmfsW-oEEb3BB19qH9HW7XdU24-qBr8x5EIbTum-6NjAz05VkItzY3oo63aGSTCSu5gRlxci2xhngjEEj0ibe2BD2BkwCovCCgEuR1oyvI7yB0xAlszdD679TMP8PP7J_xULK7mBXJk2zzEyTcDVgx2pdGcR5ZQWQz83vvGwa8A0OctiZxXlXvJgRhqA"/>
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <div class="p-2 bg-green-500/80 rounded-full text-white">
                                        <span class="material-symbols-outlined">check</span>
                                    </div>
                                </div>
                            </div>
                            <p class="mt-2 text-sm font-medium">No Face Mask</p>
                        </div>
                    </div>
                </section>

                <!-- Violations Table -->
                <section class="bg-surface-light dark:bg-surface-dark p-6 rounded-xl shadow-sm">
                    <div class="overflow-x-auto">
                        <div class="min-w-full text-sm">
                            <div class="grid grid-cols-[auto_1fr_1fr_1fr_1fr_1fr] gap-4 pb-3 border-b border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 font-medium">
                                <div class="flex items-center">
                                    <input class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500" type="checkbox"/>
                                    <span class="ml-2 text-blue-600 dark:text-blue-400">Select All</span>
                                </div>
                                <span>Notification</span>
                                <span>Name</span>
                                <span>Vest ID</span>
                                <span>Offence</span>
                                <span>Date &amp; Time</span>
                            </div>
                            <div class="divide-y divide-slate-200 dark:divide-slate-700">
                                <div class="grid grid-cols-[auto_1fr_1fr_1fr_1fr_1fr] gap-4 py-3 items-center">
                                    <input class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500" type="checkbox"/>
                                    <span class="material-symbols-outlined text-red-500">error</span>
                                    <span>SILA</span>
                                    <span>C17</span>
                                    <span>No Vest</span>
                                    <span>15-07-24 - 10:35:17</span>
                                </div>
                                <div class="grid grid-cols-[auto_1fr_1fr_1fr_1fr_1fr] gap-4 py-3 items-center">
                                    <input class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500" type="checkbox"/>
                                    <span class="material-symbols-outlined text-red-500">error</span>
                                    <span>RONALD</span>
                                    <span>C12</span>
                                    <span>No Vest</span>
                                    <span>15-07-24 - 10:35:17</span>
                                </div>
                                <div class="grid grid-cols-[auto_1fr_1fr_1fr_1fr_1fr] gap-4 py-3 items-center">
                                    <input class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500" type="checkbox"/>
                                    <span class="material-symbols-outlined text-red-500">error</span>
                                    <span>ZAKI</span>
                                    <span>C21</span>
                                    <span>No Vest</span>
                                    <span>15-07-24 - 10:35:17</span>
                                </div>
                                <div class="grid grid-cols-[auto_1fr_1fr_1fr_1fr_1fr] gap-4 py-3 items-center">
                                    <input class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500" type="checkbox"/>
                                    <span class="material-symbols-outlined text-red-500">error</span>
                                    <span>FIKRI</span>
                                    <span>C36</span>
                                    <span>No Vest</span>
                                    <span>15-07-24 - 10:35:17</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <!-- Right Column -->
            <div class="col-span-12 lg:col-span-4">
                <div class="sticky top-8">
                    <div class="flex justify-end mb-8">
                        <img alt="User avatar" class="w-10 h-10 rounded-full" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDu4j-KZj_iPvVK8ur9ZgMILtGCj8dHSJ3_wToj_EpwgtgLpdUU-P7WeDVZ484v_bieeze5A0oD5SnFYO1WqNn2uGv8LxzAtQJzKLqhqjBcQprsm45NdbH6zuQ8w-M-tP5V4grLnA5Ufg89vdBXKpx7SP84NARzB8K1kcnsOzUWv6GD2bbBHOj1QGL2smQ2SLeJUezUtDonfVN91Ec_bisxD4ITIpXQG-v_Lrb2zj5kqraSz11yLDITY4LJa1viwzbJt_McRiiJLSU"/>
                    </div>
                    <div class="bg-surface-light dark:bg-surface-dark p-6 rounded-xl shadow-sm space-y-6">
                        <!-- Restricted Zone Summary -->
                        <section>
                            <h2 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Restricted Zone Summary</h2>
                            <div class="flex items-start gap-4 p-4 rounded-lg bg-gray-50 dark:bg-gray-800/50">
                                <div class="p-4 bg-gray-700 rounded-lg">
                                    <span class="material-symbols-outlined text-4xl text-yellow-400">square_foot</span>
                                </div>
                                <div>
                                    <p class="text-sm text-slate-500 dark:text-slate-400">Date :</p>
                                    <p class="font-medium mb-2">13 - 18 July 2025</p>
                                    <p class="text-sm text-slate-500 dark:text-slate-400">Total Issues :</p>
                                    <p class="font-bold text-2xl text-red-600">37</p>
                                </div>
                            </div>
                        </section>

                        <!-- Issue Breakdown -->
                        <section>
                            <h3 class="font-semibold text-slate-900 dark:text-white mb-2">Issue Breakdown</h3>
                            <div class="h-24 flex items-end gap-3 text-center text-xs text-slate-500 dark:text-slate-400">
                                <div class="flex-1">
                                    <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded-t-sm"></div>
                                    <p class="mt-1">13</p>
                                </div>
                                <div class="flex-1">
                                    <div class="h-2 bg-gray-200 dark:bg-gray-700 rounded-t-sm"></div>
                                    <p class="mt-1">15</p>
                                </div>
                                <div class="flex-1">
                                    <div class="h-6 bg-gray-200 dark:bg-gray-700 rounded-t-sm"></div>
                                    <p class="mt-1">16</p>
                                </div>
                                <div class="flex-1">
                                    <div class="h-16 bg-orange-400 rounded-t-sm"></div>
                                    <p class="mt-1">17</p>
                                </div>
                                <div class="flex-1">
                                    <div class="h-2 bg-gray-200 dark:bg-gray-700 rounded-t-sm"></div>
                                    <p class="mt-1">18</p>
                                </div>
                            </div>
                        </section>

                        <!-- Recent Violations -->
                        <ul class="space-y-4">
                            <li class="flex items-start gap-3">
                                <div class="p-2 bg-yellow-100 dark:bg-yellow-900/50 rounded-lg mt-1">
                                    <span class="material-symbols-outlined text-yellow-500">warning</span>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-red-500">13 JULY 25</p>
                                    <p class="text-sm text-slate-900 dark:text-white">19:07 - C17 : <span class="font-bold">ANISAH ALI</span></p>
                                </div>
                            </li>
                            <li class="flex items-start gap-3">
                                <div class="p-2 bg-yellow-100 dark:bg-yellow-900/50 rounded-lg mt-1">
                                    <span class="material-symbols-outlined text-yellow-500">warning</span>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-red-500">13 JULY 25</p>
                                    <p class="text-sm text-slate-900 dark:text-white">19:37 - C21 : <span class="font-bold">ALISA</span></p>
                                </div>
                            </li>
                            <li class="flex items-start gap-3">
                                <div class="p-2 bg-yellow-100 dark:bg-yellow-900/50 rounded-lg mt-1">
                                    <span class="material-symbols-outlined text-yellow-500">warning</span>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-red-500">14 JULY 25</p>
                                    <p class="text-sm text-slate-900 dark:text-white">12:07 - C17 : <span class="font-bold">ANISAH ALI</span></p>
                                </div>
                            </li>
                            <li class="flex items-start gap-3">
                                <div class="p-2 bg-yellow-100 dark:bg-yellow-900/50 rounded-lg mt-1">
                                    <span class="material-symbols-outlined text-yellow-500">warning</span>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-red-500">14 JULY 25</p>
                                    <p class="text-sm text-slate-900 dark:text-white">18:21 - C21 : <span class="font-bold">ALISA</span></p>
                                </div>
                            </li>
                            <li class="flex items-start gap-3">
                                <div class="p-2 bg-yellow-100 dark:bg-yellow-900/50 rounded-lg mt-1">
                                    <span class="material-symbols-outlined text-yellow-500">warning</span>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-red-500">14 JULY 25</p>
                                    <p class="text-sm text-slate-900 dark:text-white">21:07 - C17 : <span class="font-bold">ANISAH ALI</span></p>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
</body>
</html>

