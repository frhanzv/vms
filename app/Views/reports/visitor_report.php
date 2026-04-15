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
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#137fec",
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
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Flatpickr for datetime picker -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <!-- SheetJS with Styles for Excel Export -->
    <script src="https://cdn.jsdelivr.net/npm/xlsx-js-style@1.2.0/dist/xlsx.bundle.js"></script>
    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 20px; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        /* Override DataTables to match VMS style */
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
            color: transparent; /* Hides any leftover text if present natively */
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
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter {
            font-size: 0.8rem;
            color: #64748b;
            font-family: 'Montserrat', sans-serif;
        }
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
            background: #137fec !important;
            color: white !important;
            border: none !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: #e0effe !important;
            color: #137fec !important;
            border: none !important;
        }
        table.dataTable thead th {
            background: #f8fafc;
            color: #475569;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 2px solid #e2e8f0 !important;
            padding: 0.85rem 1rem;
            white-space: nowrap;
        }
        table.dataTable tbody td {
            font-size: 0.82rem;
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
            vertical-align: middle;
        }
        table.dataTable tbody tr:hover td { background: #f0f7ff; }
        table.dataTable { border-collapse: collapse !important; width: 100% !important; }

        /* Flatpickr custom */
        .flatpickr-input {
            background: white !important;
        }
        .flatpickr-calendar {
            font-family: 'Montserrat', sans-serif;
            font-size: 0.82rem;
        }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark font-display text-slate-900 dark:text-slate-100 antialiased overflow-hidden">
<div class="flex h-screen w-full flex-col">
    <div class="flex flex-1 overflow-hidden">

        <?= view('reports/partials/report_sidebar', ['current' => $current]) ?>

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto bg-background-light dark:bg-background-dark custom-scrollbar p-6 lg:p-10 relative">
            <div class="mx-auto max-w-7xl flex flex-col gap-6">

                <!-- Page Header -->
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-widest text-[#535dec] mb-1">Reports</p>
                        <h1 class="text-3xl font-black tracking-tight text-slate-900 dark:text-white mb-2">Visitor Report</h1>
                        <p class="text-slate-500 dark:text-slate-400 text-sm font-medium">Comprehensive visitor management and tracking system</p>
                    </div>
                </div>

                <!-- Summary Cards Top Row -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-[#3b5998] rounded-xl border border-[#3b5998] p-5 shadow flex flex-col justify-between">
                        <p class="text-[0.85rem] text-indigo-100 font-medium mb-1">Total Visitors</p>
                        <p id="totalCount" class="text-4xl text-white font-black">0</p>
                    </div>
                    <div class="bg-[#20c997] rounded-xl border border-[#20c997] p-5 shadow flex flex-col justify-between">
                        <p class="text-[0.85rem] text-emerald-50 font-medium mb-1">Completed</p>
                        <p id="completedCount" class="text-4xl text-white font-black">0</p>
                    </div>
                    <div class="bg-[#fd7e14] rounded-xl border border-[#fd7e14] p-5 shadow flex flex-col justify-between">
                        <p class="text-[0.85rem] text-orange-50 font-medium mb-1">Active</p>
                        <p id="activeCount" class="text-4xl text-white font-black">0</p>
                    </div>
                    <div class="bg-[#425cc7] rounded-xl border border-[#425cc7] p-5 shadow flex flex-col justify-between">
                        <p class="text-[0.85rem] text-blue-100 font-medium mb-1">Today's Visitors</p>
                        <p id="todayCount" class="text-4xl text-white font-black">0</p>
                    </div>
                </div>                <!-- Results Section -->
                <div id="resultsSection" class="hidden flex-col gap-4">

                    <!-- Data Table Header & Actions -->
                    <div class="flex flex-col md:flex-row md:items-end justify-between items-center bg-white dark:bg-slate-900 rounded-t-xl border border-slate-200 dark:border-slate-700 shadow-sm border-b-0 p-5 mt-2">
                         <h2 class="text-xl font-bold tracking-tight text-slate-900 dark:text-white mt-1">Visitor Records</h2>
                         
                         <div class="flex gap-2">
                             <button type="button" onclick="openColumnsModal()" class="flex items-center gap-2 bg-[#535dec] hover:bg-[#4853e0] text-white px-4 py-2 rounded-md text-sm font-medium transition-colors shadow-sm">
                                 <span class="material-symbols-outlined text-[18px]">visibility</span>
                                 Show/Hide Columns
                             </button>
                             <button type="button" onclick="exportExcel()" class="flex items-center gap-2 bg-[#53b2ec] hover:bg-[#46a2db] text-white px-4 py-2 rounded-md text-sm font-medium transition-colors shadow-sm">
                                 <span class="material-symbols-outlined text-[18px]">query_stats</span>
                                 Export
                             </button>
                         </div>
                    </div>

                    <!-- Data Table Card -->
                    <div class="bg-white dark:bg-slate-900 rounded-b-xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden border-t-0 p-5 pt-0">
                        <div class="overflow-x-auto custom-scrollbar pb-2">
                            <table id="visitorTable" class="w-full whitespace-nowrap" style="width:100%">
                                <thead>
                                    <tr>
                                        <!-- Keep track of these exact column headers for Export mapping -->
                                        <th>NO</th>
                                        <th>DATE</th>
                                        <th>FULL NAME</th>
                                        <th>IC / PASSPORT NO</th>
                                        <th>CONTACT NO</th>
                                        <th>COMPANY</th>
                                        <th>LOCATION</th>
                                        <th>TIME IN</th>
                                        <th>TIME OUT</th>
                                        <th>PURPOSE OF VISIT</th>
                                        <th>HOST NAME</th>
                                        <th>DURATION</th>
                                        <th>STATUS</th>
                                    </tr>
                                </thead>
                                <tbody id="tableBody">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>



                <!-- Loading State -->
                <div id="loadingState" class="hidden bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm p-16 flex flex-col items-center justify-center gap-3">
                    <div class="size-10 border-4 border-[#535dec]/20 border-t-[#535dec] rounded-full animate-spin"></div>
                    <p class="text-slate-400 font-semibold text-sm">Generating report...</p>
                </div>

                <!-- No Data State -->
                <div id="noDataState" class="hidden bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm p-16 flex flex-col items-center justify-center gap-3">
                    <span class="material-symbols-outlined text-5xl text-slate-200 dark:text-slate-700">search_off</span>
                    <p class="text-slate-400 font-semibold text-sm">No visitors found for the selected period</p>
                </div>

            </div>
        </main>
    </div>
</div>

<!-- Columns Modal Overlay -->
<div id="columnsModal" class="hidden fixed inset-0 z-[100] flex items-center justify-center p-4">
    <div id="columnsModalBackdrop" onclick="closeColumnsModal()" class="absolute inset-0 bg-slate-900/55 dark:bg-black/65 cursor-pointer"></div>
    <div class="relative flex w-full max-w-[600px] flex-col rounded-xl border border-slate-200 bg-white shadow-2xl dark:border-slate-700 dark:bg-slate-900">
        <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4 dark:border-slate-700">
            <h2 class="text-lg font-bold tracking-tight text-[#3b5998] dark:text-white">Show/Hide Columns</h2>
            <button type="button" onclick="closeColumnsModal()" class="text-slate-300 hover:text-slate-500">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>
        <div class="px-6 py-5 overflow-y-auto max-h-[60vh] custom-scrollbar">
            <div class="mb-5 flex items-center gap-2">
                <input type="checkbox" id="selectAllColumns" onchange="toggleAllColumns(this)" class="rounded border-slate-300 text-[#535dec] focus:ring-[#535dec] h-4 w-4 cursor-pointer" checked>
                <label for="selectAllColumns" class="text-sm font-bold text-slate-700 dark:text-slate-300 cursor-pointer">Select All Columns</label>
            </div>
            <div class="grid grid-cols-2 gap-y-3 gap-x-4" id="columnsCheckboxesList">
                <!-- Checkboxes populated here -->
            </div>
        </div>
        <div class="flex justify-end gap-3 border-t border-slate-100 bg-white px-6 py-4 dark:border-slate-700 dark:bg-slate-900 rounded-b-xl border-t-2">
            <button type="button" onclick="closeColumnsModal()" class="rounded-md border border-slate-400 bg-slate-500 hover:bg-slate-600 px-5 py-2 text-sm font-semibold text-white transition-colors">Close</button>
            <button type="button" onclick="applyColumnsVisibility()" class="rounded-md bg-[#535dec] hover:bg-[#4853e0] px-5 py-2 text-sm font-semibold text-white shadow-md transition-colors">Apply Changes</button>
        </div>
    </div>
</div>

<script>
    let dtTable = null;
    let reportData = [];
    
    const tableHeaders = [
        "NO", "DATE", "FULL NAME", "IC / PASSPORT NO", "CONTACT NO", 
        "COMPANY", "LOCATION", "TIME IN", "TIME OUT", "PURPOSE", "HOST NAME", 
        "DURATION", "STATUS"
    ];

    function showState(state) {
        document.getElementById('loadingState').classList.toggle('hidden', state !== 'loading');
        document.getElementById('noDataState').classList.toggle('hidden', state !== 'nodata');
        
        if(state === 'results') {
            document.getElementById('resultsSection').classList.remove('hidden');
            document.getElementById('resultsSection').classList.add('flex');
        } else {
            document.getElementById('resultsSection').classList.add('hidden');
            document.getElementById('resultsSection').classList.remove('flex');
        }
    }

    function generateReport() {
        showState('loading');

        fetch('<?= base_url('report/visitor/generate') ?>', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: new FormData()
        })
        .then(res => res.json())
        .then(data => {
            if (!data.success) {
                alert(data.message || 'An error occurred.');
                return showState('empty');
            }
            if (data.visitors.length === 0) return showState('nodata');
            
            reportData = data.visitors;
            
            // Update Metric Cards
            document.getElementById('totalCount').textContent = data.total_visitors;
            document.getElementById('completedCount').textContent = data.completed;
            document.getElementById('activeCount').textContent = data.active_visitors;
            document.getElementById('todayCount').textContent = data.today_visitors;
            
            renderTable(data);
        })
        .catch(() => showState('empty'));
    }

    function renderTable(data) {
        if (dtTable) dtTable.destroy();
        
        const tbody = document.getElementById('tableBody');
        tbody.innerHTML = '';
        data.visitors.forEach((v, i) => {
            const val = (s) => (!s || s === 'N/A' || s === '-') ? '<span class="text-slate-300 font-semibold uppercase">NULL</span>' : esc(s);
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td class="text-slate-500 font-medium py-4">${i + 1}</td>
                <td class="text-slate-500 py-4">${val(v.visit_date)}</td>
                <td class="font-bold text-slate-800 uppercase tracking-tight text-xs py-4">${val(v.visitor_name)}</td>
                <td class="text-slate-500 py-4">${val(v.ic_no)}</td>
                <td class="text-slate-500 py-4">${val(v.contact_no)}</td>
                <td class="text-slate-500 py-4">${val(v.visitor_company)}</td>
                <td class="text-slate-500 py-4">${val(v.current_location)}</td>
                <td class="text-slate-500 font-medium py-4">${val(v.checkin_time)}</td>
                <td class="text-slate-500 font-medium py-4">${val(v.checkout_time)}</td>
                <td class="text-slate-500 py-4">${val(v.visit_reason)}</td>
                <td class="text-slate-500 uppercase tracking-tight text-xs font-semibold py-4">${val(v.person_visited)}</td>
                <td class="text-slate-500 py-4">${val(v.duration)}</td>
                <td class="text-slate-500 py-4">${getStatusBadge(v.visit_status)}</td>
            `;
            tbody.appendChild(tr);
        });

        dtTable = $('#visitorTable').DataTable({
            pageLength: 10,
            lengthMenu: [
                [10, 25, 50],
                ['10 ITEMS PER PAGE', '25 ITEMS PER PAGE', '50 ITEMS PER PAGE']
            ],
            ordering: true,
            responsive: false, // Disabling so horizontal scroll works
            dom: '<"flex justify-end items-center mb-5 mt-2"f><"overflow-x-auto"t><"flex flex-col md:flex-row justify-between items-center gap-4 mt-6"p<"ml-auto"l>>',
            language: { 
                search: "Search visitors:",
                searchPlaceholder: "",
                lengthMenu: "_MENU_",
                paginate: {
                    previous: "&laquo;",
                    next: "&raquo;"
                }
            },
            scrollX: false, // Handled implicitly by overflow wrapper
            autoWidth: false,
            initComplete: function () {
                var api = this.api();
                api.columns().every(function () {
                    var column = this;
                    var header = $(column.header());
                    // Extract text before appending select
                    var headerText = header.clone().children().remove().end().text().trim().toUpperCase();
                    if (headerText !== 'ACTIONS' && headerText !== 'NO' && headerText !== 'NO.') {
                        header.find('.dt-filter-wrapper').remove();
                        
                        var wrapper = $('<div class="dt-filter-wrapper inline-block relative ml-1 align-middle" onclick="event.stopPropagation()"></div>');
                        var icon = $('<span class="material-symbols-outlined text-[16px] text-slate-300 hover:text-[#535dec] transition-colors cursor-pointer" style="vertical-align: middle;">filter_alt</span>');
                        var dropdown = $('<div class="filter-dropdown hidden absolute top-full left-0 mt-1 bg-white border border-slate-200 rounded shadow-lg z-[50] p-2 text-left text-sm max-h-[250px] overflow-y-auto" style="min-width: 160px; font-weight: normal;"></div>');
                        
                        wrapper.append(icon).append(dropdown);
                        header.append(wrapper);

                        // Only add unique non-empty string values
                        var options = [];
                        column.data().unique().sort().each(function (d, j) {
                            var textVal = $('<div>').html(d).text().trim();
                            if (textVal && textVal !== '-' && textVal !== 'View' && textVal !== 'NULL' && textVal !== 'null') {
                                options.push(textVal);
                            }
                        });

                        var allLabel = $('<label class="flex items-center gap-2 px-2 py-1.5 hover:bg-slate-50 cursor-pointer font-semibold text-slate-700 capitalize mb-1"></label>');
                        var allCb = $('<input type="checkbox" checked class="form-checkbox h-4 w-4 text-[#535dec] accent-[#535dec] rounded border-slate-300 cursor-pointer">');
                        allLabel.append(allCb).append('<span class="select-none">All</span>');
                        dropdown.append(allLabel);
                        dropdown.append('<hr class="my-1 border-slate-200">');

                        var itemCbs = [];
                        options.forEach(function(val) {
                            var itemLabel = $('<label class="flex items-center gap-2 px-2 py-1.5 hover:bg-slate-50 cursor-pointer text-slate-600 capitalize"></label>');
                            var itemCb = $('<input type="checkbox" checked value="' + val.replace(/"/g, '&quot;') + '" class="form-checkbox h-4 w-4 text-[#535dec] accent-[#535dec] rounded border-slate-300 cursor-pointer">');
                            itemLabel.append(itemCb).append('<span class="select-none">' + val + '</span>');
                            dropdown.append(itemLabel);
                            itemCbs.push(itemCb);
                        });

                        icon.on('click', function(e) {
                            e.stopPropagation();
                            $('.filter-dropdown').not(dropdown).addClass('hidden');
                            dropdown.toggleClass('hidden');
                        });
                        
                        $(document).on('click', function(e) {
                            if (!$(e.target).closest(wrapper).length) {
                                dropdown.addClass('hidden');
                            }
                        });

                        function applyFilter() {
                            var selected = [];
                            var allChecked = true;
                            itemCbs.forEach(function(cb) {
                                if(cb.prop('checked')) {
                                    selected.push($.fn.dataTable.util.escapeRegex(cb.val()));
                                } else {
                                    allChecked = false;
                                }
                            });
                            
                            allCb.prop('checked', allChecked);

                            if(selected.length > 0 && selected.length < options.length) {
                                icon.removeClass('text-slate-300 text-red-500').addClass('text-[#535dec]');
                                var regex = '^(' + selected.join('|') + ')$';
                                column.search(regex, true, false).draw();
                            } else if (selected.length === 0) {
                                icon.removeClass('text-slate-300 text-[#535dec]').addClass('text-red-500');
                                column.search('^__NON_EXISTENT_MATCH__$', true, false).draw();
                            } else {
                                icon.removeClass('text-[#535dec] text-red-500').addClass('text-slate-300');
                                column.search('', true, false).draw();
                            }
                        }

                        allCb.on('change', function() {
                            var isChecked = $(this).prop('checked');
                            itemCbs.forEach(function(cb) { cb.prop('checked', isChecked); });
                            applyFilter();
                        });

                        itemCbs.forEach(function(cb) {
                            cb.on('change', applyFilter);
                        });
                    }
                });
            }
        });
        
        initColumnsCheckboxes();
        showState('results');
    }

    function esc(s) {
        if (s === null || s === undefined) return '-';
        return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }
    
    function getStatusBadge(status) {
        if (!status || status === 'N/A' || status === '-') return '<span class="text-slate-300 font-semibold">NULL</span>';
        
        const s = status.toLowerCase();
        const baseClasses = "px-3 py-1 rounded-md text-xs font-medium inline-flex items-center justify-center min-w-[80px]";
        
        if (s === 'completed' || s === 'approved') {
            return `<span class="${baseClasses} bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-300 capitalize">${status}</span>`;
        } else if (s === 'active' || s === 'pending') {
            return `<span class="${baseClasses} bg-yellow-100 text-yellow-700 dark:bg-yellow-900/50 dark:text-yellow-300 capitalize">${status}</span>`;
        } else if (s === 'submitted') {
            return `<span class="${baseClasses} bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-300 capitalize">${status}</span>`;
        } else if (s === 'rejected') {
            return `<span class="${baseClasses} bg-red-100 text-red-700 dark:bg-red-900/50 dark:text-red-300 capitalize">${status}</span>`;
        }
        
        return `<span class="text-slate-500 font-medium text-xs text-center capitalize">${esc(status)}</span>`;
    }

    // Modal & Columns Toggle Logic
    function initColumnsCheckboxes() {
        const container = document.getElementById('columnsCheckboxesList');
        container.innerHTML = '';
        let allChecked = true;
        
        tableHeaders.forEach((colName, idx) => {
            const isVisible = dtTable.column(idx).visible();
            if(!isVisible) allChecked = false;
            
            const div = document.createElement('div');
            div.className = 'flex items-center gap-2';
            div.innerHTML = `
                <input type="checkbox" id="col_${idx}" data-col-idx="${idx}" class="col-toggle-cb rounded border-slate-300 text-[#535dec] focus:ring-[#535dec] h-4 w-4 cursor-pointer" ${isVisible ? 'checked' : ''}>
                <label for="col_${idx}" class="text-sm text-slate-600 dark:text-slate-300 uppercase cursor-pointer">${colName}</label>
            `;
            container.appendChild(div);
        });
        
        document.getElementById('selectAllColumns').checked = allChecked;
        
        // Add event listeners to individual checkboxes to uncheck 'Select All' if one is unchecked
        document.querySelectorAll('.col-toggle-cb').forEach(cb => {
            cb.addEventListener('change', function() {
                const total = document.querySelectorAll('.col-toggle-cb').length;
                const checked = document.querySelectorAll('.col-toggle-cb:checked').length;
                document.getElementById('selectAllColumns').checked = (total === checked);
            });
        });
    }
    
    function openColumnsModal() {
        if(!dtTable) return;
        document.getElementById('columnsModal').classList.remove('hidden');
    }
    
    function closeColumnsModal() {
        document.getElementById('columnsModal').classList.add('hidden');
        // Reset checkboxes state to actual Datatables state
        initColumnsCheckboxes();
    }
    
    function toggleAllColumns(elem) {
        const isChecked = elem.checked;
        document.querySelectorAll('.col-toggle-cb').forEach(cb => {
            cb.checked = isChecked;
        });
    }
    
    function applyColumnsVisibility() {
        document.querySelectorAll('.col-toggle-cb').forEach(cb => {
            const idx = cb.getAttribute('data-col-idx');
            dtTable.column(idx).visible(cb.checked);
        });
        document.getElementById('columnsModal').classList.add('hidden');
    }

    // Export using SheetJS
    function exportExcel() {
        if (!reportData.length || !dtTable) return;
        
        // Find visible columns to determine which to export
        const visibleIndices = dtTable.columns().visible().toArray().map((v, i) => v ? i : -1).filter(v => v !== -1);
        const expHeaders = visibleIndices.map(i => tableHeaders[i]);
        
        const exportData = [expHeaders];
        
        let exportIndex = 1;
        dtTable.rows({search: 'applied'}).every(function() {
            var tr = this.node();
            var tds = $(tr).find('td');
            
            const fullRowData = [];
            tds.each(function(index) {
                if (index === 0) {
                    fullRowData.push(exportIndex++);
                } else {
                    fullRowData.push($(this).text().trim());
                }
            });
            
            const rowData = visibleIndices.map(idx => fullRowData[idx] || '-');
            exportData.push(rowData);
        });

        const ws = XLSX.utils.aoa_to_sheet(exportData);
        
        // Apply styling to the header row
        for (let C = 0; C < expHeaders.length; ++C) {
            const cell_address = {c: C, r: 0};
            const cell_ref = XLSX.utils.encode_cell(cell_address);
            if (!ws[cell_ref]) continue;
            ws[cell_ref].s = {
                fill: { fgColor: { rgb: "FF535DEC" } },
                font: { color: { rgb: "FFFFFFFF" }, bold: true }
            };
        }

        // Optional: you can set column widths here
        const wscols = visibleIndices.map(idx => ({wch: 18})); // default width
        ws['!cols'] = wscols;

        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, "Visitor Report");
        XLSX.writeFile(wb, "Visitor_Report_" + new Date().toISOString().slice(0, 10) + ".xlsx");
    }

    document.addEventListener('DOMContentLoaded', () => {
        generateReport();
    });
</script>
</body>
</html>
