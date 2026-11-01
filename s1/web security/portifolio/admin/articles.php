<?php
/**
 * Admin Blog & Article Management (CRUD)
 */

$pageTitle = 'Blog & Articles Management';
require_once __DIR__ . '/includes/admin_header.php';

$action = $_GET['action'] ?? 'list';
$articleId = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// Handle DELETE
if ($action === 'delete' && $articleId > 0) {
    if (isset($_GET['csrf']) && hash_equals($_SESSION['csrf_token'] ?? '', $_GET['csrf'])) {
        if (delete_article($articleId)) {
            set_flash('success', 'Article removed successfully.');
        } else {
            set_flash('danger', 'Failed to remove article.');
        }
    }
    header('Location: /admin/articles.php');
    exit;
}

// Handle CREATE / EDIT
if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($action, ['create', 'edit'])) {
    if (!csrf_verify()) {
        set_flash('danger', 'Security verification token invalid.');
        header('Location: /admin/articles.php');
        exit;
    }

    $title = trim($_POST['title'] ?? '');
    $slug = trim($_POST['slug'] ?? '');
    $summary = trim($_POST['summary'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $pdate = trim($_POST['published_date'] ?? date('M d, Y'));
    $isPub = !empty($_POST['is_published']) ? 1 : 0;
    $imagePath = trim($_POST['existing_image'] ?? '');

    // Image Upload
    if (!empty($_FILES['article_image']['name'])) {
        try {
            $imagePath = handle_file_upload($_FILES['article_image'], 'articles');
        } catch (Exception $e) {
            set_flash('danger', 'Upload failure: ' . $e->getMessage());
            header('Location: /admin/articles.php?action=' . $action . ($articleId ? '&id=' . $articleId : ''));
            exit;
        }
    }

    // Parse Tags
    $tagRaw = trim($_POST['tags'] ?? '');
    $tags = array_filter(array_map('trim', explode(',', $tagRaw)));

    $articleData = [
        'id' => $articleId,
        'title' => $title,
        'slug' => $slug ?: slugify($title),
        'summary' => $summary,
        'content' => $content,
        'image' => $imagePath,
        'published_date' => $pdate,
        'is_published' => $isPub
    ];

    $savedId = save_article($articleData, $tags);
    if ($savedId > 0) {
        set_flash('success', 'Article saved and published.');
        header('Location: /admin/articles.php');
        exit;
    } else {
        set_flash('danger', 'Failed to save article.');
    }
}

$article = null;
if ($action === 'edit' && $articleId > 0) {
    $article = get_article_by_id($articleId);
    if (!$article) {
        set_flash('danger', 'Article not located.');
        header('Location: /admin/articles.php');
        exit;
    }
}

$allArticles = get_articles_list(false);
?>

<div class="page-header">
    <div>
        <h1 class="page-title">[0x06] // ARTICLES &amp; PAPERS</h1>
        <p style="font-family: var(--font-mono); font-size: 0.85rem; color: var(--clr-stone); margin-top: 4px;">
            Publish and manage research essays, technical papers, and engineering dispatches.
        </p>
    </div>

    <div>
        <?php if ($action === 'list'): ?>
            <a href="/admin/articles.php?action=create" class="btn btn-primary btn-sm">+ WRITE NEW ARTICLE</a>
        <?php else: ?>
            <a href="/admin/articles.php" class="btn btn-secondary btn-sm">&larr; BACK TO LIST</a>
        <?php endif; ?>
    </div>
</div>

<?php if ($action === 'create' || $action === 'edit'): ?>
    <div class="form-card cut-corner">
        <h2 style="font-size: 1.3rem; margin-bottom: 24px; color: var(--clr-sand);">
            <?= $action === 'create' ? '+ COMPOSE NEW TECHNICAL ARTICLE' : 'EDIT ARTICLE: ' . e($article['title']) ?>
        </h2>

        <form method="POST" action="/admin/articles.php?action=<?= $action ?><?= $articleId ? '&id=' . $articleId : '' ?>" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <input type="hidden" name="existing_image" value="<?= e($article['image'] ?? '') ?>">

            <div class="form-grid">
                <div class="form-group form-full">
                    <label class="form-label" for="title">&gt; ARTICLE TITLE *</label>
                    <input type="text" id="title" name="title" class="form-control" value="<?= e($article['title'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="slug">&gt; URL SLUG (OPTIONAL, AUTO-GENERATED IF EMPTY)</label>
                    <input type="text" id="slug" name="slug" class="form-control" placeholder="quantum-consciousness-transfer" value="<?= e($article['slug'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label class="form-label" for="published_date">&gt; PUBLICATION DATE *</label>
                    <input type="text" id="published_date" name="published_date" class="form-control" value="<?= e($article['published_date'] ?? date('M d, Y')) ?>" required>
                </div>

                <div class="form-group form-full">
                    <label class="form-label" for="tags">&gt; TOPIC TAGS (COMMA SEPARATED)</label>
                    <input type="text" id="tags" name="tags" class="form-control" placeholder="AI, Blockchain, Quantum Computing" value="<?= e(implode(', ', $article['tags'] ?? [])) ?>">
                </div>

                <div class="form-group form-full">
                    <label class="form-label" for="summary">&gt; ABSTRACT / EXECUTIVE SUMMARY *</label>
                    <textarea id="summary" name="summary" class="form-control" rows="3" required><?= e($article['summary'] ?? '') ?></textarea>
                </div>

                <div class="form-group form-full">
                    <label class="form-label" for="content">&gt; FULL ARTICLE BODY (HTML / MARKDOWN FORMATTING) *</label>
                    <textarea id="content" name="content" class="form-control" rows="12" style="font-family: monospace;" required><?= e($article['content'] ?? '') ?></textarea>
                </div>

                <div class="form-group form-full">
                    <label class="form-label" for="article_image">&gt; FEATURED COVER IMAGE UPLOAD</label>
                    <input type="file" id="article_image" name="article_image" class="form-control" accept="image/*">
                    <?php if (!empty($article['image'])): ?>
                        <div style="margin-top: 8px;">
                            <span class="mono-data" style="color: var(--clr-stone); font-size: 0.8rem;">Current: <?= e($article['image']) ?></span>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="form-group form-full" style="border: 1px dashed var(--clr-stone-dark); padding: 14px;">
                    <label class="switch-label">
                        <input type="checkbox" name="is_published" value="1" <?= (!isset($article['is_published']) || !empty($article['is_published'])) ? 'checked' : '' ?>>
                        <span>PUBLISH NOW (VISIBLE ON PORTFOLIO)</span>
                    </label>
                </div>
            </div>

            <div style="margin-top: 24px; display: flex; justify-content: flex-end; gap: 12px;">
                <a href="/admin/articles.php" class="btn btn-secondary">CANCEL</a>
                <button type="submit" class="btn btn-primary">SAVE ARTICLE &rarr;</button>
            </div>
        </form>
    </div>
<?php else: ?>
    <div class="table-card cut-corner">
        <div class="table-header">
            <span style="font-weight: bold; color: var(--clr-sand);">PUBLISHED ARTICLES (<?= count($allArticles) ?>)</span>
            <span class="binary-tag">INDEXED</span>
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th>DATE</th>
                    <th>TITLE &amp; SLUG</th>
                    <th>TAGS</th>
                    <th>STATUS</th>
                    <th>ACTIONS</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($allArticles as $art): ?>
                    <tr>
                        <td style="font-family: var(--font-mono); color: var(--clr-terracotta); font-size: 0.8rem;">
                            <?= e($art['published_date']) ?>
                        </td>
                        <td>
                            <strong><?= e($art['title']) ?></strong>
                            <div style="font-size: 0.72rem; color: var(--clr-stone); margin-top: 2px;">
                                /blog-detail.php?slug=<?= e($art['slug']) ?>
                            </div>
                        </td>
                        <td>
                            <div style="display: flex; flex-wrap: wrap; gap: 4px;">
                                <?php foreach ($art['tags'] ?? [] as $tg): ?>
                                    <span class="tech-badge" style="font-size: 0.7rem;">#<?= e($tg) ?></span>
                                <?php endforeach; ?>
                            </div>
                        </td>
                        <td>
                            <?php if (!empty($art['is_published'])): ?>
                                <span style="color: #4ADE80; font-size: 0.78rem;">PUBLISHED</span>
                            <?php else: ?>
                                <span style="color: #F87171; font-size: 0.78rem;">DRAFT</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div style="display: flex; gap: 8px;">
                                <a href="/blog-detail.php?slug=<?= urlencode($art['slug']) ?>" target="_blank" class="btn btn-secondary btn-sm" title="View Public Post">
                                    VIEW
                                </a>
                                <a href="/admin/articles.php?action=edit&id=<?= (int)$art['id'] ?>" class="btn btn-secondary btn-sm">
                                    EDIT
                                </a>
                                <a href="/admin/articles.php?action=delete&id=<?= (int)$art['id'] ?>&csrf=<?= e(csrf_token()) ?>" 
                                   class="btn btn-danger btn-sm" 
                                   data-confirm="Delete article '<?= e($art['title']) ?>'?">
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
