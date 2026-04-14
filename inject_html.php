<?php
$file = 'app/Views/config/index.php';
$content = file_get_contents($file);

$deviceAssignmentHtml = <<<HTML
                <!-- Device Assignments Management -->
                <div class="bg-white dark:bg-slate-800 rounded-lg border border-gray-200 dark:border-slate-700 overflow-hidden">
                    <button onclick="toggleSection('device-assignment')" class="w-full px-6 py-4 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors">
                        <div class="flex items-center gap-4">
                            <div class="p-2 bg-primary/10 rounded-lg">
                                <span class="material-symbols-outlined text-primary text-xl">devices</span>
                            </div>
                            <div class="text-left">
                                <h3 class="text-base font-bold text-gray-800 dark:text-white">Device Assignments</h3>
                                <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">Manage physical devices and IP ranges</p>
                            </div>
                        </div>
                        <span id="device-assignment-icon" class="material-symbols-outlined text-gray-400 dark:text-slate-400 transition-transform">expand_more</span>
                    </button>
                    <div id="device-assignment-content" class="hidden border-t border-gray-200 dark:border-slate-700">
                        <!-- IP Range Warning Banner -->
                        <div class="bg-[#ab2b4a] text-white px-6 py-3 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined">warning</span>
                                <div>
                                    <h4 class="font-bold text-sm">Devices Outside Permitted IP Range</h4>
                                    <p class="text-xs opacity-90 text-[#f5f5f5]">We have detected that some devices have IP Addresses that falls outside the allowed IP range.</p>
                                </div>
                            </div>
                            <button onclick="openIpRangeModal()" class="border border-white/50 text-white hover:bg-white/10 px-4 py-2 rounded text-sm font-medium transition-colors">Configure IP Range</button>
                        </div>

                        <div class="p-6 bg-gray-50 dark:bg-slate-800/50">
                            <!-- Search, Sort and Create -->
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4">
                                <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                                    <div class="flex shadow-sm w-full sm:w-96">
                                        <input id="deviceSearch" onkeyup="if(event.key === 'Enter') loadDeviceAssignments(1, this.value)" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-l px-4 py-2.5 text-sm focus:ring-primary focus:border-primary outline-none" placeholder="Search Device ID, IP Address..." type="text"/>
                                        <button onclick="loadDeviceAssignments(1, document.getElementById('deviceSearch').value)" class="bg-primary hover:bg-blue-600 text-white px-6 py-2.5 rounded-r flex items-center justify-center transition-colors">
                                            <span class="material-symbols-outlined text-white text-[20px]">search</span>
                                        </button>
                                    </div>
                                    <button onclick="openIpRangeModal()" class="px-4 py-2.5 rounded border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 font-medium hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors text-sm flex items-center gap-2">
                                        <span class="material-symbols-outlined text-base">settings</span>
                                        IP Range Settings
                                    </button>
                                </div>
                                <button onclick="openDeviceAssignmentModal()" class="px-4 py-2.5 rounded bg-primary text-white font-medium hover:bg-blue-600 transition-colors text-sm flex items-center gap-2 w-full sm:w-auto">
                                    <span class="material-symbols-outlined text-base">add</span>
                                    Assign New Device
                                </button>
                            </div>

                            <!-- Devices Table -->
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-sm">
                                    <thead class="text-xs text-gray-600 dark:text-slate-400 uppercase border-b border-gray-200 dark:border-slate-700">
                                        <tr>
                                            <th class="px-4 py-3">Device ID</th>
                                            <th class="px-4 py-3">IP Address</th>
                                            <th class="px-4 py-3">Status</th>
                                            <th class="px-4 py-3">Registration</th>
                                            <th class="px-4 py-3">Location</th>
                                            <th class="px-4 py-3">Type</th>
                                            <th class="px-4 py-3">Last Heartbeat</th>
                                            <th class="px-4 py-3 w-32 text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="device-assignment-table-body" class="text-gray-700 dark:text-slate-300">
                                        <!-- Data will be loaded here -->
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div class="flex flex-col sm:flex-row justify-between items-center gap-4 mt-6" id="device-assignment-pagination">
                                <!-- Pagination will be loaded here -->
                            </div>
                        </div>
                    </div>
                </div>

HTML;

// Insert Device Assignments Management right before System Logs
$content = preg_replace('/(\s*<!-- System Logs -->)/', "\n" . $deviceAssignmentHtml . "$1", $content);

file_put_contents($file, $content);
echo "HTML injected safely.";
