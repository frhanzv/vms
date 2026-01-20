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
            line-height: 1.6;
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
            max-width: 900px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        .briefing-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin-bottom: 30px;
        }

        .card-header {
            padding: 30px;
            text-align: center;
            border-bottom: 1px solid #e2e8f0;
        }

        h1 {
            font-size: 32px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 10px;
        }

        .subtitle {
            color: #64748b;
            font-size: 16px;
        }

        .video-section {
            padding: 30px;
            background: #f8fafc;
        }

        .video-wrapper {
            position: relative;
            width: 100%;
            border-radius: 8px;
            overflow: hidden;
            background: #000;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        video {
            width: 100%;
            height: auto;
            display: block;
        }

        .progress-section {
            padding: 20px 30px;
            background: white;
        }

        .progress-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .progress-label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            color: #1e293b;
        }

        .progress-icon {
            width: 20px;
            height: 20px;
            color: #3b82f6;
        }

        .progress-percentage {
            font-weight: 700;
            color: #3b82f6;
            font-size: 18px;
        }

        .progress-bar-container {
            width: 100%;
            height: 8px;
            background: #e2e8f0;
            border-radius: 4px;
            overflow: hidden;
        }

        .progress-bar {
            height: 100%;
            background: linear-gradient(90deg, #3b82f6 0%, #2563eb 100%);
            width: 0%;
            transition: width 0.3s ease;
            border-radius: 4px;
        }

        .acknowledgment-section {
            padding: 30px;
            border-top: 1px solid #e2e8f0;
        }

        .checkbox-wrapper {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 20px;
            background: #f8fafc;
            border-radius: 8px;
            border: 2px solid #e2e8f0;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .checkbox-wrapper:hover {
            border-color: #3b82f6;
            background: #eff6ff;
        }

        .checkbox-wrapper.checked {
            border-color: #3b82f6;
            background: #eff6ff;
        }

        input[type="checkbox"] {
            width: 20px;
            height: 20px;
            cursor: pointer;
            flex-shrink: 0;
            margin-top: 2px;
        }

        .checkbox-label {
            font-size: 15px;
            color: #334155;
            line-height: 1.5;
            user-select: none;
            cursor: pointer;
        }

        .submit-button {
            width: 100%;
            padding: 16px;
            background: #3b82f6;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 20px;
        }

        .submit-button:hover:not(:disabled) {
            background: #2563eb;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
        }

        .submit-button:disabled {
            background: #94a3b8;
            cursor: not-allowed;
            opacity: 0.6;
        }

        .disclaimer {
            margin-top: 20px;
            padding: 16px;
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            border-radius: 4px;
            font-size: 13px;
            color: #78350f;
        }

        .skip-warning {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 16px 20px;
            background: #ef4444;
            color: white;
            border-radius: 8px;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.4);
            z-index: 1000;
            display: none;
            animation: slideIn 0.3s ease;
        }

        .skip-warning.show {
            display: block;
        }

        @keyframes slideIn {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .arrow-icon {
            width: 20px;
            height: 20px;
        }

        .spinner {
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top: 2px solid white;
            width: 20px;
            height: 20px;
            animation: spin 0.8s linear infinite;
            display: none;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .submit-button.loading .spinner {
            display: block;
        }

        .submit-button.loading .button-text {
            display: none;
        }

        @media (max-width: 768px) {
            .header {
                padding: 15px 20px;
            }

            .logo {
                font-size: 20px;
            }

            .logo-icon {
                width: 32px;
                height: 32px;
            }

            .container {
                padding: 20px 15px;
            }

            h1 {
                font-size: 24px;
            }

            .card-header,
            .video-section,
            .acknowledgment-section {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
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
            <span id="currentTime"></span>
        </div>
    </div>

    <!-- Skip Warning Alert -->
    <div class="skip-warning" id="skipWarning">
        ⚠️ You cannot skip the video. Please watch from start to finish.
    </div>

    <div class="container">
        <div class="briefing-card">
            <div class="card-header">
                <h1>Safety & Security Briefing</h1>
                <p class="subtitle">Please watch the following video to ensure your safety while visiting our facility.</p>
            </div>

            <div class="video-section">
                <?php if ($video_available): ?>
                    <div class="video-wrapper">
                        <video id="briefingVideo" controls controlsList="nodownload">
                            <source src="<?= esc($briefing_video_url) ?>" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                    </div>
                <?php else: ?>
                    <div style="text-align: center; padding: 60px 20px; background: white; border-radius: 8px;">
                        <svg style="width: 64px; height: 64px; color: #94a3b8; margin: 0 auto 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                        <h3 style="color: #64748b; font-size: 18px; margin-bottom: 10px;">No Video Available</h3>
                        <p style="color: #94a3b8; font-size: 14px;">Please contact the administrator.</p>
                    </div>
                <?php endif; ?>
            </div>

            <div class="progress-section">
                <div class="progress-header">
                    <div class="progress-label">
                        <svg class="progress-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Briefing Progress</span>
                    </div>
                    <span class="progress-percentage" id="progressPercentage">0%</span>
                </div>
                <div class="progress-bar-container">
                    <div class="progress-bar" id="progressBar"></div>
                </div>
            </div>

            <div class="acknowledgment-section">
                <div class="checkbox-wrapper" id="checkboxWrapper">
                    <input type="checkbox" id="acknowledgment" disabled>
                    <label for="acknowledgment" class="checkbox-label">
                        I have watched the entire video and clearly understand the safety protocols and emergency procedures of this facility.
                    </label>
                </div>

                <button type="button" id="submitButton" class="submit-button" disabled>
                    <span class="button-text">
                        I Acknowledge & Check-in
                        <svg class="arrow-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                    </span>
                    <div class="spinner"></div>
                </button>

                <div class="disclaimer">
                    By clicking confirm, you agree to abide by all SafeG site regulations. Failure to comply may result in denied access.
                </div>
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

        const video = document.getElementById('briefingVideo');
        const progressBar = document.getElementById('progressBar');
        const progressPercentage = document.getElementById('progressPercentage');
        const acknowledgmentCheckbox = document.getElementById('acknowledgment');
        const checkboxWrapper = document.getElementById('checkboxWrapper');
        const submitButton = document.getElementById('submitButton');
        const skipWarning = document.getElementById('skipWarning');
        
        let maxWatchedTime = 0;
        let videoCompleted = false;
        let lastValidTime = 0;

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
                    // User tried to skip - reset to last valid position
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
                    checkboxWrapper.style.cursor = 'pointer';
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

            // Show warning when user tries to skip
            function showSkipWarning() {
                skipWarning.classList.add('show');
                setTimeout(() => {
                    skipWarning.classList.remove('show');
                }, 3000);
            }
        }

        // Handle checkbox click
        checkboxWrapper.addEventListener('click', function() {
            if (!acknowledgmentCheckbox.disabled) {
                acknowledgmentCheckbox.checked = !acknowledgmentCheckbox.checked;
                toggleCheckboxStyle();
                toggleSubmitButton();
            }
        });

        acknowledgmentCheckbox.addEventListener('change', function() {
            toggleCheckboxStyle();
            toggleSubmitButton();
        });

        function toggleCheckboxStyle() {
            if (acknowledgmentCheckbox.checked) {
                checkboxWrapper.classList.add('checked');
            } else {
                checkboxWrapper.classList.remove('checked');
            }
        }

        function toggleSubmitButton() {
            submitButton.disabled = !acknowledgmentCheckbox.checked;
        }

        // Handle form submission
        submitButton.addEventListener('click', function() {
            if (acknowledgmentCheckbox.checked && videoCompleted) {
                submitButton.classList.add('loading');
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
                        // Redirect to next page
                        window.location.href = data.redirect_url;
                    } else {
                        alert(data.message);
                        submitButton.classList.remove('loading');
                        submitButton.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred. Please try again.');
                    submitButton.classList.remove('loading');
                    submitButton.disabled = false;
                });
            }
        });

        // Disable keyboard shortcuts that could skip video
        if (video) {
            document.addEventListener('keydown', function(e) {
                // Prevent arrow keys, space, and other video control keys
                if (video && !video.paused) {
                    const videoBounds = video.getBoundingClientRect();
                    const isVideoFocused = document.activeElement === video;
                    
                    // Block arrow keys (left/right for seeking)
                    if (e.keyCode === 37 || e.keyCode === 39) { // Left/Right arrows
                        e.preventDefault();
                        showSkipWarning();
                    }
                }
            });
        }
    </script>
</body>
</html>