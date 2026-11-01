<?php
/**
 * Admin Dashboard Overview
 */

$pageTitle = 'Dashboard Overview';
require_once __DIR__ . '/includes/admin_header.php';

// Fetch overview metrics
$metrics = get_admin_dashboard_metrics();
$recentMessages = get_contact_submissions_list(10);
?>

<div class="page-header">
    <div>
        <h1 class="page-title">[0x00] // SYSTEM OVERVIEW</h1>
        <p style="font-family: var(--font-mono); font-size: 0.85rem; color: var(--clr-stone); margin-top: 4px;">
            Real-time status of portfolio content, database records, and contact pipeline.
        </p>
    </div>

    <div style="display: flex; gap: 12px; flex-wrap: wrap;">
        <a href="/admin/projects.php?action=create" class="btn btn-primary btn-sm">+ ADD PROJECT</a>
        <a href="/admin/articles.php?action=create" class="btn btn-secondary btn-sm">+ WRITE ARTICLE</a>
    </div>
</div>

<!-- Metrics Row -->
<div class="metrics-row">
    <div class="metric-card cut-corner">
        <div class="metric-label">Total Projects</div>
        <div class="metric-val"><?= $metrics['projects'] ?></div>
        <div style="font-family: var(--font-mono); font-size: 0.72rem; color: var(--clr-stone); margin-top: 6px;">
            <a href="/admin/projects.php" style="color: var(--clr-terracotta);">Manage &rarr;</a>
        </div>
    </div>

    <div class="metric-card cut-corner">
        <div class="metric-label">Achievements &amp; Honors</div>
        <div class="metric-val"><?= $metrics['achievements'] ?></div>
        <div style="font-family: var(--font-mono); font-size: 0.72rem; color: var(--clr-stone); margin-top: 6px;">
            <a href="/admin/achievements.php" style="color: var(--clr-terracotta);">Manage &rarr;</a>
        </div>
    </div>

    <div class="metric-card cut-corner">
        <div class="metric-label">Articles &amp; Papers</div>
        <div class="metric-val"><?= $metrics['articles'] ?></div>
        <div style="font-family: var(--font-mono); font-size: 0.72rem; color: var(--clr-stone); margin-top: 6px;">
            <a href="/admin/articles.php" style="color: var(--clr-terracotta);">Manage &rarr;</a>
        </div>
    </div>

    <div class="metric-card cut-corner">
        <div class="metric-label">Keynotes &amp; Talks</div>
        <div class="metric-val"><?= $metrics['talks'] ?></div>
        <div style="font-family: var(--font-mono); font-size: 0.72rem; color: var(--clr-stone); margin-top: 6px;">
            <a href="/admin/talks.php" style="color: var(--clr-terracotta);">Manage &rarr;</a>
        </div>
    </div>

    <div class="metric-card cut-corner <?= $metrics['unread_msgs'] > 0 ? 'accent' : '' ?>">
        <div class="metric-label">Pending Inquiries</div>
        <div class="metric-val" style="<?= $metrics['unread_msgs'] > 0 ? 'color: var(--clr-terracotta);' : '' ?>">
            <?= $metrics['unread_msgs'] ?>
        </div>
        <div style="font-family: var(--font-mono); font-size: 0.72rem; color: var(--clr-stone); margin-top: 6px;">
            <a href="/admin/messages.php" style="color: var(--clr-terracotta);">View Inquiries &rarr;</a>
        </div>
    </div>
</div>

<!-- Recent Messages Table -->
<div class="table-card cut-corner">
    <div class="table-header">
        <div style="display: flex; align-items: center; gap: 10px;">
            <span style="color: var(--clr-sand); font-weight: bold;">RECENT CONTACT SUBMISSIONS</span>
            <span class="binary-tag" style="font-size: 0.7rem;">INBOX STREAM</span>
        </div>
        <a href="/admin/messages.php" class="btn btn-secondary btn-sm">VIEW ALL</a>
    </div>

    <?php if (empty($recentMessages)): ?>
        <div style="padding: 36px; text-align: center; font-family: var(--font-mono); color: var(--clr-stone);">
            [0x00] // No contact transmissions recorded yet. All queues clear.
        </div>
    <?php else: ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>STATUS</th>
                    <th>SENDER</th>
                    <th>EMAIL COORDINATE</th>
                    <th>SUBJECT</th>
                    <th>TIMESTAMP</th>
                    <th>ACTIONS</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recentMessages as $msg): ?>
                    <tr>
                        <td>
                            <?php if (!$msg['is_read']): ?>
                                <span class="badge-unread">NEW</span>
                            <?php else: ?>
                                <span style="color: var(--clr-stone); font-size: 0.75rem;">READ</span>
                            <?php endif; ?>
                        </td>
                        <td><strong><?= e($msg['name']) ?></strong></td>
                        <td><?= e($msg['email']) ?></td>
                        <td><?= e($msg['subject'] ?: 'Direct Message') ?></td>
                        <td style="color: var(--clr-stone); font-size: 0.78rem;"><?= e($msg['created_at']) ?></td>
                        <td>
                            <a href="/admin/messages.php?action=view&id=<?= (int)$msg['id'] ?>" class="btn btn-secondary btn-sm">
                                INSPECT
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
