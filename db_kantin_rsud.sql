-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 13, 2026 at 01:27 PM
-- Server version: 8.0.30
-- PHP Version: 8.3.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_kantin_rsud`
--

-- --------------------------------------------------------

--
-- Table structure for table `absensi`
--

CREATE TABLE `absensi` (
  `id_absen` int NOT NULL,
  `id_user` int DEFAULT NULL,
  `tgl_absen` date NOT NULL,
  `jam_masuk` time NOT NULL,
  `jam_pulang` time DEFAULT NULL,
  `status` enum('Hadir','Izin','Sakit','Alpa') DEFAULT 'Hadir',
  `keterangan` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `barang`
--

CREATE TABLE `barang` (
  `id_barang` int NOT NULL,
  `id_kategori` int DEFAULT NULL,
  `nama_barang` varchar(100) NOT NULL,
  `harga` decimal(10,2) NOT NULL,
  `foto` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `stok` int NOT NULL DEFAULT '0',
  `status` enum('Tersedia','Habis') DEFAULT 'Tersedia'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `barang`
--

INSERT INTO `barang` (`id_barang`, `id_kategori`, `nama_barang`, `harga`, `foto`, `stok`, `status`) VALUES
(6, NULL, 'Nasi Ikan', '15000.00', NULL, 20, 'Tersedia'),
(7, NULL, 'Nasi Ayam', '15000.00', NULL, 20, 'Tersedia'),
(8, NULL, 'Nasi Tahu Tempe', '10000.00', NULL, 20, 'Tersedia'),
(9, NULL, 'Nasi Telur', '12000.00', NULL, 20, 'Tersedia');

-- --------------------------------------------------------

--
-- Table structure for table `detail_transaksi`
--

CREATE TABLE `detail_transaksi` (
  `id_detail` int NOT NULL,
  `id_transaksi` int DEFAULT NULL,
  `id_barang` int DEFAULT NULL,
  `jumlah` int NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `detail_transaksi`
--

INSERT INTO `detail_transaksi` (`id_detail`, `id_transaksi`, `id_barang`, `jumlah`, `subtotal`) VALUES
(3, 1, 7, 1, '15000.00'),
(4, 1, 6, 1, '15000.00'),
(8, 3, 7, 2, '30000.00'),
(10, 3, 9, 1, '12000.00'),
(18, 7, 6, 1, '15000.00'),
(20, 9, 7, 2, '30000.00'),
(21, 10, 8, 1, '10000.00'),
(22, 11, 7, 1, '15000.00'),
(23, 12, 7, 1, '15000.00'),
(24, 12, 6, 1, '15000.00'),
(25, 13, 7, 1, '15000.00'),
(26, 14, 6, 2, '30000.00'),
(27, 14, 7, 1, '15000.00'),
(28, 15, 6, 1, '15000.00'),
(29, 16, 7, 1, '15000.00'),
(30, 17, 8, 1, '10000.00'),
(31, 18, 6, 1, '15000.00'),
(32, 19, 6, 1, '15000.00'),
(33, 20, 6, 1, '15000.00'),
(34, 21, 7, 2, '30000.00'),
(35, 22, 6, 1, '15000.00'),
(36, 22, 7, 1, '15000.00'),
(37, 23, 6, 1, '15000.00'),
(38, 24, 6, 1, '15000.00'),
(39, 25, 7, 1, '15000.00');

-- --------------------------------------------------------

--
-- Table structure for table `kategori`
--

CREATE TABLE `kategori` (
  `id_kategori` int NOT NULL,
  `nama_kategori` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transaksi`
--

CREATE TABLE `transaksi` (
  `id_transaksi` int NOT NULL,
  `id_user` int DEFAULT NULL,
  `tgl_transaksi` datetime DEFAULT CURRENT_TIMESTAMP,
  `total_bayar` decimal(10,2) NOT NULL,
  `jumlah_uang` decimal(10,2) DEFAULT NULL,
  `kembalian` decimal(10,2) DEFAULT NULL,
  `nama_pemesan` varchar(100) NOT NULL,
  `nomor_meja` varchar(10) NOT NULL,
  `metode_pembayaran` enum('TUNAI','QRIS') NOT NULL,
  `status_pesanan` enum('Pending','Diproses','Selesai') NOT NULL DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `transaksi`
--

INSERT INTO `transaksi` (`id_transaksi`, `id_user`, `tgl_transaksi`, `total_bayar`, `jumlah_uang`, `kembalian`, `nama_pemesan`, `nomor_meja`, `metode_pembayaran`, `status_pesanan`) VALUES
(1, NULL, '2026-05-20 13:43:34', '57000.00', NULL, NULL, 'Warda', '2', 'TUNAI', 'Selesai'),
(2, NULL, '2026-05-21 08:14:45', '15000.00', NULL, NULL, 'warda', '2', 'TUNAI', 'Selesai'),
(3, NULL, '2026-05-23 08:31:28', '87000.00', NULL, NULL, 'ginchi', '1', 'QRIS', 'Selesai'),
(4, NULL, '2026-05-24 21:29:46', '30000.00', NULL, NULL, 'Warda', '1', 'TUNAI', 'Selesai'),
(5, NULL, '2026-05-24 21:30:09', '25000.00', NULL, NULL, 'Warda', '02', 'QRIS', 'Selesai'),
(7, NULL, '2026-06-03 16:00:42', '15000.00', NULL, NULL, 'Warda', '02', 'QRIS', 'Selesai'),
(8, NULL, '2026-06-03 16:03:12', '10000.00', NULL, NULL, 'mingyu', '02', 'TUNAI', 'Selesai'),
(9, NULL, '2026-06-03 17:44:41', '30000.00', NULL, NULL, 'warda', '02', 'TUNAI', 'Selesai'),
(10, NULL, '2026-06-03 18:06:56', '10000.00', NULL, NULL, 'ZIZA', '5', 'TUNAI', 'Selesai'),
(11, NULL, '2026-06-13 11:03:27', '15000.00', NULL, NULL, 'mark', '1', 'TUNAI', 'Selesai'),
(12, NULL, '2026-06-13 11:09:08', '30000.00', NULL, NULL, 'rawr', '1', 'TUNAI', 'Selesai'),
(13, NULL, '2026-06-13 11:09:41', '15000.00', NULL, NULL, 'weww', '3', 'TUNAI', 'Selesai'),
(14, NULL, '2026-06-13 11:15:38', '45000.00', NULL, NULL, 'buyy', '2', 'TUNAI', 'Selesai'),
(15, NULL, '2026-06-13 11:17:23', '15000.00', NULL, NULL, 'bruhh', '2', 'TUNAI', 'Selesai'),
(16, NULL, '2026-06-13 11:19:30', '15000.00', NULL, NULL, 'budi', '2', 'TUNAI', 'Selesai'),
(17, NULL, '2026-06-13 11:22:39', '10000.00', NULL, NULL, 'wow', '1', 'TUNAI', 'Selesai'),
(18, NULL, '2026-06-13 11:27:37', '15000.00', NULL, NULL, 'warda', '2', 'TUNAI', 'Selesai'),
(19, NULL, '2026-06-13 11:30:49', '15000.00', NULL, NULL, 'gem', '2', 'TUNAI', 'Selesai'),
(20, NULL, '2026-06-13 11:33:35', '15000.00', NULL, NULL, 'bum', '2', 'TUNAI', 'Selesai'),
(21, NULL, '2026-07-20 08:03:44', '30000.00', NULL, NULL, 'Warda', '01', 'TUNAI', 'Selesai'),
(22, NULL, '2026-08-06 09:46:10', '30000.00', NULL, NULL, 'wardaa', '1', 'QRIS', 'Selesai'),
(23, NULL, '2026-08-06 09:56:37', '15000.00', NULL, NULL, 'ww', '3', 'TUNAI', 'Selesai'),
(24, NULL, '2026-08-06 09:59:55', '15000.00', NULL, NULL, 'frisdaa', '2', 'QRIS', 'Selesai'),
(25, NULL, '2026-10-07 09:05:05', '15000.00', NULL, NULL, 'warda', '1', 'QRIS', 'Selesai');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_user` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) DEFAULT NULL,
  `role` enum('Admin','Kasir','Pemilik') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `username`, `password`, `nama_lengkap`, `role`) VALUES
(1, 'Warda', 'bbad8d72c1fac1d081727158807a8798', 'Wardatul Jana', 'Admin'),
(2, 'Kasir 1', 'c76b063eab50852e7e6b798d52fd5a91', 'Cena Ahmad', 'Kasir'),
(3, 'pengelola', '39b46977d5cf70517745a2f0c1a0ec1f', 'Intan Wahab', 'Pemilik'),
(10, 'Kasir 2', '10c891dd7b56fc792c6aa175c3adb354', NULL, 'Kasir');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `absensi`
--
ALTER TABLE `absensi`
  ADD PRIMARY KEY (`id_absen`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `barang`
--
ALTER TABLE `barang`
  ADD PRIMARY KEY (`id_barang`),
  ADD KEY `id_kategori` (`id_kategori`);

--
-- Indexes for table `detail_transaksi`
--
ALTER TABLE `detail_transaksi`
  ADD PRIMARY KEY (`id_detail`),
  ADD KEY `id_transaksi` (`id_transaksi`),
  ADD KEY `id_barang` (`id_barang`);

--
-- Indexes for table `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id_kategori`);

--
-- Indexes for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id_transaksi`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `absensi`
--
ALTER TABLE `absensi`
  MODIFY `id_absen` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `barang`
--
ALTER TABLE `barang`
  MODIFY `id_barang` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `detail_transaksi`
--
ALTER TABLE `detail_transaksi`
  MODIFY `id_detail` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id_kategori` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id_transaksi` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `absensi`
--
ALTER TABLE `absensi`
  ADD CONSTRAINT `absensi_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE;

--
-- Constraints for table `barang`
--
ALTER TABLE `barang`
  ADD CONSTRAINT `barang_ibfk_1` FOREIGN KEY (`id_kategori`) REFERENCES `kategori` (`id_kategori`) ON DELETE SET NULL;

--
-- Constraints for table `detail_transaksi`
--
ALTER TABLE `detail_transaksi`
  ADD CONSTRAINT `detail_transaksi_ibfk_1` FOREIGN KEY (`id_transaksi`) REFERENCES `transaksi` (`id_transaksi`) ON DELETE CASCADE,
  ADD CONSTRAINT `detail_transaksi_ibfk_2` FOREIGN KEY (`id_barang`) REFERENCES `barang` (`id_barang`) ON DELETE CASCADE;

--
-- Constraints for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD CONSTRAINT `transaksi_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
