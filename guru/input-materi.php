<?php
session_start();
include_once '../database.php';

// Proteksi halaman, hanya untuk guru
if (!isset($_SESSION['userRole']) || $_SESSION['userRole'] !== 'guru') {
    header("Location: ../login.php?error=2");
    exit();
}

$guru_id = $_SESSION['userId'];
$nama_lengkap = $_SESSION['namaLengkap'];

$database = new Database();
$db = $database->getConnection();
$message = '';

// --- LOGIKA PEMROSESAN FORM ---

// 1. Proses Simpan Materi Baru
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan_materi'])) {
    $kelas_id = $_POST['kelas_id'];
    $judul = $_POST['materialTitle'];
    $konten = $_POST['materialContent'];

    if (empty($kelas_id) || empty($judul) || empty($konten)) {
        $message = '<div class="alert alert-danger">Semua field harus diisi.</div>';
    } else {
        // Keamanan: Pastikan guru hanya bisa mengisi materi di kelasnya sendiri
        $query_cek = "SELECT id FROM kelas WHERE id = :kelas_id AND guru_id = :guru_id";
        $stmt_cek = $db->prepare($query_cek);
        $stmt_cek->bindParam(':kelas_id', $kelas_id);
        $stmt_cek->bindParam(':guru_id', $guru_id);
        $stmt_cek->execute();

        if ($stmt_cek->rowCount() > 0) {
            $query_simpan = "INSERT INTO materi (kelas_id, judul, konten) VALUES (:kelas_id, :judul, :konten)";
            $stmt_simpan = $db->prepare($query_simpan);
            $stmt_simpan->bindParam(':kelas_id', $kelas_id);
            $stmt_simpan->bindParam(':judul', $judul);
            $stmt_simpan->bindParam(':konten', $konten);

            if ($stmt_simpan->execute()) {
                $message = '<div class="alert alert-success">Materi berhasil disimpan.</div>';
            } else {
                $message = '<div class="alert alert-danger">Gagal menyimpan materi.</div>';
            }
        } else {
            $message = '<div class="alert alert-danger">Anda tidak memiliki hak akses untuk kelas ini.</div>';
        }
    }
}

// 2. Proses Edit Materi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_materi'])) {
    $materi_id = $_POST['edit_materi_id'];
    $judul = $_POST['editMaterialTitle'];
    $konten = $_POST['editMaterialContent'];

    if (empty($materi_id) || empty($judul) || empty($konten)) {
        $message = '<div class="alert alert-danger">Gagal mengedit, data tidak lengkap.</div>';
    } else {
        // Keamanan: Cek apakah guru ini pemilik materi yang akan diedit
        $query_cek = "SELECT m.id FROM materi m JOIN kelas k ON m.kelas_id = k.id WHERE m.id = :materi_id AND k.guru_id = :guru_id";
        $stmt_cek = $db->prepare($query_cek);
        $stmt_cek->bindParam(':materi_id', $materi_id);
        $stmt_cek->bindParam(':guru_id', $guru_id);
        $stmt_cek->execute();

        if ($stmt_cek->rowCount() > 0) {
            $query_update = "UPDATE materi SET judul = :judul, konten = :konten WHERE id = :materi_id";
            $stmt_update = $db->prepare($query_update);
            $stmt_update->bindParam(':judul', $judul);
            $stmt_update->bindParam(':konten', $konten);
            $stmt_update->bindParam(':materi_id', $materi_id);

            if ($stmt_update->execute()) {
                $message = '<div class="alert alert-success">Materi berhasil diperbarui.</div>';
            } else {
                $message = '<div class="alert alert-danger">Gagal memperbarui materi.</div>';
            }
        } else {
            $message = '<div class="alert alert-danger">Anda tidak memiliki izin untuk mengedit materi ini.</div>';
        }
    }
}

// 3. Proses Hapus Materi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hapus_materi'])) {
    $materi_id = $_POST['materi_id'];
    // Keamanan: Cek apakah guru ini pemilik materi yang akan dihapus
    $query_cek = "SELECT m.id FROM materi m JOIN kelas k ON m.kelas_id = k.id WHERE m.id = :materi_id AND k.guru_id = :guru_id";
    $stmt_cek = $db->prepare($query_cek);
    $stmt_cek->bindParam(':materi_id', $materi_id);
    $stmt_cek->bindParam(':guru_id', $guru_id);
    $stmt_cek->execute();

    if ($stmt_cek->rowCount() > 0) {
        $query_hapus = "DELETE FROM materi WHERE id = :materi_id";
        $stmt_hapus = $db->prepare($query_hapus);
        $stmt_hapus->bindParam(':materi_id', $materi_id);
        if ($stmt_hapus->execute()) {
            $message = '<div class="alert alert-success">Materi berhasil dihapus.</div>';
        } else {
            $message = '<div class="alert alert-danger">Gagal menghapus materi.</div>';
        }
    } else {
         $message = '<div class="alert alert-danger">Anda tidak memiliki izin untuk menghapus materi ini.</div>';
    }
}


// --- PENGAMBILAN DATA UNTUK TAMPILAN ---

// Ambil daftar kelas yang diajar oleh guru ini
$query_kelas = "SELECT id, nama_kelas FROM kelas WHERE guru_id = :guru_id";
$stmt_kelas = $db->prepare($query_kelas);
$stmt_kelas->bindParam(':guru_id', $guru_id);
$stmt_kelas->execute();
$daftar_kelas = $stmt_kelas->fetchAll(PDO::FETCH_ASSOC);

// Ambil daftar materi yang sudah pernah diinput oleh guru ini
$query_materi = "SELECT m.id, m.judul, m.konten, m.created_at, k.nama_kelas 
                 FROM materi m 
                 JOIN kelas k ON m.kelas_id = k.id 
                 WHERE k.guru_id = :guru_id 
                 ORDER BY m.created_at DESC";
$stmt_materi = $db->prepare($query_materi);
$stmt_materi->bindParam(':guru_id', $guru_id);
$stmt_materi->execute();
$daftar_materi = $stmt_materi->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Input & Kelola Materi</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
  <link rel="stylesheet" href="../style.css" />
</head>
<body class="guru-theme">
  <nav class="navbar navbar-expand-lg navbar-dark px-4">
    <div class="container">
      <a class="navbar-brand" href="../guru.php"><i class="fas fa-chalkboard-teacher me-2"></i>Menu Guru</a>
      <div class="collapse navbar-collapse">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
            <span class="nav-link me-3"><i class="fas fa-user me-1"></i><?php echo htmlspecialchars($nama_lengkap); ?></span>
          </li>
          <li class="nav-item">
            <a href="../logout.php" class="btn btn-outline-light"><i class="fas fa-sign-out-alt me-1"></i>Logout</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="container py-5">
    <a href="../guru.php" class="btn btn-secondary mb-4">
        <i class="fas fa-arrow-left me-1"></i> Kembali ke Dashboard
    </a>
    <?php if ($message) echo $message; // Tampilkan pesan feedback di atas ?>

    <div class="card shadow-sm mb-5">
        <div class="card-body p-4">
            <h2 class="text-center mb-4">Input Materi Pembelajaran</h2>
            <form id="materialForm" method="POST" action="">
                <div class="mb-3">
                    <label for="kelas_id" class="form-label">Pilih Kelas</label>
                    <select class="form-select" id="kelas_id" name="kelas_id" required>
                        <option value="" disabled selected>-- Pilih Kelas untuk Materi Ini --</option>
                        <?php foreach ($daftar_kelas as $kelas): ?>
                            <option value="<?php echo $kelas['id']; ?>"><?php echo htmlspecialchars($kelas['nama_kelas']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="materialTitle" class="form-label">Judul Materi</label>
                    <input type="text" class="form-control" id="materialTitle" name="materialTitle" required>
                </div>
                <div class="mb-3">
                    <label for="materialContent" class="form-label">Isi Materi</label>
                    <textarea class="form-control" id="materialContent" name="materialContent" rows="8" required></textarea>
                </div>
                <button type="submit" name="simpan_materi" class="btn btn-primary w-100">Simpan Materi</button>
            </form>
        </div>
    </div>
    
    <div class="card shadow-sm">
        <div class="card-body p-4">
            <h3 class="mb-4">Daftar Materi yang Telah Diinput</h3>
            <?php if (empty($daftar_materi)): ?>
                <div class="alert alert-info">Anda belum menginput materi apapun.</div>
            <?php else: ?>
                <div class="list-group">
                <?php foreach ($daftar_materi as $materi): ?>
                    <div class="list-group-item">
                        <div class="d-flex w-100 justify-content-between">
                            <h5 class="mb-1"><?php echo htmlspecialchars($materi['judul']); ?></h5>
                            <small>Dibuat: <?php echo date('d M Y', strtotime($materi['created_at'])); ?></small>
                        </div>
                        <p class="mb-1" style="white-space: pre-wrap;"><?php echo htmlspecialchars($materi['konten']); ?></p>
                        <small class="text-muted">Kelas: <?php echo htmlspecialchars($materi['nama_kelas']); ?></small>
                        <div class="mt-3">
                            <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editMateriModal"
                                    data-id="<?php echo $materi['id']; ?>"
                                    data-judul="<?php echo htmlspecialchars($materi['judul']); ?>"
                                    data-konten="<?php echo htmlspecialchars($materi['konten']); ?>">
                                <i class="fas fa-edit me-1"></i>Edit
                            </button>
                            <form action="" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus materi ini?');">
                                <input type="hidden" name="materi_id" value="<?php echo $materi['id']; ?>">
                                <button type="submit" name="hapus_materi" class="btn btn-danger btn-sm">
                                    <i class="fas fa-trash me-1"></i>Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
  </div>

  <div class="modal fade" id="editMateriModal" tabindex="-1" aria-labelledby="editMateriModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editMateriModalLabel">Edit Materi</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form method="POST" action="">
            <div class="modal-body">
                <input type="hidden" id="edit_materi_id" name="edit_materi_id">
                <div class="mb-3">
                    <label for="editMaterialTitle" class="form-label">Judul Materi</label>
                    <input type="text" class="form-control" id="editMaterialTitle" name="editMaterialTitle" required>
                </div>
                <div class="mb-3">
                    <label for="editMaterialContent" class="form-label">Isi Materi</label>
                    <textarea class="form-control" id="editMaterialContent" name="editMaterialContent" rows="10" required></textarea>
                </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
              <button type="submit" name="edit_materi" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
      </div>
    </div>
  </div>


  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Script untuk mengisi data ke dalam Modal Edit
    const editMateriModal = document.getElementById('editMateriModal');
    editMateriModal.addEventListener('show.bs.modal', event => {
      // Tombol yang memicu modal
      const button = event.relatedTarget;
      
      // Ekstrak informasi dari atribut data-*
      const id = button.getAttribute('data-id');
      const judul = button.getAttribute('data-judul');
      const konten = button.getAttribute('data-konten');
      
      // Update konten modal
      const modalTitle = editMateriModal.querySelector('.modal-title');
      const modalInputId = editMateriModal.querySelector('#edit_materi_id');
      const modalInputJudul = editMateriModal.querySelector('#editMaterialTitle');
      const modalInputKonten = editMateriModal.querySelector('#editMaterialContent');

      modalTitle.textContent = `Edit Materi: ${judul}`;
      modalInputId.value = id;
      modalInputJudul.value = judul;
      modalInputKonten.value = konten;
    });
  </script>
</body>
</html>