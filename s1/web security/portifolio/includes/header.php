<?php
/**
 * Public Header Component
 */
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/db_queries.php';

$profile = get_profile_data();
$settings = get_site_settings();
$siteName = $settings['site_name'] ?? 'Navaneetha Jose // Portfolio';
$statusBadge = $settings['status_badge'] ?? 'SYSTEM: ONLINE // ACCEPTING CONTRACTS';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($siteName) ?> - <?= e($profile['tagline']) ?></title>
    <meta name="description" content="<?= e(substr(strip_tags($profile['intro']), 0, 160)) ?>">
    
    <!-- Design Tokens & Stylesheet -->
    <link rel="stylesheet" href="/assets/css/style.css">
    
    <!-- Favicon -->
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>⚡</text></svg>">
</head>
<body>

<!-- Dynamic Binary Stream Background Canvas -->
<canvas id="matrix-canvas"></canvas>

<div class="content-wrapper">
    <!-- Top Persistent Navigation Bar -->
    <header class="site-header">
        <div class="container header-inner">
            <a href="/#hero" class="brand-logo" aria-label="Home">
                <span class="brand-text">tob.zico</span>
                <span class="brand-tag">0x01</span>
            </a>

            <nav aria-label="Main Navigation">
                <ul class="nav-links">
                    <li class="nav-item"><a href="/#about"><span class="index">01.</span> About</a></li>
                    <li class="nav-item"><a href="/#projects"><span class="index">02.</span> Projects</a></li>
                    <li class="nav-item"><a href="/#skills"><span class="index">03.</span> Skills</a></li>
                    <li class="nav-item"><a href="/#achievements"><span class="index">04.</span> Honors</a></li>
                    <li class="nav-item"><a href="/#talks"><span class="index">05.</span> Talks</a></li>
                    <li class="nav-item"><a href="/#articles"><span class="index">06.</span> Writing</a></li>
                    <li class="nav-item"><a href="/#creative"><span class="index">07.</span> Lab</a></li>
                    <li class="nav-item"><a href="/#contact"><span class="index">08.</span> Contact</a></li>
                </ul>
            </nav>

            <div class="nav-actions">
                <div class="binary-tag" title="System Operational Status">
                    <span class="live-indicator"></span>
                    <span class="mono-data">SYS.LIVE</span>
                </div>
                <a href="/admin/login.php" class="btn btn-secondary btn-sm" title="Restricted Admin Gateway">
                    [ADMIN]
                </a>
                <button class="mobile-menu-btn" aria-label="Toggle Navigation Menu" aria-expanded="false">
                    [MENU // 01]
                </button>
            </div>
        </div>
    </header>
