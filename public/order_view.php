<?php
require_once __DIR__ . '/../app/Config/config.php';
require_login();

$orderModel = new Order($database);
$orderId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$order = $orderModel->find($orderId);

if (!$order) {
    redirect('orders.php');
}

$items = $orderModel->items($orderId);

include __DIR__ . '/../app/Views/layout/header.php';
?>
<div class="flex items-center justify-between mb-4">
    <div>
        <h1 class="text-2xl font-semibold">Buyurtma #<?= htmlspecialchars($order['global_number']); ?></h1>
        <p class="text-sm text-gray-500">Diler: <?= htmlspecialchars($order['dealer_name']); ?> | Status: <?= htmlspecialchars($order['status']); ?></p>
    </div>
    <a href="orders.php" class="text-sm text-gray-600">&larr; Ortga</a>
</div>
<div class="bg-white rounded shadow p-4 mb-4">
    <p><strong>Yaratilgan:</strong> <?= date('d.m.Y H:i', strtotime($order['created_at'])); ?></p>
    <p><strong>Talab qilingan sana:</strong> <?= htmlspecialchars($order['required_date']); ?></p>
    <p><strong>Izoh:</strong> <?= nl2br(htmlspecialchars($order['comment'])); ?></p>
</div>
<div class="bg-white rounded shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-slate-100">
            <tr>
                <th class="px-4 py-2">Xona</th>
                <th>Material</th>
                <th>Kenglik</th>
                <th>Uzunlik</th>
                <th>m²</th>
                <th>Narx/m²</th>
                <th>Jami</th>
                <th>Chizma</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
            <tr class="border-t">
                <td class="px-4 py-2"><?= htmlspecialchars($item['name']); ?></td>
                <td><?= htmlspecialchars($item['material_code']); ?></td>
                <td><?= $item['width_m']; ?></td>
                <td><?= $item['height_m']; ?></td>
                <td><?= $item['area_m2']; ?></td>
                <td><?= format_currency((int)$item['price_per_m2']); ?></td>
                <td><?= format_currency((int)$item['total_price']); ?></td>
                <td>
                    <?php if ($item['sketch_path']): ?>
                        <a href="<?= $item['sketch_path']; ?>" class="text-blue-600" target="_blank">Ko'rish</a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<div class="bg-white rounded shadow p-4 mt-4 flex justify-between">
    <div>
        <p>Jami m²: <strong><?= $order['total_m2']; ?></strong></p>
        <p>Jami summa: <strong><?= format_currency((int)$order['total_price']); ?></strong></p>
    </div>
    <div class="space-x-2">
        <button class="px-4 py-2 border rounded">Tasdiqlash</button>
        <button class="px-4 py-2 border rounded">Prodyuksiyaga</button>
        <button class="px-4 py-2 border rounded">Hisob-faktura</button>
    </div>
</div>
<?php include __DIR__ . '/../app/Views/layout/footer.php'; ?>
