<?php
// Selalu mulai session di awal
session_start();

// Include file database dan object user
include_once 'database.php'; // Pastikan file ini ada
include_once 'user.php';     // Pastikan file ini ada

// Buat koneksi ke database
$database = new Database();
$db = $database->getConnection();

// Buat instance dari object User
$user = new User($db);

// Cek apakah data dari form sudah di-submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Ambil data dari form login.php
    $user->username = $_POST['username'];
    $user->password = $_POST['password'];
    $user->role     = $_POST['role'];

    // Coba untuk login menggunakan method dari class User
    if ($user->login()) {
        // Jika login berhasil, simpan data ke session
        $_SESSION['userId'] = $user->id;
        $_SESSION['username'] = $user->username;
        $_SESSION['userRole'] = $user->role;
        $_SESSION['namaLengkap'] = $user->nama_lengkap;

        // Arahkan pengguna ke halaman .php yang sesuai
        if ($user->role == 'admin') {
            header("Location: admin.php");
        } elseif ($user->role == 'guru') {
            header("Location: guru.php");
        } elseif ($user->role == 'siswa') {
            header("Location: siswa.php");
        }
        exit(); // Penting untuk menghentikan eksekusi setelah redirect
    } else {
        // Jika login gagal, arahkan kembali ke halaman login dengan pesan error
        header("Location: login.php?error=1");
        exit();
    }
} else {
    // Jika file diakses langsung tanpa POST, redirect ke login
    header("Location: login.php");
    exit();
}
?>