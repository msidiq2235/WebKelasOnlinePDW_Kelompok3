<?php
session_start();

// Jika pengguna sudah login, arahkan ke halaman yang sesuai
if (isset($_SESSION['userRole'])) {
    if ($_SESSION['userRole'] == 'admin') {
        header("Location: admin.php"); 
    } elseif ($_SESSION['userRole'] == 'guru') {
        header("Location: guru.php"); 
    } elseif ($_SESSION['userRole'] == 'siswa') {
        header("Location: siswa.php"); 
    }
    exit();
}

$error_message = '';
if (isset($_GET['error'])) {
    switch ($_GET['error']) {
        case 'passwords_not_match':
            $error_message = 'Password dan konfirmasi password tidak cocok!';
            break;
        case 'username_exists':
            $error_message = 'Username sudah digunakan, silakan pilih yang lain.';
            break;
        case 'empty_fields':
            $error_message = 'Semua field wajib diisi!';
            break;
        case 'db_error':
            $error_message = 'Terjadi kesalahan pada database. Coba lagi nanti.';
            break;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Registrasi - Kelas Online</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
  <link rel="stylesheet" href="style.css" />
</head>
<body class="bg-light d-flex align-items-center vh-100">

  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-5 col-lg-5">
        <div class="card shadow-sm">
          <div class="card-body p-4">
            <div class="text-center mb-4">
              <i class="fas fa-user-plus fa-3x text-primary mb-3"></i>
              <h3 class="card-title mb-4 text-center text-primary">Buat Akun Baru</h3>
            </div>

            <?php
            if (!empty($error_message)) {
                echo '<div class="alert alert-danger" role="alert">' . htmlspecialchars($error_message) . '</div>';
            }
            ?>

            <form action="proses_register.php" method="POST">
              <div class="mb-3">
                <label for="nama_lengkap" class="form-label"><i class="fas fa-user-edit me-2"></i>Nama Lengkap</label>
                <input id="nama_lengkap" name="nama_lengkap" type="text" class="form-control" placeholder="Masukkan nama lengkap" required />
              </div>
              <div class="mb-3">
                <label for="username" class="form-label"><i class="fas fa-user me-2"></i>Username</label>
                <input id="username" name="username" type="text" class="form-control" placeholder="Masukkan username" required />
              </div>
              <div class="mb-3">
                <label for="password" class="form-label"><i class="fas fa-lock me-2"></i>Password</label>
                <input id="password" name="password" type="password" class="form-control" placeholder="Masukkan password" required />
              </div>
               <div class="mb-3">
                <label for="password_konfirmasi" class="form-label"><i class="fas fa-check-circle me-2"></i>Konfirmasi Password</label>
                <input id="password_konfirmasi" name="password_konfirmasi" type="password" class="form-control" placeholder="Ulangi password" required />
              </div>
              <div class="mb-4">
                <label for="role" class="form-label"><i class="fas fa-user-tag me-2"></i>Daftar Sebagai</label>
                <select id="role" name="role" class="form-select" required>
                  <option value="" selected disabled>-- Pilih Peran --</option>
                  <option value="siswa">Siswa</option>
                  <option value="guru">Guru</option>
                </select>
              </div>
              <button type="submit" class="btn btn-primary w-100"><i class="fas fa-user-plus me-2"></i>Daftar</button>
            </form>
             <p class="text-center mt-3 mb-0">Sudah punya akun? <a href="login.php">Login di sini</a></p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>