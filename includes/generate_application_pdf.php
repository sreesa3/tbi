<?php
declare(strict_types=1);

require_once __DIR__ . '/pdf_application_template.php';

/**
 * Build a filled application PDF for the given application id.
 * Returns the absolute path to the written file.
 */
function tbi_generate_application_pdf(PDO $pdo, int $applicationId): string
{
    $autoload = dirname(__DIR__) . '/vendor/autoload.php';
    if (!is_readable($autoload)) {
        throw new RuntimeException('Dompdf is not installed. Run: composer install');
    }
    require_once $autoload;

    $st = $pdo->prepare('SELECT * FROM applications WHERE id = ?');
    $st->execute([$applicationId]);
    $app = $st->fetch();
    if (!$app) {
        throw new RuntimeException('Application not found.');
    }

    $edu = $pdo->prepare('SELECT * FROM education WHERE application_id = ? ORDER BY sort_order, id');
    $edu->execute([$applicationId]);
    $education = $edu->fetchAll();

    $cost = $pdo->prepare('SELECT * FROM project_costs WHERE application_id = ? ORDER BY sort_order, id');
    $cost->execute([$applicationId]);
    $costs = $cost->fetchAll();

    $ref = $pdo->prepare('SELECT * FROM application_references WHERE application_id = ? ORDER BY ref_number, id');
    $ref->execute([$applicationId]);
    $references = $ref->fetchAll();

    $servicesRaw = (string) ($app['services_expected'] ?? '');
    $services = $servicesRaw === '' ? [] : explode('|', $servicesRaw);

    $html = tbi_pdf_application_html([
        'app' => $app,
        'education' => $education,
        'costs' => $costs,
        'references' => $references,
        'services' => $services,
    ]);

    $options = new \Dompdf\Options();
    $options->set('isRemoteEnabled', false);
    $options->set('defaultFont', 'DejaVu Sans');
    $dompdf = new \Dompdf\Dompdf($options);
    $dompdf->loadHtml($html, 'UTF-8');
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    $dir = tbi_uploads_dir() . '/' . $applicationId;
    if (!is_dir($dir)) {
        mkdir($dir, 0750, true);
    }
    $path = $dir . '/application.pdf';
    if (file_put_contents($path, $dompdf->output()) === false) {
        throw new RuntimeException('Could not write application PDF.');
    }

    $fileIns = $pdo->prepare(
        'INSERT INTO application_files (application_id, kind, original_name, stored_name, mime, size_bytes)
         VALUES (?,?,?,?,?,?)'
    );
    $size = filesize($path);
    $fileIns->execute([
        $applicationId,
        'application_pdf',
        'application-form-filled.pdf',
        'application.pdf',
        'application/pdf',
        $size !== false ? $size : 0,
    ]);

    return $path;
}
