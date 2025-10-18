<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Fetch current settings
$sql = "SELECT * FROM settings WHERE id = 1";
$result = mysqli_query($conn, $sql);
$settings = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $app_title = $_POST['app_title'];

    // Handle logo upload
    $logo_path = $settings['logo_path'];
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }
        $file_name = basename($_FILES["logo"]["name"]);
        $target_file = $target_dir . $file_name;
        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        if (in_array($file_type, ['jpg', 'jpeg', 'png', 'gif'])) {
            if (move_uploaded_file($_FILES["logo"]["tmp_name"], $target_file)) {
                $logo_path = $file_name;
            }
        }
    }

    $sql_update = "UPDATE settings SET app_title = '$app_title', logo_path = '$logo_path' WHERE id = 1";
    if (mysqli_query($conn, $sql_update)) {
        header("Location: settings.php");
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan - <?php echo $settings['app_title']; ?></title>
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
                <i class="fas fa-cogs"></i> <?php echo $settings['app_title']; ?>
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
            <?php if ($_SESSION['role'] == 'admin') { ?>
                <a class="nav-link" href="users.php"><i class="fas fa-users"></i> Kelola User</a>
                <a class="nav-link active" href="settings.php"><i class="fas fa-cogs"></i> Pengaturan</a>
                <a class="nav-link" href="restore.php"><i class="fas fa-undo"></i> Restore Data</a>
            <?php } ?>
        </nav>
    </div>

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-cogs"></i> Pengaturan Aplikasi</h2>
            <a href="index.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali ke Dashboard</a>
        </div>

        <div class="card">
            <div class="card-header">
                <i class="fas fa-edit"></i> Ubah Judul dan Logo Aplikasi
            </div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="app_title" class="form-label"><i class="fas fa-tag"></i> Judul Aplikasi</label>
                        <input type="text" class="form-control" id="app_title" name="app_title" value="<?php echo $settings['app_title']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="logo" class="form-label"><i class="fas fa-image"></i> Upload Logo Baru (Opsional)</label>
                        <input type="file" class="form-control" id="logo" name="logo" accept=".jpg,.jpeg,.png,.gif">
                        <small class="form-text text-muted">Format: JPG, PNG, GIF. Logo saat ini:</small>
                        <?php if ($settings['logo_path']) { ?>
                            <img src="uploads/<?php echo $settings['logo_path']; ?>" alt="Logo" style="height: 50px; margin-top: 10px;">
                        <?php } else { ?>
                            <span class="text-muted">Tidak ada logo</span>
                        <?php } ?>
                    </div>
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="submit" class="btn btn-primary me-md-2"><i class="fas fa-save"></i> Simpan Perubahan</button>
                        <a href="index.php" class="btn btn-secondary"><i class="fas fa-times"></i> Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
