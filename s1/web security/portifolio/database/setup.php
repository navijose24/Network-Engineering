<?php
/**
 * Automated Database Setup & Migration Script
 * Can be executed via CLI: `php database/setup.php`
 * Or via web browser: `/database/setup.php`
 */

define('APP_SETUP', true);
require_once __DIR__ . '/../config/db.php';

$isCli = (php_sapi_name() === 'cli');

function output_msg(string $msg, string $type = 'info', bool $isCli = false): void {
    if ($isCli) {
        $prefix = match ($type) {
            'success' => '[SUCCESS] ',
            'error'   => '[ERROR] ',
            'warning' => '[WARN] ',
            default   => '[INFO] '
        };
        echo $prefix . strip_tags($msg) . PHP_EOL;
    } else {
        $color = match ($type) {
            'success' => '#4ADE80',
            'error'   => '#F87171',
            'warning' => '#FBBF24',
            default   => '#E7D6C1'
        };
        echo "<div style='margin: 8px 0; font-family: monospace; color: {$color}; background: #1E1E22; padding: 10px 14px; border-left: 4px solid {$color};'>{$msg}</div>";
    }
}

$action = $_GET['action'] ?? ($isCli ? 'run' : 'preview');

if (!$isCli): ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Setup & Migration // [SYS.INSTALL]</title>
    <style>
        :root {
            --clr-coal: #0F0F10;
            --clr-graphite: #1E1E22;
            --clr-sand: #E7D6C1;
            --clr-violet: #6E5A73;
            --clr-terracotta: #E07A4F;
            --clr-stone: #A89C8F;
        }
        body {
            background-color: var(--clr-coal);
            color: var(--clr-sand);
            font-family: 'Space Mono', 'Courier New', monospace;
            padding: 40px 20px;
            display: flex;
            justify-content: center;
        }
        .container {
            width: 100%;
            max-width: 760px;
            background: var(--clr-graphite);
            border: 2px solid var(--clr-stone);
            padding: 30px;
            box-shadow: 8px 8px 0 var(--clr-terracotta);
        }
        h1 {
            margin-top: 0;
            font-size: 1.5rem;
            color: var(--clr-sand);
            border-bottom: 2px solid var(--clr-stone);
            padding-bottom: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .btn {
            display: inline-block;
            background: var(--clr-terracotta);
            color: #000;
            text-decoration: none;
            padding: 12px 24px;
            font-weight: bold;
            font-size: 0.95rem;
            border: 2px solid var(--clr-sand);
            box-shadow: 4px 4px 0 var(--clr-coal);
            cursor: pointer;
            margin-top: 20px;
        }
        .btn:hover {
            background: var(--clr-sand);
            color: var(--clr-coal);
        }
        .meta-box {
            background: var(--clr-coal);
            padding: 16px;
            margin: 20px 0;
            border: 1px dashed var(--clr-stone);
            font-size: 0.9rem;
            line-height: 1.6;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>[0x00] // DATABASE SETUP & MIGRATION <span style="font-size: 0.8rem; color: var(--clr-stone);">v1.0</span></h1>
<?php endif;

if ($action === 'run') {
    try {
        output_msg("Attempting connection to MySQL server at " . DB_HOST . ":" . DB_PORT . "...", 'info', $isCli);

        // Connect without dbname first to allow creation of database
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";charset=" . DB_CHARSET,
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
            ]
        );

        output_msg("Connection established. Creating database `" . DB_NAME . "` if not exists...", 'info', $isCli);
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $pdo->exec("USE `" . DB_NAME . "`");
        output_msg("Database `" . DB_NAME . "` selected.", 'success', $isCli);

        // Execute Schema
        $schemaPath = __DIR__ . '/schema.sql';
        if (file_exists($schemaPath)) {
            output_msg("Loading schema from schema.sql...", 'info', $isCli);
            $schemaSql = file_get_contents($schemaPath);
            $pdo->exec($schemaSql);
            output_msg("Database schema successfully created with all tables & constraints.", 'success', $isCli);
        } else {
            throw new Exception("schema.sql not found at {$schemaPath}");
        }

        // Execute Seed Data
        $seedPath = __DIR__ . '/seed.sql';
        if (file_exists($seedPath)) {
            output_msg("Loading seed content from seed.sql...", 'info', $isCli);
            $seedSql = file_get_contents($seedPath);
            $pdo->exec($seedSql);
            output_msg("Portfolio records seeded successfully (Projects, Skills, Talks, Achievements, Articles, Profile).", 'success', $isCli);
        }

        // Verify Default Admin
        $stmt = $pdo->query("SELECT id, username, email FROM admins WHERE username = 'admin' LIMIT 1");
        $admin = $stmt->fetch();
        if ($admin) {
            output_msg("Default admin verified: <strong>admin</strong> (Password: <code>Admin@12345</code>)", 'success', $isCli);
        }

        output_msg("ALL OPERATIONS COMPLETED SUCCESSFULLY.", 'success', $isCli);

        if (!$isCli) {
            echo "<div style='margin-top: 25px;'>
                <a href='/' class='btn'>VIEW PORTFOLIO &rarr;</a>
                <a href='/admin/login.php' class='btn' style='background: var(--clr-violet); color: var(--clr-sand); margin-left: 10px;'>ADMIN LOGIN &rarr;</a>
            </div>";
        }
    } catch (Throwable $e) {
        output_msg("Migration Failed: " . $e->getMessage(), 'error', $isCli);
        if (!$isCli) {
            echo "<p style='color: var(--clr-stone); font-size: 0.85rem; margin-top: 15px;'>Check that MySQL is running on <code>" . DB_HOST . ":" . DB_PORT . "</code> with user <code>" . DB_USER . "</code>. You can configure credentials in <code>.env</code> or <code>config/db.php</code>.</p>";
            echo "<a href='?action=run' class='btn' style='background: var(--clr-stone); color: #fff;'>RETRY MIGRATION</a>";
        }
    }
} else {
    // Preview Mode
    if (!$isCli) {
        echo "<p>This utility initializes the portfolio MySQL database, runs relational table schemas, and imports all initial projects, achievements, and blog data.</p>";
        echo "<div class='meta-box'>
            <strong>Database Target:</strong> " . DB_NAME . "<br>
            <strong>Host:</strong> " . DB_HOST . ":" . DB_PORT . "<br>
            <strong>User:</strong> " . DB_USER . "<br>
            <strong>Default Admin:</strong> admin / Admin@12345
        </div>";
        echo "<a href='?action=run' class='btn'>START MIGRATION &rarr;</a>";
    }
}

if (!$isCli): ?>
</div>
</body>
</html>
<?php endif;
