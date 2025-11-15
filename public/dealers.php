<?php
require_once __DIR__ . '/../app/Config/config.php';
require_login();

$dealerModel = new Dealer($database);
$filters = [
    'status' => $_GET['status'] ?? null,
    'search' => $_GET['search'] ?? null,
];
$dealers = $dealerModel->all($filters);

include __DIR__ . '/../app/Views/layout/header.php';
?>
<div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-semibold">Dilerlar</h1>
    <a href="dealer_form.php" class="bg-emerald-600 text-white px-4 py-2 rounded">+ Yangi diler</a>
</div>
<form method="get" class="bg-white rounded shadow p-4 mb-4 grid md:grid-cols-3 gap-4">
    <input type="text" name="search" value="<?= htmlspecialchars($filters['search'] ?? ''); ?>" placeholder="Qidiruv" class="border rounded px-3 py-2">
    <select name="status" class="border rounded px-3 py-2">
        <option value="">Status (hammasi)</option>
        <option value="active" <?= ($filters['status'] ?? '') === 'active' ? 'selected' : ''; ?>>Faol</option>
        <option value="disabled" <?= ($filters['status'] ?? '') === 'disabled' ? 'selected' : ''; ?>>O'chirilgan</option>
        <option value="vip" <?= ($filters['status'] ?? '') === 'vip' ? 'selected' : ''; ?>>VIP</option>
    </select>
    <button class="bg-slate-800 text-white rounded px-4 py-2">Filter</button>
</form>
<div class="bg-white rounded shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-slate-100 text-left">
            <tr>
                <th class="px-4 py-2">Nomi</th>
                <th>Telefon</th>
                <th>Hudud</th>
                <th>Status</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($dealers as $dealer): ?>
            <tr class="border-t">
                <td class="px-4 py-2">
                    <p class="font-semibold text-emerald-700">
                        <a href="orders.php?dealer_id=<?= $dealer['id']; ?>"><?= htmlspecialchars($dealer['name']); ?></a>
                    </p>
                    <p class="text-xs text-gray-500">Telegram: <?= htmlspecialchars($dealer['telegram_name']); ?></p>
                </td>
                <td><?= htmlspecialchars($dealer['phone']); ?></td>
                <td><?= htmlspecialchars($dealer['region']); ?></td>
                <td><span class="px-2 py-1 rounded text-xs bg-slate-100"><?= htmlspecialchars($dealer['status']); ?></span></td>
                <td class="text-right px-4">
                    <a href="dealer_form.php?id=<?= $dealer['id']; ?>" class="text-blue-600">Tahrirlash</a>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (!$dealers): ?>
            <tr>
                <td colspan="5" class="text-center text-gray-500 py-4">Diler topilmadi</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php include __DIR__ . '/../app/Views/layout/footer.php'; ?>
