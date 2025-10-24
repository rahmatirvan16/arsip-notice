<?php
require_once 'vendor/autoload.php';
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Style\Font;
use PhpOffice\PhpWord\Style\Paragraph;
use PhpOffice\PhpWord\Style\Table;
use PhpOffice\PhpWord\Style\Cell;

// Inisialisasi PhpWord
$phpWord = new PhpWord();

// Set default font
$phpWord->setDefaultFontName('Arial');
$phpWord->setDefaultFontSize(11);

// Buat section baru
$section = $phpWord->addSection();

// Judul utama
$section->addText(
    'RANCANGAN DATABASE REVISI',
    ['name' => 'Arial', 'size' => 16, 'bold' => true],
    ['alignment' => 'center']
);
$section->addText(
    'APLIKASI ARSIP DIGITAL DOKUMEN',
    ['name' => 'Arial', 'size' => 16, 'bold' => true],
    ['alignment' => 'center']
);
$section->addTextBreak(2);

// 1. PENDAHULUAN
$section->addText('1. PENDAHULUAN', ['name' => 'Arial', 'size' => 14, 'bold' => true]);
$section->addTextBreak(1);

$section->addText('1.1 Tujuan Rancangan Database', ['name' => 'Arial', 'size' => 12, 'bold' => true]);
$section->addText('Database merupakan komponen inti dari aplikasi Arsip Digital Dokumen. Rancangan database ini dirancang untuk mendukung penyimpanan, pengelolaan, dan pencarian dokumen secara efisien dan aman. Aplikasi ini fokus pada upload dan manajemen dokumen tanpa fitur notice pajak.');
$section->addTextBreak(1);

$section->addText('1.2 Prinsip Desain', ['name' => 'Arial', 'size' => 12, 'bold' => true]);
$prinsip = [
    '• Normalisasi: Database dinormalisasi hingga 3NF untuk menghindari redundansi data',
    '• Integrity: Menggunakan foreign key dan constraint untuk menjaga integritas data',
    '• Performance: Indexing pada field yang sering dicari',
    '• Security: Enkripsi password dan logging aktivitas',
    '• Scalability: Desain yang dapat dikembangkan untuk fitur tambahan'
];
foreach ($prinsip as $item) {
    $section->addText($item);
}
$section->addTextBreak(2);

// 2. ANALISIS KEBUTUHAN DATA
$section->addText('2. ANALISIS KEBUTUHAN DATA', ['name' => 'Arial', 'size' => 14, 'bold' => true]);
$section->addTextBreak(1);

$section->addText('2.1 Entity Utama', ['name' => 'Arial', 'size' => 12, 'bold' => true]);
$entity = [
    '• Dokumen: Data dokumen yang diupload dengan informasi bulan-tahun',
    '• Users: Data pengguna aplikasi dengan role admin/operator',
    '• Logs: Catatan aktivitas semua pengguna',
    '• Settings: Konfigurasi aplikasi'
];
foreach ($entity as $item) {
    $section->addText($item);
}
$section->addTextBreak(1);

$section->addText('2.2 Kebutuhan Fungsional', ['name' => 'Arial', 'size' => 12, 'bold' => true]);
$fungsional = [
    '• CRUD operations untuk semua entity',
    '• Pencarian berdasarkan bulan-tahun, nama dokumen, user',
    '• Reporting bulanan dan tahunan',
    '• Soft delete untuk data dokumen',
    '• Role-based access control',
    '• Audit logging untuk semua aktivitas'
];
foreach ($fungsional as $item) {
    $section->addText($item);
}
$section->addTextBreak(2);

// 3. STRUKTUR TABEL DATABASE
$section->addText('3. STRUKTUR TABEL DATABASE', ['name' => 'Arial', 'size' => 14, 'bold' => true]);
$section->addTextBreak(1);

// Tabel users
$section->addText('3.1 Tabel users', ['name' => 'Arial', 'size' => 12, 'bold' => true]);
$section->addText('Tabel untuk menyimpan data pengguna aplikasi.');
$section->addTextBreak(1);

// Buat tabel users
$table = $section->addTable(['borderSize' => 6, 'borderColor' => '000000', 'cellMargin' => 80]);
$table->addRow();
$table->addCell(2000)->addText('Field', ['bold' => true]);
$table->addCell(1500)->addText('Type', ['bold' => true]);
$table->addCell(1000)->addText('Length', ['bold' => true]);
$table->addCell(1000)->addText('Null', ['bold' => true]);
$table->addCell(1500)->addText('Default', ['bold' => true]);
$table->addCell(2500)->addText('Keterangan', ['bold' => true]);

$userFields = [
    ['id', 'INT', '', 'NO', 'AUTO_INCREMENT', 'Primary Key'],
    ['username', 'VARCHAR', '50', 'NO', '', 'Username unik'],
    ['password', 'VARCHAR', '255', 'NO', '', 'Password hash'],
    ['role', 'ENUM', 'admin,operator', 'NO', 'operator', 'Role pengguna'],
    ['status', 'VARCHAR', '10', 'YES', 'active', 'Status aktif/nonaktif']
];

foreach ($userFields as $field) {
    $table->addRow();
    foreach ($field as $value) {
        $table->addCell()->addText($value);
    }
}
$section->addTextBreak(1);

// Tabel dokumen
$section->addText('3.2 Tabel dokumen', ['name' => 'Arial', 'size' => 12, 'bold' => true]);
$section->addText('Tabel utama untuk menyimpan data dokumen yang diupload.');
$section->addTextBreak(1);

// Buat tabel dokumen
$table = $section->addTable(['borderSize' => 6, 'borderColor' => '000000', 'cellMargin' => 80]);
$table->addRow();
$table->addCell(2000)->addText('Field', ['bold' => true]);
$table->addCell(1500)->addText('Type', ['bold' => true]);
$table->addCell(1000)->addText('Length', ['bold' => true]);
$table->addCell(1000)->addText('Null', ['bold' => true]);
$table->addCell(1500)->addText('Default', ['bold' => true]);
$table->addCell(2500)->addText('Keterangan', ['bold' => true]);

$dokumenFields = [
    ['id', 'INT', '', 'NO', 'AUTO_INCREMENT', 'Primary Key'],
    ['nama_dokumen', 'VARCHAR', '255', 'NO', '', 'Nama dokumen'],
    ['bulan_tahun', 'VARCHAR', '7', 'NO', '', 'Bulan dan tahun (YYYY-MM)'],
    ['file_pdf', 'VARCHAR', '255', 'YES', '', 'Path file PDF dokumen'],
    ['status', 'VARCHAR', '10', 'YES', 'active', 'Status aktif/inactive'],
    ['created_at', 'TIMESTAMP', '', 'YES', 'CURRENT_TIMESTAMP', 'Waktu upload']
];

foreach ($dokumenFields as $field) {
    $table->addRow();
    foreach ($field as $value) {
        $table->addCell()->addText($value);
    }
}
$section->addTextBreak(1);

// Tabel logs
$section->addText('3.3 Tabel logs', ['name' => 'Arial', 'size' => 12, 'bold' => true]);
$section->addText('Tabel untuk mencatat semua aktivitas pengguna.');
$section->addTextBreak(1);

// Buat tabel logs
$table = $section->addTable(['borderSize' => 6, 'borderColor' => '000000', 'cellMargin' => 80]);
$table->addRow();
$table->addCell(2000)->addText('Field', ['bold' => true]);
$table->addCell(1500)->addText('Type', ['bold' => true]);
$table->addCell(1000)->addText('Length', ['bold' => true]);
$table->addCell(1000)->addText('Null', ['bold' => true]);
$table->addCell(1500)->addText('Default', ['bold' => true]);
$table->addCell(2500)->addText('Keterangan', ['bold' => true]);

$logsFields = [
    ['id', 'INT', '', 'NO', 'AUTO_INCREMENT', 'Primary Key'],
    ['user_id', 'INT', '', 'NO', '', 'Foreign Key ke users.id'],
    ['action', 'VARCHAR', '50', 'NO', '', 'Jenis aksi (add, edit, delete, upload, etc)'],
    ['dokumen_id', 'INT', '', 'YES', '', 'ID dokumen terkait (opsional)'],
    ['details', 'TEXT', '', 'YES', '', 'Detail aktivitas'],
    ['timestamp', 'TIMESTAMP', '', 'YES', 'CURRENT_TIMESTAMP', 'Waktu aktivitas']
];

foreach ($logsFields as $field) {
    $table->addRow();
    foreach ($field as $value) {
        $table->addCell()->addText($value);
    }
}
$section->addTextBreak(1);

// Tabel settings
$section->addText('3.4 Tabel settings', ['name' => 'Arial', 'size' => 12, 'bold' => true]);
$section->addText('Tabel untuk menyimpan pengaturan aplikasi.');
$section->addTextBreak(1);

// Buat tabel settings
$table = $section->addTable(['borderSize' => 6, 'borderColor' => '000000', 'cellMargin' => 80]);
$table->addRow();
$table->addCell(2000)->addText('Field', ['bold' => true]);
$table->addCell(1500)->addText('Type', ['bold' => true]);
$table->addCell(1000)->addText('Length', ['bold' => true]);
$table->addCell(1000)->addText('Null', ['bold' => true]);
$table->addCell(1500)->addText('Default', ['bold' => true]);
$table->addCell(2500)->addText('Keterangan', ['bold' => true]);

$settingsFields = [
    ['id', 'INT', '', 'NO', '', 'Primary Key (hanya 1 record)'],
    ['app_title', 'VARCHAR', '255', 'NO', '', 'Judul aplikasi'],
    ['logo_path', 'VARCHAR', '255', 'YES', '', 'Path file logo'],
    ['max_file_size', 'INT', '', 'YES', '5242880', 'Maksimal ukuran file (bytes)']
];

foreach ($settingsFields as $field) {
    $table->addRow();
    foreach ($field as $value) {
        $table->addCell()->addText($value);
    }
}
$section->addTextBreak(2);

// 4. RELASI DAN CONSTRAINT
$section->addText('4. RELASI DAN CONSTRAINT', ['name' => 'Arial', 'size' => 14, 'bold' => true]);
$section->addTextBreak(1);

$section->addText('4.1 Relasi Antar Tabel', ['name' => 'Arial', 'size' => 12, 'bold' => true]);
$relasi = [
    '• users → logs: One-to-Many (satu user dapat memiliki banyak log)',
    '• users → dokumen: One-to-Many (satu user dapat mengupload banyak dokumen)',
    '• logs → dokumen: Many-to-One (opsional, log dapat terkait dengan dokumen)'
];
foreach ($relasi as $item) {
    $section->addText($item);
}
$section->addTextBreak(1);

$section->addText('4.2 Business Rules', ['name' => 'Arial', 'size' => 12, 'bold' => true]);
$rules = [
    '• Username harus unik di seluruh sistem',
    '• Hanya admin yang dapat mengubah status user',
    '• Data dokumen yang dihapus berubah status menjadi "inactive"',
    '• Semua aktivitas user dicatat dalam logs',
    '• Settings hanya memiliki 1 record (id = 1)',
    '• Format bulan_tahun harus YYYY-MM',
    '• Hanya file PDF yang diperbolehkan diupload'
];
foreach ($rules as $item) {
    $section->addText($item);
}
$section->addTextBreak(2);

// 5. INDEXING DAN PERFORMANCE
$section->addText('5. INDEXING DAN PERFORMANCE', ['name' => 'Arial', 'size' => 14, 'bold' => true]);
$section->addTextBreak(1);

$section->addText('5.1 Index yang Dianjurkan', ['name' => 'Arial', 'size' => 12, 'bold' => true]);
$section->addTextBreak(1);

$indexCode = "CREATE INDEX idx_dokumen_bulan_tahun ON dokumen(bulan_tahun);
CREATE INDEX idx_dokumen_status ON dokumen(status);
CREATE INDEX idx_dokumen_created_at ON dokumen(created_at);
CREATE INDEX idx_logs_timestamp ON logs(timestamp);
CREATE INDEX idx_logs_user_id ON logs(user_id);
CREATE INDEX idx_logs_dokumen_id ON logs(dokumen_id);
CREATE INDEX idx_users_username ON users(username);
CREATE INDEX idx_users_role ON users(role);";

$section->addText($indexCode, ['name' => 'Courier New', 'size' => 10]);
$section->addTextBreak(2);

// 6. KEAMANAN DATABASE
$section->addText('6. KEAMANAN DATABASE', ['name' => 'Arial', 'size' => 14, 'bold' => true]);
$section->addTextBreak(1);

$section->addText('6.1 Enkripsi Data', ['name' => 'Arial', 'size' => 12, 'bold' => true]);
$enkripsi = [
    '• Password user di-hash menggunakan PASSWORD_DEFAULT',
    '• Data sensitif dienkripsi jika diperlukan'
];
foreach ($enkripsi as $item) {
    $section->addText($item);
}
$section->addTextBreak(1);

$section->addText('6.2 Access Control', ['name' => 'Arial', 'size' => 12, 'bold' => true]);
$access = [
    '• Role-based access (admin vs operator)',
    '• Validasi input untuk mencegah SQL injection',
    '• Sanitasi data sebelum disimpan ke database',
    '• Validasi tipe file dan ukuran sebelum upload'
];
foreach ($access as $item) {
    $section->addText($item);
}
$section->addTextBreak(2);

// 7. SCRIPT PEMBUATAN DATABASE
$section->addText('7. SCRIPT PEMBUATAN DATABASE', ['name' => 'Arial', 'size' => 14, 'bold' => true]);
$section->addTextBreak(1);

$section->addText('7.1 SQL Script Lengkap', ['name' => 'Arial', 'size' => 12, 'bold' => true]);
$section->addTextBreak(1);

$sqlScript = "-- Membuat database
CREATE DATABASE IF NOT EXISTS arsip_digital_dokumen;
USE arsip_digital_dokumen;

-- Tabel users
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'operator') NOT NULL DEFAULT 'operator',
    status VARCHAR(10) DEFAULT 'active'
);

-- Tabel dokumen
CREATE TABLE dokumen (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_dokumen VARCHAR(255) NOT NULL,
    bulan_tahun VARCHAR(7) NOT NULL,
    file_pdf VARCHAR(255),
    status VARCHAR(10) DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel logs
CREATE TABLE logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    action VARCHAR(50) NOT NULL,
    dokumen_id INT,
    details TEXT,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Tabel settings
CREATE TABLE settings (
    id INT PRIMARY KEY,
    app_title VARCHAR(255) NOT NULL,
    logo_path VARCHAR(255),
    max_file_size INT DEFAULT 5242880
);";

$section->addText($sqlScript, ['name' => 'Courier New', 'size' => 10]);
$section->addTextBreak(2);

// Footer
$section->addTextBreak(2);
$section->addText(
    'Rancangan Database Revisi Arsip Digital Dokumen',
    ['name' => 'Arial', 'size' => 10, 'italic' => true],
    ['alignment' => 'center']
);
$section->addText(
    'Dibuat untuk: Sistem Informasi Arsip Dokumen Digital Aceh',
    ['name' => 'Arial', 'size' => 10, 'italic' => true],
    ['alignment' => 'center']
);
$section->addText(
    'Versi: 1.1 | Tanggal: ' . date('d-m-Y'),
    ['name' => 'Arial', 'size' => 10, 'italic' => true],
    ['alignment' => 'center']
);

// Simpan file Word
$filename = 'Rancangan_Database_Revisi_Arsip_Digital_Dokumen.docx';
$path = __DIR__ . '/' . $filename;

try {
    $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
    $objWriter->save($path);

    // Set header untuk download
    header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: max-age=0');

    // Baca dan output file
    readfile($path);

    // Hapus file setelah download (opsional)
    // unlink($path);

} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
?>
