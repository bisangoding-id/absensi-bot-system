-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Waktu pembuatan: 14 Jan 2026 pada 20.03
-- Versi server: 9.5.0
-- Versi PHP: 8.4.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Basis data: `absensi_chatbot`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `absensi`
--

CREATE TABLE `absensi` (
  `id` int NOT NULL,
  `nik` varchar(20) NOT NULL,
  `tanggal` date NOT NULL,
  `jam_masuk` time DEFAULT NULL,
  `jam_keluar` time DEFAULT NULL,
  `lokasi` varchar(100) DEFAULT NULL,
  `jarak` decimal(8,2) DEFAULT NULL,
  `keterangan` enum('hadir','sakit','izin','tanpa keterangan') NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `karyawan`
--

CREATE TABLE `karyawan` (
  `id` int NOT NULL,
  `nik` varchar(20) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `no_hp` varchar(20) NOT NULL,
  `telegram_id` varchar(50) DEFAULT NULL,
  `status` enum('aktif','nonaktif') DEFAULT 'aktif',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `karyawan`
--

INSERT INTO `karyawan` (`id`, `nik`, `nama`, `no_hp`, `telegram_id`, `status`, `created_at`) VALUES
(4, '2025006', 'Dewi Lestarii', '6281234567006', NULL, 'aktif', '2025-12-22 11:55:24'),
(6, '2025008', 'Farida Utami', '6281234567008', NULL, 'aktif', '2025-12-22 11:55:24'),
(7, '2025009', 'Gilang Ramadhan', '6281234567009', NULL, 'aktif', '2025-12-22 11:55:24'),
(8, '2025010', 'Hani Safitri', '6281234567010', NULL, 'aktif', '2025-12-22 11:55:24'),
(9, '2025011', 'Indra Wijaya', '6281234567011', NULL, 'aktif', '2025-12-22 11:55:24'),
(10, '2025012', 'Joko Susilo', '6281234567012', NULL, 'aktif', '2025-12-22 11:55:24'),
(11, '2025013', 'Kartika Sari', '6281234567013', NULL, 'aktif', '2025-12-22 11:55:24'),
(13, '2025015', 'Maya Indah', '6281234567015', NULL, 'aktif', '2025-12-22 11:55:24'),
(14, '2025016', 'Nugroho Aditama', '6281234567016', NULL, 'aktif', '2025-12-22 11:55:24'),
(15, '2025017', 'Olivia Putri', '6281234567017', NULL, 'aktif', '2025-12-22 11:55:24'),
(16, '2025018', 'Panji Asmoro', '6281234567018', NULL, 'aktif', '2025-12-22 11:55:24'),
(17, '2025019', 'Qori Aina', '6281234567019', NULL, 'aktif', '2025-12-22 11:55:24'),
(18, '2025020', 'Rendy Septian', '6281234567020', NULL, 'aktif', '2025-12-22 11:55:24'),
(19, '2025021', 'Siska Amelia', '6281234567021', NULL, 'aktif', '2025-12-22 11:55:24'),
(20, '2025022', 'Taufik Hidayat', '6281234567022', NULL, 'aktif', '2025-12-22 11:55:24'),
(21, '2025023', 'Umar bin Khattab', '6281234567023', NULL, 'aktif', '2025-12-22 11:55:24'),
(22, '2025024', 'Vina Panduwinata', '6281234567024', NULL, 'aktif', '2025-12-22 11:55:24'),
(23, '2025025', 'Wahyu Hidayat', '6281234567025', NULL, 'aktif', '2025-12-22 11:55:24'),
(24, '2025026', 'Xena Gabriella', '6281234567026', NULL, 'aktif', '2025-12-22 11:55:24'),
(25, '2025027', 'Yayan Ruhian', '6281234567027', NULL, 'aktif', '2025-12-22 11:55:24'),
(27, '2025029', 'Adi Nugroho', '6281234567029', NULL, 'aktif', '2025-12-22 11:55:24'),
(28, '2025030', 'Bella Shofie', '6281234567030', NULL, 'aktif', '2025-12-22 11:55:24'),
(29, '2025031', 'Candra Wijaya', '6281234567031', NULL, 'aktif', '2025-12-22 11:55:24'),
(30, '2025032', 'Dian Sastro', '6281234567032', NULL, 'aktif', '2025-12-22 11:55:24'),
(31, '2025033', 'Erick Thohir', '6281234567033', NULL, 'aktif', '2025-12-22 11:55:24'),
(32, '2025034', 'Fero Walandouw', '6281234567034', NULL, 'aktif', '2025-12-22 11:55:24'),
(33, '2025035', 'Gading Marten', '6281234567035', NULL, 'aktif', '2025-12-22 11:55:24'),
(34, '2025036', 'Hesti Purwadinata', '6281234567036', NULL, 'aktif', '2025-12-22 11:55:24'),
(35, '2025037', 'Irfan Hakim', '6281234567037', NULL, 'aktif', '2025-12-22 11:55:24'),
(36, '2025038', 'Jessica Mila', '6281234567038', NULL, 'aktif', '2025-12-22 11:55:24'),
(37, '2025039', 'Kevin Julio', '6281234567039', NULL, 'aktif', '2025-12-22 11:55:24'),
(38, '2025040', 'Luna Maya', '6281234567040', NULL, 'aktif', '2025-12-22 11:55:24'),
(39, '2025041', 'Morgan Oey', '6281234567041', NULL, 'aktif', '2025-12-22 11:55:24'),
(40, '2025042', 'Nikita Willy', '6281234567042', NULL, 'aktif', '2025-12-22 11:55:24'),
(41, '2025043', 'Onadio Leonardo', '6281234567043', NULL, 'aktif', '2025-12-22 11:55:24'),
(42, '2025044', 'Pevita Pearce', '6281234567044', NULL, 'aktif', '2025-12-22 11:55:24'),
(44, '2025046', 'Sule Prikitiw', '6281234567046', NULL, 'aktif', '2025-12-22 11:55:24'),
(45, '2025047', 'Tukul Arwana', '6281234567047', NULL, 'aktif', '2025-12-22 11:55:24'),
(46, '2025048', 'Uus Rizky', '6281234567048', NULL, 'aktif', '2025-12-22 11:55:24'),
(47, '2025049', 'Vicky Prasetyo', '6281234567049', NULL, 'aktif', '2025-12-22 11:55:24'),
(48, '2025050', 'Wendi Cagur', '6281234567050', NULL, 'aktif', '2025-12-22 11:55:24'),
(49, '2025051', 'Yuni Shara', '6281234567051', NULL, 'aktif', '2025-12-22 11:55:24'),
(52, '2025052', 'Asep Supriatana', '628561000689', NULL, 'aktif', '2025-12-23 12:14:41'),
(99, '2025101', 'Ikmal', '628129999882', NULL, 'aktif', '2025-12-28 05:13:11'),
(100, '2025102', 'Maulana', '628129999883', NULL, 'aktif', '2025-12-28 05:13:11'),
(101, '2025103', 'Kang Ikmal', '628129999884', NULL, 'aktif', '2025-12-28 05:13:11'),
(102, '2025104', 'Kang Maulana', '628129999885', NULL, 'aktif', '2025-12-28 05:13:11'),
(103, '2025105', 'Kang IM', '628129999886', NULL, 'aktif', '2025-12-28 05:13:11');

-- --------------------------------------------------------

--
-- Struktur dari tabel `sistem`
--

CREATE TABLE `sistem` (
  `id` int NOT NULL,
  `webhook_url` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `whatsapp_token` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `telegram_token` varchar(100) NOT NULL,
  `autorecord` varchar(20) NOT NULL,
  `time` varchar(20) NOT NULL,
  `latitude` varchar(100) NOT NULL,
  `longitude` varchar(100) NOT NULL,
  `wa_status` enum('ON','OFF') DEFAULT 'ON',
  `tg_status` enum('ON','OFF') DEFAULT 'ON'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `sistem`
--

INSERT INTO `sistem` (`id`, `webhook_url`, `whatsapp_token`, `telegram_token`, `autorecord`, `time`, `latitude`, `longitude`, `wa_status`, `tg_status`) VALUES
(1, '', '', '', 'OFF', '13:00', '-6.3015101527313995', '107.30374516204748', 'ON', 'ON');

-- --------------------------------------------------------

--
-- Struktur dari tabel `state`
--

CREATE TABLE `state` (
  `id` int NOT NULL,
  `no_hp` varchar(20) NOT NULL,
  `tanggal` date NOT NULL,
  `step` enum('minta_lokasi_masuk') NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `name` varchar(50) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `name`, `username`, `password`) VALUES
(1, 'Administrator', 'admin', '$2y$12$PSafMooZWKYjDas8A2ZDqeaCZnsJ.97u3oV2ihFw1MRPJfkehWi.O');

--
-- Indeks untuk tabel yang dibuang
--

--
-- Indeks untuk tabel `absensi`
--
ALTER TABLE `absensi`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_absen` (`nik`,`tanggal`),
  ADD KEY `idx_tanggal` (`tanggal`),
  ADD KEY `idx_nik` (`nik`);

--
-- Indeks untuk tabel `karyawan`
--
ALTER TABLE `karyawan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_nik` (`nik`),
  ADD UNIQUE KEY `uk_no_hp` (`no_hp`),
  ADD UNIQUE KEY `telegram_id` (`telegram_id`);

--
-- Indeks untuk tabel `sistem`
--
ALTER TABLE `sistem`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `state`
--
ALTER TABLE `state`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_state` (`no_hp`,`tanggal`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `absensi`
--
ALTER TABLE `absensi`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `karyawan`
--
ALTER TABLE `karyawan`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=104;

--
-- AUTO_INCREMENT untuk tabel `state`
--
ALTER TABLE `state`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=127;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
