<?php
session_start();
include_once '../database.php';

// Proteksi halaman, hanya untuk admin
if (!isset($_SESSION['userRole']) || $_SESSION['userRole'] !== 'admin') {
    header("Location: ../login.php?error=2");
    exit();
}

$username = $_SESSION['namaLengkap'] ?? $_SESSION['username'];
$message = '';

$database = new Database();
$db = $database->getConnection();

// --- PROSES TAMBAH SISWA ---
if (isset($_POST['tambah_siswa'])) {
    $nama_lengkap = $_POST['fullName'];
    $username_new = $_POST['username'];
    $password_new = $_POST['password'];
    $kelas_ids = $_POST['kelas_ids'] ?? [];

    if (!empty($nama_lengkap) && !empty($username_new) && !empty($password_new)) {
        // Cek username duplikat
        $stmt_check = $db->prepare("SELECT id FROM pengguna WHERE username = ?");
        $stmt_check->execute([$username_new]);
        if ($stmt_check->rowCount() > 0) {
            $message = '<div class="alert alert-danger">Username sudah ada. Silakan gunakan username lain.</div>';
        } else {
            // Insert ke tabel pengguna
            $hashed_password = password_hash($password_new, PASSWORD_DEFAULT);
            
            // Insert ke tabel pengguna
            $stmt_insert = $db->prepare("INSERT INTO pengguna (nama_lengkap, username, password, role) VALUES (?, ?, ?, 'siswa')");
            if ($stmt_insert->execute([$nama_lengkap, $username_new, $hashed_password])) {
                $siswa_id = $db->lastInsertId();
                // Daftarkan siswa ke kelas yang dipilih
                if (!empty($kelas_ids)) {
                    $stmt_pendaftaran = $db->prepare("INSERT INTO pendaftaran_kelas (siswa_id, kelas_id) VALUES (?, ?)");
                    foreach ($kelas_ids as $kelas_id) {
                        $stmt_pendaftaran->execute([$siswa_id, $kelas_id]);
                    }
                }
                $message = '<div class="alert alert-success">Siswa baru berhasil ditambahkan.</div>';
            } else {
                $message = '<div class="alert alert-danger">Gagal menambahkan siswa.</div>';
            }
        }
    } else {
        $message = '<div class="alert alert-danger">Semua field wajib diisi.</div>';
    }
}

// --- PROSES HAPUS SISWA ---
if (isset($_POST['hapus_siswa'])) {
    $id_siswa = $_POST['id_siswa'];
    $stmt = $db->prepare("DELETE FROM pengguna WHERE id = ? AND role = 'siswa'");
    if ($stmt->execute([$id_siswa])) {
        $message = '<div class="alert alert-success">Siswa berhasil dihapus.</div>';
    } else {
        $message = '<div class="alert alert-danger">Gagal menghapus siswa.</div>';
    }
}

// --- PROSES EDIT SISWA ---
if (isset($_POST['edit_siswa'])) {
    $id_siswa = $_POST['studentId'];
    $nama_lengkap = $_POST['editFullName'];
    $username_edit = $_POST['editUsername'];
    $password_edit = $_POST['editPassword'];
    $kelas_ids_edit = $_POST['edit_kelas_ids'] ?? [];

    $query_update = "UPDATE pengguna SET nama_lengkap = ?, username = ?";
    $params = [$nama_lengkap, $username_edit];

    if (!empty($password_edit)) {
        $query_update .= ", password = ?";
        $params[] = password_hash($password_edit, PASSWORD_DEFAULT);
    }

    $query_update .= " WHERE id = ?";
    $params[] = $id_siswa;

    $stmt = $db->prepare($query_update);
    if ($stmt->execute($params)) {
        // Update pendaftaran kelas: hapus yang lama, masukkan yang baru
        $stmt_delete_pendaftaran = $db->prepare("DELETE FROM pendaftaran_kelas WHERE siswa_id = ?");
        $stmt_delete_pendaftaran->execute([$id_siswa]);

        if (!empty($kelas_ids_edit)) {
            $stmt_pendaftaran = $db->prepare("INSERT INTO pendaftaran_kelas (siswa_id, kelas_id) VALUES (?, ?)");
            foreach ($kelas_ids_edit as $kelas_id) {
                $stmt_pendaftaran->execute([$id_siswa, $kelas_id]);
            }
        }
        $message = '<div class="alert alert-success">Data siswa berhasil diperbarui.</div>';
    } else {
        $message = '<div class="alert alert-danger">Gagal memperbarui data siswa.</div>';
    }
}

// --- AMBIL DATA SISWA UNTUK DITAMPILKAN ---
$query_siswa = "SELECT p.id, p.nama_lengkap, p.username, p.password, GROUP_CONCAT(k.nama_kelas SEPARATOR ', ') as kelas_diikuti, GROUP_CONCAT(k.id) as kelas_ids
                FROM pengguna p
                LEFT JOIN pendaftaran_kelas pk ON p.id = pk.siswa_id
                LEFT JOIN kelas k ON pk.kelas_id = k.id
                WHERE p.role = 'siswa'
                GROUP BY p.id
                ORDER BY p.nama_lengkap";
$stmt_siswa = $db->query($query_siswa);
$students = $stmt_siswa->fetchAll(PDO::FETCH_ASSOC);

// Ambil semua data kelas untuk dropdown
$stmt_kelas_all = $db->query("SELECT id, nama_kelas FROM kelas ORDER BY nama_kelas");
$all_classes = $stmt_kelas_all->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Kelola Siswa - Kelas Online</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
  <link rel="stylesheet" href="../style.css" />
</head>
<body class="admin-theme">
  <nav class="navbar navbar-expand-lg navbar-dark px-4">
    <div class="container">
      <a class="navbar-brand" href="../admin.php">
        <i class="fas fa-school me-2"></i>Kelas Online
      </a>
      <div class="collapse navbar-collapse">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
            <span class="nav-link me-3"><i class="fas fa-user-shield me-1"></i><span><?php echo htmlspecialchars($username); ?></span></span>
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
        <h2>Kelola Siswa</h2>
        <a href="../admin.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Dashboard
        </a>
    </div>
    <?php echo $message; ?>
    <button class="btn btn-primary mb-4" data-bs-toggle="modal" data-bs-target="#addStudentModal">
      <i class="fas fa-plus-circle me-1"></i> Tambah Siswa Baru
    </button>

    <div class="card shadow-sm">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover table-striped">
            <thead>
              <tr>
                <th>No.</th>
                <th>Nama Lengkap</th>
                <th>Username</th>
                <th>Kelas yang Diikuti</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($students)): ?>
                <tr><td colspan="5" class="text-center">Belum ada data siswa.</td></tr>
              <?php else: ?>
                <?php $no = 1; foreach ($students as $student): ?>
                <tr>
                  <td><?php echo $no++; ?></td>
                  <td><?php echo htmlspecialchars($student['nama_lengkap']); ?></td>
                  <td><?php echo htmlspecialchars($student['username']); ?></td>
                  <td><?php echo htmlspecialchars($student['kelas_diikuti'] ?: '-'); ?></td>
                  <td>
                    <button class="btn btn-sm btn-warning me-2" data-bs-toggle="modal" data-bs-target="#editStudentModal"
                            data-id="<?php echo $student['id']; ?>"
                            data-fullname="<?php echo htmlspecialchars($student['nama_lengkap']); ?>"
                            data-username="<?php echo htmlspecialchars($student['username']); ?>"
                            data-kelasids="<?php echo htmlspecialchars($student['kelas_ids']); ?>">
                      <i class="fas fa-edit"></i> Edit
                    </button>
                    <form action="" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus siswa ini?');">
                        <input type="hidden" name="id_siswa" value="<?php echo $student['id']; ?>">
                        <button type="submit" name="hapus_siswa" class="btn btn-sm btn-danger">
                            <i class="fas fa-trash"></i> Hapus
                        </button>
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

  <div class="modal fade" id="addStudentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form method="POST" action="">
            <div class="modal-header">
              <h5 class="modal-title">Tambah Siswa Baru</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                  <label for="fullName" class="form-label">Nama Lengkap</label>
                  <input type="text" class="form-control" name="fullName" required />
                </div>
                <div class="mb-3">
                  <label for="username" class="form-label">Username</label>
                  <input type="text" class="form-control" name="username" required />
                </div>
                <div class="mb-3">
                  <label for="password" class="form-label">Password</label>
                  <input type="password" class="form-control" name="password" required />
                </div>
                <div class="mb-3">
                  <label class="form-label">Daftarkan ke Kelas</label>
                  <select name="kelas_ids[]" class="form-select" multiple size="5">
                    <?php foreach ($all_classes as $class): ?>
                      <option value="<?php echo $class['id']; ?>"><?php echo htmlspecialchars($class['nama_kelas']); ?></option>
                    <?php endforeach; ?>
                  </select>
                  <small class="text-muted">Tahan Ctrl (atau Cmd di Mac) untuk memilih lebih dari satu.</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" name="tambah_siswa" class="btn btn-primary">Simpan Siswa</button>
            </div>
        </form>
      </div>
    </div>
  </div>

  <div class="modal fade" id="editStudentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form method="POST" action="">
            <div class="modal-header">
              <h5 class="modal-title">Edit Data Siswa</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="studentId" id="editStudentId" />
                <div class="mb-3">
                  <label for="editFullName" class="form-label">Nama Lengkap</label>
                  <input type="text" class="form-control" name="editFullName" id="editFullName" required />
                </div>
                <div class="mb-3">
                  <label for="editUsername" class="form-label">Username</label>
                  <input type="text" class="form-control" name="editUsername" id="editUsername" required />
                </div>
                <div class="mb-3">
                  <label for="editPassword" class="form-label">Password Baru</label>
                  <input type="password" class="form-control" name="editPassword" id="editPassword" />
                  <small class="text-muted">Kosongkan jika tidak ingin mengubah password.</small>
                </div>
                 <div class="mb-3">
                  <label class="form-label">Kelas yang Diikuti</label>
                  <select name="edit_kelas_ids[]" id="editKelasIds" class="form-select" multiple size="5">
                    <?php foreach ($all_classes as $class): ?>
                      <option value="<?php echo $class['id']; ?>"><?php echo htmlspecialchars($class['nama_kelas']); ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" name="edit_siswa" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    const editModal = document.getElementById('editStudentModal');
    editModal.addEventListener('show.bs.modal', event => {
        const button = event.relatedTarget;
        const id = button.getAttribute('data-id');
        const fullname = button.getAttribute('data-fullname');
        const username = button.getAttribute('data-username');
        const kelasids = button.getAttribute('data-kelasids');

        const modalStudentId = editModal.querySelector('#editStudentId');
        const modalFullName = editModal.querySelector('#editFullName');
        const modalUsername = editModal.querySelector('#editUsername');
        const modalKelasIdsSelect = editModal.querySelector('#editKelasIds');

        modalStudentId.value = id;
        modalFullName.value = fullname;
        modalUsername.value = username;

        // Reset pilihan sebelumnya
        for (let option of modalKelasIdsSelect.options) {
            option.selected = false;
        }

        // Set pilihan baru berdasarkan data
        if (kelasids) {
            const kelasIdsArray = kelasids.split(',');
            for (let option of modalKelasIdsSelect.options) {
                if (kelasIdsArray.includes(option.value)) {
                    option.selected = true;
                }
            }
        }
    });
  </script>
</body>
</html>