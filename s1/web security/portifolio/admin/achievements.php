<?php
/**
 * Admin Achievements Management (CRUD)
 */

$pageTitle = 'Achievements Management';
require_once __DIR__ . '/includes/admin_header.php';

$action = $_GET['action'] ?? 'list';
$achId = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// Handle DELETE
if ($action === 'delete' && $achId > 0) {
    if (isset($_GET['csrf']) && hash_equals($_SESSION['csrf_token'] ?? '', $_GET['csrf'])) {
        if (delete_achievement($achId)) {
            set_flash('success', 'Achievement record purged.');
        } else {
            set_flash('danger', 'Failed to purge achievement.');
        }
    }
    header('Location: /admin/achievements.php');
    exit;
}

// Handle CREATE / EDIT
if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($action, ['create', 'edit'])) {
    if (!csrf_verify()) {
        set_flash('danger', 'Security verification failed.');
        header('Location: /admin/achievements.php');
        exit;
    }

    $title = trim($_POST['title'] ?? '');
    $event = trim($_POST['event'] ?? '');
    $year = trim($_POST['year'] ?? '');
    $prize = trim($_POST['prize'] ?? '');
    $level = trim($_POST['level'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $displayOrder = (int) ($_POST['display_order'] ?? 0);

    $achData = [
        'id' => $achId,
        'title' => $title,
        'event' => $event,
        'year' => $year,
        'prize' => $prize,
        'level' => $level,
        'description' => $description,
        'display_order' => $displayOrder
    ];

    $savedId = save_achievement($achData);
    if ($savedId > 0) {
        set_flash('success', 'Achievement record saved successfully.');
        header('Location: /admin/achievements.php');
        exit;
    } else {
        set_flash('danger', 'Failed to save achievement record.');
    }
}

$achievement = null;
if ($action === 'edit' && $achId > 0) {
    $achievement = get_achievement_by_id($achId);
    if (!$achievement) {
        set_flash('danger', 'Achievement not found.');
        header('Location: /admin/achievements.php');
        exit;
    }
}

$allAchievements = get_achievements_list();
?>

<div class="page-header">
    <div>
        <h1 class="page-title">[0x04] // HONORS &amp; ACHIEVEMENTS</h1>
        <p style="font-family: var(--font-mono); font-size: 0.85rem; color: var(--clr-stone); margin-top: 4px;">
            Log hackathon awards, state/national paper presentations, and research milestones.
        </p>
    </div>

    <div>
        <?php if ($action === 'list'): ?>
            <a href="/admin/achievements.php?action=create" class="btn btn-primary btn-sm">+ LOG NEW ACHIEVEMENT</a>
        <?php else: ?>
            <a href="/admin/achievements.php" class="btn btn-secondary btn-sm">&larr; BACK TO LIST</a>
        <?php endif; ?>
    </div>
</div>

<?php if ($action === 'create' || $action === 'edit'): ?>
    <div class="form-card cut-corner">
        <h2 style="font-size: 1.3rem; margin-bottom: 24px; color: var(--clr-sand);">
            <?= $action === 'create' ? '+ LOG NEW ACHIEVEMENT' : 'EDIT ACHIEVEMENT: ' . e($achievement['title']) ?>
        </h2>

        <form method="POST" action="/admin/achievements.php?action=<?= $action ?><?= $achId ? '&id=' . $achId : '' ?>">
            <?= csrf_field() ?>

            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label" for="title">&gt; TITLE / TOPIC *</label>
                    <input type="text" id="title" name="title" class="form-control" value="<?= e($achievement['title'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="event">&gt; EVENT NAME *</label>
                    <input type="text" id="event" name="event" class="form-control" value="<?= e($achievement['event'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="prize">&gt; PRIZE / RECOGNITION *</label>
                    <input type="text" id="prize" name="prize" class="form-control" placeholder="e.g. First Prize, Shortlisted" value="<?= e($achievement['prize'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="level">&gt; COMPETITION LEVEL *</label>
                    <input type="text" id="level" name="level" class="form-control" placeholder="e.g. State Level, National Level" value="<?= e($achievement['level'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="year">&gt; YEAR / STATUS *</label>
                    <input type="text" id="year" name="year" class="form-control" placeholder="e.g. 2024, 2025, Ongoing" value="<?= e($achievement['year'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="display_order">&gt; DISPLAY ORDER</label>
                    <input type="number" id="display_order" name="display_order" class="form-control" value="<?= (int) ($achievement['display_order'] ?? 0) ?>">
                </div>

                <div class="form-group form-full">
                    <label class="form-label" for="description">&gt; CITATION / DETAILS *</label>
                    <textarea id="description" name="description" class="form-control" rows="3" required><?= e($achievement['description'] ?? '') ?></textarea>
                </div>
            </div>

            <div style="margin-top: 24px; display: flex; justify-content: flex-end; gap: 12px;">
                <a href="/admin/achievements.php" class="btn btn-secondary">CANCEL</a>
                <button type="submit" class="btn btn-primary">SAVE RECORD &rarr;</button>
            </div>
        </form>
    </div>
<?php else: ?>
    <div class="table-card cut-corner">
        <div class="table-header">
            <span style="font-weight: bold; color: var(--clr-sand);">LOGGED ACHIEVEMENTS (<?= count($allAchievements) ?>)</span>
            <span class="binary-tag">VERIFIED</span>
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th>YEAR</th>
                    <th>PRIZE</th>
                    <th>TITLE &amp; EVENT</th>
                    <th>LEVEL</th>
                    <th>ACTIONS</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($allAchievements as $a): ?>
                    <tr>
                        <td style="font-family: var(--font-mono); color: var(--clr-terracotta); font-weight: bold;">
                            <?= e($a['year']) ?>
                        </td>
                        <td>
                            <span class="badge-unread"><?= e($a['prize']) ?></span>
                        </td>
                        <td>
                            <strong><?= e($a['title']) ?></strong>
                            <div style="font-size: 0.75rem; color: var(--clr-stone);"><?= e($a['event']) ?></div>
                        </td>
                        <td style="color: var(--clr-stone); font-size: 0.8rem;"><?= e($a['level']) ?></td>
                        <td>
                            <div style="display: flex; gap: 8px;">
                                <a href="/admin/achievements.php?action=edit&id=<?= (int)$a['id'] ?>" class="btn btn-secondary btn-sm">
                                    EDIT
                                </a>
                                <a href="/admin/achievements.php?action=delete&id=<?= (int)$a['id'] ?>&csrf=<?= e(csrf_token()) ?>" 
                                   class="btn btn-danger btn-sm" 
                                   data-confirm="Purge achievement '<?= e($a['title']) ?>'?">
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
