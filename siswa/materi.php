<?php
session_start();
// Path ke file database.php disesuaikan karena file ini ada di dalam subfolder
include_once '../database.php'; 

// Proteksi halaman, hanya untuk siswa
if (!isset($_SESSION['userRole']) || $_SESSION['userRole'] !== 'siswa') {
    header("Location: ../login.php?error=2");
    exit();
}

$siswa_id = $_SESSION['userId'];
$nama_lengkap = $_SESSION['namaLengkap'];

// Ambil data materi dari kelas yang diikuti siswa
$database = new Database();
$db = $database->getConnection();

$query = "SELECT m.judul, m.konten, k.id as kuis_id, k.judul_kuis 
          FROM materi m
          JOIN pendaftaran_kelas pk ON m.kelas_id = pk.kelas_id
          LEFT JOIN kuis k ON m.kelas_id = k.kelas_id
          WHERE pk.siswa_id = :siswa_id
          GROUP BY m.id"; // Group by untuk menghindari duplikasi materi jika ada banyak kuis

$stmt = $db->prepare($query);
$stmt->bindParam(':siswa_id', $siswa_id);
$stmt->execute();
$materials = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Materi Pembelajaran - Kelas Online</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
  <link rel="stylesheet" href="../style.css" />
</head>
<body class="siswa-theme">
  <nav class="navbar navbar-expand-lg navbar-dark px-4">
    <div class="container">
        <a class="navbar-brand" href="../siswa.php"><i class="fas fa-home me-2"></i>Menu Siswa</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <span class="nav-link me-3"><i class="fas fa-user-circle me-1"></i><span><?php echo htmlspecialchars($nama_lengkap); ?></span></span>
                </li>
                <li class="nav-item">
                    <a href="../logout.php" class="btn btn-outline-light"><i class="fas fa-sign-out-alt me-1"></i>Logout</a>
                </li>
            </ul>
        </div>
    </div>
  </nav>

  <div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Materi Pembelajaran Anda</h2>
        <a href="../siswa.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Menu
        </a>
    </div>
    <div class="row">
      <?php if (empty($materials)): ?>
        <div class="col-12">
          <div class="alert alert-info text-center">Anda belum terdaftar di kelas manapun atau belum ada materi yang tersedia.</div>
        </div>
      <?php else: ?>
        <?php foreach ($materials as $material): ?>
          <div class="col-md-6 mb-4">
            <div class="card h-100 shadow-sm">
              <div class="card-header bg-primary text-white">
                <?php echo htmlspecialchars($material['judul']); ?>
              </div>
              <div class="card-body d-flex flex-column">
                <p class="card-text flex-grow-1" style="white-space: pre-wrap;"><?php echo htmlspecialchars($material['konten']); ?></p>
                <?php if (!empty($material['kuis_id'])): ?>
                  <a href="kuis.php?id=<?php echo $material['kuis_id']; ?>" class="btn btn-success mt-3 align-self-start">
                    <i class="fas fa-play-circle me-1"></i> Kerjakan Kuis: <?php echo htmlspecialchars($material['judul_kuis']); ?>
                  </a>
                <?php endif; ?>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>