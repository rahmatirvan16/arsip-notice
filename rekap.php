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

// Rekapan per bulan
$sql_rekap_bulan = "SELECT MONTH(tanggal_penetapan) as bulan,
                           SUM(CASE WHEN keterangan_rusak = 'Batal' THEN 1 ELSE 0 END) as batal,
                           SUM(CASE WHEN keterangan_rusak = 'Rusak' THEN 1 ELSE 0 END) as rusak
                    FROM notices
                    WHERE status = 'active' AND YEAR(tanggal_penetapan) = '$filter_year'
                    GROUP BY MONTH(tanggal_penetapan)
                    ORDER BY bulan";
$result_rekap_bulan = mysqli_query($conn, $sql_rekap_bulan);

// Rekapan per tahun
$sql_rekap_tahun = "SELECT YEAR(tanggal_penetapan) as tahun,
                           SUM(CASE WHEN keterangan_rusak = 'Batal' THEN 1 ELSE 0 END) as batal,
                           SUM(CASE WHEN keterangan_rusak = 'Rusak' THEN 1 ELSE 0 END) as rusak
                    FROM notices
                    WHERE status = 'active'
                    GROUP BY YEAR(tanggal_penetapan)
                    ORDER BY tahun DESC";
$result_rekap_tahun = mysqli_query($conn, $sql_rekap_tahun);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Rekap - Arsip Digital</title>
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
            <a class="nav-link active" href="rekap.php"><i class="fas fa-chart-bar"></i> Laporan Rekap</a>
            <?php if ($_SESSION['role'] == 'admin') { ?>
                <a class="nav-link" href="users.php"><i class="fas fa-users"></i> Kelola User</a>
                <a class="nav-link" href="logs.php"><i class="fas fa-history"></i> Log Aktivitas</a>
                <a class="nav-link" href="restore.php"><i class="fas fa-undo"></i> Restore Data</a>
                <a class="nav-link" href="backup.php"><i class="fas fa-download"></i> Backup Database</a>
                <a class="nav-link" href="settings.php"><i class="fas fa-cogs"></i> Pengaturan</a>
            <?php } ?>
        </nav>
    </div>

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-chart-bar"></i> Laporan Rekap Notice Pajak</h2>
            <a href="index.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali ke Dashboard</a>
        </div>

        <!-- Filter -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-filter"></i> Filter Rekapan
            </div>
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-4">
                        <label for="year" class="form-label">Tahun</label>
                        <select class="form-control" id="year" name="year">
                            <?php for ($y = date('Y') - 5; $y <= date('Y') + 1; $y++) { ?>
                                <option value="<?php echo $y; ?>" <?php if ($filter_year == $y) echo 'selected'; ?>><?php echo $y; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="month" class="form-label">Bulan (Opsional)</label>
                        <select class="form-control" id="month" name="month">
                            <option value="">Semua Bulan</option>
                            <?php for ($m = 1; $m <= 12; $m++) { ?>
                                <option value="<?php echo $m; ?>" <?php if ($filter_month == $m) echo 'selected'; ?>><?php echo date('F', mktime(0, 0, 0, $m, 1)); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Filter</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Rekapan Data -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-times-circle text-danger"></i> Notice Batal</h5>
                        <h2 class="text-danger"><?php echo $count_batal; ?></h2>
                        <small class="text-muted">
                            <?php echo $filter_month ? date('F', mktime(0, 0, 0, $filter_month, 1)) . ' ' : ''; ?><?php echo $filter_year; ?>
                        </small>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-exclamation-triangle text-warning"></i> Notice Rusak</h5>
                        <h2 class="text-warning"><?php echo $count_rusak; ?></h2>
                        <small class="text-muted">
                            <?php echo $filter_month ? date('F', mktime(0, 0, 0, $filter_month, 1)) . ' ' : ''; ?><?php echo $filter_year; ?>
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rekapan Bulanan -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-calendar-alt"></i> Rekapan Bulanan Tahun <?php echo $filter_year; ?>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Bulan</th>
                                <th>Notice Batal</th>
                                <th>Notice Rusak</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($result_rekap_bulan)) { ?>
                            <tr>
                                <td><?php echo date('F', mktime(0, 0, 0, $row['bulan'], 1)); ?></td>
                                <td><?php echo $row['batal']; ?></td>
                                <td><?php echo $row['rusak']; ?></td>
                                <td><?php echo $row['batal'] + $row['rusak']; ?></td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Rekapan Tahunan -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-calendar-check"></i> Rekapan Tahunan
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Tahun</th>
                                <th>Notice Batal</th>
                                <th>Notice Rusak</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($result_rekap_tahun)) { ?>
                            <tr>
                                <td><?php echo $row['tahun']; ?></td>
                                <td><?php echo $row['batal']; ?></td>
                                <td><?php echo $row['rusak']; ?></td>
                                <td><?php echo $row['batal'] + $row['rusak']; ?></td>
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
