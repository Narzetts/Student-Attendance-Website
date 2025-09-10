<?php
require 'config.php';
if (!is_logged_student()) header('Location: index.php');

$siswa_id = $_SESSION['siswa_id'];
$nama = $_SESSION['siswa_nama'] ?? 'Siswa';
$kelas = $_SESSION['siswa_kelas'] ?? '-';
$foto = $_SESSION['siswa_foto'] ?? 'assets/foto_siswa/default.png';

// Ambil 20 data absensi terbaru siswa
$stmt = $pdo->prepare("SELECT * FROM attendance WHERE siswa_id=? ORDER BY created_at DESC LIMIT 20");
$stmt->execute([$siswa_id]);
$records = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Dashboard - <?=htmlspecialchars($nama)?></title>
<link rel="stylesheet" href="assets/style.css">
<style>
:root {
  --bg:#0f1724;
  --card:#141c2e;
  --accent:#6366f1;
  --success:#22c55e;
  --warning:#f59e0b;
  --danger:#ef4444;
  --text:#f3f4f6;
  --muted:#94a3b8;
}

body {
  margin:0;
  font-family:Arial,sans-serif;
  background:var(--bg);
  color:var(--text);
  display:flex;
  justify-content:center;
  min-height:100vh;
  padding:20px;
}

.wrapper {
  width:100%;
  max-width:900px;
}

.card {
  background:var(--card);
  border-radius:12px;
  padding:20px;
  margin-bottom:20px;
  box-shadow:0 4px 12px rgba(0,0,0,0.4);
}

.topbar {
  display:flex;
  justify-content:space-between;
  align-items:center;
  margin-bottom:20px;
}

.user-info {
  display:flex;
  align-items:center;
  gap:12px;
}

.user-info img {
  width:70px;
  height:70px;
  border-radius:50%;
  object-fit:cover;
  border:2px solid var(--accent);
}

.logout-btn {
  background:var(--danger);
  color:white;
  padding:8px 16px;
  border-radius:8px;
  text-decoration:none;
  font-weight:600;
}

h1,h2 {
  margin:0 0 10px 0;
}

.input, .textarea {
  width:100%;
  padding:10px;
  margin-bottom:10px;
  border-radius:8px;
  border:none;
  background: rgba(255,255,255,0.05);
  color: var(--text);
}

.button {
  padding:10px 18px;
  border:none;
  border-radius:8px;
  cursor:pointer;
  background:var(--accent);
  color:white;
  font-weight:600;
  transition:0.2s;
}

.button:hover { opacity:0.9; }

.form-section {
  margin-bottom:20px;
}

.form-row {
  display:flex;
  gap:10px;
  flex-wrap:wrap;
}

.riwayat-item {
  background: rgba(99,102,241,0.1);
  padding:14px;
  border-radius:10px;
  margin-bottom:12px;
}

.riwayat-head {
  display:flex;
  align-items:center;
  gap:12px;
  margin-bottom:8px;
}

.badge {
  padding:4px 10px;
  border-radius:6px;
  font-size:12px;
  font-weight:600;
}

.badge.hadir { background:var(--success); color:white; }
.badge.izin { background:var(--warning); color:white; }

.small-muted { font-size:12px; color:var(--muted); }

@media(max-width:600px){
  .topbar {flex-direction:column; align-items:flex-start;}
  .form-row {flex-direction:column;}
}
</style>
</head>
<body>
<div class="wrapper">

  <!-- Topbar -->
  <div class="card topbar">
    <div class="user-info">
      <img src="<?=htmlspecialchars($foto)?>" alt="Foto Siswa">
      <div>
        <h1><?=htmlspecialchars($nama)?></h1>
        <p>Kelas: <?=htmlspecialchars($kelas)?></p>
      </div>
    </div>
    <a href="logout.php" class="logout-btn">Logout</a>
  </div>

  <!-- Form Absen Hadir -->
  <div class="card form-section">
    <h2>Absen Hadir</h2>
    <form action="submit_absence.php" method="post" enctype="multipart/form-data">
      <input type="hidden" name="action" value="hadir">
      <input type="hidden" name="lat" id="lat">
      <input type="hidden" name="lng" id="lng">
      <input class="input" type="file" name="foto" accept="image/*" required>
      <div class="form-row">
        <button type="button" class="button" data-get-loc>📍 Ambil Lokasi</button>
        <button type="submit" class="button">✅ Kirim</button>
      </div>
    </form>
  </div>

  <!-- Form Izin -->
  <div class="card form-section">
    <h2>Izin Tidak Masuk</h2>
    <form action="submit_absence.php" method="post" enctype="multipart/form-data">
      <input type="hidden" name="action" value="izin">
      <input type="hidden" name="lat" id="lat2">
      <input type="hidden" name="lng" id="lng2">
      <textarea class="textarea" name="alasan" placeholder="Alasan izin" required></textarea>
      <input class="input" type="file" name="foto" accept="image/*" required>
      <div class="form-row">
        <button type="button" class="button" data-get-loc>📍 Ambil Lokasi</button>
        <button type="submit" class="button">📤 Kirim</button>
      </div>
    </form>
  </div>

  <!-- Riwayat Absensi -->
  <div class="card">
    <h2>Riwayat Terbaru</h2>
    <?php if(!$records): ?>
      <p class="small-muted">Belum ada data absen.</p>
    <?php endif; ?>
    <?php foreach($records as $r): ?>
    <div class="riwayat-item">
      <div class="riwayat-head">
        <img src="<?=htmlspecialchars($r['foto_path']?:'assets/foto_siswa/default.png')?>" alt="Foto Absen" width="50" height="50" style="border-radius:50%;object-fit:cover;">
        <strong><?=htmlspecialchars(ucfirst($r['jenis']))?></strong>
        <span class="badge <?= $r['jenis']=='hadir'?'hadir':'izin' ?>">
          <?=htmlspecialchars($r['jenis'])?>
        </span>
      </div>
      <div class="small-muted">
        <?=htmlspecialchars(date('d M Y H:i', strtotime($r['created_at'])))?> • <?=htmlspecialchars($r['status'])?>
      </div>
      <?php if($r['alasan']): ?>
        <div>Alasan: <em><?=nl2br(htmlspecialchars($r['alasan']))?></em></div>
      <?php endif; ?>
      <?php if($r['foto_path']): ?>
        <a href="<?=htmlspecialchars($r['foto_path'])?>" target="_blank">📷 Lihat Foto</a><br>
      <?php endif; ?>
      <?php if($r['lat'] && $r['lng']): ?>
        <span class="small-muted">📍 Lokasi: <?=htmlspecialchars($r['lat'])?>, <?=htmlspecialchars($r['lng'])?></span>
      <?php endif; ?>
    </div>
    <?php endforeach; ?>
  </div>

</div>

<script src="assets/app.js"></script>
</body>
</html>
