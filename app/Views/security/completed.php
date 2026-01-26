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
                    <span class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-lg">help</span>
                        Help
                    </span>
                    <span class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-lg">language</span>
                        English
                    </span>
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
                <h1 class="text-4xl font-bold text-slate-900 mb-3">
                    Registration Complete!
                </h1>
                <p class="text-lg text-slate-600 max-w-2xl mx-auto">
                    Thank you for completing your visitor registration. All steps have been successfully completed.
                </p>
            </div>

            <div class="px-8 py-8">
                <div class="grid md:grid-cols-2 gap-8 mb-8">
                    <!-- Left Column - Completion Steps -->
                    <div class="fade-in">
                        <h2 class="text-lg font-bold text-slate-900 mb-4 flex items-center gap-2">
                            <span class="material-symbols-outlined text-green-600">verified</span>
                            Completed Steps
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
                            Next Steps
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
                            <h3 class="font-bold text-slate-900 mb-2">Important Reminders</h3>
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
</body>
</html>