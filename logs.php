 <?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Fetch settings for dynamic navbar
$sql_settings = "SELECT * FROM settings WHERE id = 1";
$result_settings = mysqli_query($conn, $sql_settings);
$settings = mysqli_fetch_assoc($result_settings);

// Handle search
$search = '';
$where_clause = '';
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    $where_clause = "WHERE l.details LIKE '%$search%' OR u.username LIKE '%$search%' OR l.action LIKE '%$search%' OR n.nomor_notice LIKE '%$search%' OR d.nama_dokumen LIKE '%$search%'";
}

// Fetch logs with user info, notice number, and dokumen name
$sql = "SELECT l.*, u.username, n.nomor_notice, d.nama_dokumen FROM logs l JOIN users u ON l.user_id = u.id LEFT JOIN notices n ON l.notice_id = n.id LEFT JOIN dokumen d ON l.dokumen_id = d.id $where_clause ORDER BY l.timestamp DESC";
$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Aktivitas - Arsip Digital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <?php if ($settings['logo_path']) { ?>
                    <img src="uploads/<?php echo $settings['logo_path']; ?>" alt="Logo" style="height: 30px; margin-right: 10px;">
                <?php } ?>
                <i class="fas fa-archive"></i> <?php echo $settings['app_title']; ?>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <span class="navbar-text me-3">
                            <i class="fas fa-user"></i> <?php echo $_SESSION['username']; ?> (<?php echo $_SESSION['role']; ?>)
                        </span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="sidebar">
        <nav class="nav flex-column">
            <a class="nav-link" href="index.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a class="nav-link" href="add.php"><i class="fas fa-plus"></i> Tambah Notice</a>
            <a class="nav-link" href="dokumen.php"><i class="fas fa-file-alt"></i> Dokumen</a>
            <a class="nav-link" href="report.php"><i class="fas fa-search"></i> Pencarian</a>
            <a class="nav-link" href="rekap.php"><i class="fas fa-chart-bar"></i> Laporan Rekap</a>
            <a class="nav-link" href="settings.php"><i class="fas fa-cogs"></i> Pengaturan</a>
            <a class="nav-link" href="users.php"><i class="fas fa-users"></i> Kelola User</a>
            <a class="nav-link active" href="logs.php"><i class="fas fa-history"></i> Log Aktivitas</a>
            <a class="nav-link" href="restore.php"><i class="fas fa-undo"></i> Restore Data</a>
            <a class="nav-link" href="backup.php"><i class="fas fa-download"></i> Backup Database</a>
        </nav>
    </div>

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-history"></i> Log Aktivitas</h2>
            <a href="index.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali ke Dashboard</a>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-search"></i> Pencarian Log
            </div>
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-8">
                        <input type="text" class="form-control" id="search" name="search" placeholder="Cari berdasarkan username, action, detail, nomor notice, atau nama dokumen..." value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search"></i> Cari</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <i class="fas fa-table"></i> Daftar Log Aktivitas
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th><i class="fas fa-calendar-alt"></i> Waktu</th>
                                <th><i class="fas fa-user"></i> User</th>
                                <th><i class="fas fa-cogs"></i> Action</th>
                                <th><i class="fas fa-hashtag"></i> ID Notice/Dokumen</th>
                                <th><i class="fas fa-file-alt"></i> Nomor Notice / Nama Dokumen</th>
                                <th><i class="fas fa-comment"></i> Detail</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                            <tr>
                                <td><?php echo $row['timestamp']; ?></td>
                                <td><?php echo $row['username']; ?></td>
                                <td><?php echo ucfirst($row['action']); ?></td>
                                <td><?php echo $row['notice_id'] ?: ($row['dokumen_id'] ?: '-'); ?></td>
                                <td><?php echo $row['nomor_notice'] ?: ($row['nama_dokumen'] ?: '-'); ?></td>
                                <td><?php echo $row['details']; ?></td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
