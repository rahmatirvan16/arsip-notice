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

$id = $_GET['id'];
$sql = "SELECT * FROM users WHERE id = $id";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $role = $_POST['role'];
    $password_sql = "";
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $password_sql = ", password='$password'";
    }

    $sql = "UPDATE users SET username='$username', role='$role' $password_sql WHERE id=$id";
    if (mysqli_query($conn, $sql)) {
        header("Location: users.php");
    } else {
        $error = "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User - Arsip Digital</title>
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
            <a class="nav-link" href="report.php"><i class="fas fa-chart-bar"></i> Laporan Rekap</a>
            <a class="nav-link active" href="users.php"><i class="fas fa-users"></i> Kelola User</a>
            <a class="nav-link" href="restore.php"><i class="fas fa-undo"></i> Restore Data</a>
        </nav>
    </div>

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-edit"></i> Edit User</h2>
            <a href="users.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
        </div>

        <div class="card">
            <div class="card-header">
                <i class="fas fa-user"></i> Form Edit User
            </div>
            <div class="card-body">
                <?php if (isset($error)) { echo "<div class='alert alert-danger'><i class='fas fa-exclamation-triangle'></i> $error</div>"; } ?>
                <form method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="username" class="form-label"><i class="fas fa-user"></i> Username</label>
                            <input type="text" class="form-control" id="username" name="username" value="<?php echo $row['username']; ?>" required placeholder="Masukkan username">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label"><i class="fas fa-lock"></i> Password Baru (biarkan kosong jika tidak ingin mengubah)</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan password baru">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label"><i class="fas fa-user-tag"></i> Role</label>
                        <select class="form-select" id="role" name="role" required>
                            <option value="operator" <?php if ($row['role'] == 'operator') echo 'selected'; ?>><i class="fas fa-user"></i> Operator</option>
                            <option value="admin" <?php if ($row['role'] == 'admin') echo 'selected'; ?>><i class="fas fa-crown"></i> Admin</option>
                        </select>
                    </div>
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="submit" class="btn btn-primary me-md-2"><i class="fas fa-save"></i> Update</button>
                        <a href="users.php" class="btn btn-secondary"><i class="fas fa-times"></i> Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
