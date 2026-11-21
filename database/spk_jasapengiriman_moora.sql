-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 21 Nov 2026 pada 11.17
-- Versi server: 10.4.6-MariaDB
-- Versi PHP: 7.3.8

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

CREATE TABLE `admin` (
  `id_admin` int(11) NOT NULL,
  `username` varchar(25) NOT NULL,
  `password` varchar(20) NOT NULL,
  `nama` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `admin`
--

INSERT INTO `admin` (`id_admin`, `username`, `password`, `nama`) VALUES
(1, 'admin', 'admin123', 'Administrator FAM Parfum');

-- --------------------------------------------------------

--
-- Struktur dari tabel `alternatif`
--

CREATE TABLE `alternatif` (
  `id_alternatif` int(11) NOT NULL,
  `id_mitra` int(11) NOT NULL,
  `id_kriteria` int(11) NOT NULL,
  `nilai` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `alternatif`
--

INSERT INTO `alternatif` (`id_alternatif`, `id_mitra`, `id_kriteria`, `nilai`) VALUES
(1, 1, 1, 3),
(2, 1, 2, 5),
(3, 1, 3, 4),
(4, 1, 4, 5),
(5, 1, 5, 5),
(6, 2, 1, 2),
(7, 2, 2, 5),
(8, 2, 3, 5),
(9, 2, 4, 4),
(10, 2, 5, 5),
(11, 3, 1, 4),
(12, 3, 2, 4),
(13, 3, 3, 4),
(14, 3, 4, 5),
(15, 3, 5, 4),
(16, 4, 1, 2),
(17, 4, 2, 3),
(18, 4, 3, 3),
(19, 4, 4, 3),
(20, 4, 5, 4),
(21, 5, 1, 3),
(22, 5, 2, 4),
(23, 5, 3, 4),
(24, 5, 4, 3),
(25, 5, 4, 4),
(26, 6, 1, 3),
(27, 6, 2, 3),
(28, 6, 3, 3),
(29, 6, 4, 3),
(30, 6, 5, 3);

-- --------------------------------------------------------

--
-- Struktur dari tabel `ekspedisi`
--

CREATE TABLE `ekspedisi` (
  `id_ekspedisi` int(11) NOT NULL,
  `nama` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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

CREATE TABLE `kriteria` (
  `id_kriteria` int(11) NOT NULL,
  `kode_kriteria` varchar(3) NOT NULL,
  `nama_kriteria` varchar(30) NOT NULL,
  `bobot` int(11) NOT NULL,
  `jenis` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
-- Struktur dari tabel `ranking`
--

CREATE TABLE `ranking` (
  `id_ranking` int(11) NOT NULL,
  `id_alternatif` int(11) NOT NULL,
  `skor_optimasi` decimal(10,4) NOT NULL,
  `ranking` int(11) NOT NULL,
  `tgl_hitung` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `ranking`
--

INSERT INTO `ranking` (`id_ranking`, `id_alternatif`, `skor_optimasi`, `ranking`, `tgl_hitung`) VALUES
(1, 2, '0.2984', 1, '2026-10-30'),
(2, 1, '0.2575', 2, '2026-10-30'),
(3, 3, '0.1817', 3, '2026-10-30'),
(4, 4, '0.1758', 4, '2026-10-30'),
(5, 5, '0.1388', 5, '2026-10-30'),
(6, 6, '0.1250', 6, '2026-10-30');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id_admin`);

--
-- Indeks untuk tabel `alternatif`
--
ALTER TABLE `alternatif`
  ADD PRIMARY KEY (`id_alternatif`);

--
-- Indeks untuk tabel `ekspedisi`
--
ALTER TABLE `ekspedisi`
  ADD PRIMARY KEY (`id_ekspedisi`);

--
-- Indeks untuk tabel `kriteria`
--
ALTER TABLE `kriteria`
  ADD PRIMARY KEY (`id_kriteria`);

--
-- Indeks untuk tabel `ranking`
--
ALTER TABLE `ranking`
  ADD PRIMARY KEY (`id_ranking`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `admin`
--
ALTER TABLE `admin`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `alternatif`
--
ALTER TABLE `alternatif`
  MODIFY `id_alternatif` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT untuk tabel `ekspedisi`
--
ALTER TABLE `ekspedisi`
  MODIFY `id_ekspedisi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `kriteria`
--
ALTER TABLE `kriteria`
  MODIFY `id_kriteria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `ranking`
--
ALTER TABLE `ranking`
  MODIFY `id_ranking` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
