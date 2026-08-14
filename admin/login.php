<?php
declare(strict_types=1);
require dirname(__DIR__) . '/includes/app.php';
tbi_start_session();

$error = '';
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $pass = (string) ($_POST['password'] ?? '');
    $cfg = tbi_config();
    if (hash_equals((string) $cfg['admin_password'], $pass)) {
        $_SESSION['admin_ok'] = true;
        header('Location: index.php');
        exit;
    }
    $error = 'Incorrect password.';
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>TBI admin login</title>
    <link rel="stylesheet" href="../styles/main.css" />
  </head>
  <body>
    <main class="section">
      <div class="container form-wrap">
        <h2>TBI applications</h2>
        <?php if ($error !== ''): ?>
          <p class="form-alert" role="alert"><?php echo tbi_h($error); ?></p>
        <?php endif; ?>
        <form class="app-form" method="post">
          <label>Password
            <input type="password" name="password" required />
          </label>
          <button class="btn btn--solid" type="submit">Sign in</button>
        </form>
      </div>
    </main>
  </body>
</html>
