<?php
require_once __DIR__ . '/../app/Config/config.php';

$message = null;
$generated = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = trim($_POST['password'] ?? '');
    if ($password === '') {
        $message = 'Iltimos, parol kiriting.';
    } else {
        $generated = password_hash($password, PASSWORD_BCRYPT);
    }
}

include __DIR__ . '/../app/Views/layout/header.php';
?>
<div class="bg-white rounded-lg shadow p-6 max-w-2xl mx-auto">
    <h1 class="text-2xl font-semibold text-brand-600 mb-4">Parol generatori</h1>
    <p class="text-sm text-gray-600 mb-6">Ushbu sahifa yordamida admin yoki boshqa foydalanuvchilar uchun xavfsiz parol xeshini yaratib, uni foydalanuvchilar jadvaliga qo'shishingiz mumkin.</p>

    <?php if ($message): ?>
        <div class="mb-4 rounded bg-red-100 text-red-700 px-4 py-2 text-sm">
            <?= htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <form method="post" class="space-y-4">
        <div>
            <label class="block text-sm font-medium text-gray-700">Matn ko'rinishidagi parol</label>
            <input type="text" name="password" class="mt-1 w-full rounded border-gray-300 focus:border-brand-500 focus:ring-brand-500" placeholder="Masalan, Kaktus2024!" required>
        </div>
        <button type="submit" class="px-4 py-2 bg-brand-600 text-white rounded hover:bg-brand-700">Xesh yaratish</button>
    </form>

    <?php if ($generated): ?>
        <div class="mt-6">
            <label class="block text-sm font-medium text-gray-700">Yaratilgan xesh</label>
            <textarea class="mt-1 w-full rounded border-gray-300 text-xs" rows="3" readonly><?= htmlspecialchars($generated); ?></textarea>
            <p class="text-xs text-gray-500 mt-2">Ushbu qiymatni <code>users</code> jadvalidagi <code>password_hash</code> ustuniga kiriting.</p>
        </div>
    <?php endif; ?>

    <div class="mt-6 text-sm">
        <a href="login.php" class="text-brand-600 hover:text-brand-700">← Kirish sahifasiga qaytish</a>
    </div>
</div>
<?php include __DIR__ . '/../app/Views/layout/footer.php'; ?>
