<?php
/**
 * Renew action for a closed vendor pass (VendorRenewal::rowState): a Renew link, a "renewing" note,
 * or the date renewal opens. $state, $id.
 */
if (empty($state)) {
    return;
}
?>
<?php if ($state[0] === 'open'): ?>
    <span class="text-gray-300">|</span>
    <a href="<?= base_url('vendors/renew/' . (int) $id) ?>" class="inline-flex items-center gap-0.5 text-xs font-semibold text-emerald-600 hover:underline" title="Renew this vendor pass">
        <span class="material-symbols-outlined text-[16px]">autorenew</span>Renew
    </a>
<?php elseif ($state[0] === 'pending'): ?>
    <span class="text-gray-300">|</span>
    <span class="inline-flex items-center gap-0.5 rounded-full bg-amber-100 px-2 py-0.5 text-[11px] font-semibold text-amber-700" title="Renewal in progress — the current card still works">
        <span class="material-symbols-outlined text-[14px]">autorenew</span>Renewing
    </span>
<?php elseif ($state[0] === 'opens'): ?>
    <span class="text-gray-300">|</span>
    <span class="text-[11px] text-gray-400" title="Renewal opens on this date">Renew from <?= esc(date('d/m/Y', strtotime($state[1]))) ?></span>
<?php endif; ?>
