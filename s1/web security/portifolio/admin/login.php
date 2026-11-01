<?php
/**
 * Admin Secure Authentication Gateway
 * Password verification via password_verify(), session regeneration & CSRF validation
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

// Redirect if already authenticated
if (is_admin_logged_in()) {
    header('Location: /admin/index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify()) {
        $error = 'Security session expired. Please refresh the page.';
    } else {
        $identifier = trim($_POST['identifier'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($identifier) || empty($password)) {
            $error = 'Please provide both username/email and access key.';
        } else {
            // Attempt authentication
            if (admin_login($identifier, $password)) {
                set_flash('success', 'Admin clearance verified. Welcome to system console.');
                header('Location: /admin/index.php');
                exit;
            } else {
                $error = 'Invalid credentials. Access attempt has been recorded.';
            }
        }
    }
}

$flash = get_flash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Authentication Gateway // [AUTH_REQUIRED]</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 24px;
        }
        .login-card {
            width: 100%;
            max-width: 440px;
            background: var(--clr-graphite);
            border: 2px solid var(--clr-sand);
            box-shadow: 8px 8px 0 var(--clr-terracotta);
            padding: 40px 32px;
            position: relative;
        }
        .login-header {
            border-bottom: 2px solid var(--clr-stone);
            padding-bottom: 16px;
            margin-bottom: 28px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
    </style>
</head>
<body>

<canvas id="matrix-canvas"></canvas>

<div class="login-card cut-corner" style="z-index: 10;">
    <div class="login-header">
        <div>
            <div style="font-family: var(--font-pixel); font-size: 1.2rem; color: var(--clr-sand);">
                tob.zico
            </div>
            <div style="font-family: var(--font-mono); font-size: 0.72rem; color: var(--clr-stone); margin-top: 4px;">
                RESTRICTED CONSOLE // 0x00
            </div>
        </div>
        <span class="binary-tag accent">ROOT</span>
    </div>

    <?php if ($flash): ?>
        <div style="background: rgba(224, 122, 79, 0.15); border-left: 3px solid var(--clr-terracotta); color: var(--clr-sand); padding: 10px 14px; margin-bottom: 20px; font-family: var(--font-mono); font-size: 0.85rem;">
            <?= e($flash['message']) ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div style="background: rgba(239, 68, 68, 0.15); border-left: 3px solid #EF4444; color: #FCA5A5; padding: 10px 14px; margin-bottom: 20px; font-family: var(--font-mono); font-size: 0.85rem;">
            <?= e($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="/admin/login.php" novalidate>
        <?= csrf_field() ?>

        <div class="form-group" style="margin-bottom: 20px;">
            <label class="form-label" for="identifier">&gt; USERNAME OR EMAIL</label>
            <input type="text" id="identifier" name="identifier" class="form-control" placeholder="admin or admin@portfolio.local" required autofocus autocomplete="username">
        </div>

        <div class="form-group" style="margin-bottom: 28px;">
            <label class="form-label" for="password">&gt; PASSWORD ACCESS KEY</label>
            <input type="password" id="password" name="password" class="form-control" placeholder="••••••••••••" required autocomplete="current-password">
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%; margin-bottom: 16px;">
            VERIFY CREDENTIALS &rarr;
        </button>

        <div style="display: flex; justify-content: space-between; align-items: center; font-family: var(--font-mono); font-size: 0.78rem; color: var(--clr-stone);">
            <a href="/" style="color: var(--clr-stone);">&larr; Return to Portfolio</a>
            <span>ENCRYPT: BCRYPT</span>
        </div>
    </form>
</div>

<script src="/assets/js/app.js"></script>
</body>
</html>
