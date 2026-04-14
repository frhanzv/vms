<?php
$file = 'app/Views/config/index.php';
$content = file_get_contents($file);

$modalsHtml = <<<HTML
    <!-- Device Assignment Create/Edit Modal -->
    <div id="deviceAssignmentModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 overflow-y-auto">
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow-xl w-full max-w-2xl mx-4 my-8">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-700 flex items-center justify-between">
                <h3 id="deviceAssignmentModalTitle" class="text-lg font-bold text-gray-800 dark:text-white">Assign New Device</h3>
                <button onclick="closeDeviceAssignmentModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <form id="deviceAssignmentForm" onsubmit="submitDeviceAssignmentForm(event)">
                <div class="p-6 space-y-4 max-h-[calc(100vh-200px)] overflow-y-auto">
                    <input type="hidden" id="deviceAssignmentId" name="deviceAssignmentId">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300 mb-2">Device ID <span class="text-red-500">*</span></label>
                            <input type="text" id="daDeviceId" name="device_id" required class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-4 py-2.5 text-sm focus:ring-primary focus:border-primary outline-none" placeholder="E.g., DEV-001">
                            <p id="daDeviceIdError" class="text-red-500 text-xs mt-1 hidden"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300 mb-2">IP Address <span class="text-red-500">*</span></label>
                            <input type="text" id="daIpAddress" name="ip_address" required class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-4 py-2.5 text-sm focus:ring-primary focus:border-primary outline-none" placeholder="192.168.1.100">
                            <p id="daIpAddressError" class="text-red-500 text-xs mt-1 hidden"></p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300 mb-2">Status <span class="text-red-500">*</span></label>
                            <select id="daStatus" name="status" required class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-4 py-2.5 text-sm focus:ring-primary focus:border-primary outline-none">
                                <option value="Online">Online</option>
                                <option value="Offline">Offline</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300 mb-2">Registration Status <span class="text-red-500">*</span></label>
                            <select id="daRegStatus" name="registration_status" required class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-4 py-2.5 text-sm focus:ring-primary focus:border-primary outline-none">
                                <option value="Registered">Registered</option>
                                <option value="Unregistered">Unregistered</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300 mb-2">Location <span class="text-red-500">*</span></label>
                            <select id="daLocation" name="location_id" required class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-4 py-2.5 text-sm focus:ring-primary focus:border-primary outline-none">
                                <option value="">Select Location</option>
                                <!-- Location options initialized dynamically -->
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300 mb-2">Type <span class="text-red-500">*</span></label>
                            <select id="daType" name="type" required class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-4 py-2.5 text-sm focus:ring-primary focus:border-primary outline-none">
                                <option value="Check-In">Check-In</option>
                                <option value="Check-Out">Check-Out</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-gray-200 dark:border-slate-700 flex items-center justify-end gap-3">
                    <button type="button" onclick="closeDeviceAssignmentModal()" class="px-4 py-2.5 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 font-medium hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors text-sm">Cancel</button>
                    <button type="submit" class="px-4 py-2.5 rounded-lg bg-primary text-white font-medium hover:bg-blue-600 transition-colors text-sm flex items-center gap-2">
                        <span class="material-symbols-outlined text-base">save</span>
                        Save Device
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Device Assignment Modal -->
    <div id="deleteDeviceModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow-xl w-full max-w-md mx-4">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-700">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white">Confirm Delete</h3>
            </div>
            <div class="p-6">
                <p class="text-gray-700 dark:text-slate-300">Are you sure you want to delete this device? This action cannot be undone.</p>
                <p id="deleteDeviceName" class="mt-2 font-semibold text-gray-900 dark:text-white"></p>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 dark:border-slate-700 flex items-center justify-end gap-3">
                <button onclick="closeDeleteDeviceModal()" class="px-4 py-2.5 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 font-medium hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors text-sm">Cancel</button>
                <button onclick="confirmDeleteDevice()" class="px-4 py-2.5 rounded-lg bg-red-500 text-white font-medium hover:bg-red-600 transition-colors text-sm flex items-center gap-2">
                    <span class="material-symbols-outlined text-base">delete</span>
                    Delete Device
                </button>
            </div>
        </div>
    </div>

    <!-- IP Range Settings Modal -->
    <div id="ipRangeModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow-xl w-full max-w-md mx-4">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-700 flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white">IP Range Settings</h3>
                <button onclick="closeIpRangeModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <form onsubmit="submitIpRangeForm(event)">
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300 mb-2">IP Range From <span class="text-red-500">*</span></label>
                        <input type="text" id="ipRangeFrom" required class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-4 py-2.5 text-sm focus:ring-primary focus:border-primary outline-none" placeholder="192.168.1.1">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300 mb-2">IP Range To <span class="text-red-500">*</span></label>
                        <input type="text" id="ipRangeTo" required class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded px-4 py-2.5 text-sm focus:ring-primary focus:border-primary outline-none" placeholder="192.168.1.255">
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-gray-200 dark:border-slate-700 flex items-center justify-end gap-3">
                    <button type="button" onclick="closeIpRangeModal()" class="px-4 py-2.5 rounded-lg border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 font-medium hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors text-sm">Cancel</button>
                    <button type="submit" class="px-4 py-2.5 rounded-lg bg-primary text-white font-medium hover:bg-blue-600 transition-colors text-sm flex items-center gap-2">
                        <span class="material-symbols-outlined text-base">save</span>
                        Save Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
HTML;

$content = preg_replace('/(\s*<!-- Toast Notification Container -->)/', "\n" . $modalsHtml . "$1", $content);
file_put_contents($file, $content);

$jsCode = <<<JS
        // ============== DEVICE ASSIGNMENTS & IP RANGE ==============
        
        let deleteDeviceId = null;
        let globalIpRange = { from: '', to: '' };

        function ip2long(ip) {
            let parts = ip.split('.');
            if (parts.length !== 4) return 0;
            return (parts[0] << 24) | (parts[1] << 16) | (parts[2] << 8) | parts[3];
        }

        function isIpInRange(ip, from, to) {
            if (!from || !to) return true;
            let ipL = ip2long(ip);
            let fromL = ip2long(from);
            let toL = ip2long(to);
            return ipL >= fromL && ipL <= toL;
        }

        function fetchIpRangeSettings() {
            fetch('<?= base_url('config/getIpRangeSettings') ?>')
                .then(res => res.json())
                .then(data => {
                    if(data.success && data.data) {
                        globalIpRange.from = data.data.ip_range_from || '';
                        globalIpRange.to = data.data.ip_range_to || '';
                    }
                });
        }
        
        // Initialize fetch IP range
        fetchIpRangeSettings();

        // Tie loadDeviceAssignments onto the toggle wrapper
        const originalToggle = toggleSection;
        toggleSection = function(section) {
            originalToggle(section);
            if(section === 'device-assignment') {
                fetchIpRangeSettings();
                loadDeviceAssignments();
                fetchLocationsForDevice();
            }
        };

        function fetchLocationsForDevice() {
            fetch('<?= base_url('config/getAllLocations') ?>')
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const select = document.getElementById('daLocation');
                        select.innerHTML = '<option value="">Select Location</option>' + 
                            data.data.map(loc => `<option value="\${loc.id}">\${escapeHtml(loc.name)}</option>`).join('');
                    }
                })
                .catch(err => console.log(err));
        }

        function loadDeviceAssignments(page = 1, search = '') {
            fetch(`<?= base_url('config/getDeviceAssignments') ?>?page=\${page}&per_page=10&search=\${encodeURIComponent(search)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayDevices(data.data);
                        // Using utility render function since this file implements an internal pagination or use simple numeric.
                        // I will write a simple html pagination generic logic.
                    }
                });
        }

        function displayDevices(devices) {
            const tbody = document.getElementById('device-assignment-table-body');
            if (devices.length === 0) {
                tbody.innerHTML = '<tr><td colspan="8" class="px-4 py-8 text-center text-gray-500">No device assignments found</td></tr>';
                return;
            }

            tbody.innerHTML = devices.map(device => {
                const isOutOfRange = !isIpInRange(device.ip_address, globalIpRange.from, globalIpRange.to);
                
                const statusClass = device.status === 'Online' ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-500';
                const regClass = device.registration_status === 'Registered' ? 'bg-blue-500/20 text-blue-500' : 'bg-yellow-500/20 text-yellow-600';
                const warningIcon = isOutOfRange ? '<span class="material-symbols-outlined text-red-500 text-sm ml-1" title="Out of IP Range">warning</span>' : '';

                return `
                    <tr class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-100 dark:hover:bg-slate-700/30">
                        <td class="px-4 py-3 font-medium">\${escapeHtml(device.device_id)}</td>
                        <td class="px-4 py-3 flex items-center">\${escapeHtml(device.ip_address)} \${warningIcon}</td>
                        <td class="px-4 py-3"><span class="px-2 py-1 rounded text-xs font-semibold \${statusClass}">\${device.status}</span></td>
                        <td class="px-4 py-3"><span class="px-2 py-1 rounded text-xs font-semibold \${regClass}">\${device.registration_status}</span></td>
                        <td class="px-4 py-3">\${escapeHtml(device.location_name || '-')}</td>
                        <td class="px-4 py-3 text-slate-500">\${escapeHtml(device.type)}</td>
                        <td class="px-4 py-3 text-slate-500">\${device.last_heartbeat ? new Date(device.last_heartbeat).toLocaleString() : '-'}</td>
                        <td class="px-4 py-3 text-center">
                            <button onclick="openDeviceAssignmentModal(\${device.id})" class="text-primary hover:text-primary/80 mr-2"><span class="material-symbols-outlined text-base">edit</span></button>
                            <button onclick="openDeleteDeviceModal(\${device.id}, '\${escapeHtml(device.device_id)}')" class="text-red-500 hover:text-red-400"><span class="material-symbols-outlined text-base">delete</span></button>
                        </td>
                    </tr>
                `;
            }).join('');
        }

        function openDeviceAssignmentModal(id = null) {
            document.getElementById('deviceAssignmentForm').reset();
            document.getElementById('deviceAssignmentId').value = '';
            document.getElementById('deviceAssignmentModalTitle').innerText = 'Assign New Device';

            if (id) {
                document.getElementById('deviceAssignmentModalTitle').innerText = 'Edit Device';
                fetch(`<?= base_url('config/getDeviceAssignment') ?>/\${id}`)
                    .then(r => r.json())
                    .then(data => {
                        if(data.success) {
                            const d = data.data;
                            document.getElementById('deviceAssignmentId').value = d.id;
                            document.getElementById('daDeviceId').value = d.device_id;
                            document.getElementById('daIpAddress').value = d.ip_address;
                            document.getElementById('daStatus').value = d.status;
                            document.getElementById('daRegStatus').value = d.registration_status;
                            document.getElementById('daLocation').value = d.location_id;
                            document.getElementById('daType').value = d.type;
                        }
                    });
            }
            document.getElementById('deviceAssignmentModal').classList.remove('hidden');
            document.getElementById('deviceAssignmentModal').classList.add('flex');
        }

        function closeDeviceAssignmentModal() {
            document.getElementById('deviceAssignmentModal').classList.add('hidden');
            document.getElementById('deviceAssignmentModal').classList.remove('flex');
        }

        function submitDeviceAssignmentForm(e) {
            e.preventDefault();
            const id = document.getElementById('deviceAssignmentId').value;
            const url = id ? `<?= base_url('config/updateDeviceAssignment') ?>/\${id}` : '<?= base_url('config/createDeviceAssignment') ?>';

            const payload = {
                device_id: document.getElementById('daDeviceId').value,
                ip_address: document.getElementById('daIpAddress').value,
                status: document.getElementById('daStatus').value,
                registration_status: document.getElementById('daRegStatus').value,
                location_id: document.getElementById('daLocation').value,
                type: document.getElementById('daType').value,
            };

            fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            }).then(r => r.json()).then(data => {
                if(data.success) {
                    showToast(data.message, 'success');
                    closeDeviceAssignmentModal();
                    loadDeviceAssignments();
                } else {
                    showToast(data.message || 'Error saving device', 'error');
                }
            });
        }

        function openDeleteDeviceModal(id, name) {
            deleteDeviceId = id;
            document.getElementById('deleteDeviceName').innerText = name;
            document.getElementById('deleteDeviceModal').classList.remove('hidden');
            document.getElementById('deleteDeviceModal').classList.add('flex');
        }

        function closeDeleteDeviceModal() {
            document.getElementById('deleteDeviceModal').classList.add('hidden');
            document.getElementById('deleteDeviceModal').classList.remove('flex');
            deleteDeviceId = null;
        }

        function confirmDeleteDevice() {
            fetch(`<?= base_url('config/deleteDeviceAssignment') ?>/\${deleteDeviceId}`, { method: 'POST' })
                .then(r => r.json()).then(data => {
                    if(data.success) {
                        showToast(data.message, 'success');
                        closeDeleteDeviceModal();
                        loadDeviceAssignments();
                    } else {
                        showToast(data.message || 'Error occurred', 'error');
                    }
                });
        }

        function openIpRangeModal() {
            document.getElementById('ipRangeFrom').value = globalIpRange.from;
            document.getElementById('ipRangeTo').value = globalIpRange.to;
            document.getElementById('ipRangeModal').classList.remove('hidden');
            document.getElementById('ipRangeModal').classList.add('flex');
        }

        function closeIpRangeModal() {
            document.getElementById('ipRangeModal').classList.add('hidden');
            document.getElementById('ipRangeModal').classList.remove('flex');
        }

        function submitIpRangeForm(e) {
            e.preventDefault();
            const payload = {
                ip_range_from: document.getElementById('ipRangeFrom').value,
                ip_range_to: document.getElementById('ipRangeTo').value
            };
            fetch('<?= base_url('config/saveIpRangeSettings') ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            }).then(r => r.json()).then(data => {
                if(data.success) {
                    showToast(data.message, 'success');
                    closeIpRangeModal();
                    fetchIpRangeSettings();
                    loadDeviceAssignments(); // reload to show/hide warnings
                } else {
                    showToast(data.message || 'Error saving settings', 'error');
                }
            });
        }
JS;

$content = preg_replace('/(<\/script>\s*<\/body>)/', "\n" . $jsCode . "\n$1", $content);
file_put_contents($file, $content);
echo "JS injected safely.";
