<?php
require_once __DIR__ . '/../app/Config/config.php';
require_login();

$finance = new Finance($database);
$dealerModel = new Dealer($database);
$dealers = $dealerModel->all();
$message = null;

if (is_post()) {
    $finance->addPayment([
        'dealer_id' => (int)$_POST['dealer_id'],
        'order_id' => $_POST['order_id'] !== '' ? (int)$_POST['order_id'] : null,
        'amount' => (int)$_POST['amount'],
        'method' => trim($_POST['method']),
        'paid_at' => $_POST['paid_at'] ?: date('Y-m-d H:i:s'),
        'user_id' => $_SESSION['user']['id'],
    ]);
    $message = "To'lov saqlandi.";
}

$payments = $finance->latestPayments();

include __DIR__ . '/../app/Views/layout/header.php';
?>
<h1 class="text-2xl font-semibold mb-4">To'lovlar</h1>
<?php if ($message): ?>
<div class="bg-emerald-100 text-emerald-700 px-4 py-2 rounded mb-4"><?= $message; ?></div>
<?php endif; ?>
<div class="grid md:grid-cols-2 gap-6">
    <form method="post" class="bg-white rounded shadow p-4 space-y-3">
        <h2 class="font-semibold">To'lov qo'shish</h2>
        <select name="dealer_id" class="w-full border rounded px-3 py-2" required>
            <option value="">Diler</option>
            <?php foreach ($dealers as $dealer): ?>
                <option value="<?= $dealer['id']; ?>"><?= htmlspecialchars($dealer['name']); ?></option>
            <?php endforeach; ?>
        </select>
        <input type="text" name="order_id" placeholder="Buyurtma ID (ixtiyoriy)" class="w-full border rounded px-3 py-2">
        <input type="number" name="amount" placeholder="Summa" class="w-full border rounded px-3 py-2" required>
        <select name="method" class="w-full border rounded px-3 py-2">
            <option value="naqd">Naqd</option>
            <option value="bank">Bank</option>
            <option value="click">Click</option>
        </select>
        <input type="datetime-local" name="paid_at" class="w-full border rounded px-3 py-2">
        <button class="bg-emerald-600 text-white px-4 py-2 rounded">Saqlash</button>
    </form>
    <div class="bg-white rounded shadow p-4">
        <h2 class="font-semibold mb-3">Oxirgi to'lovlar</h2>
        <ul class="space-y-2 text-sm">
            <?php foreach ($payments as $payment): ?>
            <li class="border-b pb-2">
                <p class="font-semibold"><?= htmlspecialchars($payment['dealer_name']); ?> - <?= format_currency((int)$payment['amount']); ?></p>
                <p class="text-xs text-gray-500"><?= $payment['method']; ?> | <?= date('d.m.Y H:i', strtotime($payment['paid_at'])); ?></p>
            </li>
            <?php endforeach; ?>
            <?php if (!$payments): ?>
            <li class="text-gray-500">To'lovlar yo'q</li>
            <?php endif; ?>
        </ul>
    </div>
</div>
<?php include __DIR__ . '/../app/Views/layout/footer.php'; ?>
