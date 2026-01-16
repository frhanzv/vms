<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($pageTitle) ?></title>
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
            background: #3d4f51;
            border-radius: 16px;
            overflow: hidden;
            aspect-ratio: 4/3;
        }

        .camera-placeholder {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #475569 0%, #334155 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
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
                        <div class="camera-status">
                            <span class="live-indicator"></span>
                            LIVE CAMERA
                        </div>
                        <img src="<?= base_url('assets/images/facialRecognition.jpg') ?>" alt="Face" style="width: 100%; height: 100%; object-fit: cover; position: absolute; top: 0; left: 0; z-index: 1;">
                    </div>
                </div>

                <!-- Progress Section (Outside camera wrapper) -->
                <div class="progress-section">
                    <div class="progress-header">
                        <svg class="progress-icon" viewBox="0 0 24 24" fill="none" stroke="#3b82f6" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                        </svg>
                        <span class="progress-text">Scanning face...</span>
                        <span class="progress-percentage" id="scanProgress">0%</span>
                    </div>
                    <div class="progress-bar-container">
                        <div class="progress-bar" id="progressBar"></div>
                    </div>

                    <div class="disclaimer">
                        Biometric data is processed securely and not stored.
                    </div>
                </div>
            </div>

            <!-- Action Buttons (Outside camera section) -->
            <div class="action-buttons" id="actionButtons" style="display: none;">
                <button type="button" class="btn btn-secondary" onclick="retryScan()">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21.5 2v6h-6M2.5 22v-6h6M2 11.5a10 10 0 0 1 18.8-4.3M22 12.5a10 10 0 0 1-18.8 4.2"/>
                    </svg>
                    Retry Scan
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

        // Simulate facial scanning progress
        let progress = 0;
        const progressBar = document.getElementById('progressBar');
        const progressText = document.getElementById('scanProgress');
        const actionButtons = document.getElementById('actionButtons');

        function startScanning() {
            const interval = setInterval(() => {
                if (progress >= 100) {
                    clearInterval(interval);
                    setTimeout(() => {
                        actionButtons.style.display = 'flex';
                    }, 500);
                    return;
                }
                
                progress += 2;
                progressBar.style.width = progress + '%';
                progressText.textContent = progress + '%';
            }, 100);
        }

        // Start scanning after page loads
        window.addEventListener('load', () => {
            setTimeout(startScanning, 1000);
        });

        function retryScan() {
            progress = 0;
            progressBar.style.width = '0%';
            progressText.textContent = '0%';
            actionButtons.style.display = 'none';
            setTimeout(startScanning, 500);
        }

        function proceedToNextStep() {
            const token = new URLSearchParams(window.location.search).get('token');
            
            // TODO: Send verification data to backend
            alert('Facial verification completed! Proceeding to check-in confirmation...');
            
            // Redirect to next step (check-in confirmation)
            window.location.href = '<?= base_url('security/checkin?token=') ?>' + token;
        }
    </script>
</body>
</html>