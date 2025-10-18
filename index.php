<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch settings for dynamic navbar
$sql_settings = "SELECT * FROM settings WHERE id = 1";
$result_settings = mysqli_query($conn, $sql_settings);
$settings = mysqli_fetch_assoc($result_settings);

// Fetch all active notices
$sql = "SELECT * FROM notices WHERE status = 'active' ORDER BY id DESC";
$result = mysqli_query($conn, $sql);

// Get filter parameters
$filter_year = isset($_GET['year']) ? $_GET['year'] : date('Y');
$filter_month = isset($_GET['month']) ? $_GET['month'] : '';

// Count notices by keterangan_rusak with filters
$where_clause = "status = 'active'";
if ($filter_year) {
    $where_clause .= " AND YEAR(tanggal_penetapan) = '$filter_year'";
}
if ($filter_month) {
    $where_clause .= " AND MONTH(tanggal_penetapan) = '$filter_month'";
}

$sql_batal = "SELECT COUNT(*) as count FROM notices WHERE $where_clause AND keterangan_rusak = 'Batal'";
$result_batal = mysqli_query($conn, $sql_batal);
$row_batal = mysqli_fetch_assoc($result_batal);
$count_batal = $row_batal['count'];

$sql_rusak = "SELECT COUNT(*) as count FROM notices WHERE $where_clause AND keterangan_rusak = 'Rusak'";
$result_rusak = mysqli_query($conn, $sql_rusak);
$row_rusak = mysqli_fetch_assoc($result_rusak);
$count_rusak = $row_rusak['count'];


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Arsip Digital Notice Pajak</title>
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
            <a class="nav-link active" href="index.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a class="nav-link" href="add.php"><i class="fas fa-plus"></i> Tambah Notice</a>
            <a class="nav-link" href="dokumen.php"><i class="fas fa-file-alt"></i> Dokumen</a>
            <a class="nav-link" href="report.php"><i class="fas fa-search"></i> Pencarian</a>
            <a class="nav-link" href="rekap.php"><i class="fas fa-chart-bar"></i> Laporan Rekap</a>
            <?php if ($_SESSION['role'] == 'admin') { ?>
                <a class="nav-link" href="users.php"><i class="fas fa-users"></i> Kelola User</a>
                <a class="nav-link" href="settings.php"><i class="fas fa-cog"></i> Pengaturan</a>
            <?php } ?>
        </nav>
    </div>

    <div class="main-content">
        <div class="page-header">
            <h1 class="page-title"><i class="fas fa-tachometer-alt"></i> Dashboard</h1>
            <a href="add.php" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Notice Baru</a>
        </div>

        <!-- Stats cards -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card stats-card">
                    <div class="card-body">
                        <h5 class="card-title">TOTAL NOTICES</h5>
                        <h2><?php echo $count_batal + $count_rusak; ?></h2>
                        <small class="text-muted">Active notices</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stats-card">
                    <div class="card-body">
                        <h5 class="card-title">BATAL</h5>
                        <h2><?php echo $count_batal; ?></h2>
                        <small class="text-muted">Cancelled notices</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stats-card">
                    <div class="card-body">
                        <h5 class="card-title">RUSAK</h5>
                        <h2><?php echo $count_rusak; ?></h2>
                        <small class="text-muted">Damaged notices</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stats-card">
                    <div class="card-body">
                        <h5 class="card-title">TOTAL DOKUMEN</h5>
                        <h2><?php echo mysqli_num_rows($result); ?></h2>
                        <small class="text-muted">Total documents</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
