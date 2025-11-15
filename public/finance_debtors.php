<?php
require_once __DIR__ . '/../app/Config/config.php';
require_login();

$finance = new Finance($database);
$dealers = $finance->dealerBalances();

include __DIR__ . '/../app/Views/layout/header.php';
?>
<h1 class="text-2xl font-semibold mb-4">Qarzdor dilerlar</h1>
<div class="bg-white rounded shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-slate-100">
            <tr>
                <th class="px-4 py-2">Diler</th>
                <th>Hisoblangan</th>
                <th>To'langan</th>
                <th>Balans</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($dealers as $dealer): ?>
            <tr class="border-t">
                <td class="px-4 py-2"><?= htmlspecialchars($dealer['name']); ?></td>
                <td><?= format_currency((int)$dealer['total_orders']); ?></td>
                <td><?= format_currency((int)$dealer['total_paid']); ?></td>
                <td><?= format_currency((int)$dealer['balance']); ?></td>
            </tr>
            <?php endforeach; ?>
            <?php if (!$dealers): ?>
            <tr><td colspan="4" class="text-center text-gray-500 py-4">Ma'lumot yo'q</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php include __DIR__ . '/../app/Views/layout/footer.php'; ?>
