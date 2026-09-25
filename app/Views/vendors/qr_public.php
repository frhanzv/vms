<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title><?= esc($pageTitle) ?></title>
    <link rel="icon" type="image/png" href="<?= base_url('assets/images/vms-icon.png') ?>"/>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <script>
        tailwind.config = { theme: { extend: { colors: { primary: "#137fec" }, fontFamily: { sans: ["Montserrat", "sans-serif"] } } } };
    </script>
</head>
<body class="bg-gray-100 dark:bg-gray-950 font-sans text-gray-800 min-h-screen flex items-center justify-center p-4">

<?php if (!$vendor): ?>

    <div class="max-w-sm w-full bg-white rounded-2xl shadow-lg p-8 text-center">
        <div class="size-14 rounded-full bg-red-50 flex items-center justify-center text-red-500 mx-auto mb-4">
            <span class="material-symbols-outlined text-3xl">error</span>
        </div>
        <h1 class="text-lg font-bold text-gray-800 mb-1">Pass Not Found</h1>
        <p class="text-sm text-gray-500">This QR code doesn't match any vendor pass on record.</p>
    </div>

<?php else:
    $today = date('Y-m-d');
    $passValidity = 'Not Issued';
    $bannerClass  = 'from-gray-500 to-gray-600';
    if (!empty($vendor['pass_expiry'])) {
        $daysLeft = (strtotime($vendor['pass_expiry']) - strtotime($today)) / 86400;
        if ($vendor['status'] !== 'Approved') {
            $passValidity = 'Not Valid';
            $bannerClass  = 'from-red-500 to-red-600';
        } elseif ($daysLeft < 0) {
            $passValidity = 'Expired';
            $bannerClass  = 'from-red-500 to-red-600';
        } else {
            $passValidity = 'Valid';
            $bannerClass  = 'from-emerald-500 to-emerald-600';
        }
    }
?>

    <div class="max-w-sm w-full bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r <?= $bannerClass ?> px-6 py-5 text-white text-center">
            <span class="material-symbols-outlined text-3xl mb-1">badge</span>
            <p class="font-black text-xl tracking-wide"><?= esc($passValidity) ?></p>
            <p class="text-xs opacity-90 mt-0.5">Vendor Pass Verification</p>
        </div>

        <div class="p-6 space-y-4">
            <div class="text-center pb-4 border-b border-gray-100">
                <p class="text-xl font-bold text-gray-800"><?= esc($vendor['full_name'] ?? 'N/A') ?></p>
                <p class="text-sm text-gray-500"><?= esc($vendor['designation'] ?? '') ?></p>
            </div>

            <dl class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <dt class="text-gray-400">App No</dt>
                    <dd class="font-medium text-gray-700"><?= esc($vendor['app_no'] ?? 'N/A') ?></dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-400">Vendor Company</dt>
                    <dd class="font-medium text-gray-700 text-right"><?= esc($vendor['vendor_company_name'] ?? 'N/A') ?></dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-400">IC / Passport</dt>
                    <dd class="font-medium text-gray-700"><?= esc(mask_ic_passport($vendor['ic_no'] ?: ($vendor['passport_no'] ?? ''), 'N/A')) ?></dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-400">Visiting</dt>
                    <dd class="font-medium text-gray-700 text-right"><?= esc($vendor['name_of_person_visited'] ?? 'N/A') ?></dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-400">Location</dt>
                    <dd class="font-medium text-gray-700 text-right"><?= esc($vendor['location_visited'] ?? 'N/A') ?></dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-400">Pass Expiry</dt>
                    <dd class="font-medium text-gray-700"><?= $vendor['pass_expiry'] ? esc(date('d M Y', strtotime($vendor['pass_expiry']))) : '-' ?></dd>
                </div>
            </dl>
        </div>

        <div class="bg-gray-50 px-6 py-3 text-center">
            <p class="text-[11px] text-gray-400">Verified against SafeG records at <?= date('d M Y, g:i A') ?></p>
        </div>
    </div>

<?php endif; ?>

</body>
</html>
