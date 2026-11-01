<?php
/**
 * Admin Creative Works & Visual Lab Management
 */

$pageTitle = 'Creative Works Management';
require_once __DIR__ . '/includes/admin_header.php';

$action = $_GET['action'] ?? 'list';
$workId = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// Handle DELETE
if ($action === 'delete' && $workId > 0) {
    if (isset($_GET['csrf']) && hash_equals($_SESSION['csrf_token'] ?? '', $_GET['csrf'])) {
        if (delete_creative_work($workId)) {
            set_flash('success', 'Creative piece deleted.');
        } else {
            set_flash('danger', 'Failed to delete piece.');
        }
    }
    header('Location: /admin/creative.php');
    exit;
}

// Handle CREATE / EDIT
if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($action, ['create', 'edit'])) {
    if (!csrf_verify()) {
        set_flash('danger', 'Security token invalid.');
        header('Location: /admin/creative.php');
        exit;
    }

    $title = trim($_POST['title'] ?? '');
    $type = trim($_POST['work_type'] ?? 'image');
    $subtitle = trim($_POST['subtitle'] ?? '');
    $desc = trim($_POST['description'] ?? '');
    $order = (int) ($_POST['display_order'] ?? 0);
    $thumb = trim($_POST['existing_thumbnail'] ?? '');

    // Upload thumbnail
    if (!empty($_FILES['thumbnail_file']['name'])) {
        try {
            $thumb = handle_file_upload($_FILES['thumbnail_file'], 'creative');
        } catch (Exception $e) {
            set_flash('danger', 'Upload failure: ' . $e->getMessage());
            header('Location: /admin/creative.php');
            exit;
        }
    }

    $workData = [
        'id' => $workId,
        'title' => $title,
        'work_type' => $type,
        'subtitle' => $subtitle,
        'description' => $desc,
        'thumbnail' => $thumb,
        'display_order' => $order
    ];

    $savedId = save_creative_work($workData);
    if ($savedId > 0) {
        set_flash('success', 'Creative work updated.');
        header('Location: /admin/creative.php');
        exit;
    } else {
        set_flash('danger', 'Failed to update creative work.');
    }
}

$work = null;
if ($action === 'edit' && $workId > 0) {
    $work = get_creative_work_by_id($workId);
    if (!$work) {
        set_flash('danger', 'Work not located.');
        header('Location: /admin/creative.php');
        exit;
    }
}

$allWorks = get_creative_works_list();
?>

<div class="page-header">
    <div>
        <h1 class="page-title">[0x07] // CREATIVE WORKS &amp; LAB</h1>
        <p style="font-family: var(--font-mono); font-size: 0.85rem; color: var(--clr-stone); margin-top: 4px;">
            Manage visual experiments, publication pages ("Y SHE Y"), and photographic galleries.
        </p>
    </div>

    <div>
        <?php if ($action === 'list'): ?>
            <a href="/admin/creative.php?action=create" class="btn btn-primary btn-sm">+ ADD CREATIVE PIECE</a>
        <?php else: ?>
            <a href="/admin/creative.php" class="btn btn-secondary btn-sm">&larr; BACK TO GALLERY</a>
        <?php endif; ?>
    </div>
</div>

<?php if ($action === 'create' || $action === 'edit'): ?>
    <div class="form-card cut-corner">
        <h2 style="font-size: 1.3rem; margin-bottom: 24px; color: var(--clr-sand);">
            <?= $action === 'create' ? '+ REGISTER NEW CREATIVE WORK' : 'EDIT WORK: ' . e($work['title']) ?>
        </h2>

        <form method="POST" action="/admin/creative.php?action=<?= $action ?><?= $workId ? '&id=' . $workId : '' ?>" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <input type="hidden" name="existing_thumbnail" value="<?= e($work['thumbnail'] ?? '') ?>">

            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label" for="title">&gt; WORK TITLE *</label>
                    <input type="text" id="title" name="title" class="form-control" value="<?= e($work['title'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="work_type">&gt; WORK CLASSIFICATION *</label>
                    <select id="work_type" name="work_type" class="form-control" required>
                        <option value="image" <?= ($work['work_type'] ?? '') === 'image' ? 'selected' : '' ?>>Single Visual Artwork / Photo</option>
                        <option value="book" <?= ($work['work_type'] ?? '') === 'book' ? 'selected' : '' ?>>Multi-Page Publication (Book)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="subtitle">&gt; SUBTITLE</label>
                    <input type="text" id="subtitle" name="subtitle" class="form-control" value="<?= e($work['subtitle'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label class="form-label" for="display_order">&gt; DISPLAY ORDER</label>
                    <input type="number" id="display_order" name="display_order" class="form-control" value="<?= (int) ($work['display_order'] ?? 0) ?>">
                </div>

                <div class="form-group form-full">
                    <label class="form-label" for="description">&gt; ARTISTIC STATEMENT / DESCRIPTION</label>
                    <textarea id="description" name="description" class="form-control" rows="3"><?= e($work['description'] ?? '') ?></textarea>
                </div>

                <div class="form-group form-full">
                    <label class="form-label" for="thumbnail_file">&gt; THUMBNAIL / ARTWORK IMAGE UPLOAD</label>
                    <input type="file" id="thumbnail_file" name="thumbnail_file" class="form-control" accept="image/*">
                    <?php if (!empty($work['thumbnail'])): ?>
                        <div style="margin-top: 10px;">
                            <span class="mono-data" style="color: var(--clr-stone); font-size: 0.8rem;">Current file: <?= e($work['thumbnail']) ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div style="margin-top: 24px; display: flex; justify-content: flex-end; gap: 12px;">
                <a href="/admin/creative.php" class="btn btn-secondary">CANCEL</a>
                <button type="submit" class="btn btn-primary">SAVE RECORD &rarr;</button>
            </div>
        </form>
    </div>
<?php else: ?>
    <div class="table-card cut-corner">
        <div class="table-header">
            <span style="font-weight: bold; color: var(--clr-sand);">CREATIVE WORKS CATALOG (<?= count($allWorks) ?>)</span>
            <span class="binary-tag">LINES &amp; LAYERS</span>
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th>TYPE</th>
                    <th>TITLE</th>
                    <th>SUBTITLE</th>
                    <th>ORDER</th>
                    <th>ACTIONS</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($allWorks as $w): ?>
                    <tr>
                        <td>
                            <span class="binary-tag" style="font-size: 0.72rem;"><?= strtoupper(e($w['work_type'])) ?></span>
                        </td>
                        <td><strong><?= e($w['title']) ?></strong></td>
                        <td style="color: var(--clr-stone);"><?= e($w['subtitle'] ?? '—') ?></td>
                        <td><?= (int) $w['display_order'] ?></td>
                        <td>
                            <div style="display: flex; gap: 8px;">
                                <a href="/admin/creative.php?action=edit&id=<?= (int)$w['id'] ?>" class="btn btn-secondary btn-sm">
                                    EDIT
                                </a>
                                <a href="/admin/creative.php?action=delete&id=<?= (int)$w['id'] ?>&csrf=<?= e(csrf_token()) ?>" 
                                   class="btn btn-danger btn-sm" 
                                   data-confirm="Remove piece '<?= e($w['title']) ?>'?">
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
