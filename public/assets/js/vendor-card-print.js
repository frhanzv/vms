/*
 * Vendor pass card printing (front + back), shared by Process Detail,
 * Printing List and Card Info.
 *
 *   VendorCardPrint.print([card, ...], done)
 *
 * Prints ONLY the cards - never the page you are on: every card is built in a
 * hidden frame and printed from there. Each card is two sheets, front then
 * back, with the card in the top-left corner (same as the old KPK print job).
 * `card` is the object returned by vendors/printing-list/generate-serial.
 */
(function (global) {
    'use strict';

    var THEMES = {
        temporary: { a: '#f59e0b', b: '#fb923c', c: '#fde047', bar: '#8a6a4b' },
        permanent: { a: '#137fec', b: '#38bdf8', c: '#a5f3fc', bar: '#1e3a8a' },
        family:    { a: '#10b981', b: '#34d399', c: '#bef264', bar: '#166534' }
    };

    function esc(s) {
        return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }

    function themeOf(card) {
        var t = String(card.card_type || '').toLowerCase();
        if (t === 'permanent') return THEMES.permanent;
        if (t.indexOf('family') === 0) return THEMES.family;
        return THEMES.temporary;
    }

    function waves(th, id) {
        return '<svg viewBox="0 0 54 85.6" preserveAspectRatio="none" style="position:absolute;left:0;top:0;width:54mm;height:85.6mm;">' +
            '<defs><linearGradient id="g' + id + '" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="' + th.c + '"/><stop offset=".5" stop-color="' + th.b + '"/><stop offset="1" stop-color="' + th.a + '"/></linearGradient></defs>' +
            '<rect width="54" height="85.6" fill="#fff"/>' +
            '<path d="M0 0H54V16C40 22 24 8 0 20Z" fill="url(#g' + id + ')"/>' +
            '<path d="M0 59C14 52 30 66 54 56V85.6H0Z" fill="url(#g' + id + ')"/>' +
            '<path d="M0 62C16 56 32 69 54 60" fill="none" stroke="#fff" stroke-width=".8" opacity=".7"/>' +
            '</svg>';
    }

    function front(card, i) {
        var th = themeOf(card);
        return '<div class="card">' + waves(th, 'f' + i) +
            '<div class="inner" style="align-items:center;text-align:center;">' +
              '<div style="margin-top:2.2mm;font-size:5.2mm;font-weight:900;letter-spacing:.3mm;color:#0f172a;">SAFE<span style="color:' + th.bar + ';">G</span></div>' +
              '<div style="font-size:1.7mm;font-weight:700;letter-spacing:.2mm;color:#0f172a;text-transform:uppercase;max-width:46mm;">' + esc(card.issuer || '') + '</div>' +
              '<img class="photo" src="' + esc(card.photo_url) + '" style="margin-top:3.2mm;width:21mm;height:27mm;object-fit:cover;background:#fff;border:.3mm solid #e5e7eb;"/>' +
              '<div style="margin-top:2mm;font-size:3.3mm;font-weight:800;color:#111827;letter-spacing:.2mm;">' + esc(card.type_label || card.card_type) + '</div>' +
              '<div style="margin-top:1mm;min-width:20mm;max-width:46mm;padding:.6mm 2mm;background:' + th.bar + ';color:#fff;font-size:2.1mm;font-weight:800;letter-spacing:.1mm;text-transform:uppercase;">' + esc(card.access_code || '-') + '</div>' +
              '<div style="margin-top:auto;margin-bottom:10mm;width:100%;">' +
                '<div style="font-size:3mm;font-weight:800;color:#111827;text-transform:uppercase;line-height:1.15;">' + esc(card.full_name) + '</div>' +
                '<div style="margin-top:.8mm;font-size:2.5mm;font-weight:700;font-family:\'Courier New\',monospace;color:#111827;">' + esc(card.ic_display || card.ic_no) + '</div>' +
                '<div style="margin-top:.8mm;font-size:2.3mm;font-weight:700;font-family:\'Courier New\',monospace;color:#111827;">Sah Sehingga : ' + esc(card.valid_until_upper || card.valid_until) + '</div>' +
              '</div>' +
            '</div></div>';
    }

    function back(card, i) {
        var th = themeOf(card);
        var issuer = esc(card.issuer || 'pihak pengurusan');
        var terms = [
            'Paskad ini adalah surat rasmi yang dikeluarkan oleh ' + issuer + '.',
            'Paskad ini hendaklah sentiasa dipamerkan sepanjang masa berada dalam tempat/kawasan yang berkenaan dan hendaklah ditunjukkan jikalau dikehendaki oleh pegawai yang berkuasa memeriksanya.',
            'Paskad ini hendaklah dikembalikan kepada pejabat yang mengeluarkannya apabila pemegangnya telah berhenti daripada jawatan atau pekerjaan yang menyebabkan paskad ini dikeluarkan.',
            'Kehilangan paskad ini mestilah dilaporkan dengan segera kepada pejabat yang mengeluarkannya.',
            'Sesiapa yang menjumpai paskad ini hendaklah menyerahkannya kepada ' + issuer + '.'
        ];
        var li = terms.map(function (t, n) { return '<li style="display:flex;gap:1mm;margin-bottom:1.2mm;"><span>' + (n + 1) + '.</span><span>' + t + '</span></li>'; }).join('');
        return '<div class="card" style="background:#fff;">' +
            '<div class="inner">' +
              '<ol style="list-style:none;margin:2mm 0 0;padding:0;font-size:2mm;line-height:1.25;text-align:justify;color:#111827;">' + li + '</ol>' +
              '<div style="margin-top:auto;margin-bottom:9mm;text-align:center;">' +
                '<div style="font-size:4.2mm;font-weight:800;font-family:\'Courier New\',monospace;color:#111827;">' + esc(card.receipt_no) + '</div>' +
                '<div style="display:inline-block;margin-top:.6mm;min-width:16mm;padding:.5mm 2mm;background:' + th.bar + ';color:#fff;font-size:2.4mm;font-weight:800;text-transform:uppercase;">' + esc(card.access_code || '-') + '</div>' +
                '<div style="border-top:.2mm dashed #6b7280;width:85%;margin:6mm auto 1mm;"></div>' +
                '<div style="font-size:1.9mm;font-weight:700;text-transform:uppercase;">Pegawai Yang Dibenarkan</div>' +
              '</div>' +
            '</div></div>';
    }

    function documentHtml(cards) {
        var sheets = cards.map(function (c, i) {
            return '<div class="sheet">' + front(c, i) + '</div><div class="sheet">' + back(c, i) + '</div>';
        }).join('');
        return '<!doctype html><html><head><meta charset="utf-8"><title>Vendor Pass</title><style>' +
            '@page{size:A4 portrait;margin:8mm;}' +
            '*{box-sizing:border-box;-webkit-print-color-adjust:exact;print-color-adjust:exact;}' +
            'html,body{margin:0;padding:0;font-family:Arial,Helvetica,sans-serif;}' +
            '.sheet{page-break-after:always;break-after:page;width:54mm;height:85.6mm;}' +
            '.sheet:last-child{page-break-after:auto;break-after:auto;}' +
            '.card{position:relative;width:54mm;height:85.6mm;overflow:hidden;border:.2mm solid #d1d5db;}' +
            '.inner{position:absolute;left:0;top:0;width:54mm;height:85.6mm;padding:3mm;display:flex;flex-direction:column;}' +
            '</style></head><body>' + sheets + '</body></html>';
    }

    function print(cards, done) {
        cards = (cards || []).filter(Boolean);
        if (!cards.length) { if (done) done(); return; }

        var frame = document.createElement('iframe');
        frame.setAttribute('aria-hidden', 'true');
        frame.style.cssText = 'position:fixed;right:0;bottom:0;width:0;height:0;border:0;visibility:hidden;';
        document.body.appendChild(frame);

        var doc = frame.contentWindow.document;
        doc.open();
        doc.write(documentHtml(cards));
        doc.close();

        var finished = false;
        function run() {
            if (finished) return;
            finished = true;
            try { frame.contentWindow.focus(); frame.contentWindow.print(); } catch (e) { /* ignore */ }
            setTimeout(function () {
                if (frame.parentNode) frame.parentNode.removeChild(frame);
                if (done) done();
            }, 300);
        }

        // wait for the photos, but never hang on a broken one
        var imgs = Array.prototype.slice.call(doc.images);
        var pending = imgs.length;
        if (!pending) { setTimeout(run, 100); return; }
        function one() { pending--; if (pending <= 0) setTimeout(run, 100); }
        imgs.forEach(function (im) {
            if (im.complete) { one(); } else { im.onload = one; im.onerror = one; }
        });
        setTimeout(run, 4000);
    }

    global.VendorCardPrint = { print: print };
})(window);
