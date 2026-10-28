<?php foreach (['success' => 'bg-green-50 text-green-700 border-green-200', 'error' => 'bg-red-50 text-red-700 border-red-200'] as $k => $cls): ?>
    <?php if (session()->getFlashdata($k)): ?>
        <div class="px-4 py-3 rounded border text-sm <?= $cls ?>"><?= esc(session()->getFlashdata($k)) ?></div>
    <?php endif; ?>
<?php endforeach; ?>
