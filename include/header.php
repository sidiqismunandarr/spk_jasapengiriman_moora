<?php
/**
 * Top Header Navigation
 * SPK Pemilihan Jasa Pengiriman MOORA - FAM Parfum
 */
$pageTitles = [
    'dashboard' => 'Dashboard Utama',
    'ranking' => 'Hasil Perankingan',
    'ekspedisi' => 'Data Jasa Pengiriman',
    'kriteria' => 'Data Kriteria Penilaian',
    'penilaian' => 'Penilaian Alternatif',
    'proses' => 'Proses Perhitungan MOORA',
    'riwayat' => 'Riwayat Perhitungan',
    'admin' => 'Pengaturan Akun Admin',
    'cetak' => 'Cetak Laporan Rekomendasi'
];

$titleCurrent = isset($pageTitles[$currentPage]) ? $pageTitles[$currentPage] : 'Dashboard';
$headerAdminNama = isset($_SESSION['admin_nama']) ? $_SESSION['admin_nama'] : 'Admin';
?>

<header class="app-header">
    <div class="header-left">
        <button id="mobileToggleBtn" class="mobile-toggle" aria-label="Buka Menu">
            <i data-lucide="menu"></i>
        </button>

        <div class="header-breadcrumb">
            <span>SPK FAM Parfum</span>
            <span class="separator">/</span>
            <span class="current"><?= htmlspecialchars($titleCurrent) ?></span>
        </div>
    </div>

    <div class="header-right">
        <div class="header-badge">
            <span class="dot"></span>
            <span>MOORA Aktif</span>
        </div>

        <div class="header-badge" style="background: #FFFFFF;">
            <i data-lucide="user" style="width: 14px; height: 14px; color: var(--primary);"></i>
            <span><?= htmlspecialchars($headerAdminNama) ?></span>
        </div>

        <a href="logout.php" onclick="return confirm('Apakah Anda yakin ingin keluar (logout) dari sistem SPK?');" class="btn btn-secondary btn-sm" style="color: var(--danger); border-color: var(--danger-border); gap: 6px;" title="Keluar dari sistem">
            <i data-lucide="log-out" style="width: 14px; height: 14px;"></i>
            <span>Keluar</span>
        </a>
    </div>
</header>
