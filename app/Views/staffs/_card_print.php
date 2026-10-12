<?php /* Staff pass card printing - shared by Printing List and the pass detail page.
         The card artwork is a pre-made image (public/assets/images/card-templates/staff_*.jpg);
         only the person's details are printed on it. See public/assets/js/staff-card-print.js. */ ?>
<script src="<?= base_url('assets/js/staff-card-print.js') ?>"></script>
<script>
    function staffGenerateSerial(id, reason) {
        return staffPost('<?= base_url('staffs/printing-list/generate-serial/') ?>' + id, { reason: reason || '' });
    }
    function staffPrintCards(cards) {
        StaffCardPrint.print(cards, () => location.reload());
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
