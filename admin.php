<?php
session_start();

// Proteksi halaman
if (!isset($_SESSION['userRole']) || $_SESSION['userRole'] !== 'admin') {
    header("Location: login.php?error=2");
    exit();
}

$username = $_SESSION['namaLengkap'] ?? $_SESSION['username'];

// Hubungkan ke database
include_once 'database.php';
$database = new Database();
$db = $database->getConnection();

// Query untuk statistik (total pengguna, guru, kelas)
$stmt_pengguna = $db->query("SELECT COUNT(*) as total FROM pengguna");
$total_pengguna = $stmt_pengguna->fetch(PDO::FETCH_ASSOC)['total'];
$stmt_guru = $db->query("SELECT COUNT(*) as total FROM pengguna WHERE role = 'guru'");
$total_guru = $stmt_guru->fetch(PDO::FETCH_ASSOC)['total'];
$stmt_kelas = $db->query("SELECT COUNT(*) as total FROM kelas");
$total_kelas = $stmt_kelas->fetch(PDO::FETCH_ASSOC)['total'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Menu Admin - Kelas Online</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
  <link rel="stylesheet" href="style.css" />
</head>
<body class="admin-theme">
  <nav class="navbar navbar-expand-lg navbar-dark px-4">
    <div class="container">
      <a class="navbar-brand" href="#"><i class="fas fa-school me-2"></i>Kelas Online</a>
      <div class="collapse navbar-collapse">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
            <span class="nav-link me-3"><i class="fas fa-user-shield me-1"></i><span><?php echo htmlspecialchars($username); ?></span></span>
          </li>
          <li class="nav-item">
            <a href="logout.php" class="btn btn-outline-light"><i class="fas fa-sign-out-alt me-1"></i>Logout</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="container my-5">
    <h2>Halo, <?php echo htmlspecialchars($username); ?>!</h2>
    
    <div class="row mb-4">
      <div class="col-12">
        <div class="card shadow-sm">
          <div class="card-body">
            <h5 class="card-title text-center mb-4">Dashboard Sistem</h5>
            <div class="row text-center">
              <div class="col-md-4 mb-3 mb-md-0"> <div class="p-3 rounded" style="background-color: rgba(142, 68, 173, 0.1);">
                  <i class="fas fa-users mb-2" style="font-size: 24px; color: #8e44ad;"></i>
                  <h4><?php echo $total_pengguna; ?></h4>
                  <p class="mb-0">Total Pengguna</p>
                </div>
              </div>
              <div class="col-md-4 mb-3 mb-md-0">
                <div class="p-3 rounded" style="background-color: rgba(41, 128, 185, 0.1);">
                  <i class="fas fa-chalkboard mb-2" style="font-size: 24px; color: #2980b9;"></i>
                  <h4><?php echo $total_kelas; ?></h4>
                  <p class="mb-0">Total Kelas</p>
                </div>
              </div>
              <div class="col-md-4">
                <div class="p-3 rounded" style="background-color: rgba(39, 174, 96, 0.1);">
                  <i class="fas fa-graduation-cap mb-2" style="font-size: 24px; color: #27ae60;"></i>
                  <h4><?php echo $total_guru; ?></h4>
                  <p class="mb-0">Total Guru</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <div class="row g-4">
      <div class="col-md-4">
        <div class="card shadow-sm h-100">
          <div class="card-body d-flex flex-column justify-content-center align-items-center">
            <div class="card-icon text-primary"><i class="fas fa-user-graduate"></i></div>
            <h5 class="card-title">Kelola Siswa</h5>
            <p class="card-text text-center">Tambah, edit, atau hapus data siswa.</p>
            <a href="admin/kelola-siswa.php" class="btn btn-primary mt-auto"><i class="fas fa-cog me-1"></i>Kelola Siswa</a>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card shadow-sm h-100">
          <div class="card-body d-flex flex-column justify-content-center align-items-center">
            <div class="card-icon text-primary"><i class="fas fa-chalkboard-teacher"></i></div>
            <h5 class="card-title">Kelola Guru</h5>
            <p class="card-text text-center">Atur data dan akses guru.</p>
            <a href="admin/kelola-guru.php" class="btn btn-primary mt-auto"><i class="fas fa-cog me-1"></i>Kelola Guru</a>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card shadow-sm h-100">
          <div class="card-body d-flex flex-column justify-content-center align-items-center">
            <div class="card-icon text-primary"><i class="fas fa-school"></i></div>
            <h5 class="card-title">Kelola Kelas</h5>
            <p class="card-text text-center">Kelola seluruh data kelas dan pengguna.</p>
            <a href="admin/kelola-kelas.php" class="btn btn-primary mt-auto"><i class="fas fa-cog me-1"></i>Kelola Kelas</a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>