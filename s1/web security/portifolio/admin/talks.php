<?php
/**
 * Admin Talks & Speaking Sessions Management
 */

$pageTitle = 'Talks & Events Management';
require_once __DIR__ . '/includes/admin_header.php';

$action = $_GET['action'] ?? 'list';
$talkId = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// Handle DELETE
if ($action === 'delete' && $talkId > 0) {
    if (isset($_GET['csrf']) && hash_equals($_SESSION['csrf_token'] ?? '', $_GET['csrf'])) {
        if (delete_talk($talkId)) {
            set_flash('success', 'Talk record purged.');
        } else {
            set_flash('danger', 'Failed to purge talk.');
        }
    }
    header('Location: /admin/talks.php');
    exit;
}

// Handle CREATE / EDIT
if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($action, ['create', 'edit'])) {
    if (!csrf_verify()) {
        set_flash('danger', 'Security verification failed.');
        header('Location: /admin/talks.php');
        exit;
    }

    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $year = trim($_POST['year'] ?? '');
    $thumb = trim($_POST['video_thumbnail'] ?? '');
    $url = trim($_POST['video_url'] ?? '');
    $order = (int) ($_POST['display_order'] ?? 0);

    $savedId = save_talk([
        'id' => $talkId,
        'title' => $title,
        'description' => $description,
        'year' => $year,
        'video_thumbnail' => $thumb,
        'video_url' => $url,
        'display_order' => $order
    ]);

    if ($savedId > 0) {
        set_flash('success', 'Talk session recorded successfully.');
        header('Location: /admin/talks.php');
        exit;
    } else {
        set_flash('danger', 'Failed to record talk.');
    }
}

$talk = null;
if ($action === 'edit' && $talkId > 0) {
    $talk = get_talk_by_id($talkId);
    if (!$talk) {
        set_flash('danger', 'Talk session not found.');
        header('Location: /admin/talks.php');
        exit;
    }
}

$allTalks = get_talks_list();
?>

<div class="page-header">
    <div>
        <h1 class="page-title">[0x05] // TALKS &amp; SESSIONS</h1>
        <p style="font-family: var(--font-mono); font-size: 0.85rem; color: var(--clr-stone); margin-top: 4px;">
            Manage invited talks, workshops, and speaking engagements.
        </p>
    </div>

    <div>
        <?php if ($action === 'list'): ?>
            <a href="/admin/talks.php?action=create" class="btn btn-primary btn-sm">+ LOG NEW TALK</a>
        <?php else: ?>
            <a href="/admin/talks.php" class="btn btn-secondary btn-sm">&larr; BACK TO LIST</a>
        <?php endif; ?>
    </div>
</div>

<?php if ($action === 'create' || $action === 'edit'): ?>
    <div class="form-card cut-corner">
        <h2 style="font-size: 1.3rem; margin-bottom: 24px; color: var(--clr-sand);">
            <?= $action === 'create' ? '+ LOG NEW SPEAKING SESSION' : 'EDIT SESSION: ' . e($talk['title']) ?>
        </h2>

        <form method="POST" action="/admin/talks.php?action=<?= $action ?><?= $talkId ? '&id=' . $talkId : '' ?>">
            <?= csrf_field() ?>

            <div class="form-grid">
                <div class="form-group form-full">
                    <label class="form-label" for="title">&gt; SESSION TITLE *</label>
                    <input type="text" id="title" name="title" class="form-control" value="<?= e($talk['title'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="year">&gt; YEAR *</label>
                    <input type="text" id="year" name="year" class="form-control" value="<?= e($talk['year'] ?? date('Y')) ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="display_order">&gt; DISPLAY ORDER</label>
                    <input type="number" id="display_order" name="display_order" class="form-control" value="<?= (int) ($talk['display_order'] ?? 0) ?>">
                </div>

                <div class="form-group">
                    <label class="form-label" for="video_thumbnail">&gt; THUMBNAIL IMAGE URL</label>
                    <input type="text" id="video_thumbnail" name="video_thumbnail" class="form-control" placeholder="/talks/session.jpg" value="<?= e($talk['video_thumbnail'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label class="form-label" for="video_url">&gt; VIDEO RECORDING LINK</label>
                    <input type="url" id="video_url" name="video_url" class="form-control" placeholder="https://youtube.com/..." value="<?= e($talk['video_url'] ?? '') ?>">
                </div>

                <div class="form-group form-full">
                    <label class="form-label" for="description">&gt; SUMMARY OF TALK *</label>
                    <textarea id="description" name="description" class="form-control" rows="3" required><?= e($talk['description'] ?? '') ?></textarea>
                </div>
            </div>

            <div style="margin-top: 24px; display: flex; justify-content: flex-end; gap: 12px;">
                <a href="/admin/talks.php" class="btn btn-secondary">CANCEL</a>
                <button type="submit" class="btn btn-primary">SAVE SESSION &rarr;</button>
            </div>
        </form>
    </div>
<?php else: ?>
    <div class="table-card cut-corner">
        <div class="table-header">
            <span style="font-weight: bold; color: var(--clr-sand);">TALKS &amp; PRESENTATIONS (<?= count($allTalks) ?>)</span>
            <span class="binary-tag">LOGGED</span>
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th>YEAR</th>
                    <th>TOPIC TITLE</th>
                    <th>DESCRIPTION</th>
                    <th>ACTIONS</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($allTalks as $t): ?>
                    <tr>
                        <td style="font-family: var(--font-mono); color: var(--clr-terracotta); font-weight: bold;">
                            <?= e($t['year']) ?>
                        </td>
                        <td><strong><?= e($t['title']) ?></strong></td>
                        <td style="color: var(--clr-stone); font-size: 0.85rem; max-width: 400px;">
                            <?= e($t['description']) ?>
                        </td>
                        <td>
                            <div style="display: flex; gap: 8px;">
                                <a href="/admin/talks.php?action=edit&id=<?= (int)$t['id'] ?>" class="btn btn-secondary btn-sm">
                                    EDIT
                                </a>
                                <a href="/admin/talks.php?action=delete&id=<?= (int)$t['id'] ?>&csrf=<?= e(csrf_token()) ?>" 
                                   class="btn btn-danger btn-sm" 
                                   data-confirm="Delete talk '<?= e($t['title']) ?>'?">
                                    PURGE
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
