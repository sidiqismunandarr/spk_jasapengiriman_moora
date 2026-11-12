-- phpMyAdmin SQL Dump
-- SPK Pemilihan Jasa Pengiriman MOORA - FAM Parfum
-- Versi Database

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `spk_jasapengiriman_moora`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `admin`
--

CREATE TABLE IF NOT EXISTS `admin` (
  `id_admin` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(25) NOT NULL,
  `password` varchar(20) NOT NULL,
  `nama` varchar(50) NOT NULL,
  PRIMARY KEY (`id_admin`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `admin`
--

INSERT INTO `admin` (`id_admin`, `username`, `password`, `nama`) VALUES
(1, 'admin', 'admin123', 'Administrator FAM Parfum');

-- --------------------------------------------------------

--
-- Struktur dari tabel `ekspedisi`
--

CREATE TABLE IF NOT EXISTS `ekspedisi` (
  `id_ekspedisi` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(50) NOT NULL,
  PRIMARY KEY (`id_ekspedisi`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `ekspedisi`
--

INSERT INTO `ekspedisi` (`id_ekspedisi`, `nama`) VALUES
(1, 'J&T Express'),
(2, 'SiCepat Ekspres'),
(3, 'JNE Express'),
(4, 'Shopee Xpress (SPX)'),
(5, 'Anteraja'),
(6, 'Ninja Xpress');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kriteria`
--

CREATE TABLE IF NOT EXISTS `kriteria` (
  `id_kriteria` int(11) NOT NULL AUTO_INCREMENT,
  `kode_kriteria` varchar(5) NOT NULL,
  `nama_kriteria` varchar(50) NOT NULL,
  `bobot` int(11) NOT NULL,
  `jenis` varchar(10) NOT NULL,
  PRIMARY KEY (`id_kriteria`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `kriteria`
--

INSERT INTO `kriteria` (`id_kriteria`, `kode_kriteria`, `nama_kriteria`, `bobot`, `jenis`) VALUES
(1, 'C1', 'Biaya Pengiriman', 25, 'Cost'),
(2, 'C2', 'Kecepatan Pengiriman', 25, 'Benefit'),
(3, 'C3', 'Keamanan Paket Parfum', 20, 'Benefit'),
(4, 'C4', 'Jangkauan Pengiriman', 15, 'Benefit'),
(5, 'C5', 'Layanan Pick-up & Tracking', 15, 'Benefit');

-- --------------------------------------------------------

--
-- Struktur dari tabel `alternatif`
--

CREATE TABLE IF NOT EXISTS `alternatif` (
  `id_alternatif` int(11) NOT NULL AUTO_INCREMENT,
  `id_mitra` int(11) NOT NULL,
  `id_kriteria` int(11) NOT NULL,
  `nilai` int(11) NOT NULL,
  PRIMARY KEY (`id_alternatif`),
  KEY `id_mitra` (`id_mitra`),
  KEY `id_kriteria` (`id_kriteria`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `alternatif`
--

INSERT INTO `alternatif` (`id_alternatif`, `id_mitra`, `id_kriteria`, `nilai`) VALUES
(1, 1, 1, 3), (2, 1, 2, 5), (3, 1, 3, 4), (4, 1, 4, 5), (5, 1, 5, 5),
(6, 2, 1, 2), (7, 2, 2, 5), (8, 2, 3, 5), (9, 2, 4, 4), (10, 2, 5, 5),
(11, 3, 1, 4), (12, 3, 2, 4), (13, 3, 3, 4), (14, 3, 4, 5), (15, 3, 5, 4),
(16, 4, 1, 2), (17, 4, 2, 3), (18, 4, 3, 3), (19, 4, 4, 3), (20, 4, 5, 4),
(21, 5, 1, 3), (22, 5, 2, 4), (23, 5, 3, 4), (24, 5, 4, 3), (25, 5, 5, 4),
(26, 6, 1, 3), (27, 6, 2, 3), (28, 6, 3, 3), (29, 6, 4, 3), (30, 6, 5, 3);

-- --------------------------------------------------------

--
-- Struktur dari tabel `ranking`
--

CREATE TABLE IF NOT EXISTS `ranking` (
  `id_ranking` int(11) NOT NULL AUTO_INCREMENT,
  `id_alternatif` int(11) NOT NULL,
  `skor_optimasi` decimal(10,4) NOT NULL,
  `ranking` int(11) NOT NULL,
  `tgl_hitung` date NOT NULL,
  PRIMARY KEY (`id_ranking`),
  KEY `id_alternatif` (`id_alternatif`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
