/*
 * Vendor pass card printing (front + back) - same method as KPK.
 *
 *   VendorCardPrint.print([card, ...], done)
 *
 * The card artwork is a pre-made image (public/assets/images/template/);
 * only the person's details are printed on top of it, at the same millimetre
 * positions KPK uses. Each card is two pages of exactly 54 x 85.6 mm (CR-80):
 * front, then back. Only the cards are printed, never the page you are on.
 *
 * `card` is what vendors/printing-list/generate-serial returns.
 */
(function (global) {
    'use strict';

    // .../assets/js/vendor-card-print.js  ->  .../assets/images/template/
    var script = document.currentScript;
    var IMG = (script && script.src ? script.src.replace(/js\/vendor-card-print\.js.*$/, '') : '/assets/') + 'images/template/';

    function esc(s) {
        return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }

    var NAME_FONT = "Calibri,Carlito,Arial,sans-serif";
    var MONO_FONT = "'Courier New',Courier,monospace";

    function abs(top, height, inner) {
        return '<div style="height:' + height + 'mm;margin-top:' + top + 'mm;position:absolute;width:100%;text-align:center;">' + inner + '</div>';
    }
    function span(size, color, font, text, extra) {
        return '<span class="pasDetails" style="font-size:' + size + 'mm;color:' + color + ';font-family:' + font + ';font-weight:bold;' + (extra || '') + '">' + text + '</span>';
    }

    // layout per card kind; numbers are KPK's (process-list print markup)
    var LAYOUT = {
        permanent: { bg: 'vendor_permanent.jpg', photoPad: 8, photoH: 36, photoImg: 'max-height:35mm;', name: 56, id: 63, until: 66, untilInner: 0 },
        temporary: { bg: 'vendor_temporary.jpg', photoPad: 8, photoH: 36, photoImg: 'height:35mm;',     name: 56, id: 62, until: 66, untilInner: 0 }
    };

    function kindOf(card) {
        var t = String(card.card_type || '').toLowerCase();
        if (t === 'permanent') return 'permanent';
        if (t.indexOf('family') === 0) return 'family';
        return 'temporary';
    }

    function bgImg(file) {
        return '<img src="' + IMG + file + '" width="219" height="351" style="position:absolute;pointer-events:none;object-fit:scale-down;margin-left:-2mm;margin-top:-2.2mm;margin-right:-3.5mm;margin-bottom:-4mm;">';
    }

    function front(card) {
        var kind = kindOf(card);
        var name = esc(card.display_name || card.full_name);
        var ic = esc(card.ic_display || card.ic_no);
        var until = 'Sah Sehingga : ' + esc(card.valid_until_upper || card.valid_until);
        var h = '<div class="wrap"><div class="fix">';

        if (kind === 'family') {
            h += bgImg('family_type.png') + '<div style="height:15mm;position:relative;"></div>';
            h += abs(49, 3.5, span(4, '#000', NAME_FONT, name));
            h += '<div style="height:4mm;position:absolute;width:100%;text-align:center;"><div style="height:5mm;margin-top:66mm;">' +
                 span(3, '#000', MONO_FONT, until, 'position:relative;top:-1mm;') + '</div></div>';
        } else {
            var L = LAYOUT[kind];
            h += bgImg(L.bg) + '<div style="height:15mm;position:relative;"></div>';
            h += '<div style="height:' + L.photoH + 'mm;padding-top:' + L.photoPad + 'mm;position:absolute;width:100%;text-align:center;">' +
                 '<img src="' + esc(card.photo_url) + '" style="max-width:95%;' + L.photoImg + 'object-fit:contain;display:block;margin:auto;"/></div>';
            h += abs(L.name, 3.5, span(4, '#fff', NAME_FONT, name));
            h += abs(L.id, 4, span(3, '#fff', MONO_FONT, ic));
            if (L.untilInner) {
                h += '<div style="height:4mm;margin-top:' + L.until + 'mm;position:absolute;width:100%;text-align:center;"><div style="height:5mm;margin-top:' + L.untilInner + 'mm;">' +
                     span(3, '#000', MONO_FONT, until, 'position:relative;top:-1mm;') + '</div></div>';
            } else {
                h += abs(L.until, 4, span(3, '#000', MONO_FONT, until));
            }
        }
        return h + '</div></div>';
    }

    function back(card) {
        var h = '<div class="wrap"><div class="fix">';
        h += '<img src="' + IMG + 'template_back.jpg" style="width:100%;height:100%;position:absolute;">';
        if (card.company) {
            h += abs(64, 3.5, span(2.5, '#000', NAME_FONT, esc(card.company)));
        }
        h += '<img src="' + IMG + 'sign.png" style="width:65%;height:8mm;left:8%;margin-top:68mm;position:absolute;text-align:center;">';
        return h + '</div></div>';
    }

    function documentHtml(cards) {
        var body = cards.map(function (c) { return front(c) + back(c); }).join('');
        return '<!doctype html><html><head><meta charset="utf-8"><title>Vendor Pass</title><style>' +
            '@page{size:54mm 85.6mm;margin:0;}' +
            '*{box-sizing:border-box;-webkit-print-color-adjust:exact;print-color-adjust:exact;}' +
            'html,body{margin:0;padding:0;}' +
            '.wrap{width:54mm;height:85.6mm;position:relative;margin:0;padding:0;overflow:hidden;page-break-after:always;break-after:page;}' +
            '.wrap:last-child{page-break-after:auto;break-after:auto;}' +
            '.fix{padding:0;}' +
            '.pasDetails{font-weight:750;text-shadow:0.2px 0 black;}' +
            '</style></head><body>' + body + '</body></html>';
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

        // wait for the artwork and photos, but never hang on a broken one
        var imgs = Array.prototype.slice.call(doc.images);
        var pending = imgs.length;
        if (!pending) { setTimeout(run, 100); return; }
        function one() { pending--; if (pending <= 0) setTimeout(run, 100); }
        imgs.forEach(function (im) {
            if (im.complete) { one(); } else { im.onload = one; im.onerror = one; }
        });
        setTimeout(run, 5000);
    }

    global.VendorCardPrint = { print: print };
})(window);
