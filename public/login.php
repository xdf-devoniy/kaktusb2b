<?php
require_once __DIR__ . '/../app/Config/config.php';

if (!empty($_SESSION['user'])) {
    redirect('index.php');
}

$error = null;

if (is_post()) {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (Auth::attempt($database, $username, $password)) {
        redirect('index.php');
    } else {
        $error = "Login yoki parol noto'g'ri.";
    }
}

include __DIR__ . '/../app/Views/layout/header.php';
?>
<div class="max-w-md mx-auto bg-white p-6 rounded shadow">
    <h1 class="text-xl font-semibold mb-4">Kirish</h1>
    <?php if ($error): ?>
        <div class="bg-red-100 text-red-700 px-3 py-2 rounded mb-3"><?= $error; ?></div>
    <?php endif; ?>
    <form method="post" class="space-y-4">
        <div>
            <label class="block text-sm font-medium">Login</label>
            <input type="text" name="username" class="mt-1 w-full border rounded px-3 py-2" required>
        </div>
        <div>
            <label class="block text-sm font-medium">Parol</label>
            <input type="password" name="password" class="mt-1 w-full border rounded px-3 py-2" required>
        </div>
        <button class="w-full bg-emerald-600 text-white py-2 rounded">Kirish</button>
    </form>
    <p class="text-xs text-gray-500 mt-4">
        Admin uchun yangi parol xeshi kerakmi? <a href="password_generator.php" class="text-brand-600 font-medium">Parol generatoridan</a> foydalaning.
    </p>
</div>
<?php include __DIR__ . '/../app/Views/layout/footer.php'; ?>
