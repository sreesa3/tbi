<?php
declare(strict_types=1);
require dirname(__DIR__) . '/includes/app.php';
tbi_require_admin();

if (isset($_GET['logout'])) {
    tbi_start_session();
    $_SESSION = [];
    session_destroy();
    header('Location: login.php');
    exit;
}

$rows = tbi_pdo()->query(
    'SELECT id, entrepreneur_name, email, phone_mobile, organization_name, created_at
     FROM applications ORDER BY id DESC'
)->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Applications | TBI admin</title>
    <link rel="stylesheet" href="../styles/main.css" />
  </head>
  <body>
    <header class="site-header">
      <div class="container site-header__inner">
        <strong>TBI applications</strong>
        <a href="?logout=1">Sign out</a>
      </div>
    </header>
    <main class="section">
      <div class="container">
        <?php if (!$rows): ?>
          <p>No applications yet.</p>
        <?php else: ?>
          <table class="admin-table">
            <thead>
              <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Mobile</th>
                <th>Organization</th>
                <th>Submitted</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($rows as $r): ?>
                <tr>
                  <td><a href="view.php?id=<?php echo (int) $r['id']; ?>"><?php echo (int) $r['id']; ?></a></td>
                  <td><?php echo tbi_h($r['entrepreneur_name']); ?></td>
                  <td><?php echo tbi_h($r['email']); ?></td>
                  <td><?php echo tbi_h($r['phone_mobile']); ?></td>
                  <td><?php echo tbi_h($r['organization_name']); ?></td>
                  <td><?php echo tbi_h($r['created_at']); ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php endif; ?>
      </div>
    </main>
  </body>
</html>
