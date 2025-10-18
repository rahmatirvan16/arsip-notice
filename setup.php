<?php
include 'db.php';

// Create database if not exists
$sql = "CREATE DATABASE IF NOT EXISTS arsip_digital";
if (mysqli_query($conn, $sql)) {
    echo "Database created successfully<br>";
} else {
    echo "Error creating database: " . mysqli_error($conn) . "<br>";
}

// Select database
mysqli_select_db($conn, 'arsip_digital');

// Create table
$sql = "CREATE TABLE IF NOT EXISTS notices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nomor_notice VARCHAR(255) NOT NULL,
    tanggal_penetapan DATE NOT NULL,
    tanggal_cetak DATE NOT NULL,
    keterangan_rusak TEXT,
    file_pdf VARCHAR(255),
    status VARCHAR(10) DEFAULT 'active'
)";

if (mysqli_query($conn, $sql)) {
    echo "Table notices created successfully<br>";
} else {
    echo "Error creating table notices: " . mysqli_error($conn) . "<br>";
}

// Create users table
$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'operator') NOT NULL DEFAULT 'operator',
    status VARCHAR(10) DEFAULT 'active'
)";

if (mysqli_query($conn, $sql)) {
    echo "Table users created successfully<br>";
} else {
    echo "Error creating table users: " . mysqli_error($conn) . "<br>";
}

// Add status column if not exists
$sql = "ALTER TABLE users ADD COLUMN IF NOT EXISTS status VARCHAR(10) DEFAULT 'active'";
if (mysqli_query($conn, $sql)) {
    echo "Status column added or already exists<br>";
} else {
    echo "Error adding status column: " . mysqli_error($conn) . "<br>";
}

// Update existing users to have active status if NULL
$sql = "UPDATE users SET status = 'active' WHERE status IS NULL";
if (mysqli_query($conn, $sql)) {
    echo "Existing users updated with active status<br>";
} else {
    echo "Error updating existing users: " . mysqli_error($conn) . "<br>";
}

// Create logs table
$sql = "CREATE TABLE IF NOT EXISTS logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    action VARCHAR(50) NOT NULL,
    notice_id INT,
    details TEXT,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
)";

if (mysqli_query($conn, $sql)) {
    echo "Table logs created successfully<br>";
} else {
    echo "Error creating table logs: " . mysqli_error($conn) . "<br>";
}

// Create settings table
$sql = "CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    app_title VARCHAR(255) NOT NULL DEFAULT 'Sistem Informasi Arsip Notice Digital Aceh',
    logo_path VARCHAR(255) NULL
)";

if (mysqli_query($conn, $sql)) {
    echo "Table settings created successfully<br>";
} else {
    echo "Error creating table settings: " . mysqli_error($conn) . "<br>";
}

// Create dokumen table
$sql = "CREATE TABLE IF NOT EXISTS dokumen (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_dokumen VARCHAR(255) NOT NULL,
    bulan_tahun VARCHAR(7) NOT NULL,
    file_pdf VARCHAR(255),
    status VARCHAR(10) DEFAULT 'active'
)";

if (mysqli_query($conn, $sql)) {
    echo "Table dokumen created successfully<br>";
} else {
    echo "Error creating table dokumen: " . mysqli_error($conn) . "<br>";
}

// Insert default settings if not exists
$sql = "INSERT IGNORE INTO settings (id, app_title) VALUES (1, 'Sistem Informasi Arsip Notice Digital Aceh')";
if (mysqli_query($conn, $sql)) {
    echo "Default settings inserted<br>";
} else {
    echo "Error inserting default settings: " . mysqli_error($conn) . "<br>";
}

// Insert default admin user if not exists
$sql = "INSERT IGNORE INTO users (username, password, role) VALUES ('admin', '" . password_hash('admin123', PASSWORD_DEFAULT) . "', 'admin')";
if (mysqli_query($conn, $sql)) {
    echo "Default admin user created (username: admin, password: admin123)<br>";
} else {
    echo "Error creating default admin: " . mysqli_error($conn) . "<br>";
}

// Insert default operator user if not exists
$sql = "INSERT IGNORE INTO users (username, password, role) VALUES ('operator', '" . password_hash('operator123', PASSWORD_DEFAULT) . "', 'operator')";
if (mysqli_query($conn, $sql)) {
    echo "Default operator user created (username: operator, password: operator123)<br>";
} else {
    echo "Error creating default operator: " . mysqli_error($conn) . "<br>";
}

echo "Setup complete. You can now use the application.";
?>
