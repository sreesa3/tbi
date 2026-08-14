<?php
declare(strict_types=1);
require dirname(__DIR__) . '/includes/app.php';
tbi_require_admin();

$id = (int) ($_GET['id'] ?? 0);
if ($id < 1) {
    header('Location: index.php');
    exit;
}

$pdo = tbi_pdo();
$st = $pdo->prepare('SELECT * FROM applications WHERE id = ?');
$st->execute([$id]);
$app = $st->fetch();
if (!$app) {
    http_response_code(404);
    echo 'Not found';
    exit;
}

$edu = $pdo->prepare('SELECT * FROM education WHERE application_id = ? ORDER BY sort_order');
$edu->execute([$id]);
$eduRows = $edu->fetchAll();

$costs = $pdo->prepare('SELECT * FROM project_costs WHERE application_id = ? ORDER BY sort_order');
$costs->execute([$id]);
$costRows = $costs->fetchAll();

$refs = $pdo->prepare('SELECT * FROM application_references WHERE application_id = ? ORDER BY ref_number');
$refs->execute([$id]);
$refRows = $refs->fetchAll();

$files = $pdo->prepare('SELECT * FROM application_files WHERE application_id = ?');
$files->execute([$id]);
$fileRows = $files->fetchAll();

function row(string $label, ?string $value): void
{
    echo '<p><strong>' . tbi_h($label) . ':</strong> ' . nl2br(tbi_h($value ?? '')) . '</p>';
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Application #<?php echo $id; ?></title>
    <link rel="stylesheet" href="../styles/main.css" />
  </head>
  <body>
    <header class="site-header">
      <div class="container site-header__inner">
        <a href="index.php">All applications</a>
      </div>
    </header>
    <main class="section">
      <div class="container form-wrap">
        <h2><?php echo tbi_h($app['entrepreneur_name']); ?></h2>
        <?php
        row('Submitted', $app['created_at']);
        row('Email', $app['email']);
        row('Mobile', $app['phone_mobile']);
        row('Phone res', $app['phone_res']);
        row('Phone office', $app['phone_off']);
        row('Age', $app['age'] !== null ? (string) $app['age'] : '');
        row('Date of birth', $app['date_of_birth']);
        row('Communication address', $app['communication_address']);
        row('Permanent address', $app['permanent_address']);
        row('Skills', $app['skills_experience']);
        row('Type of business', $app['type_of_business']);
        row('Organization', $app['organization_name']);
        row('Product / business', $app['product_description']);
        row('Start-up year', $app['startup_year']);
        row('Why entrepreneur', $app['why_entrepreneur']);
        row('Legal position', $app['legal_position']);
        row('Services expected', str_replace('|', ', ', (string) $app['services_expected']));
        row('Team', $app['team_details']);
        row('FT / PT / consultants', trim($app['employees_fulltime'] . ' / ' . $app['employees_parttime'] . ' / ' . $app['employees_consultants']));
        row('Employees in org', $app['employees_org']);
        row('Promoter', $app['promoter_name']);
        row('Promoter qualification', $app['promoter_qualification']);
        row('Promoter designation', $app['promoter_designation']);
        row('Promoter experience', $app['promoter_experience']);
        row('Promoter communication', $app['promoter_communication_address']);
        row('Promoter permanent', $app['promoter_permanent_address']);
        row('Promoter phones', trim($app['promoter_phone_res'] . ' / ' . $app['promoter_phone_off'] . ' / ' . $app['promoter_phone_mobile']));
        row('Promoter email', $app['promoter_email']);
        row('Promoter fax', $app['promoter_fax']);
        row('Place', $app['place']);
        row('Agreed rules', $app['agreed_rules'] ? 'Yes' : 'No');
        ?>

        <h3>Education</h3>
        <?php foreach ($eduRows as $e): ?>
          <p><?php echo tbi_h(implode(' · ', array_filter([$e['class_course'], $e['college'], $e['branch'], $e['university_board'], $e['year_of_pass'], $e['percent_secured']]))); ?></p>
        <?php endforeach; ?>

        <h3>Project cost</h3>
        <?php foreach ($costRows as $c): ?>
          <p><?php echo tbi_h($c['item_name'] . ' — ' . $c['amount']); ?></p>
        <?php endforeach; ?>

        <h3>References</h3>
        <?php foreach ($refRows as $r): ?>
          <p>
            <strong>Ref <?php echo (int) $r['ref_number']; ?>:</strong>
            <?php echo tbi_h($r['ref_name']); ?> —
            <?php echo tbi_h($r['designation']); ?><br />
            <?php echo nl2br(tbi_h($r['address'])); ?><br />
            <?php echo tbi_h($r['phone'] . ' · ' . $r['email']); ?>
          </p>
        <?php endforeach; ?>

        <h3>Files</h3>
        <?php if (!$fileRows): ?>
          <p>None</p>
        <?php else: ?>
          <ul>
            <?php foreach ($fileRows as $f): ?>
              <li>
                <a href="download.php?id=<?php echo (int) $f['id']; ?>">
                  <?php echo tbi_h($f['kind'] . ': ' . $f['original_name']); ?>
                </a>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      </div>
    </main>
  </body>
</html>
