<?php
require_once __DIR__ . '/../app/Config/config.php';
require_login();

$production = new Production($database);
$tasks = $production->cuttingTasks();

include __DIR__ . '/../app/Views/layout/header.php';
?>
<h1 class="text-2xl font-semibold mb-4">Kesish vazifalari</h1>
<div class="bg-white rounded shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-slate-100">
            <tr>
                <th class="px-4 py-2">Buyurtma</th>
                <th>Xona</th>
                <th>Material</th>
                <th>Uzunlik</th>
                <th>Status</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($tasks as $task): ?>
            <tr class="border-t">
                <td class="px-4 py-2"><?= htmlspecialchars($task['global_number']); ?></td>
                <td><?= htmlspecialchars($task['room_name']); ?></td>
                <td><?= htmlspecialchars($task['material_code']); ?></td>
                <td><?= $task['cutting_length_m']; ?> m</td>
                <td><?= htmlspecialchars($task['status']); ?></td>
                <td class="text-right px-4">
                    <button class="px-3 py-1 border rounded text-xs">Boshlash</button>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (!$tasks): ?>
            <tr><td colspan="6" class="text-center text-gray-500 py-4">Vazifalar yo'q</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php include __DIR__ . '/../app/Views/layout/footer.php'; ?>
