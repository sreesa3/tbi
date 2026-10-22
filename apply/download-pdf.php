<?php
declare(strict_types=1);
require dirname(__DIR__) . '/includes/app.php';

tbi_start_session();

$appId = (int) ($_SESSION['last_application_id'] ?? 0);
if ($appId < 1) {
    http_response_code(403);
    echo 'No recent application PDF available. Please submit the form again.';
    exit;
}

$path = tbi_uploads_dir() . '/' . $appId . '/application.pdf';
if (!is_readable($path)) {
    http_response_code(404);
    echo 'PDF not found. Your application was saved; contact TBI if you need a copy.';
    exit;
}

$filename = 'TBI-application-' . $appId . '.pdf';
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Content-Length: ' . (string) filesize($path));
header('X-Content-Type-Options: nosniff');
readfile($path);
exit;
