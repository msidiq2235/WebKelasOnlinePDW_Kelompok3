<?php
session_start();
include_once 'database.php';

// Cek jika metode request adalah POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validasi dasar: Cek apakah ada field yang kosong
    if (empty($_POST['nama_lengkap']) || empty($_POST['username']) || empty($_POST['password']) || empty($_POST['password_konfirmasi']) || empty($_POST['role'])) {
        header("Location: register.php?error=empty_fields");
        exit();
    }

    $nama_lengkap = $_POST['nama_lengkap'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $password_konfirmasi = $_POST['password_konfirmasi'];
    $role = $_POST['role'];

    // Validasi: Password harus cocok
    if ($password !== $password_konfirmasi) {
        header("Location: register.php?error=passwords_not_match");
        exit();
    }
    
    // Hubungkan ke database
    $database = new Database();
    $db = $database->getConnection();

    // Validasi: Cek apakah username sudah ada
    try {
        $query_check = "SELECT id FROM pengguna WHERE username = :username LIMIT 1";
        $stmt_check = $db->prepare($query_check);
        $stmt_check->bindParam(':username', $username);
        $stmt_check->execute();

        if ($stmt_check->rowCount() > 0) {
            // Username sudah ada
            header("Location: register.php?error=username_exists");
            exit();
        }

        // --- PERUBAHAN DIMULAI DI SINI ---
        // Hash password sebelum disimpan
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        // --- AKHIR PERUBAHAN ---

        // Jika username belum ada, lanjutkan proses insert
        $query_insert = "INSERT INTO pengguna (nama_lengkap, username, password, role) VALUES (:nama_lengkap, :username, :password, :role)";
        $stmt_insert = $db->prepare($query_insert);
        
        // Binding parameters
        $stmt_insert->bindParam(':nama_lengkap', $nama_lengkap);
        $stmt_insert->bindParam(':username', $username);
        // --- PERUBAHAN DIMULAI DI SINI ---
        $stmt_insert->bindParam(':password', $hashed_password); // Gunakan password yang sudah di-hash
        // --- AKHIR PERUBAHAN ---
        $stmt_insert->bindParam(':role', $role);
        
        // Eksekusi query
        if ($stmt_insert->execute()) {
            
            // ... (sisa kode tidak berubah)
            // Logika untuk mendaftarkan siswa baru ke kelas
            if ($role === 'siswa') {
                $siswa_id_baru = $db->lastInsertId();
                $kelas_ids_untuk_didaftarkan = [1, 2];
                $query_pendaftaran = "INSERT INTO pendaftaran_kelas (siswa_id, kelas_id) VALUES (:siswa_id, :kelas_id)";
                $stmt_pendaftaran = $db->prepare($query_pendaftaran);
                foreach ($kelas_ids_untuk_didaftarkan as $kelas_id) {
                    $stmt_pendaftaran->bindParam(':siswa_id', $siswa_id_baru);
                    $stmt_pendaftaran->bindParam(':kelas_id', $kelas_id);
                    $stmt_pendaftaran->execute(); 
                }
            }
            
            // Registrasi berhasil
            header("Location: login.php?register=success");
            exit();

        } else {
            // Gagal insert ke database
            header("Location: register.php?error=db_error");
            exit();
        }

    } catch (PDOException $e) {
        header("Location: register.php?error=db_error");
        exit();
    }

} else {
    // Jika file diakses langsung tanpa POST, redirect ke halaman registrasi
    header("Location: register.php");
    exit();
}
?>