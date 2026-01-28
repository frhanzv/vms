<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($pageTitle) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#137fec",
                        "primary-dark": "#0f66be",
                        "background-light": "#f6f7f8",
                        "background-dark": "#101922",
                        "surface-light": "#ffffff",
                        "surface-dark": "#1a2634",
                    },
                    fontFamily: {
                        "display": ["Montserrat", "sans-serif"],
                        "sans": ["Montserrat", "sans-serif"]
                    },
                    borderRadius: {"DEFAULT": "0.375rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px"},
                },
            },
        }
    </script>
    <style>
        body { 
            font-family: 'Montserrat', sans-serif;
            min-height: 100vh;
        }
        @keyframes checkmark {
            0% { stroke-dashoffset: 100; }
            100% { stroke-dashoffset: 0; }
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .checkmark-circle {
            stroke-dasharray: 166;
            stroke-dashoffset: 166;
            animation: checkmark 0.6s ease-in-out forwards;
        }
        .checkmark-check {
            stroke-dasharray: 48;
            stroke-dashoffset: 48;
            animation: checkmark 0.3s 0.5s ease-in-out forwards;
        }
        .fade-in {
            animation: fadeIn 0.6s ease-out forwards;
        }
    </style>
</head>
<body class="bg-background-light">
    <!-- Header -->
    <div class="bg-surface-light shadow-sm border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="bg-primary/10 rounded-lg size-10 flex items-center justify-center">
                        <span class="material-symbols-outlined text-primary text-2xl">shield_person</span>
                    </div>
                    <span class="text-xl font-bold text-slate-900">SafeG</span>
                </div>
                <div class="flex items-center gap-6 text-slate-600 text-sm">
                    <button type="button" onclick="showHelp()" class="flex items-center gap-2 hover:text-primary transition-colors cursor-pointer">
                        <span class="material-symbols-outlined text-lg">help</span>
                        Help
                    </button>
                    <button type="button" onclick="showLanguageMenu()" class="flex items-center gap-2 hover:text-primary transition-colors cursor-pointer">
                        <span class="material-symbols-outlined text-lg">language</span>
                        <span id="currentLang">English</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-5xl mx-auto px-6 py-12">
        <div class="bg-surface-light rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <!-- Success Header with Green Background -->
            <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-b border-green-200 px-8 py-12 text-center">
                <div class="flex justify-center mb-6">
                    <svg class="w-28 h-28" viewBox="0 0 52 52">
                        <circle class="checkmark-circle" cx="26" cy="26" r="25" fill="none" stroke="#22c55e" stroke-width="2"/>
                        <path class="checkmark-check" fill="none" stroke="#22c55e" stroke-width="3" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
                    </svg>
                </div>
                <h1 class="text-4xl font-bold text-slate-900 mb-3" data-translate="Registration Complete!">
                    Registration Complete!
                </h1>
                <p class="text-lg text-slate-600 max-w-2xl mx-auto" data-translate="Thank you for completing your visitor registration. All steps have been successfully completed.">
                    Thank you for completing your visitor registration. All steps have been successfully completed.
                </p>
            </div>

            <div class="px-8 py-8">
                <div class="grid md:grid-cols-2 gap-8 mb-8">
                    <!-- Left Column - Completion Steps -->
                    <div class="fade-in">
                        <h2 class="text-lg font-bold text-slate-900 mb-4 flex items-center gap-2">
                            <span class="material-symbols-outlined text-green-600">verified</span>
                            <span data-translate="Completed Steps">Completed Steps</span>
                        </h2>
                        <div class="bg-green-50 rounded-lg p-6 border border-green-200">
                            <div class="space-y-4">
                                <div class="flex items-start gap-3">
                                    <div class="size-8 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                                        <span class="material-symbols-outlined text-green-600 text-xl">check_circle</span>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-slate-900">Visitor Information Submitted</p>
                                        <p class="text-sm text-slate-600 mt-1">Your details have been recorded in our system</p>
                                    </div>
                                </div>
                                <div class="flex items-start gap-3">
                                    <div class="size-8 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                                        <span class="material-symbols-outlined text-green-600 text-xl">check_circle</span>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-slate-900">Security Briefing Completed</p>
                                        <p class="text-sm text-slate-600 mt-1">Safety protocols have been acknowledged</p>
                                    </div>
                                </div>
                                <div class="flex items-start gap-3">
                                    <div class="size-8 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                                        <span class="material-symbols-outlined text-green-600 text-xl">check_circle</span>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-slate-900">Facial Verification Done</p>
                                        <p class="text-sm text-slate-600 mt-1">Identity verification completed successfully</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column - Next Steps -->
                    <div class="fade-in" style="animation-delay: 0.2s;">
                        <h2 class="text-lg font-bold text-slate-900 mb-4 flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary">arrow_forward</span>
                            <span data-translate="Next Steps">Next Steps</span>
                        </h2>
                        <div class="bg-blue-50 rounded-lg p-6 border border-blue-200">
                            <div class="space-y-4">
                                <div class="flex items-start gap-3">
                                    <div class="size-8 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                                        <span class="text-primary font-bold">1</span>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-slate-900">Proceed to Reception</p>
                                        <p class="text-sm text-slate-600 mt-1">Make your way to the main reception desk</p>
                                    </div>
                                </div>
                                <div class="flex items-start gap-3">
                                    <div class="size-8 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                                        <span class="text-primary font-bold">2</span>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-slate-900">Present Identification</p>
                                        <p class="text-sm text-slate-600 mt-1">Show your IC or passport for verification</p>
                                    </div>
                                </div>
                                <div class="flex items-start gap-3">
                                    <div class="size-8 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                                        <span class="text-primary font-bold">3</span>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-slate-900">Collect Visitor Pass</p>
                                        <p class="text-sm text-slate-600 mt-1">Receive and wear your visitor badge</p>
                                    </div>
                                </div>
                                <div class="flex items-start gap-3">
                                    <div class="size-8 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                                        <span class="text-primary font-bold">4</span>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-slate-900">Follow Safety Protocols</p>
                                        <p class="text-sm text-slate-600 mt-1">Adhere to all safety guidelines during your visit</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Important Notice Banner -->
                <div class="bg-amber-50 border-l-4 border-amber-400 p-6 rounded-r-lg fade-in" style="animation-delay: 0.4s;">
                    <div class="flex items-start gap-4">
                        <span class="material-symbols-outlined text-amber-600 text-3xl flex-shrink-0">warning</span>
                        <div>
                            <h3 class="font-bold text-slate-900 mb-2" data-translate="Important Reminders">Important Reminders</h3>
                            <ul class="text-sm text-slate-700 space-y-1.5">
                                <li class="flex items-start gap-2">
                                    <span class="text-amber-600 mt-0.5">•</span>
                                    <span>Your visitor pass must be worn visibly at all times</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="text-amber-600 mt-0.5">•</span>
                                    <span>Escort requirements may apply in certain areas</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="text-amber-600 mt-0.5">•</span>
                                    <span>Return your visitor pass before departing</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="text-amber-600 mt-0.5">•</span>
                                    <span>Emergency exits are marked throughout the facility</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="border-t border-slate-200 bg-surface-light py-8 mt-12">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-2 text-slate-600">
                    <span class="material-symbols-outlined text-primary">shield_person</span>
                    <span class="font-semibold">SafeG Visitor Management System</span>
                </div>
                <div class="text-sm text-slate-500">
                    © 2026 SafeG. All rights reserved.
                </div>
            </div>
        </div>
    </div>

    <!-- Help Modal -->
    <div id="helpModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full" style="animation: scale-in 0.2s ease-out;">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                        <span class="material-symbols-outlined text-2xl text-primary">help</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900">Need Help?</h3>
                </div>
            </div>
            <div class="p-6">
                <div class="space-y-4 text-gray-700">
                    <p class="font-semibold">Completion Information:</p>
                    <ul class="space-y-2 text-sm">
                        <li class="flex items-start gap-2">
                            <span class="material-symbols-outlined text-lg text-primary mt-0.5">check_circle</span>
                            <span>All registration steps have been completed successfully</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="material-symbols-outlined text-lg text-primary mt-0.5">badge</span>
                            <span>Please proceed to reception to collect your visitor pass</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="material-symbols-outlined text-lg text-primary mt-0.5">contact_support</span>
                            <span>Contact reception if you need assistance</span>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="p-6 bg-gray-50 rounded-b-2xl flex justify-end">
                <button onclick="closeHelpModal()" class="px-6 py-2.5 bg-primary hover:bg-primary-dark text-white rounded-lg font-medium transition-colors">
                    Got it
                </button>
            </div>
        </div>
    </div>

    <!-- Language Modal -->
    <div id="languageModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full" style="animation: scale-in 0.2s ease-out;">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                        <span class="material-symbols-outlined text-2xl text-purple-600">language</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900">Select Language</h3>
                </div>
            </div>
            <div class="p-6 max-h-96 overflow-y-auto">
                <div class="space-y-2">
                    <button onclick="changeLanguage('en')" class="w-full flex items-center justify-between p-3 hover:bg-gray-100 rounded-lg transition-colors text-left">
                        <span class="font-medium">🇬🇧 English</span>
                        <span id="check-en" class="material-symbols-outlined text-primary">check</span>
                    </button>
                    <button onclick="changeLanguage('ms')" class="w-full flex items-center justify-between p-3 hover:bg-gray-100 rounded-lg transition-colors text-left">
                        <span class="font-medium">🇲🇾 Bahasa Malaysia</span>
                        <span id="check-ms" class="material-symbols-outlined text-primary hidden">check</span>
                    </button>
                    <button onclick="changeLanguage('zh-CN')" class="w-full flex items-center justify-between p-3 hover:bg-gray-100 rounded-lg transition-colors text-left">
                        <span class="font-medium">🇨🇳 简体中文</span>
                        <span id="check-zh-CN" class="material-symbols-outlined text-primary hidden">check</span>
                    </button>
                    <button onclick="changeLanguage('zh-TW')" class="w-full flex items-center justify-between p-3 hover:bg-gray-100 rounded-lg transition-colors text-left">
                        <span class="font-medium">🇹🇼 繁體中文</span>
                        <span id="check-zh-TW" class="material-symbols-outlined text-primary hidden">check</span>
                    </button>
                    <button onclick="changeLanguage('ta')" class="w-full flex items-center justify-between p-3 hover:bg-gray-100 rounded-lg transition-colors text-left">
                        <span class="font-medium">🇮🇳 தமிழ்</span>
                        <span id="check-ta" class="material-symbols-outlined text-primary hidden">check</span>
                    </button>
                    <button onclick="changeLanguage('hi')" class="w-full flex items-center justify-between p-3 hover:bg-gray-100 rounded-lg transition-colors text-left">
                        <span class="font-medium">🇮🇳 हिन्दी</span>
                        <span id="check-hi" class="material-symbols-outlined text-primary hidden">check</span>
                    </button>
                    <button onclick="changeLanguage('ja')" class="w-full flex items-center justify-between p-3 hover:bg-gray-100 rounded-lg transition-colors text-left">
                        <span class="font-medium">🇯🇵 日本語</span>
                        <span id="check-ja" class="material-symbols-outlined text-primary hidden">check</span>
                    </button>
                    <button onclick="changeLanguage('ko')" class="w-full flex items-center justify-between p-3 hover:bg-gray-100 rounded-lg transition-colors text-left">
                        <span class="font-medium">🇰🇷 한국어</span>
                        <span id="check-ko" class="material-symbols-outlined text-primary hidden">check</span>
                    </button>
                    <button onclick="changeLanguage('th')" class="w-full flex items-center justify-between p-3 hover:bg-gray-100 rounded-lg transition-colors text-left">
                        <span class="font-medium">🇹🇭 ภาษาไทย</span>
                        <span id="check-th" class="material-symbols-outlined text-primary hidden">check</span>
                    </button>
                    <button onclick="changeLanguage('vi')" class="w-full flex items-center justify-between p-3 hover:bg-gray-100 rounded-lg transition-colors text-left">
                        <span class="font-medium">🇻🇳 Tiếng Việt</span>
                        <span id="check-vi" class="material-symbols-outlined text-primary hidden">check</span>
                    </button>
                    <button onclick="changeLanguage('id')" class="w-full flex items-center justify-between p-3 hover:bg-gray-100 rounded-lg transition-colors text-left">
                        <span class="font-medium">🇮🇩 Bahasa Indonesia</span>
                        <span id="check-id" class="material-symbols-outlined text-primary hidden">check</span>
                    </button>
                </div>
            </div>
            <div class="p-6 bg-gray-50 rounded-b-2xl flex justify-end">
                <button onclick="closeLanguageModal()" class="px-6 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg font-medium transition-colors">
                    Close
                </button>
            </div>
        </div>
    </div>

    <style>
        @keyframes scale-in {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }
    </style>

    <script>
        function showHelp() {
            document.getElementById('helpModal').classList.remove('hidden');
        }

        function closeHelpModal() {
            document.getElementById('helpModal').classList.add('hidden');
        }

        function showLanguageMenu() {
            document.getElementById('languageModal').classList.remove('hidden');
        }

        function closeLanguageModal() {
            document.getElementById('languageModal').classList.add('hidden');
        }

        function changeLanguage(langCode) {
            localStorage.setItem('selectedLanguage', langCode);
            const langNames = {'en': 'English', 'ms': 'Bahasa Malaysia', 'zh-CN': '中文', 'zh-TW': '繁體中文', 'ta': 'தமிழ்', 'hi': 'हिन्दी', 'ja': '日本語', 'ko': '한국어', 'th': 'ภาษาไทย', 'vi': 'Tiếng Việt', 'id': 'Bahasa Indonesia'};
            document.getElementById('currentLang').textContent = langNames[langCode];
            
            // Update checkmarks
            ['en', 'ms', 'zh-CN', 'zh-TW', 'ta', 'hi', 'ja', 'ko', 'th', 'vi', 'id'].forEach(lang => {
                const check = document.getElementById('check-' + lang);
                if (check) {
                    if (lang === langCode) {
                        check.classList.remove('hidden');
                    } else {
                        check.classList.add('hidden');
                    }
                }
            });

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
                'Registration Complete!': 'Registration Complete!',
                'Thank you for completing your visitor registration. All steps have been successfully completed.': 'Thank you for completing your visitor registration. All steps have been successfully completed.',
                'Completed Steps': 'Completed Steps',
                'Next Steps': 'Next Steps',
                'Important Reminders': 'Important Reminders'
            };

            const translations = {
                'ms': {
                    'Registration Complete!': 'Pendaftaran Selesai!',
                    'Thank you for completing your visitor registration. All steps have been successfully completed.': 'Terima kasih kerana melengkapkan pendaftaran pelawat anda. Semua langkah telah berjaya diselesaikan.',
                    'Completed Steps': 'Langkah-langkah Selesai',
                    'Next Steps': 'Langkah Seterusnya',
                    'Important Reminders': 'Peringatan Penting'
                },
                'zh-CN': {
                    'Registration Complete!': '注册完成！',
                    'Thank you for completing your visitor registration. All steps have been successfully completed.': '感谢您完成访客注册。所有步骤已成功完成。',
                    'Completed Steps': '已完成步骤',
                    'Next Steps': '下一步',
                    'Important Reminders': '重要提醒'
                },
                'zh-TW': {
                    'Registration Complete!': '註冊完成！',
                    'Thank you for completing your visitor registration. All steps have been successfully completed.': '感謝您完成訪客註冊。所有步驟已成功完成。',
                    'Completed Steps': '已完成步驟',
                    'Next Steps': '下一步',
                    'Important Reminders': '重要提醒'
                },
                'ta': {
                    'Registration Complete!': 'பதிவு முடிந்தது!',
                    'Thank you for completing your visitor registration. All steps have been successfully completed.': 'உங்கள் பார்வையாளர் பதிவை முடித்ததற்கு நன்றி. அனைத்து படிகளும் வெற்றிகரமாக முடிக்கப்பட்டன.',
                    'Completed Steps': 'முடிந்த படிகள்',
                    'Next Steps': 'அடுத்த படிகள்',
                    'Important Reminders': 'முக்கிய நினைவூட்டல்கள்'
                },
                'hi': {
                    'Registration Complete!': 'पंजीकरण पूर्ण!',
                    'Thank you for completing your visitor registration. All steps have been successfully completed.': 'आपका आगंतुक पंजीकरण पूरा करने के लिए धन्यवाद। सभी चरण सफलतापूर्वक पूर्ण हो गए हैं।',
                    'Completed Steps': 'पूर्ण चरण',
                    'Next Steps': 'अगले चरण',
                    'Important Reminders': 'महत्वपूर्ण अनुस्मारक'
                },
                'ja': {
                    'Registration Complete!': '登録完了！',
                    'Thank you for completing your visitor registration. All steps have been successfully completed.': '訪問者登録を完了していただきありがとうございます。すべての手順が正常に完了しました。',
                    'Completed Steps': '完了した手順',
                    'Next Steps': '次のステップ',
                    'Important Reminders': '重要な注意事項'
                },
                'ko': {
                    'Registration Complete!': '등록 완료!',
                    'Thank you for completing your visitor registration. All steps have been successfully completed.': '방문자 등록을 완료해 주셔서 감사합니다. 모든 단계가 성공적으로 완료되었습니다.',
                    'Completed Steps': '완료된 단계',
                    'Next Steps': '다음 단계',
                    'Important Reminders': '중요 알림'
                },
                'th': {
                    'Registration Complete!': 'การลงทะเบียนเสร็จสมบูรณ์!',
                    'Thank you for completing your visitor registration. All steps have been successfully completed.': 'ขอบคุณที่ทำการลงทะเบียนผู้เยี่ยมชมของคุณเสร็จสมบูรณ์ ทุกขั้นตอนได้ดำเนินการเสร็จสิ้นเรียบร้อยแล้ว',
                    'Completed Steps': 'ขั้นตอนที่เสร็จสมบูรณ์',
                    'Next Steps': 'ขั้นตอนถัดไป',
                    'Important Reminders': 'การเตือนที่สำคัญ'
                },
                'vi': {
                    'Registration Complete!': 'Đăng ký hoàn tất!',
                    'Thank you for completing your visitor registration. All steps have been successfully completed.': 'Cảm ơn bạn đã hoàn thành đăng ký khách. Tất cả các bước đã được hoàn thành thành công.',
                    'Completed Steps': 'Các bước đã hoàn thành',
                    'Next Steps': 'Bước tiếp theo',
                    'Important Reminders': 'Lời nhắc quan trọng'
                },
                'id': {
                    'Registration Complete!': 'Pendaftaran Selesai!',
                    'Thank you for completing your visitor registration. All steps have been successfully completed.': 'Terima kasih telah menyelesaikan pendaftaran pengunjung Anda. Semua langkah telah berhasil diselesaikan.',
                    'Completed Steps': 'Langkah-langkah Selesai',
                    'Next Steps': 'Langkah Selanjutnya',
                    'Important Reminders': 'Pengingat Penting'
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

        // Load saved language on page load
        window.addEventListener('DOMContentLoaded', function() {
            const savedLang = localStorage.getItem('selectedLanguage');
            if (savedLang && savedLang !== 'en') {
                changeLanguage(savedLang);
            }
        });

        // Close modals on outside click
        document.getElementById('helpModal')?.addEventListener('click', function(e) {
            if (e.target === this) closeHelpModal();
        });
        document.getElementById('languageModal')?.addEventListener('click', function(e) {
            if (e.target === this) closeLanguageModal();
        });
    </script>
</body>
</html>