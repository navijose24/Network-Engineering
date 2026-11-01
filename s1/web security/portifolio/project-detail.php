<?php
/**
 * Individual Project System Detail View
 */

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/db_queries.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$project = null;

if ($id > 0) {
    $project = get_project_by_id($id);
}

if (!$project) {
    http_response_code(404);
    require_once __DIR__ . '/includes/header.php';
    echo '<div class="container" style="padding: 100px 24px; text-align: center;">
            <h1 class="section-title">[404] // PROJECT SPECIFICATION NOT FOUND</h1>
            <p style="margin: 20px 0;">The requested engineering project does not exist.</p>
            <a href="/" class="btn btn-primary">&larr; RETURN TO PORTFOLIO</a>
          </div>';
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="container" style="padding: 60px 24px; max-width: 960px;">
    <div style="margin-bottom: 30px;">
        <a href="/#projects" class="btn btn-secondary btn-sm">&larr; RETURN TO ALL PROJECTS</a>
    </div>

    <article class="cut-corner" style="background: var(--clr-graphite); border: 2px solid var(--clr-sand); padding: 44px; box-shadow: 8px 8px 0 var(--clr-terracotta);">
        <div class="project-category-tag"><?= e($project['category']) ?></div>
        
        <h1 style="font-size: clamp(2rem, 4vw, 3rem); line-height: 1.1; margin-bottom: 12px; color: var(--clr-sand);">
            <?= e($project['title']) ?>
        </h1>

        <?php if (!empty($project['subtitle'])): ?>
            <div style="font-family: var(--font-mono); font-size: 1.1rem; color: var(--clr-terracotta); margin-bottom: 24px;">
                <?= e($project['subtitle']) ?>
            </div>
        <?php endif; ?>

        <div class="tech-tags" style="margin-bottom: 32px;">
            <?php foreach ($project['technologies'] ?? [] as $t): ?>
                <span class="tech-badge"><?= e($t) ?></span>
            <?php endforeach; ?>
        </div>

        <?php if (!empty($project['image'])): ?>
            <div style="margin-bottom: 36px; border: 2px solid var(--clr-stone); background: #000; overflow: hidden;">
                <img src="<?= e($project['image']) ?>" alt="<?= e($project['title']) ?>" style="width: 100%; max-height: 480px; object-fit: cover;" onerror="this.style.display='none'">
            </div>
        <?php endif; ?>

        <h3 style="font-size: 1.25rem; color: var(--clr-sand); margin-bottom: 14px; text-transform: uppercase;">
            System Summary
        </h3>
        <p style="font-size: 1.05rem; color: #DECFC0; line-height: 1.7; margin-bottom: 28px;">
            <?= e($project['detailed_description'] ?: $project['description']) ?>
        </p>

        <?php if (!empty($project['bullets'])): ?>
            <h3 style="font-size: 1.25rem; color: var(--clr-sand); margin-bottom: 14px; text-transform: uppercase;">
                Architectural Highlights &amp; Accomplishments
            </h3>
            <ul style="padding-left: 20px; line-height: 1.75; color: #D3C3B0; margin-bottom: 36px;">
                <?php foreach ($project['bullets'] as $bullet): ?>
                    <li style="margin-bottom: 10px;"><?= e($bullet) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <div style="display: flex; gap: 16px; flex-wrap: wrap; border-top: 1px solid var(--clr-stone-dark); padding-top: 24px;">
            <?php if (!empty($project['link'])): ?>
                <a href="<?= e($project['link']) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-primary">
                    LAUNCH DEPLOYED SYSTEM &rarr;
                </a>
            <?php endif; ?>

            <?php if (!empty($project['github'])): ?>
                <a href="<?= e($project['github']) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-secondary">
                    INSPECT GITHUB REPOSITORY &nearr;
                </a>
            <?php endif; ?>
        </div>
    </article>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
