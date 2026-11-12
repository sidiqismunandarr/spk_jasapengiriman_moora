<?php
/**
 * Sistem Pendukung Keputusan (SPK) Pemilihan Jasa Pengiriman
 * Metode MOORA - Bisnis Online FAM Parfum
 * 
 * Main Entry & Controller
 */

session_start();

require_once __DIR__ . '/include/koneksi.php';
require_once __DIR__ . '/include/moora_helper.php';

// Route determination
$page = isset($_GET['page']) ? cleanInput($_GET['page']) : 'dashboard';

// Handle logout route
if ($page === 'logout') {
    require_once __DIR__ . '/logout.php';
    exit;
}

// Authentication Guard: Check if logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php?msg=auth_required");
    exit;
}

// Standalone pages without dashboard layout (e.g., printable report)
if ($page === 'cetak') {
    require_once __DIR__ . '/pages/cetak.php';
    exit;
}

$pageTitles = [
    'dashboard' => 'Dashboard Utama - SPK MOORA FAM Parfum',
    'ranking'   => 'Hasil Perankingan Jasa Pengiriman - SPK MOORA',
    'ekspedisi' => 'Data Jasa Pengiriman - SPK MOORA FAM Parfum',
    'kriteria'  => 'Data Kriteria Penilaian - SPK MOORA FAM Parfum',
    'penilaian' => 'Penilaian Alternatif - SPK MOORA FAM Parfum',
    'proses'    => 'Proses Perhitungan MOORA - SPK MOORA FAM Parfum',
    'riwayat'   => 'Riwayat Perhitungan - SPK MOORA FAM Parfum',
    'admin'     => 'Pengaturan Akun Admin - SPK MOORA FAM Parfum',
];

$pageTitle = isset($pageTitles[$page]) ? $pageTitles[$page] : 'SPK MOORA FAM Parfum';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Main Stylesheet -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<div class="app-layout">
    <!-- Persistent Left Sidebar -->
    <?php require_once __DIR__ . '/include/sidebar.php'; ?>

    <!-- Main Content Area -->
    <main class="app-main">
        <!-- Compact Top Header -->
        <?php require_once __DIR__ . '/include/header.php'; ?>

        <!-- Dynamic Page View -->
        <?php
        switch ($page) {
            case 'ranking':
                require_once __DIR__ . '/pages/ranking.php';
                break;
            case 'ekspedisi':
                require_once __DIR__ . '/pages/ekspedisi.php';
                break;
            case 'kriteria':
                require_once __DIR__ . '/pages/kriteria.php';
                break;
            case 'penilaian':
                require_once __DIR__ . '/pages/penilaian.php';
                break;
            case 'proses':
                require_once __DIR__ . '/pages/proses.php';
                break;
            case 'riwayat':
                require_once __DIR__ . '/pages/riwayat.php';
                break;
            case 'admin':
                require_once __DIR__ . '/pages/admin.php';
                break;
            case 'dashboard':
            default:
                require_once __DIR__ . '/pages/dashboard.php';
                break;
        }
        ?>

        <!-- Footer -->
        <?php require_once __DIR__ . '/include/footer.php'; ?>