<?php
/**
 * Shared Helper & Security Functions
 */

if (session_status() === PHP_SESSION_NONE) {
    // Secure session cookies
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_samesite', 'Strict');
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        ini_set('session.cookie_secure', 1);
    }
    session_start();
}

/**
 * Escape HTML output for XSS prevention
 */
function e(?string $value): string {
    return htmlspecialchars($value ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Generate or retrieve CSRF token
 */
function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Output hidden CSRF token input tag
 */
function csrf_field(): string {
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

/**
 * Verify CSRF token from POST request
 */
function csrf_verify(): bool {
    $token = $_POST['csrf_token'] ?? '';
    if (empty($token) || empty($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Set flash message
 */
function set_flash(string $type, string $message): void {
    $_SESSION['flash'] = [
        'type' => $type, // 'success', 'danger', 'info'
        'message' => $message
    ];
}

/**
 * Get and clear flash message
 */
function get_flash(): ?array {
    if (!empty($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Generate clean URL slug
 */
function slugify(string $text): string {
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    $text = strtolower($text);
    return empty($text) ? 'item-' . time() : $text;
}

/**
 * Securely validate and upload files
 * Checks MIME type via finfo, size, and whitelist extension
 *
 * @param array $file $_FILES['input_name']
 * @param string $destinationDir Subfolder inside /uploads/
 * @param int $maxSizeBytes Max allowed size (default 10MB)
 * @return string Relative URL of uploaded file
 * @throws Exception
 */
function handle_file_upload(array $file, string $destinationDir = '', int $maxSizeBytes = 10485760): string {
    if (!isset($file['error']) || is_array($file['error'])) {
        throw new Exception('Invalid upload parameters.');
    }

    switch ($file['error']) {
        case UPLOAD_ERR_OK:
            break;
        case UPLOAD_ERR_NO_FILE:
            throw new Exception('No file was uploaded.');
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            throw new Exception('Uploaded file exceeds maximum allowed size.');
        default:
            throw new Exception('Unknown upload error occurred.');
    }

    if ($file['size'] > $maxSizeBytes) {
        throw new Exception('File size exceeds ' . ($maxSizeBytes / 1048576) . 'MB limit.');
    }

    // Whitelist mime types and extensions
    $allowedMimes = [
        'jpg'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png'  => 'image/png',
        'webp' => 'image/webp',
        'gif'  => 'image/gif',
        'mp4'  => 'video/mp4',
        'pdf'  => 'application/pdf'
    ];

    // Validate MIME type with PHP finfo
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    $ext = array_search($mime, $allowedMimes, true);

    if ($ext === false) {
        throw new Exception('Invalid file format. Allowed: JPG, PNG, WEBP, GIF, MP4, PDF.');
    }

    // Secondary extension check
    $originalExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if ($originalExt === 'jpeg') $originalExt = 'jpg';
    if ($originalExt !== $ext && !($ext === 'jpg' && in_array($originalExt, ['jpg', 'jpeg']))) {
        throw new Exception('File extension does not match true MIME signature.');
    }

    // Target upload directory
    $baseDir = __DIR__ . '/../uploads';
    if (!empty($destinationDir)) {
        $baseDir .= '/' . trim($destinationDir, '/\\');
    }
    if (!is_dir($baseDir)) {
        mkdir($baseDir, 0755, true);
    }

    // Generate non-guessable random file name
    $newFileName = sprintf('%s_%s.%s', date('Ymd_His'), bin2hex(random_bytes(8)), $ext);
    $destination = $baseDir . '/' . $newFileName;

    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        throw new Exception('Failed to move uploaded file to target directory.');
    }

    $relativePath = '/uploads/' . (!empty($destinationDir) ? trim($destinationDir, '/') . '/' : '') . $newFileName;
    return $relativePath;
}
