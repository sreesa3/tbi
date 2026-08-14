<?php
declare(strict_types=1);
require dirname(__DIR__) . '/includes/app.php';
tbi_require_admin();

$id = (int) ($_GET['id'] ?? 0);
$st = tbi_pdo()->prepare('SELECT * FROM application_files WHERE id = ?');
$st->execute([$id]);
$file = $st->fetch();
if (!$file) {
    http_response_code(404);
    echo 'Not found';
    exit;
}

$path = tbi_uploads_dir() . '/' . (int) $file['application_id'] . '/' . $file['stored_name'];
if (!is_readable($path)) {
    http_response_code(404);
    echo 'File missing';
    exit;
}

header('Content-Type: ' . $file['mime']);
header('Content-Disposition: attachment; filename="' . str_replace(['"', "\r", "\n"], '', $file['original_name']) . '"');
header('X-Content-Type-Options: nosniff');
readfile($path);
exit;
