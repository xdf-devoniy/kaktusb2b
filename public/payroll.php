<?php
require_once __DIR__ . '/../app/Config/config.php';
require_login();

$payroll = new Payroll($database);
$userModel = new User($database);
$users = $userModel->all();
$date = $_GET['date'] ?? date('Y-m-d');
$message = null;

if (is_post()) {
    $type = $_POST['type'];
    $quantity = (float)($_POST['quantity'] ?? 0);
    $rate = (int)($_POST['rate'] ?? 0);
    $sum = (int)($quantity * $rate);
    if ($type === 'pechat') {
        $payroll->addPechat([
            'user_id' => (int)$_POST['user_id'],
            'order_id' => $_POST['order_id'] !== '' ? (int)$_POST['order_id'] : null,
            'order_item_id' => null,
            'date' => $_POST['date'],
            'quantity_m2' => $quantity,
            'rate_per_m2' => $rate,
            'sum_earned' => $sum,
        ]);
        $message = "Pechat logi qo'shildi";
    } else {
        $payroll->addGarfun([
            'user_id' => (int)$_POST['user_id'],
            'order_id' => $_POST['order_id'] !== '' ? (int)$_POST['order_id'] : null,
            'order_item_id' => null,
            'date' => $_POST['date'],
            'meters_used' => $quantity,
            'rate_per_meter' => $rate,
            'sum_earned' => $sum,
        ]);
        $message = "Garfun logi qo'shildi";
    }
}

$pechatLogs = $payroll->pechatLogs($date);
$garfunLogs = $payroll->garfunLogs($date);

include __DIR__ . '/../app/Views/layout/header.php';
?>
<h1 class="text-2xl font-semibold mb-4">Ish haqi</h1>
<?php if ($message): ?>
<div class="bg-emerald-100 text-emerald-700 px-4 py-2 rounded mb-4"><?= $message; ?></div>
<?php endif; ?>
<form method="get" class="mb-4">
    <label class="text-sm text-gray-600">Sana:</label>
    <input type="date" name="date" value="<?= htmlspecialchars($date); ?>" class="border rounded px-3 py-2">
    <button class="bg-slate-800 text-white px-4 py-2 rounded">Ko'rsatish</button>
</form>
<div class="grid md:grid-cols-2 gap-6">
    <div class="bg-white rounded shadow p-4">
        <h2 class="font-semibold mb-3">Pechat</h2>
        <form method="post" class="space-y-2 mb-4">
            <input type="hidden" name="type" value="pechat">
            <input type="hidden" name="date" value="<?= htmlspecialchars($date); ?>">
            <select name="user_id" class="w-full border rounded px-3 py-2" required>
                <option value="">Xodim</option>
                <?php foreach ($users as $user): ?>
                <option value="<?= $user['id']; ?>"><?= htmlspecialchars($user['name']); ?></option>
                <?php endforeach; ?>
            </select>
            <input type="text" name="order_id" placeholder="Buyurtma ID" class="w-full border rounded px-3 py-2">
            <div class="flex gap-2">
                <input type="number" step="0.01" name="quantity" placeholder="m²" class="border rounded px-3 py-2 w-1/2" required>
                <input type="number" name="rate" placeholder="Tarif" class="border rounded px-3 py-2 w-1/2" required>
            </div>
            <button class="bg-emerald-600 text-white px-4 py-2 rounded">Qo'shish</button>
        </form>
        <ul class="space-y-2 text-sm">
            <?php foreach ($pechatLogs as $log): ?>
            <li class="border-b pb-2">
                <p class="font-semibold"><?= htmlspecialchars($log['worker_name']); ?> - <?= $log['quantity_m2']; ?> m²</p>
                <p class="text-xs text-gray-500"><?= format_currency((int)$log['sum_earned']); ?></p>
            </li>
            <?php endforeach; ?>
            <?php if (!$pechatLogs): ?>
            <li class="text-gray-500">Ma'lumot yo'q</li>
            <?php endif; ?>
        </ul>
    </div>
    <div class="bg-white rounded shadow p-4">
        <h2 class="font-semibold mb-3">Garfun</h2>
        <form method="post" class="space-y-2 mb-4">
            <input type="hidden" name="type" value="garfun">
            <input type="hidden" name="date" value="<?= htmlspecialchars($date); ?>">
            <select name="user_id" class="w-full border rounded px-3 py-2" required>
                <option value="">Xodim</option>
                <?php foreach ($users as $user): ?>
                <option value="<?= $user['id']; ?>"><?= htmlspecialchars($user['name']); ?></option>
                <?php endforeach; ?>
            </select>
            <input type="text" name="order_id" placeholder="Buyurtma ID" class="w-full border rounded px-3 py-2">
            <div class="flex gap-2">
                <input type="number" step="0.01" name="quantity" placeholder="metr" class="border rounded px-3 py-2 w-1/2" required>
                <input type="number" name="rate" placeholder="Tarif" class="border rounded px-3 py-2 w-1/2" required>
            </div>
            <button class="bg-emerald-600 text-white px-4 py-2 rounded">Qo'shish</button>
        </form>
        <ul class="space-y-2 text-sm">
            <?php foreach ($garfunLogs as $log): ?>
            <li class="border-b pb-2">
                <p class="font-semibold"><?= htmlspecialchars($log['worker_name']); ?> - <?= $log['meters_used']; ?> m</p>
                <p class="text-xs text-gray-500"><?= format_currency((int)$log['sum_earned']); ?></p>
            </li>
            <?php endforeach; ?>
            <?php if (!$garfunLogs): ?>
            <li class="text-gray-500">Ma'lumot yo'q</li>
            <?php endif; ?>
        </ul>
    </div>
</div>
<?php include __DIR__ . '/../app/Views/layout/footer.php'; ?>
