<?php
if (session_status() === PHP_SESSION_NONE) {
    if (PHP_SAPI === 'cli') {
        @session_start();
    } elseif (!headers_sent()) {
        session_start();
    }
}

function superadmin_config(): array
{
    static $config = null;

    if ($config === null) {
        $configPath = __DIR__ . '/superadmin_config.php';
        $config = file_exists($configPath) ? require $configPath : [];
    }

    return is_array($config) ? $config : [];
}

function superadmin_fallback_usernames(): array
{
    $config = superadmin_config();
    $usernames = $config['fallback_superadmin_usernames'] ?? [];

    if (!is_array($usernames)) {
        return [];
    }

    $normalized = [];
    foreach ($usernames as $username) {
        $value = strtolower(trim((string)$username));
        if ($value !== '') {
            $normalized[$value] = true;
        }
    }

    return array_keys($normalized);
}

function superadmin_hardcoded_credentials(): array
{
    $config = superadmin_config();
    $credentials = $config['superadmin_credentials'] ?? [];

    if (!is_array($credentials)) {
        return ['username' => '', 'password' => ''];
    }

    return [
        'username' => trim((string)($credentials['username'] ?? '')),
        'password' => (string)($credentials['password'] ?? ''),
    ];
}

function is_hardcoded_superadmin_login(string $username, string $password): bool
{
    $credentials = superadmin_hardcoded_credentials();
    if ($credentials['username'] === '' || $credentials['password'] === '') {
        return false;
    }

    return hash_equals(strtolower($credentials['username']), strtolower(trim($username)))
        && hash_equals($credentials['password'], $password);
}

function is_fallback_superadmin_username(string $username): bool
{
    $username = strtolower(trim($username));
    if ($username === '') {
        return false;
    }

    return in_array($username, superadmin_fallback_usernames(), true);
}

function normalize_admin_role($role): string
{
    $role = strtolower(trim((string)$role));
    return $role === 'superadmin' ? 'superadmin' : 'admin';
}

function resolve_admin_role(array $admin): string
{
    return 'admin';
}

function set_admin_session(array $admin): void
{
    $role = resolve_admin_role($admin);

    $_SESSION['admin_logged_in'] = true;
    $_SESSION['admin_id'] = $admin['admin_id'] ?? null;
    $_SESSION['admin_username'] = $admin['username'] ?? '';
    $_SESSION['admin_role'] = $role;
    $_SESSION['is_superadmin'] = ($role === 'superadmin');
}

function set_hardcoded_superadmin_session(string $username): void
{
    $_SESSION['admin_logged_in'] = true;
    $_SESSION['admin_id'] = 0;
    $_SESSION['admin_username'] = trim($username);
    $_SESSION['admin_role'] = 'superadmin';
    $_SESSION['is_superadmin'] = true;
}

function is_admin_authenticated(): bool
{
    return !empty($_SESSION['admin_logged_in']);
}

function is_superadmin(): bool
{
    return is_admin_authenticated() && !empty($_SESSION['is_superadmin']);
}

function require_admin_login(string $redirect = 'login.php'): void
{
    if (!is_admin_authenticated()) {
        header('Location: ' . $redirect);
        exit();
    }
}

function require_superadmin(string $redirect = 'index.php'): void
{
    require_admin_login();
    if (!is_superadmin()) {
        $_SESSION['error'] = 'Access denied. Super Admin only.';
        header('Location: ' . $redirect);
        exit();
    }
}
