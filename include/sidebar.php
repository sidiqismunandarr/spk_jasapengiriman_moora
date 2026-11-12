<?php
/**
 * Persistent Left Sidebar Navigation
 * SPK Pemilihan Jasa Pengiriman MOORA - FAM Parfum
 */
$currentPage = isset($_GET['page']) ? cleanInput($_GET['page']) : 'dashboard';
$adminNama = isset($_SESSION['admin_nama']) ? $_SESSION['admin_nama'] : 'Administrator FAM Parfum';
$adminUser = isset($_SESSION['admin_username']) ? $_SESSION['admin_username'] : 'admin';

// Extract initials
$words = explode(' ', trim($adminNama));
$initials = '';
foreach ($words as $w) {
    if (!empty($w)) $initials .= strtoupper($w[0]);
    if (strlen($initials) >= 2) break;
}
if (empty($initials)) $initials = 'AD';
?>

<!-- Mobile Sidebar Overlay -->
<div id="sidebarOverlay" class="modal-overlay" style="z-index: 45;"></div>

<!-- Persistent Left Sidebar -->
<aside id="appSidebar" class="app-sidebar">
    <!-- Brand / Logo -->
    <div class="sidebar-brand">
        <div class="brand-icon">
            <i data-lucide="package-search"></i>
        </div>
        <div class="brand-info">
            <span class="brand-title">FAM Parfum</span>
            <span class="brand-subtitle">SPK MOORA Logistik</span>
        </div>
    </div>

    <!-- Navigation Menu -->
    <nav class="sidebar-nav">
        <div class="nav-section-title">Menu Utama</div>

        <a href="index.php?page=dashboard" class="nav-item <?= ($currentPage == 'dashboard') ? 'active' : '' ?>">
            <i data-lucide="layout-dashboard"></i>
            <span>Dashboard</span>
        </a>

        <a href="index.php?page=ranking" class="nav-item <?= ($currentPage == 'ranking') ? 'active' : '' ?>">
            <i data-lucide="award"></i>
            <span>Hasil Perankingan</span>
            <span class="nav-badge">MOORA</span>
        </a>

        <div class="nav-section-title">Master Data & Penilaian</div>

        <a href="index.php?page=ekspedisi" class="nav-item <?= ($currentPage == 'ekspedisi') ? 'active' : '' ?>">
            <i data-lucide="truck"></i>
            <span>Data Jasa Pengiriman</span>
        </a>

        <a href="index.php?page=kriteria" class="nav-item <?= ($currentPage == 'kriteria') ? 'active' : '' ?>">
            <i data-lucide="sliders"></i>
            <span>Data Kriteria</span>
        </a>

        <a href="index.php?page=penilaian" class="nav-item <?= ($currentPage == 'penilaian') ? 'active' : '' ?>">
            <i data-lucide="clipboard-check"></i>
            <span>Penilaian Alternatif</span>
        </a>

        <div class="nav-section-title">Analisis & Laporan</div>

        <a href="index.php?page=proses" class="nav-item <?= ($currentPage == 'proses') ? 'active' : '' ?>">
            <i data-lucide="calculator"></i>
            <span>Proses MOORA</span>
        </a>

        <a href="index.php?page=riwayat" class="nav-item <?= ($currentPage == 'riwayat') ? 'active' : '' ?>">
            <i data-lucide="history"></i>
            <span>Riwayat Perhitungan</span>
        </a>

        <div class="nav-section-title">Sistem & Akun</div>

        <a href="index.php?page=admin" class="nav-item <?= ($currentPage == 'admin') ? 'active' : '' ?>">
            <i data-lucide="user-cog"></i>
            <span>Pengaturan Akun</span>
        </a>

        <a href="logout.php" onclick="return confirm('Apakah Anda yakin ingin keluar (logout) dari sistem SPK?');" class="nav-item" style="color: #F87171; margin-top: 4px;">
            <i data-lucide="log-out" style="color: #F87171;"></i>
            <span>Keluar (Logout)</span>
        </a>
    </nav>

    <!-- Sidebar Footer -->
    <div class="sidebar-footer">
        <div class="admin-card">
            <div class="admin-avatar" title="<?= htmlspecialchars($adminNama) ?>"><?= htmlspecialchars($initials) ?></div>
            <div class="admin-meta">
                <div class="admin-name" title="<?= htmlspecialchars($adminNama) ?>"><?= htmlspecialchars($adminNama) ?></div>
                <div class="admin-role">@<?= htmlspecialchars($adminUser) ?></div>
            </div>
            <a href="logout.php" onclick="return confirm('Apakah Anda yakin ingin keluar dari sistem?');" title="Logout" style="color: #F87171; padding: 4px;">
                <i data-lucide="log-out" style="width: 16px; height: 16px;"></i>
            </a>
        </div>
    </div>
</aside>
