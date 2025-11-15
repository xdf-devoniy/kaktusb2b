<?php

class Auth
{
    public static function attempt(Database $db, string $username, string $password): bool
    {
        $stmt = $db->getConnection()->prepare('SELECT * FROM users WHERE username = :username AND is_active = 1 LIMIT 1');
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user'] = $user;
            return true;
        }

        return false;
    }

    public static function logout(): void
    {
        unset($_SESSION['user']);
        session_destroy();
    }
}
