<?php
require_once __DIR__ . '/../app/Config/config.php';

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

$message = null;
$formUsername = '';
$formPassword = '';
$generated = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['generate_random'])) {
        $formUsername = generateUsername();
        $formPassword = generatePassword();
    } else {
        $formUsername = trim($_POST['username'] ?? '');
        $formPassword = trim($_POST['password'] ?? '');
    }

    if ($formUsername === '' || $formPassword === '') {
        $message = "Iltimos, login va parolni to'ldiring.";
    } else {
        $generated = [
            'username' => $formUsername,
            'password' => $formPassword,
            'hash' => password_hash($formPassword, PASSWORD_BCRYPT),
        ];
    }
}

include __DIR__ . '/../app/Views/layout/header.php';
?>
<div class="bg-white rounded-lg shadow p-6 max-w-3xl mx-auto">
    <div class="flex items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-brand-600 mb-2">Login va parol generatori</h1>
            <p class="text-sm text-gray-600">
                Adminlar yangi foydalanuvchi yaratish jarayonida quyidagi vosita yordamida <strong>login</strong>, <strong>oddiy
                parol</strong> va <strong>bcrypt xesh</strong> ni birdaniga olishi mumkin.
            </p>
        </div>
        <form method="post">
            <input type="hidden" name="generate_random" value="1">
            <button type="submit" class="px-4 py-2 bg-brand-100 text-brand-700 rounded hover:bg-brand-200 text-sm">
                Tasodifiy yaratish
            </button>
        </form>
    </div>

    <?php if ($message): ?>
        <div class="mt-4 rounded bg-red-100 text-red-700 px-4 py-2 text-sm">
            <?= htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <form method="post" class="mt-6 space-y-4">
        <div>
            <label class="block text-sm font-medium text-gray-700">Login (username)</label>
            <input type="text" name="username" value="<?= htmlspecialchars($formUsername); ?>"
                   class="mt-1 w-full rounded border-gray-300 focus:border-brand-500 focus:ring-brand-500"
                   placeholder="Masalan, admin01" required>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Parol (oddiy ko'rinishda)</label>
            <input type="text" name="password" value="<?= htmlspecialchars($formPassword); ?>"
                   class="mt-1 w-full rounded border-gray-300 focus:border-brand-500 focus:ring-brand-500"
                   placeholder="Masalan, Kaktus2024!" required>
            <p class="text-xs text-gray-500 mt-1">Parolni xodimga aynan shu ko'rinishda yuborasiz.</p>
        </div>
        <button type="submit"
                class="px-4 py-2 bg-brand-600 text-white rounded hover:bg-brand-700">
            Login va parolni tasdiqlash
        </button>
    </form>

    <?php if ($generated): ?>
        <div class="mt-8 border-t pt-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Yaratilgan ma'lumotlar</h2>
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
            <p class="text-xs text-gray-500 mt-3">
                <code>users</code> jadvaliga qo'shish uchun ustunlarni quyidagicha to'ldiring: <code>username</code> = login,
                <code>password_hash</code> = xesh, boshqa maydonlar (ism, rol) ni ham kiritishni unutmang.
            </p>
        </div>
    <?php endif; ?>

    <div class="mt-6 text-sm">
        <a href="login.php" class="text-brand-600 hover:text-brand-700">← Kirish sahifasiga qaytish</a>
    </div>
</div>
<?php include __DIR__ . '/../app/Views/layout/footer.php'; ?>
