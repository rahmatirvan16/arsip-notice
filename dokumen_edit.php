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

$id = $_GET['id'];
$sql = "SELECT * FROM dokumen WHERE id = $id";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_dokumen = $_POST['nama_dokumen'];
    $bulan_tahun = $_POST['bulan_tahun'];

    // Handle file upload
    $file_pdf = $row['file_pdf'];
    if (isset($_FILES['file_pdf']) && $_FILES['file_pdf']['error'] == 0) {
        $target_dir = "uploads/";
        $file_name = basename($_FILES["file_pdf"]["name"]);
        $target_file = $target_dir . $file_name;
        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        if ($file_type == "pdf") {
            if (move_uploaded_file($_FILES["file_pdf"]["tmp_name"], $target_file)) {
                $file_pdf = $file_name;
            }
        }
    }

    $sql = "UPDATE dokumen SET nama_dokumen='$nama_dokumen', bulan_tahun='$bulan_tahun', file_pdf='$file_pdf' WHERE id=$id";
    if (mysqli_query($conn, $sql)) {
        header("Location: dokumen.php");
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
    <title>Edit Dokumen - Arsip Digital</title>
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
                <a class="nav-link" href="restore.php"><i class="fas fa-undo"></i> Restore Data</a>
                <a class="nav-link" href="settings.php"><i class="fas fa-cogs"></i> Pengaturan</a>
            <?php } ?>
        </nav>
    </div>

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-edit"></i> Edit Dokumen</h2>
            <a href="dokumen.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali ke Dokumen</a>
        </div>

        <div class="card">
            <div class="card-header">
                <i class="fas fa-file-alt"></i> Form Edit Dokumen
            </div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nama_dokumen" class="form-label"><i class="fas fa-tag"></i> Nama Dokumen</label>
                            <input type="text" class="form-control" id="nama_dokumen" name="nama_dokumen" value="<?php echo $row['nama_dokumen']; ?>" required placeholder="Masukkan nama dokumen">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="bulan_tahun" class="form-label"><i class="fas fa-calendar-alt"></i> Bulan-Tahun</label>
                            <input type="month" class="form-control" id="bulan_tahun" name="bulan_tahun" value="<?php echo $row['bulan_tahun']; ?>" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="file_pdf" class="form-label"><i class="fas fa-file-pdf"></i> Upload File PDF Baru</label>
                        <input type="file" class="form-control" id="file_pdf" name="file_pdf" accept=".pdf">
                        <?php if ($row['file_pdf']) { ?>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle"></i> File saat ini: <a href="uploads/<?php echo $row['file_pdf']; ?>" target="_blank"><?php echo $row['file_pdf']; ?></a>
                            </small>
                        <?php } ?>
                    </div>
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="submit" class="btn btn-primary me-md-2"><i class="fas fa-save"></i> Update</button>
                        <a href="dokumen.php" class="btn btn-secondary"><i class="fas fa-times"></i> Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
