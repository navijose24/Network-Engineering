<?php
/**
 * Admin Skills Matrix Management
 */

$pageTitle = 'Skills Matrix Management';
require_once __DIR__ . '/includes/admin_header.php';

$action = $_GET['action'] ?? 'list';
$skillId = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// Handle Delete Skill
if ($action === 'delete' && $skillId > 0) {
    if (isset($_GET['csrf']) && hash_equals($_SESSION['csrf_token'] ?? '', $_GET['csrf'])) {
        if (delete_skill($skillId)) {
            set_flash('success', 'Skill entry deleted successfully.');
        } else {
            set_flash('danger', 'Failed to remove skill.');
        }
    }
    header('Location: /admin/skills.php');
    exit;
}

// Handle Add/Edit Skill
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_skill'])) {
    if (!csrf_verify()) {
        set_flash('danger', 'Security token invalid.');
        header('Location: /admin/skills.php');
        exit;
    }

    $name = trim($_POST['name'] ?? '');
    $categoryId = (int) ($_POST['category_id'] ?? 0);
    $proficiency = (int) ($_POST['proficiency'] ?? 85);
    $order = (int) ($_POST['display_order'] ?? 0);
    $editId = (int) ($_POST['skill_id'] ?? 0);

    if (empty($name) || $categoryId <= 0) {
        set_flash('danger', 'Skill name and category are required.');
    } else {
        $saved = save_skill([
            'id' => $editId,
            'name' => $name,
            'category_id' => $categoryId,
            'proficiency' => $proficiency,
            'display_order' => $order
        ]);

        if ($saved) {
            set_flash('success', 'Skill entry committed to database.');
        } else {
            set_flash('danger', 'Failed to commit skill entry.');
        }
    }

    header('Location: /admin/skills.php');
    exit;
}

$categories = get_all_skill_categories();
$skillsGrouped = get_skills_grouped();
?>

<div class="page-header">
    <div>
        <h1 class="page-title">[0x03] // SKILLS MATRIX</h1>
        <p style="font-family: var(--font-mono); font-size: 0.85rem; color: var(--clr-stone); margin-top: 4px;">
            Configure technical proficiencies, frameworks, tools, and domain categories.
        </p>
    </div>
</div>

<div class="form-grid" style="align-items: start;">
    <!-- Add / Edit Skill Form -->
    <div class="form-card cut-corner">
        <h2 style="font-size: 1.25rem; margin-bottom: 20px; color: var(--clr-sand);">
            + ADD SKILL ENTRY
        </h2>

        <form method="POST" action="/admin/skills.php">
            <?= csrf_field() ?>
            <input type="hidden" name="save_skill" value="1">
            <input type="hidden" name="skill_id" value="0">

            <div class="form-group">
                <label class="form-label" for="category_id">&gt; CATEGORY *</label>
                <select id="category_id" name="category_id" class="form-control" required>
                    <option value="">Select Domain Category</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= (int)$cat['id'] ?>"><?= e($cat['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label" for="name">&gt; SKILL / TECHNOLOGY NAME *</label>
                <input type="text" id="name" name="name" class="form-control" placeholder="e.g. Python, Docker, PyTorch" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="proficiency">&gt; PROFICIENCY PERCENTAGE (0 - 100)</label>
                <input type="number" id="proficiency" name="proficiency" class="form-control" min="10" max="100" value="90">
            </div>

            <div class="form-group">
                <label class="form-label" for="display_order">&gt; DISPLAY ORDER</label>
                <input type="number" id="display_order" name="display_order" class="form-control" value="0">
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 10px;">
                SAVE SKILL ENTRY &rarr;
            </button>
        </form>
    </div>

    <!-- Grouped Skills List -->
    <div>
        <?php foreach ($skillsGrouped as $catName => $catData): ?>
            <div class="table-card cut-corner" style="margin-bottom: 24px;">
                <div class="table-header">
                    <span style="font-weight: bold; color: var(--clr-sand);"><?= e($catName) ?></span>
                    <span class="binary-tag"><?= count($catData['skills']) ?> ENTRIES</span>
                </div>

                <table class="data-table">
                    <thead>
                        <tr>
                            <th>SKILL</th>
                            <th>PROFICIENCY</th>
                            <th style="text-align: right;">ACTION</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($catData['skills'])): ?>
                            <tr>
                                <td colspan="3" style="text-align: center; color: var(--clr-stone);">No skills in this category yet.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($catData['skills'] as $s): ?>
                                <tr>
                                    <td><strong><?= e($s['name']) ?></strong></td>
                                    <td><?= (int)$s['proficiency'] ?>%</td>
                                    <td style="text-align: right;">
                                        <a href="/admin/skills.php?action=delete&id=<?= (int)$s['id'] ?>&csrf=<?= e(csrf_token()) ?>" 
                                           class="btn btn-danger btn-sm" 
                                           data-confirm="Remove '<?= e($s['name']) ?>'?">
                                            DEL
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
