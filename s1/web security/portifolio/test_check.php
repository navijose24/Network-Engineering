<?php
/**
 * Self-Check Test Suite (Assert-based, no external dependencies)
 * Verifies core security routines, CSRF verification, password hashing, and DAL contracts
 */

// Enable assertions
ini_set('zend.assertions', 1);
ini_set('assert.exception', 1);

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

echo "========================================================\n";
echo "PORTFOLIO SYSTEM SELF-CHECK (Core Logic & Security)\n";
echo "========================================================\n";

$testsRun = 0;
$testsPassed = 0;

function run_test(string $name, callable $fn): void {
    global $testsRun, $testsPassed;
    $testsRun++;
    try {
        $fn();
        $testsPassed++;
        echo " [PASS] {$name}\n";
    } catch (Throwable $e) {
        echo " [FAIL] {$name}: " . $e->getMessage() . "\n";
    }
}

// 1. Test XSS HTML Escaping
run_test("XSS Escaping with e()", function () {
    $raw = '<script>alert("XSS")</script>&"\'';
    $escaped = e($raw);
    assert(!str_contains($escaped, '<script>'), "Tags must be escaped");
    assert(str_contains($escaped, '&lt;script&gt;'), "Must convert to &lt;");
    assert(str_contains($escaped, '&quot;'), "Must escape double quotes");
    assert(str_contains($escaped, '&#039;'), "Must escape single quotes");
});

// 2. Test CSRF Token Generation & Verification
run_test("CSRF Token Cryptographic Integrity", function () {
    $token = csrf_token();
    assert(strlen($token) === 64, "Token should be 64-char hex string (32 bytes)");

    // Simulated valid POST
    $_POST['csrf_token'] = $token;
    assert(csrf_verify() === true, "Valid CSRF token should verify");

    // Simulated tampered POST
    $_POST['csrf_token'] = 'tampered_invalid_token_1234567890';
    assert(csrf_verify() === false, "Tampered CSRF token should fail verification");
});

// 3. Test Slugify Function
run_test("URL Slug Generation", function () {
    $title = "Quantum Consciousness Transfer: Science & Speculation!";
    $slug = slugify($title);
    assert($slug === "quantum-consciousness-transfer-science-speculation", "Slug must be lowercase alphanumeric with hyphens");
});

// 4. Test Password Hashing & Verification
run_test("BCRYPT Password Hash Verification", function () {
    $password = "Admin@12345";
    $hash = password_hash($password, PASSWORD_BCRYPT);
    assert(password_verify($password, $hash) === true, "password_verify must pass with correct password");
    assert(password_verify("WrongPassword", $hash) === false, "password_verify must reject incorrect password");

    // Test seed hash
    $seedHash = '$2y$12$UmsUTKjGEZEZ1C/nRyyJM.ojO0u4vLMJNrxM5f0OQu7EdPN6luM0u';
    assert(password_verify('Admin@12345', $seedHash) === true, "Seed admin hash must match default credentials");
});

// 5. Test Schema & Seed SQL File Integrity
run_test("SQL Schema and Seed Artifacts", function () {
    $schemaPath = __DIR__ . '/database/schema.sql';
    $seedPath = __DIR__ . '/database/seed.sql';

    assert(file_exists($schemaPath), "database/schema.sql must exist");
    assert(file_exists($seedPath), "database/seed.sql must exist");

    $schemaContent = file_get_contents($schemaPath);
    $seedContent = file_get_contents($seedPath);

    $requiredTables = [
        'admins', 'profile', 'projects', 'project_technologies',
        'project_bullets', 'skill_categories', 'skills',
        'achievements', 'talks', 'articles', 'article_tags',
        'creative_works', 'contact_submissions', 'site_settings'
    ];

    foreach ($requiredTables as $tbl) {
        assert(str_contains($schemaContent, "CREATE TABLE IF NOT EXISTS `{$tbl}`"), "Schema must define table: {$tbl}");
        assert(str_contains($seedContent, "`{$tbl}`"), "Seed data must populate table: {$tbl}");
    }
});

// 6. Test Upload Validation Logic
run_test("File Upload Security Constraints", function () {
    $dummyFile = [
        'name' => 'malicious.php',
        'type' => 'application/x-php',
        'tmp_name' => __FILE__,
        'error' => UPLOAD_ERR_OK,
        'size' => 1024
    ];

    $failedAsExpected = false;
    try {
        handle_file_upload($dummyFile);
    } catch (Exception $e) {
        $failedAsExpected = true;
    }
    assert($failedAsExpected === true, "handle_file_upload must reject unauthorized MIME types and extensions");
});

echo "========================================================\n";
echo "RESULTS: {$testsPassed} / {$testsRun} tests passed successfully.\n";
echo "========================================================\n";
