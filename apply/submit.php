<?php
declare(strict_types=1);
require dirname(__DIR__) . '/includes/app.php';

tbi_start_session();

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Location: index.php');
    exit;
}

function fail(string $message, array $old): void
{
    $_SESSION['form_error'] = $message;
    $_SESSION['form_old'] = $old;
    header('Location: index.php');
    exit;
}

$old = $_POST;
unset($old['csrf'], $old['website'], $old['agreed_rules'], $old['services']);

if (!tbi_csrf_ok($_POST['csrf'] ?? null)) {
    fail('Your session expired. Please submit again.', $old);
}

if (tbi_str($_POST['website'] ?? '', 80) !== '') {
    header('Location: success.php');
    exit;
}

$entrepreneur = tbi_str($_POST['entrepreneur_name'] ?? '', 200);
$comm = tbi_str($_POST['communication_address'] ?? '', 2000);
$perm = tbi_str($_POST['permanent_address'] ?? '', 2000);
$mobile = tbi_str($_POST['phone_mobile'] ?? '', 40);
$email = tbi_str($_POST['email'] ?? '', 190);
$product = tbi_str($_POST['product_description'] ?? '', 8000);
$place = tbi_str($_POST['place'] ?? '', 120);
$agreed = isset($_POST['agreed_rules']) && $_POST['agreed_rules'] === '1';

if ($entrepreneur === '' || $comm === '' || $perm === '' || $mobile === '' || $email === '' || $product === '' || $place === '') {
    fail('Please fill all required fields.', $old);
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    fail('Please enter a valid email address.', $old);
}
if (!$agreed) {
    fail('You must agree to the rules and regulations.', $old);
}

$age = tbi_str($_POST['age'] ?? '', 3);
$ageVal = ($age !== '' && ctype_digit($age)) ? (int) $age : null;
$dob = tbi_str($_POST['date_of_birth'] ?? '', 10);
if ($dob !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $dob)) {
    $dob = '';
}

$legalOk = ['', 'Proprietorship', 'Partnership', 'Corporation', 'Private'];
$legal = tbi_str($_POST['legal_position'] ?? '', 40);
if (!in_array($legal, $legalOk, true)) {
    $legal = '';
}

$services = $_POST['services'] ?? [];
if (!is_array($services)) {
    $services = [];
}
$services = array_values(array_unique(array_map(static function ($s) {
    return tbi_str((string) $s, 120);
}, $services)));
$servicesJson = implode('|', $services);

$cfg = tbi_config();
$ip = tbi_client_ip();
$pdo = tbi_pdo();

$limit = (int) ($cfg['rate_limit_seconds'] ?? 60);
if ($ip !== '' && $limit > 0) {
    $st = $pdo->prepare('SELECT created_at FROM applications WHERE ip_address = ? ORDER BY id DESC LIMIT 1');
    $st->execute([$ip]);
    $last = $st->fetchColumn();
    if ($last && (time() - strtotime((string) $last)) < $limit) {
        fail('Please wait a minute before submitting again.', $old);
    }
}

$pdo->beginTransaction();
try {
    $ins = $pdo->prepare(
        'INSERT INTO applications (
            entrepreneur_name, age, date_of_birth, communication_address, permanent_address,
            phone_res, phone_off, phone_mobile, email, skills_experience, type_of_business,
            organization_name, product_description, startup_year, why_entrepreneur, legal_position,
            services_expected, team_details, employees_fulltime, employees_parttime, employees_consultants,
            employees_org, promoter_name, promoter_qualification, promoter_designation, promoter_experience,
            promoter_communication_address, promoter_permanent_address, promoter_phone_res, promoter_phone_off,
            promoter_phone_mobile, promoter_email, promoter_fax, place, agreed_rules, ip_address
        ) VALUES (
            ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?
        )'
    );
    $ins->execute([
        $entrepreneur,
        $ageVal,
        $dob !== '' ? $dob : null,
        $comm,
        $perm,
        tbi_str($_POST['phone_res'] ?? '', 40),
        tbi_str($_POST['phone_off'] ?? '', 40),
        $mobile,
        $email,
        tbi_str($_POST['skills_experience'] ?? '', 4000),
        tbi_str($_POST['type_of_business'] ?? '', 200),
        tbi_str($_POST['organization_name'] ?? '', 200),
        $product,
        tbi_str($_POST['startup_year'] ?? '', 10),
        tbi_str($_POST['why_entrepreneur'] ?? '', 8000),
        $legal !== '' ? $legal : null,
        $servicesJson,
        tbi_str($_POST['team_details'] ?? '', 4000),
        tbi_str($_POST['employees_fulltime'] ?? '', 20),
        tbi_str($_POST['employees_parttime'] ?? '', 20),
        tbi_str($_POST['employees_consultants'] ?? '', 20),
        tbi_str($_POST['employees_org'] ?? '', 20),
        tbi_str($_POST['promoter_name'] ?? '', 200),
        tbi_str($_POST['promoter_qualification'] ?? '', 200),
        tbi_str($_POST['promoter_designation'] ?? '', 200),
        tbi_str($_POST['promoter_experience'] ?? '', 80),
        tbi_str($_POST['promoter_communication_address'] ?? '', 2000),
        tbi_str($_POST['promoter_permanent_address'] ?? '', 2000),
        tbi_str($_POST['promoter_phone_res'] ?? '', 40),
        tbi_str($_POST['promoter_phone_off'] ?? '', 40),
        tbi_str($_POST['promoter_phone_mobile'] ?? '', 40),
        tbi_str($_POST['promoter_email'] ?? '', 190),
        tbi_str($_POST['promoter_fax'] ?? '', 40),
        $place,
        1,
        $ip !== '' ? $ip : null,
    ]);
    $appId = (int) $pdo->lastInsertId();

    $eduIns = $pdo->prepare(
        'INSERT INTO education (application_id, class_course, college, branch, university_board, year_of_pass, percent_secured, sort_order)
         VALUES (?,?,?,?,?,?,?,?)'
    );
    $courses = $_POST['edu_course'] ?? [];
    if (!is_array($courses)) {
        $courses = [];
    }
    $n = count($courses);
    for ($i = 0; $i < $n; $i++) {
        $row = [
            tbi_str($courses[$i] ?? '', 160),
            tbi_str(($_POST['edu_college'][$i] ?? ''), 200),
            tbi_str(($_POST['edu_branch'][$i] ?? ''), 160),
            tbi_str(($_POST['edu_board'][$i] ?? ''), 160),
            tbi_str(($_POST['edu_year'][$i] ?? ''), 20),
            tbi_str(($_POST['edu_percent'][$i] ?? ''), 20),
        ];
        if (implode('', $row) === '') {
            continue;
        }
        $eduIns->execute(array_merge([$appId], $row, [$i]));
    }

    $costIns = $pdo->prepare(
        'INSERT INTO project_costs (application_id, item_name, amount, sort_order) VALUES (?,?,?,?)'
    );
    $items = $_POST['cost_item'] ?? [];
    if (!is_array($items)) {
        $items = [];
    }
    $cn = count($items);
    for ($i = 0; $i < $cn; $i++) {
        $item = tbi_str($items[$i] ?? '', 200);
        $amt = tbi_str(($_POST['cost_amount'][$i] ?? ''), 40);
        if ($item === '' && $amt === '') {
            continue;
        }
        $costIns->execute([$appId, $item, $amt, $i]);
    }

    $refIns = $pdo->prepare(
        'INSERT INTO application_references (application_id, ref_number, ref_name, designation, address, phone, email)
         VALUES (?,?,?,?,?,?,?)'
    );
    for ($n = 1; $n <= 2; $n++) {
        $refIns->execute([
            $appId,
            $n,
            tbi_str($_POST["ref{$n}_name"] ?? '', 200),
            tbi_str($_POST["ref{$n}_designation"] ?? '', 160),
            tbi_str($_POST["ref{$n}_address"] ?? '', 2000),
            tbi_str($_POST["ref{$n}_phone"] ?? '', 40),
            tbi_str($_POST["ref{$n}_email"] ?? '', 190),
        ]);
    }

    $fileIns = $pdo->prepare(
        'INSERT INTO application_files (application_id, kind, original_name, stored_name, mime, size_bytes)
         VALUES (?,?,?,?,?,?)'
    );
    $appDir = tbi_uploads_dir() . '/' . $appId;
    if (!is_dir($appDir)) {
        mkdir($appDir, 0750, true);
    }

    $specs = [
        'photo' => [
            'max' => 2 * 1024 * 1024,
            'mimes' => ['image/jpeg' => 'jpg', 'image/png' => 'png'],
        ],
        'writeup' => [
            'max' => 5 * 1024 * 1024,
            'mimes' => [
                'application/pdf' => 'pdf',
                'application/msword' => 'doc',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
            ],
        ],
    ];

    foreach ($specs as $field => $spec) {
        if (empty($_FILES[$field]) || !is_array($_FILES[$field])) {
            continue;
        }
        $err = (int) ($_FILES[$field]['error'] ?? UPLOAD_ERR_NO_FILE);
        if ($err === UPLOAD_ERR_NO_FILE) {
            continue;
        }
        if ($err !== UPLOAD_ERR_OK) {
            throw new RuntimeException('File upload failed. Please try a smaller file.');
        }
        $size = (int) $_FILES[$field]['size'];
        $tmp = (string) $_FILES[$field]['tmp_name'];
        if ($size <= 0 || $size > $spec['max'] || !is_uploaded_file($tmp)) {
            throw new RuntimeException('A file was too large or invalid.');
        }
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($tmp) ?: '';
        if (!isset($spec['mimes'][$mime])) {
            throw new RuntimeException('File type not allowed.');
        }
        $stored = $field . '-' . bin2hex(random_bytes(8)) . '.' . $spec['mimes'][$mime];
        if (!move_uploaded_file($tmp, $appDir . '/' . $stored)) {
            throw new RuntimeException('Could not save uploaded file.');
        }
        $orig = tbi_str((string) $_FILES[$field]['name'], 255);
        $fileIns->execute([$appId, $field, $orig, $stored, $mime, $size]);
    }

    $pdo->commit();
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollback();
    }
    fail('Could not save the application. Please try again later.', $old);
}

$notify = (string) ($cfg['notify_email'] ?? '');
if ($notify !== '' && filter_var($notify, FILTER_VALIDATE_EMAIL)) {
    $from = (string) ($cfg['from_email'] ?? $notify);
    $subj = 'New TBI incubatee application: ' . $entrepreneur;
    $body = "A new application was submitted.\n\nName: {$entrepreneur}\nEmail: {$email}\nMobile: {$mobile}\nID: {$appId}\n";
    @mail($notify, $subj, $body, 'From: ' . $from . "\r\nContent-Type: text/plain; charset=UTF-8");
}

$_SESSION['csrf'] = bin2hex(random_bytes(32));
header('Location: success.php');
exit;
