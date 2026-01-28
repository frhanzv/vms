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
    <script defer src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
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
        
        @keyframes pulse-ring {
            0%, 100% { 
                opacity: 1;
                transform: scale(1);
            }
            50% { 
                opacity: 0.7;
                transform: scale(1.05);
            }
        }
        
        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }
        
        .face-detection-box {
            position: absolute;
            border: 3px solid #10b981;
            border-radius: 12px;
            z-index: 5;
            box-shadow: 0 0 30px rgba(16, 185, 129, 0.6);
            animation: pulse-ring 2s ease-in-out infinite;
        }
        @keyframes scaleIn {
            from { transform: scale(0.9); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
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
                    <p class="font-semibold">Facial Verification Tips:</p>
                    <ul class="space-y-2 text-sm">
                        <li class="flex items-start gap-2"><span class="material-symbols-outlined text-lg text-primary mt-0.5">check_circle</span><span>Ensure good lighting on your face</span></li>
                        <li class="flex items-start gap-2"><span class="material-symbols-outlined text-lg text-primary mt-0.5">check_circle</span><span>Look directly at the camera</span></li>
                        <li class="flex items-start gap-2"><span class="material-symbols-outlined text-lg text-primary mt-0.5">check_circle</span><span>Remove sunglasses</span></li>
                        <li class="flex items-start gap-2"><span class="material-symbols-outlined text-lg text-primary mt-0.5">check_circle</span><span>Hold still during countdown</span></li>
                    </ul>
                </div>
            </div>
            <div class="p-6 border-t border-gray-200 dark:border-gray-700">
                <button onclick="closeHelpModal()" class="w-full px-4 py-3 bg-primary hover:bg-primary-hover text-white font-semibold rounded-xl transition-colors">Got it!</button>
            </div>
        </div>
    </div>

    <!-- Language Modal -->
    <div id="languageModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-md w-full" style="animation: scaleIn 0.2s ease-out;">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700"><div class="flex items-center gap-3"><div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center"><span class="material-symbols-outlined text-2xl text-primary">language</span></div><h3 class="text-xl font-bold text-gray-900 dark:text-white">Select Language</h3></div></div>
            <div class="p-6 max-h-96 overflow-y-auto"><div class="space-y-2">
                <button onclick="changeLanguage('en')" class="language-option w-full px-4 py-3 text-left rounded-xl hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors flex items-center justify-between" data-lang="en"><span class="font-medium">🇬🇧 English</span><span class="material-symbols-outlined text-primary hidden">check_circle</span></button>
                <button onclick="changeLanguage('ms')" class="language-option w-full px-4 py-3 text-left rounded-xl hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors flex items-center justify-between" data-lang="ms"><span class="font-medium">🇲🇾 Bahasa Malaysia</span><span class="material-symbols-outlined text-primary hidden">check_circle</span></button>
                <button onclick="changeLanguage('zh-CN')" class="language-option w-full px-4 py-3 text-left rounded-xl hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors flex items-center justify-between" data-lang="zh-CN"><span class="font-medium">🇨🇳 中文 (简体)</span><span class="material-symbols-outlined text-primary hidden">check_circle</span></button>
                <button onclick="changeLanguage('zh-TW')" class="language-option w-full px-4 py-3 text-left rounded-xl hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors flex items-center justify-between" data-lang="zh-TW"><span class="font-medium">🇹🇼 繁體中文</span><span class="material-symbols-outlined text-primary hidden">check_circle</span></button>
                <button onclick="changeLanguage('ta')" class="language-option w-full px-4 py-3 text-left rounded-xl hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors flex items-center justify-between" data-lang="ta"><span class="font-medium">🇮🇳 தமிழ்</span><span class="material-symbols-outlined text-primary hidden">check_circle</span></button>
                <button onclick="changeLanguage('hi')" class="language-option w-full px-4 py-3 text-left rounded-xl hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors flex items-center justify-between" data-lang="hi"><span class="font-medium">🇮🇳 हिन्दी</span><span class="material-symbols-outlined text-primary hidden">check_circle</span></button>
                <button onclick="changeLanguage('ja')" class="language-option w-full px-4 py-3 text-left rounded-xl hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors flex items-center justify-between" data-lang="ja"><span class="font-medium">🇯🇵 日本語</span><span class="material-symbols-outlined text-primary hidden">check_circle</span></button>
                <button onclick="changeLanguage('ko')" class="language-option w-full px-4 py-3 text-left rounded-xl hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors flex items-center justify-between" data-lang="ko"><span class="font-medium">🇰🇷 한국어</span><span class="material-symbols-outlined text-primary hidden">check_circle</span></button>
                <button onclick="changeLanguage('th')" class="language-option w-full px-4 py-3 text-left rounded-xl hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors flex items-center justify-between" data-lang="th"><span class="font-medium">🇹🇭 ภาษาไทย</span><span class="material-symbols-outlined text-primary hidden">check_circle</span></button>
                <button onclick="changeLanguage('vi')" class="language-option w-full px-4 py-3 text-left rounded-xl hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors flex items-center justify-between" data-lang="vi"><span class="font-medium">🇻🇳 Tiếng Việt</span><span class="material-symbols-outlined text-primary hidden">check_circle</span></button>
                <button onclick="changeLanguage('id')" class="language-option w-full px-4 py-3 text-left rounded-xl hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors flex items-center justify-between" data-lang="id"><span class="font-medium">🇮🇩 Bahasa Indonesia</span><span class="material-symbols-outlined text-primary hidden">check_circle</span></button>
            </div></div>
            <div class="p-6 border-t border-gray-200 dark:border-gray-700"><button onclick="closeLanguageModal()" class="w-full px-4 py-3 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-white font-semibold rounded-xl transition-colors">Close</button></div>
        </div>
    </div>

    <main class="flex-1 w-full max-w-[960px] mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Progress Bar -->
        <div class="mb-8">
            <div class="flex justify-between items-end mb-2">
                <span class="text-sm font-semibold text-primary font-brand uppercase tracking-wider">Step 3 of 3</span>
                <span class="text-xs text-text-sub dark:text-gray-400">Facial Verification</span>
            </div>
            <div class="h-2 w-full bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                <div class="h-full bg-primary rounded-full shadow-[0_0_10px_rgba(19,127,236,0.5)]" style="width: 100%;"></div>
            </div>
        </div>

        <!-- Page Header -->
        <div class="mb-8 space-y-2">
            <h1 class="text-3xl sm:text-4xl font-black text-text-main dark:text-white font-brand tracking-tight" data-translate="Facial Verification">Facial Verification</h1>
            <p class="text-text-sub dark:text-gray-400 text-lg max-w-2xl font-brand" data-translate="Please look directly at the camera and hold still for a moment.">
                Please look directly at the camera and hold still for a moment.
            </p>
        </div>

        <!-- Camera Section -->
        <section class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-sm border border-border-color dark:border-gray-800 overflow-hidden mb-8">
            <div class="p-6 sm:p-8">
                <!-- Camera Wrapper -->
                <div class="relative w-full aspect-[4/3] max-w-2xl mx-auto bg-gray-100 dark:bg-gray-800 rounded-xl overflow-hidden shadow-inner">
                    <!-- Live Camera Status -->
                    <div id="cameraStatus" class="hidden absolute top-4 left-1/2 transform -translate-x-1/2 z-20 bg-black/70 backdrop-blur-sm text-white px-4 py-2 rounded-full text-sm font-semibold flex items-center gap-2 shadow-lg">
                        <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                        LIVE CAMERA
                    </div>

                    <!-- Face Frame Overlay -->
                    <div class="absolute inset-0 z-10 pointer-events-none">
                        <svg class="absolute inset-0 w-full h-full" preserveAspectRatio="xMidYMid slice">
                            <defs>
                                <mask id="frameMask">
                                    <rect width="100%" height="100%" fill="white"/>
                                    <rect x="15%" y="12.5%" width="70%" height="75%" rx="24" ry="24" fill="black"/>
                                </mask>
                            </defs>
                            <rect width="100%" height="100%" fill="rgba(0,0,0,0.5)" mask="url(#frameMask)"/>
                            <rect x="15%" y="12.5%" width="70%" height="75%" rx="24" ry="24" fill="none" stroke="#3b82f6" stroke-width="4"/>
                        </svg>
                    </div>

                    <!-- Countdown Overlay -->
                    <div id="countdownOverlay" class="hidden absolute inset-0 z-30 flex items-start justify-center pt-20">
                        <div class="text-6xl font-black text-white text-center" style="text-shadow: 0 4px 30px rgba(0,0,0,0.9), 0 0 60px rgba(255,255,255,0.3);"></div>
                    </div>

                    <!-- Video Element -->
                    <video id="video" autoplay playsinline class="absolute inset-0 w-full h-full object-cover"></video>
                    
                    <!-- Captured Image -->
                    <img id="capturedImage" alt="Captured Face" class="hidden absolute inset-0 w-full h-full object-cover">
                    
                    <!-- Hidden Canvas -->
                    <canvas id="canvas" class="hidden"></canvas>

                    <!-- Camera Access Prompt -->
                    <div id="cameraPrompt" class="absolute inset-0 flex flex-col items-center justify-center text-center p-8 bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-900">
                        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-blue-100 dark:bg-blue-900/20 mb-6">
                            <span class="material-symbols-outlined text-5xl text-blue-600 dark:text-blue-400">photo_camera</span>
                        </div>
                        <h3 class="text-xl font-bold text-gray-700 dark:text-gray-300 mb-2" data-translate="Camera Access Required">Camera Access Required</h3>
                        <p class="text-gray-500 dark:text-gray-400 max-w-md" data-translate="Please allow camera access to continue with facial verification">Please allow camera access to continue with facial verification</p>
                    </div>
                </div>

                <!-- Progress Section -->
                <div id="progressSection" class="hidden mt-6">
                    <div class="flex items-center justify-center gap-3 mb-4">
                        <div class="w-6 h-6 border-3 border-primary border-t-transparent rounded-full animate-spin"></div>
                        <span id="progressText" class="text-lg font-semibold text-primary">Loading face detection...</span>
                    </div>
                </div>
            </div>

            <!-- Info Section -->
            <div class="px-6 sm:px-8 pb-6">
                <div class="p-4 bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-500 dark:border-blue-400 rounded-lg">
                    <div class="flex gap-3">
                        <span class="material-symbols-outlined text-blue-600 dark:text-blue-400 flex-shrink-0">shield</span>
                        <p class="text-sm text-blue-800 dark:text-blue-200 font-medium">
                            Biometric data is processed securely and not stored permanently. Your privacy is protected.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Action Buttons -->
        <div id="actionButtons" class="hidden flex flex-col sm:flex-row gap-4 justify-center">
            <button type="button" onclick="retakePhoto()" 
                    class="px-8 py-4 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 border-2 border-gray-300 dark:border-gray-600 text-text-main dark:text-white font-bold rounded-xl transition-all duration-200 shadow-sm hover:shadow-md flex items-center justify-center gap-2 text-lg">
                <span class="material-symbols-outlined">refresh</span>
                Retake Photo
            </button>
            <button type="button" onclick="proceedToNextStep()" 
                    class="px-8 py-4 bg-primary hover:bg-primary-hover text-white font-bold rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl hover:-translate-y-0.5 flex items-center justify-center gap-2 text-lg">
                <span class="material-symbols-outlined">check_circle</span>
                Continue
                <span class="material-symbols-outlined">arrow_forward</span>
            </button>
        </div>
    </main>

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

        // Camera and Face Detection
        const video = document.getElementById('video');
        const canvas = document.getElementById('canvas');
        const capturedImage = document.getElementById('capturedImage');
        const cameraPrompt = document.getElementById('cameraPrompt');
        const cameraStatus = document.getElementById('cameraStatus');
        const countdownOverlay = document.getElementById('countdownOverlay');
        const progressSection = document.getElementById('progressSection');
        const progressText = document.getElementById('progressText');
        const actionButtons = document.getElementById('actionButtons');
        
        let stream = null;
        let capturedPhotoData = null;
        let countdownTimer = null;
        let detectionInterval = null;
        let faceDetected = false;
        let modelsLoaded = false;

        // Load face-api.js models
        async function loadFaceDetectionModels() {
            try {
                progressText.textContent = 'Loading face detection models...';
                const MODEL_URL = 'https://cdn.jsdelivr.net/npm/@vladmandic/face-api/model/';
                
                await faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL);
                modelsLoaded = true;
                progressText.textContent = 'Face detection ready!';
                return true;
            } catch (error) {
                console.error('Error loading models:', error);
                progressText.textContent = 'Error loading face detection';
                return false;
            }
        }

        // Detect face in video stream
        async function detectFace() {
            if (!video.videoWidth || !modelsLoaded) return;

            try {
                const detections = await faceapi.detectAllFaces(
                    video, 
                    new faceapi.TinyFaceDetectorOptions({ inputSize: 224, scoreThreshold: 0.5 })
                );

                if (detections && detections.length > 0) {
                    // Face detected
                    if (!faceDetected) {
                        faceDetected = true;
                        progressText.textContent = 'Face detected! Hold still...';
                        
                        // Start countdown after stable detection
                        setTimeout(() => {
                            if (faceDetected) {
                                startCountdown();
                            }
                        }, 500);
                    }
                } else {
                    // No face detected
                    if (faceDetected) {
                        faceDetected = false;
                        progressText.textContent = 'Looking for face...';
                        
                        // Cancel countdown if running
                        if (countdownTimer) {
                            clearInterval(countdownTimer);
                            countdownTimer = null;
                            countdownOverlay.style.display = 'none';
                        }
                    }
                }
            } catch (error) {
                console.error('Detection error:', error);
            }
        }

        // Request camera access
        async function requestCameraAccess() {
            try {
                // Load models first
                progressSection.style.display = 'block';
                const loaded = await loadFaceDetectionModels();
                
                if (!loaded) {
                    throw new Error('Failed to load face detection models');
                }

                stream = await navigator.mediaDevices.getUserMedia({ 
                    video: { 
                        facingMode: 'user',
                        width: { ideal: 1280 },
                        height: { ideal: 720 }
                    } 
                });
                
                video.srcObject = stream;
                cameraPrompt.style.display = 'none';
                cameraStatus.style.display = 'flex';
                progressText.textContent = 'Looking for face...';
                
                // Wait for video to be ready
                video.onloadedmetadata = () => {
                    // Start face detection
                    detectionInterval = setInterval(detectFace, 300);
                };
                
            } catch (error) {
                console.error('Camera access error:', error);
                cameraPrompt.innerHTML = `
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                    <h3>Camera Access Denied</h3>
                    <p>Please allow camera access in your browser settings and refresh the page</p>
                `;
            }
        }

        // Countdown before capture
        function startCountdown() {
            if (countdownTimer) return; // Prevent multiple countdowns
            
            let count = 5;
            const countdownText = countdownOverlay.querySelector('div');
            countdownOverlay.style.display = 'flex';
            countdownOverlay.classList.remove('hidden');
            countdownText.textContent = count;
            
            countdownTimer = setInterval(() => {
                // Check if face is still detected
                if (!faceDetected) {
                    clearInterval(countdownTimer);
                    countdownTimer = null;
                    countdownOverlay.style.display = 'none';
                    countdownOverlay.classList.add('hidden');
                    return;
                }
                
                count--;
                if (count > 0) {
                    countdownText.textContent = count;
                } else {
                    clearInterval(countdownTimer);
                    countdownTimer = null;
                    countdownOverlay.style.display = 'none';
                    countdownOverlay.classList.add('hidden');
                    capturePhoto();
                }
            }, 1000);
        }

        // Capture photo from video stream
        function capturePhoto() {
            // Stop detection
            if (detectionInterval) {
                clearInterval(detectionInterval);
                detectionInterval = null;
            }
            
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            const context = canvas.getContext('2d');
            context.drawImage(video, 0, 0, canvas.width, canvas.height);
            
            // Get image data
            capturedPhotoData = canvas.toDataURL('image/jpeg', 0.9);
            
            // Display captured image
            capturedImage.src = capturedPhotoData;
            capturedImage.style.display = 'block';
            video.style.display = 'none';
            
            // Stop camera stream
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
            }
            
            // Update UI
            cameraStatus.style.display = 'none';
            progressText.textContent = 'Photo captured successfully!';
            actionButtons.style.display = 'flex';
        }

        // Retake photo
        function retakePhoto() {
            // Reset UI
            capturedImage.style.display = 'none';
            video.style.display = 'block';
            actionButtons.style.display = 'none';
            countdownOverlay.style.display = 'none';
            countdownOverlay.classList.add('hidden');
            faceDetected = false;
            
            // Clear any existing timers
            if (countdownTimer) {
                clearInterval(countdownTimer);
                countdownTimer = null;
            }
            if (detectionInterval) {
                clearInterval(detectionInterval);
                detectionInterval = null;
            }
            
            // Restart camera
            requestCameraAccess();
        }

        // Proceed to next step
        function proceedToNextStep() {
            if (!capturedPhotoData) {
                alert('No photo captured. Please try again.');
                return;
            }
            
            const token = new URLSearchParams(window.location.search).get('token');
            
            // TODO: Send captured photo to backend for verification
            // You can send capturedPhotoData (base64 image) to server
            
            // For now, just proceed
            alert('Facial verification completed! All steps are now complete.');
            window.location.href = '<?= base_url('security/completed?token=') ?>' + token;
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
            const langNames = {'en': 'English', 'ms': 'Bahasa Malaysia', 'zh-CN': '中文', 'zh-TW': '繁體中文', 'ta': 'தமிழ்', 'hi': 'हिन्दी', 'ja': '日本語', 'ko': '한국어', 'th': 'ภาษาไทย', 'vi': 'Tiếng Việt', 'id': 'Bahasa Indonesia'};
            document.getElementById('currentLang').textContent = langNames[langCode];
            translatePage(langCode);
            closeLanguageModal();
            const notification = document.createElement('div');
            notification.className = 'fixed top-20 right-4 bg-green-500 text-white px-6 py-3 rounded-xl shadow-lg z-50';
            notification.textContent = `Language: ${langNames[langCode]}`;
            document.body.appendChild(notification);
            setTimeout(() => notification.remove(), 3000);
        }

        function translatePage(lang) {
            const originalTexts = {
                'Facial Verification': 'Facial Verification',
                'Please look directly at the camera and hold still for a moment.': 'Please look directly at the camera and hold still for a moment.',
                'Camera Access Required': 'Camera Access Required',
                'Please allow camera access to continue with facial verification': 'Please allow camera access to continue with facial verification'
            };

            const translations = {
                'ms': {
                    'Facial Verification': 'Pengesahan Wajah',
                    'Please look directly at the camera and hold still for a moment.': 'Sila lihat terus ke kamera dan kekal diam seketika.',
                    'Camera Access Required': 'Akses Kamera Diperlukan',
                    'Please allow camera access to continue with facial verification': 'Sila benarkan akses kamera untuk meneruskan pengesahan wajah'
                },
                'zh-CN': {
                    'Facial Verification': '面部验证',
                    'Please look directly at the camera and hold still for a moment.': '请直视相机并保持静止片刻。',
                    'Camera Access Required': '需要相机访问',
                    'Please allow camera access to continue with facial verification': '请允许相机访问以继续面部验证'
                },
                'zh-TW': {
                    'Facial Verification': '面部驗證',
                    'Please look directly at the camera and hold still for a moment.': '請直視相機並保持靜止片刻。',
                    'Camera Access Required': '需要相機存取',
                    'Please allow camera access to continue with facial verification': '請允許相機存取以繼續面部驗證'
                },
                'id': {
                    'Facial Verification': 'Verifikasi Wajah',
                    'Please look directly at the camera and hold still for a moment.': 'Silakan lihat langsung ke kamera dan tetap diam sejenak.',
                    'Camera Access Required': 'Akses Kamera Diperlukan',
                    'Please allow camera access to continue with facial verification': 'Harap izinkan akses kamera untuk melanjutkan verifikasi wajah'
                },
                'ta': {
                    'Facial Verification': 'முக சரிபார்ப்பு',
                    'Please look directly at the camera and hold still for a moment.': 'கேமராவை நேரடியாகப் பார்த்து ஒரு கணம் அமைதியாக இருங்கள்.',
                    'Camera Access Required': 'கேமரா அணுகல் தேவை',
                    'Please allow camera access to continue with facial verification': 'முக சரிபார்ப்பைத் தொடர கேமரா அணுகலை அனுமதிக்கவும்'
                },
                'ja': {
                    'Facial Verification': '顔認証',
                    'Please look directly at the camera and hold still for a moment.': 'カメラを直視し、しばらく静止してください。',
                    'Camera Access Required': 'カメラアクセスが必要',
                    'Please allow camera access to continue with facial verification': '顔認証を続行するにはカメラへのアクセスを許可してください'
                },
                'ko': {
                    'Facial Verification': '얼굴 확인',
                    'Please look directly at the camera and hold still for a moment.': '카메라를 직접 보고 잠시 가만히 계세요.',
                    'Camera Access Required': '카메라 액세스 필요',
                    'Please allow camera access to continue with facial verification': '얼굴 확인을 계속하려면 카메라 액세스를 허용하세요'
                },
                'th': {
                    'Facial Verification': 'การยืนยันใบหน้า',
                    'Please look directly at the camera and hold still for a moment.': 'โปรดมองตรงไปที่กล้องและอยู่นิ่งสักครู่',
                    'Camera Access Required': 'ต้องการการเข้าถึงกล้อง',
                    'Please allow camera access to continue with facial verification': 'โปรดอนุญาตการเข้าถึงกล้องเพื่อดำเนินการยืนยันใบหน้าต่อ'
                },
                'vi': {
                    'Facial Verification': 'Xác minh khuôn mặt',
                    'Please look directly at the camera and hold still for a moment.': 'Vui lòng nhìn thẳng vào camera và giữ yên trong giây lát.',
                    'Camera Access Required': 'Cần quyền truy cập camera',
                    'Please allow camera access to continue with facial verification': 'Vui lòng cho phép truy cập camera để tiếp tục xác minh khuôn mặt'
                },
                'hi': {
                    'Facial Verification': 'चेहरे का सत्यापन',
                    'Please look directly at the camera and hold still for a moment.': 'कृपया कैमरे की ओर सीधे देखें और एक पल के लिए स्थिर रहें।',
                    'Camera Access Required': 'कैमरा एक्सेस आवश्यक',
                    'Please allow camera access to continue with facial verification': 'चेहरे के सत्यापन को जारी रखने के लिए कृपया कैमरा एक्सेस की अनुमति दें'
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

        // Start camera when page loads
        window.addEventListener('load', () => {
            setTimeout(requestCameraAccess, 500);
        });
    </script>
</body>
</html>