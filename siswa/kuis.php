<?php
session_start();
include_once '../database.php';

// Proteksi halaman umum
if (!isset($_SESSION['userRole']) || $_SESSION['userRole'] !== 'siswa') {
    header("Location: ../login.php?error=2");
    exit();
}

$siswa_id = $_SESSION['userId'];
$nama_lengkap = $_SESSION['namaLengkap'];

$database = new Database();
$db = $database->getConnection();

// Cek apakah ada ID kuis di URL
$mode_pengerjaan = isset($_GET['id']) && !empty($_GET['id']);

if ($mode_pengerjaan) {
    // ---- LOGIKA UNTUK MENGERJAKAN KUIS ----
    $kuis_id = $_GET['id'];

    // Logika untuk memproses form jawaban saat disubmit
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $jawaban_user = $_POST['jawaban'] ?? [];
        $skor = 0;

        $query_kunci = "SELECT id, kunci_jawaban FROM pertanyaan WHERE kuis_id = :kuis_id";
        $stmt_kunci = $db->prepare($query_kunci);
        $stmt_kunci->bindParam(':kuis_id', $kuis_id);
        $stmt_kunci->execute();
        $kunci_jawaban_arr = $stmt_kunci->fetchAll(PDO::FETCH_KEY_PAIR);

        foreach ($jawaban_user as $pertanyaan_id => $jawaban) {
            if (isset($kunci_jawaban_arr[$pertanyaan_id]) && $kunci_jawaban_arr[$pertanyaan_id] == $jawaban) {
                $skor++;
            }
        }
        
        $total_pertanyaan = count($kunci_jawaban_arr);
        $nilai_akhir = ($total_pertanyaan > 0) ? ($skor / $total_pertanyaan) * 100 : 0;

        // Simpan nilai ke database
        $query_simpan = "INSERT INTO nilai (kuis_id, siswa_id, nilai) VALUES (:kuis_id, :siswa_id, :nilai)";
        $stmt_simpan = $db->prepare($query_simpan);
        $stmt_simpan->bindParam(':kuis_id', $kuis_id);
        $stmt_simpan->bindParam(':siswa_id', $siswa_id);
        $stmt_simpan->bindParam(':nilai', $nilai_akhir);
        $stmt_simpan->execute();

        header("Location: nilai.php");
        exit();
    }

    // Logika untuk menampilkan kuis
    $query_kuis = "SELECT * FROM kuis WHERE id = :kuis_id";
    $stmt_kuis = $db->prepare($query_kuis);
    $stmt_kuis->bindParam(':kuis_id', $kuis_id);
    $stmt_kuis->execute();
    $kuis = $stmt_kuis->fetch(PDO::FETCH_ASSOC);

    if (!$kuis) {
        die("Kuis tidak ditemukan atau ID kuis tidak valid.");
    }

    $query_pertanyaan = "SELECT p.id, p.teks_pertanyaan, o.kode_opsi, o.teks_opsi 
                         FROM pertanyaan p 
                         JOIN opsi_jawaban o ON p.id = o.pertanyaan_id 
                         WHERE p.kuis_id = :kuis_id ORDER BY p.id, o.kode_opsi";
    $stmt_pertanyaan = $db->prepare($query_pertanyaan);
    $stmt_pertanyaan->bindParam(':kuis_id', $kuis_id);
    $stmt_pertanyaan->execute();
    $results = $stmt_pertanyaan->fetchAll(PDO::FETCH_ASSOC);

    $pertanyaan_list = [];
    foreach ($results as $row) {
        $pertanyaan_list[$row['id']]['teks_pertanyaan'] = $row['teks_pertanyaan'];
        $pertanyaan_list[$row['id']]['opsi'][] = ['kode' => $row['kode_opsi'], 'teks' => $row['teks_opsi']];
    }
} else {
    // ---- LOGIKA UNTUK MENAMPILKAN DAFTAR KUIS ----
    $query_list = "SELECT k.id, k.judul_kuis, kl.nama_kelas
                   FROM kuis k
                   JOIN pendaftaran_kelas pk ON k.kelas_id = pk.kelas_id
                   JOIN kelas kl ON k.kelas_id = kl.id
                   WHERE pk.siswa_id = :siswa_id
                   ORDER BY kl.nama_kelas, k.judul_kuis";

    $stmt_list = $db->prepare($query_list);
    $stmt_list->bindParam(':siswa_id', $siswa_id);
    $stmt_list->execute();
    $quizzes = $stmt_list->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title><?php echo $mode_pengerjaan ? 'Kuis: ' . htmlspecialchars($kuis['judul_kuis']) : 'Pilih Kuis'; ?></title>
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
        <?php if ($mode_pengerjaan): ?>
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><?php echo htmlspecialchars($kuis['judul_kuis']); ?></h2>
                <a href="kuis.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar Kuis
                </a>
            </div>
            <div class="card shadow-sm">
                <div class="card-body p-4 p-md-5">
                    <form action="kuis.php?id=<?php echo $kuis_id; ?>" method="POST">
                        <?php 
                        $no = 1;
                        foreach ($pertanyaan_list as $id_pertanyaan => $detail_pertanyaan): ?>
                            <div class="mb-4 pb-3 border-bottom">
                                <p class="fw-bold"><?php echo $no++; ?>. <?php echo htmlspecialchars($detail_pertanyaan['teks_pertanyaan']); ?></p>
                                <?php foreach ($detail_pertanyaan['opsi'] as $opsi): ?>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="jawaban[<?php echo $id_pertanyaan; ?>]" id="opsi_<?php echo $id_pertanyaan; ?>_<?php echo $opsi['kode']; ?>" value="<?php echo $opsi['kode']; ?>" required>
                                        <label class="form-check-label" for="opsi_<?php echo $id_pertanyaan; ?>_<?php echo $opsi['kode']; ?>">
                                            <?php echo htmlspecialchars($opsi['teks']); ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endforeach; ?>
                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">Kumpulkan Jawaban</button>
                        </div>
                    </form>
                </div>
            </div>
        <?php else: ?>
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Pilih Kuis untuk Dikerjakan</h2>
                <a href="../siswa.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Kembali ke Menu
                </a>
            </div>
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="list-group shadow-sm">
                        <?php if (empty($quizzes)): ?>
                            <div class="list-group-item text-center p-4">
                                <p class="mb-0">Belum ada kuis yang tersedia untuk Anda.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($quizzes as $quiz): ?>
                                <a href="kuis.php?id=<?php echo $quiz['id']; ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="mb-1"><?php echo htmlspecialchars($quiz['judul_kuis']); ?></h5>
                                        <small class="text-muted"><?php echo htmlspecialchars($quiz['nama_kelas']); ?></small>
                                    </div>
                                    <i class="fas fa-chevron-right text-primary"></i>
                                </a>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>