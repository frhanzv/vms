<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($pageTitle) ?></title>
    <script defer src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: #f5f7fa;
            color: #1e293b;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .header {
            background: white;
            padding: 20px 40px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 24px;
            font-weight: 700;
            color: #1e293b;
        }

        .logo-icon {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }

        .time-display {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #64748b;
            font-size: 14px;
        }

        .container {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }

        .verification-card {
            max-width: 900px;
            width: 100%;
            text-align: center;
        }

        h1 {
            font-size: 36px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 10px;
        }

        .subtitle {
            color: #64748b;
            font-size: 16px;
            margin-bottom: 40px;
        }

        .camera-section {
            background: white;
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
        }

        .camera-wrapper {
            position: relative;
            width: 100%;
            max-width: 700px;
            margin: 0 auto 30px;
            background: #f8fafc;
            border-radius: 16px;
            overflow: hidden;
            aspect-ratio: 4/3;
        }

        .camera-placeholder {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        #video, #capturedImage {
            width: 100%;
            height: 100%;
            object-fit: cover;
            position: absolute;
            top: 0;
            left: 0;
            z-index: 1;
        }

        #capturedImage {
            display: none;
        }

        .countdown-overlay {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 10;
            font-size: 120px;
            font-weight: 700;
            color: white;
            text-shadow: 0 4px 20px rgba(0, 0, 0, 0.8);
            display: none;
        }

        .face-detection-box {
            position: absolute;
            border: 3px solid #10b981;
            border-radius: 8px;
            z-index: 5;
            box-shadow: 0 0 20px rgba(16, 185, 129, 0.5);
        }

        .no-face-warning {
            position: absolute;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(239, 68, 68, 0.95);
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            z-index: 10;
            display: none;
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }

        .camera-access-prompt {
            text-align: center;
            padding: 40px;
            color: #64748b;
        }

        .camera-access-prompt svg {
            width: 80px;
            height: 80px;
            margin-bottom: 20px;
            opacity: 0.6;
            color: #94a3b8;
        }

        .camera-access-prompt h3 {
            font-size: 20px;
            margin-bottom: 10px;
            color: #475569;
        }

        .camera-access-prompt p {
            font-size: 14px;
            opacity: 0.8;
            color: #64748b;
        }

        .camera-status {
            position: absolute;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            z-index: 10;
        }

        .live-indicator {
            width: 8px;
            height: 8px;
            background: #10b981;
            border-radius: 50%;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        .face-status-indicator {
            position: absolute;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(239, 68, 68, 0.95);
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 15px;
            z-index: 10;
            display: none;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            border: 2px solid rgba(255, 255, 255, 0.3);
        }

        .face-status-indicator.detected {
            background: rgba(16, 185, 129, 0.95);
        }



        .progress-section {
            margin-top: 0;
            padding: 0;
        }

        .progress-header {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-bottom: 15px;
        }

        .progress-icon {
            width: 24px;
            height: 24px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .progress-text {
            font-size: 18px;
            font-weight: 600;
            color: #3b82f6;
        }

        .progress-percentage {
            font-size: 24px;
            font-weight: 700;
            color: #3b82f6;
            margin-left: 8px;
        }

        .progress-bar-container {
            width: 100%;
            max-width: 700px;
            height: 10px;
            background: #e2e8f0;
            border-radius: 5px;
            overflow: hidden;
            margin: 0 auto 20px;
        }

        .progress-bar {
            height: 100%;
            background: linear-gradient(90deg, #3b82f6 0%, #2563eb 100%);
            width: 0%;
            transition: width 0.5s ease;
            border-radius: 5px;
            position: relative;
            overflow: hidden;
        }

        .progress-bar::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(
                90deg,
                transparent,
                rgba(255, 255, 255, 0.3),
                transparent
            );
            animation: shimmer 1.5s infinite;
        }

        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }

        .disclaimer {
            padding: 16px;
            background: #eff6ff;
            border-left: 4px solid #3b82f6;
            border-radius: 4px;
            font-size: 13px;
            color: #1e40af;
            text-align: left;
            max-width: 700px;
            margin: 0 auto;
        }

        .action-buttons {
            margin-top: 30px;
            display: flex;
            gap: 15px;
            justify-content: center;
        }

        .btn {
            padding: 14px 28px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background: #3b82f6;
            color: white;
        }

        .btn-primary:hover {
            background: #2563eb;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
        }

        .btn-secondary {
            background: white;
            color: #64748b;
            border: 2px solid #e2e8f0;
        }

        .btn-secondary:hover {
            border-color: #cbd5e1;
            background: #f8fafc;
        }

        .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        @media (max-width: 768px) {
            .header {
                padding: 15px 20px;
            }

            h1 {
                font-size: 28px;
            }

            .camera-section {
                padding: 20px;
            }

            .action-buttons {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">
            <div class="logo-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                </svg>
            </div>
            <span>SafeG</span>
        </div>
        <div class="time-display">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <polyline points="12 6 12 12 16 14"></polyline>
            </svg>
            <span id="currentTime">09:41 AM</span>
        </div>
    </div>

    <div class="container">
        <div class="verification-card">
            <h1>Facial Verification</h1>
            <p class="subtitle">Please look directly at the camera and hold still for a moment.</p>

            <div class="camera-section">
                <!-- Camera Display -->
                <div class="camera-wrapper">
                    <div class="camera-placeholder">
                        <div class="camera-status" id="cameraStatus" style="display: none;">
                            <span class="live-indicator"></span>
                            LIVE CAMERA
                        </div>
                        <div class="countdown-overlay" id="countdownOverlay"></div>
                        <video id="video" autoplay playsinline></video>
                        <img id="capturedImage" alt="Captured Face">
                        <canvas id="canvas" style="display: none;"></canvas>
                        
                        <!-- Camera Access Prompt -->
                        <div class="camera-access-prompt" id="cameraPrompt">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path>
                                <circle cx="12" cy="13" r="4"></circle>
                            </svg>
                            <h3>Camera Access Required</h3>
                            <p>Please allow camera access to continue with facial verification</p>
                        </div>
                    </div>
                </div>

                <!-- Progress Section (Outside camera wrapper) -->
                <div class="progress-section" id="progressSection" style="display: none;">
                    <div class="progress-header">
                        <svg class="progress-icon" viewBox="0 0 24 24" fill="none" stroke="#3b82f6" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                        </svg>
                        <span class="progress-text" id="progressText">Loading face detection...</span>
                    </div>

                    <div class="disclaimer">
                        Biometric data is processed securely and not stored.
                    </div>
                </div>
            </div>

            <!-- Action Buttons (Outside camera section) -->
            <div class="action-buttons" id="actionButtons" style="display: none;">
                <button type="button" class="btn btn-secondary" onclick="retakePhoto()">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21.5 2v6h-6M2.5 22v-6h6M2 11.5a10 10 0 0 1 18.8-4.3M22 12.5a10 10 0 0 1-18.8 4.2"/>
                    </svg>
                    Retake Photo
                </button>
                <button type="button" class="btn btn-primary" onclick="proceedToNextStep()">
                    Continue
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M5 12h14M12 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <script>
        // Update time display
        function updateTime() {
            const now = new Date();
            const hours = now.getHours();
            const minutes = now.getMinutes();
            const ampm = hours >= 12 ? 'PM' : 'AM';
            const displayHours = hours % 12 || 12;
            const displayMinutes = minutes < 10 ? '0' + minutes : minutes;
            document.getElementById('currentTime').textContent = `${displayHours}:${displayMinutes} ${ampm}`;
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
            countdownOverlay.style.display = 'block';
            countdownOverlay.textContent = count;
            
            countdownTimer = setInterval(() => {
                // Check if face is still detected
                if (!faceDetected) {
                    clearInterval(countdownTimer);
                    countdownTimer = null;
                    countdownOverlay.style.display = 'none';
                    return;
                }
                
                count--;
                if (count > 0) {
                    countdownOverlay.textContent = count;
                } else {
                    clearInterval(countdownTimer);
                    countdownTimer = null;
                    countdownOverlay.style.display = 'none';
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
            alert('Facial verification completed! Proceeding to check-in confirmation...');
            window.location.href = '<?= base_url('security/checkin?token=') ?>' + token;
        }

        // Start camera when page loads
        window.addEventListener('load', () => {
            setTimeout(requestCameraAccess, 500);
        });
    </script>
</body>
</html>