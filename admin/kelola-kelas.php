<?php
session_start();
include_once '../database.php';

// Proteksi halaman, hanya untuk admin
if (!isset($_SESSION['userRole']) || $_SESSION['userRole'] !== 'admin') {
    header("Location: ../login.php?error=2");
    exit();
}

$admin_username = $_SESSION['namaLengkap'] ?? $_SESSION['username'];
$message = '';

$database = new Database();
$db = $database->getConnection();

// --- Tampilkan pesan dari session jika ada ---
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

// --- PROSES TAMBAH KELAS ---
if (isset($_POST['tambah_kelas'])) {
    $nama_kelas = $_POST['courseName'];
    $deskripsi = $_POST['courseDescription'];
    $guru_id = $_POST['teacher_id'];

    if (!empty($nama_kelas) && !empty($guru_id)) {
        $stmt = $db->prepare("INSERT INTO kelas (nama_kelas, deskripsi, guru_id) VALUES (?, ?, ?)");
        if ($stmt->execute([$nama_kelas, $deskripsi, $guru_id])) {
            $_SESSION['message'] = '<div class="alert alert-success">Kelas baru berhasil ditambahkan.</div>';
        } else {
            $_SESSION['message'] = '<div class="alert alert-danger">Gagal menambahkan kelas.</div>';
        }
    } else {
        $_SESSION['message'] = '<div class="alert alert-danger">Nama kelas dan guru pengampu wajib diisi.</div>';
    }
    header("Location: kelola-kelas.php");
    exit();
}

// --- PROSES HAPUS KELAS ---
if (isset($_POST['hapus_kelas'])) {
    $id_kelas = $_POST['id_kelas'];
    $stmt = $db->prepare("DELETE FROM kelas WHERE id = ?");
    if ($stmt->execute([$id_kelas])) {
        $_SESSION['message'] = '<div class="alert alert-success">Kelas berhasil dihapus.</div>';
    } else {
        $_SESSION['message'] = '<div class="alert alert-danger">Gagal menghapus kelas.</div>';
    }
    header("Location: kelola-kelas.php");
    exit();
}

// --- PROSES EDIT KELAS ---
if (isset($_POST['edit_kelas'])) {
    $id_kelas = $_POST['editCourseId'];
    $nama_kelas = $_POST['editCourseName'];
    $deskripsi = $_POST['editCourseDescription'];
    $guru_id = $_POST['edit_teacher_id'];

    $stmt = $db->prepare("UPDATE kelas SET nama_kelas = ?, deskripsi = ?, guru_id = ? WHERE id = ?");
    if ($stmt->execute([$nama_kelas, $deskripsi, $guru_id, $id_kelas])) {
        $_SESSION['message'] = '<div class="alert alert-success">Data kelas berhasil diperbarui.</div>';
    } else {
        $_SESSION['message'] = '<div class="alert alert-danger">Gagal memperbarui data kelas.</div>';
    }
    header("Location: kelola-kelas.php");
    exit();
}


// --- AMBIL DATA UNTUK TAMPILAN ---
// Ambil daftar semua kelas dengan nama gurunya
$query_kelas = "SELECT k.id, k.nama_kelas, k.deskripsi, k.guru_id, p.nama_lengkap as nama_guru
                FROM kelas k
                JOIN pengguna p ON k.guru_id = p.id
                ORDER BY k.nama_kelas";
$stmt_kelas = $db->query($query_kelas);
$classes = $stmt_kelas->fetchAll(PDO::FETCH_ASSOC);

// Ambil daftar semua guru untuk dropdown
$query_guru = "SELECT id, nama_lengkap FROM pengguna WHERE role = 'guru' ORDER BY nama_lengkap";
$stmt_guru = $db->query($query_guru);
$teachers = $stmt_guru->fetchAll(PDO::FETCH_ASSOC);

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
<body class="admin-theme">
  <nav class="navbar navbar-expand-lg navbar-dark px-4">
    <div class="container">
      <a class="navbar-brand" href="../admin.php"><i class="fas fa-school me-2"></i>Kelas Online</a>
      <div class="collapse navbar-collapse">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
            <span class="nav-link me-3"><i class="fas fa-user-shield me-1"></i><span><?php echo htmlspecialchars($admin_username); ?></span></span>
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
        <h2>Kelola Data Kelas</h2>
        <a href="../admin.php" class="btn btn-secondary">
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
                <input type="text" class="form-control" name="courseName" required>
              </div>
              <div class="mb-3">
                <label for="courseDescription" class="form-label">Deskripsi</label>
                <textarea class="form-control" name="courseDescription" rows="3"></textarea>
              </div>
              <div class="mb-3">
                <label for="teacher_id" class="form-label">Guru Pengampu</label>
                <select name="teacher_id" class="form-select" required>
                    <option value="" disabled selected>-- Pilih Guru --</option>
                    <?php foreach ($teachers as $teacher): ?>
                        <option value="<?php echo $teacher['id']; ?>"><?php echo htmlspecialchars($teacher['nama_lengkap']); ?></option>
                    <?php endforeach; ?>
                </select>
              </div>
              <button type="submit" name="tambah_kelas" class="btn btn-primary w-100">Tambah Kelas</button>
            </form>
          </div>
        </div>
      </div>

      <div class="col-md-7 mb-4">
        <div class="card shadow-sm h-100">
          <div class="card-header bg-info text-white">
            <h5>Daftar Kelas</h5>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th>Nama Kelas</th>
                    <th>Guru</th>
                    <th>Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (empty($classes)): ?>
                    <tr><td colspan="3" class="text-center">Belum ada data kelas.</td></tr>
                  <?php else: ?>
                    <?php foreach ($classes as $class): ?>
                      <tr>
                        <td>
                          <strong><?php echo htmlspecialchars($class['nama_kelas']); ?></strong><br>
                          <small class="text-muted"><?php echo htmlspecialchars($class['deskripsi']); ?></small>
                        </td>
                        <td><?php echo htmlspecialchars($class['nama_guru']); ?></td>
                        <td>
                          <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editCourseModal"
                                  data-id="<?php echo $class['id']; ?>"
                                  data-name="<?php echo htmlspecialchars($class['nama_kelas']); ?>"
                                  data-description="<?php echo htmlspecialchars($class['deskripsi']); ?>"
                                  data-teacherid="<?php echo $class['guru_id']; ?>">
                            <i class="fas fa-edit"></i>
                          </button>
                          <form action="" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus kelas ini?');">
                            <input type="hidden" name="id_kelas" value="<?php echo $class['id']; ?>">
                            <button type="submit" name="hapus_kelas" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                          </form>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
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
                        <div class="mb-3">
                            <label for="edit_teacher_id" class="form-label">Guru Pengampu</label>
                            <select name="edit_teacher_id" id="editTeacherId" class="form-select" required>
                                <option value="" disabled>-- Pilih Guru --</option>
                                <?php foreach ($teachers as $teacher): ?>
                                    <option value="<?php echo $teacher['id']; ?>"><?php echo htmlspecialchars($teacher['nama_lengkap']); ?></option>
                                <?php endforeach; ?>
                            </select>
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
            const teacherId = button.getAttribute('data-teacherid');

            editModal.querySelector('#editCourseId').value = id;
            editModal.querySelector('#editCourseName').value = name;
            editModal.querySelector('#editCourseDescription').value = description;
            editModal.querySelector('#editTeacherId').value = teacherId;
        });
    }
  </script>
</body>
</html>