<?php
require_once __DIR__ . '/../app/Config/config.php';

$userModel = new User($database);

function generateUsername(): string
{
    $prefixes = ['admin', 'manager', 'user', 'kaktus'];
    $prefix = $prefixes[array_rand($prefixes)];
    return $prefix . random_int(100, 999);
}

function generatePassword(int $length = 12): string
{
    $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789!@$%&*';
    $password = '';
    for ($i = 0; $i < $length; $i++) {
        $password .= $chars[random_int(0, strlen($chars) - 1)];
    }
    return $password;
}

$roles = [
    'admin' => 'Admin',
    'manager' => 'Sotuvchi (manager)',
    'production_manager' => 'Ishlab chiqarish boshlig\'i',
    'cutter' => 'Kesuvchi',
    'packer' => 'Qadoqlovchi',
    'accountant' => 'Buxgalter',
];

$message = null;
$messageType = 'info';
$formName = '';
$formPhone = '';
$formRole = 'manager';
$formUsername = '';
$formPassword = '';
$formAklad = '';
$formRateM2 = '';
$formRateMeter = '';
$formSalesPercent = '';
$generated = null;

default_timezone_set('Asia/Tashkent');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'random') {
        $formUsername = generateUsername();
        $formPassword = generatePassword();
        $generated = [
            'username' => $formUsername,
            'password' => $formPassword,
            'hash' => password_hash($formPassword, PASSWORD_BCRYPT),
            'stored' => false,
        ];
        $message = 'Tasodifiy login va parol yaratildi. Endi quyidagi formani to\'ldirib foydalanuvchini saqlang.';
        $messageType = 'info';
    } elseif ($action === 'create') {
        $formName = trim($_POST['full_name'] ?? '');
        $formPhone = trim($_POST['phone'] ?? '');
        $formRole = $_POST['role'] ?? 'manager';
        $formUsername = trim($_POST['username'] ?? '');
        $formPassword = trim($_POST['password'] ?? '');
        $formAklad = trim($_POST['aklad_monthly'] ?? '');
        $formRateM2 = trim($_POST['rate_per_m2'] ?? '');
        $formRateMeter = trim($_POST['rate_per_meter'] ?? '');
        $formSalesPercent = trim($_POST['sales_percent'] ?? '');

        if ($formName === '' || $formUsername === '' || $formPassword === '') {
            $message = "Iltimos, ism, login va parolni to'liq kiriting.";
            $messageType = 'error';
        } elseif (!isset($roles[$formRole])) {
            $message = 'Rol tanlashda xatolik.';
            $messageType = 'error';
        } elseif ($userModel->findByUsername($formUsername)) {
            $message = 'Bu login allaqachon mavjud. Boshqasini tanlang.';
            $messageType = 'error';
        } else {
            try {
                $hash = password_hash($formPassword, PASSWORD_BCRYPT);
                $newUserId = $userModel->create([
                    'name' => $formName,
                    'username' => $formUsername,
                    'password_hash' => $hash,
                    'role' => $formRole,
                    'phone' => $formPhone,
                    'aklad_monthly' => $formAklad !== '' ? (int)$formAklad : 0,
                    'rate_per_m2' => $formRateM2 !== '' ? (int)$formRateM2 : 0,
                    'rate_per_meter' => $formRateMeter !== '' ? (int)$formRateMeter : 0,
                    'sales_percent' => $formSalesPercent !== '' ? (float)$formSalesPercent : 0,
                    'is_active' => 1,
                ]);

                $generated = [
                    'username' => $formUsername,
                    'password' => $formPassword,
                    'hash' => $hash,
                    'stored' => true,
                    'user_id' => $newUserId,
                ];

                $message = "Foydalanuvchi #{$newUserId} muvaffaqiyatli yaratildi.";
                $messageType = 'success';
            } catch (Throwable $e) {
                $message = 'Saqlashda kutilmagan xatolik: ' . $e->getMessage();
                $messageType = 'error';
            }
        }
    }
}

include __DIR__ . '/../app/Views/layout/header.php';
?>
<div class="bg-white rounded-lg shadow p-6 max-w-4xl mx-auto">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-brand-600 mb-1">Login va foydalanuvchi yaratish</h1>
            <p class="text-sm text-gray-600">Yangi xodim uchun login, parol va xeshni avtomatik yarating va darhol bazaga qo'shing.</p>
        </div>
        <form method="post">
            <input type="hidden" name="action" value="random">
            <button type="submit" class="px-4 py-2 bg-brand-100 text-brand-700 rounded hover:bg-brand-200 text-sm">
                Tasodifiy login/parol
            </button>
        </form>
    </div>

    <?php if ($message): ?>
        <?php
        $colors = [
            'success' => 'bg-green-100 text-green-800 border-green-200',
            'error' => 'bg-red-100 text-red-800 border-red-200',
            'info' => 'bg-blue-100 text-blue-800 border-blue-200',
        ];
        $colorClass = $colors[$messageType] ?? $colors['info'];
        ?>
        <div class="mt-4 px-4 py-3 border rounded <?= $colorClass; ?> text-sm">
            <?= htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <form method="post" class="mt-6 space-y-4">
        <input type="hidden" name="action" value="create">
        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Xodimning to'liq ismi</label>
                <input type="text" name="full_name" value="<?= htmlspecialchars($formName); ?>"
                       class="mt-1 w-full rounded border-gray-300 focus:border-brand-500 focus:ring-brand-500"
                       placeholder="Masalan, Aliyev Diyor" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Telefon (ixtiyoriy)</label>
                <input type="text" name="phone" value="<?= htmlspecialchars($formPhone); ?>"
                       class="mt-1 w-full rounded border-gray-300 focus:border-brand-500 focus:ring-brand-500"
                       placeholder="99890...">
            </div>
        </div>
        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Rol</label>
                <select name="role" class="mt-1 w-full rounded border-gray-300 focus:border-brand-500 focus:ring-brand-500">
                    <?php foreach ($roles as $key => $label): ?>
                        <option value="<?= $key; ?>" <?= $formRole === $key ? 'selected' : ''; ?>><?= htmlspecialchars($label); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Login (username)</label>
                <input type="text" name="username" value="<?= htmlspecialchars($formUsername); ?>"
                       class="mt-1 w-full rounded border-gray-300 focus:border-brand-500 focus:ring-brand-500"
                       placeholder="Masalan, admin01" required>
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Parol (oddiy ko'rinishda)</label>
            <input type="text" name="password" value="<?= htmlspecialchars($formPassword); ?>"
                   class="mt-1 w-full rounded border-gray-300 focus:border-brand-500 focus:ring-brand-500"
                   placeholder="Masalan, Kaktus2024!" required>
            <p class="text-xs text-gray-500 mt-1">Parolni xodimga aynan shu ko'rinishda yuboring. Saytda esa bcrypt xesh saqlanadi.</p>
        </div>
        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Aklad (oylik) – ixtiyoriy</label>
                <input type="number" name="aklad_monthly" value="<?= htmlspecialchars($formAklad); ?>"
                       class="mt-1 w-full rounded border-gray-300 focus:border-brand-500 focus:ring-brand-500"
                       placeholder="0">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Sotuv foizi (%) – ixtiyoriy</label>
                <input type="number" step="0.1" name="sales_percent" value="<?= htmlspecialchars($formSalesPercent); ?>"
                       class="mt-1 w-full rounded border-gray-300 focus:border-brand-500 focus:ring-brand-500"
                       placeholder="0">
            </div>
        </div>
        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Pechat stavkasi (so'm/m²)</label>
                <input type="number" name="rate_per_m2" value="<?= htmlspecialchars($formRateM2); ?>"
                       class="mt-1 w-full rounded border-gray-300 focus:border-brand-500 focus:ring-brand-500"
                       placeholder="0">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Garfun stavkasi (so'm/m)</label>
                <input type="number" name="rate_per_meter" value="<?= htmlspecialchars($formRateMeter); ?>"
                       class="mt-1 w-full rounded border-gray-300 focus:border-brand-500 focus:ring-brand-500"
                       placeholder="0">
            </div>
        </div>
        <button type="submit" class="px-4 py-2 bg-brand-600 text-white rounded hover:bg-brand-700">
            Foydalanuvchini yaratish
        </button>
    </form>

    <?php if ($generated): ?>
        <div class="mt-8 border-t pt-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Natija</h2>
            <div class="grid md:grid-cols-3 gap-4 text-sm">
                <div class="p-3 rounded border">
                    <p class="text-gray-500 text-xs uppercase">Login</p>
                    <p class="font-semibold text-gray-800 text-lg break-all">
                        <?= htmlspecialchars($generated['username']); ?>
                    </p>
                </div>
                <div class="p-3 rounded border">
                    <p class="text-gray-500 text-xs uppercase">Oddiy parol</p>
                    <p class="font-semibold text-gray-800 text-lg break-all">
                        <?= htmlspecialchars($generated['password']); ?>
                    </p>
                    <p class="text-xs text-orange-600 mt-1">* Ushbu parolni faqat xavfsiz kanal orqali yuboring.</p>
                </div>
                <div class="p-3 rounded border md:col-span-1">
                    <p class="text-gray-500 text-xs uppercase">Bcrypt xesh</p>
                    <textarea class="mt-1 w-full rounded border-gray-300 text-xs" rows="4" readonly><?= htmlspecialchars($generated['hash']); ?></textarea>
                </div>
            </div>
            <?php if (!empty($generated['stored'])): ?>
                <p class="text-xs text-green-700 mt-3">
                    Foydalanuvchi ID: <?= htmlspecialchars((string)($generated['user_id'] ?? '')); ?> bazaga yozildi va darhol tizimga kirishi mumkin.
                </p>
            <?php else: ?>
                <p class="text-xs text-gray-500 mt-3">
                    Ushbu ma'lumotlar hozircha bazaga yozilmadi. Saqlash uchun yuqoridagi formani to'ldirib yuboring.
                </p>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="mt-6 text-sm">
        <a href="login.php" class="text-brand-600 hover:text-brand-700">← Kirish sahifasiga qaytish</a>
    </div>
</div>
<?php include __DIR__ . '/../app/Views/layout/footer.php'; ?>
