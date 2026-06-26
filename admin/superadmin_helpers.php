<?php
require_once __DIR__ . '/auth.php';

function superadmin_project_root(): string
{
    return realpath(__DIR__ . '/..') ?: dirname(__DIR__);
}

function superadmin_backup_directory(): string
{
    $config = superadmin_config();
    $backupDirectory = $config['backup_directory'] ?? (__DIR__ . '/../private_backups/system');
    $backupDirectory = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, (string)$backupDirectory);

    if (!is_dir($backupDirectory)) {
        mkdir($backupDirectory, 0750, true);
    }

    $htaccessPath = $backupDirectory . DIRECTORY_SEPARATOR . '.htaccess';
    if (!file_exists($htaccessPath)) {
        $rules = "Require all denied\n";
        $rules .= "<IfModule !mod_authz_core.c>\n";
        $rules .= "Deny from all\n";
        $rules .= "</IfModule>\n";
        file_put_contents($htaccessPath, $rules);
    }

    $indexPath = $backupDirectory . DIRECTORY_SEPARATOR . 'index.php';
    if (!file_exists($indexPath)) {
        file_put_contents($indexPath, "<?php http_response_code(403); exit('Forbidden');");
    }

    return realpath($backupDirectory) ?: $backupDirectory;
}

function superadmin_normalize_relative_path(string $path): string
{
    $normalized = trim(str_replace('\\', '/', $path), '/');
    return $normalized;
}

function superadmin_table_exists(mysqli $connection, string $table): bool
{
    $escapedTable = $connection->real_escape_string($table);
    $result = $connection->query("SHOW TABLES LIKE '{$escapedTable}'");
    return $result && $result->num_rows > 0;
}

function superadmin_current_database_name(mysqli $connection): string
{
    $result = $connection->query("SELECT DATABASE() AS db");
    if ($result && $row = $result->fetch_assoc()) {
        return (string)($row['db'] ?? '');
    }

    return '';
}

function superadmin_all_base_tables(mysqli $connection): array
{
    $tables = [];
    $result = $connection->query("SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'");
    if (!$result) {
        return $tables;
    }

    while ($row = $result->fetch_row()) {
        if (!isset($row[0])) {
            continue;
        }
        $tableName = trim((string)$row[0]);
        if ($tableName !== '') {
            $tables[] = $tableName;
        }
    }

    return array_values(array_unique($tables));
}

function superadmin_excluded_tables(): array
{
    $config = superadmin_config();
    $excluded = $config['excluded_tables'] ?? [];
    if (!is_array($excluded)) {
        return [];
    }

    $normalized = [];
    foreach ($excluded as $table) {
        $name = trim((string)$table);
        if ($name !== '') {
            $normalized[$name] = true;
        }
    }

    return array_keys($normalized);
}

function superadmin_existing_tables(mysqli $connection): array
{
    $config = superadmin_config();
    $includeAllTables = isset($config['include_all_tables']) ? (bool)$config['include_all_tables'] : true;
    $excludedTables = superadmin_excluded_tables();

    if ($includeAllTables) {
        $tables = superadmin_all_base_tables($connection);
    } else {
        $tables = $config['data_tables'] ?? [];
        if (!is_array($tables)) {
            return [];
        }
    }

    $existing = [];
    foreach ($tables as $table) {
        $tableName = trim((string)$table);
        if ($tableName === '' || in_array($tableName, $excludedTables, true)) {
            continue;
        }

        if (superadmin_table_exists($connection, $tableName)) {
            $existing[] = $tableName;
        }
    }

    sort($existing);
    return array_values(array_unique($existing));
}

function superadmin_list_backups(): array
{
    $directory = superadmin_backup_directory();
    $files = glob($directory . DIRECTORY_SEPARATOR . '*.json') ?: [];

    usort($files, static function ($a, $b) {
        return filemtime($b) <=> filemtime($a);
    });

    $backups = [];
    foreach ($files as $file) {
        $name = basename($file);
        $baseName = pathinfo($name, PATHINFO_FILENAME);

        $backups[] = [
            'filename' => $name,
            'created_at' => date('Y-m-d H:i:s', filemtime($file)),
            'size_bytes' => filesize($file),
            'has_upload_snapshot' => is_dir($directory . DIRECTORY_SEPARATOR . $baseName . '_uploads'),
        ];
    }

    return $backups;
}

function superadmin_prune_old_backups(): void
{
    $config = superadmin_config();
    $maxBackups = isset($config['max_backups']) ? (int)$config['max_backups'] : 25;
    if ($maxBackups < 1) {
        return;
    }

    $directory = superadmin_backup_directory();
    $files = glob($directory . DIRECTORY_SEPARATOR . '*.json') ?: [];

    usort($files, static function ($a, $b) {
        return filemtime($b) <=> filemtime($a);
    });

    if (count($files) <= $maxBackups) {
        return;
    }

    $toDelete = array_slice($files, $maxBackups);
    foreach ($toDelete as $file) {
        $baseName = pathinfo(basename($file), PATHINFO_FILENAME);
        @unlink($file);

        $snapshotDir = $directory . DIRECTORY_SEPARATOR . $baseName . '_uploads';
        if (is_dir($snapshotDir)) {
            superadmin_delete_directory($snapshotDir);
        }
    }
}

function superadmin_delete_directory(string $directory): bool
{
    if (!is_dir($directory)) {
        return true;
    }

    $items = scandir($directory);
    if ($items === false) {
        return false;
    }

    foreach ($items as $item) {
        if ($item === '.' || $item === '..') {
            continue;
        }

        $path = $directory . DIRECTORY_SEPARATOR . $item;
        if (is_dir($path)) {
            superadmin_delete_directory($path);
        } else {
            @unlink($path);
        }
    }

    return @rmdir($directory);
}

function superadmin_clear_directory_contents(string $directory): void
{
    if (!is_dir($directory)) {
        return;
    }

    $items = scandir($directory);
    if ($items === false) {
        return;
    }

    foreach ($items as $item) {
        if ($item === '.' || $item === '..') {
            continue;
        }

        $path = $directory . DIRECTORY_SEPARATOR . $item;
        if (is_dir($path)) {
            superadmin_delete_directory($path);
        } else {
            @unlink($path);
        }
    }
}

function superadmin_copy_directory(string $source, string $destination): void
{
    if (!is_dir($source)) {
        return;
    }

    if (!is_dir($destination)) {
        mkdir($destination, 0750, true);
    }

    $items = scandir($source);
    if ($items === false) {
        return;
    }

    foreach ($items as $item) {
        if ($item === '.' || $item === '..') {
            continue;
        }

        $sourcePath = $source . DIRECTORY_SEPARATOR . $item;
        $destinationPath = $destination . DIRECTORY_SEPARATOR . $item;

        if (is_dir($sourcePath)) {
            superadmin_copy_directory($sourcePath, $destinationPath);
        } else {
            @copy($sourcePath, $destinationPath);
        }
    }
}

function superadmin_clear_runtime_cache(): void
{
    $projectRoot = superadmin_project_root();
    $cacheDirectories = [
        $projectRoot . DIRECTORY_SEPARATOR . 'cache',
        $projectRoot . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'cache',
    ];

    foreach ($cacheDirectories as $cacheDirectory) {
        if (!is_dir($cacheDirectory)) {
            continue;
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($cacheDirectory, FilesystemIterator::SKIP_DOTS)
        );

        foreach ($iterator as $item) {
            if ($item->isFile() && strtolower($item->getExtension()) === 'cache') {
                @unlink($item->getPathname());
            }
        }
    }
}

function superadmin_create_backup(mysqli $connection, string $createdBy): array
{
    $tables = superadmin_existing_tables($connection);
    if (empty($tables)) {
        return ['success' => false, 'message' => 'No tables found in current database for backup.'];
    }

    $backupDirectory = superadmin_backup_directory();
    $backupId = 'system_backup_' . date('Ymd_His');
    $backupFile = $backupDirectory . DIRECTORY_SEPARATOR . $backupId . '.json';
    $uploadSnapshotRoot = $backupDirectory . DIRECTORY_SEPARATOR . $backupId . '_uploads';
    $databaseName = superadmin_current_database_name($connection);

    $tablesData = [];
    foreach ($tables as $table) {
        $safeTable = str_replace('`', '``', $table);
        $result = $connection->query("SELECT * FROM `{$safeTable}`");
        if (!$result) {
            return ['success' => false, 'message' => 'Backup failed on table: ' . $table];
        }

        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        $tablesData[$table] = $rows;
    }

    $config = superadmin_config();
    $uploadDirectories = is_array($config['upload_directories'] ?? null) ? $config['upload_directories'] : [];
    $projectRoot = superadmin_project_root();
    $includedUploads = [];

    foreach ($uploadDirectories as $relativeDir) {
        $normalized = superadmin_normalize_relative_path((string)$relativeDir);
        if ($normalized === '') {
            continue;
        }

        $sourcePath = $projectRoot . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $normalized);
        if (!is_dir($sourcePath)) {
            continue;
        }

        $destinationPath = $uploadSnapshotRoot . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $normalized);
        superadmin_copy_directory($sourcePath, $destinationPath);
        $includedUploads[] = $normalized;
    }

    $payload = [
        'meta' => [
            'version' => 1,
            'created_at' => gmdate('c'),
            'created_by' => $createdBy,
            'database' => $databaseName,
            'tables_count' => count($tables),
            'upload_directories' => $includedUploads,
        ],
        'tables' => $tablesData,
    ];

    $json = json_encode($payload, JSON_PRETTY_PRINT);
    if ($json === false) {
        return ['success' => false, 'message' => 'Failed to encode backup payload.'];
    }

    if (file_put_contents($backupFile, $json) === false) {
        return ['success' => false, 'message' => 'Failed to write backup file.'];
    }

    superadmin_prune_old_backups();

    return [
        'success' => true,
        'message' => 'Backup created successfully: ' . basename($backupFile),
        'filename' => basename($backupFile),
    ];
}

function superadmin_reset_system(mysqli $connection): array
{
    $tables = superadmin_existing_tables($connection);
    if (empty($tables)) {
        return ['success' => false, 'message' => 'No tables found in current database for reset.'];
    }

    $connection->query('SET FOREIGN_KEY_CHECKS = 0');
    try {
        foreach ($tables as $table) {
            $safeTable = str_replace('`', '``', $table);
            $truncateOk = $connection->query("TRUNCATE TABLE `{$safeTable}`");
            if (!$truncateOk) {
                $deleteOk = $connection->query("DELETE FROM `{$safeTable}`");
                if (!$deleteOk) {
                    return ['success' => false, 'message' => 'Reset failed on table: ' . $table];
                }
            }
        }
    } finally {
        $connection->query('SET FOREIGN_KEY_CHECKS = 1');
    }

    $config = superadmin_config();
    $uploadDirectories = is_array($config['upload_directories'] ?? null) ? $config['upload_directories'] : [];
    $projectRoot = superadmin_project_root();

    foreach ($uploadDirectories as $relativeDir) {
        $normalized = superadmin_normalize_relative_path((string)$relativeDir);
        if ($normalized === '') {
            continue;
        }

        $absolutePath = $projectRoot . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $normalized);
        superadmin_clear_directory_contents($absolutePath);
    }

    superadmin_clear_runtime_cache();

    return [
        'success' => true,
        'message' => 'Full database data reset completed successfully.',
    ];
}

function superadmin_restore_backup(mysqli $connection, string $backupFilename = ''): array
{
    $backupDirectory = superadmin_backup_directory();
    $availableBackups = superadmin_list_backups();

    if (empty($availableBackups)) {
        return ['success' => false, 'message' => 'No backups available to restore.'];
    }

    $targetFilename = trim($backupFilename);
    if ($targetFilename === '') {
        $targetFilename = $availableBackups[0]['filename'];
    }

    $targetFilename = basename($targetFilename);
    $backupFilePath = $backupDirectory . DIRECTORY_SEPARATOR . $targetFilename;
    if (!is_file($backupFilePath)) {
        return ['success' => false, 'message' => 'Selected backup file not found.'];
    }

    $payloadRaw = file_get_contents($backupFilePath);
    if ($payloadRaw === false) {
        return ['success' => false, 'message' => 'Failed to read backup file.'];
    }

    $payload = json_decode($payloadRaw, true);
    if (!is_array($payload) || !isset($payload['tables']) || !is_array($payload['tables'])) {
        return ['success' => false, 'message' => 'Invalid backup format.'];
    }

    $connection->query('SET FOREIGN_KEY_CHECKS = 0');
    try {
        foreach ($payload['tables'] as $table => $rows) {
            if (!superadmin_table_exists($connection, (string)$table)) {
                continue;
            }

            $safeTable = str_replace('`', '``', (string)$table);
            $connection->query("TRUNCATE TABLE `{$safeTable}`");

            if (!is_array($rows)) {
                continue;
            }

            foreach ($rows as $row) {
                if (!is_array($row) || empty($row)) {
                    continue;
                }

                $columns = array_keys($row);
                $escapedColumns = [];
                $values = [];

                foreach ($columns as $column) {
                    $escapedColumns[] = '`' . str_replace('`', '``', (string)$column) . '`';
                    $value = $row[$column];
                    if ($value === null) {
                        $values[] = 'NULL';
                    } else {
                        $values[] = "'" . $connection->real_escape_string((string)$value) . "'";
                    }
                }

                $insertSql = "INSERT INTO `{$safeTable}` (" . implode(', ', $escapedColumns) . ") VALUES (" . implode(', ', $values) . ")";
                if (!$connection->query($insertSql)) {
                    return ['success' => false, 'message' => 'Restore failed on table: ' . $table];
                }
            }
        }
    } finally {
        $connection->query('SET FOREIGN_KEY_CHECKS = 1');
    }

    $snapshotDirectory = $backupDirectory . DIRECTORY_SEPARATOR . pathinfo($targetFilename, PATHINFO_FILENAME) . '_uploads';
    if (is_dir($snapshotDirectory)) {
        $config = superadmin_config();
        $uploadDirectories = is_array($config['upload_directories'] ?? null) ? $config['upload_directories'] : [];
        $projectRoot = superadmin_project_root();

        foreach ($uploadDirectories as $relativeDir) {
            $normalized = superadmin_normalize_relative_path((string)$relativeDir);
            if ($normalized === '') {
                continue;
            }

            $targetPath = $projectRoot . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $normalized);
            if (!is_dir($targetPath)) {
                mkdir($targetPath, 0750, true);
            }

            superadmin_clear_directory_contents($targetPath);

            $snapshotPath = $snapshotDirectory . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $normalized);
            if (is_dir($snapshotPath)) {
                superadmin_copy_directory($snapshotPath, $targetPath);
            }
        }
    }

    superadmin_clear_runtime_cache();

    return [
        'success' => true,
        'message' => 'Backup restored successfully: ' . $targetFilename,
    ];
}
