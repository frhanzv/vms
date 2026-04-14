<!DOCTYPE html>
<?php $current = service('uri')->getPath(); ?>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title><?= esc($pageTitle) ?></title>
    <link rel="icon" type="image/png" href="<?= base_url('assets/images/vms-icon.png') ?>"/>
    
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800;900&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
    
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- jQuery (Needed for DataTables & Select2) -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    
    <!-- Flatpickr for datetime picker -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <!-- SheetJS for Export -->
    <script src="https://cdn.jsdelivr.net/npm/xlsx-js-style@1.2.0/dist/xlsx.bundle.js"></script>

    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#535dec", // Matches the actual system colors
                        "background-light": "#f6f7f8",
                        "background-dark": "#101922",
                    },
                    fontFamily: {
                        "display": ["Montserrat", "sans-serif"],
                        "sans": ["Montserrat", "sans-serif"]
                    },
                    borderRadius: {"DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px"},
                },
            },
        }
    </script>
    
    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 20px; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        .select2-container .select2-selection--single {
            height: 42px !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 0.5rem !important;
            font-family: inherit !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 40px !important;
            padding-left: 0.75rem !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            top: 6px !important;
            right: 8px !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__clear {
            margin-right: -4px !important;
            margin-top: 10px !important;
        }
        .select2-search__field {
            font-family: 'Montserrat', sans-serif !important;
            outline: none !important;
        }

        /* DataTables overrides mapped consistently */
        .dataTables_wrapper .dataTables_length select {
            appearance: none;
            background-color: white;
            border: 1px solid #d1d5db;
            color: #374151;
            padding: 0.375rem 2rem 0.375rem 0.75rem;
            border-radius: 0.375rem;
            font-size: 0.75rem;
            font-family: 'Montserrat', sans-serif;
            font-weight: 600;
            cursor: pointer;
            outline: none;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%236b7280'%3e%3cpath d='M16.59 8.59L12 13.17 7.41 8.59 6 10l6 6 6-6z'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 0.5rem center;
            background-size: 1.25em;
        }
        .dataTables_wrapper .dataTables_length label {
            margin: 0;
            color: transparent; 
        }
        .dataTables_wrapper .dataTables_filter label {
            font-size: 1rem;
            color: #334155;
            display: flex;
            align-items: center;
            font-family: inherit;
        }
        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid #cbd5e1;
            border-radius: 0.375rem;
            padding: 0.375rem 0.75rem;
            font-size: 0.875rem;
            font-family: 'Montserrat', sans-serif;
            outline: none;
            min-width: 200px;
            margin-left: 0.5rem;
        }
        .dataTables_wrapper .dataTables_filter input:focus {
            border-color: #137fec;
            box-shadow: 0 0 0 2px rgba(19,127,236,0.15);
        }
        table.dataTable thead th {
            border-bottom: 2px solid #e2e8f0;
            padding: 0.75rem 1rem;
            font-size: 0.75rem;
            text-transform: uppercase;
            font-weight: 700;
            color: #475569;
            font-family: 'Montserrat', sans-serif;
            letter-spacing: 0.05em;
        }
        table.dataTable.no-footer { border-bottom: 1px solid #e2e8f0; }
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            border-radius: 0.25rem !important;
            border: 1px solid #e2e8f0 !important;
            font-size: 0.8rem;
            font-family: 'Montserrat', sans-serif;
            padding: 0.3rem 0.85rem !important;
            margin: 0 0.15rem;
            background: white !important;
            color: #64748b !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current,
        .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
            background: #535dec !important;
            color: white !important;
            border: none !important;
        }
    </style>
</head>

<body class="bg-background-light dark:bg-background-dark font-display text-slate-900 dark:text-slate-100 antialiased overflow-hidden">
<div class="flex h-screen w-full flex-col">
    <!-- Navbar placeholder from typical layout -->

    <div class="flex flex-1 overflow-hidden">
        <?= view('reports/partials/report_sidebar', ['current' => $current]) ?>
        
        <main class="flex-1 overflow-y-auto bg-background-light dark:bg-background-dark custom-scrollbar p-6 lg:p-10 relative">
            <div class="mx-auto max-w-7xl flex flex-col gap-6">
                
                <!-- Page Header -->
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-widest text-[#535dec] mb-1">Reports</p>
                        <h1 class="text-3xl font-black tracking-tight text-slate-900 dark:text-white mb-2">Visitor Info By Door</h1>
                        <p class="text-slate-500 dark:text-slate-400 text-sm font-medium">Analyse visitors by entrance or location</p>
                    </div>
                </div>

                <!-- Filters Section -->
                <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm p-6 grid grid-cols-1 lg:grid-cols-3 gap-6 items-end">
                    <!-- Location Dropdown -->
                    <div class="flex flex-col gap-1.5 min-w-0">
                        <label class="text-xs font-semibold text-slate-500 tracking-wider" for="locationSelect">
                            Select Location
                        </label>
                        <div class="relative">
                            <select id="locationSelect" class="w-full border border-slate-200 dark:border-slate-700 rounded-lg pl-4 pr-10 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary appearance-none bg-none cursor-pointer">
                                <option value="">Select location...</option>
                                <?php foreach ($locations as $loc): ?>
                                    <option value="<?= esc($loc['id']) ?>"><?= esc($loc['id']) ?>. <?= esc($loc['branch']) ?> - <?= esc($loc['location_access']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 material-symbols-outlined text-[18px] text-slate-400 pointer-events-none">expand_more</span>
                        </div>
                        <p class="text-xs text-slate-400">Select scanning location</p>
                    </div>
                    
                    <!-- Date Picker -->
                    <div class="flex flex-col gap-1.5 min-w-0">
                        <label class="text-xs font-semibold text-slate-500 tracking-wider" for="dateSelect">
                            Select Date
                        </label>
                        <div class="relative">
                            <input type="text" id="dateSelect" class="w-full border border-slate-200 dark:border-slate-700 rounded-lg px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary cursor-pointer" placeholder="Select a date">
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 material-symbols-outlined text-[18px] text-slate-400 pointer-events-none">calendar_today</span>
                        </div>
                        <p class="text-xs text-slate-400">Visitor check-in date</p>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex gap-3 mb-6">
                        <button onclick="fetchVisitors()" class="flex items-center justify-center gap-2 px-6 py-2.5 rounded-lg bg-[#535dec] hover:bg-[#4853e0] text-white font-bold text-sm shadow-md shadow-primary/20 transition-all whitespace-nowrap h-[42px] min-w-[140px]">
                            <span class="material-symbols-outlined text-[18px]">search</span>
                            Fetch Visitors
                        </button>
                        
                        <button onclick="exportExcel()" id="exportBtn" class="flex items-center justify-center gap-2 px-6 py-2.5 rounded-lg bg-[#59ab73] hover:bg-[#4d9763] text-white font-bold text-sm transition-colors shadow-sm whitespace-nowrap h-[42px] min-w-[140px]">
                            <span class="material-symbols-outlined text-[18px]">download</span>
                            Export Excel
                        </button>
                    </div>
                </div>
                
                <!-- Results Section -->
                <div id="resultsSection" class="hidden flex-col gap-0">
                    
                    <!-- Alert Banner -->
                    <div class="px-5 py-4 bg-[#cceef0] border border-[#a2e2e7] rounded-md flex justify-between items-center bg-opacity-70 dark:bg-[#164e63] dark:border-[#0891b2] mb-5">
                        <div class="flex items-center text-[#0f5f68] dark:text-cyan-100 text-[13px]">
                            <span class="material-symbols-outlined text-[18px] mr-2">info</span>
                            <span class="font-medium">Showing visitors for: <b id="lblLocation" class="font-bold"></b> | Date: <b id="lblDate" class="font-bold"></b> | Total Visitors: </span>
                            <span id="lblTotal" class="ml-2 bg-[#2d7bf4] text-white text-[11px] font-bold px-2 py-0.5 rounded-full"></span>
                        </div>
                        <div class="text-[#0f5f68] dark:text-cyan-100 text-[13px] font-medium">
                            Last Updated: <span id="lblUpdated"></span>
                        </div>
                    </div>

                    <!-- Data Table Header & Actions -->
                    <div class="flex flex-col md:flex-row md:items-end justify-between items-center bg-white dark:bg-slate-900 rounded-t-xl border border-slate-200 dark:border-slate-700 shadow-sm border-b-0 p-5 mt-2">
                        <h2 class="text-xl font-bold tracking-tight text-slate-900 dark:text-white mt-1">Visitor Records</h2>
                        
                        <div class="flex items-center gap-3">
                            <label class="text-sm font-medium text-slate-500 whitespace-nowrap">Search logs:</label>
                            <input type="text" id="customSearchBox" class="border border-slate-300 dark:border-slate-600 rounded-md px-3 py-[7px] text-sm focus:ring-[#535dec] focus:border-[#535dec] outline-none min-w-[200px] w-full max-w-[280px]">
                        </div>
                    </div>

                    <!-- Data Table Card -->
                    <div class="bg-white dark:bg-slate-900 rounded-b-xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden border-t-0 p-5 pt-0">
                        <div class="overflow-x-auto custom-scrollbar pb-2">
                            <table id="doorTable" class="w-full whitespace-nowrap text-left" style="width:100%">
                                <thead>
                                    <tr>
                                        <!-- Keep track of these exact column headers for Export mapping -->
                                        <th>#</th>
                                        <th>Visitor Name</th>
                                        <th>Contact No</th>
                                        <th>Staff No</th>
                                        <th>Person Visited</th>
                                        <th>Check-in Time</th>
                                        <th>Location</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="tableBody" class="text-sm">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Empty State Wrapper placeholder -->
                <div id="emptyState" class="hidden bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm py-16 flex flex-col items-center justify-center text-center">
                    <span class="material-symbols-outlined text-6xl text-slate-200 dark:text-slate-700 mb-4 block">search_off</span>
                    <p class="text-slate-500 font-medium">No results found for the selected parameters.</p>
                </div>

            </div>
        </main>
    </div>
</div>

<!-- Reused Detail Modal Logic -->
<div id="detailModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-[100] flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-2xl max-w-[650px] w-full transform transition-all">
        <!-- Header -->
        <div class="border-b border-gray-200 dark:border-gray-700 px-6 py-4 flex items-center justify-between">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <span class="material-symbols-outlined text-[#535dec]">assignment_ind</span>
                Log Details
            </h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <span class="material-symbols-outlined text-2xl">close</span>
            </button>
        </div>

        <!-- Body -->
        <div class="p-6 space-y-6">
            <div class="flex items-start gap-4 pb-6 border-b border-gray-100 dark:border-gray-700">
                <div class="h-14 w-14 rounded-full bg-[#f1f5f9] dark:bg-slate-700 flex items-center justify-center text-slate-400 border border-slate-200">
                    <span class="material-symbols-outlined text-3xl">person</span>
                </div>
                <div class="flex-1 min-w-0">
                    <h4 class="text-xl font-bold text-slate-900 dark:text-white mb-1 truncate" id="mFullName">-</h4>
                    <p class="text-sm text-slate-500 flex items-center gap-1">
                        <span class="material-symbols-outlined text-[16px]">domain</span>
                        <span id="mCompany" class="truncate">-</span>
                    </p>
                </div>
                <div id="mStatusBadge"></div>
            </div>
            
            <div class="grid grid-cols-2 gap-y-6 gap-x-8">
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1 uppercase tracking-wider">Checkin Time</label>
                    <p class="text-slate-800 dark:text-slate-200 font-medium text-sm flex items-center gap-2">
                        <span class="material-symbols-outlined text-[16px] text-slate-400">login</span>
                        <span id="mCheckIn">-</span>
                    </p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1 uppercase tracking-wider">Location Scanned</label>
                    <p class="text-slate-800 dark:text-slate-200 font-medium text-sm flex items-center gap-2">
                        <span class="material-symbols-outlined text-[16px] text-slate-400">door_front</span>
                        <span id="mLocation">-</span>
                    </p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1 uppercase tracking-wider">IC / Passport</label>
                    <p class="text-slate-800 dark:text-slate-200 font-medium text-sm" id="mIC">-</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1 uppercase tracking-wider">Contact No</label>
                    <p class="text-slate-800 dark:text-slate-200 font-medium text-sm" id="mContact">-</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1 uppercase tracking-wider">Person Visited</label>
                    <p class="text-[#535dec] font-medium text-sm" id="mPerson">-</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1 uppercase tracking-wider">Purpose of Visit</label>
                    <p class="text-slate-800 dark:text-slate-200 font-medium text-sm" id="mReason">-</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let dtTable = null;
    let globalVisitorData = [];

    // Table Headers specifically mapped for Export
    const tableHeaders = [
        "No.", "Visitor Name", "Contact No", "Staff No", "Person Visited", "Check-in Time", "Location"
    ];

    document.addEventListener('DOMContentLoaded', () => {
        // Remove Select2 Initialization
        
        // Initialize Flatpickr matching Access Report explicitly
        flatpickr("#dateSelect", {
            dateFormat: "Y-m-d",
            defaultDate: "today",
            disableMobile: true
        });
        
        initTable();
    });

    function initTable() {
        dtTable = $('#doorTable').DataTable({
            pageLength: 10,
            lengthMenu: [
                [10, 25, 50],
                ['10 ITEMS PER PAGE', '25 ITEMS PER PAGE', '50 ITEMS PER PAGE']
            ],
            ordering: true,
            responsive: false, 
            dom: '<"overflow-x-auto"t><"flex flex-col md:flex-row justify-between items-center gap-4 mt-6"p<"ml-auto"l>>',
            language: { 
                lengthMenu: "_MENU_",
                paginate: {
                    previous: "&laquo;",
                    next: "&raquo;"
                }
            },
            columnDefs: [
                { orderable: false, targets: [7] }, // Disable sorting on Action
                { className: "text-center", targets: [7] }
            ]
        });

        // Bind custom search box directly to data table API
        $('#customSearchBox').on('keyup', function() {
            dtTable.search(this.value).draw();
        });
    }

    function esc(s) {
        if (s === null || s === undefined) return '-';
        return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }
    
    function getStatusPillBadge(status) {
        if (!status || status === 'N/A' || status === '-') return '<span class="text-slate-300 font-semibold uppercase">NULL</span>';
        const s = status.toLowerCase();
        const baseClasses = "px-3 py-1 rounded-md text-xs font-medium inline-flex items-center justify-center min-w-[80px]";
        
        if (s === 'completed' || s === 'approved') {
            return `<span class="${baseClasses} bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-300 capitalize">${status}</span>`;
        } else if (s === 'active' || s === 'pending') {
            return `<span class="${baseClasses} bg-yellow-100 text-yellow-700 dark:bg-yellow-900/50 dark:text-yellow-300 capitalize">${status}</span>`;
        } 
        return `<span class="${baseClasses} bg-gray-100 text-gray-700 capitalize">${esc(status)}</span>`;
    }

    function fetchVisitors() {
        const locationId = $('#locationSelect').val();
        // Since altInput is used, the main input continues resolving raw YYYY-MM-DD cleanly mapped inside Flatpickr
        const rawDate = document.getElementById('dateSelect').value;
        
        if (!locationId || !rawDate) {
            alert('Please select both a Location and Date to proceed.');
            return;
        }

        const btn = document.querySelector('button[onclick="fetchVisitors()"]');
        const origContent = btn.innerHTML;
        btn.innerHTML = `<span class="material-symbols-outlined text-[18px] mr-1.5 animate-spin">autorenew</span>Loading...`;
        btn.disabled = true;
        
        // Hide UI while loading
        document.getElementById('emptyState').classList.add('hidden');
        document.getElementById('resultsSection').classList.add('hidden');
        document.getElementById('resultsSection').classList.remove('flex');
        
        const fd = new FormData();
        fd.append('location', locationId);
        fd.append('date', rawDate);

        fetch('<?= base_url('report/bydoor/generate') ?>', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: fd
        })
        .then(res => res.json())
        .then(data => {
            btn.innerHTML = origContent;
            btn.disabled = false;
            
            if (!data.success) {
                alert(data.message || 'An error occurred fetching records.');
                return;
            }
            
            globalVisitorData = data.visitors;
            
            if (globalVisitorData.length === 0) {
                if (dtTable) dtTable.clear().draw();
                document.getElementById('emptyState').classList.remove('hidden');
                return;
            }
            
            // Build visual dynamic rows manually
            buildTable(data.visitors);
            
            // Paint Banner Params
            document.getElementById('lblLocation').textContent = data.location_text;
            document.getElementById('lblDate').textContent = data.date_text;
            document.getElementById('lblTotal').textContent = data.total_visitors;
            document.getElementById('lblUpdated').textContent = data.last_updated;
            
            document.getElementById('resultsSection').classList.remove('hidden');
            document.getElementById('resultsSection').classList.add('flex');
            
        })
        .catch(e => {
            console.error(e);
            btn.innerHTML = origContent;
            btn.disabled = false;
            alert('A network error triggered. Try again.');
        });
    }

    function buildTable(rows) {
        if (dtTable) dtTable.clear();
        
        rows.forEach((v, i) => {
            // Null parsing for staff no pill
            let staffPill = v.staff_no;
            if(!staffPill || staffPill === 'null' || staffPill === 'N/A') {
                staffPill = `<span class="bg-[#1fc9ea] text-white px-2.5 py-0.5 rounded-full text-[11px] font-bold">null</span>`;
            } else {
                staffPill = `<span class="text-slate-600 font-medium">${esc(staffPill)}</span>`;
            }

            const trNode = dtTable.row.add([
                `<span class="text-slate-500 font-medium">${i + 1}</span>`,
                `<span class="text-slate-600 font-medium tracking-tight text-[13px] capitalize">${esc(v.visitor_name)}</span>`,
                `<span class="text-slate-500 uppercase tracking-tight text-[13px]">${esc(v.contact_no)}</span>`,
                staffPill,
                `<span class="text-slate-500 tracking-tight text-[13px] capitalize">${esc(v.person_visited)}</span>`,
                `<span class="text-slate-500 tracking-tight text-[13px] font-medium">${esc(v.checkin_time)}</span>`,
                `<span class="bg-gray-400 text-white rounded-full px-2 py-0.5 text-[11px] font-bold">${esc(v.location_name)}</span>`,
                `<div class="flex justify-center"><span class="material-symbols-outlined text-[18px] text-[#535dec] cursor-pointer hover:text-blue-800 transition-colors" onclick="openModal(${i})">visibility</span></div>`
            ]).node();
            
            // Add custom padding directly safely
            $(trNode).find('td').addClass('py-3 align-middle');
        });
        
        dtTable.draw(false);
    }
    
    function exportExcel() {
        if (!globalVisitorData.length) {
            alert("No data available to export! Please fetch visitors first.");
            return;
        }
        
        const exportData = [tableHeaders];
        
        globalVisitorData.forEach((r, i) => {
            exportData.push([
                i + 1,
                r.visitor_name || '-',
                r.contact_no || '-',
                r.staff_no || '-',
                r.person_visited || '-',
                r.checkin_time || '-',
                r.location_name || '-'
            ]);
        });
        
        const ws = XLSX.utils.aoa_to_sheet(exportData);
        
        for (let C = 0; C < tableHeaders.length; ++C) {
            const cell_ref = XLSX.utils.encode_cell({c: C, r: 0});
            if (!ws[cell_ref]) continue;
            ws[cell_ref].s = {
                fill: { fgColor: { rgb: "FF535DEC" } }, 
                font: { color: { rgb: "FFFFFFFF" }, bold: true }
            };
        }
        
        const wscols = tableHeaders.map(() => ({wch: 20}));
        ws['!cols'] = wscols;

        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, "Visitor Report");
        XLSX.writeFile(wb, "Visitor_Door_Report_" + new Date().toISOString().slice(0, 10) + ".xlsx");
    }

    function openModal(idx) {
        const v = globalVisitorData[idx];
        if(!v) return;
        
        document.getElementById('mFullName').textContent = v.full_name;
        document.getElementById('mCompany').textContent = v.company;
        document.getElementById('mStatusBadge').innerHTML = getStatusPillBadge(v.status);
        
        document.getElementById('mIC').textContent = v.ic_passport;
        document.getElementById('mContact').textContent = v.contact_no;
        document.getElementById('mPerson').textContent = v.person_visited;
        document.getElementById('mCheckIn').textContent = v.checkin_time;
        document.getElementById('mLocation').textContent = v.location;
        document.getElementById('mReason').textContent = v.reason;

        document.getElementById('detailModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('detailModal').classList.add('hidden');
    }
</script>
</body>
</html>
