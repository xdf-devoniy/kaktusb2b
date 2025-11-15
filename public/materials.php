<?php
require_once __DIR__ . '/../app/Config/config.php';
require_role(['admin', 'manager']);

$materialModel = new Material($database);
$message = null;
$error = null;
$editId = isset($_GET['edit']) ? (int)$_GET['edit'] : 0;
$materialToEdit = $editId ? $materialModel->find($editId) : null;

if (is_post()) {
    if (!empty($_POST['delete_id'])) {
        $materialModel->delete((int)$_POST['delete_id']);
        redirect('materials.php');
    }

    $payload = [
        'code' => strtoupper(trim($_POST['code'] ?? '')),
        'color' => trim($_POST['color'] ?? ''),
        'texture' => trim($_POST['texture'] ?? ''),
        'roll_width_m' => (float)($_POST['roll_width_m'] ?? 0),
        'default_price_small_m2' => (int)($_POST['default_price_small_m2'] ?? 15000),
        'default_price_large_m2' => (int)($_POST['default_price_large_m2'] ?? 25000),
        'purchase_price_per_m2' => (int)($_POST['purchase_price_per_m2'] ?? 0),
        'notes' => trim($_POST['notes'] ?? ''),
    ];

    if ($payload['code'] === '' || $payload['roll_width_m'] <= 0) {
        $error = 'Material kodi va ruli kengligi shart.';
    } else {
        $id = (int)($_POST['material_id'] ?? 0);
        if ($id > 0) {
            $materialModel->update($id, $payload);
            $message = 'Material yangilandi.';
        } else {
            $materialModel->create($payload);
            $message = 'Yangi material qo\'shildi.';
        }
        redirect('materials.php?status=success');
    }
}

if (isset($_GET['status']) && $_GET['status'] === 'success') {
    $message = 'Ma\'lumotlar saqlandi.';
}

$materials = $materialModel->all();

include __DIR__ . '/../app/Views/layout/header.php';
?>
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-semibold">Materiallar</h1>
        <p class="text-sm text-gray-500">Buyurtma narxlarini to\'g\'ri hisoblash uchun barcha materiallarni shu yerda boshqaring.</p>
    </div>
    <a href="materials.php" class="text-sm text-emerald-600">Yangi material</a>
</div>

<?php if ($message): ?>
    <div class="bg-emerald-50 text-emerald-700 px-4 py-2 rounded mb-4"><?= htmlspecialchars($message); ?></div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="bg-red-100 text-red-700 px-4 py-2 rounded mb-4"><?= htmlspecialchars($error); ?></div>
<?php endif; ?>

<div class="bg-white rounded shadow p-4 mb-6">
    <h2 class="font-semibold mb-4"><?= $materialToEdit ? 'Materialni tahrirlash' : 'Yangi material qo\'shish'; ?></h2>
    <form method="post" class="grid md:grid-cols-2 gap-4">
        <input type="hidden" name="material_id" value="<?= $materialToEdit['id'] ?? 0; ?>">
        <div>
            <label class="block text-sm font-medium">Kod</label>
            <input type="text" name="code" value="<?= htmlspecialchars($materialToEdit['code'] ?? ''); ?>" class="w-full border rounded px-3 py-2" required>
        </div>
        <div>
            <label class="block text-sm font-medium">Rang</label>
            <input type="text" name="color" value="<?= htmlspecialchars($materialToEdit['color'] ?? ''); ?>" class="w-full border rounded px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium">Tekstura</label>
            <input type="text" name="texture" value="<?= htmlspecialchars($materialToEdit['texture'] ?? ''); ?>" class="w-full border rounded px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium">Rul kengligi (m)</label>
            <input type="number" step="0.1" name="roll_width_m" value="<?= htmlspecialchars($materialToEdit['roll_width_m'] ?? ''); ?>" class="w-full border rounded px-3 py-2" required>
        </div>
        <div>
            <label class="block text-sm font-medium">Kichik kenglik narxi (1.5-3.6m)</label>
            <input type="number" name="default_price_small_m2" value="<?= htmlspecialchars($materialToEdit['default_price_small_m2'] ?? 15000); ?>" class="w-full border rounded px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium">Katta kenglik narxi (3.7-5.0m)</label>
            <input type="number" name="default_price_large_m2" value="<?= htmlspecialchars($materialToEdit['default_price_large_m2'] ?? 25000); ?>" class="w-full border rounded px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium">Xarid narxi (m²)</label>
            <input type="number" name="purchase_price_per_m2" value="<?= htmlspecialchars($materialToEdit['purchase_price_per_m2'] ?? 0); ?>" class="w-full border rounded px-3 py-2">
        </div>
        <div class="md:col-span-2">
            <label class="block text-sm font-medium">Izoh</label>
            <textarea name="notes" class="w-full border rounded px-3 py-2" rows="2"><?= htmlspecialchars($materialToEdit['notes'] ?? ''); ?></textarea>
        </div>
        <div class="md:col-span-2 flex items-center gap-3">
            <button class="bg-emerald-600 text-white px-6 py-2 rounded"><?= $materialToEdit ? 'Yangilash' : 'Qo\'shish'; ?></button>
            <?php if ($materialToEdit): ?>
                <a href="materials.php" class="text-sm text-gray-500">Bekor qilish</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<div class="bg-white rounded shadow overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-slate-100">
            <tr>
                <th class="px-3 py-2 text-left">Kod</th>
                <th>Rang</th>
                <th>Tekstura</th>
                <th>Rul kengligi</th>
                <th>1.5-3.6m narxi</th>
                <th>3.7-5.0m narxi</th>
                <th>Xarid narxi</th>
                <th>Izoh</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($materials as $material): ?>
                <tr class="border-t">
                    <td class="px-3 py-2 font-semibold"><?= htmlspecialchars($material['code']); ?></td>
                    <td><?= htmlspecialchars($material['color']); ?></td>
                    <td><?= htmlspecialchars($material['texture']); ?></td>
                    <td><?= htmlspecialchars($material['roll_width_m']); ?> m</td>
                    <td><?= number_format((int)$material['default_price_small_m2'], 0, '.', ' '); ?></td>
                    <td><?= number_format((int)$material['default_price_large_m2'], 0, '.', ' '); ?></td>
                    <td><?= number_format((int)$material['purchase_price_per_m2'], 0, '.', ' '); ?></td>
                    <td><?= htmlspecialchars($material['notes']); ?></td>
                    <td class="text-right px-3">
                        <a href="materials.php?edit=<?= $material['id']; ?>" class="text-emerald-600 text-sm">Tahrirlash</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/../app/Views/layout/footer.php'; ?>
