-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 30, 2026 at 01:25 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `kominfo`
--

-- --------------------------------------------------------

--
-- Table structure for table `cuti`
--

CREATE TABLE `cuti` (
  `id_cuti` int(11) NOT NULL,
  `id_pegawai` int(11) DEFAULT NULL,
  `id_jenis` int(11) DEFAULT NULL,
  `alasan_cuti` text DEFAULT NULL,
  `lama_cuti` int(11) DEFAULT NULL,
  `tanggal_mulai` date DEFAULT NULL,
  `tanggal_selesai` date DEFAULT NULL,
  `alamat_selama_cuti` text DEFAULT NULL,
  `no_telp` varchar(20) DEFAULT NULL,
  `tahun_cuti` year(4) DEFAULT NULL,
  `sisa_cuti_n2` int(11) DEFAULT NULL,
  `sisa_cuti_n1` int(11) DEFAULT NULL,
  `sisa_cuti_n` int(11) DEFAULT NULL,
  `status_cuti` enum('Diajukan','Disetujui','Ditolak') DEFAULT 'Diajukan',
  `tanggal_pengajuan` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jenis_cuti`
--

CREATE TABLE `jenis_cuti` (
  `id_jenis` int(11) NOT NULL,
  `nama_cuti` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jenis_cuti`
--

INSERT INTO `jenis_cuti` (`id_jenis`, `nama_cuti`) VALUES
(1, 'Cuti Tahunan'),
(2, 'Cuti Besar'),
(3, 'Cuti Sakit'),
(4, 'Cuti Melahirkan'),
(5, 'Cuti Karena Alasan Penting'),
(6, 'Cuti di Luar Tanggungan Negara');

-- --------------------------------------------------------

--
-- Table structure for table `pegawai`
--

CREATE TABLE `pegawai` (
  `id_pegawai` int(11) NOT NULL,
  `nip` varchar(30) NOT NULL,
  `nama_pegawai` varchar(100) NOT NULL,
  `jabatan` varchar(100) DEFAULT NULL,
  `unit_kerja` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `persetujuan_cuti`
--

CREATE TABLE `persetujuan_cuti` (
  `id_persetujuan` int(11) NOT NULL,
  `id_cuti` int(11) DEFAULT NULL,
  `pejabat_penyetuju` varchar(100) DEFAULT NULL,
  `jabatan_penyetuju` varchar(100) DEFAULT NULL,
  `nip_penyetuju` varchar(30) DEFAULT NULL,
  `tanggal_persetujuan` date DEFAULT NULL,
  `catatan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cuti`
--
ALTER TABLE `cuti`
  ADD PRIMARY KEY (`id_cuti`),
  ADD KEY `id_pegawai` (`id_pegawai`),
  ADD KEY `id_jenis` (`id_jenis`);

--
-- Indexes for table `jenis_cuti`
--
ALTER TABLE `jenis_cuti`
  ADD PRIMARY KEY (`id_jenis`);

--
-- Indexes for table `pegawai`
--
ALTER TABLE `pegawai`
  ADD PRIMARY KEY (`id_pegawai`);

--
-- Indexes for table `persetujuan_cuti`
--
ALTER TABLE `persetujuan_cuti`
  ADD PRIMARY KEY (`id_persetujuan`),
  ADD KEY `id_cuti` (`id_cuti`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cuti`
--
ALTER TABLE `cuti`
  MODIFY `id_cuti` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jenis_cuti`
--
ALTER TABLE `jenis_cuti`
  MODIFY `id_jenis` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `pegawai`
--
ALTER TABLE `pegawai`
  MODIFY `id_pegawai` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `persetujuan_cuti`
--
ALTER TABLE `persetujuan_cuti`
  MODIFY `id_persetujuan` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cuti`
--
ALTER TABLE `cuti`
  ADD CONSTRAINT `cuti_ibfk_1` FOREIGN KEY (`id_pegawai`) REFERENCES `pegawai` (`id_pegawai`),
  ADD CONSTRAINT `cuti_ibfk_2` FOREIGN KEY (`id_jenis`) REFERENCES `jenis_cuti` (`id_jenis`);

--
-- Constraints for table `persetujuan_cuti`
--
ALTER TABLE `persetujuan_cuti`
  ADD CONSTRAINT `persetujuan_cuti_ibfk_1` FOREIGN KEY (`id_cuti`) REFERENCES `cuti` (`id_cuti`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
