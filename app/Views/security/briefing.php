<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title><?= esc($pageTitle) ?></title>
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#137fec",
                        "primary-hover": "#0f6ac6",
                        "background-light": "#f6f7f8",
                        "background-dark": "#101922",
                        "surface-light": "#ffffff",
                        "surface-dark": "#1a2632",
                        "text-main": "#0d141b",
                        "text-sub": "#4c739a",
                        "border-color": "#e7edf3",
                    },
                    fontFamily: {
                        "sans": ["Montserrat", "sans-serif"],
                        "display": ["Montserrat", "sans-serif"],
                        "brand": ["Montserrat", "sans-serif"],
                    },
                    borderRadius: {"DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px"},
                },
            },
        }
    </script>
    <style>
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: #cfdbe7;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #4c739a;
        }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark text-text-main dark:text-white font-brand antialiased min-h-screen flex flex-col">
    <!-- Header -->
    <header class="sticky top-0 z-50 w-full bg-surface-light/95 dark:bg-surface-dark/95 backdrop-blur border-b border-border-color dark:border-gray-800">
        <div class="max-w-[960px] mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-gradient-to-br from-primary to-blue-600 rounded-lg flex items-center justify-center">
                    <span class="material-symbols-outlined text-white text-xl">shield_person</span>
                </div>
                <span class="text-xl font-bold text-text-main dark:text-white">SafeG</span>
            </div>
            <div class="hidden sm:flex items-center gap-4 text-sm font-medium text-text-sub dark:text-gray-400">
                <span class="flex items-center gap-1" id="currentTime">
                    <span class="material-symbols-outlined text-[18px]">schedule</span>
                    <span>--:-- --</span>
                </span>
                <button type="button" onclick="showHelp()" class="flex items-center gap-1 hover:text-primary transition-colors cursor-pointer">
                    <span class="material-symbols-outlined text-[18px]">help</span> Help
                </button>
                <button type="button" onclick="showLanguageMenu()" class="flex items-center gap-1 hover:text-primary transition-colors cursor-pointer">
                    <span class="material-symbols-outlined text-[18px]">language</span> <span id="currentLang">English</span>
                </button>
            </div>
        </div>
    </header>

    <!-- Help Modal -->
    <div id="helpModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-md w-full" style="animation: scaleIn 0.2s ease-out;">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                        <span class="material-symbols-outlined text-2xl text-primary">help</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Need Help?</h3>
                </div>
            </div>
            <div class="p-6">
                <div class="space-y-4 text-gray-700 dark:text-gray-300">
                    <p class="font-semibold">Security Briefing Assistance:</p>
                    <ul class="space-y-2 text-sm">
                        <li class="flex items-start gap-2">
                            <span class="material-symbols-outlined text-lg text-primary mt-0.5">check_circle</span>
                            <span>Watch the entire video to proceed</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="material-symbols-outlined text-lg text-primary mt-0.5">check_circle</span>
                            <span>Video cannot be skipped or fast-forwarded</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="material-symbols-outlined text-lg text-primary mt-0.5">check_circle</span>
                            <span>Acknowledge after viewing to continue</span>
                        </li>
                    </ul>
                    <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                        <p class="font-semibold mb-2">Technical Support:</p>
                        <p class="text-sm">📞 +60 3-XXXX XXXX</p>
                        <p class="text-sm">✉️ support@safeg.com</p>
                    </div>
                </div>
            </div>
            <div class="p-6 border-t border-gray-200 dark:border-gray-700">
                <button onclick="closeHelpModal()" class="w-full px-4 py-3 bg-primary hover:bg-primary-hover text-white font-semibold rounded-xl transition-colors">
                    Got it, thanks!
                </button>
            </div>
        </div>
    </div>

    <!-- Language Modal -->
    <div id="languageModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-md w-full" style="animation: scaleIn 0.2s ease-out;">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                        <span class="material-symbols-outlined text-2xl text-primary">language</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Select Language</h3>
                </div>
            </div>
            <div class="p-6 max-h-96 overflow-y-auto">
                <div class="space-y-2">
                    <button onclick="changeLanguage('en')" class="language-option w-full px-4 py-3 text-left rounded-xl hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors flex items-center justify-between" data-lang="en">
                        <span class="font-medium">🇬🇧 English</span>
                        <span class="material-symbols-outlined text-primary hidden">check_circle</span>
                    </button>
                    <button onclick="changeLanguage('ms')" class="language-option w-full px-4 py-3 text-left rounded-xl hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors flex items-center justify-between" data-lang="ms">
                        <span class="font-medium">🇲🇾 Bahasa Malaysia</span>
                        <span class="material-symbols-outlined text-primary hidden">check_circle</span>
                    </button>
                    <button onclick="changeLanguage('zh-CN')" class="language-option w-full px-4 py-3 text-left rounded-xl hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors flex items-center justify-between" data-lang="zh-CN">
                        <span class="font-medium">🇨🇳 中文 (简体)</span>
                        <span class="material-symbols-outlined text-primary hidden">check_circle</span>
                    </button>
                    <button onclick="changeLanguage('zh-TW')" class="language-option w-full px-4 py-3 text-left rounded-xl hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors flex items-center justify-between" data-lang="zh-TW">
                        <span class="font-medium">🇹🇼 繁體中文</span>
                        <span class="material-symbols-outlined text-primary hidden">check_circle</span>
                    </button>
                    <button onclick="changeLanguage('ta')" class="language-option w-full px-4 py-3 text-left rounded-xl hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors flex items-center justify-between" data-lang="ta">
                        <span class="font-medium">🇮🇳 தமிழ் (Tamil)</span>
                        <span class="material-symbols-outlined text-primary hidden">check_circle</span>
                    </button>
                    <button onclick="changeLanguage('hi')" class="language-option w-full px-4 py-3 text-left rounded-xl hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors flex items-center justify-between" data-lang="hi">
                        <span class="font-medium">🇮🇳 हिन्दी (Hindi)</span>
                        <span class="material-symbols-outlined text-primary hidden">check_circle</span>
                    </button>
                    <button onclick="changeLanguage('ja')" class="language-option w-full px-4 py-3 text-left rounded-xl hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors flex items-center justify-between" data-lang="ja">
                        <span class="font-medium">🇯🇵 日本語 (Japanese)</span>
                        <span class="material-symbols-outlined text-primary hidden">check_circle</span>
                    </button>
                    <button onclick="changeLanguage('ko')" class="language-option w-full px-4 py-3 text-left rounded-xl hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors flex items-center justify-between" data-lang="ko">
                        <span class="font-medium">🇰🇷 한국어 (Korean)</span>
                        <span class="material-symbols-outlined text-primary hidden">check_circle</span>
                    </button>
                    <button onclick="changeLanguage('th')" class="language-option w-full px-4 py-3 text-left rounded-xl hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors flex items-center justify-between" data-lang="th">
                        <span class="font-medium">🇹🇭 ภาษาไทย (Thai)</span>
                        <span class="material-symbols-outlined text-primary hidden">check_circle</span>
                    </button>
                    <button onclick="changeLanguage('vi')" class="language-option w-full px-4 py-3 text-left rounded-xl hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors flex items-center justify-between" data-lang="vi">
                        <span class="font-medium">🇻🇳 Tiếng Việt (Vietnamese)</span>
                        <span class="material-symbols-outlined text-primary hidden">check_circle</span>
                    </button>
                    <button onclick="changeLanguage('id')" class="language-option w-full px-4 py-3 text-left rounded-xl hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors flex items-center justify-between" data-lang="id">
                        <span class="font-medium">🇮🇩 Bahasa Indonesia</span>
                        <span class="material-symbols-outlined text-primary hidden">check_circle</span>
                    </button>
                </div>
            </div>
            <div class="p-6 border-t border-gray-200 dark:border-gray-700">
                <button onclick="closeLanguageModal()" class="w-full px-4 py-3 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-white font-semibold rounded-xl transition-colors">
                    Close
                </button>
            </div>
        </div>
    </div>

    <main class="flex-1 w-full max-w-[960px] mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Progress Bar -->
        <div class="mb-8">
            <div class="flex justify-between items-end mb-2">
                <span class="text-sm font-semibold text-primary font-brand uppercase tracking-wider">Step 2 of 3</span>
                <span class="text-xs text-text-sub dark:text-gray-400">Security Briefing</span>
            </div>
            <div class="h-2 w-full bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                <div class="h-full bg-primary rounded-full shadow-[0_0_10px_rgba(19,127,236,0.5)]" style="width: 66.67%;"></div>
            </div>
        </div>

        <!-- Page Header -->
        <div class="mb-8 space-y-2">
            <h1 class="text-3xl sm:text-4xl font-black text-text-main dark:text-white font-brand tracking-tight" data-translate="Safety & Security Briefing">Safety & Security Briefing</h1>
            <p class="text-text-sub dark:text-gray-400 text-lg max-w-2xl font-brand" data-translate="Please watch the following video to ensure your safety while visiting our facility.">
                Please watch the following video to ensure your safety while visiting our facility.
            </p>
        </div>

        <!-- Skip Warning Alert -->
        <div id="skipWarning" class="hidden fixed top-20 right-4 z-50 max-w-md">
            <div class="bg-red-500 text-white px-6 py-4 rounded-xl shadow-2xl border-l-4 border-red-700 flex items-center gap-3 animate-slide-in">
                <span class="material-symbols-outlined text-2xl">warning</span>
                <span class="font-semibold">You cannot skip the video. Please watch from start to finish.</span>
            </div>
        </div>

        <!-- Video Section -->
        <section class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-sm border border-border-color dark:border-gray-800 overflow-hidden mb-8">
            <div class="p-6 sm:p-8">
                <?php if ($video_available): ?>
                    <div class="rounded-xl overflow-hidden bg-black shadow-lg">
                        <video id="briefingVideo" controls controlsList="nodownload" class="w-full h-auto">
                            <source src="<?= esc($briefing_video_url) ?>" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                    </div>
                <?php else: ?>
                    <div class="text-center py-16 px-6 bg-gray-50 dark:bg-gray-800 rounded-xl">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-200 dark:bg-gray-700 mb-4">
                            <span class="material-symbols-outlined text-4xl text-gray-400">videocam_off</span>
                        </div>
                        <h3 class="text-xl font-bold text-gray-700 dark:text-gray-300 mb-2">No Video Available</h3>
                        <p class="text-gray-500 dark:text-gray-400">Please contact the administrator.</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Progress Section -->
            <div class="px-6 sm:px-8 pb-6">
                <div class="flex items-center gap-3 mb-4 pb-4 border-b border-border-color dark:border-gray-800">
                    <div class="size-10 rounded-full bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-primary">
                        <span class="material-symbols-outlined">schedule</span>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center justify-between">
                            <h2 class="text-lg font-bold font-brand text-text-main dark:text-white" data-translate="Viewing Progress">Viewing Progress</h2>
                            <span class="text-2xl font-bold text-primary" id="progressPercentage">0%</span>
                        </div>
                    </div>
                </div>
                <div class="h-3 w-full bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                    <div id="progressBar" class="h-full bg-gradient-to-r from-primary to-blue-600 rounded-full transition-all duration-300" style="width: 0%;"></div>
                </div>
            </div>
        </section>

        <!-- Acknowledgment Section -->
        <section class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-sm border border-border-color dark:border-gray-800 p-6 sm:p-8 mb-8">
            <div class="flex items-center gap-3 mb-6 pb-4 border-b border-border-color dark:border-gray-800">
                <div class="size-10 rounded-full bg-green-50 dark:bg-green-900/20 flex items-center justify-center text-green-600 dark:text-green-400">
                    <span class="material-symbols-outlined">verified</span>
                </div>
                <div>
                    <h2 class="text-lg font-bold font-brand text-text-main dark:text-white" data-translate="Acknowledgment">Acknowledgment</h2>
                    <p class="text-sm text-text-sub dark:text-gray-400 font-brand" data-translate="Confirm your understanding">Confirm your understanding</p>
                </div>
            </div>

            <div id="checkboxWrapper" class="cursor-pointer p-5 bg-gray-50 dark:bg-gray-800/50 border-2 border-gray-200 dark:border-gray-700 rounded-xl hover:border-primary hover:bg-blue-50 dark:hover:bg-blue-900/10 transition-all duration-200">
                <label class="flex items-start gap-4 cursor-pointer">
                    <input type="checkbox" id="acknowledgment" disabled 
                           class="mt-1 h-5 w-5 rounded text-primary border-gray-300 dark:border-gray-600 focus:ring-primary focus:ring-2 cursor-pointer disabled:cursor-not-allowed disabled:opacity-50"/>
                    <span class="text-text-main dark:text-white font-medium leading-relaxed select-none" data-translate="I have watched the entire video and clearly understand the safety protocols and emergency procedures of this facility.">
                        I have watched the entire video and clearly understand the safety protocols and emergency procedures of this facility.
                    </span>
                </label>
            </div>

            <button type="button" id="submitButton" disabled
                    class="w-full mt-6 px-8 py-4 bg-primary hover:bg-primary-hover disabled:bg-gray-300 dark:disabled:bg-gray-700 disabled:cursor-not-allowed text-white font-bold rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl hover:-translate-y-0.5 disabled:hover:translate-y-0 disabled:hover:shadow-lg flex items-center justify-center gap-2 text-lg">
                <span id="buttonText" class="flex items-center gap-2">
                    <span class="material-symbols-outlined">check_circle</span>
                    <span data-translate="I Acknowledge & Proceed">I Acknowledge & Proceed</span>
                    <span class="material-symbols-outlined">arrow_forward</span>
                </span>
                <svg id="spinner" class="hidden animate-spin h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </button>

            <div class="mt-6 p-4 bg-amber-50 dark:bg-amber-900/20 border-l-4 border-amber-500 dark:border-amber-400 rounded-lg">
                <div class="flex gap-3">
                    <span class="material-symbols-outlined text-amber-600 dark:text-amber-400 flex-shrink-0">info</span>
                    <p class="text-sm text-amber-800 dark:text-amber-200 font-medium">
                        By clicking confirm, you agree to abide by all SafeG site regulations. Failure to comply may result in denied access.
                    </p>
                </div>
            </div>
        </section>
    </main>

    <style>
        @keyframes slide-in {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        .animate-slide-in {
            animation: slide-in 0.3s ease-out;
        }
        @keyframes scaleIn {
            from { transform: scale(0.9); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }
        @keyframes slideIn {
            from { transform: translateX(400px); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
    </style>

    <script>
        // Update time display
        function updateTime() {
            const now = new Date();
            const hours = now.getHours();
            const minutes = now.getMinutes();
            const ampm = hours >= 12 ? 'PM' : 'AM';
            const displayHours = hours % 12 || 12;
            const displayMinutes = minutes < 10 ? '0' + minutes : minutes;
            const timeElement = document.getElementById('currentTime');
            if (timeElement) {
                const timeSpan = timeElement.querySelector('span:last-child');
                if (timeSpan) {
                    timeSpan.textContent = `${displayHours}:${displayMinutes} ${ampm}`;
                }
            }
        }
        
        updateTime();
        setInterval(updateTime, 1000);

        const video = document.getElementById('briefingVideo');
        const progressBar = document.getElementById('progressBar');
        const progressPercentage = document.getElementById('progressPercentage');
        const acknowledgmentCheckbox = document.getElementById('acknowledgment');
        const checkboxWrapper = document.getElementById('checkboxWrapper');
        const submitButton = document.getElementById('submitButton');
        const skipWarning = document.getElementById('skipWarning');
        const buttonText = document.getElementById('buttonText');
        const spinner = document.getElementById('spinner');
        
        let maxWatchedTime = 0;
        let videoCompleted = false;
        let lastValidTime = 0;

        // Show warning when user tries to skip
        function showSkipWarning() {
            skipWarning.classList.remove('hidden');
            setTimeout(() => {
                skipWarning.classList.add('hidden');
            }, 3000);
        }

        // Only add video event listeners if video element exists
        if (video) {
            // Disable right-click on video to prevent download options
            video.addEventListener('contextmenu', function(e) {
                e.preventDefault();
            });

            // Track video progress - only allow sequential watching
            video.addEventListener('timeupdate', function() {
                const currentTime = video.currentTime;
                const duration = video.duration;
                
                // Check if user is trying to skip ahead
                if (currentTime > maxWatchedTime + 0.5) {
                    video.currentTime = maxWatchedTime;
                    showSkipWarning();
                    return;
                }
                
                // Update max watched time only if playing sequentially
                if (currentTime > maxWatchedTime) {
                    maxWatchedTime = currentTime;
                }
                
                lastValidTime = currentTime;
                
                // Calculate progress based on sequential watching
                const progress = (maxWatchedTime / duration) * 100;
                progressBar.style.width = progress + '%';
                progressPercentage.textContent = Math.round(progress) + '%';
                
                // Enable checkbox when video is 95% complete
                if (progress >= 95 && !videoCompleted) {
                    videoCompleted = true;
                    acknowledgmentCheckbox.disabled = false;
                    checkboxWrapper.classList.remove('cursor-not-allowed');
                    checkboxWrapper.classList.add('cursor-pointer');
                }
            });

            // Prevent seeking beyond watched point
            video.addEventListener('seeking', function() {
                if (video.currentTime > maxWatchedTime + 0.5) {
                    video.currentTime = maxWatchedTime;
                    showSkipWarning();
                }
            });

            // Prevent playback rate changes (2x speed, etc)
            video.addEventListener('ratechange', function() {
                if (video.playbackRate !== 1) {
                    video.playbackRate = 1;
                    showSkipWarning();
                }
            });
        }

        // Handle checkbox change
        acknowledgmentCheckbox.addEventListener('change', function() {
            if (this.checked) {
                checkboxWrapper.classList.add('border-primary', 'bg-blue-50', 'dark:bg-blue-900/10');
                checkboxWrapper.classList.remove('border-gray-200', 'dark:border-gray-700');
            } else {
                checkboxWrapper.classList.remove('border-primary', 'bg-blue-50', 'dark:bg-blue-900/10');
                checkboxWrapper.classList.add('border-gray-200', 'dark:border-gray-700');
            }
            submitButton.disabled = !this.checked;
        });

        // Handle form submission
        submitButton.addEventListener('click', function() {
            if (acknowledgmentCheckbox.checked && videoCompleted) {
                buttonText.classList.add('hidden');
                spinner.classList.remove('hidden');
                submitButton.disabled = true;
                
                // Send AJAX request
                fetch('<?= base_url('security/validateCompletion') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        token: '<?= esc($token) ?>',
                        watched_duration: maxWatchedTime,
                        video_duration: video ? video.duration : 0,
                        acknowledged: true
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = data.redirect_url;
                    } else {
                        alert(data.message);
                        buttonText.classList.remove('hidden');
                        spinner.classList.add('hidden');
                        submitButton.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred. Please try again.');
                    buttonText.classList.remove('hidden');
                    spinner.classList.add('hidden');
                    submitButton.disabled = false;
                });
            }
        });

        // Disable keyboard shortcuts that could skip video
        if (video) {
            document.addEventListener('keydown', function(e) {
                if (video && !video.paused) {
                    const isVideoFocused = document.activeElement === video;
                    
                    // Block arrow keys (left/right for seeking)
                    if (e.keyCode === 37 || e.keyCode === 39) {
                        e.preventDefault();
                        showSkipWarning();
                    }
                }
            });
        }

        // Help and Language functions
        function showHelp() {
            document.getElementById('helpModal').classList.remove('hidden');
        }

        function closeHelpModal() {
            document.getElementById('helpModal').classList.add('hidden');
        }

        function showLanguageMenu() {
            const currentLang = localStorage.getItem('selectedLanguage') || 'en';
            document.querySelectorAll('.language-option').forEach(btn => {
                const checkIcon = btn.querySelector('.material-symbols-outlined');
                if (btn.dataset.lang === currentLang) {
                    checkIcon.classList.remove('hidden');
                    btn.classList.add('bg-blue-50', 'dark:bg-blue-900/20');
                } else {
                    checkIcon.classList.add('hidden');
                    btn.classList.remove('bg-blue-50', 'dark:bg-blue-900/20');
                }
            });
            document.getElementById('languageModal').classList.remove('hidden');
        }

        function closeLanguageModal() {
            document.getElementById('languageModal').classList.add('hidden');
        }

        function changeLanguage(langCode) {
            localStorage.setItem('selectedLanguage', langCode);
            const langNames = {
                'en': 'English', 'ms': 'Bahasa Malaysia', 'zh-CN': '中文', 'zh-TW': '繁體中文',
                'ta': 'தமிழ்', 'hi': 'हिन्दी', 'ja': '日本語', 'ko': '한국어',
                'th': 'ภาษาไทย', 'vi': 'Tiếng Việt', 'id': 'Bahasa Indonesia'
            };
            document.getElementById('currentLang').textContent = langNames[langCode];
            translatePage(langCode);
            closeLanguageModal();
            const notification = document.createElement('div');
            notification.className = 'fixed top-20 right-4 bg-green-500 text-white px-6 py-3 rounded-xl shadow-lg z-50';
            notification.style.animation = 'slideIn 0.3s ease-out';
            notification.textContent = `Language: ${langNames[langCode]}`;
            document.body.appendChild(notification);
            setTimeout(() => notification.remove(), 3000);
        }

        function translatePage(lang) {
            const originalTexts = {
                'Safety & Security Briefing': 'Safety & Security Briefing',
                'Please watch the following video to ensure your safety while visiting our facility.': 'Please watch the following video to ensure your safety while visiting our facility.',
                'Viewing Progress': 'Viewing Progress',
                'Acknowledgment': 'Acknowledgment',
                'Confirm your understanding': 'Confirm your understanding',
                'I have watched the entire video and clearly understand the safety protocols and emergency procedures of this facility.': 'I have watched the entire video and clearly understand the safety protocols and emergency procedures of this facility.',
                'I Acknowledge & Proceed': 'I Acknowledge & Proceed'
            };

            const translations = {
                'ms': {
                    'Safety & Security Briefing': 'Taklimat Keselamatan & Keamanan',
                    'Please watch the following video to ensure your safety while visiting our facility.': 'Sila tonton video berikut untuk memastikan keselamatan anda semasa melawat kemudahan kami.',
                    'Viewing Progress': 'Kemajuan Tontonan',
                    'Acknowledgment': 'Pengakuan',
                    'Confirm your understanding': 'Sahkan pemahaman anda',
                    'I have watched the entire video and clearly understand the safety protocols and emergency procedures of this facility.': 'Saya telah menonton keseluruhan video dan memahami dengan jelas protokol keselamatan dan prosedur kecemasan kemudahan ini.',
                    'I Acknowledge & Proceed': 'Saya Akui & Teruskan'
                },
                'zh-CN': {
                    'Safety & Security Briefing': '安全与保安简报',
                    'Please watch the following video to ensure your safety while visiting our facility.': '请观看以下视频以确保您在访问我们设施时的安全。',
                    'Viewing Progress': '观看进度',
                    'Acknowledgment': '确认',
                    'Confirm your understanding': '确认您的理解',
                    'I have watched the entire video and clearly understand the safety protocols and emergency procedures of this facility.': '我已观看完整视频并清楚了解本设施的安全协议和紧急程序。',
                    'I Acknowledge & Proceed': '我确认并继续'
                },
                'zh-TW': {
                    'Safety & Security Briefing': '安全與保安簡報',
                    'Please watch the following video to ensure your safety while visiting our facility.': '請觀看以下影片以確保您在訪問我們設施時的安全。',
                    'Viewing Progress': '觀看進度',
                    'Acknowledgment': '確認',
                    'Confirm your understanding': '確認您的理解',
                    'I have watched the entire video and clearly understand the safety protocols and emergency procedures of this facility.': '我已觀看完整影片並清楚了解本設施的安全協議和緊急程序。',
                    'I Acknowledge & Proceed': '我確認並繼續'
                },
                'id': {
                    'Safety & Security Briefing': 'Pengarahan Keselamatan & Keamanan',
                    'Please watch the following video to ensure your safety while visiting our facility.': 'Harap tonton video berikut untuk memastikan keselamatan Anda saat mengunjungi fasilitas kami.',
                    'Viewing Progress': 'Kemajuan Menonton',
                    'Acknowledgment': 'Pengakuan',
                    'Confirm your understanding': 'Konfirmasi pemahaman Anda',
                    'I have watched the entire video and clearly understand the safety protocols and emergency procedures of this facility.': 'Saya telah menonton seluruh video dan memahami dengan jelas protokol keselamatan dan prosedur darurat fasilitas ini.',
                    'I Acknowledge & Proceed': 'Saya Mengakui & Lanjutkan'
                },
                'ta': {
                    'Safety & Security Briefing': 'பாதுகாப்பு மற்றும் பாதுகாப்பு விளக்கம்',
                    'Please watch the following video to ensure your safety while visiting our facility.': 'எங்கள் வசதியைப் பார்வையிடும் போது உங்கள் பாதுகாப்பை உறுதிப்படுத்த பின்வரும் வீடியோவைப் பார்க்கவும்.',
                    'Viewing Progress': 'பார்வை முன்னேற்றம்',
                    'Acknowledgment': 'ஒப்புதல்',
                    'Confirm your understanding': 'உங்கள் புரிதலை உறுதிப்படுத்தவும்',
                    'I have watched the entire video and clearly understand the safety protocols and emergency procedures of this facility.': 'நான் முழு வீடியோவையும் பார்த்துவிட்டேன் மற்றும் இந்த வசதியின் பாதுகாப்பு நெறிமுறைகள் மற்றும் அவசர நடைமுறைகளை தெளிவாக புரிந்துகொள்கிறேன்.',
                    'I Acknowledge & Proceed': 'நான் ஒப்புக்கொள்கிறேன் மற்றும் தொடரவும்'
                },
                'ja': {
                    'Safety & Security Briefing': '安全・セキュリティブリーフィング',
                    'Please watch the following video to ensure your safety while visiting our facility.': '施設訪問時の安全を確保するため、以下のビデオをご覧ください。',
                    'Viewing Progress': '視聴進捗',
                    'Acknowledgment': '確認',
                    'Confirm your understanding': '理解の確認',
                    'I have watched the entire video and clearly understand the safety protocols and emergency procedures of this facility.': 'ビデオ全体を視聴し、この施設の安全プロトコルと緊急手順を明確に理解しました。',
                    'I Acknowledge & Proceed': '確認して続行'
                },
                'ko': {
                    'Safety & Security Briefing': '안전 및 보안 브리핑',
                    'Please watch the following video to ensure your safety while visiting our facility.': '시설 방문 시 안전을 보장하기 위해 다음 비디오를 시청하십시오.',
                    'Viewing Progress': '시청 진행률',
                    'Acknowledgment': '확인',
                    'Confirm your understanding': '이해 확인',
                    'I have watched the entire video and clearly understand the safety protocols and emergency procedures of this facility.': '전체 비디오를 시청했으며 이 시설의 안전 프로토콜 및 비상 절차를 명확히 이해했습니다.',
                    'I Acknowledge & Proceed': '확인 및 계속'
                },
                'th': {
                    'Safety & Security Briefing': 'การบรรยายสรุปด้านความปลอดภัยและการรักษาความปลอดภัย',
                    'Please watch the following video to ensure your safety while visiting our facility.': 'โปรดดูวิดีโอต่อไปนี้เพื่อความปลอดภัยของคุณขณะเยี่ยมชมสถานที่ของเรา',
                    'Viewing Progress': 'ความคืบหน้าการรับชม',
                    'Acknowledgment': 'การรับทราบ',
                    'Confirm your understanding': 'ยืนยันความเข้าใจของคุณ',
                    'I have watched the entire video and clearly understand the safety protocols and emergency procedures of this facility.': 'ฉันได้ดูวิดีโอทั้งหมดและเข้าใจอย่างชัดเจนเกี่ยวกับโปรโตคอลความปลอดภัยและขั้นตอนฉุกเฉินของสถานที่แห่งนี้',
                    'I Acknowledge & Proceed': 'ฉันรับทราบและดำเนินการต่อ'
                },
                'vi': {
                    'Safety & Security Briefing': 'Hướng dẫn An toàn & Bảo mật',
                    'Please watch the following video to ensure your safety while visiting our facility.': 'Vui lòng xem video sau để đảm bảo an toàn của bạn khi đến thăm cơ sở của chúng tôi.',
                    'Viewing Progress': 'Tiến độ xem',
                    'Acknowledgment': 'Xác nhận',
                    'Confirm your understanding': 'Xác nhận sự hiểu biết của bạn',
                    'I have watched the entire video and clearly understand the safety protocols and emergency procedures of this facility.': 'Tôi đã xem toàn bộ video và hiểu rõ các giao thức an toàn và quy trình khẩn cấp của cơ sở này.',
                    'I Acknowledge & Proceed': 'Tôi xác nhận & Tiếp tục'
                },
                'hi': {
                    'Safety & Security Briefing': 'सुरक्षा और सुरक्षा ब्रीफिंग',
                    'Please watch the following video to ensure your safety while visiting our facility.': 'हमारी सुविधा का दौरा करते समय अपनी सुरक्षा सुनिश्चित करने के लिए कृपया निम्नलिखित वीडियो देखें।',
                    'Viewing Progress': 'देखने की प्रगति',
                    'Acknowledgment': 'स्वीकृति',
                    'Confirm your understanding': 'अपनी समझ की पुष्टि करें',
                    'I have watched the entire video and clearly understand the safety protocols and emergency procedures of this facility.': 'मैंने पूरा वीडियो देखा है और इस सुविधा की सुरक्षा प्रोटोकॉल और आपातकालीन प्रक्रियाओं को स्पष्ट रूप से समझता हूं।',
                    'I Acknowledge & Proceed': 'मैं स्वीकार करता हूं और आगे बढ़ता हूं'
                }
            };

            const trans = lang === 'en' ? originalTexts : translations[lang];
            if (!trans) return;

            document.querySelectorAll('[data-translate]').forEach(el => {
                const key = el.getAttribute('data-translate');
                if (trans[key]) {
                    if (el.tagName === 'INPUT' || el.tagName === 'TEXTAREA') {
                        el.placeholder = trans[key];
                    } else {
                        el.textContent = trans[key];
                    }
                }
            });
        }

        window.addEventListener('DOMContentLoaded', function() {
            const savedLang = localStorage.getItem('selectedLanguage');
            if (savedLang && savedLang !== 'en') changeLanguage(savedLang);
        });

        document.getElementById('helpModal')?.addEventListener('click', function(e) {
            if (e.target === this) closeHelpModal();
        });
        document.getElementById('languageModal')?.addEventListener('click', function(e) {
            if (e.target === this) closeLanguageModal();
        });
    </script>
</body>
</html>