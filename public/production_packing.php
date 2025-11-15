<?php
require_once __DIR__ . '/../app/Config/config.php';
require_login();

$production = new Production($database);
$tasks = $production->packingTasks();

include __DIR__ . '/../app/Views/layout/header.php';
?>
<h1 class="text-2xl font-semibold mb-4">Qadoqlash</h1>
<div class="bg-white rounded shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-slate-100">
            <tr>
                <th class="px-4 py-2">Buyurtma</th>
                <th>Diler</th>
                <th>m²</th>
                <th>Status</th>
                <th>Aksessuarlar</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($tasks as $task): ?>
            <tr class="border-t">
                <td class="px-4 py-2"><?= htmlspecialchars($task['global_number']); ?></td>
                <td><?= htmlspecialchars($task['dealer_name']); ?></td>
                <td><?= $task['total_m2']; ?></td>
                <td><?= htmlspecialchars($task['status']); ?></td>
                <td><?= htmlspecialchars($task['accessories']); ?></td>
            </tr>
            <?php endforeach; ?>
            <?php if (!$tasks): ?>
            <tr><td colspan="5" class="text-center text-gray-500 py-4">Vazifalar yo'q</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php include __DIR__ . '/../app/Views/layout/footer.php'; ?>
