<?php /* Staff pass card printing — shared by Printing List and the pass detail page. */ ?>
<style>
    @media screen { #print-section { position: fixed; left: -10000px; top: 0; } }
    @media print {
        body * { visibility: hidden; }
        #print-section, #print-section * { visibility: visible; }
        #print-section { position: absolute; left: 0; top: 0; margin: 0; padding: 0; }
        .card-page { page-break-after: always; }
        .card-page:last-child { page-break-after: auto; }
    }
    .card-page {
        width: 54mm; height: 85.6mm; position: relative; overflow: hidden; border-radius: 3mm;
        color: #fff; font-family: 'Montserrat', sans-serif;
        background: linear-gradient(160deg, #064e3b 0%, #059669 55%, #022c22 100%);
    }
    .card-page.card-back { background: #fff; color: #1f2937; border: 0.3mm solid #d1d5db; }
</style>
<div id="print-section"></div>
<script>
    // Back-of-card wording is a placeholder — replace with your company's text.
    const STAFF_CARD_TERMS = [
        "This pass remains the property of the company and must be returned on resignation or on request.",
        "This pass must be worn visibly at all times while on the premises.",
        "Loss of this pass must be reported to security immediately.",
        "This pass is not transferable and may only be used by the person named on it.",
    ];
    function escHtml(v) {
        return String(v ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
    }
    function staffCardMarkup(card) {
        const terms = STAFF_CARD_TERMS.map((t, i) => `<li style="margin-bottom:1.5mm;">${i + 1}. ${escHtml(t)}</li>`).join('');
        return `
            <div class="card-page">
                <div style="padding:4mm; display:flex; flex-direction:column; align-items:center; height:100%; box-sizing:border-box;">
                    <div style="font-size:2.6mm; letter-spacing:0.5mm; opacity:.85; margin-bottom:2mm;">SAFEG STAFF PASS</div>
                    <img src="${escHtml(card.photo_url)}" style="width:24mm; height:28mm; object-fit:cover; border-radius:2mm; border:0.5mm solid rgba(255,255,255,.8);" />
                    <div style="margin-top:3mm; font-size:3.6mm; font-weight:700; text-align:center;">${escHtml(card.full_name)}</div>
                    <div style="font-size:3mm; margin-top:1mm; font-weight:700; font-family:'Courier New',monospace;">${escHtml(card.staff_no)}</div>
                    <div style="font-size:2.4mm; opacity:.9; margin-top:1mm; text-align:center;">${escHtml(card.designation)}</div>
                    <div style="font-size:2.4mm; opacity:.85; text-align:center;">${escHtml(card.department)}</div>
                    <div style="margin-top:auto; font-size:2.4mm; font-family:'Courier New',monospace;">Valid Until: ${escHtml(card.valid_until)}</div>
                    <div style="font-size:2.2mm; opacity:.75; margin-top:0.5mm; font-family:'Courier New',monospace;">${escHtml(card.receipt_no)}</div>
                </div>
            </div>
            <div class="card-page card-back">
                <div style="padding:4mm; display:flex; flex-direction:column; height:100%; box-sizing:border-box;">
                    <div style="font-size:2.6mm; font-weight:700; text-align:center; margin-bottom:2mm; text-transform:uppercase;">Terms &amp; Conditions</div>
                    <ol style="list-style:none; padding:0; margin:0; font-size:2.2mm; line-height:1.3;">${terms}</ol>
                    <div style="margin-top:auto; text-align:center;">
                        <div style="border-top:0.2mm solid #9ca3af; width:80%; margin:6mm auto 1mm;"></div>
                        <div style="font-size:2mm;">Authorized Signature</div>
                    </div>
                </div>
            </div>`;
    }
    function staffGenerateSerial(id, reason) {
        return staffPost('<?= base_url('staffs/printing-list/generate-serial/') ?>' + id, { reason: reason || '' });
    }
    function staffPrintCards(cards) {
        const section = document.getElementById('print-section');
        section.innerHTML = cards.map(staffCardMarkup).join('');
        const imgs = Array.from(section.querySelectorAll('img'));
        Promise.all(imgs.map(img => img.complete ? null : new Promise(res => { img.onload = img.onerror = res; })))
            .then(() => { window.print(); location.reload(); });
    }
    function staffPrintOne(id, reason) {
        staffGenerateSerial(id, reason).then(d => {
            if (!d.success) { if (d.message) alert(d.message); return; }
            staffPrintCards([d.card]);
        });
    }
    function staffPrintMany(ids) {
        const cards = [], errors = [];
        (function next(i) {
            if (i >= ids.length) {
                if (errors.length) alert('Skipped:\n' + errors.join('\n'));
                if (cards.length) staffPrintCards(cards);
                return;
            }
            staffGenerateSerial(ids[i]).then(d => {
                if (d.success) cards.push(d.card); else if (d.message) errors.push('#' + ids[i] + ': ' + d.message);
                next(i + 1);
            });
        })(0);
    }
</script>
