<?php
/**
 * Admin Contact Inquiries & Transmission Messages Management
 */

$pageTitle = 'Contact Inquiries';
require_once __DIR__ . '/includes/admin_header.php';

$action = $_GET['action'] ?? 'list';
$msgId = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// Handle DELETE
if ($action === 'delete' && $msgId > 0) {
    if (isset($_GET['csrf']) && hash_equals($_SESSION['csrf_token'] ?? '', $_GET['csrf'])) {
        if (delete_contact_submission($msgId)) {
            set_flash('success', 'Transmission record purged.');
        } else {
            set_flash('danger', 'Failed to purge transmission record.');
        }
    }
    header('Location: /admin/messages.php');
    exit;
}

// Handle MARK READ / UNREAD
if ($action === 'toggle_read' && $msgId > 0) {
    $status = isset($_GET['status']) ? (int) $_GET['status'] : 1;
    mark_contact_submission_read($msgId, $status);
    header('Location: /admin/messages.php');
    exit;
}

// View single message
$viewMessage = null;
if ($action === 'view' && $msgId > 0) {
    $viewMessage = get_contact_submission_by_id($msgId);
    if ($viewMessage) {
        // Auto mark as read on inspection
        mark_contact_submission_read($msgId, 1);
    }
}

$allMessages = get_contact_submissions_list(150);
?>

<div class="page-header">
    <div>
        <h1 class="page-title">[0x08] // INCOMING INQUIRIES &amp; TRANSMISSIONS</h1>
        <p style="font-family: var(--font-mono); font-size: 0.85rem; color: var(--clr-stone); margin-top: 4px;">
            Incoming communications routed through the portfolio terminal gateway.
        </p>
    </div>
</div>

<?php if ($viewMessage): ?>
    <!-- Single Message Detail Card -->
    <div class="form-card cut-corner" style="margin-bottom: 36px;">
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--clr-stone); padding-bottom: 14px; margin-bottom: 20px;">
            <div>
                <span class="binary-tag accent">INSPECTING TRANSMISSION #<?= (int)$viewMessage['id'] ?></span>
                <span style="font-family: var(--font-mono); font-size: 0.8rem; color: var(--clr-stone); margin-left: 10px;">
                    Received: <?= e($viewMessage['created_at']) ?>
                </span>
            </div>
            <a href="/admin/messages.php" class="btn btn-secondary btn-sm">&larr; BACK TO LIST</a>
        </div>

        <div class="telemetry-feed" style="margin-bottom: 20px;">
            <div class="telemetry-row">
                <span class="key">SENDER NAME:</span>
                <span class="val"><?= e($viewMessage['name']) ?></span>
            </div>
            <div class="telemetry-row">
                <span class="key">EMAIL COORDINATE:</span>
                <span class="val"><a href="mailto:<?= e($viewMessage['email']) ?>" style="color: var(--clr-sand); text-decoration: underline;"><?= e($viewMessage['email']) ?></a></span>
            </div>
            <div class="telemetry-row">
                <span class="key">PACKET SUBJECT:</span>
                <span class="val"><?= e($viewMessage['subject'] ?: 'Direct Transmission') ?></span>
            </div>
            <div class="telemetry-row">
                <span class="key">SOURCE IP ADDRESS:</span>
                <span class="val"><?= e($viewMessage['ip_address'] ?? 'Undisclosed') ?></span>
            </div>
        </div>

        <h4 style="font-size: 0.9rem; color: var(--clr-sand); text-transform: uppercase; margin-bottom: 10px;">PAYLOAD MESSAGE</h4>
        <div style="background: var(--clr-coal); border: 2px solid var(--clr-stone-dark); padding: 24px; font-family: var(--font-mono); font-size: 0.95rem; line-height: 1.7; color: var(--clr-sand); white-space: pre-wrap; margin-bottom: 24px;">
<?= e($viewMessage['message']) ?>
        </div>

        <div style="display: flex; gap: 12px; justify-content: flex-end;">
            <a href="mailto:<?= e($viewMessage['email']) ?>?subject=Re: <?= urlencode($viewMessage['subject'] ?? 'Inquiry') ?>" class="btn btn-primary btn-sm">
                REPLY VIA EMAIL &rarr;
            </a>
            <a href="/admin/messages.php?action=delete&id=<?= (int)$viewMessage['id'] ?>&csrf=<?= e(csrf_token()) ?>" 
               class="btn btn-danger btn-sm" 
               data-confirm="Purge this transmission permanently?">
                PURGE TRANSMISSION
            </a>
        </div>
    </div>
<?php endif; ?>

<div class="table-card cut-corner">
    <div class="table-header">
        <span style="font-weight: bold; color: var(--clr-sand);">ALL RECORDED INQUIRIES (<?= count($allMessages) ?>)</span>
        <span class="binary-tag">QUEUE</span>
    </div>

    <?php if (empty($allMessages)): ?>
        <div style="padding: 40px; text-align: center; font-family: var(--font-mono); color: var(--clr-stone);">
            [0x00] // No transmissions logged. Queue is clear.
        </div>
    <?php else: ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 70px;">STATUS</th>
                    <th>SENDER</th>
                    <th>EMAIL</th>
                    <th>SUBJECT</th>
                    <th>TIMESTAMP</th>
                    <th>ACTIONS</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($allMessages as $msg): ?>
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
                        <td><?= e($msg['subject'] ?: 'Direct Inquiry') ?></td>
                        <td style="color: var(--clr-stone); font-size: 0.78rem;"><?= e($msg['created_at']) ?></td>
                        <td>
                            <div style="display: flex; gap: 6px;">
                                <a href="/admin/messages.php?action=view&id=<?= (int)$msg['id'] ?>" class="btn btn-secondary btn-sm">
                                    INSPECT
                                </a>
                                <a href="/admin/messages.php?action=delete&id=<?= (int)$msg['id'] ?>&csrf=<?= e(csrf_token()) ?>" 
                                   class="btn btn-danger btn-sm" 
                                   data-confirm="Delete message from <?= e($msg['name']) ?>?">
                                    DEL
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
