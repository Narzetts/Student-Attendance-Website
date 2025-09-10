![alt text](https://github.com/Narzetts/Student-Attendance-Website/blob/main/assets/Screenshot%202025-09-10%20190634.png?raw=true)
# 📘 Sistem Absensi Sekolah

Proyek ini adalah aplikasi **Sistem Absensi Siswa berbasis Web** menggunakan **PHP, MySQL, dan CSS modern (dark mode)**.  
Didesain untuk mempermudah guru/admin dalam mengelola data siswa, absensi, dan statistik kehadiran.

This project is a **Web-based Student Attendance System** built with **PHP, MySQL, and modern CSS (dark mode)**.  
It is designed to help teachers/admins manage student data, attendance, and attendance statistics easily.

---

## 🚀 Fitur Utama / Main Features
- 👥 **Manajemen Siswa / Student Management**  
  Tambah, edit, hapus data siswa.  
  Add, edit, delete student data.

- 📝 **Absensi Online / Online Attendance**  
  Siswa dapat melakukan absensi dengan status hadir/izin/sakit.  
  Students can submit attendance with present/permission/sick status.

- 📊 **Dashboard Statistik / Statistics Dashboard**  
  Menampilkan grafik kehadiran siswa.  
  Display attendance statistics with charts.

- 🌑 **Tampilan Full Dark Mode / Full Dark Mode UI**  
  Modern, responsif, dan ramah mata.  
  Modern, responsive, and eye-friendly.

- ⬇️ **Export Excel**  
  Admin dapat mengunduh data absensi.  
  Admins can download attendance data.

---

## 🛠️ Teknologi / Technologies
- **PHP 8+**
- **MySQL / MariaDB**
- **HTML5, CSS3 (Dark Mode UI)**
- **Chart.js** untuk grafik  
  for charts
- **XAMPP / Laragon** (local server)

---

## 📂 Struktur Folder / Folder Structure
```
TST/
│── admin_dashboard.php   # Halaman dashboard admin / Admin dashboard page
│── admin_login.php       # Halaman login admin / Admin login page
│── logout.php            # Logout dashboard admin / Logout admin page
│── index.php             # Halaman login siswa / Student login page
│── student.php           # Halaman siswa / Student page
│── config.php            # Konfigurasi database / Database config
│── submit_absence.php    # Proses kirim absensi / Attendance submission process
│── db.sql                # Database
│── uploads/
│── assets/
│   ├── style.css         # CSS utama / Main stylesheet
│   ├── app.js            # JS utama / Main javascript
│   ├── foto_siswa/       # Folder foto siswa / Student photos
│── export_excel.php      # Export data ke Excel / Export data to Excel
```

---

## ⚙️ Instalasi / Installation

### Bahasa Indonesia
1. Clone repo ini:  
   ```bash
   git clone https://github.com/username/Student-Attendance-Website.git
   ```
2. Import database dari file `absensi_sc.sql` ke MySQL.  
3. Ubah konfigurasi `config.php` sesuai dengan username, password, dan nama database.  
4. Jalankan dengan XAMPP/Laragon, akses via:  
   ```
   http://localhost/TST/admin_dashboard.php
   ```

### English
1. Clone this repo:  
   ```bash
   git clone https://github.com/username/sistem-absensi.git
   ```
2. Import the database from `absensi_sc.sql` into MySQL.  
3. Edit `config.php` with your MySQL username, password, and database name.  
4. Run via XAMPP/Laragon and access:  
   ```
   http://localhost/TST/admin_dashboard.php
   ```

---

## 🔑 Akun Default / Default Account
- **Admin Login**  
  Username: `admin`  
  Password: `admin123`

---

## 📸 Screenshots
![alt text](https://github.com/Narzetts/Student-Attendance-Website/blob/main/assets/Screenshot%202025-09-10%20190424.png?raw=true)

---
