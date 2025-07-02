<?php
$host = "localhost";
$user = "root"; // User default XAMPP/Laragon
$pass = "";     // Password default XAMPP/Laragon kosong
$db   = "kelas_online"; // Sesuaikan dengan nama database Anda

$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>