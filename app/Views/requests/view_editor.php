<?php if ($canEditRequestView): ?>
<div class="px-6 pt-4 flex justify-end items-center gap-3">
    <?php if ($requestViewClients !== []): ?>
    <label class="text-sm" for="requestViewClient">Client</label>
    <select id="requestViewClient" class="border rounded-lg px-3 py-2 text-sm dark:bg-slate-900" onchange="window.location.href='<?= base_url('requests') ?>?view_client_id='+encodeURIComponent(this.value)">
        <?php foreach ($requestViewClients as $client): ?>
        <option value="<?= (int)$client['id'] ?>" <?= (int)$client['id'] === $requestViewClientId ? 'selected' : '' ?>><?= esc($client['name']) ?></option>
        <?php endforeach; ?>
    </select>
    <?php endif; ?>
    <button type="button" id="editRequestView" class="px-4 py-2 border rounded-lg bg-white dark:bg-slate-900 text-sm font-semibold">Edit View</button>
</div>
<dialog id="requestViewEditor" class="rounded-xl p-6 w-full max-w-md dark:bg-slate-900 dark:text-white shadow-xl backdrop:bg-black/40">
    <form id="requestViewForm">
        <h2 class="text-lg font-bold mb-2">Edit Request Queue View</h2>
        <p class="text-sm text-gray-500 mb-4">Choose what everyone sees for <?= esc($requestViewClientName) ?>.</p>
        <div class="space-y-3">
        <?php foreach (\App\Services\RequestViewService::SECTIONS as $key => $label): ?>
        <label class="flex items-center gap-3"><input type="checkbox" name="<?= esc($key) ?>" <?= $requestViewSettings[$key] ? 'checked' : '' ?>><?= esc($label) ?></label>
        <?php endforeach; ?>
        </div>
        <p id="requestViewMessage" role="status" class="text-sm text-red-600 mt-4"></p>
        <div class="flex justify-end gap-3 mt-5">
            <button type="button" id="cancelRequestView" class="px-4 py-2 border rounded-lg">Cancel</button>
            <button type="submit" id="saveRequestView" class="px-4 py-2 bg-primary text-white rounded-lg">Save View</button>
        </div>
    </form>
</dialog>
<script>
{
    const editor = document.getElementById('requestViewEditor');
    const form = document.getElementById('requestViewForm');
    document.getElementById('editRequestView').addEventListener('click', () => {
        form.reset();
        document.getElementById('requestViewMessage').textContent = '';
        editor.showModal();
    });
    document.getElementById('cancelRequestView').addEventListener('click', () => editor.close());
    form.addEventListener('submit', async event => {
        event.preventDefault();
        const button = document.getElementById('saveRequestView');
        button.disabled = true;
        const sections = {};
        form.querySelectorAll('input[type=checkbox]').forEach(input => sections[input.name] = input.checked);
        try {
            const response = await fetch('<?= base_url('requests/view-settings') ?>', {
                method: 'POST', headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({client_id: <?= (int)$requestViewClientId ?>, sections})
            });
            const result = await response.json();
            if (!response.ok || !result.success) throw new Error(result.message || 'Unable to save view.');
            window.location.reload();
        } catch (error) {
            document.getElementById('requestViewMessage').textContent = error.message;
            button.disabled = false;
        }
    });
}
</script>
<?php endif; ?>
<style>
<?php if (!array_filter(array_intersect_key($requestViewSettings, array_flip(['card_pending','card_flagged','card_expected','card_rejected'])))): ?>
[data-request-section="summary"] { display: none !important; }
<?php endif; ?>
<?php foreach ($requestViewSettings as $key => $visible): if (!$visible): ?>
[data-request-section="<?= esc($key) ?>"] { display: none !important; }
<?php endif; endforeach; ?>
<?php if (!$requestViewSettings['watchlist'] || !$requestViewSettings['identity']): ?>
[data-request-review-grid] { grid-template-columns: minmax(0,1fr) !important; }
[data-request-review-grid] > div { grid-column: auto !important; }
<?php endif; ?>
<?php if (!$requestViewSettings['watchlist'] && !$requestViewSettings['identity']): ?>
[data-request-review-grid] { display: none !important; }
<?php endif; ?>
</style>
