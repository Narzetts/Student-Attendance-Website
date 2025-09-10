<?php
require 'config.php';
if (!is_logged_student()) {
    http_response_code(403);
    exit('Akses ditolak');
}
$siswa_id = $_SESSION['siswa_id'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: student.php');
    exit;
}

$action = $_POST['action'] ?? 'hadir';
$alasan = trim($_POST['alasan'] ?? null);
$lat = !empty($_POST['lat']) ? floatval($_POST['lat']) : null;
$lng = !empty($_POST['lng']) ? floatval($_POST['lng']) : null;

// handle file
if (empty($_FILES['foto']) || $_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
    $_SESSION['flash'] = 'Foto wajib diunggah.';
    header('Location: student.php'); exit;
}

$file = $_FILES['foto'];
$maxSize = 3 * 1024 * 1024;
$allowed = ['image/jpeg','image/png','image/webp'];

if ($file['size'] > $maxSize) {
    $_SESSION['flash'] = 'Ukuran file terlalu besar (max 3MB).';
    header('Location: student.php'); exit;
}
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);
if (!in_array($mime, $allowed)) {
    $_SESSION['flash'] = 'Format foto tidak didukung.';
    header('Location: student.php'); exit;
}

$ext = '';
switch ($mime) {
    case 'image/png': $ext = '.png'; break;
    case 'image/webp': $ext = '.webp'; break;
    default: $ext = '.jpg';
}
$baseDir = __DIR__ . '/uploads';
if (!is_dir($baseDir)) mkdir($baseDir, 0755, true);
$filename = 's'.$siswa_id.'_'.time().bin2hex(random_bytes(4)).$ext;
$savePath = $baseDir . '/' . $filename;
$publicPath = 'uploads/' . $filename;

if (!move_uploaded_file($file['tmp_name'], $savePath)) {
    $_SESSION['flash'] = 'Gagal unggah foto.';
    header('Location: student.php'); exit;
}

// insert ke DB (status pending)
$stmt = $pdo->prepare("INSERT INTO attendance (siswa_id, jenis, alasan, foto_path, lat, lng) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->execute([
    $siswa_id,
    $action === 'izin' ? 'izin' : 'hadir',
    $action === 'izin' ? $alasan : null,
    $publicPath,
    $lat ?: null,
    $lng ?: null
]);

$_SESSION['flash'] = 'Absensi terkirim. Admin akan memeriksa (jika izin).';
header('Location: student.php');
exit;
