<?php
session_start();
include_once '../database.php';

if (!isset($_SESSION['userRole']) || $_SESSION['userRole'] !== 'siswa') {
    header("Location: ../login.php?error=2");
    exit();
}

$siswa_id = $_SESSION['userId'];
$nama_lengkap = $_SESSION['namaLengkap'];

$database = new Database();
$db = $database->getConnection();

// Ambil data nilai dari siswa yang login
$query = "SELECT n.nilai, n.tanggal_pengerjaan, k.judul_kuis 
          FROM nilai n 
          JOIN kuis k ON n.kuis_id = k.id 
          WHERE n.siswa_id = :siswa_id 
          ORDER BY n.tanggal_pengerjaan DESC";

$stmt = $db->prepare($query);
$stmt->bindParam(':siswa_id', $siswa_id);
$stmt->execute();
$scores = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Rekap Nilai - Kelas Online</title>
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

    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Rekap Nilai Anda</h2>
            <a href="../siswa.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Menu
            </a>
        </div>
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Halo, <?php echo htmlspecialchars($nama_lengkap); ?>!</h4>
                    </div>
                    <div class="card-body p-4">
                        <h5 class="mt-2 mb-3">Detail Nilai Kuis:</h5>
                        <?php if (empty($scores)): ?>
                            <div class="alert alert-info">Belum ada riwayat kuis yang Anda kerjakan.</div>
                        <?php else: ?>
                            <ul class="list-group">
                            <?php foreach ($scores as $score): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong><?php echo htmlspecialchars($score['judul_kuis']); ?></strong>
                                        <br>
                                        <small class="text-muted">Dikerjakan pada: <?php echo date('d M Y, H:i', strtotime($score['tanggal_pengerjaan'])); ?></small>
                                    </div>
                                    <span class="badge bg-primary rounded-pill fs-5"><?php echo number_format($score['nilai'], 2); ?></span>
                                </li>
                            <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>