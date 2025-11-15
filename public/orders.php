<?php
require_once __DIR__ . '/../app/Config/config.php';
require_login();

$orderModel = new Order($database);
$dealerModel = new Dealer($database);
$dealers = $dealerModel->all();
$filters = [
    'dealer_id' => $_GET['dealer_id'] ?? null,
    'status' => $_GET['status'] ?? null,
    'from_date' => $_GET['from_date'] ?? null,
    'to_date' => $_GET['to_date'] ?? null,
];
$orders = $orderModel->all($filters);

include __DIR__ . '/../app/Views/layout/header.php';
?>
<div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-semibold">Buyurtmalar</h1>
    <a href="order_form.php" class="bg-emerald-600 text-white px-4 py-2 rounded">+ Buyurtma</a>
</div>
<form method="get" class="bg-white rounded shadow p-4 mb-4 grid md:grid-cols-4 gap-3">
    <select name="dealer_id" class="border rounded px-3 py-2">
        <option value="">Diler</option>
        <?php foreach ($dealers as $dealer): ?>
            <option value="<?= $dealer['id']; ?>" <?= ($filters['dealer_id'] ?? '') == $dealer['id'] ? 'selected' : ''; ?>><?= htmlspecialchars($dealer['name']); ?></option>
        <?php endforeach; ?>
    </select>
    <select name="status" class="border rounded px-3 py-2">
        <option value="">Status</option>
        <?php $statuses = ['draft','confirmed','in_production','ready_for_pack','ready_for_ship','shipped','closed']; ?>
        <?php foreach ($statuses as $status): ?>
            <option value="<?= $status; ?>" <?= ($filters['status'] ?? '') === $status ? 'selected' : ''; ?>><?= $status; ?></option>
        <?php endforeach; ?>
    </select>
    <input type="date" name="from_date" value="<?= htmlspecialchars($filters['from_date'] ?? ''); ?>" class="border rounded px-3 py-2">
    <input type="date" name="to_date" value="<?= htmlspecialchars($filters['to_date'] ?? ''); ?>" class="border rounded px-3 py-2">
    <button class="bg-slate-800 text-white rounded px-4 py-2">Filter</button>
</form>
<div class="bg-white rounded shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-slate-100 text-left">
            <tr>
                <th class="px-4 py-2">№</th>
                <th>Diler</th>
                <th>Status</th>
                <th>m²</th>
                <th>Summa</th>
                <th>Sana</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $order): ?>
            <tr class="border-t">
                <td class="px-4 py-2 font-semibold"><?= htmlspecialchars($order['global_number']); ?></td>
                <td><?= htmlspecialchars($order['dealer_name']); ?></td>
                <td><?= htmlspecialchars($order['status']); ?></td>
                <td><?= $order['total_m2']; ?></td>
                <td><?= format_currency((int)$order['total_price']); ?></td>
                <td><?= date('d.m.Y', strtotime($order['created_at'])); ?></td>
                <td class="text-right px-4">
                    <a href="order_view.php?id=<?= $order['id']; ?>" class="text-blue-600">Ko'rish</a>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (!$orders): ?>
            <tr><td colspan="7" class="text-center text-gray-500 py-4">Buyurtmalar yo'q</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php include __DIR__ . '/../app/Views/layout/footer.php'; ?>
