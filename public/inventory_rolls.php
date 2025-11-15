<?php
require_once __DIR__ . '/../app/Config/config.php';
require_login();

$stock = new Stock($database);
$rolls = $stock->rolls();

include __DIR__ . '/../app/Views/layout/header.php';
?>
<h1 class="text-2xl font-semibold mb-4">PVC rullar ombori</h1>
<div class="bg-white rounded shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-slate-100">
            <tr>
                <th class="px-4 py-2">Material</th>
                <th>Kenglik</th>
                <th>Qoldiq</th>
                <th>Holat</th>
                <th>Joy</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rolls as $roll): ?>
            <tr class="border-t">
                <td class="px-4 py-2"><?= htmlspecialchars($roll['material_code']); ?></td>
                <td><?= $roll['roll_width_m']; ?> m</td>
                <td><?= $roll['length_remaining_m']; ?> / <?= $roll['length_total_m']; ?> m</td>
                <td><?= htmlspecialchars($roll['status']); ?></td>
                <td><?= htmlspecialchars($roll['location']); ?></td>
            </tr>
            <?php endforeach; ?>
            <?php if (!$rolls): ?>
            <tr><td colspan="5" class="text-center text-gray-500 py-4">Ma'lumot yo'q</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php include __DIR__ . '/../app/Views/layout/footer.php'; ?>
