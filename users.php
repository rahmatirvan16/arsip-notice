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

if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = $_GET['id'];
    if ($_GET['action'] == 'deactivate') {
        $sql = "UPDATE users SET status = 'inactive' WHERE id = $id";
        mysqli_query($conn, $sql);
    } elseif ($_GET['action'] == 'activate') {
        $sql = "UPDATE users SET status = 'active' WHERE id = $id";
        mysqli_query($conn, $sql);
    }
    header("Location: users.php");
    exit();
}

// Fetch all users
$sql = "SELECT * FROM users ORDER BY id DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola User - Arsip Digital</title>
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
            <a class="nav-link active" href="users.php"><i class="fas fa-users"></i> Kelola User</a>
            <a class="nav-link" href="restore.php"><i class="fas fa-undo"></i> Restore Data</a>
        </nav>
    </div>

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-users"></i> Kelola User</h2>
            <div>
                <a href="user_add.php" class="btn btn-primary me-2"><i class="fas fa-user-plus"></i> Tambah User Baru</a>
                <a href="index.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali ke Dashboard</a>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <i class="fas fa-table"></i> Daftar User
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th><i class="fas fa-hashtag"></i> ID</th>
                                <th><i class="fas fa-user"></i> Username</th>
                                <th><i class="fas fa-user-tag"></i> Role</th>
                                <th><i class="fas fa-toggle-on"></i> Status</th>
                                <th><i class="fas fa-cogs"></i> Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo $row['username']; ?></td>
                                <td>
                                    <span class="badge bg-<?php echo $row['role'] == 'admin' ? 'primary' : 'secondary'; ?>">
                                        <i class="fas fa-<?php echo $row['role'] == 'admin' ? 'crown' : 'user'; ?>"></i> <?php echo ucfirst($row['role']); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-<?php echo $row['status'] == 'active' ? 'success' : 'danger'; ?>">
                                        <i class="fas fa-<?php echo $row['status'] == 'active' ? 'check-circle' : 'times-circle'; ?>"></i> <?php echo ucfirst($row['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="user_edit.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning me-1"><i class="fas fa-edit"></i> Edit</a>
                                    <?php if ($row['status'] == 'active') { ?>
                                        <a href="users.php?action=deactivate&id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin nonaktifkan user ini?')"><i class="fas fa-ban"></i> Nonaktifkan</a>
                                    <?php } else { ?>
                                        <a href="users.php?action=activate&id=<?php echo $row['id']; ?>" class="btn btn-sm btn-success" onclick="return confirm('Yakin aktifkan user ini?')"><i class="fas fa-check"></i> Aktifkan</a>
                                    <?php } ?>
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
