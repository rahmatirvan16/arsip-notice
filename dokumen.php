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
$filter_bulan_tahun = isset($_GET['bulan_tahun']) ? $_GET['bulan_tahun'] : '';

// Fetch all active dokumen with filter
$where_clause = "status = 'active'";
if ($filter_bulan_tahun) {
    $where_clause .= " AND bulan_tahun = '$filter_bulan_tahun'";
}

$sql = "SELECT * FROM dokumen WHERE $where_clause ORDER BY id DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dokumen - Arsip Digital</title>
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
            <a class="nav-link active" href="dokumen.php"><i class="fas fa-file-alt"></i> Dokumen</a>
            <a class="nav-link" href="report.php"><i class="fas fa-search"></i> Pencarian</a>
            <a class="nav-link" href="rekap.php"><i class="fas fa-chart-bar"></i> Laporan Rekap</a>
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
            <h2><i class="fas fa-file-alt"></i> Daftar Dokumen</h2>
            <a href="dokumen_add.php" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Dokumen Baru</a>
        </div>

        <!-- Filter -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-filter"></i> Filter Dokumen
            </div>
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-4">
                        <label for="bulan_tahun" class="form-label">Bulan-Tahun</label>
                        <input type="month" class="form-control" id="bulan_tahun" name="bulan_tahun" value="<?php echo $filter_bulan_tahun; ?>">
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Filter</button>
                        <?php if ($filter_bulan_tahun) { ?>
                            <a href="dokumen.php" class="btn btn-secondary ms-2"><i class="fas fa-times"></i> Reset</a>
                        <?php } ?>
                    </div>
                </form>
            </div>
        </div>

        <!-- Dokumen Table -->
        <div class="card">
            <div class="card-header">
                <i class="fas fa-table"></i> Data Dokumen
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th><i class="fas fa-hashtag"></i> ID</th>
                                <th><i class="fas fa-file-alt"></i> Nama Dokumen</th>
                                <th><i class="fas fa-calendar-alt"></i> Bulan-Tahun</th>
                                <th><i class="fas fa-file-pdf"></i> File PDF</th>
                                <th><i class="fas fa-cogs"></i> Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo $row['nama_dokumen']; ?></td>
                                <td><?php echo $row['bulan_tahun']; ?></td>
                                <td>
                                    <?php if ($row['file_pdf']) { ?>
                                        <a href="uploads/<?php echo $row['file_pdf']; ?>" target="_blank" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i> Lihat PDF</a>
                                    <?php } else { echo '<span class="text-muted">Tidak ada</span>'; } ?>
                                </td>
                                <td>
                                    <a href="dokumen_edit.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning me-1"><i class="fas fa-edit"></i> Edit</a>
                                    <a href="dokumen_delete.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus dokumen ini?')"><i class="fas fa-trash"></i> Hapus</a>
                                </td>
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
