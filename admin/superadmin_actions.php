<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/DbConfig.php';
require_once __DIR__ . '/superadmin_helpers.php';

require_superadmin('index.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit();
}

$sessionToken = $_SESSION['superadmin_csrf_token'] ?? '';
$requestToken = $_POST['csrf_token'] ?? '';

if (empty($sessionToken) || empty($requestToken) || !hash_equals($sessionToken, $requestToken)) {
    $_SESSION['error'] = 'Invalid request token. Please try again.';
    header('Location: index.php');
    exit();
}

$action = $_POST['action'] ?? '';

try {
    $db = new DbConfig();
    $connection = $db->connection;

    if (!$connection) {
        throw new Exception('Database connection not available.');
    }

    if ($action === 'backup_system') {
        $result = superadmin_create_backup($connection, (string)($_SESSION['admin_username'] ?? 'superadmin'));
        $_SESSION[$result['success'] ? 'success' : 'error'] = $result['message'];
    } elseif ($action === 'reset_system') {
        $confirmText = trim((string)($_POST['confirm_text'] ?? ''));
        if ($confirmText !== 'RESET') {
            $_SESSION['error'] = 'Reset cancelled. Please type RESET to confirm.';
        } else {
            $backupResult = superadmin_create_backup($connection, (string)($_SESSION['admin_username'] ?? 'superadmin'));
            if (!$backupResult['success']) {
                $_SESSION['error'] = 'Reset cancelled because backup failed: ' . $backupResult['message'];
            } else {
                $resetResult = superadmin_reset_system($connection);
                if ($resetResult['success']) {
                    $_SESSION['success'] = 'Reset completed. Safety backup: ' . ($backupResult['filename'] ?? 'created');
                } else {
                    $_SESSION['error'] = $resetResult['message'];
                }
            }
        }
    } elseif ($action === 'restore_data') {
        $confirmText = trim((string)($_POST['confirm_text'] ?? ''));
        if ($confirmText !== 'RESTORE') {
            $_SESSION['error'] = 'Restore cancelled. Please type RESTORE to confirm.';
        } else {
            $selectedBackup = trim((string)($_POST['backup_file'] ?? ''));
            $result = superadmin_restore_backup($connection, $selectedBackup);
            $_SESSION[$result['success'] ? 'success' : 'error'] = $result['message'];
        }
    } else {
        $_SESSION['error'] = 'Invalid superadmin action.';
    }
} catch (Exception $exception) {
    $_SESSION['error'] = 'Superadmin action failed: ' . $exception->getMessage();
}

header('Location: index.php');
exit();
