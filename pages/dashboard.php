<?php
/**
 * Halaman Dashboard Utama
 * SPK Pemilihan Jasa Pengiriman MOORA - FAM Parfum
 */

$moora = new MooraHelper($pdo);
$stats = $moora->getDashboardStats();
$latestRanking = $moora->getStoredRanking();
$topEkspedisi = $stats['top_ekspedisi'];

// If no calculation saved yet, calculate on the fly for preview
if (empty($latestRanking)) {
    $calcResult = $moora->calculate();
    if ($calcResult['success']) {
        $moora->saveResults($calcResult['ranking']);
        $latestRanking = $moora->getStoredRanking();
        $stats = $moora->getDashboardStats();
        $topEkspedisi = $stats['top_ekspedisi'];
    }
}
?>

<div class="page-container">
    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h1 class="page-title">Dashboard</h1>
            <p class="page-description">Ringkasan data dan rekomendasi jasa pengiriman untuk operasional pengiriman barang FAM Parfum.</p>
        </div>
        <div class="page-actions">
            <a href="index.php?page=ranking" class="btn btn-primary">
                <i data-lucide="award"></i>
                <span>Lihat Hasil Perankingan</span>
            </a>
            <a href="index.php?page=proses" class="btn btn-secondary">
                <i data-lucide="calculator"></i>
                <span>Mulai Perhitungan</span>
            </a>
        </div>
    </div>

    <!-- 4 Summary Cards -->
    <div class="stats-grid">
        <!-- 1. Total Ekspedisi -->
        <div class="stat-card">
            <div>
                <div class="stat-label">Total Ekspedisi</div>
                <div class="stat-value"><?= number_format($stats['total_ekspedisi']) ?></div>
                <div class="stat-subtext">
                    <span style="color: var(--primary); font-weight: 600;">Alternatif aktif</span>
                </div>
            </div>
            <div class="stat-icon blue">
                <i data-lucide="truck"></i>
            </div>
        </div>

        <!-- 2. Total Kriteria -->
        <div class="stat-card">
            <div>
                <div class="stat-label">Total Kriteria Penilaian</div>
                <div class="stat-value"><?= number_format($stats['total_kriteria']) ?></div>
                <div class="stat-subtext">
                    <span style="color: var(--success); font-weight: 600;">Cost & Benefit</span>
                </div>
            </div>
            <div class="stat-icon green">
                <i data-lucide="sliders"></i>
            </div>
        </div>

        <!-- 3. Perhitungan Terakhir -->
        <div class="stat-card">
            <div>
                <div class="stat-label">Perhitungan Terakhir</div>
                <div class="stat-value" style="font-size: 18px; padding-top: 4px;">
                    <?= $stats['tgl_hitung_terakhir'] ? formatTanggalIndo($stats['tgl_hitung_terakhir']) : 'Belum Ada' ?>
                </div>
                <div class="stat-subtext">
                    <i data-lucide="calendar-check" style="width: 14px; height: 14px; color: var(--warning);"></i>
                    <span>Siklus evaluasi terbaru</span>
                </div>
            </div>
            <div class="stat-icon amber">
                <i data-lucide="history"></i>
            </div>
        </div>

        <!-- 4. Peringkat 1 Saat Ini -->
        <div class="stat-card">
            <div>
                <div class="stat-label">Alternatif Peringkat 1</div>
                <div class="stat-value" style="font-size: 19px; padding-top: 4px; color: var(--primary);">
                    <?= $topEkspedisi ? htmlspecialchars($topEkspedisi['nama_ekspedisi']) : 'Belum Ada' ?>
                </div>
                <div class="stat-subtext">
                    <span class="badge badge-rank-1">Rekomendasi Utama</span>
                </div>
            </div>
            <div class="stat-icon purple">
                <i data-lucide="trophy"></i>
            </div>
        </div>
    </div>

    <!-- Kartu Rekomendasi Unggulan -->
    <?php if ($topEkspedisi): ?>
    <div class="hero-card">
        <div class="hero-grid">
            <div>
                <div class="hero-badge-wrap">
                    <i data-lucide="sparkles" style="width: 14px; height: 14px;"></i>
                    <span>Rekomendasi Utama Operasional FAM Parfum</span>
                </div>
                <h2 class="hero-title"><?= htmlspecialchars($topEkspedisi['nama_ekspedisi']) ?></h2>
                <p class="hero-desc">
                    Berdasarkan perhitungan matematis metode <strong>MOORA</strong>, jasa pengiriman <strong><?= htmlspecialchars($topEkspedisi['nama_ekspedisi']) ?></strong> memperoleh nilai optimasi tertinggi setelah mempertimbangkan biaya pengiriman (*cost*), ketepatan waktu pengantaran, keamanan handling cairan parfum (*fragile*), serta jangkauan area pengiriman.
                </p>

                <div class="hero-stats-row">
                    <div class="hero-stat-item">
                        <span class="hero-stat-label">Peringkat SPK</span>
                        <span class="hero-stat-val" style="color: var(--success);">Rank 1</span>
                    </div>
                    <div class="hero-stat-item">
                        <span class="hero-stat-label">Skor Optimasi MOORA ($Y_i$)</span>
                        <span class="hero-stat-val"><?= number_format($topEkspedisi['skor_optimasi'], 4) ?></span>
                    </div>
                    <div class="hero-stat-item">
                        <span class="hero-stat-label">Tanggal Analisis</span>
                        <span class="hero-stat-val"><?= formatTanggalIndo($topEkspedisi['tgl_hitung']) ?></span>
                    </div>
                    <div class="hero-stat-item">
                        <span class="hero-stat-label">Status Kelayakan</span>
                        <span class="badge badge-benefit" style="align-self: flex-start; margin-top: 4px;">Sangat Direkomendasikan</span>
                    </div>
                </div>
            </div>

            <div>
                <div class="hero-score-badge">
                    <div class="hero-score-lbl">Nilai Optimasi</div>
                    <div class="hero-score-val"><?= number_format($topEkspedisi['skor_optimasi'], 4) ?></div>
                    <div style="font-size: 12px; color: var(--text-muted);">Peringkat Tertinggi</div>
                    <div style="margin-top: 14px;">
                        <a href="index.php?page=ranking" class="btn btn-primary btn-sm" style="width: 100%;">
                            <span>Detail Ranking</span>
                            <i data-lucide="arrow-right" style="width: 14px; height: 14px;"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Grid: Ringkasan 3 Besar & Informasi Kriteria Cepat -->
    <div style="display: grid; grid-template-columns: 2fr 1.2fr; gap: 24px;">
        <!-- Tabel Ringkasan Peringkat (Top 3) -->
        <div class="card" style="margin-bottom: 0;">
            <div class="card-header">
                <div>
                    <h3 class="card-title">
                        <i data-lucide="medal" style="color: var(--primary); width: 18px; height: 18px;"></i>
                        <span>Pratinjau 3 Besar Alternatif Terbaik</span>
                    </h3>
                    <div class="card-subtitle">Hasil evaluasi perankingan teratas metode MOORA</div>
                </div>
                <a href="index.php?page=ranking" class="btn btn-secondary btn-sm">
                    <span>Lihat Semua</span>
                    <i data-lucide="chevron-right" style="width: 14px; height: 14px;"></i>
                </a>
            </div>
            <div class="card-body" style="padding: 0;">
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th style="width: 80px; text-align: center;">Peringkat</th>
                                <th>Jasa Pengiriman</th>
                                <th style="text-align: right;">Skor MOORA ($Y_i$)</th>
                                <th>Status Rekomendasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $count = 0;
                            foreach ($latestRanking as $row): 
                                $count++;
                                if ($count > 3) break;
                                $isTop = ($row['ranking'] == 1);
                            ?>
                            <tr class="<?= $isTop ? 'highlight-rank-1' : '' ?>">
                                <td style="text-align: center;">
                                    <?php if ($row['ranking'] == 1): ?>
                                        <span class="badge badge-rank-1">Juara 1</span>
                                    <?php elseif ($row['ranking'] == 2): ?>
                                        <span class="badge badge-rank-2">Rank 2</span>
                                    <?php elseif ($row['ranking'] == 3): ?>
                                        <span class="badge badge-rank-3">Rank 3</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($row['nama_ekspedisi']) ?></strong>
                                    <?php if ($isTop): ?>
                                        <span style="font-size: 11px; color: var(--success); font-weight: 600; margin-left: 6px;">★ Pilihan Terbaik</span>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align: right; font-weight: 600; color: var(--primary);">
                                    <?= number_format($row['skor_optimasi'], 4) ?>
                                </td>
                                <td>
                                    <?php if ($row['ranking'] == 1): ?>
                                        <span class="badge badge-benefit">Sangat Direkomendasikan</span>
                                    <?php else: ?>
                                        <span class="badge badge-benefit" style="background: #F1F5F9; color: #334155; border-color: #CBD5E1;">Direkomendasikan</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($latestRanking)): ?>
                            <tr>
                                <td colspan="4" style="text-align: center; padding: 24px; color: var(--text-muted);">
                                    Belum ada data hasil perhitungan perankingan.
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                <span style="font-size: 12px; color: var(--text-muted);">
                    Data diambil dari penilaian operasional terbaru FAM Parfum
                </span>
                <a href="index.php?page=proses" class="btn btn-primary btn-sm">
                    <i data-lucide="calculator" style="width: 14px; height: 14px;"></i>
                    <span>Hitung Ulang MOORA</span>
                </a>
            </div>
        </div>

        <!-- Kartu Karakteristik MOORA untuk FAM Parfum -->
        <div class="card" style="margin-bottom: 0;">
            <div class="card-header">
                <h3 class="card-title">
                    <i data-lucide="info" style="color: var(--primary); width: 18px; height: 18px;"></i>
                    <span>Prinsip Seleksi Ekspedisi</span>
                </h3>
            </div>
            <div class="card-body">
                <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 16px;">
                    Produk parfum memiliki karakteristik khusus (cairan, kemasan botol kaca rentan pecah, sensitivitas aroma). Kriteria seleksi dibagi menjadi dua kategori:
                </p>

                <div style="display: flex; flex-direction: column; gap: 12px;">
                    <div style="padding: 12px; background: var(--bg-subtle); border-radius: var(--radius-md); border-left: 4px solid var(--danger);">
                        <div style="font-weight: 600; font-size: 13px; color: var(--text-heading); display: flex; align-items: center; justify-content: space-between;">
                            <span>Kriteria Cost (Biaya)</span>
                            <span class="badge badge-cost">Diminimalkan</span>
                        </div>
                        <div style="font-size: 12px; color: var(--text-muted); margin-top: 4px;">
                            Biaya ongkos kirim per kg agar harga jual FAM Parfum tetap kompetitif.
                        </div>
                    </div>

                    <div style="padding: 12px; background: var(--bg-subtle); border-radius: var(--radius-md); border-left: 4px solid var(--success);">
                        <div style="font-weight: 600; font-size: 13px; color: var(--text-heading); display: flex; align-items: center; justify-content: space-between;">
                            <span>Kriteria Benefit (Manfaat)</span>
                            <span class="badge badge-benefit">Dimaksimalkan</span>
                        </div>
                        <div style="font-size: 12px; color: var(--text-muted); margin-top: 4px;">
                            Kecepatan sampai, perlindungan packing cairan, jangkauan ekspedisi, serta kemudahan pick-up barang.
                        </div>
                    </div>
                </div>

                <div style="margin-top: 20px; padding-top: 16px; border-top: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-size: 12px; color: var(--text-muted);">Format Bobot:</span>
                    <strong style="color: var(--text-heading); font-size: 12.5px;">100% Terdistribusi</strong>
                </div>
            </div>
        </div>
    </div>
</div>
