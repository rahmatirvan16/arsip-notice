<?php
require_once 'vendor/autoload.php';
use Dompdf\Dompdf;
use Dompdf\Options;

// Inisialisasi Dompdf
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);

// Konten HTML untuk rancangan database revisi
$html = '
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rancangan Database Revisi Aplikasi Arsip Digital Dokumen</title>
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
        .code {
            background-color: #f8f9fa;
            padding: 10px;
            border-left: 4px solid #3498db;
            font-family: monospace;
            margin: 10px 0;
        }
        .table-design {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .table-design th, .table-design td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .table-design th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .erd {
            text-align: center;
            margin: 20px 0;
            font-style: italic;
            color: #7f8c8d;
        }
        .constraint {
            background-color: #e8f4f8;
            padding: 8px;
            margin: 5px 0;
            border-radius: 3px;
        }
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>

<h1>RANCANGAN DATABASE REVISI</h1>
<h1>APLIKASI ARSIP DIGITAL DOKUMEN</h1>

<div class="section">
    <h2>1. PENDAHULUAN</h2>
    <p>Database merupakan komponen inti dari aplikasi Arsip Digital Dokumen. Rancangan database ini dirancang untuk mendukung penyimpanan, pengelolaan, dan pencarian dokumen secara efisien dan aman. Aplikasi ini fokus pada upload dan manajemen dokumen tanpa fitur notice pajak.</p>

    <h3>1.1 Tujuan Rancangan Database</h3>
    <ul>
        <li>Menyimpan data pengguna dengan sistem role-based access</li>
        <li>Mengelola dokumen dengan informasi lengkap (nama, bulan-tahun, file)</li>
        <li>Mencatat semua aktivitas pengguna untuk audit trail</li>
        <li>Menyimpan pengaturan aplikasi yang dapat dikustomisasi</li>
        <li>Mendukung backup dan restore data</li>
    </ul>

    <h3>1.2 Prinsip Desain</h3>
    <ul>
        <li><strong>Normalisasi:</strong> Database dinormalisasi hingga 3NF untuk menghindari redundansi data</li>
        <li><strong>Integrity:</strong> Menggunakan foreign key dan constraint untuk menjaga integritas data</li>
        <li><strong>Performance:</strong> Indexing pada field yang sering dicari</li>
        <li><strong>Security:</strong> Enkripsi password dan logging aktivitas</li>
        <li><strong>Scalability:</strong> Desain yang dapat dikembangkan untuk fitur tambahan</li>
    </ul>
</div>

<div class="section page-break">
    <h2>2. ANALISIS KEBUTUHAN DATA</h2>

    <h3>2.1 Entity Utama</h3>
    <ol>
        <li><strong>Dokumen:</strong> Data dokumen yang diupload dengan informasi bulan-tahun</li>
        <li><strong>Users:</strong> Data pengguna aplikasi dengan role admin/operator</li>
        <li><strong>Logs:</strong> Catatan aktivitas semua pengguna</li>
        <li><strong>Settings:</strong> Konfigurasi aplikasi</li>
    </ol>

    <h3>2.2 Relasi Antar Entity</h3>
    <div class="erd">
        [ERD Diagram]<br>
        Users (1) -- (N) Logs<br>
        Users (1) -- (N) Dokumen<br>
        Settings (1) -- (1) App_Config
    </div>

    <h3>2.3 Kebutuhan Fungsional</h3>
    <ul>
        <li>CRUD operations untuk semua entity</li>
        <li>Pencarian berdasarkan bulan-tahun, nama dokumen, user</li>
        <li>Reporting bulanan dan tahunan</li>
        <li>Soft delete untuk data dokumen</li>
        <li>Role-based access control</li>
        <li>Audit logging untuk semua aktivitas</li>
    </ul>
</div>

<div class="section page-break">
    <h2>3. STRUKTUR TABEL DATABASE</h2>

    <h3>3.1 Tabel users</h3>
    <p>Tabel untuk menyimpan data pengguna aplikasi.</p>
    <table class="table-design">
        <tr>
            <th>Field</th>
            <th>Type</th>
            <th>Length</th>
            <th>Null</th>
            <th>Default</th>
            <th>Keterangan</th>
        </tr>
        <tr>
            <td>id</td>
            <td>INT</td>
            <td></td>
            <td>NO</td>
            <td>AUTO_INCREMENT</td>
            <td>Primary Key</td>
        </tr>
        <tr>
            <td>username</td>
            <td>VARCHAR</td>
            <td>50</td>
            <td>NO</td>
            <td></td>
            <td>Username unik</td>
        </tr>
        <tr>
            <td>password</td>
            <td>VARCHAR</td>
            <td>255</td>
            <td>NO</td>
            <td></td>
            <td>Password hash</td>
        </tr>
        <tr>
            <td>role</td>
            <td>ENUM</td>
            <td>admin,operator</td>
            <td>NO</td>
            <td>operator</td>
            <td>Role pengguna</td>
        </tr>
        <tr>
            <td>status</td>
            <td>VARCHAR</td>
            <td>10</td>
            <td>YES</td>
            <td>active</td>
            <td>Status aktif/nonaktif</td>
        </tr>
    </table>
    <div class="constraint">
        <strong>Constraints:</strong> UNIQUE(username), PRIMARY KEY(id)
    </div>

    <h3>3.2 Tabel dokumen</h3>
    <p>Tabel utama untuk menyimpan data dokumen yang diupload.</p>
    <table class="table-design">
        <tr>
            <th>Field</th>
            <th>Type</th>
            <th>Length</th>
            <th>Null</th>
            <th>Default</th>
            <th>Keterangan</th>
        </tr>
        <tr>
            <td>id</td>
            <td>INT</td>
            <td></td>
            <td>NO</td>
            <td>AUTO_INCREMENT</td>
            <td>Primary Key</td>
        </tr>
        <tr>
            <td>nama_dokumen</td>
            <td>VARCHAR</td>
            <td>255</td>
            <td>NO</td>
            <td></td>
            <td>Nama dokumen</td>
        </tr>
        <tr>
            <td>bulan_tahun</td>
            <td>VARCHAR</td>
            <td>7</td>
            <td>NO</td>
            <td></td>
            <td>Bulan dan tahun (YYYY-MM)</td>
        </tr>
        <tr>
            <td>file_pdf</td>
            <td>VARCHAR</td>
            <td>255</td>
            <td>YES</td>
            <td></td>
            <td>Path file PDF dokumen</td>
        </tr>
        <tr>
            <td>status</td>
            <td>VARCHAR</td>
            <td>10</td>
            <td>YES</td>
            <td>active</td>
            <td>Status aktif/inactive</td>
        </tr>
        <tr>
            <td>created_at</td>
            <td>TIMESTAMP</td>
            <td></td>
            <td>YES</td>
            <td>CURRENT_TIMESTAMP</td>
            <td>Waktu upload</td>
        </tr>
    </table>
    <div class="constraint">
        <strong>Constraints:</strong> PRIMARY KEY(id)
    </div>
</div>

<div class="section page-break">
    <h3>3.3 Tabel logs</h3>
    <p>Tabel untuk mencatat semua aktivitas pengguna.</p>
    <table class="table-design">
        <tr>
            <th>Field</th>
            <th>Type</th>
            <th>Length</th>
            <th>Null</th>
            <th>Default</th>
            <th>Keterangan</th>
        </tr>
        <tr>
            <td>id</td>
            <td>INT</td>
            <td></td>
            <td>NO</td>
            <td>AUTO_INCREMENT</td>
            <td>Primary Key</td>
        </tr>
        <tr>
            <td>user_id</td>
            <td>INT</td>
            <td></td>
            <td>NO</td>
            <td></td>
            <td>Foreign Key ke users.id</td>
        </tr>
        <tr>
            <td>action</td>
            <td>VARCHAR</td>
            <td>50</td>
            <td>NO</td>
            <td></td>
            <td>Jenis aksi (add, edit, delete, upload, etc)</td>
        </tr>
        <tr>
            <td>dokumen_id</td>
            <td>INT</td>
            <td></td>
            <td>YES</td>
            <td></td>
            <td>ID dokumen terkait (opsional)</td>
        </tr>
        <tr>
            <td>details</td>
            <td>TEXT</td>
            <td></td>
            <td>YES</td>
            <td></td>
            <td>Detail aktivitas</td>
        </tr>
        <tr>
            <td>timestamp</td>
            <td>TIMESTAMP</td>
            <td></td>
            <td>YES</td>
            <td>CURRENT_TIMESTAMP</td>
            <td>Waktu aktivitas</td>
        </tr>
    </table>
    <div class="constraint">
        <strong>Constraints:</strong> PRIMARY KEY(id), FOREIGN KEY(user_id) REFERENCES users(id)
    </div>

    <h3>3.4 Tabel settings</h3>
    <p>Tabel untuk menyimpan pengaturan aplikasi.</p>
    <table class="table-design">
        <tr>
            <th>Field</th>
            <th>Type</th>
            <th>Length</th>
            <th>Null</th>
            <th>Default</th>
            <th>Keterangan</th>
        </tr>
        <tr>
            <td>id</td>
            <td>INT</td>
            <td></td>
            <td>NO</td>
            <td></td>
            <td>Primary Key (hanya 1 record)</td>
        </tr>
        <tr>
            <td>app_title</td>
            <td>VARCHAR</td>
            <td>255</td>
            <td>NO</td>
            <td></td>
            <td>Judul aplikasi</td>
        </tr>
        <tr>
            <td>logo_path</td>
            <td>VARCHAR</td>
            <td>255</td>
            <td>YES</td>
            <td></td>
            <td>Path file logo</td>
        </tr>
        <tr>
            <td>max_file_size</td>
            <td>INT</td>
            <td></td>
            <td>YES</td>
            <td>5242880</td>
            <td>Maksimal ukuran file (bytes)</td>
        </tr>
    </table>
    <div class="constraint">
        <strong>Constraints:</strong> PRIMARY KEY(id)
    </div>
</div>

<div class="section page-break">
    <h2>4. RELASI DAN CONSTRAINT</h2>

    <h3>4.1 Relasi Antar Tabel</h3>
    <ul>
        <li><strong>users → logs:</strong> One-to-Many (satu user dapat memiliki banyak log)</li>
        <li><strong>users → dokumen:</strong> One-to-Many (satu user dapat mengupload banyak dokumen)</li>
        <li><strong>logs → dokumen:</strong> Many-to-One (opsional, log dapat terkait dengan dokumen)</li>
    </ul>

    <h3>4.2 Foreign Key Constraints</h3>
    <div class="code">
    ALTER TABLE logs ADD CONSTRAINT fk_logs_user_id<br>
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE;
    </div>

    <h3>4.3 Business Rules</h3>
    <ul>
        <li>Username harus unik di seluruh sistem</li>
        <li>Hanya admin yang dapat mengubah status user</li>
        <li>Data dokumen yang dihapus berubah status menjadi "inactive"</li>
        <li>Semua aktivitas user dicatat dalam logs</li>
        <li>Settings hanya memiliki 1 record (id = 1)</li>
        <li>Format bulan_tahun harus YYYY-MM</li>
        <li>Hanya file PDF yang diperbolehkan diupload</li>
    </ul>
</div>

<div class="section page-break">
    <h2>5. INDEXING DAN PERFORMANCE</h2>

    <h3>5.1 Index yang Dianjurkan</h3>
    <div class="code">
    -- Index untuk pencarian cepat<br>
    CREATE INDEX idx_dokumen_bulan_tahun ON dokumen(bulan_tahun);<br>
    CREATE INDEX idx_dokumen_status ON dokumen(status);<br>
    CREATE INDEX idx_dokumen_created_at ON dokumen(created_at);<br>
    CREATE INDEX idx_logs_timestamp ON logs(timestamp);<br>
    CREATE INDEX idx_logs_user_id ON logs(user_id);<br>
    CREATE INDEX idx_logs_dokumen_id ON logs(dokumen_id);<br>
    CREATE INDEX idx_users_username ON users(username);<br>
    CREATE INDEX idx_users_role ON users(role);
    </div>

    <h3>5.2 Query Optimization</h3>
    <ul>
        <li>Gunakan EXPLAIN untuk menganalisis query kompleks</li>
        <li>Implementasikan pagination untuk query dengan banyak hasil</li>
        <li>Gunakan prepared statements untuk mencegah SQL injection</li>
        <li>Optimalkan query join dengan index yang tepat</li>
    </ul>
</div>

<div class="section page-break">
    <h2>6. KEAMANAN DATABASE</h2>

    <h3>6.1 Enkripsi Data</h3>
    <ul>
        <li>Password user di-hash menggunakan PASSWORD_DEFAULT</li>
        <li>Data sensitif dienkripsi jika diperlukan</li>
    </ul>

    <h3>6.2 Access Control</h3>
    <ul>
        <li>Role-based access (admin vs operator)</li>
        <li>Validasi input untuk mencegah SQL injection</li>
        <li>Sanitasi data sebelum disimpan ke database</li>
        <li>Validasi tipe file dan ukuran sebelum upload</li>
    </ul>

    <h3>6.3 Backup dan Recovery</h3>
    <ul>
        <li>Regular backup database</li>
        <li>Implementasi soft delete untuk data penting</li>
        <li>Log semua perubahan data untuk audit trail</li>
    </ul>
</div>

<div class="section page-break">
    <h2>7. SCRIPT PEMBUATAN DATABASE</h2>

    <h3>7.1 SQL Script Lengkap</h3>
    <div class="code">
    -- Membuat database<br>
    CREATE DATABASE IF NOT EXISTS arsip_digital_dokumen;<br>
    USE arsip_digital_dokumen;<br>
    <br>
    -- Tabel users<br>
    CREATE TABLE users (<br>
        id INT AUTO_INCREMENT PRIMARY KEY,<br>
        username VARCHAR(50) UNIQUE NOT NULL,<br>
        password VARCHAR(255) NOT NULL,<br>
        role ENUM(\'admin\', \'operator\') NOT NULL DEFAULT \'operator\',<br>
        status VARCHAR(10) DEFAULT \'active\'<br>
    );<br>
    <br>
    -- Tabel dokumen<br>
    CREATE TABLE dokumen (<br>
        id INT AUTO_INCREMENT PRIMARY KEY,<br>
        nama_dokumen VARCHAR(255) NOT NULL,<br>
        bulan_tahun VARCHAR(7) NOT NULL,<br>
        file_pdf VARCHAR(255),<br>
        status VARCHAR(10) DEFAULT \'active\',<br>
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP<br>
    );<br>
    <br>
    -- Tabel logs<br>
    CREATE TABLE logs (<br>
        id INT AUTO_INCREMENT PRIMARY KEY,<br>
        user_id INT NOT NULL,<br>
        action VARCHAR(50) NOT NULL,<br>
        dokumen_id INT,<br>
        details TEXT,<br>
        timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,<br>
        FOREIGN KEY (user_id) REFERENCES users(id)<br>
    );<br>
    <br>
    -- Tabel settings<br>
    CREATE TABLE settings (<br>
        id INT PRIMARY KEY,<br>
        app_title VARCHAR(255) NOT NULL,<br>
        logo_path VARCHAR(255),<br>
        max_file_size INT DEFAULT 5242880<br>
    );
    </div>
</div>

<div class="section">
    <h2>8. PENGEMBANGAN MASA DEPAN</h2>

    <h3>8.1 Potensi Perluasan</h3>
    <ul>
        <li>Tabel untuk kategori dokumen</li>
        <li>Tabel untuk departemen/unit kerja</li>
        <li>Tabel untuk approval workflow</li>
        <li>Archiving tabel untuk data lama</li>
        <li>Integration dengan sistem eksternal</li>
        <li>Tabel untuk metadata dokumen tambahan</li>
    </ul>

    <h3>8.2 Monitoring dan Maintenance</h3>
    <ul>
        <li>Regular check database integrity</li>
        <li>Monitoring query performance</li>
        <li>Cleanup log files secara berkala</li>
        <li>Update statistik tabel</li>
    </ul>

    <div style="text-align: center; margin-top: 50px; color: #7f8c8d;">
        <p><strong>Rancangan Database Revisi Arsip Digital Dokumen</strong></p>
        <p>Dibuat untuk: Sistem Informasi Arsip Dokumen Digital Aceh</p>
        <p>Versi: 1.1 | Tanggal: ' . date('d-m-Y') . '</p>
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
$dompdf->stream('Rancangan_Database_Revisi_Arsip_Digital_Dokumen.pdf', array('Attachment' => 0));
?>
