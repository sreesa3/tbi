<?php
declare(strict_types=1);

/**
 * @param array{
 *   app: array<string, mixed>,
 *   education: list<array<string, mixed>>,
 *   costs: list<array<string, mixed>>,
 *   references: list<array<string, mixed>>,
 *   services: list<string>
 * } $data
 */
function tbi_pdf_application_html(array $data): string
{
    $app = $data['app'];
    $education = $data['education'];
    $costs = $data['costs'];
    $references = $data['references'];
    $services = $data['services'];

    $allServices = [
        'Workspace',
        'Shared office services',
        'Access to specialized equipment',
        'Management assistance',
        'Business planning',
        'Access to finance',
        'Technical assistance (Testing & Quality control etc.)',
        'Networking support',
        'Branding and marketing',
        'Patenting',
        'Mentoring / Counselling',
        'Technology Upgradation (R & D) / Value Addition',
    ];

    $e = static function (?string $v): string {
        return htmlspecialchars((string) $v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    };

    $field = static function (string $label, ?string $value) use ($e): string {
        return '<tr><td class="label">' . $e($label) . '</td><td class="value">' . nl2br($e($value)) . '</td></tr>';
    };

    $ageStr = '';
    if (isset($app['age']) && $app['age'] !== null && $app['age'] !== '') {
        $ageStr = (string) $app['age'];
    }

    $applicantFields = $field('Name of the Entrepreneur', $app['entrepreneur_name'] ?? '')
        . $field('Age', $ageStr)
        . $field('Date of Birth', $app['date_of_birth'] ?? '')
        . $field('Address for Communication', $app['communication_address'] ?? '')
        . $field('Permanent Address', $app['permanent_address'] ?? '')
        . $field('Phone (Res)', $app['phone_res'] ?? '')
        . $field('Phone (Office)', $app['phone_off'] ?? '')
        . $field('Mobile', $app['phone_mobile'] ?? '')
        . $field('Email', $app['email'] ?? '')
        . $field('Innovative Skills & Experience', $app['skills_experience'] ?? '')
        . $field('Type of Business', $app['type_of_business'] ?? '')
        . $field('Name of the Organization', $app['organization_name'] ?? '');

    $eduRows = '';
    if ($education === []) {
        $eduRows = '<tr><td colspan="6" class="muted">—</td></tr>';
    } else {
        foreach ($education as $row) {
            $eduRows .= '<tr>'
                . '<td>' . $e($row['class_course'] ?? '') . '</td>'
                . '<td>' . $e($row['college'] ?? '') . '</td>'
                . '<td>' . $e($row['branch'] ?? '') . '</td>'
                . '<td>' . $e($row['university_board'] ?? '') . '</td>'
                . '<td>' . $e($row['year_of_pass'] ?? '') . '</td>'
                . '<td>' . $e($row['percent_secured'] ?? '') . '</td>'
                . '</tr>';
        }
    }

    $productFields = $field('Brief description of Product and Business/Service', $app['product_description'] ?? '')
        . $field('Start-up Year', $app['startup_year'] ?? '')
        . $field('Why you want to become an entrepreneur', $app['why_entrepreneur'] ?? '')
        . $field('Legal position', $app['legal_position'] ?? '');

    $serviceRows = '';
    foreach ($allServices as $s) {
        $yes = in_array($s, $services, true);
        $serviceRows .= '<tr><td>' . $e($s) . '</td><td class="center">' . ($yes ? 'Yes' : 'No') . '</td></tr>';
    }

    $teamFields = $field('Details of your Team', $app['team_details'] ?? '')
        . $field('Resident employees — Full-time', $app['employees_fulltime'] ?? '')
        . $field('Part-time', $app['employees_parttime'] ?? '')
        . $field('Consultants', $app['employees_consultants'] ?? '')
        . $field('Number of employees in the Organization', $app['employees_org'] ?? '');

    $promoterFields = $field('Name of the Promoter', $app['promoter_name'] ?? '')
        . $field('Educational Qualification', $app['promoter_qualification'] ?? '')
        . $field('Designation', $app['promoter_designation'] ?? '')
        . $field('Years of Experience', $app['promoter_experience'] ?? '')
        . $field('Address for Communication', $app['promoter_communication_address'] ?? '')
        . $field('Permanent Address', $app['promoter_permanent_address'] ?? '')
        . $field('Phone (Res)', $app['promoter_phone_res'] ?? '')
        . $field('Phone (Office)', $app['promoter_phone_off'] ?? '')
        . $field('Mobile', $app['promoter_phone_mobile'] ?? '')
        . $field('Email', $app['promoter_email'] ?? '')
        . $field('Fax', $app['promoter_fax'] ?? '');

    $costRows = '';
    if ($costs === []) {
        $costRows = '<tr><td colspan="2" class="muted">—</td></tr>';
    } else {
        foreach ($costs as $c) {
            $costRows .= '<tr><td>' . $e($c['item_name'] ?? '') . '</td><td>' . $e($c['amount'] ?? '') . '</td></tr>';
        }
    }

    $refHtml = '';
    foreach ($references as $r) {
        $n = (int) ($r['ref_number'] ?? 0);
        $refHtml .= '<h3>Reference ' . $n . '</h3><table class="fields">'
            . $field('Name', $r['ref_name'] ?? '')
            . $field('Designation', $r['designation'] ?? '')
            . $field('Address', $r['address'] ?? '')
            . $field('Phone', $r['phone'] ?? '')
            . $field('Email', $r['email'] ?? '')
            . '</table>';
    }

    $declFields = $field('Place', $app['place'] ?? '')
        . $field('Agreed to TBI rules', !empty($app['agreed_rules']) ? 'Yes' : 'No');

    $submitted = $e((string) ($app['created_at'] ?? date('Y-m-d H:i:s')));

    return <<<HTML
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<style>
  body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #222; line-height: 1.35; }
  h1 { font-size: 14px; text-align: center; text-transform: uppercase; margin: 4px 0; }
  h2 { font-size: 12px; text-align: center; text-transform: uppercase; margin: 12px 0 8px; border-bottom: 1px solid #28245b; padding-bottom: 4px; color: #28245b; }
  h3 { font-size: 11px; margin: 10px 0 4px; color: #28245b; }
  .header { text-align: center; margin-bottom: 10px; }
  .sub { font-size: 9px; color: #444; }
  table.fields { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
  table.fields td { border: 1px solid #ccc; padding: 4px 6px; vertical-align: top; }
  table.fields td.label { width: 32%; background: #f2f5f7; font-weight: bold; }
  table.grid { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
  table.grid th, table.grid td { border: 1px solid #ccc; padding: 4px 5px; text-align: left; font-size: 10px; }
  table.grid th { background: #28245b; color: #fff; }
  .center { text-align: center; }
  .muted { color: #888; }
  .sig { margin-top: 24px; }
  .sig td { border: none; padding: 8px 0; }
  .rules { font-size: 9px; }
  .rules li { margin-bottom: 3px; }
</style>
</head>
<body>
  <div class="header">
    <div><strong>SRI CHANDRASEKHARENDRA SARASWATHI VISWA MAHA VIDYALAYA</strong></div>
    <div class="sub">Deemed to be University u/s 3 of act 1956 | Accredited with “A” Grade by NAAC</div>
    <div class="sub">Enathur, Kanchipuram, Tamil Nadu - 631 561</div>
    <h1>Application Form for Incubatees</h1>
    <div class="sub">KSRF – Technology Business Incubator · Submitted {$submitted}</div>
  </div>

  <h2>1. Applicant</h2>
  <table class="fields">{$applicantFields}</table>

  <h2>2. Educational Qualification</h2>
  <table class="grid">
    <thead>
      <tr>
        <th>Class / Course</th>
        <th>College / Institution</th>
        <th>Branch</th>
        <th>University / Board</th>
        <th>Year</th>
        <th>%</th>
      </tr>
    </thead>
    <tbody>{$eduRows}</tbody>
  </table>

  <h2>3. Product / Business</h2>
  <table class="fields">{$productFields}</table>

  <h2>4. Services expected from SCSVMV-TBI</h2>
  <table class="grid">
    <thead><tr><th>Facility</th><th>Required</th></tr></thead>
    <tbody>{$serviceRows}</tbody>
  </table>

  <h2>5. Team</h2>
  <table class="fields">{$teamFields}</table>

  <h2>6. Promoter details</h2>
  <table class="fields">{$promoterFields}</table>

  <h2>7. Estimated Project cost</h2>
  <table class="grid">
    <thead><tr><th>Item</th><th>Amount (₹)</th></tr></thead>
    <tbody>{$costRows}</tbody>
  </table>

  <h2>8. References</h2>
  {$refHtml}

  <h2>9. Declaration</h2>
  <table class="fields">{$declFields}</table>
  <table class="sig" width="100%">
    <tr>
      <td>Date: {$submitted}</td>
      <td style="text-align:right">Signature of the Incubatee<br /><br />________________________</td>
    </tr>
  </table>

  <h2>10. Rules &amp; regulations (summary)</h2>
  <ol class="rules">
    <li>Registration only if the idea is innovative, relevant and/or commercially viable.</li>
    <li>Incubatee registration fee of ₹5,000 for office space and lab/workshop use rights.</li>
    <li>Pre-incubation support may be offered with a nominal fee for proof-of-concept work.</li>
    <li>Lab and office rentals payable as per TBI guidelines, typically quarterly.</li>
    <li>Preferences depend on idea strength, progress, conduct, and social commitment.</li>
    <li>Reference letters require written request and expert review.</li>
    <li>TBI does not shoulder financial responsibility for un-announced schemes.</li>
    <li>Facilities during SCSVMV office hours unless permission is granted for extended use.</li>
    <li>Facilities and TBI brand only for intended purpose, with written intimation for brand use.</li>
    <li>Exit based on tenure (typically 2–3 years), milestones, and business sustenance.</li>
  </ol>
</body>
</html>
HTML;
}
