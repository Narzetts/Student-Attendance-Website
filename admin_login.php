<?php
require 'config.php';
if (is_logged_admin()) header('Location: admin_dashboard.php');

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = $_POST['username'] ?? '';
    $pass = $_POST['password'] ?? '';
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->execute([$user]);
    $a = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($a && password_verify($pass, $a['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['admin_id'] = $a['id'];
        $_SESSION['admin_user'] = $a['username'];
        header('Location: admin_dashboard.php');
        exit;
    } else {
        $msg = 'Username atau password salah.';
    }
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login Admin</title>
  <link rel="stylesheet" href="assets/style.css">
  <style>
    /* Override untuk halaman login */
    body {
      display: flex;
      justify-content: center;
      align-items: center;
      background: var(--bg);
    }
    .container {
      width: 100%;
      max-width: 400px;
      padding: 20px;
    }
    .card {
      background: var(--card);
      border-radius: 12px;
      padding: 24px;
      box-shadow: 0 6px 18px rgba(0,0,0,0.3);
      display: flex;
      flex-direction: column;
      gap: 16px;
      animation: fadeIn 0.5s ease;
    }
    .card .header .h1 {
      font-size: 24px;
      font-weight: 700;
      margin-bottom: 4px;
    }
    .card .header .small {
      font-size: 14px;
      color: var(--muted);
    }
    .card .header .small-muted a {
      font-size: 13px;
      color: var(--accent);
      text-decoration: none;
    }
    .card .header .small-muted a:hover {
      text-decoration: underline;
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-10px);}
      to { opacity: 1; transform: translateY(0);}
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="card">
      <div class="header">
        <div>
          <div class="h1">Admin Login</div>
          <div class="small">Masuk untuk mengelola absensi</div>
        </div>
        <div class="small-muted"><a href="index.php">Kembali ke login siswa</a></div>
      </div>

      <?php if($msg): ?>
        <div class="alert error"><?=htmlspecialchars($msg)?></div>
      <?php endif; ?>

      <form method="post">
        <input class="input" name="username" placeholder="Username" required>
        <input class="input" name="password" type="password" placeholder="Password" required>
        <button class="button" type="submit">Masuk</button>
      </form>
    </div>
  </div>
</body>
</html>
