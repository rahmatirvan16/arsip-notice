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

if (isset($_GET['restore_id'])) {
    $id = $_GET['restore_id'];
    $sql = "UPDATE notices SET status = 'active' WHERE id = $id";
    if (mysqli_query($conn, $sql)) {
        // Log the restore action
        $user_id = $_SESSION['user_id'];
        $log_sql = "INSERT INTO logs (user_id, action, notice_id, details) VALUES ($user_id, 'restore', $id, 'Restored notice to active status')";
        mysqli_query($conn, $log_sql);
        header("Location: restore.php");
        exit();
    } else {
        echo "Error restoring record: " . mysqli_error($conn);
    }
}

// Fetch all inactive notices
$sql = "SELECT * FROM notices WHERE status = 'inactive' ORDER BY id DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restore Data - Arsip Digital</title>
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
            <a class="nav-link active" href="restore.php"><i class="fas fa-undo"></i> Restore Data</a>
        </nav>
    </div>

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-undo"></i> Restore Data Inactive</h2>
            <a href="index.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali ke Dashboard</a>
        </div>

        <div class="card">
            <div class="card-header">
                <i class="fas fa-table"></i> Daftar Notice Inactive
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th><i class="fas fa-hashtag"></i> ID</th>
                                <th><i class="fas fa-file-alt"></i> Nomor Notice</th>
                                <th><i class="fas fa-calendar-alt"></i> Tanggal Penetapan</th>
                                <th><i class="fas fa-print"></i> Tanggal Cetak</th>
                                <th><i class="fas fa-comment"></i> Keterangan Rusak/Batal</th>
                                <th><i class="fas fa-file-pdf"></i> File PDF</th>
                                <th><i class="fas fa-cogs"></i> Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo $row['nomor_notice']; ?></td>
                                <td><?php echo $row['tanggal_penetapan']; ?></td>
                                <td><?php echo $row['tanggal_cetak']; ?></td>
                                <td><?php echo $row['keterangan_rusak']; ?></td>
                                <td>
                                    <?php if ($row['file_pdf']) { ?>
                                        <a href="uploads/<?php echo $row['file_pdf']; ?>" target="_blank" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i> Lihat PDF</a>
                                    <?php } else { echo '<span class="text-muted">Tidak ada</span>'; } ?>
                                </td>
                                <td>
                                    <a href="restore.php?restore_id=<?php echo $row['id']; ?>" class="btn btn-sm btn-success" onclick="return confirm('Yakin restore data ini?')"><i class="fas fa-undo"></i> Restore</a>
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
