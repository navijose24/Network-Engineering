<?php
/**
 * Admin Project Management (CRUD)
 */

$pageTitle = 'Project Management';
require_once __DIR__ . '/includes/admin_header.php';

$action = $_GET['action'] ?? 'list';
$projectId = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// Handle DELETE
if ($action === 'delete' && $projectId > 0) {
    if (csrf_verify() || isset($_GET['csrf']) && hash_equals($_SESSION['csrf_token'] ?? '', $_GET['csrf'])) {
        if (delete_project($projectId)) {
            set_flash('success', 'Project purged successfully.');
        } else {
            set_flash('danger', 'Failed to purge project record.');
        }
    } else {
        set_flash('danger', 'Security verification failed for deletion.');
    }
    header('Location: /admin/projects.php');
    exit;
}

// Handle Form Submission (Create or Edit)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($action, ['create', 'edit'])) {
    if (!csrf_verify()) {
        set_flash('danger', 'Security verification token invalid.');
        header('Location: /admin/projects.php');
        exit;
    }

    $title = trim($_POST['title'] ?? '');
    $subtitle = trim($_POST['subtitle'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $detailedDescription = trim($_POST['detailed_description'] ?? '');
    $github = trim($_POST['github'] ?? '');
    $link = trim($_POST['link'] ?? '');
    $video = trim($_POST['video'] ?? '');
    $displayOrder = (int) ($_POST['display_order'] ?? 0);
    $isFeatured = !empty($_POST['is_featured']) ? 1 : 0;
    $isPublished = !empty($_POST['is_published']) ? 1 : 0;
    $imagePath = trim($_POST['existing_image'] ?? '');

    // Handle Image Upload
    if (!empty($_FILES['project_image']['name'])) {
        try {
            $imagePath = handle_file_upload($_FILES['project_image'], 'projects');
        } catch (Exception $e) {
            set_flash('danger', 'Image upload failure: ' . $e->getMessage());
            header('Location: /admin/projects.php?action=' . $action . ($projectId ? '&id=' . $projectId : ''));
            exit;
        }
    }

    // Parse Technologies (comma separated)
    $techRaw = trim($_POST['technologies'] ?? '');
    $techs = array_filter(array_map('trim', explode(',', $techRaw)));

    // Parse Bullets (one per line)
    $bulletRaw = trim($_POST['bullets'] ?? '');
    $bullets = array_filter(array_map('trim', explode("\n", $bulletRaw)));

    $projectData = [
        'id' => $projectId,
        'title' => $title,
        'subtitle' => $subtitle,
        'category' => $category,
        'description' => $description,
        'detailed_description' => $detailedDescription,
        'image' => $imagePath,
        'video' => $video,
        'github' => $github,
        'link' => $link,
        'is_featured' => $isFeatured,
        'display_order' => $displayOrder,
        'is_published' => $isPublished
    ];

    $savedId = save_project($projectData, $techs, $bullets);
    if ($savedId > 0) {
        set_flash('success', 'Project saved successfully and active on portfolio.');
        header('Location: /admin/projects.php');
        exit;
    } else {
        set_flash('danger', 'Failed to commit project changes to database.');
    }
}

// Render Form or List
$project = null;
if ($action === 'edit' && $projectId > 0) {
    $project = get_project_by_id($projectId);
    if (!$project) {
        set_flash('danger', 'Requested project not found.');
        header('Location: /admin/projects.php');
        exit;
    }
}
?>

<div class="page-header">
    <div>
        <h1 class="page-title">[0x02] // PROJECT MANAGEMENT</h1>
        <p style="font-family: var(--font-mono); font-size: 0.85rem; color: var(--clr-stone); margin-top: 4px;">
            Add, update, reorder, feature, and publish software engineering systems.
        </p>
    </div>

    <div>
        <?php if ($action === 'list'): ?>
            <a href="/admin/projects.php?action=create" class="btn btn-primary btn-sm">+ CREATE NEW PROJECT</a>
        <?php else: ?>
            <a href="/admin/projects.php" class="btn btn-secondary btn-sm">&larr; BACK TO PROJECT LIST</a>
        <?php endif; ?>
    </div>
</div>

<?php if ($action === 'create' || $action === 'edit'): ?>
    <!-- Project Edit / Create Form -->
    <div class="form-card cut-corner">
        <h2 style="font-size: 1.3rem; margin-bottom: 24px; color: var(--clr-sand);">
            <?= $action === 'create' ? '+ INITIALIZE NEW PROJECT SPECIFICATION' : 'MODIFY SPECIFICATION: ' . e($project['title']) ?>
        </h2>

        <form method="POST" action="/admin/projects.php?action=<?= $action ?><?= $projectId ? '&id=' . $projectId : '' ?>" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <input type="hidden" name="existing_image" value="<?= e($project['image'] ?? '') ?>">

            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label" for="title">&gt; PROJECT TITLE *</label>
                    <input type="text" id="title" name="title" class="form-control" value="<?= e($project['title'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="category">&gt; CATEGORY *</label>
                    <input type="text" id="category" name="category" class="form-control" placeholder="e.g. AI • Full Stack, DevOps, Blockchain" value="<?= e($project['category'] ?? '') ?>" required>
                </div>

                <div class="form-group form-full">
                    <label class="form-label" for="subtitle">&gt; SUBTITLE / ONE-LINER</label>
                    <input type="text" id="subtitle" name="subtitle" class="form-control" placeholder="e.g. Real-time Infrastructure Observability with Prometheus & Grafana" value="<?= e($project['subtitle'] ?? '') ?>">
                </div>

                <div class="form-group form-full">
                    <label class="form-label" for="description">&gt; SUMMARY DESCRIPTION (CARD VIEW) *</label>
                    <textarea id="description" name="description" class="form-control" rows="3" required><?= e($project['description'] ?? '') ?></textarea>
                </div>

                <div class="form-group form-full">
                    <label class="form-label" for="detailed_description">&gt; DETAILED ARCHITECTURE &amp; SPECIFICATIONS (MODAL VIEW)</label>
                    <textarea id="detailed_description" name="detailed_description" class="form-control" rows="5"><?= e($project['detailed_description'] ?? '') ?></textarea>
                </div>

                <div class="form-group form-full">
                    <label class="form-label" for="technologies">&gt; TECHNOLOGIES (COMMA-SEPARATED)</label>
                    <input type="text" id="technologies" name="technologies" class="form-control" placeholder="React 19, Django, FastAPI, Llama 3.3, Docker" value="<?= e(implode(', ', $project['technologies'] ?? [])) ?>">
                </div>

                <div class="form-group form-full">
                    <label class="form-label" for="bullets">&gt; KEY HIGHLIGHTS / BULLET POINTS (ONE PER LINE)</label>
                    <textarea id="bullets" name="bullets" class="form-control" rows="4" placeholder="Orchestrated a containerized telemetry stack using Docker Compose..."><?= e(implode("\n", $project['bullets'] ?? [])) ?></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label" for="github">&gt; REPOSITORY URL (GITHUB)</label>
                    <input type="url" id="github" name="github" class="form-control" placeholder="https://github.com/username/project" value="<?= e($project['github'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label class="form-label" for="link">&gt; LIVE SYSTEM DEMO URL</label>
                    <input type="text" id="link" name="link" class="form-control" placeholder="https://demo.example.com" value="<?= e($project['link'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label class="form-label" for="video">&gt; VIDEO / MEDIA LINK</label>
                    <input type="text" id="video" name="video" class="form-control" placeholder="/project/demo.mp4 or YouTube URL" value="<?= e($project['video'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label class="form-label" for="display_order">&gt; DISPLAY ORDER (NUMERICAL)</label>
                    <input type="number" id="display_order" name="display_order" class="form-control" value="<?= (int) ($project['display_order'] ?? 0) ?>">
                </div>

                <div class="form-group form-full" style="display: flex; gap: 32px; border: 1px dashed var(--clr-stone-dark); padding: 18px;">
                    <label class="switch-label">
                        <input type="checkbox" name="is_featured" value="1" <?= !empty($project['is_featured']) ? 'checked' : '' ?>>
                        <span>FEATURE ON HOME / HERO</span>
                    </label>

                    <label class="switch-label">
                        <input type="checkbox" name="is_published" value="1" <?= (!isset($project['is_published']) || !empty($project['is_published'])) ? 'checked' : '' ?>>
                        <span>PUBLISHED (VISIBLE TO PUBLIC)</span>
                    </label>
                </div>

                <div class="form-group form-full">
                    <label class="form-label" for="project_image">&gt; PROJECT COVER IMAGE UPLOAD</label>
                    <input type="file" id="project_image" name="project_image" class="form-control" accept="image/*" data-preview="proj-preview">
                    
                    <?php if (!empty($project['image'])): ?>
                        <div style="margin-top: 10px; display: flex; align-items: center; gap: 14px;">
                            <img id="proj-preview" src="<?= e($project['image']) ?>" alt="Cover" style="max-height: 100px; border: 1px solid var(--clr-stone);">
                            <span class="mono-data" style="color: var(--clr-stone); font-size: 0.8rem;">Current: <?= e($project['image']) ?></span>
                        </div>
                    <?php else: ?>
                        <img id="proj-preview" src="" alt="Preview" style="max-height: 100px; border: 1px solid var(--clr-stone); display: none; margin-top: 10px;">
                    <?php endif; ?>
                </div>
            </div>

            <div style="margin-top: 30px; display: flex; justify-content: flex-end; gap: 14px;">
                <a href="/admin/projects.php" class="btn btn-secondary">CANCEL</a>
                <button type="submit" class="btn btn-primary">SAVE PROJECT &rarr;</button>
            </div>
        </form>
    </div>

<?php else: ?>
    <!-- Projects List Table -->
    <?php $allProjects = get_projects(null, false, false); ?>
    <div class="table-card cut-corner">
        <div class="table-header">
            <span style="font-weight: bold; color: var(--clr-sand);">ALL PROJECTS REGISTERED (<?= count($allProjects) ?>)</span>
            <span class="binary-tag">DB: LIVE</span>
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 60px;">ORDER</th>
                    <th>TITLE &amp; CATEGORY</th>
                    <th>TECH STACK</th>
                    <th>STATUS</th>
                    <th>ACTIONS</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($allProjects as $p): ?>
                    <tr>
                        <td style="font-family: var(--font-mono); color: var(--clr-terracotta);">
                            <?= (int) $p['display_order'] ?>
                        </td>
                        <td>
                            <strong><?= e($p['title']) ?></strong>
                            <div style="font-size: 0.75rem; color: var(--clr-stone); margin-top: 2px;">
                                <?= e($p['category']) ?>
                            </div>
                        </td>
                        <td>
                            <div style="display: flex; flex-wrap: wrap; gap: 4px; max-width: 320px;">
                                <?php foreach (array_slice($p['technologies'] ?? [], 0, 4) as $t): ?>
                                    <span class="tech-badge" style="font-size: 0.7rem;"><?= e($t) ?></span>
                                <?php endforeach; ?>
                            </div>
                        </td>
                        <td>
                            <?php if (!empty($p['is_featured'])): ?>
                                <span class="badge-unread" style="font-size: 0.7rem; background: var(--clr-violet); color: #fff;">FEATURED</span>
                            <?php endif; ?>
                            <?php if (!empty($p['is_published'])): ?>
                                <span style="color: #4ADE80; font-size: 0.78rem;">PUBLISHED</span>
                            <?php else: ?>
                                <span style="color: #F87171; font-size: 0.78rem;">DRAFT</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div style="display: flex; gap: 8px;">
                                <a href="/admin/projects.php?action=edit&id=<?= (int)$p['id'] ?>" class="btn btn-secondary btn-sm">
                                    EDIT
                                </a>
                                <a href="/admin/projects.php?action=delete&id=<?= (int)$p['id'] ?>&csrf=<?= e(csrf_token()) ?>" 
                                   class="btn btn-danger btn-sm" 
                                   data-confirm="Purge project '<?= e($p['title']) ?>' permanently?">
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
