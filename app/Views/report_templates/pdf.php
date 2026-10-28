<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 9px; color: #1f2937; }
        h1 { font-size: 15px; margin: 0 0 2px; }
        .meta { color: #6b7280; margin-bottom: 10px; }
        h2 { font-size: 11px; color: #137fec; text-transform: uppercase; margin: 14px 0 4px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #f3f4f6; text-align: left; text-transform: uppercase; font-size: 8px; }
        th, td { border: 1px solid #e5e7eb; padding: 3px 4px; }
        .empty { color: #6b7280; text-align: center; }
    </style>
</head>
<body>
    <h1><?= esc($tpl['name']) ?></h1>
    <div class="meta">
        <?= ($from || $to) ? 'Period: ' . esc($from ?: '...') . ' to ' . esc($to ?: '...') . ' &middot; ' : '' ?>Generated on <?= date('d M Y, g:i A') ?>
        <?= ! empty($tpl['description']) ? '<br>' . esc($tpl['description']) : '' ?>
    </div>
    <?php foreach ($data as $s): ?>
        <h2><?= esc($s['label']) ?> (<?= $s['total'] ?? count($s['rows']) ?>)</h2>
        <?php if (($s['total'] ?? 0) > count($s['rows'])): ?><div class="meta">Showing the first <?= $limit ?> of <?= $s['total'] ?> records - the Excel file has all of them.</div><?php endif; ?>
        <table>
            <tr><th>No</th><?php foreach ($s['columns'] as $l): ?><th><?= esc($l) ?></th><?php endforeach; ?></tr>
            <?php if (! $s['rows']): ?><tr><td colspan="<?= count($s['columns']) + 1 ?>" class="empty">No records found for this period.</td></tr><?php endif; ?>
            <?php foreach ($s['rows'] as $i => $r): ?>
                <tr><td><?= $i + 1 ?></td><?php foreach ($r as $v): ?><td><?= esc((string) $v) ?></td><?php endforeach; ?></tr>
            <?php endforeach; ?>
        </table>
    <?php endforeach; ?>
</body>
</html>
