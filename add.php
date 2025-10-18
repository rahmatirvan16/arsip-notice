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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nomor_notice = $_POST['nomor_notice'];
    $tanggal_penetapan = $_POST['tanggal_penetapan'];
    $tanggal_cetak = $_POST['tanggal_cetak'];
    $keterangan_rusak = $_POST['keterangan_rusak'];

    // Handle file upload
    $file_pdf = '';
    if (isset($_FILES['file_pdf']) && $_FILES['file_pdf']['error'] == 0) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }
        $file_name = basename($_FILES["file_pdf"]["name"]);
        $target_file = $target_dir . $file_name;
        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        if ($file_type == "pdf") {
            if (move_uploaded_file($_FILES["file_pdf"]["tmp_name"], $target_file)) {
                $file_pdf = $file_name;
            }
        }
    }

    $sql = "INSERT INTO notices (nomor_notice, tanggal_penetapan, tanggal_cetak, keterangan_rusak, file_pdf) VALUES ('$nomor_notice', '$tanggal_penetapan', '$tanggal_cetak', '$keterangan_rusak', '$file_pdf')";
    if (mysqli_query($conn, $sql)) {
        $notice_id = mysqli_insert_id($conn);
        // Log the add action
        $user_id = $_SESSION['user_id'];
        $log_sql = "INSERT INTO logs (user_id, action, notice_id, details) VALUES ($user_id, 'add', $notice_id, 'Added new notice: $nomor_notice')";
        mysqli_query($conn, $log_sql);
        header("Location: index.php");
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Notice - Arsip Digital</title>
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
            <a class="nav-link active" href="add.php"><i class="fas fa-plus"></i> Tambah Notice</a>
            <a class="nav-link" href="dokumen.php"><i class="fas fa-file-alt"></i> Dokumen</a>
            <a class="nav-link" href="report.php"><i class="fas fa-search"></i> Pencarian</a>
            <a class="nav-link" href="rekap.php"><i class="fas fa-chart-bar"></i> Laporan Rekap</a>
            <?php if ($_SESSION['role'] == 'admin') { ?>
                <a class="nav-link" href="users.php"><i class="fas fa-users"></i> Kelola User</a>
                <a class="nav-link" href="settings.php"><i class="fas fa-cogs"></i> Pengaturan</a>
                <a class="nav-link" href="restore.php"><i class="fas fa-undo"></i> Restore Data</a>
            <?php } ?>
        </nav>
    </div>

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-plus-circle"></i> Tambah Notice Baru</h2>
            <a href="index.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali ke Dashboard</a>
        </div>

        <div class="card">
            <div class="card-header">
                <i class="fas fa-file-alt"></i> Form Tambah Notice
            </div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nomor_notice" class="form-label"><i class="fas fa-hashtag"></i> Nomor Notice</label>
                            <input type="text" class="form-control" id="nomor_notice" name="nomor_notice" required placeholder="Masukkan nomor notice">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="tanggal_penetapan" class="form-label"><i class="fas fa-calendar-alt"></i> Tanggal Penetapan</label>
                            <input type="date" class="form-control" id="tanggal_penetapan" name="tanggal_penetapan" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="tanggal_cetak" class="form-label"><i class="fas fa-print"></i> Tanggal Cetak</label>
                            <input type="date" class="form-control" id="tanggal_cetak" name="tanggal_cetak" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="file_pdf" class="form-label"><i class="fas fa-file-pdf"></i> Upload File PDF</label>
                            <input type="file" class="form-control" id="file_pdf" name="file_pdf" accept=".pdf">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="keterangan_rusak" class="form-label"><i class="fas fa-comment"></i> Keterangan Rusak/Batal</label>
                        <select class="form-control" id="keterangan_rusak" name="keterangan_rusak" required>
                            <option value="">Pilih Keterangan</option>
                            <option value="Batal">Batal</option>
                            <option value="Rusak">Rusak</option>
                        </select>
                    </div>
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="submit" class="btn btn-primary me-md-2"><i class="fas fa-save"></i> Simpan</button>
                        <a href="index.php" class="btn btn-secondary"><i class="fas fa-times"></i> Batal</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Data Notice -->
        <div class="card mt-4">
            <div class="card-header">
                <i class="fas fa-table"></i> Data Notice Pajak Rusak/Batal
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
                            <?php
                            // Fetch all active notices for display
                            $sql_all = "SELECT * FROM notices WHERE status = 'active' ORDER BY id DESC";
                            $result_all = mysqli_query($conn, $sql_all);
                            while ($row = mysqli_fetch_assoc($result_all)) { ?>
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
                                    <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning me-1"><i class="fas fa-edit"></i> Edit</a>
                                    <a href="delete.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus?')"><i class="fas fa-trash"></i> Hapus</a>
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
