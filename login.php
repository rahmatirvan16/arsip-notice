<?php
session_start();
include 'db.php';

// Fetch settings for dynamic display
$sql_settings = "SELECT * FROM settings WHERE id = 1";
$result_settings = mysqli_query($conn, $sql_settings);
$settings = mysqli_fetch_assoc($result_settings);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username = '$username' AND status = 'active'";
    $result = mysqli_query($conn, $sql);
    if ($row = mysqli_fetch_assoc($result)) {
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['role'] = $row['role'];
            header("Location: index.php");
            exit();
        } else {
            $error = "Password salah";
        }
    } else {
        $error = "Username tidak ditemukan atau tidak aktif";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php echo $settings['app_title']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="assets/css/style.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0,0,0,.1);
            padding: 40px;
            max-width: 450px;
            width: 100%;
        }
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-header i {
            font-size: 3rem;
            color: #007bff;
            margin-bottom: 10px;
        }
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 25px;
            padding: 12px;
            font-weight: bold;
            transition: transform 0.2s;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,.2);
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <?php if ($settings['logo_path']) { ?>
                <img src="uploads/<?php echo $settings['logo_path']; ?>" alt="Logo" style="height: 60px; margin-bottom: 10px;">
            <?php } else { ?>
                <i class="fas fa-archive"></i>
            <?php } ?>
            <h2><?php echo $settings['app_title']; ?></h2>
            <!-- <p class="text-muted">Sistem Informasi Arsip Notice Digital Aceh</p> -->
        </div>
        <?php if (isset($error)) { echo "<div class='alert alert-danger'><i class='fas fa-exclamation-triangle'></i> $error</div>"; } ?>
        <form method="POST">
            <div class="mb-3">
                <label for="username" class="form-label"><i class="fas fa-user"></i> Username</label>
                <input type="text" class="form-control" id="username" name="username" required placeholder="Masukkan username">
            </div>
            <div class="mb-3">
                <label for="password" class="form-label"><i class="fas fa-lock"></i> Password</label>
                <input type="password" class="form-control" id="password" name="password" required placeholder="Masukkan password">
            </div>
            <button type="submit" class="btn btn-primary w-100 btn-login"><i class="fas fa-sign-in-alt"></i> Login</button>
        </form>
        <!-- <div class="mt-4 text-center">
            <small class="text-muted">
                <i class="fas fa-info-circle"></i> Default users: admin/admin123 (admin), operator/operator123 (operator)
            </small>
        </div> -->
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
