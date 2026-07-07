<?php
declare(strict_types=1);
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/vars.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Method not allowed']);
    exit;
}

// Honeypot — silently accept and discard bot submissions.
if (!empty($_POST['website'] ?? '')) { echo json_encode(['ok' => true]); exit; }

// Same per-session token as the lead/review forms.
$sent = $_POST['csrf_token'] ?? '';
$sess = $_SESSION['lead_form_token'] ?? '';
if ($sess === '' || !hash_equals($sess, $sent)) {
    http_response_code(419);
    echo json_encode(['ok' => false, 'error' => 'Session expired. Please refresh and try again.']);
    exit;
}

// Light rate limit: 5 attempts / 10 minutes per session.
$rl = $_SESSION['nl_rl'] ?? ['c' => 0, 'w' => time()];
if (time() - $rl['w'] > 600) $rl = ['c' => 0, 'w' => time()];
if ($rl['c'] >= 5) {
    http_response_code(429);
    echo json_encode(['ok' => false, 'error' => 'Too many attempts. Please try again later.']);
    exit;
}

$email = mb_substr(trim((string)($_POST['email'] ?? '')), 0, 190);
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'error' => 'Please enter a valid email address.']);
    exit;
}

if (!$conn instanceof mysqli) {
    http_response_code(503);
    echo json_encode(['ok' => false, 'error' => 'Service temporarily unavailable. Please try again.']);
    exit;
}

// Table is created on demand so older installs need no manual migration.
try {
    $conn->query(
        "CREATE TABLE IF NOT EXISTS newsletter_subscribers (
            id         INT AUTO_INCREMENT PRIMARY KEY,
            email      VARCHAR(190) NOT NULL UNIQUE,
            source     VARCHAR(30) DEFAULT 'footer',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
    );
    $stmt = $conn->prepare(
        'INSERT INTO newsletter_subscribers (email, source) VALUES (?, ?)
         ON DUPLICATE KEY UPDATE email = email'
    );
    $src = 'footer';
    $stmt->bind_param('ss', $email, $src);
    $stmt->execute();
    $stmt->close();
} catch (mysqli_sql_exception) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Could not subscribe right now. Please try again.']);
    exit;
}

$rl['c']++;
$_SESSION['nl_rl'] = $rl;
$conn->close();
echo json_encode(['ok' => true]);
