<?php
require_once __DIR__ . '/../app/Config/config.php';
require_login();

$settingsModel = new Settings($database);
$message = null;

if (is_post()) {
    $settingsModel->save([
        'company_name' => $_POST['company_name'],
        'working_days_per_month' => $_POST['working_days_per_month'],
        'small_width_min' => $_POST['small_width_min'],
        'small_width_max' => $_POST['small_width_max'],
        'large_width_min' => $_POST['large_width_min'],
        'large_width_max' => $_POST['large_width_max'],
    ]);
    $message = 'Sozlamalar saqlandi';
}

$settings = $settingsModel->all();

include __DIR__ . '/../app/Views/layout/header.php';
?>
<h1 class="text-2xl font-semibold mb-4">Sozlamalar</h1>
<?php if ($message): ?>
<div class="bg-emerald-100 text-emerald-700 px-4 py-2 rounded mb-4"><?= $message; ?></div>
<?php endif; ?>
<form method="post" class="bg-white rounded shadow p-4 grid md:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium">Kompaniya nomi</label>
        <input type="text" name="company_name" value="<?= htmlspecialchars($settings['company_name'] ?? ''); ?>" class="w-full border rounded px-3 py-2">
    </div>
    <div>
        <label class="block text-sm font-medium">Oyiga ish kunlari</label>
        <input type="number" name="working_days_per_month" value="<?= htmlspecialchars($settings['working_days_per_month'] ?? '26'); ?>" class="w-full border rounded px-3 py-2">
    </div>
    <div>
        <label class="block text-sm font-medium">Kichik kenglik min</label>
        <input type="number" step="0.1" name="small_width_min" value="<?= htmlspecialchars($settings['small_width_min'] ?? '1.5'); ?>" class="w-full border rounded px-3 py-2">
    </div>
    <div>
        <label class="block text-sm font-medium">Kichik kenglik max</label>
        <input type="number" step="0.1" name="small_width_max" value="<?= htmlspecialchars($settings['small_width_max'] ?? '3.6'); ?>" class="w-full border rounded px-3 py-2">
    </div>
    <div>
        <label class="block text-sm font-medium">Katta kenglik min</label>
        <input type="number" step="0.1" name="large_width_min" value="<?= htmlspecialchars($settings['large_width_min'] ?? '3.7'); ?>" class="w-full border rounded px-3 py-2">
    </div>
    <div>
        <label class="block text-sm font-medium">Katta kenglik max</label>
        <input type="number" step="0.1" name="large_width_max" value="<?= htmlspecialchars($settings['large_width_max'] ?? '5.0'); ?>" class="w-full border rounded px-3 py-2">
    </div>
    <div class="md:col-span-2 flex justify-end">
        <button class="bg-emerald-600 text-white px-4 py-2 rounded">Saqlash</button>
    </div>
</form>
<?php include __DIR__ . '/../app/Views/layout/footer.php'; ?>
