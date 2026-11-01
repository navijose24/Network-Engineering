<?php
/**
 * Admin Logout Action
 */

require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

admin_logout();

header('Location: /admin/login.php');
exit;
