<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <title><?= esc($pageTitle) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { theme: { extend: { colors: { primary: "#137fec" }, fontFamily: { sans: ["Montserrat","sans-serif"] } } } };</script>
    <style>
        @media print {
            body * { visibility: hidden; }
            #print-section, #print-section * { visibility: visible; }
            #print-section { position: absolute; left: 0; top: 0; margin: 0; padding: 0; }
            .card-page { page-break-after: always; }
            .card-page:last-child { page-break-after: auto; }
        }
        .card-page {
            width: 54mm;
            height: 85.6mm;
            position: relative;
            overflow: hidden;
            border-radius: 3mm;
            color: #fff;
            font-family: 'Montserrat', sans-serif;
        }
        .card-page.type-permanent { background: linear-gradient(160deg, #0f3d91 0%, #137fec 55%, #1e2a5e 100%); }
        .card-page.type-temporary { background: linear-gradient(160deg, #92400e 0%, #f59e0b 55%, #78350f 100%); }
    </style>
</head>
<body class="bg-gray-50 dark:bg-gray-900 font-sans text-gray-800 dark:text-gray-200 h-screen flex">
    <?= view('partials/sidebar') ?>
    <main class="flex-1 overflow-y-auto p-4 md:p-8">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mx-auto max-w-6xl">
            <div class="flex flex-wrap items-center justify-between gap-3 mb-2">
                <div>
                    <h1 class="text-xl font-bold uppercase">Vendor Printing List</h1>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Generates a card serial number and prints the physical pass — same two-step action as KPK's printing list.</p>
                </div>
                <button id="printSelectedBtn" onclick="printSelected()" disabled
                    class="h-9 px-4 rounded-lg bg-primary text-white text-sm font-semibold disabled:opacity-40 disabled:cursor-not-allowed flex items-center gap-1.5">
                    <span class="material-icons text-base">print</span>
                    Print Selected (<span id="selectedCount">0</span>)
                </button>
            </div>

            <form method="get" class="mb-4 mt-4 flex flex-wrap gap-2 items-center">
                <input name="search" value="<?= esc($searchTerm ?? '') ?>" class="border border-gray-300 dark:border-gray-600 dark:bg-gray-900 rounded px-3 py-2 text-sm w-72" placeholder="IC / Passport / Full Name / App No / Company / Receipt No"/>
                <select name="card_type" class="border border-gray-300 dark:border-gray-600 dark:bg-gray-900 rounded px-3 py-2 text-sm" onchange="this.form.submit()">
                    <option value="" <?= ($cardType ?? '') === '' ? 'selected' : '' ?>>All Card Types</option>
                    <option value="unassigned" <?= ($cardType ?? '') === 'unassigned' ? 'selected' : '' ?>>Not Assigned</option>
                    <option value="Permanent" <?= ($cardType ?? '') === 'Permanent' ? 'selected' : '' ?>>Permanent</option>
                    <option value="Temporary" <?= ($cardType ?? '') === 'Temporary' ? 'selected' : '' ?>>Temporary</option>
                </select>
                <select name="sort_by" class="border border-gray-300 dark:border-gray-600 dark:bg-gray-900 rounded px-3 py-2 text-sm" onchange="this.form.submit()">
                    <option value="date_desc" <?= ($sortBy ?? '') === 'date_desc' ? 'selected' : '' ?>>Date (Newest First)</option>
                    <option value="date_asc" <?= ($sortBy ?? '') === 'date_asc' ? 'selected' : '' ?>>Date (Oldest First)</option>
                    <option value="name_asc" <?= ($sortBy ?? '') === 'name_asc' ? 'selected' : '' ?>>Full Name (A-Z)</option>
                    <option value="name_desc" <?= ($sortBy ?? '') === 'name_desc' ? 'selected' : '' ?>>Full Name (Z-A)</option>
                    <option value="company_asc" <?= ($sortBy ?? '') === 'company_asc' ? 'selected' : '' ?>>Company (A-Z)</option>
                    <option value="company_desc" <?= ($sortBy ?? '') === 'company_desc' ? 'selected' : '' ?>>Company (Z-A)</option>
                </select>
                <button type="submit" class="h-9 px-4 rounded-lg border border-gray-300 dark:border-gray-600 text-sm font-semibold">Search</button>
            </form>

            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700 font-bold uppercase">
                        <th class="p-3 border-b w-8">
                            <input type="checkbox" id="selectAll" onchange="toggleSelectAll(this.checked)"/>
                        </th>
                        <th class="p-3 border-b">No</th>
                        <th class="p-3 border-b">Photo</th>
                        <th class="p-3 border-b">Full Name</th>
                        <th class="p-3 border-b">Vendor Company</th>
                        <th class="p-3 border-b">IC/Passport</th>
                        <th class="p-3 border-b">Card Type</th>
                        <th class="p-3 border-b">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($list)): ?>
                    <tr><td colspan="8" class="p-6 text-center text-gray-500">No approved passes waiting to be printed.</td></tr>
                    <?php else: foreach ($list as $row): ?>
                    <tr class="border-b border-gray-100 dark:border-gray-700"
                        data-id="<?= $row['id'] ?>"
                        data-name="<?= esc($row['full_name'], 'attr') ?>"
                        data-ic="<?= esc($row['ic_passport_masked'], 'attr') ?>"
                        data-company="<?= esc($row['vendor_company_name'], 'attr') ?>"
                        data-cardtype="<?= esc($row['card_type'], 'attr') ?>">
                        <td class="p-3">
                            <?php if ($canPrint ?? false): ?>
                            <input type="checkbox" class="row-check" value="<?= $row['id'] ?>" onchange="onRowCheck()"/>
                            <?php endif; ?>
                        </td>
                        <td class="p-3"><?= $row['no'] ?></td>
                        <td class="p-3">
                            <?php if ($row['photo_url']): ?>
                                <img src="<?= esc($row['photo_url']) ?>" class="w-8 h-8 rounded object-cover border border-gray-200 dark:border-gray-600" alt=""/>
                            <?php else: ?>
                                <span class="w-8 h-8 rounded bg-gray-200 dark:bg-gray-600 flex items-center justify-center text-[10px] text-gray-500">N/A</span>
                            <?php endif; ?>
                        </td>
                        <td class="p-3 font-semibold"><?= esc($row['full_name']) ?></td>
                        <td class="p-3"><?= esc($row['vendor_company_name']) ?></td>
                        <td class="p-3"><?= esc($row['ic_passport_masked']) ?></td>
                        <td class="p-3"><?= esc($row['card_type']) ?></td>
                        <td class="p-3">
                            <div class="flex items-center gap-2">
                                <a href="<?= base_url('vendors/process-list/detail/view/' . $row['id']) ?>" class="text-primary hover:underline text-xs font-semibold">View Details</a>
                                <?php if ($canPrint ?? false): ?>
                                <span class="text-gray-300">|</span>
                                <button onclick="printOne(<?= $row['id'] ?>)" class="text-primary hover:underline text-xs font-semibold flex items-center gap-1">
                                    <span class="material-icons text-sm">print</span>Print
                                </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>

            <?php if (($pagination['last_page'] ?? 1) > 1): ?>
            <div class="flex justify-end gap-1 mt-4">
                <?php for ($i = 1; $i <= $pagination['last_page']; $i++):
                    $qs = array_merge($_GET, ['page' => $i]);
                ?>
                <a href="?<?= http_build_query($qs) ?>" class="w-8 h-8 flex items-center justify-center border border-gray-300 dark:border-gray-600 rounded text-xs <?= $i === $pagination['current_page'] ? 'bg-primary text-white' : 'hover:bg-gray-50 dark:hover:bg-gray-800' ?>"><?= $i ?></a>
                <?php endfor; ?>
            </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- Hidden print area: filled just before window.print() -->
    <div id="print-section"></div>

    <script>
        function cardMarkup(card) {
            const typeClass = String(card.card_type).toLowerCase() === 'permanent' ? 'type-permanent' : 'type-temporary';
            return `
                <div class="card-page ${typeClass}">
                    <div style="padding:4mm; display:flex; flex-direction:column; align-items:center; height:100%; box-sizing:border-box;">
                        <div style="font-size:2.6mm; letter-spacing:0.5mm; opacity:.85; margin-bottom:2mm;">SAFEG VENDOR PASS</div>
                        <img src="${card.photo_url}" style="width:22mm; height:22mm; object-fit:cover; border-radius:2mm; border:0.5mm solid rgba(255,255,255,.8);" />
                        <div style="margin-top:3mm; font-size:3.6mm; font-weight:700; text-align:center;">${card.full_name}</div>
                        <div style="font-size:2.8mm; opacity:.9; margin-top:1mm; font-family:'Courier New',monospace;">${card.ic_no}</div>
                        <div style="font-size:2.4mm; opacity:.85; margin-top:1mm; text-align:center;">${card.company}</div>
                        <div style="margin-top:auto; width:100%; text-align:center; font-size:2.6mm; font-weight:700; text-transform:uppercase; letter-spacing:0.3mm;">${card.card_type}</div>
                        <div style="font-size:2.4mm; font-family:'Courier New',monospace; margin-top:0.5mm;">Valid Until: ${card.valid_until}</div>
                        <div style="font-size:2.2mm; opacity:.75; margin-top:0.5mm; font-family:'Courier New',monospace;">${card.receipt_no}</div>
                    </div>
                </div>` + cardBackMarkup(card);
        }

        /*
         * Back of the card — matches KPK's real print job, which prints
         * every card as TWO pages: front (photo) + back (terms &
         * conditions and a signature line). We don't have KPK's actual
         * back-of-card artwork or legal wording (proprietary/government
         * text), so this is an original layout at the same size with
         * placeholder terms — replace TERMS_TEXT below with your
         * company's actual wording.
         */
        const TERMS_TEXT = [
            "This pass remains the property of the issuing company and must be returned upon request or on expiry.",
            "This pass must be worn visibly at all times while on the premises.",
            "Loss of this pass must be reported to security immediately.",
            "This pass is not transferable and may only be used by the person named on it.",
        ];

        function cardBackMarkup(card) {
            const typeClass = String(card.card_type).toLowerCase() === 'permanent' ? 'type-permanent' : 'type-temporary';
            const terms = TERMS_TEXT.map((t, i) => `<li style="margin-bottom:1.5mm;">${i + 1}. ${t}</li>`).join('');
            return `
                <div class="card-page ${typeClass}" style="background:#fff; color:#1f2937; border:0.3mm solid #d1d5db;">
                    <div style="padding:4mm; display:flex; flex-direction:column; height:100%; box-sizing:border-box;">
                        <div style="font-size:2.6mm; font-weight:700; text-align:center; margin-bottom:2mm; text-transform:uppercase;">Terms &amp; Conditions</div>
                        <ol style="list-style:none; padding:0; margin:0; font-size:2.2mm; line-height:1.3;">
                            ${terms}
                        </ol>
                        <div style="margin-top:auto; text-align:center; font-size:2.2mm;">
                            <div style="font-size:2mm; opacity:.7;">${card.company}</div>
                            <div style="border-top:0.2mm solid #9ca3af; width:80%; margin:6mm auto 1mm;"></div>
                            <div style="font-size:2mm;">Authorized Signature</div>
                        </div>
                    </div>
                </div>`;
        }

        function csrfHeaders() {
            return { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '<?= csrf_hash() ?>' };
        }

        function generateSerial(id) {
            return fetch('<?= base_url('vendors/printing-list/generate-serial/') ?>' + id, {
                method: 'POST',
                headers: csrfHeaders(),
            }).then(r => {
                if (!r.ok) throw new Error('http_' + r.status);
                return r.json();
            });
        }

        function printOne(id) {
            generateSerial(id).then(d => {
                if (!d.success) { alert(d.message); return; }
                document.getElementById('print-section').innerHTML = cardMarkup(d.card);
                setTimeout(() => { window.print(); location.reload(); }, 150);
            }).catch(() => alert('Could not reach the server. Please check your connection and try again.'));
        }

        function onRowCheck() {
            const boxes = document.querySelectorAll('.row-check');
            const checked = document.querySelectorAll('.row-check:checked');
            document.getElementById('selectAll').checked = boxes.length > 0 && checked.length === boxes.length;
            document.getElementById('selectedCount').textContent = checked.length;
            document.getElementById('printSelectedBtn').disabled = checked.length === 0;
        }

        function toggleSelectAll(checked) {
            document.querySelectorAll('.row-check').forEach(cb => cb.checked = checked);
            onRowCheck();
        }

        function printSelected() {
            const ids = Array.from(document.querySelectorAll('.row-check:checked')).map(cb => cb.value);
            if (ids.length === 0) return;

            const cards = [];
            (function next(i) {
                if (i >= ids.length) {
                    document.getElementById('print-section').innerHTML = cards.map(cardMarkup).join('');
                    setTimeout(() => { window.print(); location.reload(); }, 150);
                    return;
                }
                generateSerial(ids[i]).then(d => {
                    if (d.success) cards.push(d.card);
                    next(i + 1);
                }).catch(() => next(i + 1));
            })(0);
        }
    </script>
</body>
</html>
