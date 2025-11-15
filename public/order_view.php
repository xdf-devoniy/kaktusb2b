<?php
require_once __DIR__ . '/../app/Config/config.php';
require_login();

$orderModel = new Order($database);
$orderId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$order = $orderModel->find($orderId);

if (!$order) {
    redirect('orders.php');
}

$items = $orderModel->items($orderId);
$totalRooms = count($items);

$copies = [
    [
        'label' => "Xisob-faktura",
        'description' => 'Mijoz nusxasi',
    ],
    [
        'label' => 'Ishlab chiqarish',
        'description' => 'Bichuvchi / Garfun uchun',
    ],
];

include __DIR__ . '/../app/Views/layout/header.php';
?>
<div class="flex items-center justify-between mb-6 print:hidden">
    <a href="orders.php" class="text-sm text-gray-600">&larr; Buyurtmalar ro'yxati</a>
    <div class="space-x-2">
        <a href="order_form.php?repeat=<?= $order['id']; ?>" class="px-3 py-1 text-sm border rounded text-gray-700">Nusxa olish</a>
        <button onclick="window.print()" class="px-4 py-2 bg-emerald-600 text-white rounded">PDF/Print</button>
    </div>
</div>

<section class="bg-white rounded shadow p-6 mb-6">
    <div class="flex flex-wrap items-start justify-between gap-6">
        <div>
            <p class="text-xs uppercase tracking-widest text-gray-500">Buyurtma</p>
            <h1 class="text-3xl font-semibold text-slate-900">#<?= htmlspecialchars($order['global_number']); ?></h1>
            <p class="text-gray-500"><?= date('d.m.Y H:i', strtotime($order['created_at'])); ?> · <?= htmlspecialchars($order['status']); ?></p>
        </div>
        <dl class="grid grid-cols-2 gap-x-10 text-sm">
            <div>
                <dt class="text-gray-500">Kundalik tartib raqami</dt>
                <dd class="font-semibold"><?= htmlspecialchars($order['daily_sequence']); ?></dd>
            </div>
            <div>
                <dt class="text-gray-500">Talab qilingan sana</dt>
                <dd class="font-semibold"><?= $order['required_date'] ? date('d.m.Y', strtotime($order['required_date'])) : '—'; ?></dd>
            </div>
            <div>
                <dt class="text-gray-500">Jami m²</dt>
                <dd class="font-semibold"><?= number_format((float)$order['total_m2'], 2); ?></dd>
            </div>
            <div>
                <dt class="text-gray-500">Jami summa</dt>
                <dd class="font-semibold text-emerald-600"><?= format_currency((int)$order['total_price']); ?></dd>
            </div>
        </dl>
    </div>
    <div class="mt-6 grid md:grid-cols-2 gap-4 text-sm">
        <div class="p-4 border rounded">
            <p class="text-xs uppercase text-gray-500 mb-1">Mijoz haqida ma'lumot</p>
            <p class="font-semibold text-lg"><?= htmlspecialchars($order['dealer_name']); ?></p>
            <p>Tel: <?= htmlspecialchars($order['dealer_phone'] ?: '—'); ?></p>
            <p>Hudud: <?= htmlspecialchars($order['dealer_region'] ?: 'Ko\'rsatilmagan'); ?></p>
            <p>Telegram: <?= htmlspecialchars($order['dealer_telegram'] ?: '—'); ?></p>
            <p>Chegirma: <?= (float)$order['discount_percent']; ?>%</p>
        </div>
        <div class="p-4 border rounded">
            <p class="text-xs uppercase text-gray-500 mb-1">Izoh</p>
            <p class="text-gray-700 whitespace-pre-line"><?= $order['comment'] ? htmlspecialchars($order['comment']) : '—'; ?></p>
            <p class="text-xs text-gray-500 mt-3">Mas'ul xodim: <?= htmlspecialchars($_SESSION['user']['name'] ?? ''); ?></p>
        </div>
    </div>
</section>

<section class="grid lg:grid-cols-2 gap-6 print:gap-4">
    <?php foreach ($copies as $copy): ?>
    <article class="invoice-copy bg-white border border-emerald-100 rounded-xl shadow-sm p-5">
        <div class="flex items-start justify-between border-b border-dashed pb-3">
            <div>
                <p class="text-xs uppercase tracking-widest text-emerald-600"><?= COMPANY_SERVICE; ?></p>
                <h2 class="text-2xl font-bold text-emerald-700">KAKTUS PROFI</h2>
                <p class="text-sm text-gray-500">Tel: <?= COMPANY_PHONE; ?> · <?= COMPANY_LOCATION; ?></p>
            </div>
            <div class="text-right text-xs uppercase text-gray-500">
                <p><?= htmlspecialchars($copy['label']); ?></p>
                <p class="text-[11px] font-semibold text-slate-700"><?= htmlspecialchars($copy['description']); ?></p>
                <p><?= date('Y', strtotime($order['created_at'])); ?></p>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-3 text-xs mt-4">
            <div class="border border-slate-200 rounded p-2">
                <p class="text-[11px] uppercase text-gray-500">Buyurtma raqami</p>
                <p class="text-lg font-semibold text-slate-900"><?= htmlspecialchars($order['global_number']); ?></p>
            </div>
            <div class="border border-slate-200 rounded p-2">
                <p class="text-[11px] uppercase text-gray-500">Kundalik tartib</p>
                <p class="text-lg font-semibold text-slate-900"><?= htmlspecialchars($order['daily_sequence']); ?></p>
            </div>
            <div class="border border-slate-200 rounded p-2">
                <p class="text-[11px] uppercase text-gray-500">Buyurtma sanasi</p>
                <p class="text-base font-semibold text-slate-900"><?= date('d.m.Y', strtotime($order['created_at'])); ?></p>
            </div>
            <div class="border border-slate-200 rounded p-2">
                <p class="text-[11px] uppercase text-gray-500">Talab qilingan sana</p>
                <p class="text-base font-semibold text-slate-900"><?= $order['required_date'] ? date('d.m.Y', strtotime($order['required_date'])) : '—'; ?></p>
            </div>
        </div>

        <div class="mt-4 text-xs border border-slate-200 rounded p-3">
            <p class="text-[11px] uppercase text-gray-500 mb-2">Mijoz ma'lumotlari</p>
            <div class="grid grid-cols-2 gap-2">
                <div>
                    <p class="text-gray-500">Ismi</p>
                    <p class="font-semibold text-slate-900"><?= htmlspecialchars($order['dealer_name']); ?></p>
                </div>
                <div>
                    <p class="text-gray-500">Telefon</p>
                    <p class="font-semibold text-slate-900"><?= htmlspecialchars($order['dealer_phone'] ?: '—'); ?></p>
                </div>
                <div>
                    <p class="text-gray-500">Hudud</p>
                    <p class="font-semibold text-slate-900"><?= htmlspecialchars($order['dealer_region'] ?: '—'); ?></p>
                </div>
                <div>
                    <p class="text-gray-500">Telegram</p>
                    <p class="font-semibold text-slate-900"><?= htmlspecialchars($order['dealer_telegram'] ?: '—'); ?></p>
                </div>
            </div>
        </div>

        <div class="mt-4 overflow-hidden border border-slate-200 rounded">
            <table class="w-full text-[12px]">
                <thead class="bg-emerald-50 text-emerald-700">
                    <tr>
                        <th class="px-2 py-2 text-left">#</th>
                        <th class="text-left">Material</th>
                        <th>Eni</th>
                        <th>Bo'yi</th>
                        <th>Umumiy kv</th>
                        <th>Narx/m²</th>
                        <th>Jami</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $index => $item): ?>
                    <tr class="border-t">
                        <td class="px-2 py-2 font-semibold text-slate-700"><?= $index + 1; ?>. <?= htmlspecialchars($item['name']); ?></td>
                        <td class="text-slate-600">
                            <div class="font-semibold text-slate-900"><?= htmlspecialchars($item['material_code']); ?></div>
                            <div class="text-[11px] text-gray-500">
                                <?= htmlspecialchars(trim(($item['material_color'] ?? '') . ' ' . ($item['material_texture'] ?? ''))); ?>
                            </div>
                        </td>
                        <td class="text-center"><?= number_format((float)$item['width_m'], 2); ?> m</td>
                        <td class="text-center"><?= number_format((float)$item['height_m'], 2); ?> m</td>
                        <td class="text-center font-semibold"><?= number_format((float)$item['area_m2'], 2); ?></td>
                        <td class="text-center"><?= number_format((int)$item['price_per_m2'], 0, '.', ' '); ?></td>
                        <td class="text-center font-semibold"><?= number_format((int)$item['total_price'], 0, '.', ' '); ?></td>
                    </tr>
                    <?php if ($item['sketch_path']): ?>
                    <tr class="border-t">
                        <td colspan="7" class="px-2 py-3 bg-slate-50">
                            <p class="text-[11px] uppercase text-gray-500 mb-2">Chizma</p>
                            <img src="<?= htmlspecialchars($item['sketch_path']); ?>" alt="Sketch" class="max-h-40 object-contain">
                        </td>
                    </tr>
                    <?php endif; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="mt-4 grid grid-cols-3 gap-4 text-xs">
            <div class="border border-slate-200 rounded p-2">
                <p class="text-[11px] uppercase text-gray-500">Jami xonalar</p>
                <p class="text-lg font-semibold text-slate-900"><?= $totalRooms; ?></p>
            </div>
            <div class="border border-slate-200 rounded p-2">
                <p class="text-[11px] uppercase text-gray-500">Jami kv.m</p>
                <p class="text-lg font-semibold text-slate-900"><?= number_format((float)$order['total_m2'], 2); ?></p>
            </div>
            <div class="border border-slate-200 rounded p-2">
                <p class="text-[11px] uppercase text-gray-500">Umumiy summa</p>
                <p class="text-lg font-semibold text-emerald-600"><?= format_currency((int)$order['total_price']); ?></p>
            </div>
        </div>

        <div class="mt-5 grid grid-cols-2 gap-6 text-xs">
            <div>
                <p class="text-[11px] uppercase text-gray-500">Bichuvchi</p>
                <div class="h-10 border-b border-dashed"></div>
            </div>
            <div>
                <p class="text-[11px] uppercase text-gray-500">Garfun ustasi</p>
                <div class="h-10 border-b border-dashed"></div>
            </div>
        </div>
    </article>
    <?php endforeach; ?>
</section>

<?php include __DIR__ . '/../app/Views/layout/footer.php'; ?>
