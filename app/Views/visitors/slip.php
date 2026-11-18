<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="utf-8">
    <title>Slip Pas Pelawat - <?= esc($fullName) ?></title>
    <style>
        /* KPK VisitorPassSlip.vm: A4 slip, printed from the Visitor Pass List */
        @page { size: A4; margin: 15mm; }
        body { font-family: Arial, sans-serif; font-size: 12px; color: #000; margin: 0; }
        .slip { max-width: 180mm; margin: 0 auto; }
        h1 { text-align: center; font-size: 15px; margin: 0 0 12px; }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 4px 6px; vertical-align: top; }
        .lbl { width: 22%; font-weight: bold; white-space: nowrap; }
        .sep { width: 2%; }
        .box { border: 1px solid #000; }
        .head td { font-weight: bold; text-align: center; border: 1px solid #000; }
        .pass { text-align: center; font-weight: bold; font-size: 14px; padding: 10px 0 2px; }
        .note { text-align: center; font-size: 11px; font-style: italic; margin-bottom: 10px; }
        .sign td { padding-top: 40px; text-align: center; }
        ol { padding-left: 18px; margin: 6px 0 0; }
        .toolbar { text-align: center; margin: 16px 0; }
        @media print { .toolbar { display: none; } }
    </style>
</head>
<body>
<div class="toolbar"><button onclick="window.print()">Print</button></div>
<div class="slip">
    <h1>PERMOHONAN MEMASUKI PREMIS (<?= esc($location) ?>)</h1>

    <table class="box">
        <tr class="head"><td colspan="3">MASUK</td><td colspan="3">KELUAR</td></tr>
        <tr>
            <td class="lbl">TARIKH</td><td class="sep">:</td><td><?= $in ? esc(date('d/m/Y', strtotime((string) $in))) : '' ?></td>
            <td class="lbl">TARIKH</td><td class="sep">:</td><td><?= $out ? esc(date('d/m/Y', strtotime((string) $out))) : '' ?></td>
        </tr>
        <tr>
            <td class="lbl">MASA</td><td class="sep">:</td><td><?= $in ? esc(date('H:i', strtotime((string) $in))) : '' ?></td>
            <td class="lbl">MASA</td><td class="sep">:</td><td><?= $out ? esc(date('H:i', strtotime((string) $out))) : '' ?></td>
        </tr>
    </table>

    <div class="pass">NO PAS PELAWAT: <?= esc($passNo) ?></div>
    <div class="note">(Sila pastikan PAS PELAWAT dipamerkan di bahu/dada sebelah kiri)</div>

    <table>
        <tr><td class="lbl">NO APPLICATION</td><td class="sep">:</td><td colspan="4"><?= esc($appNo) ?></td></tr>
        <tr><td class="lbl">NAMA PENUH</td><td class="sep">:</td><td colspan="4"><?= esc($fullName) ?></td></tr>
        <tr>
            <td class="lbl">NO K/P BARU</td><td class="sep">:</td><td><?= esc($icNo) ?></td>
            <td class="lbl">NO PASSPORT</td><td class="sep">:</td><td><?= esc($passport) ?></td>
        </tr>
        <tr>
            <td class="lbl">TUJUAN MASUK</td><td class="sep">:</td><td><?= esc($reason) ?></td>
            <td class="lbl">LOKASI</td><td class="sep">:</td><td><?= esc($place) ?></td>
        </tr>
        <tr>
            <td class="lbl">NO KENDERAAN</td><td class="sep">:</td><td><?= esc($vehicleNo) ?></td>
            <td class="lbl">JENIS KENDERAAN</td><td class="sep">:</td><td><?= esc($vehicle) ?></td>
        </tr>
    </table>

    <table class="sign">
        <tr><td>_______________________<br>TANDATANGAN PEMOHON</td><td>___________________________________________<br>TANDATANGAN DAN K/P ORANG YANG DITEMUI</td></tr>
    </table>

    <p style="margin-top:24px"><strong>PERINGATAN:</strong></p>
    <ol>
        <li>Surat kebenaran ini adalah merupakan surat rasmi yang dikeluarkan mengikut seksyen 5(1) Akta Kawasan Larangan Dan Tempat Larangan 1959.</li>
        <li>Slip dan pas pelawat ini tidak dibenarkan berpindahmilik, ia hendaklah dipulangkan ketika keluar dari premis.</li>
        <li>Pengguna hendaklah sentiasa mematuhi peraturan dan tanda-tanda lalulintas di dalam premis.</li>
    </ol>
</div>
<script>window.addEventListener('load', () => window.print());</script>
</body>
</html>
