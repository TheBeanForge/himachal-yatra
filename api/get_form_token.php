<?php
declare(strict_types=1);
// Returns the session's public-form CSRF token (minting one if needed).
// Lets the lead forms recover automatically when the page was loaded
// long ago and the session expired — common on mobile, where the browser
// restores a days-old tab. No DB needed, so it also works while MySQL is down.
session_start();
header('Content-Type: application/json');
header('Cache-Control: no-store');
if (empty($_SESSION['lead_form_token'])) {
    $_SESSION['lead_form_token'] = bin2hex(random_bytes(16));
}
echo json_encode(['token' => $_SESSION['lead_form_token']]);
