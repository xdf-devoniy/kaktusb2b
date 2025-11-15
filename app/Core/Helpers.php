<?php

function view(string $path, array $data = []): void
{
    extract($data);
    include __DIR__ . '/../Views/layout/header.php';
    include __DIR__ . '/../Views/' . $path . '.php';
    include __DIR__ . '/../Views/layout/footer.php';
}

function redirect(string $path): void
{
    header('Location: ' . $path);
    exit;
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
    }

    return $_SESSION['csrf_token'];
}

function csrf_check(string $token): bool
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function format_currency(int $value): string
{
    return number_format($value, 0, '.', ' ') . " so'm";
}

function is_post(): bool
{
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

function require_login(): void
{
    if (empty($_SESSION['user'])) {
        redirect('login.php');
    }
}

function require_role(array $roles): void
{
    require_login();
    if (!in_array($_SESSION['user']['role'], $roles, true)) {
        http_response_code(403);
        echo "Ruxsat yo'q";
        exit;
    }
}

function get_input(string $key, $default = null)
{
    return $_POST[$key] ?? $_GET[$key] ?? $default;
}
