<?php
// Skrip ini hanya untuk dijalankan sekali, lalu hapus file ini.

include_once 'database.php';

echo "<h1>Memulai proses update password...</h1>";

$database = new Database();
$db = $database->getConnection();

try {
    // 1. Ambil semua pengguna yang passwordnya belum di-hash
    // Password hash dari PHP biasanya dimulai dengan '$2y$' atau '$argon2id$' dan panjangnya 60 karakter.
    // Kita cari saja yang panjangnya kurang dari 50 untuk menyederhanakan.
    $stmt = $db->prepare("SELECT id, username, password FROM pengguna WHERE LENGTH(password) < 50");
    $stmt->execute();

    $users_to_update = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($users_to_update)) {
        echo "<p>Tidak ada password yang perlu diupdate. Sepertinya semua sudah di-hash.</p>";
        die();
    }

    echo "<p>Ditemukan " . count($users_to_update) . " pengguna untuk diupdate.</p>";

    // 2. Siapkan statement untuk update
    $stmt_update = $db->prepare("UPDATE pengguna SET password = :password WHERE id = :id");

    $updated_count = 0;

    // 3. Loop melalui setiap pengguna, hash passwordnya, dan update ke database
    foreach ($users_to_update as $user) {
        $id = $user['id'];
        $plain_password = $user['password'];

        // Hash password
        $hashed_password = password_hash($plain_password, PASSWORD_DEFAULT);

        // Update database
        $stmt_update->bindParam(':password', $hashed_password);
        $stmt_update->bindParam(':id', $id);
        $stmt_update->execute();

        echo "Password untuk user '{$user['username']}' berhasil diupdate.<br>";
        $updated_count++;
    }

    echo "<h2>Selesai!</h2>";
    echo "<p>Total {$updated_count} password pengguna telah berhasil di-hash dan diperbarui.</p>";
    echo "<p style='color:red; font-weight:bold;'>PENTING: Hapus file 'update_passwords.php' ini dari server Anda sekarang!</p>";

} catch (PDOException $e) {
    die("Koneksi atau query database gagal: " . $e->getMessage());
}
?>