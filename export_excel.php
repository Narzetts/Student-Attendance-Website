<?php
require 'config.php';
if (!is_logged_admin()) die("Unauthorized");

// ambil data
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=absensi.xls");

$q = $pdo->query("SELECT a.*, s.nama, s.nisn FROM attendance a JOIN siswa s ON a.siswa_id = s.id ORDER BY a.created_at DESC");
echo "Nama\tNISN\tJenis\tAlasan\tStatus\tTanggal\n";
while($r=$q->fetch(PDO::FETCH_ASSOC)){
    echo "{$r['nama']}\t{$r['nisn']}\t{$r['jenis']}\t{$r['alasan']}\t{$r['status']}\t{$r['created_at']}\n";
}
