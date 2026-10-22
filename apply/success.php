<?php
declare(strict_types=1);
require dirname(__DIR__) . '/includes/app.php';
tbi_start_session();

$appId = (int) ($_SESSION['last_application_id'] ?? 0);
$pdfOk = !empty($_SESSION['last_application_pdf']);
$hasPdf = $pdfOk && $appId > 0 && is_readable(tbi_uploads_dir() . '/' . $appId . '/application.pdf');
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Application received | KSRF TBI</title>
    <link rel="icon" href="../assets/logos/scsvmv-logo.png" type="image/png" />
    <link rel="stylesheet" href="../styles/main.css" />
    <link
      href="https://fonts.googleapis.com/css2?family=Noto+Serif:wght@600;700&family=Poppins:wght@400;500;600;700&display=swap"
      rel="stylesheet"
    />
  </head>
  <body>
    <header class="site-header">
      <div class="container site-header__inner">
        <a class="brand__home" href="../index.html">
          <span class="brand__name" style="color: var(--navy)">Technology Business Incubator</span>
        </a>
      </div>
    </header>
    <main class="section">
      <div class="container">
        <header class="section__head">
          <p class="section__label">Apply</p>
          <h2>Application received</h2>
          <p class="section__intro">
            Thank you. KSRF – TBI will review your submission. Do not email PAN or Aadhaar
            unless TBI asks through official channels.
          </p>
        </header>
        <div class="cta-row">
          <?php if ($hasPdf): ?>
            <a class="btn btn--solid" href="download-pdf.php">Download filled application PDF</a>
          <?php endif; ?>
          <a class="btn btn--outline" href="../index.html">Back to home</a>
        </div>
        <?php if ($appId > 0 && !$hasPdf): ?>
          <p class="note" style="margin-top: 1.25rem">
            Your application was saved (ref #<?php echo $appId; ?>). The PDF could not be
            generated automatically — TBI can still review your details in the admin panel.
          </p>
        <?php endif; ?>
      </div>
    </main>
  </body>
</html>
