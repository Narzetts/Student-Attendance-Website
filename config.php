<?php
// config.php
session_start();

$DB_HOST = 'localhost';
$DB_NAME = 'absensi_sc';
$DB_USER = 'root';
$DB_PASS = ''; // sesuaikan

try {
    $pdo = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4", $DB_USER, $DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (Exception $e) {
    die('DB connection error: ' . $e->getMessage());
}

function is_logged_student() {
    return !empty($_SESSION['siswa_id']);
}
function is_logged_admin() {
    return !empty($_SESSION['admin_id']);
}
