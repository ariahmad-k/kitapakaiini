-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 07, 2025 at 05:22 PM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `asu`
--

-- --------------------------------------------------------

--
-- Table structure for table `detail_pesanan`
--

CREATE TABLE `detail_pesanan` (
  `id_detail` bigint NOT NULL,
  `id_pesanan` varchar(20) NOT NULL,
  `id_produk` varchar(20) NOT NULL,
  `jumlah` int NOT NULL,
  `harga_saat_transaksi` decimal(12,2) NOT NULL,
  `sub_total` decimal(12,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `detail_pesanan`
--

INSERT INTO `detail_pesanan` (`id_detail`, `id_pesanan`, `id_produk`, `jumlah`, `harga_saat_transaksi`, `sub_total`) VALUES
(3, 'PSK-20250607170438', 'KS001', 2, 3000.00, 6000.00),
(4, 'PSK-20250607170438', 'KS002', 2, 3000.00, 6000.00),
(5, 'PSK-20250607170438', 'DK001', 3, 5000.00, 15000.00),
(6, 'PSK-20250607170438', 'DK002', 2, 5500.00, 11000.00),
(7, 'PSK-20250607170500', 'KS001', 1, 3000.00, 3000.00),
(8, 'PSK-20250607170500', 'KS002', 1, 3000.00, 3000.00),
(9, 'PSK-20250607170500', 'KB001', 3, 3000.00, 9000.00),
(10, 'PSK-20250607170500', 'DK002', 6, 5500.00, 33000.00),
(11, 'PSK-20250607170500', 'KB003', 6, 5000.00, 30000.00),
(12, 'PSK-20250607170500', 'OT002', 2, 8000.00, 16000.00),
(13, 'PSK-20250607170500', 'KB002', 1, 4000.00, 4000.00),
(14, 'PSK-20250607170524', 'OT001', 10, 10000.00, 100000.00),
(15, 'PSK-20250607170538', 'KS003', 1, 5000.00, 5000.00);

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `id_feedback` int NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `pesan` text NOT NULL,
  `tanggal` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `karyawan`
--

CREATE TABLE `karyawan` (
  `id_karyawan` int NOT NULL,
  `nama` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `jabatan` enum('owner','admin','kasir') NOT NULL,
  `no_telepon` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `dibuat_pada` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `karyawan`
--

INSERT INTO `karyawan` (`id_karyawan`, `nama`, `username`, `password`, `jabatan`, `no_telepon`, `email`, `dibuat_pada`) VALUES
(1, 'wiam', 'wimz', '$2y$10$6UYYP70NDm7ckJLQaG.TeOrxGEtXdERmOCOlxE.oF6zwa34bfxJja', 'owner', '0932423', 'wim@gmail.com', '2025-06-06 17:00:00'),
(2, 'rid<3', 'rida', '$2y$10$U3cC3.E0GtqcFIihsrKlb.KJzKmmkmpOKPZqxiksOc8J/W0FgIYhi', 'kasir', '2324', 'wedfsa@gmail.com', '2025-06-07 15:43:44'),
(3, 'dapongz', 'dz', '$2y$10$LVuH03iAfobVRlerlLI8w.D5PTSs5la4Fw8/lzKzM.SnoAaKO5442', 'admin', '1213132', 'asda@gmail.com', '2025-06-07 15:44:11');

-- --------------------------------------------------------

--
-- Table structure for table `pesanan`
--

CREATE TABLE `pesanan` (
  `id_pesanan` varchar(20) NOT NULL,
  `id_karyawan` int DEFAULT NULL,
  `tipe_pesanan` enum('kasir','online') NOT NULL,
  `jenis_pesanan` enum('dine_in','take_away') DEFAULT 'dine_in',
  `nama_pemesan` varchar(100) DEFAULT 'Walk-in',
  `tgl_pesanan` datetime NOT NULL,
  `total_harga` decimal(12,2) NOT NULL DEFAULT '0.00',
  `metode_pembayaran` enum('tunai','qris','debit','transfer') NOT NULL,
  `status_pesanan` enum('menunggu_konfirmasi','pending','diproses','selesai','dibatalkan') NOT NULL,
  `bukti_pembayaran` varchar(255) DEFAULT NULL,
  `catatan` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `pesanan`
--

INSERT INTO `pesanan` (`id_pesanan`, `id_karyawan`, `tipe_pesanan`, `jenis_pesanan`, `nama_pemesan`, `tgl_pesanan`, `total_harga`, `metode_pembayaran`, `status_pesanan`, `bukti_pembayaran`, `catatan`) VALUES
('PSK-20250607170438', 2, 'kasir', 'dine_in', 'SIHITAM', '2025-06-07 17:04:38', 38000.00, 'tunai', 'diproses', NULL, 'anu'),
('PSK-20250607170500', 2, 'kasir', 'dine_in', 'dflajfasd', '2025-06-07 17:05:00', 98000.00, 'tunai', 'diproses', NULL, ''),
('PSK-20250607170524', 2, 'kasir', 'dine_in', 'Walk-in', '2025-06-07 17:05:24', 100000.00, 'tunai', 'diproses', NULL, ''),
('PSK-20250607170538', 2, 'kasir', 'dine_in', 'Walk-in', '2025-06-07 17:05:38', 5000.00, 'tunai', 'diproses', NULL, '');

-- --------------------------------------------------------

--
-- Table structure for table `produk`
--

CREATE TABLE `produk` (
  `id_produk` varchar(20) NOT NULL,
  `nama_produk` varchar(100) NOT NULL,
  `kategori` enum('makanan','minuman') NOT NULL,
  `harga` decimal(12,2) NOT NULL DEFAULT '0.00',
  `stok` int NOT NULL DEFAULT '0',
  `poto_produk` varchar(255) DEFAULT 'default.jpg',
  `status_produk` enum('aktif','tidak aktif') NOT NULL DEFAULT 'aktif',
  `dibuat_pada` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `produk`
--

INSERT INTO `produk` (`id_produk`, `nama_produk`, `kategori`, `harga`, `stok`, `poto_produk`, `status_produk`, `dibuat_pada`) VALUES
('DK001', 'kopi HITAM', 'minuman', 5000.00, 27, 'logo polsub.png', 'aktif', '2025-06-07 15:57:29'),
('DK002', 'kopi susu', 'minuman', 5500.00, 12, 'Logo-IPB-baru1000.png', 'aktif', '2025-06-07 15:58:05'),
('KB001', 'kue balok susu', 'makanan', 3000.00, 17, 'Asset 5@0.75x.png', 'aktif', '2025-06-07 15:51:24'),
('KB002', 'kue balok keju', 'makanan', 4000.00, 19, 'download.png', 'aktif', '2025-06-07 15:52:25'),
('KB003', 'kue balok taro', 'makanan', 5000.00, 14, 'gopay.png', 'aktif', '2025-06-07 15:52:47'),
('KB004', 'kue balok mix', 'makanan', 4000.00, 20, 'Logo belajar maju.png', 'aktif', '2025-06-07 15:53:08'),
('KS001', 'ketan susu coklat', 'makanan', 3000.00, 17, 'logo bri.png', 'aktif', '2025-06-07 15:53:27'),
('KS002', 'ketan susu keju', 'makanan', 3000.00, 27, 'logo dana.png', 'aktif', '2025-06-07 15:54:11'),
('KS003', 'ketan susu mix', 'makanan', 5000.00, 29, 'logo logo gopay.png', 'aktif', '2025-06-07 15:54:35'),
('OT001', 'mie goreng telur', 'makanan', 10000.00, 0, 'logo low bit.png', 'aktif', '2025-06-07 15:56:21'),
('OT002', 'mie gorang aja', 'makanan', 8000.00, 18, 'logo ovo.png', 'aktif', '2025-06-07 15:56:43');

-- --------------------------------------------------------

--
-- Table structure for table `stok_harian`
--

CREATE TABLE `stok_harian` (
  `id_stok_harian` int NOT NULL,
  `id_produk` varchar(20) NOT NULL,
  `stok` int NOT NULL,
  `tanggal` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `stok_harian`
--

INSERT INTO `stok_harian` (`id_stok_harian`, `id_produk`, `stok`, `tanggal`) VALUES
(1, 'KS001', 20, '2025-06-07'),
(2, 'KS002', 30, '2025-06-07'),
(3, 'KS003', 30, '2025-06-07'),
(4, 'DK001', 30, '2025-06-07'),
(5, 'DK002', 20, '2025-06-07'),
(6, 'KB002', 20, '2025-06-07'),
(7, 'KB004', 20, '2025-06-07'),
(8, 'KB001', 20, '2025-06-07'),
(9, 'KB003', 20, '2025-06-07'),
(10, 'OT002', 20, '2025-06-07'),
(11, 'OT001', 10, '2025-06-07');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `detail_pesanan`
--
ALTER TABLE `detail_pesanan`
  ADD PRIMARY KEY (`id_detail`),
  ADD KEY `id_pesanan` (`id_pesanan`),
  ADD KEY `id_produk` (`id_produk`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id_feedback`);

--
-- Indexes for table `karyawan`
--
ALTER TABLE `karyawan`
  ADD PRIMARY KEY (`id_karyawan`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `pesanan`
--
ALTER TABLE `pesanan`
  ADD PRIMARY KEY (`id_pesanan`),
  ADD KEY `id_karyawan` (`id_karyawan`);

--
-- Indexes for table `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`id_produk`);

--
-- Indexes for table `stok_harian`
--
ALTER TABLE `stok_harian`
  ADD PRIMARY KEY (`id_stok_harian`),
  ADD UNIQUE KEY `produk_per_hari` (`id_produk`,`tanggal`),
  ADD KEY `id_produk_stok` (`id_produk`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `detail_pesanan`
--
ALTER TABLE `detail_pesanan`
  MODIFY `id_detail` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id_feedback` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `karyawan`
--
ALTER TABLE `karyawan`
  MODIFY `id_karyawan` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `stok_harian`
--
ALTER TABLE `stok_harian`
  MODIFY `id_stok_harian` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `detail_pesanan`
--
ALTER TABLE `detail_pesanan`
  ADD CONSTRAINT `fk_detail_pesanan` FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan` (`id_pesanan`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_detail_produk` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id_produk`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `pesanan`
--
ALTER TABLE `pesanan`
  ADD CONSTRAINT `fk_pesanan_karyawan` FOREIGN KEY (`id_karyawan`) REFERENCES `karyawan` (`id_karyawan`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `stok_harian`
--
ALTER TABLE `stok_harian`
  ADD CONSTRAINT `fk_stok_harian_produk` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id_produk`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
