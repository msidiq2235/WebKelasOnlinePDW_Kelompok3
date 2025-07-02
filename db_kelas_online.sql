-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 18, 2025 at 01:30 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Database: `db_kelas_online`
--
CREATE DATABASE IF NOT EXISTS `db_kelas_online` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `db_kelas_online`;

-- --------------------------------------------------------

--
-- Struktur dari tabel `kelas`
--

CREATE TABLE `kelas` (
  `id` int(11) NOT NULL,
  `nama_kelas` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `guru_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kelas`
--

INSERT INTO `kelas` (`id`, `nama_kelas`, `deskripsi`, `guru_id`) VALUES
(1, 'Matematika Dasar X-A', 'Kelas untuk mempelajari konsep dasar matematika.', 2),
(2, 'Bahasa Indonesia X-A', 'Kelas untuk meningkatkan kemampuan berbahasa Indonesia.', 3);

-- --------------------------------------------------------

--
-- Struktur dari tabel `kuis`
--

CREATE TABLE `kuis` (
  `id` int(11) NOT NULL,
  `kelas_id` int(11) NOT NULL,
  `judul_kuis` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kuis`
--

INSERT INTO `kuis` (`id`, `kelas_id`, `judul_kuis`, `created_at`) VALUES
(1, 1, 'Kuis Bilangan Bulat', '2025-06-17 18:29:19'),
(2, 2, 'Kuis Teks Deskripsi', '2025-06-17 18:29:19');

-- --------------------------------------------------------

--
-- Struktur dari tabel `materi`
--

CREATE TABLE `materi` (
  `id` int(11) NOT NULL,
  `kelas_id` int(11) NOT NULL,
  `judul` varchar(255) NOT NULL,
  `konten` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `materi`
--

INSERT INTO `materi` (`id`, `kelas_id`, `judul`, `konten`, `created_at`) VALUES
(1, 1, 'Pengantar Bilangan Bulat', 'Bilangan bulat adalah himpunan bilangan yang mencakup bilangan cacah (0, 1, 2, ...) dan bilangan negatif (-1, -2, -3, ...).', '2025-06-17 18:29:19'),
(2, 1, 'Operasi Hitung Campuran', 'Urutan pengerjaan operasi hitung campuran adalah: Kurung, Pangkat/Akar, Kali/Bagi, Tambah/Kurang.', '2025-06-17 18:29:19'),
(3, 2, 'Teks Deskripsi', 'Teks deskripsi adalah teks yang bertujuan untuk menggambarkan suatu objek, tempat, atau peristiwa secara rinci.', '2025-06-17 18:29:19');

-- --------------------------------------------------------

--
-- Struktur dari tabel `nilai`
--

CREATE TABLE `nilai` (
  `id` int(11) NOT NULL,
  `kuis_id` int(11) NOT NULL,
  `siswa_id` int(11) NOT NULL,
  `nilai` decimal(5,2) NOT NULL,
  `tanggal_pengerjaan` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `nilai`
--

INSERT INTO `nilai` (`id`, `kuis_id`, `siswa_id`, `nilai`, `tanggal_pengerjaan`) VALUES
(1, 1, 4, '85.50', '2025-06-17 18:29:19');

-- --------------------------------------------------------

--
-- Struktur dari tabel `opsi_jawaban`
--

CREATE TABLE `opsi_jawaban` (
  `id` int(11) NOT NULL,
  `pertanyaan_id` int(11) NOT NULL,
  `kode_opsi` char(1) NOT NULL COMMENT 'A, B, C, D',
  `teks_opsi` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `opsi_jawaban`
--

INSERT INTO `opsi_jawaban` (`id`, `pertanyaan_id`, `kode_opsi`, `teks_opsi`) VALUES
(1, 1, 'A', '5'),
(2, 1, 'B', '20'),
(3, 1, 'C', '40'),
(4, 1, 'D', '-5'),
(5, 2, 'A', '-12°C'),
(6, 2, 'B', '8°C'),
(7, 2, 'C', '12°C'),
(8, 2, 'D', '-8°C'),
(9, 3, 'A', '-3, -1, 5, 8'),
(10, 3, 'B', '-1, -3, 5, 8'),
(11, 3, 'C', '5, 8, -1, -3'),
(12, 3, 'D', '8, 5, -1, -3'),
(13, 4, 'A', '16 cm'),
(14, 4, 'B', '32 cm'),
(15, 4, 'C', '64 cm'),
(16, 4, 'D', '24 cm'),
(17, 5, 'A', '1/2'),
(18, 5, 'B', '3/4'),
(19, 5, 'C', '2/3'),
(20, 5, 'D', '6/9'),
(21, 6, 'A', 'Mengajak pembaca untuk melakukan sesuatu'),
(22, 6, 'B', 'Menceritakan sebuah kisah dengan urutan waktu'),
(23, 6, 'C', 'Menggambarkan sebuah objek, tempat, atau suasana secara rinci'),
(24, 6, 'D', 'Menjelaskan langkah-langkah membuat sesuatu'),
(25, 7, 'A', 'Tesis, Argumen, Penegasan Ulang'),
(26, 7, 'B', 'Orientasi, Komplikasi, Resolusi'),
(27, 7, 'C', 'Pernyataan Umum, Penjelasan'),
(28, 7, 'D', 'Identifikasi, Deskripsi Bagian, Simpulan/Kesan'),
(29, 8, 'A', 'Kata Kerja (Verba)'),
(30, 8, 'B', 'Kata Benda (Nomina)'),
(31, 8, 'C', 'Kata Sifat (Adjektiva)'),
(32, 8, 'D', 'Kata Keterangan (Adverbia)'),
(33, 9, 'A', 'Deskripsi Subjektif'),
(34, 9, 'B', 'Deskripsi Spasial'),
(35, 9, 'C', 'Deskripsi Objektif'),
(36, 9, 'D', 'Deskripsi Bagian'),
(37, 10, 'A', 'Melibatkan panca indra (penglihatan, pendengaran, dll)'),
(38, 10, 'B', 'Mengandung argumen untuk meyakinkan pembaca'),
(39, 10, 'C', 'Menjelaskan ciri-ciri objek secara spesifik'),
(40, 10, 'D', 'Membuat pembaca seolah-olah merasakan langsung objeknya');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pendaftaran_kelas`
--

CREATE TABLE `pendaftaran_kelas` (
  `id` int(11) NOT NULL,
  `kelas_id` int(11) NOT NULL,
  `siswa_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pendaftaran_kelas`
--

INSERT INTO `pendaftaran_kelas` (`id`, `kelas_id`, `siswa_id`) VALUES
(1, 1, 4),
(2, 1, 5),
(3, 2, 4),
(4, 2, 6);

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengguna`
--

CREATE TABLE `pengguna` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `role` enum('admin','guru','siswa') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pengguna`
--

INSERT INTO `pengguna` (`id`, `username`, `password`, `nama_lengkap`, `role`, `created_at`) VALUES
(1, 'admin', 'password123', 'Admin Utama', 'admin', '2025-06-17 18:29:19'),
(2, 'budi_guru', 'password123', 'Budi Santoso, S.Pd.', 'guru', '2025-06-17 18:29:19'),
(3, 'citra_guru', 'password123', 'Citra Lestari, M.Kom.', 'guru', '2025-06-17 18:29:19'),
(4, 'ahmad_siswa', 'password123', 'Ahmad Yani', 'siswa', '2025-06-17 18:29:19'),
(5, 'bella_siswa', 'password123', 'Bella Swan', 'siswa', '2025-06-17 18:29:19'),
(6, 'chandra_siswa', 'password123', 'Chandra Gupta', 'siswa', '2025-06-17 18:29:19');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pertanyaan`
--

CREATE TABLE `pertanyaan` (
  `id` int(11) NOT NULL,
  `kuis_id` int(11) NOT NULL,
  `teks_pertanyaan` text NOT NULL,
  `kunci_jawaban` char(1) NOT NULL COMMENT 'Contoh: A, B, C, atau D'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pertanyaan`
--

INSERT INTO `pertanyaan` (`id`, `kuis_id`, `teks_pertanyaan`, `kunci_jawaban`) VALUES
(1, 1, 'Hasil dari 15 + (-5) x 2 adalah...', 'A'),
(2, 1, 'Suhu sebuah ruangan awalnya adalah -2°C. Jika suhu ruangan tersebut naik sebesar 10°C, maka suhu ruangan sekarang adalah...', 'B'),
(3, 1, 'Urutan bilangan -1, 5, -3, 8 dari yang terbesar adalah...', 'D'),
(4, 1, 'Sebuah persegi memiliki panjang sisi 8 cm. Keliling persegi tersebut adalah...', 'B'),
(5, 1, 'Bentuk pecahan paling sederhana dari 12/18 adalah...', 'C'),
(6, 2, 'Tujuan utama dari sebuah teks deskripsi adalah...', 'C'),
(7, 2, 'Struktur teks deskripsi yang paling umum dan benar adalah...', 'D'),
(8, 2, 'Kata-kata seperti ''indah'', ''tinggi'', ''merdu'' yang sering digunakan dalam teks deskripsi termasuk dalam jenis kata...', 'C'),
(9, 2, 'Paragraf yang menggambarkan ciri-ciri fisik suatu objek secara terperinci disebut...', 'D'),
(10, 2, 'Berikut ini yang BUKAN merupakan ciri dari teks deskripsi adalah...', 'B');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `kelas`
--
ALTER TABLE `kelas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `guru_id` (`guru_id`);

--
-- Indeks untuk tabel `kuis`
--
ALTER TABLE `kuis`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kelas_id` (`kelas_id`);

--
-- Indeks untuk tabel `materi`
--
ALTER TABLE `materi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kelas_id` (`kelas_id`);

--
-- Indeks untuk tabel `nilai`
--
ALTER TABLE `nilai`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kuis_id` (`kuis_id`),
  ADD KEY `siswa_id` (`siswa_id`);

--
-- Indeks untuk tabel `opsi_jawaban`
--
ALTER TABLE `opsi_jawaban`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pertanyaan_id` (`pertanyaan_id`);

--
-- Indeks untuk tabel `pendaftaran_kelas`
--
ALTER TABLE `pendaftaran_kelas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kelas_id` (`kelas_id`),
  ADD KEY `siswa_id` (`siswa_id`);

--
-- Indeks untuk tabel `pengguna`
--
ALTER TABLE `pengguna`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indeks untuk tabel `pertanyaan`
--
ALTER TABLE `pertanyaan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kuis_id` (`kuis_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT untuk tabel `kelas`
--
ALTER TABLE `kelas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `kuis`
--
ALTER TABLE `kuis`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `materi`
--
ALTER TABLE `materi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `nilai`
--
ALTER TABLE `nilai`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `opsi_jawaban`
--
ALTER TABLE `opsi_jawaban`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT untuk tabel `pendaftaran_kelas`
--
ALTER TABLE `pendaftaran_kelas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `pengguna`
--
ALTER TABLE `pengguna`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `pertanyaan`
--
ALTER TABLE `pertanyaan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Ketidakleluasaan untuk tabel `kelas`
--
ALTER TABLE `kelas`
  ADD CONSTRAINT `fk_kelas_guru` FOREIGN KEY (`guru_id`) REFERENCES `pengguna` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `kuis`
--
ALTER TABLE `kuis`
  ADD CONSTRAINT `fk_kuis_kelas` FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `materi`
--
ALTER TABLE `materi`
  ADD CONSTRAINT `fk_materi_kelas` FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `nilai`
--
ALTER TABLE `nilai`
  ADD CONSTRAINT `fk_nilai_kuis` FOREIGN KEY (`kuis_id`) REFERENCES `kuis` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_nilai_siswa` FOREIGN KEY (`siswa_id`) REFERENCES `pengguna` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `opsi_jawaban`
--
ALTER TABLE `opsi_jawaban`
  ADD CONSTRAINT `fk_opsi_pertanyaan` FOREIGN KEY (`pertanyaan_id`) REFERENCES `pertanyaan` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `pendaftaran_kelas`
--
ALTER TABLE `pendaftaran_kelas`
  ADD CONSTRAINT `fk_pendaftaran_kelas` FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_pendaftaran_siswa` FOREIGN KEY (`siswa_id`) REFERENCES `pengguna` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `pertanyaan`
--
ALTER TABLE `pertanyaan`
  ADD CONSTRAINT `fk_pertanyaan_kuis` FOREIGN KEY (`kuis_id`) REFERENCES `kuis` (`id`) ON DELETE CASCADE;
COMMIT;