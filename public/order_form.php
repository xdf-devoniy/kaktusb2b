<?php
require_once __DIR__ . '/../app/Config/config.php';
require_login();

$dealerModel = new Dealer($database);
$materialModel = new Material($database);
$orderModel = new Order($database);

$dealers = $dealerModel->all();
$materials = $materialModel->all();
$message = null;

if (is_post()) {
    $dealerId = (int)($_POST['dealer_id'] ?? 0);
    $status = $_POST['status'] ?? 'draft';
    $requiredDate = $_POST['required_date'] ?? null;
    $comment = $_POST['comment'] ?? '';

    $names = $_POST['item_name'] ?? [];
    $materialIds = $_POST['item_material'] ?? [];
    $widths = $_POST['item_width'] ?? [];
    $heights = $_POST['item_height'] ?? [];
    $welding = array_map('strval', $_POST['item_welding'] ?? []);

    $items = [];
    $totalM2 = 0;
    $totalPrice = 0;

    $sketchDir = __DIR__ . '/uploads/sketches';
    if (!is_dir($sketchDir)) {
        mkdir($sketchDir, 0777, true);
    }

    foreach ($names as $index => $name) {
        $name = trim($name);
        if (!$name || empty($materialIds[$index])) {
            continue;
        }
        $width = (float)$widths[$index];
        $height = (float)$heights[$index];
        if ($width <= 0 || $height <= 0) {
            continue;
        }
        $area = round($width * $height, 2);
        if ($width >= 1.5 && $width <= 3.6) {
            $pricePer = 15000;
        } elseif ($width >= 3.7 && $width <= 5.0) {
            $pricePer = 25000;
        } else {
            $pricePer = 15000;
        }
        $total = (int)($area * $pricePer);
        $totalM2 += $area;
        $totalPrice += $total;

        $sketchPath = null;
        if (isset($_FILES['item_sketch']['name'][$index]) && $_FILES['item_sketch']['name'][$index]) {
            $filename = time() . '_' . basename($_FILES['item_sketch']['name'][$index]);
            $target = $sketchDir . '/' . $filename;
            if (move_uploaded_file($_FILES['item_sketch']['tmp_name'][$index], $target)) {
                $sketchPath = 'uploads/sketches/' . $filename;
            }
        }

        $items[] = [
            'name' => $name,
            'material_id' => (int)$materialIds[$index],
            'width_m' => $width,
            'height_m' => $height,
            'area_m2' => $area,
            'perimeter_m' => 0,
            'welding_required' => in_array((string)$index, $welding, true) ? 1 : 0,
            'price_per_m2' => $pricePer,
            'total_price' => $total,
            'sketch_path' => $sketchPath,
            'notes' => '',
        ];
    }

    if ($dealerId <= 0) {
        $message = 'Diler tanlang.';
    } elseif ($items) {
        $orderNumber = 'ORD-' . date('ymd-His');
        $daily = date('d/m');
        $orderId = $orderModel->create([
            'dealer_id' => $dealerId,
            'global_number' => $orderNumber,
            'daily_sequence' => $daily,
            'required_date' => $requiredDate,
            'status' => $status,
            'comment' => $comment,
            'created_by' => $_SESSION['user']['id'],
            'total_m2' => $totalM2,
            'total_price' => $totalPrice,
        ], $items);
        redirect('order_view.php?id=' . $orderId);
    } else {
        $message = 'Kamida bitta xona kiriting.';
    }
}

include __DIR__ . '/../app/Views/layout/header.php';
?>
<h1 class="text-2xl font-semibold mb-4">Buyurtma yaratish</h1>
<?php if ($message): ?>
<div class="bg-red-100 text-red-700 px-4 py-2 rounded mb-4"><?= $message; ?></div>
<?php endif; ?>
<form method="post" enctype="multipart/form-data" class="space-y-6">
    <div class="bg-white rounded shadow p-4 grid md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium">Diler</label>
            <select name="dealer_id" class="w-full border rounded px-3 py-2" required>
                <option value="">Tanlang</option>
                <?php foreach ($dealers as $dealer): ?>
                    <option value="<?= $dealer['id']; ?>"><?= htmlspecialchars($dealer['name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium">Talab qilingan sana</label>
            <input type="date" name="required_date" class="w-full border rounded px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium">Status</label>
            <select name="status" class="w-full border rounded px-3 py-2">
                <option value="draft">Qoralama</option>
                <option value="confirmed">Tasdiqlangan</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium">Izoh</label>
            <textarea name="comment" class="w-full border rounded px-3 py-2" rows="2"></textarea>
        </div>
    </div>

    <div class="bg-white rounded shadow p-4">
        <div class="flex items-center justify-between mb-1">
            <div>
                <h2 class="font-semibold">Xonalar</h2>
                <p class="text-xs text-gray-500">Kerakli material topilmasa, <a class="text-emerald-600" href="materials.php" target="_blank">materiallar oynasidan</a> qo'shing.</p>
            </div>
            <button type="button" id="add-row" class="text-emerald-600">+ qator</button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm" id="items-table">
                <thead class="bg-slate-100">
                    <tr>
                        <th class="px-2 py-1">Xona</th>
                        <th>Material</th>
                        <th>Kenglik (m)</th>
                        <th>Uzunlik (m)</th>
                        <th>m²</th>
                        <th>Narx/m²</th>
                        <th>Jami</th>
                        <th>Chizma</th>
                        <th>Payvand</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
    <button class="bg-emerald-600 text-white px-6 py-2 rounded">Saqlash</button>
</form>

<script>
const materials = <?= json_encode($materials, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
const tbody = document.querySelector('#items-table tbody');
const addRowBtn = document.getElementById('add-row');
let rowCounter = 0;

function createRow() {
    const rowIndex = rowCounter++;
    const row = document.createElement('tr');
    row.classList.add('border-t');
    row.innerHTML = `
        <td class="px-2 py-1"><input name="item_name[]" class="border rounded px-2 py-1" required></td>
        <td>
            <select name="item_material[]" class="border rounded px-2 py-1" required>
                <option value="">Tanlang</option>
                ${materials.map(m => {
                    const extra = [m.color, m.texture].filter(Boolean).join(', ');
                    return `<option value="${m.id}">${m.code}${extra ? ' – ' + extra : ''}</option>`;
                }).join('')}
            </select>
        </td>
        <td><input type="number" step="0.01" name="item_width[]" class="border rounded px-2 py-1 calc-field" required></td>
        <td><input type="number" step="0.01" name="item_height[]" class="border rounded px-2 py-1 calc-field" required></td>
        <td class="text-center"><span class="area">0</span></td>
        <td class="text-center"><span class="price">0</span></td>
        <td class="text-center"><span class="total">0</span></td>
        <td><input type="file" name="item_sketch[]" accept="image/*"></td>
        <td class="text-center"><input type="checkbox" name="item_welding[]" value="${rowIndex}"></td>
    `;
    tbody.appendChild(row);
    row.querySelectorAll('.calc-field').forEach(input => input.addEventListener('input', () => updateRow(row)));
}

function updateRow(row) {
    const width = parseFloat(row.querySelector('input[name="item_width[]"]').value) || 0;
    const height = parseFloat(row.querySelector('input[name="item_height[]"]').value) || 0;
    const area = width * height;
    let pricePer = 15000;
    if (width >= 3.7) {
        pricePer = 25000;
    }
    row.querySelector('.area').textContent = area.toFixed(2);
    row.querySelector('.price').textContent = pricePer.toLocaleString('uz-UZ');
    row.querySelector('.total').textContent = Math.round(area * pricePer).toLocaleString('uz-UZ');
}

addRowBtn.addEventListener('click', createRow);
createRow();
</script>
<?php include __DIR__ . '/../app/Views/layout/footer.php'; ?>
