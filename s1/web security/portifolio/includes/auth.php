<?php
/**
 * Admin Authentication & Session Management
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/functions.php';

/**
 * Check if the admin is authenticated
 */
function is_admin_logged_in(): bool {
    return !empty($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

/**
 * Get current admin user record
 */
function get_admin_user(): ?array {
    return $_SESSION['admin_user'] ?? null;
}

/**
 * Route protection guard: redirect to login if unauthenticated
 */
function require_admin(): void {
    if (!is_admin_logged_in()) {
        set_flash('danger', 'Access denied. Please authenticate to access the admin console.');
        header('Location: /admin/login.php');
        exit;
    }
}

/**
 * Attempt admin login with prepared statement & password_verify
 */
function admin_login(string $identifier, string $password): bool {
    try {
        $db = get_db();
        $stmt = $db->prepare("
            SELECT id, username, email, password_hash
            FROM admins
            WHERE username = :id1 OR email = :id2
            LIMIT 1
        ");
        $stmt->execute([':id1' => $identifier, ':id2' => $identifier]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password_hash'])) {
            // Mitigate session fixation by regenerating session ID upon privilege escalation
            session_regenerate_id(true);
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_user'] = [
                'id' => (int) $admin['id'],
                'username' => $admin['username'],
                'email' => $admin['email']
            ];
            return true;
        }
    } catch (PDOException $e) {
        error_log("Authentication failure: " . $e->getMessage());
    }
    return false;
}

/**
 * Log out admin securely
 */
function admin_logout(): void {
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }
    session_destroy();
}
