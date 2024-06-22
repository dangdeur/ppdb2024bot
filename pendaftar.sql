-- phpMyAdmin SQL Dump
-- version 5.1.1deb5ubuntu1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jun 22, 2024 at 11:27 AM
-- Server version: 10.6.16-MariaDB-0ubuntu0.22.04.1
-- PHP Version: 8.1.2-1ubuntu2.17

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ppdb_2024`
--

-- --------------------------------------------------------

--
-- Table structure for table `pendaftar`
--

CREATE TABLE `pendaftar` (
  `no` char(11) DEFAULT NULL,
  `no_un` char(20) DEFAULT NULL,
  `no_ujian` char(20) DEFAULT NULL,
  `nisn` varchar(50) DEFAULT NULL,
  `nama` varchar(50) DEFAULT NULL,
  `kelamin` varchar(50) DEFAULT NULL,
  `tmp_lahir` varchar(50) DEFAULT NULL,
  `tgl_lahir` varchar(50) DEFAULT NULL,
  `alamat_lengkap` varchar(256) DEFAULT NULL,
  `provinsi` varchar(50) DEFAULT NULL,
  `kota_kab` varchar(50) DEFAULT NULL,
  `kecamatan` varchar(50) DEFAULT NULL,
  `kelurahan` varchar(50) DEFAULT NULL,
  `no_rw` char(5) DEFAULT NULL,
  `no_rt` char(5) DEFAULT NULL,
  `status_verifikasi` varchar(50) DEFAULT NULL,
  `keterangan_verifikasi` varchar(50) DEFAULT NULL,
  `operator` varchar(50) DEFAULT NULL,
  `asal_sekolah` varchar(50) DEFAULT NULL,
  `jenis_lulusan` varchar(50) DEFAULT NULL,
  `tahun_lulus` char(7) DEFAULT NULL,
  `kapasitas` varchar(50) DEFAULT NULL,
  `nomor_hp_wa_(aktif)` varchar(50) DEFAULT NULL,
  `domisili` varchar(50) DEFAULT NULL,
  `nama_orang_tua_wali` varchar(50) DEFAULT NULL,
  `nik` char(30) DEFAULT NULL,
  `tanggal_ajuan` varchar(50) DEFAULT NULL,
  `jam_ajuan` varchar(50) DEFAULT NULL,
  `rapor_agama_dan_budi_pekerti` varchar(50) DEFAULT NULL,
  `rapor_pkn` varchar(50) DEFAULT NULL,
  `rapor_bahasa_indonesia` varchar(50) DEFAULT NULL,
  `rapor_matematika` varchar(50) DEFAULT NULL,
  `rapor_ipa` varchar(50) DEFAULT NULL,
  `rapor_ips` varchar(50) DEFAULT NULL,
  `rapor_bahasa_inggris` varchar(50) DEFAULT NULL,
  `rapor_seni_budaya` varchar(50) DEFAULT NULL,
  `rapor_pjok_penjas` varchar(50) DEFAULT NULL,
  `rapor_prakarya_informatika` varchar(50) DEFAULT NULL,
  `rapor_rerata_pelajaran` varchar(50) DEFAULT NULL,
  `status_siswa_afirmasi_atau_abk` varchar(50) DEFAULT NULL,
  `prestasi_akademik` varchar(50) DEFAULT NULL,
  `bidang_prestasi` varchar(50) DEFAULT NULL,
  `pilihan_1` varchar(64) DEFAULT NULL,
  `pilihan_2` varchar(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
