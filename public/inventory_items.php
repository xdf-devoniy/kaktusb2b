<?php
require_once __DIR__ . '/../app/Config/config.php';
require_login();

$stock = new Stock($database);
$items = $stock->items();

include __DIR__ . '/../app/Views/layout/header.php';
?>
<h1 class="text-2xl font-semibold mb-4">Aksessuarlar</h1>
<div class="bg-white rounded shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-slate-100">
            <tr>
                <th class="px-4 py-2">Nomi</th>
                <th>Tur</th>
                <th>Qoldiq</th>
                <th>Joy</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
            <tr class="border-t">
                <td class="px-4 py-2"><?= htmlspecialchars($item['name']); ?></td>
                <td><?= htmlspecialchars($item['type']); ?></td>
                <td><?= $item['qty_remaining']; ?> <?= htmlspecialchars($item['unit']); ?></td>
                <td><?= htmlspecialchars($item['location']); ?></td>
            </tr>
            <?php endforeach; ?>
            <?php if (!$items): ?>
            <tr><td colspan="4" class="text-center text-gray-500 py-4">Ma'lumot yo'q</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php include __DIR__ . '/../app/Views/layout/footer.php'; ?>
