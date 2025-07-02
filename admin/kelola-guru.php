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

// --- PROSES TAMBAH GURU ---
if (isset($_POST['tambah_guru'])) {
    $nama_lengkap = $_POST['teacherName'];
    $username = $_POST['teacherUsername'];
    $password = $_POST['teacherPassword'];

    if (!empty($nama_lengkap) && !empty($username) && !empty($password)) {
        // Cek username duplikat
        $stmt_check = $db->prepare("SELECT id FROM pengguna WHERE username = ?");
        $stmt_check->execute([$username]);
        if ($stmt_check->rowCount() > 0) {
            $message = '<div class="alert alert-danger">Username sudah ada. Silakan gunakan username lain.</div>';
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $db->prepare("INSERT INTO pengguna (nama_lengkap, username, password, role) VALUES (?, ?, ?, 'guru')");
            if ($stmt->execute([$nama_lengkap, $username, $hashed_password])) {
                 $message = '<div class="alert alert-success">Guru baru berhasil ditambahkan.</div>';
            } else {
                $message = '<div class="alert alert-danger">Gagal menambahkan guru baru.</div>';
            }
        }
    } else {
        $message = '<div class="alert alert-danger">Semua field wajib diisi.</div>';
    }
}

// --- PROSES HAPUS GURU ---
if (isset($_POST['hapus_guru'])) {
    $id_guru = $_POST['id_guru'];
    $stmt = $db->prepare("DELETE FROM pengguna WHERE id = ? AND role = 'guru'");
    if ($stmt->execute([$id_guru])) {
        $message = '<div class="alert alert-success">Data guru berhasil dihapus.</div>';
    } else {
        $message = '<div class="alert alert-danger">Gagal menghapus data guru. Pastikan guru tersebut tidak terikat dengan data kelas.</div>';
    }
}

// --- PROSES EDIT GURU ---
if (isset($_POST['edit_guru'])) {
    $id_guru = $_POST['editTeacherId'];
    $nama_lengkap = $_POST['editTeacherName'];
    $username = $_POST['editTeacherUsername'];
    $password = $_POST['editTeacherPassword'];

    $query = "UPDATE pengguna SET nama_lengkap = ?, username = ?";
    $params = [$nama_lengkap, $username];

    if (!empty($password)) {
        $query .= ", password = ?";
        $params[] = password_hash($password, PASSWORD_DEFAULT);
    }

    $query .= " WHERE id = ?";
    $params[] = $id_guru;

    $stmt = $db->prepare($query);
    if ($stmt->execute($params)) {
        $message = '<div class="alert alert-success">Data guru berhasil diperbarui.</div>';
    } else {
        $message = '<div class="alert alert-danger">Gagal memperbarui data guru.</div>';
    }
}


// --- AMBIL DATA GURU UNTUK DITAMPILKAN ---
$query_guru = "SELECT p.id, p.nama_lengkap, p.username, COUNT(k.id) as jumlah_kelas
               FROM pengguna p
               LEFT JOIN kelas k ON p.id = k.guru_id
               WHERE p.role = 'guru'
               GROUP BY p.id
               ORDER BY p.nama_lengkap";
$stmt_guru = $db->query($query_guru);
$teachers = $stmt_guru->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Kelola Guru - Kelas Online</title>
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
            <h2>Kelola Data Guru</h2>
            <a href="../admin.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Dashboard
            </a>
        </div>
        <?php echo $message; ?>
        <div class="row mb-4">
            <div class="col-lg-12">
            <div class="card shadow-sm">
                <div class="card-body">
                <h5 class="card-title mb-4">Tambah Guru Baru</h5>
                <form id="addTeacherForm" method="POST" action="">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="teacherName" class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" name="teacherName" required>
                        </div>
                        <div class="col-md-6">
                            <label for="teacherUsername" class="form-label">Username</label>
                            <input type="text" class="form-control" name="teacherUsername" required>
                        </div>
                        <div class="col-md-6">
                            <label for="teacherPassword" class="form-label">Password</label>
                            <input type="password" class="form-control" name="teacherPassword" required>
                        </div>
                        <div class="col-12">
                            <button type="submit" name="tambah_guru" class="btn btn-primary">
                                <i class="fas fa-plus-circle me-1"></i>Tambah Guru
                            </button>
                        </div>
                    </div>
                </form>
                </div>
            </div>
            </div>
        </div>
        
        <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-4">Daftar Guru</h5>
            <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>Nama</th>
                    <th>Username</th>
                    <th>Jumlah Kelas</th>
                    <th>Aksi</th>
                </tr>
                </thead>
                <tbody>
                <?php if (empty($teachers)): ?>
                    <tr><td colspan="4" class="text-center">Belum ada data guru.</td></tr>
                <?php else: ?>
                    <?php foreach ($teachers as $teacher): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($teacher['nama_lengkap']); ?></td>
                        <td><?php echo htmlspecialchars($teacher['username']); ?></td>
                        <td><?php echo $teacher['jumlah_kelas']; ?></td>
                        <td>
                            <button class="btn btn-sm btn-warning me-1" data-bs-toggle="modal" data-bs-target="#editTeacherModal"
                                    data-id="<?php echo $teacher['id']; ?>"
                                    data-name="<?php echo htmlspecialchars($teacher['nama_lengkap']); ?>"
                                    data-username="<?php echo htmlspecialchars($teacher['username']); ?>">
                            <i class="fas fa-edit"></i> Edit
                            </button>
                            <form action="" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus guru ini? Ini mungkin akan mempengaruhi kelas yang terhubung.');">
                                <input type="hidden" name="id_guru" value="<?php echo $teacher['id']; ?>">
                                <button type="submit" name="hapus_guru" class="btn btn-sm btn-danger">
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

  <div class="modal fade" id="editTeacherModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <form id="editTeacherForm" method="POST" action="">
            <div class="modal-header">
              <h5 class="modal-title">Edit Data Guru</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="editTeacherId" name="editTeacherId">
                <div class="row g-3">
                  <div class="col-md-6">
                    <label for="editTeacherName" class="form-label">Nama Lengkap</label>
                    <input type="text" class="form-control" id="editTeacherName" name="editTeacherName" required>
                  </div>
                  <div class="col-md-6">
                    <label for="editTeacherUsername" class="form-label">Username</label>
                    <input type="text" class="form-control" id="editTeacherUsername" name="editTeacherUsername" required>
                  </div>
                  <div class="col-md-12">
                    <label for="editTeacherPassword" class="form-label">Password Baru (kosongkan jika tidak diubah)</label>
                    <input type="password" class="form-control" id="editTeacherPassword" name="editTeacherPassword">
                  </div>
                </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
              <button type="submit" name="edit_guru" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    const editModal = document.getElementById('editTeacherModal');
    if(editModal) {
        editModal.addEventListener('show.bs.modal', event => {
            const button = event.relatedTarget;
            
            const id = button.getAttribute('data-id');
            const name = button.getAttribute('data-name');
            const username = button.getAttribute('data-username');
            
            const modalId = editModal.querySelector('#editTeacherId');
            const modalName = editModal.querySelector('#editTeacherName');
            const modalUsername = editModal.querySelector('#editTeacherUsername');
            
            modalId.value = id;
            modalName.value = name;
            modalUsername.value = username;
        });
    }
  </script>
</body>
</html>