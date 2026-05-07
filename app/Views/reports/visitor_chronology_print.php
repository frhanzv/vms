<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visitor Chronology Report - <?= esc($data['summary']['full_name']) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
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
        .page-break-before { page-break-before: always; }
        .avoid-page-break { page-break-inside: avoid; }
    </style>
    <style>
        body { font-family: 'Montserrat', sans-serif; background-color: white; color: #1e293b; }
        .section-title { color: #3b82f6; font-size: 1.125rem; font-weight: 700; margin-bottom: 0.75rem; margin-top: 1.5rem; }
        .info-box { border: 1px solid #e2e8f0; border-radius: 0.375rem; padding: 1rem; margin-bottom: 0.5rem; background-color: #f8fafc; }
        .label { font-size: 0.75rem; font-weight: 800; color: #0f172a; margin-bottom: 0.25rem; display: block; text-transform: uppercase; }
        .value { font-size: 0.875rem; font-weight: 600; color: #334155; }
        
        .timeline-line { border-left: 2px solid #3b82f6; position: absolute; left: 6px; top: 8px; bottom: -16px; }
        .timeline-dot { width: 14px; height: 14px; border-radius: 50%; background-color: #3b82f6; position: absolute; left: 0; top: 4px; box-shadow: 0 0 0 4px white; }
    </style>
</head>
<body class="p-8 max-w-4xl mx-auto" onload="window.print()">
    <!-- Header -->
    <div class="text-center mb-6">
        <h1 class="text-2xl font-bold text-slate-800 mb-1">Visitor Chronology Report</h1>
        <p class="text-xs text-slate-500 font-medium">Generated on: <?= esc($generated_at) ?></p>
    </div>

    <hr class="border-slate-800 border-t-2 mb-6">

    <!-- Visitor Information -->
    <div class="section-title">Visitor Information</div>
    <div class="info-box flex flex-col gap-4">
        <div>
            <span class="label">Full Name:</span>
            <div class="value uppercase"><?= esc($data['summary']['full_name']) ?></div>
        </div>
        <div>
            <span class="label">IC Number:</span>
            <div class="value uppercase"><?= esc($data['summary']['ic_no']) ?></div>
        </div>
    </div>

    <!-- Summary -->
    <div class="section-title">Summary</div>
    <div class="grid grid-cols-1 gap-2 mb-2">
        <div class="info-box text-center py-4">
            <span class="label text-slate-400">CURRENT STATUS</span>
            <div class="mt-2">
                <?php if (strtoupper($data['summary']['status']) === 'CHECKED OUT'): ?>
                    <span class="bg-red-100 text-red-600 px-3 py-1 rounded text-xs font-bold uppercase">OUT OF BUILDING</span>
                <?php else: ?>
                    <span class="bg-emerald-100 text-emerald-600 px-3 py-1 rounded text-xs font-bold uppercase">IN BUILDING</span>
                <?php endif; ?>
            </div>
        </div>
        <div class="info-box text-center py-4">
            <span class="label text-slate-400">TOTAL TIME SPENT</span>
            <div class="text-lg font-bold text-slate-800 mt-1"><?= esc($data['summary']['total_time']) ?></div>
        </div>
        <div class="info-box text-center py-4">
            <span class="label text-slate-400">TOTAL VISITS</span>
            <div class="text-lg font-bold text-slate-800 mt-1"><?= esc($data['summary']['total_visits']) ?></div>
        </div>
        <div class="info-box text-center py-4">
            <span class="label text-slate-400">TOTAL SCANS</span>
            <div class="text-lg font-bold text-slate-800 mt-1"><?= esc($data['summary']['total_scans']) ?></div>
        </div>
    </div>

    <!-- Visit Dates -->
    <?php if (!empty($data['dates'])): ?>
    <div class="section-title mt-6">Visit Dates</div>
    <div class="flex flex-wrap gap-2 mb-6">
        <?php foreach ($data['dates'] as $d): ?>
            <div class="border border-blue-400 bg-blue-50 text-blue-600 px-4 py-2 rounded-full text-xs font-bold w-full">
                <?= esc($d['display_date']) ?>
            </div>
        <?php endforeach; ?>
    </div>
    
    <!-- Complete Movement Timeline -->
    <div class="section-title mt-6">Complete Movement Timeline</div>
    <div class="ml-2 mt-4 pb-8">
        <?php foreach ($data['dates'] as $d): ?>
            <?php foreach ($d['movements'] as $idx => $m): ?>
                <div class="relative pl-6 mb-6 avoid-page-break">
                    <div class="timeline-line" <?= ($idx === count($d['movements']) - 1) ? 'style="border-left: 2px dashed #cbd5e1;"' : '' ?>></div>
                    <div class="timeline-dot"></div>
                    
                    <div class="text-xs font-bold text-slate-500 mb-2">
                        Movement <?= esc($m['movement_index']) ?> | <?= esc($d['display_date']) ?> <?= esc($m['entry_time']) ?>
                    </div>
                    
                    <div class="info-box p-4 grid grid-cols-2 gap-4">
                        <div>
                            <span class="label text-slate-800">From Location:</span>
                            <div class="value text-xs font-bold uppercase"><?= esc($m['from']) ?></div>
                        </div>
                        <div>
                            <span class="label text-slate-800">To Location:</span>
                            <div class="value text-xs font-bold uppercase"><?= esc($m['to']) ?></div>
                        </div>
                        <div>
                            <span class="label text-slate-800">Time Spent:</span>
                            <div class="value text-xs font-medium"><?= esc($m['time_spent']) ?></div>
                        </div>
                        <div>
                            <span class="label text-slate-800">Status:</span>
                            <div class="mt-1">
                                <?php if (strtoupper($m['status']) === 'GRANTED'): ?>
                                    <span class="bg-emerald-100 text-emerald-600 px-2 py-0.5 rounded text-[10px] font-bold uppercase">GRANTED</span>
                                <?php else: ?>
                                    <span class="bg-red-100 text-red-600 px-2 py-0.5 rounded text-[10px] font-bold uppercase"><?= esc($m['status']) ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div>
                            <span class="label text-slate-800">Entry Time:</span>
                            <div class="value text-xs font-medium"><?= esc($m['entry_time']) ?></div>
                        </div>
                        <div>
                            <span class="label text-slate-800">Exit Time:</span>
                            <div class="value text-xs font-medium"><?= esc($m['exit_time']) ?></div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- Footer -->
    <div class="mt-8 pt-6 border-t border-slate-200 text-center">
        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-tight">© <?= date('Y') ?> Visitor Management System | Page 1 of 1</p>
    </div>
</body>
</html>
