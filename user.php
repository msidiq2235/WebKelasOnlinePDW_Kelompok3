<?php
class User {
    // Koneksi database dan nama tabel
    private $conn;
    private $table_name = "pengguna"; // Sesuai dengan nama tabel di database

    // Properti Objek User
    public $id;
    public $username;
    public $password;
    public $role;
    public $nama_lengkap;

    // Constructor dengan $db sebagai koneksi database
    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Fungsi untuk login pengguna.
     * Fungsi ini akan memeriksa username, password, dan peran (role) dari form login.
     */
    function login() {
        // Query untuk mencari user berdasarkan username dan role
        $query = "SELECT id, username, password, nama_lengkap, role 
                  FROM " . $this->table_name . " 
                  WHERE username = :username AND role = :role 
                  LIMIT 1";

        // Mempersiapkan statement query
        $stmt = $this->conn->prepare($query);

        // Membersihkan data (sanitasi)
        $this->username = htmlspecialchars(strip_tags($this->username));
        $this->role = htmlspecialchars(strip_tags($this->role));

        // Mengikat (bind) nilai
        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':role', $this->role);

        // Eksekusi query
        $stmt->execute();

        // Cek jika user ditemukan
        if ($stmt->rowCount() > 0) {
            // Ambil detail user
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            // --- PERUBAHAN DIMULAI DI SINI ---
            // Verifikasi password menggunakan password_verify()
            if (password_verify($this->password, $row['password'])) {
                $this->id = $row['id'];
                $this->nama_lengkap = $row['nama_lengkap'];
                $this->role = $row['role'];
                return true; // Login berhasil
            }
            // --- AKHIR PERUBAHAN ---
        }

        return false; // Login gagal
    }

    /**
     * Fungsi untuk menghitung total pengguna, guru, atau siswa.
     * Digunakan untuk menampilkan statistik di dashboard admin dan guru.
     * @param string $role Opsional. Jika diisi ('guru' atau 'siswa'), akan menghitung berdasarkan peran.
     * @return int Jumlah pengguna.
     */
    function countByRole($role = "") {
        if (!empty($role)) {
            $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " WHERE role = :role";
        } else {
            // Jika role kosong, hitung semua pengguna
            $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
        }

        $stmt = $this->conn->prepare($query);

        if (!empty($role)) {
            $stmt->bindParam(':role', $role);
        }

        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row['total'];
    }
}
?>