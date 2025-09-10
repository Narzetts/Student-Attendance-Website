<?php
require 'config.php';

// Jika sudah login, langsung ke dashboard siswa
if (is_logged_student()) header('Location: student.php');

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nisn = trim($_POST['nisn'] ?? '');
    if ($nisn === '') {
        $message = 'Masukkan NISN.';
    } else {
        $stmt = $pdo->prepare("SELECT id, nama, kelas, foto_path FROM siswa WHERE nisn = ?");
        $stmt->execute([$nisn]);
        $s = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($s) {
            session_regenerate_id(true);
            $_SESSION['siswa_id'] = $s['id'];
            $_SESSION['siswa_nama'] = $s['nama'];
            $_SESSION['siswa_kelas'] = $s['kelas'] ?? '-';
            $_SESSION['siswa_foto'] = $s['foto_path'] ?: 'assets/foto_siswa/default.png';
            header('Location: student.php');
            exit;
        } else {
            $message = 'NISN tidak ditemukan. Hubungi admin untuk didaftarkan.';
        }
    }
}
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Login Siswa - Absensi</title>
<link rel="stylesheet" href="assets/style.css">
<style>
body {
    display:flex;
    justify-content:center;
    align-items:center;
    min-height:100vh;
    background:var(--bg);
    margin:0;
}
.login-wrapper {
    width:100%;
    max-width:400px;
    padding:20px;
}
.login-card {
    background: var(--card);
    padding: 32px;
    border-radius: 12px;
    text-align: center;
    box-shadow: 0 6px 18px rgba(0,0,0,0.4);
    transition: transform 0.2s;
}
.login-card:hover {
    transform: translateY(-4px);
}
.login-card h1 {
    margin-bottom: 8px;
    font-size: 22px;
}
.login-card p {
    margin-bottom: 18px;
    color: var(--muted);
}
.input.full {
    width:100%;
}
.button.full {
    width:100%;
    margin-top:10px;
}
.alert.error {
    margin-bottom:10px;
    background: rgba(239,68,68,0.2);
    color: var(--danger);
    padding: 10px;
    border-radius:8px;
}
.link {
    color: var(--accent);
    text-decoration:none;
}
.link:hover {
    text-decoration:underline;
}
.footer {
    margin-top:18px;
    font-size:13px;
    color: var(--muted);
}
</style>
</head>
<body>

<div class="login-wrapper">
    <div class="login-card">
        <h1>Login Siswa</h1>
        <p>Masukkan NISN untuk mulai absen</p>

        <?php if($message): ?>
            <div class="alert error"><?=htmlspecialchars($message)?></div>
        <?php endif; ?>

        <form method="post">
            <input class="input full" type="text" name="nisn" placeholder="NISN (contoh: 1234567890)" required>
            <button class="button full">Masuk</button>
        </form>

    </div>
</div>

</body>
</html>
