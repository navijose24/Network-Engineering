<?php
/**
 * Admin Profile Management
 */

$pageTitle = 'Profile & Bio Management';
require_once __DIR__ . '/includes/admin_header.php';

$profile = get_profile_data();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify()) {
        set_flash('danger', 'Security session validation failed.');
        header('Location: /admin/profile.php');
        exit;
    }

    $name = trim($_POST['name'] ?? '');
    $tagline = trim($_POST['tagline'] ?? '');
    $intro = trim($_POST['intro'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $linkedin = trim($_POST['linkedin'] ?? '');
    $github = trim($_POST['github'] ?? '');
    $instagram = trim($_POST['instagram'] ?? '');
    $resumeUrl = trim($_POST['resume_url'] ?? '');
    $avatarPath = $profile['avatar'] ?? '';

    // Handle avatar upload if provided
    if (!empty($_FILES['avatar_file']['name'])) {
        try {
            $avatarPath = handle_file_upload($_FILES['avatar_file'], 'avatars');
        } catch (Exception $e) {
            set_flash('danger', 'Avatar upload error: ' . $e->getMessage());
            header('Location: /admin/profile.php');
            exit;
        }
    }

    $updateData = [
        'id' => $profile['id'] ?? 1,
        'name' => $name,
        'tagline' => $tagline,
        'intro' => $intro,
        'email' => $email,
        'phone' => $phone,
        'linkedin' => $linkedin,
        'github' => $github,
        'instagram' => $instagram,
        'avatar' => $avatarPath,
        'resume_url' => $resumeUrl
    ];

    if (update_profile_data($updateData)) {
        set_flash('success', 'Profile coordinates and biographical data updated successfully.');
    } else {
        set_flash('danger', 'Failed to update profile record in database.');
    }

    header('Location: /admin/profile.php');
    exit;
}
?>

<div class="page-header">
    <div>
        <h1 class="page-title">[0x01] // PROFILE COORDINATES</h1>
        <p style="font-family: var(--font-mono); font-size: 0.85rem; color: var(--clr-stone); margin-top: 4px;">
            Manage primary identity credentials, contact endpoints, and personal branding copy.
        </p>
    </div>
</div>

<div class="form-card cut-corner">
    <form method="POST" action="/admin/profile.php" enctype="multipart/form-data">
        <?= csrf_field() ?>

        <div class="form-grid">
            <div class="form-group">
                <label class="form-label" for="name">&gt; FULL IDENTITY / NAME *</label>
                <input type="text" id="name" name="name" class="form-control" value="<?= e($profile['name']) ?>" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="tagline">&gt; TAGLINE / SPECIALTY *</label>
                <input type="text" id="tagline" name="tagline" class="form-control" value="<?= e($profile['tagline']) ?>" required>
            </div>

            <div class="form-group form-full">
                <label class="form-label" for="intro">&gt; BIOGRAPHICAL SUMMARY &amp; PHILOSOPHY *</label>
                <textarea id="intro" name="intro" class="form-control" rows="5" required><?= e($profile['intro']) ?></textarea>
            </div>

            <div class="form-group">
                <label class="form-label" for="email">&gt; PRIMARY EMAIL ENDPOINT *</label>
                <input type="email" id="email" name="email" class="form-control" value="<?= e($profile['email']) ?>" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="phone">&gt; TELEPHONY LINK</label>
                <input type="text" id="phone" name="phone" class="form-control" value="<?= e($profile['phone']) ?>">
            </div>

            <div class="form-group">
                <label class="form-label" for="github">&gt; GITHUB URL / HANDLE</label>
                <input type="text" id="github" name="github" class="form-control" value="<?= e($profile['github']) ?>">
            </div>

            <div class="form-group">
                <label class="form-label" for="linkedin">&gt; LINKEDIN URL</label>
                <input type="text" id="linkedin" name="linkedin" class="form-control" value="<?= e($profile['linkedin']) ?>">
            </div>

            <div class="form-group">
                <label class="form-label" for="instagram">&gt; INSTAGRAM HANDLE</label>
                <input type="text" id="instagram" name="instagram" class="form-control" value="<?= e($profile['instagram']) ?>">
            </div>

            <div class="form-group">
                <label class="form-label" for="resume_url">&gt; RESUME FILE LINK (URL)</label>
                <input type="text" id="resume_url" name="resume_url" class="form-control" value="<?= e($profile['resume_url']) ?>">
            </div>

            <div class="form-group form-full" style="border-top: 1px dashed var(--clr-stone); padding-top: 20px;">
                <label class="form-label" for="avatar_file">&gt; AVATAR IMAGE UPLOAD (JPG, PNG, WEBP)</label>
                <input type="file" id="avatar_file" name="avatar_file" class="form-control" accept="image/*" data-preview="avatar-preview">
                
                <?php if (!empty($profile['avatar'])): ?>
                    <div style="margin-top: 12px; display: flex; align-items: center; gap: 16px;">
                        <img id="avatar-preview" src="<?= e($profile['avatar']) ?>" alt="Current Avatar" style="width: 80px; height: 80px; object-fit: cover; border: 2px solid var(--clr-stone);">
                        <span class="mono-data" style="color: var(--clr-stone);">Current file: <?= e($profile['avatar']) ?></span>
                    </div>
                <?php else: ?>
                    <img id="avatar-preview" src="" alt="Avatar Preview" style="width: 80px; height: 80px; object-fit: cover; border: 2px solid var(--clr-stone); display: none; margin-top: 12px;">
                <?php endif; ?>
            </div>
        </div>

        <div style="margin-top: 30px; display: flex; justify-content: flex-end;">
            <button type="submit" class="btn btn-primary">
                SAVE PROFILE CHANGES &rarr;
            </button>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
