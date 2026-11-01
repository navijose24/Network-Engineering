<?php
/**
 * Admin Website Settings Configuration
 */

$pageTitle = 'Website Settings';
require_once __DIR__ . '/includes/admin_header.php';

$settings = get_site_settings();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify()) {
        set_flash('danger', 'Security verification failed.');
        header('Location: /admin/settings.php');
        exit;
    }

    $keys = [
        'site_name',
        'tagline',
        'status_badge',
        'binary_hero_sub',
        'contact_email',
        'contact_phone',
        'github_url',
        'linkedin_url',
        'instagram_url',
        'footer_text'
    ];

    foreach ($keys as $k) {
        if (isset($_POST[$k])) {
            update_site_setting($k, trim($_POST[$k]));
        }
    }

    set_flash('success', 'Site configuration settings updated immediately.');
    header('Location: /admin/settings.php');
    exit;
}
?>

<div class="page-header">
    <div>
        <h1 class="page-title">[0x09] // WEBSITE CONFIGURATION &amp; SETTINGS</h1>
        <p style="font-family: var(--font-mono); font-size: 0.85rem; color: var(--clr-stone); margin-top: 4px;">
            Global site variables, telemetry headers, binary accents, and metadata tokens.
        </p>
    </div>
</div>

<div class="form-card cut-corner">
    <form method="POST" action="/admin/settings.php">
        <?= csrf_field() ?>

        <div class="form-grid">
            <div class="form-group">
                <label class="form-label" for="site_name">&gt; SITE TITLE / BRAND LOGO *</label>
                <input type="text" id="site_name" name="site_name" class="form-control" value="<?= e($settings['site_name'] ?? 'Navaneetha Jose // Portfolio') ?>" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="status_badge">&gt; OPERATIONAL STATUS BADGE</label>
                <input type="text" id="status_badge" name="status_badge" class="form-control" value="<?= e($settings['status_badge'] ?? 'SYSTEM: ONLINE // ACCEPTING CONTRACTS') ?>">
            </div>

            <div class="form-group form-full">
                <label class="form-label" for="binary_hero_sub">&gt; BINARY MOTTO / SUBTITLE</label>
                <input type="text" id="binary_hero_sub" name="binary_hero_sub" class="form-control" value="<?= e($settings['binary_hero_sub'] ?? '01000100 01000101 01010110 // ENGINEERED FOR IMPACT') ?>">
            </div>

            <div class="form-group">
                <label class="form-label" for="contact_email">&gt; DISPATCH NOTIFICATION EMAIL</label>
                <input type="email" id="contact_email" name="contact_email" class="form-control" value="<?= e($settings['contact_email'] ?? 'navaneethajose004@gmail.com') ?>">
            </div>

            <div class="form-group">
                <label class="form-label" for="contact_phone">&gt; CONTACT TELEPHONY</label>
                <input type="text" id="contact_phone" name="contact_phone" class="form-control" value="<?= e($settings['contact_phone'] ?? '+91 8281252386') ?>">
            </div>

            <div class="form-group">
                <label class="form-label" for="github_url">&gt; GITHUB REPOSITORY LINK</label>
                <input type="url" id="github_url" name="github_url" class="form-control" value="<?= e($settings['github_url'] ?? 'https://github.com/navijose24') ?>">
            </div>

            <div class="form-group">
                <label class="form-label" for="linkedin_url">&gt; LINKEDIN LINK</label>
                <input type="url" id="linkedin_url" name="linkedin_url" class="form-control" value="<?= e($settings['linkedin_url'] ?? 'https://linkedin.com/in/navaneetha-jose') ?>">
            </div>

            <div class="form-group form-full">
                <label class="form-label" for="footer_text">&gt; FOOTER COLOPHON</label>
                <input type="text" id="footer_text" name="footer_text" class="form-control" value="<?= e($settings['footer_text'] ?? 'BUILT OF 0s AND 1s. ALL RIGHTS RESERVED.') ?>">
            </div>
        </div>

        <div style="margin-top: 30px; display: flex; justify-content: flex-end;">
            <button type="submit" class="btn btn-primary">
                SAVE CONFIGURATION &rarr;
            </button>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
