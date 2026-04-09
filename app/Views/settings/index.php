<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title><?= esc($pageTitle) ?></title>
    <link rel="icon" type="image/png" href="<?= base_url('assets/images/vms-icon.png') ?>"/>
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        primary: "#137fec",
                        secondary: "#3b82f6",
                        success: "#10b981",
                        "background-light": "#f6f7f8",
                        "background-dark": "#111827",
                        "card-light": "#ffffff",
                        "card-dark": "#1f2937",
                    },
                    fontFamily: {
                        display: ["Montserrat", "sans-serif"],
                        sans: ["Montserrat", "sans-serif"],
                    },
                },
            },
        };
    </script>
</head>
<body class="bg-background-light dark:bg-background-dark font-sans text-gray-800 dark:text-gray-200 antialiased h-screen flex overflow-hidden transition-colors duration-200">
    <!-- Sidebar -->
    <aside class="w-64 flex-shrink-0 border-r border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 flex flex-col justify-between p-4 hidden md:flex h-full">
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
                <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary dark:hover:text-white transition-colors group" href="<?= base_url('config') ?>">
                    <span class="material-symbols-outlined text-[22px] group-hover:scale-110 transition-transform">tune</span>
                    <p class="text-sm font-medium">Config</p>
                </a>
                <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg bg-primary/10 text-primary group transition-colors" href="<?= base_url('settings') ?>">
                    <span class="material-symbols-outlined text-[22px] font-medium fill-1 group-hover:scale-110 transition-transform">settings</span>
                    <p class="text-sm font-semibold">Settings</p>
                </a>
            </nav>
        </div>
        <div class="border-t border-slate-200 dark:border-slate-700 pt-4 px-2">
            <div class="flex items-center gap-3">
                <?php if (!empty($user['profile_photo'])): ?>
                    <img src="<?= base_url('assets/uploads/profiles/' . $user['profile_photo']) ?>" 
                         alt="Profile" 
                         class="size-9 rounded-full object-cover shadow-sm ring-2 ring-white dark:ring-slate-900"/>
                <?php else: ?>
                    <div class="size-9 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold text-xs shadow-sm ring-2 ring-white dark:ring-slate-900">
                        <?= strtoupper(substr($user['full_name'] ?? 'U', 0, 2)) ?>
                    </div>
                <?php endif; ?>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-slate-900 dark:text-white truncate"><?= esc($user['full_name'] ?? 'User') ?></p>
                    <p class="text-xs text-slate-500 dark:text-slate-400 truncate"><?= esc(ucfirst($user['role'] ?? 'User')) ?></p>
                </div>
                <a href="<?= base_url('auth/logout') ?>" class="text-slate-400 hover:text-slate-600 dark:hover:text-white p-1 rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                    <span class="material-symbols-outlined text-xl">logout</span>
                </a>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto h-full p-4 md:p-8 bg-background-light dark:bg-background-dark">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white mb-2">Settings</h1>
            <p class="text-sm text-slate-600 dark:text-slate-400">Manage your account settings and preferences</p>
        </div>

        <!-- Success/Error Messages -->
        <?php if (session()->getFlashdata('success')): ?>
            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-200 px-4 py-3 rounded-lg mb-6 flex items-center gap-2">
                <span class="material-symbols-outlined text-xl">check_circle</span>
                <span><?= session()->getFlashdata('success') ?></span>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-200 px-4 py-3 rounded-lg mb-6 flex items-center gap-2">
                <span class="material-symbols-outlined text-xl">error</span>
                <span><?= session()->getFlashdata('error') ?></span>
            </div>
        <?php endif; ?>

        <!-- Settings Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Profile Photo Section -->
            <div class="bg-card-light dark:bg-card-dark rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-lg font-semibold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">account_circle</span>
                    Profile Photo
                </h2>
                
                <div class="flex flex-col items-center gap-6">
                    <!-- Photo Preview -->
                    <div class="flex-shrink-0">
                        <div id="photoPreview" class="relative group">
                            <?php if (!empty($user['profile_photo'])): ?>
                                <img id="previewImage" 
                                     src="<?= base_url('assets/uploads/profiles/' . $user['profile_photo']) ?>" 
                                     alt="" 
                                     class="w-32 h-32 rounded-full object-cover shadow-lg ring-4 ring-primary/20"
                                     onerror="this.style.display='none'; document.getElementById('previewInitials').style.display='flex';"/>
                                <div id="previewInitials" class="w-32 h-32 rounded-full bg-primary/10 text-primary hidden items-center justify-center font-bold text-4xl shadow-lg ring-4 ring-primary/20">
                                    <?= strtoupper(substr($user['full_name'] ?? 'U', 0, 2)) ?>
                                </div>
                                <!-- Remove Photo Button -->
                                <button type="button" id="removePhotoBtn" 
                                        class="absolute -bottom-2 -right-2 bg-red-500 hover:bg-red-600 text-white rounded-full p-2 shadow-lg transition-all duration-200 hover:scale-110"
                                        title="Remove photo">
                                    <span class="material-symbols-outlined text-lg">delete</span>
                                </button>
                            <?php else: ?>
                                <div id="previewInitials" class="w-32 h-32 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold text-4xl shadow-lg ring-4 ring-primary/20">
                                    <?= strtoupper(substr($user['full_name'] ?? 'U', 0, 2)) ?>
                                </div>
                                <img id="previewImage" 
                                     src="" 
                                     alt="" 
                                     class="w-32 h-32 rounded-full object-cover shadow-lg ring-4 ring-primary/20 hidden"/>
                                <!-- Remove Photo Button (hidden initially) -->
                                <button type="button" id="removePhotoBtn" 
                                        class="hidden absolute -bottom-2 -right-2 bg-red-500 hover:bg-red-600 text-white rounded-full p-2 shadow-lg transition-all duration-200 hover:scale-110"
                                        title="Remove photo">
                                    <span class="material-symbols-outlined text-lg">delete</span>
                                </button>
                            <?php endif; ?>
                            <div id="uploadProgressOverlay" class="hidden absolute inset-0 bg-black/60 rounded-full flex flex-col items-center justify-center">
                                <div class="text-white text-2xl font-bold mb-1" id="progressPercent">0%</div>
                                <div class="text-white text-xs font-medium">Uploading...</div>
                            </div>
                        </div>
                    </div>

                    <!-- Upload Area -->
                    <div class="w-full">
                        <div id="dropZone" 
                             class="border-2 border-dashed border-slate-300 dark:border-slate-600 rounded-lg p-6 text-center cursor-pointer hover:border-primary hover:bg-primary/5 transition-all duration-200">
                            <span class="material-symbols-outlined text-5xl text-slate-400 dark:text-slate-500 mb-2 block">cloud_upload</span>
                            <p class="text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                                Drop your photo here or click to browse
                            </p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">
                                PNG, JPG, GIF up to 2MB
                            </p>
                            <input type="file" 
                                   id="photoInput" 
                                   name="profile_photo" 
                                   accept="image/*" 
                                   class="hidden"/>
                        </div>
                        
                        <!-- Selected File Info and Upload Button -->
                        <div id="selectedFileInfo" class="hidden mt-4">
                            <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700">
                                <div class="flex items-center gap-3 flex-1 min-w-0">
                                    <span class="material-symbols-outlined text-primary">image</span>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-slate-900 dark:text-white truncate" id="fileName">photo.jpg</p>
                                        <p class="text-xs text-slate-500 dark:text-slate-400" id="fileSize">1.2 MB</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 ml-3">
                                    <button type="button" id="uploadBtn" 
                                            class="px-4 py-2 bg-primary hover:bg-primary-dark text-white text-sm font-medium rounded-lg transition-colors duration-200 flex items-center gap-2">
                                        <span class="material-symbols-outlined text-lg">upload</span>
                                        Upload
                                    </button>
                                    <button type="button" id="cancelBtn" 
                                            class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg transition-colors">
                                        <span class="material-symbols-outlined text-xl">close</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div id="uploadMessage" class="mt-3 text-sm"></div>
                    </div>
                </div>
            </div>

            <!-- Profile Information Section -->
            <div class="bg-card-light dark:bg-card-dark rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-lg font-semibold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">person</span>
                    Profile Information
                </h2>
            
                <form action="<?= base_url('settings/updateProfile') ?>" method="post">
                    <?= csrf_field() ?>
                    
                    <div class="space-y-4">
                        <div>
                            <label for="staff_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                                Staff ID
                            </label>
                            <input type="text" 
                                   id="staff_id" 
                                   value="<?= esc($user['staff_id'] ?? 'N/A') ?>" 
                                   disabled
                                   class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-900 dark:text-white cursor-not-allowed"/>
                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Staff ID cannot be changed</p>
                        </div>

                        <div>
                            <label for="username" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                                Username
                            </label>
                            <input type="text" 
                                   id="username" 
                                   value="<?= esc($user['username']) ?>" 
                                   disabled
                                   class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-900 dark:text-white cursor-not-allowed"/>
                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Username cannot be changed</p>
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                                Email Address <span class="text-red-500">*</span>
                            </label>
                            <input type="email" 
                                   id="email" 
                                   name="email"
                                   value="<?= esc(old('email', $user['email'])) ?>" 
                                   required
                                   class="w-full px-4 py-2.5 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary transition-all"/>
                            <?php if (session()->getFlashdata('errors')['email'] ?? null): ?>
                                <p class="mt-1 text-xs text-red-600 dark:text-red-400">
                                    <?= session()->getFlashdata('errors')['email'] ?>
                                </p>
                            <?php endif; ?>
                        </div>

                        <div>
                            <label for="full_name" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                                Full Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="full_name" 
                                   name="full_name" 
                                   value="<?= esc(old('full_name', $user['full_name'])) ?>"
                                   required
                                   class="w-full px-4 py-2.5 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary transition-all"/>
                            <?php if (session()->getFlashdata('errors')['full_name'] ?? null): ?>
                                <p class="mt-1 text-xs text-red-600 dark:text-red-400">
                                    <?= session()->getFlashdata('errors')['full_name'] ?>
                                </p>
                            <?php endif; ?>
                        </div>

                        <div>
                            <label for="contact_no" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                                Contact Number <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="contact_no" 
                                   name="contact_no" 
                                   value="<?= esc(old('contact_no', $user['contact_no'])) ?>"
                                   required
                                   class="w-full px-4 py-2.5 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary transition-all"/>
                            <?php if (session()->getFlashdata('errors')['contact_no'] ?? null): ?>
                                <p class="mt-1 text-xs text-red-600 dark:text-red-400">
                                    <?= session()->getFlashdata('errors')['contact_no'] ?>
                                </p>
                            <?php endif; ?>
                        </div>

                        <div class="flex justify-end pt-2">
                            <button type="submit" 
                                    class="px-6 py-2.5 bg-primary hover:bg-primary-dark text-white font-medium rounded-lg transition-colors duration-200 flex items-center gap-2">
                                <span class="material-symbols-outlined text-xl">save</span>
                                Save Changes
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Change Password Section - Full Width -->
        <div class="bg-card-light dark:bg-card-dark rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mt-6">
            <h2 class="text-lg font-semibold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">lock</span>
                Change Password
            </h2>

            <?php if (session()->getFlashdata('password_error')): ?>
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-200 px-4 py-3 rounded-lg mb-4 flex items-center gap-2">
                    <span class="material-symbols-outlined text-xl">error</span>
                    <span><?= session()->getFlashdata('password_error') ?></span>
                </div>
            <?php endif; ?>
            
            <form action="<?= base_url('settings/updatePassword') ?>" method="post">
                <?= csrf_field() ?>
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                    <div>
                        <label for="current_password" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            Current Password <span class="text-red-500">*</span>
                        </label>
                        <input type="password" 
                               id="current_password" 
                               name="current_password"
                               required
                               class="w-full px-4 py-2.5 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary transition-all"/>
                        <?php if (session()->getFlashdata('password_errors')['current_password'] ?? null): ?>
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">
                                <?= session()->getFlashdata('password_errors')['current_password'] ?>
                            </p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label for="new_password" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            New Password <span class="text-red-500">*</span>
                        </label>
                        <input type="password" 
                               id="new_password" 
                               name="new_password"
                               required
                               class="w-full px-4 py-2.5 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary transition-all"/>
                        <?php if (session()->getFlashdata('password_errors')['new_password'] ?? null): ?>
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">
                                <?= session()->getFlashdata('password_errors')['new_password'] ?>
                            </p>
                        <?php endif; ?>
                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Minimum 6 characters</p>
                    </div>

                    <div>
                        <label for="confirm_password" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            Confirm New Password <span class="text-red-500">*</span>
                        </label>
                        <input type="password" 
                               id="confirm_password" 
                               name="confirm_password"
                               required
                               class="w-full px-4 py-2.5 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary transition-all"/>
                        <?php if (session()->getFlashdata('password_errors')['confirm_password'] ?? null): ?>
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">
                                <?= session()->getFlashdata('password_errors')['confirm_password'] ?>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="flex justify-end pt-4 mt-2 border-t border-slate-200 dark:border-slate-700">
                    <button type="submit" 
                            class="px-6 py-2.5 bg-primary hover:bg-primary-dark text-white font-medium rounded-lg transition-colors duration-200 flex items-center gap-2">
                        <span class="material-symbols-outlined text-xl">lock_reset</span>
                        Change Password
                    </button>
                </div>
            </form>
        </div>
    </main>

    <!-- Confirmation Modal -->
    <div id="confirmModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-2xl max-w-md w-full transform transition-all">
            <div class="p-6">
                <div class="flex items-center gap-4 mb-4">
                    <div class="flex-shrink-0 w-12 h-12 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                        <span class="material-symbols-outlined text-red-600 dark:text-red-400 text-2xl">warning</span>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white" id="modalTitle">Remove Photo</h3>
                        <p class="text-sm text-slate-600 dark:text-slate-400 mt-1" id="modalMessage">
                            This will remove your profile photo and show your initials instead. Continue?
                        </p>
                    </div>
                </div>
                <div class="flex gap-3 justify-end">
                    <button type="button" id="modalCancelBtn"
                            class="px-4 py-2.5 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 font-medium rounded-lg transition-colors duration-200">
                        Cancel
                    </button>
                    <button type="button" id="modalConfirmBtn"
                            class="px-4 py-2.5 bg-red-500 hover:bg-red-600 text-white font-medium rounded-lg transition-colors duration-200 flex items-center gap-2">
                        <span class="material-symbols-outlined text-lg">delete</span>
                        Remove Photo
                    </button>
                </div>