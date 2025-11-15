<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.4.1/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/custom.css">
</head>
<body class="bg-slate-100 min-h-screen">
    <header class="bg-white shadow">
        <div class="max-w-6xl mx-auto px-4 py-4 flex items-center justify-between">
            <a href="index.php" class="font-semibold text-xl text-emerald-600"><?= APP_NAME; ?></a>
            <?php if (!empty($_SESSION['user'])): ?>
            <nav class="space-x-3 text-sm">
                <a href="dealers.php" class="text-gray-600 hover:text-black">Dilerlar</a>
                <a href="orders.php" class="text-gray-600 hover:text-black">Buyurtmalar</a>
                <a href="production_cutting.php" class="text-gray-600 hover:text-black">Kesish</a>
                <a href="production_packing.php" class="text-gray-600 hover:text-black">Qadoqlash</a>
                <a href="inventory_rolls.php" class="text-gray-600 hover:text-black">Rullar</a>
                <a href="inventory_items.php" class="text-gray-600 hover:text-black">Aksessuarlar</a>
                <a href="finance_payments.php" class="text-gray-600 hover:text-black">To'lovlar</a>
                <a href="payroll.php" class="text-gray-600 hover:text-black">Ish haqi</a>
                <a href="settings.php" class="text-gray-600 hover:text-black">Sozlamalar</a>
                <a href="logout.php" class="text-red-500">Chiqish</a>
            </nav>
            <?php endif; ?>
        </div>
    </header>
    <main class="max-w-6xl mx-auto px-4 py-6">
