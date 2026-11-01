<?php
/**
 * Admin Reusable Header Component
 * Enforces authentication guard and renders consistent brutalist admin navigation
 */

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db_queries.php';

// Route protection: Must be logged in as admin
require_admin();

$adminUser = get_admin_user();
$unreadCount = get_unread_contacts_count();
$currentScript = basename($_SERVER['SCRIPT_NAME'] ?? '');
$flash = get_flash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'Admin Console') ?> // navaneetha</title>
    
    <!-- Design Tokens & Admin Stylesheet -->
    <link rel="stylesheet" href="/assets/css/admin.css">
    
    <!-- Favicon -->
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🛡️</text></svg>">
</head>
<body>

<div class="admin-layout">
    <!-- Admin Sidebar -->
    <aside class="admin-sidebar">
        <div class="sidebar-header">
            <div class="sidebar-brand">
                <span>navaneetha</span>
                <span style="font-size: 0.72rem; color: var(--clr-terracotta); border: 1px solid var(--clr-terracotta); padding: 2px 6px;">
                    ROOT
                </span>
            </div>
            <div style="font-family: var(--font-mono); font-size: 0.75rem; color: var(--clr-stone); margin-top: 6px;">
                SESSION // SECURE
            </div>
        </div>

        <ul class="sidebar-nav">
            <li class="sidebar-item <?= $currentScript === 'index.php' ? 'active' : '' ?>">
                <a href="/admin/index.php">
                    <span>[0x00] Dashboard</span>
                </a>
            </li>
            <li class="sidebar-item <?= $currentScript === 'profile.php' ? 'active' : '' ?>">
                <a href="/admin/profile.php">
                    <span>[0x01] Profile &amp; Bio</span>
                </a>
            </li>
            <li class="sidebar-item <?= $currentScript === 'projects.php' ? 'active' : '' ?>">
                <a href="/admin/projects.php">
                    <span>[0x02] Projects</span>
                </a>
            </li>
            <li class="sidebar-item <?= $currentScript === 'skills.php' ? 'active' : '' ?>">
                <a href="/admin/skills.php">
                    <span>[0x03] Skills Matrix</span>
                </a>
            </li>
            <li class="sidebar-item <?= $currentScript === 'achievements.php' ? 'active' : '' ?>">
                <a href="/admin/achievements.php">
                    <span>[0x04] Achievements</span>
                </a>
            </li>
            <li class="sidebar-item <?= $currentScript === 'talks.php' ? 'active' : '' ?>">
                <a href="/admin/talks.php">
                    <span>[0x05] Talks &amp; Events</span>
                </a>
            </li>
            <li class="sidebar-item <?= $currentScript === 'articles.php' ? 'active' : '' ?>">
                <a href="/admin/articles.php">
                    <span>[0x06] Blog &amp; Articles</span>
                </a>
            </li>
            <li class="sidebar-item <?= $currentScript === 'creative.php' ? 'active' : '' ?>">
                <a href="/admin/creative.php">
                    <span>[0x07] Creative Works</span>
                </a>
            </li>
            <li class="sidebar-item <?= $currentScript === 'messages.php' ? 'active' : '' ?>">
                <a href="/admin/messages.php">
                    <span>[0x08] Inquiries</span>
                    <?php if ($unreadCount > 0): ?>
                        <span class="badge-unread"><?= $unreadCount ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <li class="sidebar-item <?= $currentScript === 'settings.php' ? 'active' : '' ?>">
                <a href="/admin/settings.php">
                    <span>[0x09] Site Settings</span>
                </a>
            </li>
        </ul>

        <div style="padding: 24px; border-top: 1px solid var(--clr-stone-dark);">
            <a href="/admin/logout.php" class="btn btn-danger btn-sm" style="width: 100%; justify-content: center;" data-confirm="Terminate current admin session?">
                LOGOUT // 0x00
            </a>
        </div>
    </aside>

    <!-- Main Content Panel -->
    <div class="admin-main">
        <header class="admin-topbar">
            <div class="topbar-left">
                <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: #22C55E;"></span>
                <span>AUTHENTICATED: <strong><?= e($adminUser['username'] ?? 'admin') ?></strong></span>
                <span style="color: var(--clr-stone);">[<?= e($adminUser['email'] ?? '') ?>]</span>
            </div>

            <div class="topbar-right">
                <a href="/" target="_blank" rel="noopener noreferrer" class="btn btn-secondary btn-sm">
                    VIEW LIVE SITE &nearr;
                </a>
            </div>
        </header>

        <main class="admin-content">
            <?php if ($flash): ?>
                <div class="alert alert-<?= e($flash['type']) ?>" role="alert">
                    <?= e($flash['message']) ?>
                </div>
            <?php endif; ?>
