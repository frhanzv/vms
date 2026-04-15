<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visitor Details Report - <?= esc($visitor['full_name']) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script id="tailwind-config">
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        "sans": ["Montserrat", "sans-serif"]
                    }
                }
            }
        }
    </script>
    <style type="text/css" media="print">
        @page { size: portrait; margin: 1cm; }
        body { -webkit-print-color-adjust: exact; }
    </style>
    <style>
        body { font-family: 'Montserrat', sans-serif; background-color: white; color: #1e293b; }
        .info-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 0.375rem;
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
            font-weight: 500;
            color: #334155;
            min-height: 2.5rem;
            display: flex;
            align-items: center;
        }
        .label {
            font-size: 0.75rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 0.25rem;
            display: block;
        }
    </style>
</head>
<body class="p-8 max-w-4xl mx-auto" onload="window.print()">
    <!-- Header -->
    <div class="text-center mb-6">
        <h1 class="text-2xl font-extrabold text-slate-900 mb-1">Visitor Details Report</h1>
        <p class="text-sm text-slate-500 font-medium">Generated on: <?= esc($generated_at) ?></p>
    </div>

    <hr class="border-slate-900 border-t-2 mb-8">

    <!-- Content Grid -->
    <div class="grid grid-cols-2 gap-x-8 gap-y-6">
        <!-- Left Column -->
        <div class="space-y-6">
            <div>
                <span class="label">Full Name:</span>
                <div class="info-box uppercase"><?= esc($visitor['full_name']) ?></div>
            </div>
            <div>
                <span class="label">IC No:</span>
                <div class="info-box"><?= esc($visitor['ic_passport']) ?></div>
            </div>
            <div>
                <span class="label">Person Visited:</span>
                <div class="info-box uppercase"><?= esc($visitor['invited_by']) ?></div>
            </div>
            <div>
                <span class="label">Visit From:</span>
                <div class="info-box">
                    <?= !empty($visitor['visit_from']) ? date('l, F j, Y \a\t g:i:s A', strtotime($visitor['visit_from'])) : 'N/A' ?> 
                    <?php if(!empty($visitor['visit_from'])): ?><span class="text-xs text-slate-400 ml-1 font-normal">(From device_access_logs)</span><?php endif; ?>
                </div>
            </div>
            <div>
                <span class="label">Search Type:</span>
                <div class="info-box italic">Auto Detect</div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="space-y-6">
            <div>
                <span class="label">Reason for Visit:</span>
                <div class="info-box uppercase"><?= esc($visitor['reason']) ?: 'N/A' ?></div>
            </div>
            <div>
                <span class="label">Staff No:</span>
                <div class="info-box"><?= esc($visitor['staff_id']) ?: 'N/A' ?></div>
            </div>
            <div>
                <span class="label">Contact No:</span>
                <div class="info-box"><?= esc($visitor['contact']) ?></div>
            </div>
            <div>
                <span class="label">Visit To:</span>
                <div class="info-box">
                    <?= !empty($visitor['visit_to']) ? date('l, F j, Y \a\t g:i:s A', strtotime($visitor['visit_to'])) : 'N/A' ?>
                    <?php if(!empty($visitor['visit_to'])): ?><span class="text-xs text-slate-400 ml-1 font-normal">(From device_access_logs)</span><?php endif; ?>
                </div>
            </div>
            <div>
                <span class="label">Last Updated:</span>
                <div class="info-box"><?= date('n/j/Y, g:i:s A', strtotime($visitor['updated_at'])) ?></div>
            </div>
        </div>
    </div>

    <!-- Full Width Section (Duration) -->
    <div class="mt-8">
        <span class="label">Visit Duration:</span>
        <div class="info-box bg-blue-50/50 border-blue-100">
            <span class="text-blue-600 font-bold"><?= esc($visit_duration) ?> <?= esc($status_text) ?></span>
        </div>
    </div>

    <!-- Company Name -->
    <div class="mt-6">
        <span class="label">Company Name:</span>
        <div class="info-box bg-blue-50/50 border-blue-100 uppercase"><?= esc($visitor['company']) ?: 'N/A' ?></div>
    </div>

    <!-- Footer -->
    <div class="mt-16 pt-8 border-t border-slate-100 text-center">
        <p class="text-[10px] text-slate-400 font-medium mb-1 italic">This is a computer-generated document. No signature required.</p>
        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-tight">© <?= date('Y') ?> Visitor Management System</p>
    </div>

    <!-- Watermark / Microtext as in Image 1 bottom left -->
    <div class="fixed bottom-4 left-4 text-[8px] text-slate-300 font-bold uppercase tracking-tighter opacity-20">
        SafeG VMS
    </div>
</body>
</html>
