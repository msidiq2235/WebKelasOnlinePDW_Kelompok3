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

// Tampilkan pesan dari session jika ada setelah redirect
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

// --- PROSES TAMBAH KELAS ---
if (isset($_POST['tambah_kelas'])) {
    $nama_kelas = $_POST['courseName'];
    $deskripsi = $_POST['courseDescription'];

    if (empty($nama_kelas)) {
        $_SESSION['message'] = '<div class="alert alert-danger">Nama kelas tidak boleh kosong.</div>';
    } else {
        $query_tambah = "INSERT INTO kelas (nama_kelas, deskripsi, guru_id) VALUES (:nama_kelas, :deskripsi, :guru_id)";
        $stmt_tambah = $db->prepare($query_tambah);
        $stmt_tambah->bindParam(':nama_kelas', $nama_kelas);
        $stmt_tambah->bindParam(':deskripsi', $deskripsi);
        $stmt_tambah->bindParam(':guru_id', $guru_id);

        if ($stmt_tambah->execute()) {
            $_SESSION['message'] = '<div class="alert alert-success">Kelas baru berhasil ditambahkan.</div>';
        } else {
            $_SESSION['message'] = '<div class="alert alert-danger">Gagal menambahkan kelas baru.</div>';
        }
    }
    header("Location: kelola-kelas.php");
    exit();
}

// --- PROSES EDIT KELAS ---
if (isset($_POST['edit_kelas'])) {
    $id_kelas = $_POST['editCourseId'];
    $nama_kelas = $_POST['editCourseName'];
    $deskripsi = $_POST['editCourseDescription'];

    // Keamanan: Pastikan guru hanya mengedit kelas miliknya
    $stmt = $db->prepare("UPDATE kelas SET nama_kelas = ?, deskripsi = ? WHERE id = ? AND guru_id = ?");
    if ($stmt->execute([$nama_kelas, $deskripsi, $id_kelas, $guru_id])) {
        $_SESSION['message'] = '<div class="alert alert-success">Data kelas berhasil diperbarui.</div>';
    } else {
        $_SESSION['message'] = '<div class="alert alert-danger">Gagal memperbarui data kelas.</div>';
    }
    header("Location: kelola-kelas.php");
    exit();
}

// --- PROSES HAPUS KELAS ---
if (isset($_POST['hapus_kelas'])) {
    $id_kelas = $_POST['id_kelas'];
    
    // Keamanan: Pastikan guru hanya menghapus kelas miliknya
    $stmt = $db->prepare("DELETE FROM kelas WHERE id = ? AND guru_id = ?");
    if ($stmt->execute([$id_kelas, $guru_id])) {
        $_SESSION['message'] = '<div class="alert alert-success">Kelas berhasil dihapus.</div>';
    } else {
        $_SESSION['message'] = '<div class="alert alert-danger">Gagal menghapus kelas.</div>';
    }
    header("Location: kelola-kelas.php");
    exit();
}

// Ambil daftar kelas yang diajar oleh guru ini
$query_kelas = "SELECT id, nama_kelas, deskripsi FROM kelas WHERE guru_id = :guru_id ORDER BY nama_kelas";
$stmt_kelas = $db->prepare($query_kelas);
$stmt_kelas->bindParam(':guru_id', $guru_id);
$stmt_kelas->execute();
$daftar_kelas = $stmt_kelas->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Kelola Kelas - Kelas Online</title>
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

  <div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Kelola Kelas Anda</h2>
        <a href="../guru.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Dashboard
        </a>
    </div>
    <?php echo $message; ?>
    <div class="row">
      <div class="col-md-5 mb-4">
        <div class="card shadow-sm h-100">
          <div class="card-header bg-primary text-white">
            <h5>Tambah Kelas Baru</h5>
          </div>
          <div class="card-body">
            <form id="addCourseForm" method="POST" action="">
              <div class="mb-3">
                <label for="courseName" class="form-label">Nama Kelas</label>
                <input type="text" class="form-control" id="courseName" name="courseName" required>
              </div>
              <div class="mb-3">
                <label for="courseDescription" class="form-label">Deskripsi Singkat</label>
                <textarea class="form-control" id="courseDescription" name="courseDescription" rows="3"></textarea>
              </div>
              <button type="submit" name="tambah_kelas" class="btn btn-primary w-100">Tambah Kelas</button>
            </form>
          </div>
        </div>
      </div>

      <div class="col-md-7 mb-4">
        <div class="card shadow-sm h-100">
          <div class="card-header bg-info text-white">
            <h5>Daftar Kelas Anda</h5>
          </div>
          <div class="card-body">
            <?php if (empty($daftar_kelas)): ?>
                <div class="alert alert-light">Anda belum memiliki kelas. Silakan buat kelas baru.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nama Kelas</th>
                                <th>Deskripsi</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($daftar_kelas as $kelas): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($kelas['nama_kelas']); ?></td>
                                <td><?php echo htmlspecialchars($kelas['deskripsi'] ?: '-'); ?></td>
                                <td>
                                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editCourseModal"
                                            data-id="<?php echo $kelas['id']; ?>"
                                            data-name="<?php echo htmlspecialchars($kelas['nama_kelas']); ?>"
                                            data-description="<?php echo htmlspecialchars($kelas['deskripsi']); ?>">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus kelas ini? Semua materi dan kuis di dalamnya juga akan terhapus.');">
                                        <input type="hidden" name="id_kelas" value="<?php echo $kelas['id']; ?>">
                                        <button type="submit" name="hapus_kelas" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="editCourseModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Kelas</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="editCourseId" id="editCourseId">
                        <div class="mb-3">
                            <label for="editCourseName" class="form-label">Nama Kelas</label>
                            <input type="text" class="form-control" name="editCourseName" id="editCourseName" required>
                        </div>
                        <div class="mb-3">
                            <label for="editCourseDescription" class="form-label">Deskripsi</label>
                            <textarea class="form-control" name="editCourseDescription" id="editCourseDescription" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="edit_kelas" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    const editModal = document.getElementById('editCourseModal');
    if (editModal) {
        editModal.addEventListener('show.bs.modal', event => {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            const name = button.getAttribute('data-name');
            const description = button.getAttribute('data-description');

            editModal.querySelector('#editCourseId').value = id;
            editModal.querySelector('#editCourseName').value = name;
            editModal.querySelector('#editCourseDescription').value = description;
        });
    }
  </script>
</body>
</html>