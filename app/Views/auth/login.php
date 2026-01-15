<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>SafeG - Visitor Management System Login</title>
    <link rel="icon" type="image/png" href="<?= base_url('assets/images/vms-icon.png') ?>"/>
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#137fec",
                        "background-light": "#f6f7f8",
                        "background-dark": "#101922",
                    },
                    fontFamily: {
                        "display": ["Manrope", "sans-serif"],
                        "montserrat": ["Montserrat", "sans-serif"],
                    },
                    borderRadius: {"DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px"},
                },
            },
        }
    </script>
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
        }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-white antialiased overflow-hidden">
<div class="flex min-h-screen w-full relative">
    <!-- Left Side - Login Form -->
    <div class="flex flex-col w-full lg:w-[45%] xl:w-[40%] bg-background-light dark:bg-background-dark h-screen overflow-y-auto relative z-10 shadow-xl">
        <div class="flex flex-col justify-center flex-grow px-8 sm:px-12 md:px-16 lg:px-20 py-12">
            <!-- Logo -->
            <div class="flex items-center gap-3 mb-10">
                <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-primary text-white shadow-lg shadow-primary/30">
                    <span class="material-symbols-outlined text-2xl">shield_person</span>
                </div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">SafeG</h1>
            </div>

            <!-- Header -->
            <div class="mb-8">
                <h2 class="text-3xl sm:text-4xl font-bold text-slate-900 dark:text-white tracking-tight leading-tight mb-3">Welcome back</h2>
                <p class="text-slate-500 dark:text-slate-400 text-base">Please enter your details to sign in.</p>
            </div>

            <!-- Alert Messages -->
            <?php if (session()->getFlashdata('error')): ?>
            <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                <p class="text-sm text-red-800 dark:text-red-200"><?= esc(session()->getFlashdata('error')) ?></p>
            </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('success')): ?>
            <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                <p class="text-sm text-green-800 dark:text-green-200"><?= esc(session()->getFlashdata('success')) ?></p>
            </div>
            <?php endif; ?>

            <!-- Login Form -->
            <form action="<?= base_url('auth/attemptLogin') ?>" method="post" class="flex flex-col gap-6 w-full max-w-[480px]">
                <?= csrf_field() ?>
                
                <!-- Username/Email Field -->
                <div class="flex flex-col gap-2">
                    <label class="text-slate-900 dark:text-white text-sm font-semibold leading-normal" for="username">Username or Email</label>
                    <div class="relative group">
                        <span class="absolute left-4 top-4 text-slate-400 dark:text-slate-500 material-symbols-outlined">person</span>
                        <input 
                            class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-lg text-slate-900 dark:text-white focus:outline-0 focus:ring-0 border border-slate-200 dark:border-slate-700 bg-white dark:bg-[#1a2632] focus:border-primary h-14 placeholder:text-slate-400 dark:placeholder:text-slate-500 pl-12 pr-4 text-base font-normal leading-normal transition-colors" 
                            id="username" 
                            name="username"
                            placeholder="Enter your username or email" 
                            type="text" 
                            value="<?= old('username') ?>"
                            required
                        />
                    </div>
                </div>

                <!-- Password Field -->
                <div class="flex flex-col gap-2">
                    <label class="text-slate-900 dark:text-white text-sm font-semibold leading-normal" for="password">Password</label>
                    <div class="relative group">
                        <span class="absolute left-4 top-4 text-slate-400 dark:text-slate-500 material-symbols-outlined">lock</span>
                        <input 
                            class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-lg text-slate-900 dark:text-white focus:outline-0 focus:ring-0 border border-slate-200 dark:border-slate-700 bg-white dark:bg-[#1a2632] focus:border-primary h-14 placeholder:text-slate-400 dark:placeholder:text-slate-500 pl-12 pr-4 text-base font-normal leading-normal transition-colors" 
                            id="password" 
                            name="password"
                            placeholder="Enter your password" 
                            type="password"
                            required
                        />
                    </div>
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="flex flex-wrap items-center justify-between gap-3 mt-1">
                    <label class="flex items-center gap-2 cursor-pointer group">
                        <input class="w-4 h-4 rounded border-slate-300 text-primary focus:ring-primary/20 cursor-pointer" type="checkbox" name="remember" value="1"/>
                        <span class="text-sm font-medium text-slate-500 dark:text-slate-400 group-hover:text-slate-700 dark:group-hover:text-slate-300 transition-colors">Remember me</span>
                    </label>
                    <a class="text-sm font-semibold text-primary hover:text-blue-600 transition-colors" href="#">Forgot Password?</a>
                </div>

                <!-- Login Button -->
                <button type="submit" class="flex w-full items-center justify-center rounded-lg bg-primary h-14 px-4 text-base font-bold text-white shadow-md shadow-primary/20 hover:bg-blue-600 hover:shadow-lg hover:shadow-primary/30 active:scale-[0.99] transition-all duration-200 mt-2">
                    Login
                </button>
            </form>

            <!-- Demo Credentials Info -->
            <div class="mt-6 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                <p class="text-xs text-blue-800 dark:text-blue-200 font-medium mb-2">Demo Credentials:</p>
                <div class="space-y-1">
                    <div>
                        <p class="text-xs text-blue-700 dark:text-blue-300">Admin: <span class="font-bold">admin</span> / <span class="font-bold">admin123</span></p>
                    </div>
                    <div>
                        <p class="text-xs text-blue-700 dark:text-blue-300">Host: <span class="font-bold">host</span> / <span class="font-bold">host123</span></p>
                    </div>
                </div>
            </div>

            <!-- Contact Admin -->
            <div class="mt-8 text-center sm:text-left">
                <p class="text-sm text-slate-500 dark:text-slate-400">
                    Don't have an account? <a class="font-semibold text-primary hover:text-blue-600 underline decoration-transparent hover:decoration-current transition-all" href="#">Contact Administrator</a>
                </p>
            </div>
        </div>

        <!-- Footer -->
        <div class="px-8 sm:px-12 md:px-16 lg:px-20 pb-6">
            <p class="text-xs text-slate-400 dark:text-slate-600">SafeG Visitor Management System.</p>
        </div>
    </div>

    <!-- Right Side - Hero Image -->
    <div class="hidden lg:block lg:w-[55%] xl:w-[60%] relative bg-slate-100 dark:bg-slate-900">
        <div class="absolute inset-0 w-full h-full bg-cover bg-center transition-all duration-700 ease-in-out" data-alt="Modern bright corporate office lobby with glass walls and reception area" style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuCMW9bMBWzlF_H5fr9w7w3EWL0xxvt6SL3WOWhab785VAq-rPN7ObkIsyr14Mt_qViRpBCsWeGiJy_MvZtevN5n7tZw-bEZ5gGzGS2KQKwDBo8Tn69WH_kATZaaiyZsJGR9HjJoetsBEwp1g9XBSxn7zDaU-iPaepoY4EqrJGMvx8MR2FGxM9MzfDj0bLLzMBl0EAhlHtGT5a3UQyiNcsJ6_IRtUWS8HkpAFoMcKYbXFM3murPhLrKZYTSGa2hSBA4v8ggyH-BBtQ');">
        </div>
        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/30 to-transparent"></div>
        <div class="absolute bottom-0 left-0 w-full p-20 flex flex-col justify-end items-start text-white">
            <div class="w-16 h-1 bg-primary mb-6 rounded-full"></div>
            <h3 class="text-4xl font-bold leading-tight mb-4 max-w-2xl drop-shadow-sm">Secure, Seamless Visitor Management.</h3>
            <p class="text-lg text-slate-200 max-w-xl leading-relaxed drop-shadow-md">Safety Without the Hassle with SafeG's Intelligent Visitor Management System</p>
            <div class="flex items-center gap-4 mt-8 opacity-80">
                <div class="flex -space-x-3">
                    <img alt="User Avatar 1" class="w-10 h-10 rounded-full border-2 border-white object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAlAWrTtuEMMIHRwEAwYvILMQ2J00Uyk5xyS-cr-vnMfz6nI_-gdnBo7fGxCPuxOaUD1cdWdFvD_1xk-C9Y80I5PIDB1-38IZhEtupaOfyqQJZajwcxWW8yZ89ykpovWNIpRJ5r9B9lI8ZTYgBOKMxDB91P3wOLk430Ga2Ulqg76LSDn7Px5PZJoC7-mQ5TZIXGidf_RWJia2DpBRIRKxY-TFMtqrPLzMkAJ4uTsqAsMr6SsFYckXQlkfDjQGxOdKks5mujVfn1lw"/>
                    <img alt="User Avatar 2" class="w-10 h-10 rounded-full border-2 border-white object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBfCLRrFzMc9DX2fAU-1EbmfUQeTCz9_itw8bxxuo3aEGujNoc1idTsm7WOTWXQQByBjzGhMi-mUS0eUigChzRqe4jvn61H_yFUEEb9m45Pr43i4a9AQdfLu6F8sNF65ISK7LwB8NIk-4oxIuOStzO10wqLmP903WahLk_5mIU9dh69_UOPriRxq_3PuZBpNQjfEhpnjZ59_aPi1ffWE6329r-dRmJTqoPb8DKpypiuqUYQAnti5uebrQwak5k60DkSaQE3pCN_Sw"/>
                    <img alt="User Avatar 3" class="w-10 h-10 rounded-full border-2 border-white object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAQDTI-tQtch3EZBG3Bx4BI7KqhbTuBSfXVDBqzeJbpM4xPXnJIYRQPKrNJxVa0_BubDSgIC-RQaV122PfUvazxIHteEhFQnyMcFvMYN9zuHamZQ0iCov5aMka3LECtspq4WXWjw_dkI9rorcRY8AdGWTe74akGOlXRd2hJDZ6-G9qN2Tj9wqQ7LFKMxcdj9MET5P-8OBIufZCTt1Yb-fFmDPoJeKgkoD9B5V8gBFmviSZ0mywXZy6vVBobVOtqSf0kqrwxBOiQ6A"/>
                </div>
                <div class="flex flex-col">
                    <span class="text-sm font-bold">A Malaysian Product</span>
                    <div class="flex text-yellow-400 text-xs">
                        <span class="material-symbols-outlined text-[16px] fill-current">star</span>
                        <span class="material-symbols-outlined text-[16px] fill-current">star</span>
                        <span class="material-symbols-outlined text-[16px] fill-current">star</span>
                        <span class="material-symbols-outlined text-[16px] fill-current">star</span>
                        <span class="material-symbols-outlined text-[16px] fill-current">star</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
