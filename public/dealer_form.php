<?php
require_once __DIR__ . '/../app/Config/config.php';
require_login();

$dealerModel = new Dealer($database);
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;
$dealer = $id ? $dealerModel->find($id) : null;

if ($id && !$dealer) {
    redirect('dealers.php');
}

if (is_post()) {
    $data = [
        'id' => $id,
        'name' => trim($_POST['name']),
        'phone' => trim($_POST['phone']),
        'region' => trim($_POST['region']),
        'telegram_name' => trim($_POST['telegram_name']),
        'discount_percent' => (float)($_POST['discount_percent'] ?? 0),
        'credit_limit' => (int)($_POST['credit_limit'] ?? 0),
        'status' => $_POST['status'],
        'notes' => trim($_POST['notes']),
    ];
    $dealerModel->save($data);
    redirect('dealers.php');
}

include __DIR__ . '/../app/Views/layout/header.php';
?>
<div class="max-w-3xl mx-auto bg-white rounded shadow p-6">
    <h1 class="text-2xl font-semibold mb-4"><?= $id ? 'Dilerni tahrirlash' : 'Yangi diler'; ?></h1>
    <form method="post" class="grid md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium">Nomi</label>
            <input type="text" name="name" value="<?= htmlspecialchars($dealer['name'] ?? ''); ?>" required class="w-full border rounded px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium">Telefon</label>
            <input type="text" name="phone" value="<?= htmlspecialchars($dealer['phone'] ?? ''); ?>" class="w-full border rounded px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium">Hudud</label>
            <input type="text" name="region" value="<?= htmlspecialchars($dealer['region'] ?? ''); ?>" class="w-full border rounded px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium">Telegram</label>
            <input type="text" name="telegram_name" value="<?= htmlspecialchars($dealer['telegram_name'] ?? ''); ?>" class="w-full border rounded px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium">Chegirma %</label>
            <input type="number" step="0.1" name="discount_percent" value="<?= htmlspecialchars($dealer['discount_percent'] ?? 0); ?>" class="w-full border rounded px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium">Kredit limiti</label>
            <input type="number" name="credit_limit" value="<?= htmlspecialchars($dealer['credit_limit'] ?? 0); ?>" class="w-full border rounded px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium">Status</label>
            <select name="status" class="w-full border rounded px-3 py-2">
                <?php $status = $dealer['status'] ?? 'active'; ?>
                <option value="active" <?= $status === 'active' ? 'selected' : ''; ?>>Faol</option>
                <option value="disabled" <?= $status === 'disabled' ? 'selected' : ''; ?>>O'chirilgan</option>
                <option value="vip" <?= $status === 'vip' ? 'selected' : ''; ?>>VIP</option>
            </select>
        </div>
        <div class="md:col-span-2">
            <label class="block text-sm font-medium">Izoh</label>
            <textarea name="notes" class="w-full border rounded px-3 py-2" rows="3"><?= htmlspecialchars($dealer['notes'] ?? ''); ?></textarea>
        </div>
        <div class="md:col-span-2 flex justify-end">
            <a href="dealers.php" class="px-4 py-2 border rounded mr-2">Bekor qilish</a>
            <button class="bg-emerald-600 text-white px-4 py-2 rounded">Saqlash</button>
        </div>
    </form>
</div>
<?php include __DIR__ . '/../app/Views/layout/footer.php'; ?>
