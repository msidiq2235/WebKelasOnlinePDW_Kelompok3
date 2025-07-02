<?php
session_start();

if (isset($_SESSION['userRole'])) {
    if ($_SESSION['userRole'] == 'admin') {
        header("Location: admin.php"); 
        exit();
    } elseif ($_SESSION['userRole'] == 'guru') {
        header("Location: guru.php"); 
        exit();
    } elseif ($_SESSION['userRole'] == 'siswa') {
        header("Location: siswa.php"); 
        exit();
    }
}

$error_message = '';
$success_message = '';

// Pesan error dari proses login
if (isset($_GET['error'])) {
    if ($_GET['error'] == 1) {
        $error_message = 'Username, password, atau peran yang Anda masukkan salah!';
    } elseif ($_GET['error'] == 2) {
        $error_message = 'Anda harus login untuk mengakses halaman tersebut!';
    }
}

// Pesan sukses setelah logout
if (isset($_GET['logout']) && $_GET['logout'] == 1) {
    $success_message = 'Anda telah berhasil logout.';
}

// Pesan sukses setelah registrasi
if (isset($_GET['register']) && $_GET['register'] == 'success') {
    $success_message = 'Registrasi berhasil! Silakan login dengan akun Anda.';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Login - Kelas Online</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
  <link rel="stylesheet" href="style.css" />
  <style>
    /* CSS Kustom untuk membuat tombol ekstra kecil */
    .btn-xs {
        --bs-btn-padding-y: .15rem;
        --bs-btn-padding-x: .4rem;
        --bs-btn-font-size: .75rem;
    }
  </style>
</head>
<body class="bg-light d-flex align-items-center vh-100">

  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-5 col-lg-4">
        <div class="card shadow-sm">
          <div class="card-body p-4">
            <div class="text-center mb-4">
              <a href="index.php"><i class="fas fa-graduation-cap fa-3x text-primary mb-3"></i></a>
              <h3 class="card-title mb-4 text-center text-primary">Login Kelas Online</h3>
            </div>

            <?php
            if (!empty($error_message)) {
                echo '<div class="alert alert-danger" role="alert">' . htmlspecialchars($error_message) . '</div>';
            }
            if (!empty($success_message)) {
                echo '<div class="alert alert-success" role="alert">' . htmlspecialchars($success_message) . '</div>';
            }
            ?>

            <form action="proses_login.php" method="POST">
              <div class="mb-3">
                <label for="username" class="form-label"><i class="fas fa-user me-2"></i>Username</label>
                <input id="username" name="username" type="text" class="form-control" placeholder="Masukkan username" required />
              </div>
              <div class="mb-3">
                <label for="password" class="form-label"><i class="fas fa-lock me-2"></i>Password</label>
                <input id="password" name="password" type="password" class="form-control" placeholder="Masukkan password" required />
              </div>
              <div class="mb-4">
                <label for="role" class="form-label"><i class="fas fa-user-tag me-2"></i>Pilih Peran</label>
                <select id="role" name="role" class="form-select" required>
                  <option value="" selected disabled>-- Pilih Peran --</option>
                  <option value="siswa">Siswa</option>
                  <option value="guru">Guru</option>
                  <option value="admin">Admin</option>
                </select>
              </div>
              <button type="submit" class="btn btn-primary w-100"><i class="fas fa-sign-in-alt me-2"></i>Masuk</button>
            </form>
            <div class="text-center mt-3">
                <p class="mb-2">Belum punya akun? <a href="register.php">Daftar di sini</a></p>
                <a href="index.php" class="btn btn-outline-secondary btn-xs">
                    <i class="fas fa-home me-1"></i> Back to Home
                </a>
            </div>
          </div>
        </div>
        <p class="text-center mt-3 text-white">© 2025 Kelas Online | Platform Belajar Digital</p>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>