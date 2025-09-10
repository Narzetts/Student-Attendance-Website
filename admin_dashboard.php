<?php
require 'config.php';
if (!is_logged_admin()) header('Location: admin_login.php');

$action = $_GET['action'] ?? null;
if ($action === 'logout') { 
    session_destroy(); 
    header('Location: admin_login.php'); 
    exit; 
}

$msg = null;

// UPDATE STATUS ABSEN
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['update_status'])) {
    $id = intval($_POST['id']);
    $status = in_array($_POST['status'], ['approved','rejected','pending']) ? $_POST['status'] : 'pending';
    $pdo->prepare("UPDATE attendance SET status=? WHERE id=?")->execute([$status,$id]);
    $msg = "<div class='alert success'>Status berhasil diperbarui</div>";
}

// TAMBAH SISWA
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['tambah_siswa'])) {
    $nisn = trim($_POST['nisn']);
    $nama = trim($_POST['nama']);
    $kelas = trim($_POST['kelas']);
    
    $foto_path = 'assets/foto_siswa/default.png';
    if (!empty($_FILES['foto']['name'])) {
        $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $target = "assets/foto_siswa/".uniqid().".$ext";
        if (move_uploaded_file($_FILES['foto']['tmp_name'], $target)) $foto_path = $target;
    }

    $stmt = $pdo->prepare("INSERT INTO siswa (nisn,nama,kelas,foto_path) VALUES (?,?,?,?)");
    try { 
        $stmt->execute([$nisn,$nama,$kelas,$foto_path]); 
        $msg="<div class='alert success'>Siswa berhasil ditambahkan</div>"; 
    }
    catch(Exception $e){ 
        $msg="<div class='alert error'>Gagal menambah: NISN sudah terdaftar</div>"; 
    }
}

// EDIT SISWA
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['edit_siswa'])) {
    $id = intval($_POST['id']); 
    $nama=trim($_POST['nama']); 
    $nisn=trim($_POST['nisn']); 
    $kelas=trim($_POST['kelas']);
    if (!empty($_FILES['foto']['name'])) {
        $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $target = "assets/foto_siswa/".uniqid().".$ext";
        if (move_uploaded_file($_FILES['foto']['tmp_name'], $target)) {
            $pdo->prepare("UPDATE siswa SET nama=?,nisn=?,kelas=?,foto_path=? WHERE id=?")
                ->execute([$nama,$nisn,$kelas,$target,$id]);
        }
    } else {
        $pdo->prepare("UPDATE siswa SET nama=?,nisn=?,kelas=? WHERE id=?")->execute([$nama,$nisn,$kelas,$id]);
    }
    header("Location: admin_dashboard.php?menu=siswa"); exit;
}

// HAPUS SISWA
if ($action==='hapus_siswa' && isset($_GET['id'])) {
    $id=intval($_GET['id']);
    $pdo->prepare("DELETE FROM siswa WHERE id=?")->execute([$id]);
    header("Location: admin_dashboard.php?menu=siswa"); exit;
}

// DATA ABSENSI
$rows = $pdo->query("SELECT a.*, s.nama, s.nisn, s.kelas, s.foto_path FROM attendance a JOIN siswa s ON a.siswa_id=s.id ORDER BY a.created_at DESC LIMIT 200")->fetchAll();

// STATISTIK
$stat = $pdo->query("
SELECT s.id, s.nama, s.nisn, s.kelas,
SUM(CASE WHEN a.jenis='hadir' THEN 1 ELSE 0 END) as total_hadir,
SUM(CASE WHEN a.jenis='izin' THEN 1 ELSE 0 END) as total_izin
FROM siswa s LEFT JOIN attendance a ON a.siswa_id=s.id
GROUP BY s.id ORDER BY s.nama ASC")->fetchAll();

// DAFTAR SISWA
$allSiswa = $pdo->query("SELECT * FROM siswa ORDER BY nama ASC")->fetchAll();
$menu = $_GET['menu'] ?? 'statistik';
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Admin Dashboard</title>
<link rel="stylesheet" href="assets/style.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
:root {
  --bg:#0f1724; --sidebar:#0b1220; --card:#141c2e; --accent:#6366f1;
  --success:#22c55e; --warning:#f59e0b; --danger:#ef4444; --text:#f3f4f6; --muted:#94a3b8;
}
* {margin:0;padding:0;box-sizing:border-box;font-family:"Inter",sans-serif;}
body {display:flex; min-height:100vh; background:var(--bg); color:var(--text);}
.sidebar {width:220px; background:var(--sidebar); padding:20px; display:flex; flex-direction:column; gap:12px;}
.sidebar h2 {font-size:18px; margin-bottom:16px;}
.sidebar a {color:var(--muted); text-decoration:none; padding:10px 12px; border-radius:8px; transition:0.2s; font-size:14px;}
.sidebar a:hover, .sidebar a.active {background:var(--accent); color:white;}
.sidebar a.danger {background:var(--danger); color:white;}
.content {flex:1; padding:24px; overflow-y:auto;}
h1,h2 {margin-bottom:12px;}
.card {background:var(--card); border-radius:12px; padding:20px; margin-bottom:20px; box-shadow:0 4px 12px rgba(0,0,0,0.4);}
table {width:100%; border-collapse:collapse; font-size:14px;}
table th, table td {padding:10px; text-align:left;}
table th {color:var(--muted); border-bottom:1px solid rgba(255,255,255,0.1);}
table tr:nth-child(even){background:rgba(255,255,255,0.05);}
.input, select, textarea {width:100%; padding:8px; border-radius:8px; border:none; background:rgba(255,255,255,0.05); color:var(--text); margin-bottom:8px;}
.button {background:var(--accent); border:none; padding:8px 14px; border-radius:8px; color:white; cursor:pointer; font-weight:600; transition:0.2s;}
.button:hover {opacity:0.9;}
.button.danger {background:var(--danger);}
.badge {padding:4px 8px; border-radius:6px; font-size:12px; font-weight:600;}
.badge.success{background:var(--success);}
.badge.warning{background:var(--warning);}
.badge.danger{background:var(--danger);}
.alert {padding:12px; border-radius:8px; margin-bottom:12px;}
.alert.success{background:rgba(34,197,94,0.2); color:var(--success);}
.alert.error{background:rgba(239,68,68,0.2); color:var(--danger);}
img.siswa {width:40px;height:40px;border-radius:50%;object-fit:cover;}
.absen-item img {width:50px;height:50px;border-radius:50%;object-fit:cover; margin-right:10px;}
.absen-form select {max-width:120px;}
.small-muted {font-size:12px; color:var(--muted);}
@media(max-width:600px){.sidebar{width:100%;flex-direction:row;overflow-x:auto;}.content{padding:12px;}}
</style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
<h2>Admin Panel</h2>
<a href="?menu=statistik" class="<?= $menu=='statistik'?'active':'' ?>">📊 Statistik</a>
<a href="?menu=tambah" class="<?= $menu=='tambah'?'active':'' ?>">➕ Tambah Siswa</a>
<a href="?menu=siswa" class="<?= $menu=='siswa'?'active':'' ?>">👥 Daftar Siswa</a>
<a href="?menu=absensi" class="<?= $menu=='absensi'?'active':'' ?>">📝 Data Absensi</a>
<a href="export_excel.php">⬇️ Export Excel</a>
<a href="admin_dashboard.php?action=logout">🚪 Logout</a>
</div>

<!-- Content -->
<div class="content">
<h1>Selamat Datang, <?=htmlspecialchars($_SESSION['admin_user'])?></h1>
<?= $msg ?? '' ?>

<!-- STATISTIK -->
<?php if($menu=='statistik'): ?>
<div class="card">
<h2>Statistik Absensi</h2>
<table>
<tr><th>Nama</th><th>NISN</th><th>Kelas</th><th>Hadir</th><th>Izin</th><th>% Kehadiran</th></tr>
<?php foreach($stat as $s):
$total=$s['total_hadir']+$s['total_izin']; 
$persen=$total>0?round($s['total_hadir']/$total*100,1):0; ?>
<tr>
<td><?=htmlspecialchars($s['nama'])?></td>
<td><?=htmlspecialchars($s['nisn'])?></td>
<td><?=htmlspecialchars($s['kelas'])?></td>
<td><?=$s['total_hadir']?></td>
<td><?=$s['total_izin']?></td>
<td><?=$persen?>%</td>
</tr>
<?php endforeach; ?>
</table>
</div>
<div class="card chart-container">
<canvas id="chartAbsensi"></canvas>
</div>
<script>
const ctx = document.getElementById('chartAbsensi');
new Chart(ctx, {
type:'bar',
data:{
labels: <?= json_encode(array_column($stat,'nama')) ?>,
datasets:[
{label:'Hadir', data:<?=json_encode(array_column($stat,'total_hadir'))?>, backgroundColor:'rgba(79,70,229,0.8)'},
{label:'Izin', data:<?=json_encode(array_column($stat,'total_izin'))?>, backgroundColor:'rgba(239,68,68,0.8)'}
]
}
});
</script>
<?php endif; ?>

<!-- TAMBAH SISWA -->
<?php if($menu=='tambah'): ?>
<div class="card">
<h2>Tambah Siswa</h2>
<form method="post" enctype="multipart/form-data">
<input name="nisn" class="input" placeholder="NISN" required>
<input name="nama" class="input" placeholder="Nama Lengkap" required>
<input name="kelas" class="input" placeholder="Kelas" required>
<label>Foto Siswa (opsional)</label>
<input type="file" name="foto" accept="image/*">
<button class="button" name="tambah_siswa">Tambah</button>
</form>
</div>
<?php endif; ?>

<!-- DAFTAR SISWA -->
<?php if($menu=='siswa'): ?>
<div class="card">
<h2>Daftar Siswa</h2>
<table>
<tr><th>Foto</th><th>Nama</th><th>NISN</th><th>Kelas</th><th>Aksi</th></tr>
<?php foreach($allSiswa as $s): ?>
<tr>
<td><img src="<?=htmlspecialchars($s['foto_path']?:'assets/foto_siswa/default.png')?>" class="siswa"></td>
<td><?=htmlspecialchars($s['nama'])?></td>
<td><?=htmlspecialchars($s['nisn'])?></td>
<td><?=htmlspecialchars($s['kelas'])?></td>
<td style="display:flex; gap:6px;">
<form method="post" enctype="multipart/form-data" style="display:flex; gap:6px;">
<input type="hidden" name="id" value="<?=$s['id']?>">
<input type="text" name="nama" value="<?=htmlspecialchars($s['nama'])?>" class="input" style="max-width:120px;">
<input type="text" name="nisn" value="<?=htmlspecialchars($s['nisn'])?>" class="input" style="max-width:100px;">
<input type="text" name="kelas" value="<?=htmlspecialchars($s['kelas'])?>" class="input" style="max-width:80px;">
<input type="file" name="foto" accept="image/*" style="max-width:140px;">
<button class="button" name="edit_siswa">Update</button>
</form>
<a href="?action=hapus_siswa&id=<?=$s['id']?>" class="button danger" onclick="return confirm('Hapus siswa ini?')">Hapus</a>
</td>
</tr>
<?php endforeach; ?>
</table>
</div>
<?php endif; ?>

<!-- DATA ABSENSI -->
<?php if($menu=='absensi'): ?>
<div class="card">
<h2>Data Absensi</h2>
<?php if(!$rows): ?><p class="small-muted">Belum ada data absensi.</p><?php endif; ?>
<?php foreach($rows as $r): ?>
<div class="absen-item" style="display:flex; flex-direction:column; gap:8px; border-bottom:1px solid rgba(255,255,255,0.1); padding:12px 0;">
  <div style="display:flex; align-items:center; justify-content:space-between;">
    <div style="display:flex; align-items:center; gap:12px;">
      <img src="<?=htmlspecialchars($r['foto_path']?:'assets/foto_siswa/default.png')?>" class="siswa">
      <div>
        <strong><?=htmlspecialchars($r['nama'])?></strong><br>
        <span class="small-muted"><?=$r['nisn']?> • <?=$r['kelas']?></span>
      </div>
    </div>
    <div style="display:flex; gap:6px;">
      <span class="badge <?=$r['jenis']=='hadir'?'success':'warning'?>"><?=htmlspecialchars($r['jenis'])?></span>
      <span class="badge <?=($r['status']=='approved'?'success':($r['status']=='rejected'?'danger':'pending'))?>"><?=htmlspecialchars($r['status'])?></span>
    </div>
  </div>

  <?php if($r['alasan']): ?>
  <div style="padding-left:52px; font-size:13px; color:var(--muted);">
    Alasan: <em><?=nl2br(htmlspecialchars($r['alasan']))?></em>
  </div>
  <?php endif; ?>

  <div class="absen-links" style="padding-left:52px; display:flex; gap:12px; font-size:13px;">
    <?php if($r['foto_path']): ?><a href="<?=htmlspecialchars($r['foto_path'])?>" target="_blank">📷 Lihat Foto</a><?php endif; ?>
    <?php if($r['lat'] && $r['lng']): ?><a href="https://www.google.com/maps/search/?api=1&query=<?=$r['lat']?>,<?=$r['lng']?>" target="_blank">📍 Lihat Lokasi</a><?php endif; ?>
  </div>

  <form method="post" class="absen-form" style="padding-left:52px; display:flex; gap:8px; align-items:center; margin-top:6px;">
    <input type="hidden" name="id" value="<?=$r['id']?>">
    <select name="status" class="input" style="max-width:120px;">
      <option value="pending" <?=$r['status']=='pending'?'selected':''?>>Pending</option>
      <option value="approved" <?=$r['status']=='approved'?'selected':''?>>Approved</option>
      <option value="rejected" <?=$r['status']=='rejected'?'selected':''?>>Rejected</option>
    </select>
    <button name="update_status" class="button" style="padding:6px 12px;">Simpan</button>
  </form>
</div>
<?php endforeach; ?>
</div>
<?php endif; ?>

</div>
</body>
</html>
