<?php
/**
 * Single Article / Technical Paper View
 */

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/db_queries.php';

$slug = trim($_GET['slug'] ?? '');
$article = null;

if (!empty($slug)) {
    $article = get_article_by_slug($slug);
}

if (!$article) {
    http_response_code(404);
    require_once __DIR__ . '/includes/header.php';
    echo '<div class="container" style="padding: 100px 24px; text-align: center;">
            <h1 class="section-title">[404] // ARTICLE NOT LOCATED</h1>
            <p style="margin: 20px 0;">The requested technical paper or article does not exist or has been retracted.</p>
            <a href="/" class="btn btn-primary">&larr; RETURN TO PORTFOLIO</a>
          </div>';
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

$profile = get_profile_data();
require_once __DIR__ . '/includes/header.php';
?>

<div class="container" style="padding: 60px 24px; max-width: 900px;">
    <div style="margin-bottom: 30px;">
        <a href="/#articles" class="btn btn-secondary btn-sm">&larr; BACK TO PAPERS &amp; ARTICLES</a>
    </div>

    <article class="cut-corner" style="background: var(--clr-graphite); border: 2px solid var(--clr-sand); padding: 48px 36px; box-shadow: 8px 8px 0 var(--clr-terracotta);">
        <div style="display: flex; gap: 12px; margin-bottom: 16px; align-items: center; flex-wrap: wrap;">
            <span class="binary-tag accent"><?= e($article['published_date']) ?></span>
            <span class="binary-tag">AUTHOR: <?= e($profile['name']) ?></span>
        </div>

        <h1 style="font-size: clamp(1.8rem, 4vw, 2.8rem); line-height: 1.15; margin-bottom: 20px; color: var(--clr-sand);">
            <?= e($article['title']) ?>
        </h1>

        <div class="tech-tags" style="margin-bottom: 32px;">
            <?php foreach ($article['tags'] ?? [] as $tag): ?>
                <span class="tech-badge">#<?= e($tag) ?></span>
            <?php endforeach; ?>
        </div>

        <?php if (!empty($article['image'])): ?>
            <div style="margin-bottom: 36px; border: 2px solid var(--clr-stone); overflow: hidden; background: #000;">
                <img src="<?= e($article['image']) ?>" alt="<?= e($article['title']) ?>" style="width: 100%; max-height: 440px; object-fit: cover;" onerror="this.style.display='none'">
            </div>
        <?php endif; ?>

        <div style="font-size: 1.15rem; color: #DECFC0; line-height: 1.7; border-left: 3px solid var(--clr-terracotta); padding-left: 20px; margin-bottom: 36px;">
            <?= e($article['summary']) ?>
        </div>

        <!-- Article HTML Content -->
        <div class="article-content" style="color: #D3C3B0; font-size: 1.05rem; line-height: 1.8;">
            <?= $article['content'] ?>
        </div>

        <div style="border-top: 1px solid var(--clr-stone-dark); margin-top: 48px; padding-top: 24px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
            <div class="binary-tag">
                <span>DIGEST VERIFIED</span> // <span>200 OK</span>
            </div>
            <a href="/#contact" class="btn btn-primary btn-sm">DISCUSS WITH THE AUTHOR &rarr;</a>
        </div>
    </article>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
