<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title><?= esc($pageTitle) ?></title>
    <link rel="icon" type="image/png" href="<?= base_url('assets/images/vms-icon.png') ?>"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#137fec",
                        "background-light": "#f6f7f8",
                        "background-dark": "#101922",
                        "surface-dark": "#1a2632",
                        "border-dark": "#233648",
                        "node-trigger": "#8b5cf6",
                        "node-logic": "#f59e0b",
                        "node-action": "#10b981",
                    },
                    fontFamily: {
                        "display": ["Montserrat", "sans-serif"]
                    },
                    borderRadius: { "DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px" },
                    backgroundImage: {
                        'dot-pattern': 'radial-gradient(#324d67 1px, transparent 1px)',
                    }
                },
            },
        }
    </script>
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        .material-symbols-outlined.fill {
            font-variation-settings: 'FILL' 1, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f5f9; 
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1; 
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8; 
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 font-display overflow-hidden h-screen flex flex-col selection:bg-primary/30">
    <div class="flex h-screen w-full flex-col">
        <div class="flex flex-1 overflow-hidden">
            <!-- SafeG Sidebar -->
            <aside class="w-64 flex-shrink-0 border-r border-slate-200 bg-white flex flex-col justify-between p-4 hidden md:flex h-full">
                <div class="flex flex-col gap-8">
                    <div class="flex items-center gap-3 px-2">
                        <div class="bg-center bg-no-repeat bg-cover rounded-lg size-10 bg-primary/10 flex items-center justify-center text-primary">
                            <span class="material-symbols-outlined text-3xl">shield_person</span>
                        </div>
                        <h1 class="text-lg font-bold tracking-tight text-slate-900">SafeG</h1>
                    </div>
                    <nav class="flex flex-col gap-2">
                        <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 hover:bg-slate-100 hover:text-primary transition-colors group" href="<?= base_url('dashboard') ?>">
                            <span class="material-symbols-outlined text-[22px] group-hover:scale-110 transition-transform">dashboard</span>
                            <p class="text-sm font-medium">Dashboard</p>
                        </a>
                        <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 hover:bg-slate-100 hover:text-primary transition-colors group" href="<?= base_url('compliance') ?>">
                            <span class="material-symbols-outlined text-[22px] group-hover:scale-110 transition-transform">health_and_safety</span>
                            <p class="text-sm font-medium">Compliance</p>
                        </a>
                        <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 hover:bg-slate-100 hover:text-primary transition-colors group" href="<?= base_url('invitations') ?>">
                            <span class="material-symbols-outlined text-[22px] group-hover:scale-110 transition-transform">mail</span>
                            <p class="text-sm font-medium">Invitations</p>
                        </a>
                        <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 hover:bg-slate-100 hover:text-primary transition-colors group" href="<?= base_url('requests') ?>">
                            <span class="material-symbols-outlined text-[22px] group-hover:scale-110 transition-transform">assignment</span>
                            <p class="text-sm font-medium">Request List</p>
                        </a>
                        <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 hover:bg-slate-100 hover:text-primary transition-colors group" href="<?= base_url('visitors') ?>">
                            <span class="material-symbols-outlined text-[22px] group-hover:scale-110 transition-transform">group</span>
                            <p class="text-sm font-medium">Visitors List</p>
                        </a>
                        <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 hover:bg-slate-100 hover:text-primary transition-colors group" href="<?= base_url('logbook') ?>">
                            <span class="material-symbols-outlined text-[22px] group-hover:scale-110 transition-transform">menu_book</span>
                            <p class="text-sm font-medium">Visitor Logbook</p>
                        </a>
                        <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg bg-primary/10 text-primary group transition-colors" href="<?= base_url('workflow') ?>">
                            <span class="material-symbols-outlined text-[22px] font-medium fill-1 group-hover:scale-110 transition-transform">account_tree</span>
                            <p class="text-sm font-semibold">Visitor Workflow</p>
                        </a>
                        <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 hover:bg-slate-100 hover:text-primary transition-colors group" href="<?= base_url('config') ?>">
                            <span class="material-symbols-outlined text-[22px] group-hover:scale-110 transition-transform">tune</span>
                            <p class="text-sm font-medium">Config</p>
                        </a>
                        <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 hover:bg-slate-100 hover:text-primary transition-colors group" href="<?= base_url('settings') ?>">
                            <span class="material-symbols-outlined text-[22px] group-hover:scale-110 transition-transform">settings</span>
                            <p class="text-sm font-medium">Settings</p>
                        </a>
                    </nav>
                </div>
                <div class="border-t border-slate-200 pt-4 px-2">
                    <div class="flex items-center gap-3">
                        <div class="size-9 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold text-xs shadow-sm ring-2 ring-white">
                            <?= strtoupper(substr(session()->get('full_name') ?? 'U', 0, 2)) ?>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-slate-900 truncate"><?= esc(session()->get('full_name') ?? 'User') ?></p>
                            <p class="text-xs text-slate-500 truncate"><?= esc(ucfirst(session()->get('role') ?? 'User')) ?></p>
                        </div>
                        <a href="<?= base_url('auth/logout') ?>" class="text-slate-400 hover:text-slate-600 p-1 rounded-full hover:bg-slate-100 transition-colors">
                            <span class="material-symbols-outlined text-xl">logout</span>
                        </a>
                    </div>
                </div>
            </aside>

            <!-- Workflow Editor Main Area -->
            <div class="flex-1 flex flex-col overflow-hidden">
                <!-- Header -->
                <header class="flex items-center justify-between whitespace-nowrap border-b border-slate-200 bg-white px-6 py-3 shrink-0 z-50">
                    <div class="flex items-center gap-4 text-slate-900">
                        <h2 class="text-slate-900 text-lg font-bold leading-tight tracking-[-0.015em]">VisitorFlow</h2>
                        <div class="h-6 w-px bg-slate-200 mx-2"></div>
                        <div class="flex items-center gap-2">
                            <span class="text-slate-900 font-medium"><?= esc($workflowName) ?></span>
                            <span class="px-2 py-0.5 rounded text-xs font-medium bg-green-500/20 text-green-400 border border-green-500/30"><?= esc($workflowStatus) ?></span>
                        </div>
                    </div>
                    <div class="flex flex-1 justify-end gap-6 items-center">
                        <div class="flex gap-2">
                            <button class="flex items-center justify-center rounded-lg h-9 w-9 bg-transparent hover:bg-slate-100 text-slate-600 hover:text-slate-900 transition-colors">
                                <span class="material-symbols-outlined">settings</span>
                            </button>
                            <button class="flex items-center justify-center rounded-lg h-9 w-9 bg-transparent hover:bg-slate-100 text-slate-600 hover:text-slate-900 transition-colors">
                                <span class="material-symbols-outlined">help</span>
                            </button>
                            <div class="size-9 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold text-xs shadow-sm ring-2 ring-white ml-2">
                                <?= strtoupper(substr(session()->get('full_name') ?? 'U', 0, 2)) ?>
                            </div>
                        </div>
                    </div>
                </header>

                <!-- Toolbar -->
                <div class="flex items-center justify-between border-b border-slate-200 bg-white px-6 py-2 shrink-0 z-40">
                    <div class="flex items-center gap-2">
                        <a href="<?= base_url('workflow') ?>" class="flex items-center gap-2 px-3 py-1.5 rounded-md hover:bg-slate-100 text-slate-600 hover:text-slate-900 transition-colors text-sm">
                            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                            Back to List
                        </a>
                    </div>
                    <div class="flex items-center gap-2 bg-slate-50 rounded-lg p-1 border border-slate-200">
                        <button class="p-1.5 rounded hover:bg-slate-200 text-slate-600 hover:text-slate-900" title="Undo">
                            <span class="material-symbols-outlined text-[20px]">undo</span>
                        </button>
                        <button class="p-1.5 rounded hover:bg-slate-200 text-slate-600 hover:text-slate-900" title="Redo">
                            <span class="material-symbols-outlined text-[20px]">redo</span>
                        </button>
                        <div class="w-px h-4 bg-slate-300 mx-1"></div>
                        <button class="p-1.5 rounded hover:bg-slate-200 text-slate-600 hover:text-slate-900" title="Zoom Out">
                            <span class="material-symbols-outlined text-[20px]">remove</span>
                        </button>
                        <span class="text-xs font-medium text-slate-900 min-w-[3ch] text-center">100%</span>
                        <button class="p-1.5 rounded hover:bg-slate-200 text-slate-600 hover:text-slate-900" title="Zoom In">
                            <span class="material-symbols-outlined text-[20px]">add</span>
                        </button>
                        <button class="p-1.5 rounded hover:bg-slate-200 text-slate-600 hover:text-slate-900" title="Fit to Screen">
                            <span class="material-symbols-outlined text-[20px]">fit_screen</span>
                        </button>
                    </div>
                    <div class="flex items-center gap-3">
                        <button class="flex cursor-pointer items-center justify-center overflow-hidden rounded-lg h-9 bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 gap-2 text-sm font-medium px-4 transition-colors">
                            <span class="material-symbols-outlined text-[18px]">play_arrow</span>
                            Test Run
                        </button>
                        <button class="flex cursor-pointer items-center justify-center overflow-hidden rounded-lg h-9 bg-primary hover:bg-primary/90 text-white gap-2 text-sm font-bold px-4 transition-colors shadow-lg shadow-primary/20">
                            <span class="material-symbols-outlined text-[18px] fill">publish</span>
                            Publish Workflow
                        </button>
                    </div>
                </div>

                <!-- Workflow Editor Content -->
                <div class="flex flex-1 overflow-hidden relative">
        <aside class="w-72 bg-white border-r border-slate-200 flex flex-col z-20 shadow-sm">
            <div class="p-4 border-b border-slate-200">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                        <span class="material-symbols-outlined text-[20px]">search</span>
                    </span>
                    <input class="w-full bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-lg block pl-10 p-2.5 focus:ring-primary focus:border-primary placeholder-slate-400" placeholder="Search components..." type="text"/>
                </div>
            </div>
            <div class="flex-1 overflow-y-auto p-4 flex flex-col gap-4">
                <!-- Triggers Section -->
                <div class="flex flex-col gap-2">
                    <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Triggers</h3>
                    <div class="draggable-item cursor-grab active:cursor-grabbing bg-slate-50 hover:bg-slate-100 border border-slate-200 hover:border-node-trigger/50 rounded-lg p-3 flex items-center gap-3 group transition-all">
                        <div class="p-2 bg-node-trigger/10 rounded-md text-node-trigger group-hover:bg-node-trigger group-hover:text-white transition-colors">
                            <span class="material-symbols-outlined text-[20px]">person_add</span>
                        </div>
                        <div>
                            <p class="text-slate-900 text-sm font-medium">Visitor Arrives</p>
                            <p class="text-slate-500 text-xs">Kiosk check-in</p>
                        </div>
                    </div>
                    <div class="draggable-item cursor-grab active:cursor-grabbing bg-slate-50 hover:bg-slate-100 border border-slate-200 hover:border-node-trigger/50 rounded-lg p-3 flex items-center gap-3 group transition-all">
                        <div class="p-2 bg-node-trigger/10 rounded-md text-node-trigger group-hover:bg-node-trigger group-hover:text-white transition-colors">
                            <span class="material-symbols-outlined text-[20px]">input</span>
                        </div>
                        <div>
                            <p class="text-slate-900 text-sm font-medium">Form Submitted</p>
                            <p class="text-slate-500 text-xs">Pre-registration</p>
                        </div>
                    </div>
                </div>

                <!-- Logic Section -->
                <div class="flex flex-col gap-2">
                    <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1 mt-2">Logic</h3>
                    <div class="draggable-item cursor-grab active:cursor-grabbing bg-slate-50 hover:bg-slate-100 border border-slate-200 hover:border-node-logic/50 rounded-lg p-3 flex items-center gap-3 group transition-all">
                        <div class="p-2 bg-node-logic/10 rounded-md text-node-logic group-hover:bg-node-logic group-hover:text-white transition-colors">
                            <span class="material-symbols-outlined text-[20px]">call_split</span>
                        </div>
                        <div>
                            <p class="text-slate-900 text-sm font-medium">Condition (If/Else)</p>
                            <p class="text-slate-500 text-xs">Branch flow</p>
                        </div>
                    </div>
                    <div class="draggable-item cursor-grab active:cursor-grabbing bg-slate-50 hover:bg-slate-100 border border-slate-200 hover:border-node-logic/50 rounded-lg p-3 flex items-center gap-3 group transition-all">
                        <div class="p-2 bg-node-logic/10 rounded-md text-node-logic group-hover:bg-node-logic group-hover:text-white transition-colors">
                            <span class="material-symbols-outlined text-[20px]">hourglass_empty</span>
                        </div>
                        <div>
                            <p class="text-slate-900 text-sm font-medium">Wait / Delay</p>
                            <p class="text-slate-500 text-xs">Pause workflow</p>
                        </div>
                    </div>
                </div>

                <!-- Actions Section -->
                <div class="flex flex-col gap-2">
                    <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1 mt-2">Actions</h3>
                    <div class="draggable-item cursor-grab active:cursor-grabbing bg-slate-50 hover:bg-slate-100 border border-slate-200 hover:border-node-action/50 rounded-lg p-3 flex items-center gap-3 group transition-all">
                        <div class="p-2 bg-node-action/10 rounded-md text-node-action group-hover:bg-node-action group-hover:text-white transition-colors">
                            <span class="material-symbols-outlined text-[20px]">mail</span>
                        </div>
                        <div>
                            <p class="text-slate-900 text-sm font-medium">Send Email</p>
                        </div>
                    </div>
                    <div class="draggable-item cursor-grab active:cursor-grabbing bg-slate-50 hover:bg-slate-100 border border-slate-200 hover:border-node-action/50 rounded-lg p-3 flex items-center gap-3 group transition-all">
                        <div class="p-2 bg-node-action/10 rounded-md text-node-action group-hover:bg-node-action group-hover:text-white transition-colors">
                            <span class="material-symbols-outlined text-[20px]">print</span>
                        </div>
                        <div>
                            <p class="text-slate-900 text-sm font-medium">Print Badge</p>
                        </div>
                    </div>
                    <div class="draggable-item cursor-grab active:cursor-grabbing bg-slate-50 hover:bg-slate-100 border border-slate-200 hover:border-node-action/50 rounded-lg p-3 flex items-center gap-3 group transition-all">
                        <div class="p-2 bg-node-action/10 rounded-md text-node-action group-hover:bg-node-action group-hover:text-white transition-colors">
                            <span class="material-symbols-outlined text-[20px]">notifications_active</span>
                        </div>
                        <div>
                            <p class="text-slate-900 text-sm font-medium">Notify Host</p>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Canvas Area -->
        <main class="flex-1 bg-slate-50 relative overflow-auto bg-dot-pattern [background-size:20px_20px] flex justify-center p-10 cursor-grab active:cursor-grabbing" style="background-image: radial-gradient(#cbd5e1 1px, transparent 1px);">
            <div class="flex flex-col items-center min-h-[800px] w-full max-w-4xl pt-10">
                <!-- Start Node -->
                <div class="relative group z-10 w-72">
                    <div class="absolute -top-3 left-1/2 -translate-x-1/2 bg-white border border-slate-200 text-slate-600 text-[10px] px-2 rounded-full uppercase font-bold tracking-wider">Start</div>
                    <div class="bg-white border-2 border-node-trigger rounded-xl p-4 shadow-lg flex items-center gap-4 hover:shadow-node-trigger/20 transition-all cursor-pointer">
                        <div class="bg-node-trigger/20 p-2.5 rounded-lg text-node-trigger">
                            <span class="material-symbols-outlined">person_add</span>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-slate-900 font-semibold">Visitor Arrives</h4>
                            <p class="text-slate-600 text-xs">Device: Main Lobby Kiosk</p>
                        </div>
                        <span class="material-symbols-outlined text-slate-400">more_vert</span>
                    </div>
                    <div class="absolute left-1/2 -translate-x-1/2 h-12 w-0.5 bg-slate-300 top-full"></div>
                    <button class="absolute left-1/2 -translate-x-1/2 -bottom-[34px] w-6 h-6 rounded-full bg-white border border-slate-300 flex items-center justify-center text-slate-600 hover:text-slate-900 hover:border-slate-900 transition-colors z-20">
                        <span class="material-symbols-outlined text-[16px]">add</span>
                    </button>
                </div>

                <div class="h-12 w-full"></div>

                <!-- Logic Node (Condition) -->
                <div class="relative group z-10 w-80">
                    <div class="absolute -top-8 left-1/2 -translate-x-1/2 text-node-logic/80 text-[10px] uppercase font-bold tracking-wider flex flex-col items-center">
                        <span class="material-symbols-outlined text-[16px] mb-1">arrow_downward</span>
                    </div>
                    <div class="absolute -inset-1 border-2 border-primary rounded-2xl opacity-100 animate-pulse"></div>
                    <div class="relative bg-white border border-node-logic rounded-xl p-4 shadow-xl flex items-center gap-4 hover:shadow-node-logic/20 transition-all cursor-pointer">
                        <div class="bg-node-logic/20 p-2.5 rounded-lg text-node-logic">
                            <span class="material-symbols-outlined">call_split</span>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-slate-900 font-semibold">Check VIP Status</h4>
                            <p class="text-slate-600 text-xs">If Visitor Type == VIP</p>
                        </div>
                        <span class="material-symbols-outlined text-slate-400">more_vert</span>
                    </div>
                    <!-- Branch Connectors -->
                    <div class="absolute top-full left-1/2 -translate-x-1/2 w-0.5 h-8 bg-slate-300"></div>
                    <div class="absolute top-[calc(100%+32px)] left-1/2 -translate-x-1/2 w-[340px] h-0.5 bg-slate-300"></div>
                    <div class="absolute top-[calc(100%+32px)] left-[calc(50%-170px)] w-0.5 h-8 bg-slate-300"></div>
                    <div class="absolute top-[calc(100%+32px)] right-[calc(50%-170px)] w-0.5 h-8 bg-slate-300"></div>
                    <div class="absolute top-[calc(100%+12px)] left-[calc(50%-120px)] bg-white px-2 py-0.5 rounded text-[10px] text-green-600 font-bold border border-green-500/50">TRUE</div>
                    <div class="absolute top-[calc(100%+12px)] right-[calc(50%-120px)] bg-white px-2 py-0.5 rounded text-[10px] text-red-600 font-bold border border-red-500/50">FALSE</div>
                </div>

                <div class="h-24 w-full"></div>

                <!-- Branches -->
                <div class="flex gap-16 w-full justify-center">
                    <!-- TRUE Branch -->
                    <div class="flex flex-col items-center w-72">
                        <div class="relative group z-10 w-full">
                            <div class="bg-white border border-node-action rounded-xl p-4 shadow-lg flex items-center gap-4 hover:shadow-node-action/20 transition-all cursor-pointer">
                                <div class="bg-node-action/20 p-2.5 rounded-lg text-node-action">
                                    <span class="material-symbols-outlined">notifications_active</span>
                                </div>
                                <div class="flex-1">
                                    <h4 class="text-slate-900 font-semibold">Notify Concierge</h4>
                                    <p class="text-slate-600 text-xs">Send Slack Alert</p>
                                </div>
                                <span class="material-symbols-outlined text-slate-400">more_vert</span>
                            </div>
                            <div class="absolute left-1/2 -translate-x-1/2 h-8 w-0.5 bg-slate-300 top-full"></div>
                        </div>
                        <div class="h-8"></div>
                        <div class="relative group z-10 w-full">
                            <div class="bg-white border border-node-action rounded-xl p-4 shadow-lg flex items-center gap-4 hover:shadow-node-action/20 transition-all cursor-pointer">
                                <div class="bg-node-action/20 p-2.5 rounded-lg text-node-action">
                                    <span class="material-symbols-outlined">print</span>
                                </div>
                                <div class="flex-1">
                                    <h4 class="text-slate-900 font-semibold">Print VIP Badge</h4>
                                    <p class="text-slate-600 text-xs">Template: Gold_Badge_v2</p>
                                </div>
                                <span class="material-symbols-outlined text-slate-400">more_vert</span>
                            </div>
                        </div>
                    </div>

                    <!-- FALSE Branch -->
                    <div class="flex flex-col items-center w-72">
                        <div class="relative group z-10 w-full">
                            <div class="bg-white border border-node-action rounded-xl p-4 shadow-lg flex items-center gap-4 hover:shadow-node-action/20 transition-all cursor-pointer opacity-80 hover:opacity-100">
                                <div class="bg-node-action/20 p-2.5 rounded-lg text-node-action">
                                    <span class="material-symbols-outlined">print</span>
                                </div>
                                <div class="flex-1">
                                    <h4 class="text-slate-900 font-semibold">Print Standard Badge</h4>
                                    <p class="text-slate-600 text-xs">Template: Std_Visitor</p>
                                </div>
                                <span class="material-symbols-outlined text-slate-400">more_vert</span>
                            </div>
                            <div class="absolute left-1/2 -translate-x-1/2 h-8 w-0.5 bg-slate-300 top-full"></div>
                        </div>
                        <div class="h-8"></div>
                        <div class="relative group z-10 w-full flex justify-center">
                            <div class="bg-white border border-slate-300 text-slate-600 rounded-full px-4 py-1 text-xs font-bold uppercase tracking-wider">End Flow</div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Right Sidebar - Configuration Panel -->
        <aside class="w-80 bg-white border-l border-slate-200 flex flex-col z-20 shadow-sm overflow-y-auto">
            <div class="p-5 border-b border-slate-200 flex items-center justify-between">
                <div>
                    <h3 class="text-slate-900 font-bold text-lg">Configuration</h3>
                    <p class="text-slate-600 text-xs">Editing: Check VIP Status</p>
                </div>
                <button class="text-slate-400 hover:text-slate-900">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <div class="p-5 flex flex-col gap-6">
                <!-- Node Name -->
                <div class="flex flex-col gap-3">
                    <label class="text-sm font-medium text-slate-900">Node Name</label>
                    <input class="bg-slate-50 border border-slate-300 rounded-lg p-2.5 text-slate-900 text-sm focus:ring-primary focus:border-primary placeholder-slate-400" type="text" value="Check VIP Status"/>
                </div>

                <!-- Condition Configuration -->
                <div class="flex flex-col gap-3">
                    <label class="text-sm font-medium text-slate-900 flex items-center justify-between">
                        Condition 
                        <span class="text-[10px] text-primary bg-primary/10 px-2 py-0.5 rounded-full uppercase">Logic</span>
                    </label>
                    <div class="p-3 bg-slate-50 rounded-lg border border-slate-200 flex flex-col gap-3">
                        <!-- Variable -->
                        <div class="flex flex-col gap-1.5">
                            <span class="text-xs text-slate-600">If Variable</span>
                            <div class="flex items-center bg-white border border-slate-300 rounded px-2 py-2 gap-2 cursor-pointer hover:border-slate-400">
                                <span class="material-symbols-outlined text-[16px] text-node-trigger">data_object</span>
                                <span class="text-sm text-slate-900 font-mono flex-1">{Visitor.Type}</span>
                                <span class="material-symbols-outlined text-[16px] text-slate-400">expand_more</span>
                            </div>
                        </div>

                        <!-- Operator -->
                        <div class="flex flex-col gap-1.5">
                            <span class="text-xs text-slate-600">Operator</span>
                            <div class="relative">
                                <select class="w-full bg-white border border-slate-300 text-slate-900 text-sm rounded px-2 py-2 appearance-none focus:ring-primary focus:border-primary">
                                    <option>Is Equal To</option>
                                    <option>Is Not Equal To</option>
                                    <option>Contains</option>
                                    <option>Is Empty</option>
                                </select>
                                <span class="absolute right-2 top-2.5 material-symbols-outlined text-[16px] text-slate-400 pointer-events-none">expand_more</span>
                            </div>
                        </div>

                        <!-- Value -->
                        <div class="flex flex-col gap-1.5">
                            <span class="text-xs text-slate-600">Value</span>
                            <input class="bg-white border border-slate-300 text-slate-900 text-sm rounded px-2 py-2 focus:ring-primary focus:border-primary" type="text" value="VIP"/>
                        </div>
                    </div>
                    <button class="text-xs text-primary font-medium flex items-center gap-1 hover:underline">
                        <span class="material-symbols-outlined text-[14px]">add</span>
                        Add Another Condition (AND/OR)
                    </button>
                </div>

                <div class="h-px bg-slate-200 w-full"></div>

                <!-- Notes -->
                <div class="flex flex-col gap-3">
                    <label class="text-sm font-medium text-slate-900">Notes</label>
                    <textarea class="bg-slate-50 border border-slate-300 rounded-lg p-2.5 text-slate-900 text-sm focus:ring-primary focus:border-primary placeholder-slate-400 resize-none" placeholder="Add comments for your team..." rows="3"></textarea>
                </div>
            </div>

            <!-- Footer Actions -->
            <div class="mt-auto p-5 border-t border-slate-200 bg-slate-50">
                <div class="flex gap-3">
                    <button class="flex-1 bg-white hover:bg-slate-100 text-slate-700 py-2 rounded-lg text-sm font-medium border border-slate-300 transition-colors">Cancel</button>
                    <button class="flex-1 bg-primary hover:bg-primary/90 text-white py-2 rounded-lg text-sm font-bold shadow-lg shadow-primary/20 transition-colors">Save Changes</button>
                </div>
            </div>
        </aside>
        </div>
    </div>
</body>
</html>
