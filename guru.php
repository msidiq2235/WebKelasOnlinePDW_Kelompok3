<?php
session_start();

if (!isset($_SESSION['userRole']) || $_SESSION['userRole'] !== 'guru') {
    header("Location: login.php?error=2");
    exit();
}

$username = $_SESSION['namaLengkap'] ?? $_SESSION['username'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Menu Guru - Kelas Online</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
  <link rel="stylesheet" href="style.css" />
</head>
<body class="guru-theme">
  <nav class="navbar navbar-expand-lg navbar-dark px-4">
    <div class="container">
      <a class="navbar-brand" href="#"><i class="fas fa-chalkboard-teacher me-2"></i>Kelas Online</a>
      <div class="collapse navbar-collapse">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
            <span class="nav-link me-3"><i class="fas fa-user-circle me-1"></i><span><?php echo htmlspecialchars($username); ?></span></span>
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
    <div class="row g-4">
        <div class="col-md-6"><div class="card shadow-sm h-100"><div class="card-body d-flex flex-column justify-content-center align-items-center"><div class="card-icon text-primary"><i class="fas fa-book-reader"></i></div><h5 class="card-title">Input Materi</h5><p class="card-text text-center">Tambahkan materi pembelajaran baru untuk siswa.</p><a href="guru/input-materi.php" class="btn btn-primary mt-auto"><i class="fas fa-plus-circle me-1"></i>Input Materi</a></div></div></div>
        <div class="col-md-6"><div class="card shadow-sm h-100"><div class="card-body d-flex flex-column justify-content-center align-items-center"><div class="card-icon text-primary"><i class="fas fa-users-cog"></i></div><h5 class="card-title">Kelola Kelas</h5><p class="card-text text-center">Atur kelas dan data siswa secara efisien.</p><a href="guru/kelola-kelas.php" class="btn btn-primary mt-auto"><i class="fas fa-cog me-1"></i>Kelola Kelas</a></div></div></div>
    </div>
    </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>