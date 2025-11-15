<?php
require_once __DIR__ . '/../app/Config/config.php';
require_login();

$conn = $database->getConnection();
$today = date('Y-m-d');

$ordersStmt = $conn->prepare('SELECT status, COUNT(*) AS total FROM orders WHERE date(created_at) = :today GROUP BY status');
$ordersStmt->execute(['today' => $today]);
$ordersToday = $ordersStmt->fetchAll(PDO::FETCH_KEY_PAIR);
$statusSummary = $ordersToday ? implode(', ', array_map(fn($s, $c) => "$s: $c", array_keys($ordersToday), $ordersToday)) : "Ma'lumot yo'q";
$ordersCountToday = $ordersToday ? array_sum($ordersToday) : 0;

$tasksStmt = $conn->prepare('SELECT COUNT(*) FROM production_tasks WHERE date(created_at) = :today');
$tasksStmt->execute(['today' => $today]);
$todayCutting = (int)$tasksStmt->fetchColumn();

$packingStmt = $conn->prepare('SELECT COUNT(*) FROM packing_tasks WHERE date(packed_at) = :today');
$packingStmt->execute(['today' => $today]);
$todayPacking = (int)$packingStmt->fetchColumn();

$m2Stmt = $conn->prepare('SELECT IFNULL(SUM(total_m2),0) FROM orders WHERE date(created_at) = :today');
$m2Stmt->execute(['today' => $today]);
$todayM2 = (float)$m2Stmt->fetchColumn();

$debtStmt = $conn->query('SELECT IFNULL(SUM(total_price),0) - IFNULL((SELECT SUM(amount) FROM payments),0) FROM orders');
$totalDebt = (int)$debtStmt->fetchColumn();

include __DIR__ . '/../app/Views/layout/header.php';
?>
<h1 class="text-2xl font-semibold mb-6">Bugungi holat</h1>
<div class="grid md:grid-cols-3 gap-4">
    <div class="bg-white rounded shadow p-4">
        <p class="text-sm text-gray-500">Bugungi buyurtmalar</p>
        <p class="text-3xl font-bold"><?= $ordersCountToday; ?></p>
        <p class="text-xs text-gray-500 mt-2">Holatlar: <?= htmlspecialchars($statusSummary); ?></p>
    </div>
    <div class="bg-white rounded shadow p-4">
        <p class="text-sm text-gray-500">Bugungi kesish vazifalari</p>
        <p class="text-3xl font-bold"><?= $todayCutting; ?></p>
    </div>
    <div class="bg-white rounded shadow p-4">
        <p class="text-sm text-gray-500">Bugungi qadoqlash</p>
        <p class="text-3xl font-bold"><?= $todayPacking; ?></p>
    </div>
</div>
<div class="grid md:grid-cols-2 gap-4 mt-6">
    <div class="bg-white rounded shadow p-4">
        <p class="text-sm text-gray-500">Bugungi m²</p>
        <p class="text-3xl font-bold"><?= $todayM2; ?> m²</p>
    </div>
    <div class="bg-white rounded shadow p-4">
        <p class="text-sm text-gray-500">Umumiy qarzdorlik</p>
        <p class="text-3xl font-bold"><?= format_currency($totalDebt); ?></p>
    </div>
</div>
<?php include __DIR__ . '/../app/Views/layout/footer.php'; ?>
