-- Buat database: absensi_db
CREATE DATABASE IF NOT EXISTS absensi_db CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE absensi_db;

-- Tabel siswa (data NISN dimasukkan admin terlebih dahulu)
CREATE TABLE IF NOT EXISTS siswa (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nisn VARCHAR(20) NOT NULL UNIQUE,
  nama VARCHAR(100) NOT NULL
);

-- Tabel admin
CREATE TABLE IF NOT EXISTS admins (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL
);

-- Tabel attendance
CREATE TABLE IF NOT EXISTS attendance (
  id INT AUTO_INCREMENT PRIMARY KEY,
  siswa_id INT NOT NULL,
  jenis ENUM('hadir','izin') NOT NULL DEFAULT 'hadir',
  alasan TEXT NULL,
  foto_path VARCHAR(255) NULL,
  lat DECIMAL(10,7) NULL,
  lng DECIMAL(10,7) NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  status ENUM('pending','approved','rejected') DEFAULT 'pending',
  FOREIGN KEY (siswa_id) REFERENCES siswa(id) ON DELETE CASCADE
);

-- Insert admin contoh (password: admin123) -> ganti setelah import
INSERT INTO admins (username, password_hash) VALUES ('admin', '$2y$10$u/4QyCn1p7YQ9I9f4Zzb2u8qY9k0B3O7c8y5JzQG6VvGx1DdZ3fG'); 
-- (hash untuk 'admin123' menggunakan password_hash)
