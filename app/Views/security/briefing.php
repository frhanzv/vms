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
        }

        .container {
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
    <div class="container">
        <div class="briefing-card">
            <div class="card-header">
                <h1>Safety & Security Briefing</h1>
                <p class="subtitle">Please watch the following video to ensure your safety while visiting our facility.</p>
            </div>

            <div class="video-section">
                <div class="video-wrapper">
                    <video id="briefingVideo" controls controlsList="nodownload">
                        <source src="<?= esc($briefing_video_url) ?>" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                </div>
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
        const video = document.getElementById('briefingVideo');
        const progressBar = document.getElementById('progressBar');
        const progressPercentage = document.getElementById('progressPercentage');
        const acknowledgmentCheckbox = document.getElementById('acknowledgment');
        const checkboxWrapper = document.getElementById('checkboxWrapper');
        const submitButton = document.getElementById('submitButton');
        
        let maxWatchedTime = 0;
        let videoCompleted = false;

        // Track video progress
        video.addEventListener('timeupdate', function() {
            const currentTime = video.currentTime;
            const duration = video.duration;
            
            // Update max watched time
            if (currentTime > maxWatchedTime) {
                maxWatchedTime = currentTime;
            }
            
            // Calculate progress based on maximum time watched
            const progress = (maxWatchedTime / duration) * 100;
            progressBar.style.width = progress + '%';
            progressPercentage.textContent = Math.round(progress) + '%';
            
            // Enable checkbox when video is 90% complete
            if (progress >= 90 && !videoCompleted) {
                videoCompleted = true;
                acknowledgmentCheckbox.disabled = false;
                checkboxWrapper.style.cursor = 'pointer';
            }
        });

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
                        video_duration: video.duration,
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

        // Prevent video seeking beyond watched point
        video.addEventListener('seeking', function() {
            if (video.currentTime > maxWatchedTime + 1) {
                video.currentTime = maxWatchedTime;
            }
        });
    </script>
</body>
</html>