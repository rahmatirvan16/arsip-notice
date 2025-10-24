<?php
require_once 'vendor/autoload.php';
use Dompdf\Dompdf;
use Dompdf\Options;

// Inisialisasi Dompdf
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);

// Konten HTML untuk panduan
$html = '
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panduan Penggunaan Aplikasi Arsip Digital Notice Pajak</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 20px;
            color: #333;
        }
        h1, h2, h3 {
            color: #2c3e50;
            border-bottom: 2px solid #3498db;
            padding-bottom: 5px;
        }
        h1 {
            text-align: center;
            color: #e74c3c;
            border-bottom: 3px solid #e74c3c;
        }
        .section {
            margin-bottom: 30px;
        }
        .step {
            margin-left: 20px;
            margin-bottom: 10px;
        }
        .code {
            background-color: #f8f9fa;
            padding: 10px;
            border-left: 4px solid #3498db;
            font-family: monospace;
            margin: 10px 0;
        }
        .warning {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 10px;
            border-radius: 5px;
            margin: 10px 0;
        }
        .info {
            background-color: #d1ecf1;
            border: 1px solid #bee5eb;
            padding: 10px;
            border-radius: 5px;
            margin: 10px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>

<h1>PANDUAN PENGGUNAAN APLIKASI</h1>
<h1>ARSIP DIGITAL NOTICE PAJAK</h1>

<div class="section">
    <h2>1. PENDAHULUAN</h2>
    <p>Aplikasi Arsip Digital Notice Pajak adalah sistem informasi yang dirancang untuk mengelola arsip notice pajak yang rusak atau batal. Aplikasi ini memungkinkan pengguna untuk menyimpan, mengelola, dan mencari data notice pajak secara digital.</p>

    <h3>1.1 Fitur Utama</h3>
    <ul>
        <li>Manajemen notice pajak (tambah, edit, hapus)</li>
        <li>Upload dan penyimpanan file PDF</li>
        <li>Pencarian notice berdasarkan tanggal</li>
        <li>Laporan rekapitulasi bulanan dan tahunan</li>
        <li>Manajemen dokumen tambahan</li>
        <li>Sistem backup dan restore database</li>
        <li>Log aktivitas pengguna</li>
        <li>Manajemen pengguna (khusus admin)</li>
    </ul>
</div>

<div class="section page-break">
    <h2>2. INSTALASI DAN SETUP</h2>

    <h3>2.1 Persyaratan Sistem</h3>
    <ul>
        <li>Web server (Apache/Nginx)</li>
        <li>PHP 7.4 atau lebih tinggi</li>
        <li>MySQL/MariaDB</li>
        <li>Composer (untuk dependensi)</li>
    </ul>

    <h3>2.2 Langkah Instalasi</h3>
    <div class="step">
        <strong>1. Download dan ekstrak file aplikasi</strong><br>
        Ekstrak file aplikasi ke dalam folder htdocs (XAMPP) atau www (WAMP).
    </div>

    <div class="step">
        <strong>2. Install dependensi</strong><br>
        Buka command prompt/terminal, navigasi ke folder aplikasi, dan jalankan:<br>
        <div class="code">composer install</div>
    </div>

    <div class="step">
        <strong>3. Setup database</strong><br>
        <ol>
            <li>Buka phpMyAdmin</li>
            <li>Buat database baru dengan nama "arsip_digital"</li>
            <li>Import file setup.php melalui browser atau jalankan:<br>
            <div class="code">php setup.php</div></li>
        </ol>
    </div>

    <div class="step">
        <strong>4. Konfigurasi database</strong><br>
        Edit file db.php dan sesuaikan pengaturan database:
        <div class="code">
        $servername = "localhost";<br>
        $username = "root";<br>
        $password = "";<br>
        $dbname = "arsip_digital";
        </div>
    </div>

    <div class="step">
        <strong>5. Akses aplikasi</strong><br>
        Buka browser dan akses: <strong>http://localhost/nama-folder-aplikasi</strong>
    </div>

    <div class="info">
        <strong>Catatan:</strong> User default setelah instalasi:<br>
        - Admin: username "admin", password "admin123"<br>
        - Operator: username "operator", password "operator123"
    </div>
</div>

<div class="section page-break">
    <h2>3. LOGIN DAN NAVIGASI</h2>

    <h3>3.1 Login ke Sistem</h3>
    <div class="step">
        <strong>1. Buka halaman login</strong><br>
        Akses URL aplikasi dan Anda akan diarahkan ke halaman login.
    </div>

    <div class="step">
        <strong>2. Masukkan kredensial</strong><br>
        Masukkan username dan password yang telah diberikan admin.
    </div>

    <div class="step">
        <strong>3. Klik tombol "Login"</strong><br>
        Setelah berhasil login, Anda akan diarahkan ke dashboard.
    </div>

    <h3>3.2 Role Pengguna</h3>
    <table>
        <tr>
            <th>Role</th>
            <th>Akses</th>
        </tr>
        <tr>
            <td>Admin</td>
            <td>Semua fitur termasuk manajemen user, backup, restore, log aktivitas</td>
        </tr>
        <tr>
            <td>Operator</td>
            <td>CRUD notice, dokumen, pencarian, laporan rekap</td>
        </tr>
    </table>

    <h3>3.3 Navigasi Menu</h3>
    <ul>
        <li><strong>Dashboard:</strong> Halaman utama dengan statistik</li>
        <li><strong>Tambah Notice:</strong> Form untuk menambah notice baru</li>
        <li><strong>Dokumen:</strong> Kelola dokumen tambahan</li>
        <li><strong>Pencarian:</strong> Cari notice berdasarkan rentang tanggal</li>
        <li><strong>Laporan Rekap:</strong> Laporan statistik bulanan/tahunan</li>
        <li><strong>Kelola User:</strong> Manajemen pengguna (admin only)</li>
        <li><strong>Log Aktivitas:</strong> Riwayat aktivitas (admin only)</li>
        <li><strong>Backup Database:</strong> Backup data (admin only)</li>
        <li><strong>Restore Data:</strong> Restore data yang dihapus (admin only)</li>
        <li><strong>Pengaturan:</strong> Konfigurasi aplikasi (admin only)</li>
    </ul>
</div>

<div class="section page-break">
    <h2>4. MANAJEMEN NOTICE PAJAK</h2>

    <h3>4.1 Menambah Notice Baru</h3>
    <div class="step">
        <strong>1. Klik menu "Tambah Notice"</strong>
    </div>

    <div class="step">
        <strong>2. Isi form dengan data:</strong>
        <ul>
            <li>Nomor Notice</li>
            <li>Tanggal Penetapan</li>
            <li>Tanggal Cetak</li>
            <li>Keterangan Rusak/Batal</li>
            <li>Upload file PDF (opsional)</li>
        </ul>
    </div>

    <div class="step">
        <strong>3. Klik "Simpan"</strong><br>
        Data akan tersimpan dan tercatat dalam log aktivitas.
    </div>

    <h3>4.2 Mengedit Notice</h3>
    <div class="step">
        <strong>1. Dari halaman "Tambah Notice" atau "Dashboard"</strong>
    </div>

    <div class="step">
        <strong>2. Klik tombol "Edit" pada notice yang ingin diubah</strong>
    </div>

    <div class="step">
        <strong>3. Ubah data yang diperlukan</strong>
    </div>

    <div class="step">
        <strong>4. Klik "Simpan Perubahan"</strong>
    </div>

    <h3>4.3 Menghapus Notice</h3>
    <div class="warning">
        <strong>Perhatian:</strong> Data yang dihapus akan berstatus "inactive" dan dapat direstore oleh admin.
    </div>

    <div class="step">
        <strong>1. Klik tombol "Hapus" pada notice yang ingin dihapus</strong>
    </div>

    <div class="step">
        <strong>2. Konfirmasi penghapusan</strong>
    </div>
</div>

<div class="section page-break">
    <h2>5. KELOLA DOKUMEN</h2>

    <h3>5.1 Menambah Dokumen</h3>
    <div class="step">
        <strong>1. Klik menu "Dokumen"</strong>
    </div>

    <div class="step">
        <strong>2. Klik "Tambah Dokumen Baru"</strong>
    </div>

    <div class="step">
        <strong>3. Isi form:</strong>
        <ul>
            <li>Nama Dokumen</li>
            <li>Bulan-Tahun (format: YYYY-MM)</li>
            <li>Upload file PDF</li>
        </ul>
    </div>

    <div class="step">
        <strong>4. Klik "Simpan"</strong>
    </div>

    <h3>5.2 Filter Dokumen</h3>
    <div class="step">
        <strong>1. Pada halaman Dokumen</strong>
    </div>

    <div class="step">
        <strong>2. Pilih bulan-tahun di filter</strong>
    </div>

    <div class="step">
        <strong>3. Klik "Filter"</strong>
    </div>
</div>

<div class="section page-break">
    <h2>6. PENCARIAN DAN LAPORAN</h2>

    <h3>6.1 Pencarian Notice</h3>
    <div class="step">
        <strong>1. Klik menu "Pencarian"</strong>
    </div>

    <div class="step">
        <strong>2. Masukkan tanggal mulai dan tanggal akhir</strong>
    </div>

    <div class="step">
        <strong>3. Klik "Generate Report"</strong>
    </div>

    <div class="step">
        <strong>4. Hasil akan ditampilkan dalam tabel</strong>
    </div>

    <h3>6.2 Laporan Rekap</h3>
    <div class="step">
        <strong>1. Klik menu "Laporan Rekap"</strong>
    </div>

    <div class="step">
        <strong>2. Pilih tahun dan bulan (opsional)</strong>
    </div>

    <div class="step">
        <strong>3. Klik "Filter"</strong>
    </div>

    <div class="info">
        Laporan rekap menampilkan:
        <ul>
            <li>Statistik notice batal dan rusak</li>
            <li>Rekapan bulanan per tahun</li>
            <li>Rekapan tahunan</li>
        </ul>
    </div>
</div>

<div class="section page-break">
    <h2>7. FITUR ADMIN</h2>

    <h3>7.1 Manajemen User</h3>
    <div class="step">
        <strong>1. Klik menu "Kelola User"</strong>
    </div>

    <div class="step">
        <strong>2. Tambah user baru:</strong>
        <ul>
            <li>Klik "Tambah User Baru"</li>
            <li>Isi username, password, role</li>
            <li>Klik "Simpan"</li>
        </ul>
    </div>

    <div class="step">
        <strong>3. Edit/Nonaktifkan user:</strong>
        <ul>
            <li>Klik tombol "Edit" atau "Nonaktifkan"</li>
            <li>Konfirmasi aksi</li>
        </ul>
    </div>

    <h3>7.2 Backup Database</h3>
    <div class="step">
        <strong>1. Klik menu "Backup Database"</strong>
    </div>

    <div class="step">
        <strong>2. Klik "Download Backup SQL"</strong>
    </div>

    <div class="info">
        File backup akan otomatis disimpan di folder "backup/" dan didownload.
    </div>

    <h3>7.3 Restore Data</h3>
    <div class="step">
        <strong>1. Klik menu "Restore Data"</strong>
    </div>

    <div class="step">
        <strong>2. Klik "Restore" pada data yang ingin dikembalikan</strong>
    </div>

    <div class="step">
        <strong>3. Konfirmasi restore</strong>
    </div>

    <h3>7.4 Log Aktivitas</h3>
    <div class="step">
        <strong>1. Klik menu "Log Aktivitas"</strong>
    </div>

    <div class="step">
        <strong>2. Gunakan fitur pencarian untuk filter log</strong>
    </div>

    <h3>7.5 Pengaturan Aplikasi</h3>
    <div class="step">
        <strong>1. Klik menu "Pengaturan"</strong>
    </div>

    <div class="step">
        <strong>2. Ubah judul aplikasi dan upload logo baru</strong>
    </div>

    <div class="step">
        <strong>3. Klik "Simpan Perubahan"</strong>
    </div>
</div>

<div class="section page-break">
    <h2>8. TROUBLESHOOTING</h2>

    <h3>8.1 Masalah Umum</h3>
    <table>
        <tr>
            <th>Masalah</th>
            <th>Solusi</th>
        </tr>
        <tr>
            <td>Tidak bisa login</td>
            <td>Periksa username/password, pastikan akun aktif</td>
        </tr>
        <tr>
            <td>File PDF tidak terupload</td>
            <td>Periksa ukuran file, format PDF, dan permission folder uploads/</td>
        </tr>
        <tr>
            <td>Database error</td>
            <td>Periksa koneksi database di db.php, pastikan MySQL running</td>
        </tr>
        <tr>
            <td>Halaman tidak loading</td>
            <td>Periksa Apache/PHP status, clear cache browser</td>
        </tr>
    </table>

    <h3>8.2 File Penting</h3>
    <ul>
        <li><strong>db.php:</strong> Konfigurasi database</li>
        <li><strong>setup.php:</strong> Setup awal database</li>
        <li><strong>uploads/:</strong> Folder penyimpanan file PDF</li>
        <li><strong>backup/:</strong> Folder penyimpanan backup</li>
        <li><strong>assets/css/style.css:</strong> File CSS aplikasi</li>
    </ul>
</div>

<div class="section">
    <h2>9. KONTAK DAN DUKUNGAN</h2>
    <p>Untuk bantuan teknis atau pertanyaan lebih lanjut, silakan hubungi administrator sistem atau developer aplikasi.</p>

    <div class="info">
        <strong>Versi Aplikasi:</strong> 1.0<br>
        <strong>Tanggal Update:</strong> ' . date('d-m-Y') . '<br>
        <strong>Dikembangkan untuk:</strong> Sistem Informasi Arsip Notice Digital Aceh
    </div>
</div>

</body>
</html>
';

// Load HTML ke Dompdf
$dompdf->loadHtml($html);

// Set ukuran kertas dan orientasi
$dompdf->setPaper('A4', 'portrait');

// Render PDF
$dompdf->render();

// Output PDF ke browser
$dompdf->stream('Panduan_Aplikasi_Arsip_Digital_Notice_Pajak.pdf', array('Attachment' => 0));
?>
