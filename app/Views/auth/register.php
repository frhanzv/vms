<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title><?= esc($pageTitle ?? 'Register Your Company - SafeG') ?></title>
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
        body { font-family: 'Montserrat', sans-serif; }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-white antialiased">
<div class="flex min-h-screen w-full justify-center py-12 px-4">
    <div class="w-full max-w-2xl">
        <!-- Logo -->
        <div class="flex items-center gap-3 mb-8">
            <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-primary text-white shadow-lg shadow-primary/30">
                <span class="material-symbols-outlined text-2xl">shield_person</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">SafeG</h1>
        </div>

        <div class="mb-8">
            <h2 class="text-3xl font-bold text-slate-900 dark:text-white tracking-tight leading-tight mb-3">Register Your Company</h2>
            <p class="text-slate-500 dark:text-slate-400 text-base">Create a Vendor Pass account for your company so you can submit and track your own vendor pass requests online.</p>
        </div>

        <?php if (session()->getFlashdata('error')): ?>
        <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
            <p class="text-sm text-red-800 dark:text-red-200"><?= esc(session()->getFlashdata('error')) ?></p>
        </div>
        <?php endif; ?>

        <?php if (! empty($clientLink)): ?>
        <div class="mb-6 rounded-lg border border-primary/30 bg-primary/5 px-4 py-3 text-sm">
            You are registering with <strong><?= esc($clientLink['name']) ?></strong>. Your passes will be submitted to this client.
        </div>
        <?php endif; ?>

        <div id="searchMsg" class="hidden mb-6 p-4 rounded-lg text-sm"></div>

        <form action="<?= ! empty($clientLink) ? base_url('c/' . rawurlencode($clientLink['code']) . '/register') : base_url('register') ?>" method="post" class="bg-white dark:bg-[#1a2632] border border-slate-200 dark:border-slate-700 rounded-xl p-6 sm:p-8 flex flex-col gap-6">
            <?= csrf_field() ?>

            <!-- Company lookup -->
            <div class="flex flex-col gap-2">
                <label class="text-sm font-semibold" for="ssm_no">Company SSM No</label>
                <div class="flex gap-2">
                    <input class="form-input flex-1 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-[#101922] h-12 px-4 text-base" id="ssm_no" name="ssm_no" type="text" value="<?= esc(old('ssm_no') ?: ($prefillSsm ?? '')) ?>" placeholder="e.g. 201901012345" required/>
                    <button type="button" id="searchCompanyBtn" class="h-12 px-5 rounded-lg bg-slate-700 hover:bg-slate-800 text-white text-sm font-semibold whitespace-nowrap">Search</button>
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400">Search for your company first — it must already be registered with KPK before you can create an account for it.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="flex flex-col gap-2">
                    <label class="text-sm font-semibold">Company Name</label>
                    <input id="company_name" class="form-input rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-[#101922] h-12 px-4 text-base" type="text" value="" readonly placeholder="Found after searching"/>
                </div>
                <div class="flex flex-col gap-2">
                    <label class="text-sm font-semibold">Company Name In Port Pass</label>
                    <input id="company_pass_name" class="form-input rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-[#101922] h-12 px-4 text-base" type="text" value="" readonly placeholder="Found after searching"/>
                </div>
            </div>

            <?php if (! empty($clientLink)): ?>
            <input type="hidden" name="client_id" value="<?= (int) $clientLink['id'] ?>"/>
            <?php endif; ?>
            <div id="clientWrap" class="hidden flex flex-col gap-2">
                <label class="text-sm font-semibold" for="client_id">Client you are working with</label>
                <select class="form-select rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-[#101922] h-12 px-4 text-base" id="client_id" name="client_id">
                    <option value="">Select client</option>
                </select>
                <p class="text-xs text-slate-500 dark:text-slate-400">The passes you request will be submitted to this client.</p>
            </div>

            <hr class="border-slate-200 dark:border-slate-700"/>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="flex flex-col gap-2">
                    <label class="text-sm font-semibold" for="password">Password</label>
                    <input class="form-input rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-[#101922] h-12 px-4 text-base" id="password" name="password" type="password" minlength="6" required/>
                </div>
                <div class="flex flex-col gap-2">
                    <label class="text-sm font-semibold" for="email">Email</label>
                    <input class="form-input rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-[#101922] h-12 px-4 text-base" id="email" name="email" type="email" value="<?= old('email') ?>" required/>
                </div>
                <div class="flex flex-col gap-2">
                    <label class="text-sm font-semibold" for="country">Country</label>
                    <input class="form-input rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-[#101922] h-12 px-4 text-base" id="country" name="country" type="text" value="<?= old('country', 'Malaysia') ?>"/>
                </div>
                <div class="flex flex-col gap-2">
                    <label class="text-sm font-semibold" for="full_name">Administrator's Full Name</label>
                    <input class="form-input rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-[#101922] h-12 px-4 text-base" id="full_name" name="full_name" type="text" value="<?= old('full_name') ?>" required/>
                </div>
                <div class="flex flex-col gap-2">
                    <label class="text-sm font-semibold" for="ic_number">IC Number</label>
                    <input class="form-input rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-[#101922] h-12 px-4 text-base" id="ic_number" name="ic_number" type="text" value="<?= old('ic_number') ?>" placeholder="Numbers only, no dashes" required/>
                </div>
                <div class="flex flex-col gap-2">
                    <label class="text-sm font-semibold" for="contact_no">Contact Number</label>
                    <input class="form-input rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-[#101922] h-12 px-4 text-base" id="contact_no" name="contact_no" type="text" value="<?= old('contact_no') ?>" placeholder="Numbers only, no dashes" required/>
                </div>
            </div>

            <label class="flex items-start gap-3 cursor-pointer">
                <input class="w-4 h-4 mt-0.5 rounded border-slate-300 text-primary focus:ring-primary/20" type="checkbox" name="agree_terms" value="1" required/>
                <span class="text-sm text-slate-600 dark:text-slate-300">I agree to the Terms &amp; Conditions and confirm the details above are correct.</span>
            </label>

            <button type="submit" class="flex w-full items-center justify-center rounded-lg bg-primary h-14 px-4 text-base font-bold text-white shadow-md shadow-primary/20 hover:bg-blue-600 transition-all">
                Register
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-slate-500 dark:text-slate-400">
            Already have an account? <a class="font-semibold text-primary hover:underline" href="<?= base_url('login') ?>">Back to Login</a>
        </p>
    </div>
</div>

<script>
    const baseUrl = '<?= rtrim(base_url(), '/') ?>';
    const csrfToken = '<?= csrf_hash() ?>';
    const csrfName = '<?= csrf_token() ?>';
    const linkClient = <?= ! empty($clientLink) ? 'true' : 'false' ?>;
    const previousClientId = '<?= esc((string) old('client_id'), 'js') ?>';

    const msg = document.getElementById('searchMsg');
    const clientWrap = document.getElementById('clientWrap');
    const clientSelect = document.getElementById('client_id');

    function showMsg(text, ok) {
        msg.textContent = text;
        msg.className = 'mb-6 p-4 rounded-lg text-sm ' + (ok
            ? 'bg-green-50 text-green-800 dark:bg-green-900/20 dark:text-green-200'
            : 'bg-red-50 text-red-800 dark:bg-red-900/20 dark:text-red-200');
        msg.classList.remove('hidden');
    }

    function clearCompany() {
        document.getElementById('company_name').value = '';
        document.getElementById('company_pass_name').value = '';
        clientWrap.classList.add('hidden');
        clientSelect.innerHTML = '<option value="">Select client</option>';
    }

    function fillIfEmpty(id, value) {
        const el = document.getElementById(id);
        if (el && value && el.value.trim() === '') el.value = value;
    }

    function searchCompany(quiet) {
        const ssmNo = document.getElementById('ssm_no').value.trim();
        if (!ssmNo) {
            if (!quiet) showMsg('Please enter your company SSM No first.', false);
            return;
        }

        const body = new URLSearchParams({ ssm_no: ssmNo });
        body.set(csrfName, csrfToken);

        fetch(`${baseUrl}/register/search-company`, { method: 'POST', body })
            .then(r => r.json())
            .then(res => {
                if (!res.success) {
                    clearCompany();
                    if (!quiet) showMsg(res.message || 'Company not found.', false);
                    return;
                }

                document.getElementById('company_name').value = res.name;
                document.getElementById('company_pass_name').value = res.pass_name;

                // Company details already on file are filled in for the applicant.
                fillIfEmpty('email', res.email);
                fillIfEmpty('contact_no', res.contact_no);

                if (linkClient) {
                    if (!quiet) showMsg('Company found: ' + res.name, true);
                    return;
                }

                clientSelect.innerHTML = '<option value="">Select client</option>';
                (res.clients || []).forEach(c => {
                    const opt = document.createElement('option');
                    opt.value = c.id;
                    opt.textContent = c.name;
                    clientSelect.appendChild(opt);
                });
                if ((res.clients || []).length === 1) {
                    clientSelect.value = res.clients[0].id;
                } else if (previousClientId) {
                    clientSelect.value = previousClientId;
                }
                clientWrap.classList.remove('hidden');

                if (!quiet) showMsg('Company found: ' + res.name, true);
            })
            .catch(() => {
                if (!quiet) showMsg('Network error while searching. Please try again.', false);
            });
    }

    document.getElementById('searchCompanyBtn').addEventListener('click', () => searchCompany(false));

    // Coming back from a failed submit: refill the company + client choices.
    if (document.getElementById('ssm_no').value.trim() !== '') {
        searchCompany(true);
    }
</script>
</body>
</html>
