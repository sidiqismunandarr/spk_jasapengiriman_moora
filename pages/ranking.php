<?php
$moora = new MooraHelper($pdo);

// Check if specific date requested, else latest
$tglFilter = isset($_GET['tgl']) ? cleanInput($_GET['tgl']) : null;
$rankings = $moora->getStoredRanking($tglFilter);

// If empty, auto calculate once
if (empty($rankings)) {
    $calcResult = $moora->calculate();
    if ($calcResult['success']) {
        $moora->saveResults($calcResult['ranking']);
        $rankings = $moora->getStoredRanking();
    }
}

$topOne = !empty($rankings) ? $rankings[0] : null;
$totalEvaluated = count($rankings);
$tglHitung = $topOne ? $topOne['tgl_hitung'] : date('Y-m-d');
?>

<div class="page-container">
    <div class="page-header">
        <div>
            <h1 class="page-title">Hasil Perankingan</h1>
        </div>
        <div class="page-actions">
            <a href="index.php?page=cetak<?= $tglFilter ? '&tgl=' . urlencode($tglFilter) : '' ?>" target="_blank" class="btn btn-primary">
                <span>Cetak Hasil</span>
            </a>
            <a href="index.php?page=proses" class="btn btn-secondary">
                <span>Lihat Detail Perhitungan</span>
            </a>
        </div>
    </div>

    <?php if ($topOne): ?>
    <div class="hero-card" style="background: linear-gradient(to right, #FFFFFF, #F8FAFC);">
        <div class="hero-grid">
            <div>
                <div style="display: flex; align-items: baseline; gap: 12px; margin-bottom: 8px;">
                    <span class="badge badge-rank-1" style="font-size: 13px; padding: 4px 12px;">Rank 1</span>
                    <h2 class="hero-title" style="margin: 0; font-size: 24px; color: var(--navy-dark);">
                        <?= htmlspecialchars($topOne['nama_ekspedisi']) ?>
                    </h2>
                </div>

                <p class="hero-desc">
                    Jasa pengiriman <strong><?= htmlspecialchars($topOne['nama_ekspedisi']) ?></strong> menempati posisi teratas dengan efisiensi cost yang paling optimal dan skor utilitas tertinggi pada seluruh kriteria. 
                </p>

                <div class="hero-stats-row">
                    <div class="hero-stat-item">
                        <span class="hero-stat-label">Alternatif Dievaluasi</span>
                        <span class="hero-stat-val"><?= $totalEvaluated ?> Jasa Pengiriman</span>
                    </div>
                    <div class="hero-stat-item">
                        <span class="hero-stat-label">Tanggal Perhitungan</span>
                        <span class="hero-stat-val"><?= formatTanggalIndo($tglHitung) ?></span>
                    </div>
                    <div class="hero-stat-item">
                        <span class="hero-stat-label">Status Kelayakan</span>
                        <span class="badge badge-benefit" style="margin-top: 4px;">Sangat Direkomendasikan</span>
                    </div>
                </div>
            </div>

            <div>
                <div class="hero-score-badge" style="background: #FFFFFF; box-shadow: var(--shadow-sm); border: 2px solid var(--primary-border);">
                    <div class="hero-score-lbl" style="color: var(--primary);">Skor Optimasi MOORA</div>
                    <div class="hero-score-val" style="color: var(--primary);"><?= number_format($topOne['skor_optimasi'], 4) ?></div>
                    <div style="font-size: 11.5px; color: var(--text-muted); margin-bottom: 12px;">Nilai Maksimum Evaluasi</div>
                    <a href="index.php?page=proses" class="btn btn-primary btn-sm" style="width: 100%;">
                        <i data-lucide="eye" style="width: 14px; height: 14px;"></i>
                        <span>Lihat Detail Perhitungan</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header">
            <div>
                <h3 class="card-title">
                    <i data-lucide="list-ordered" style="color: var(--primary); width: 18px; height: 18px;"></i>
                    <span>Tabel Perankingan Alternatif Ekspedisi</span>
            </div>
        </div>

        <div class="card-body">
            <div class="filter-bar">
                <div class="search-input-wrap">
                    <i data-lucide="search" class="search-icon"></i>
                    <input type="text" class="form-control" placeholder="Cari nama ekspedisi..." data-table-search="rankingTable">
                </div>
            </div>

            <div class="table-responsive">
                <table class="data-table" id="rankingTable">
                    <thead>
                        <tr>
                            <th style="width: 90px; text-align: center;">Peringkat</th>
                            <th style="width: 130px;">Kode Alternatif</th>
                            <th>Nama Jasa Pengiriman</th>
                            <th style="text-align: right; width: 200px;">Nilai Optimasi MOORA</th>
                            <th style="width: 200px;">Status Rekomendasi</th>
                            <th style="width: 120px; text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        foreach ($rankings as $row): 
                            $isRank1 = ($row['ranking'] == 1);
                            $kodeAlt = 'A' . $row['id_alternatif'];
                            
                            // Status classification
                            if ($row['ranking'] == 1) {
                                $statusBadge = '<span class="badge badge-rank-1">Sangat Direkomendasikan</span>';
                            } elseif ($row['ranking'] <= 10) {
                                $statusBadge = '<span class="badge badge-benefit">Direkomendasikan</span>';
                            } elseif ($row['ranking'] <= 25) {
                                $statusBadge = '<span class="badge badge-benefit">Cukup</span>';
                            } else {
                                $statusBadge = '<span class="badge badge-cost">Tidak direkomendasikan</span>';
                            }
                        ?>
                        <tr class="<?= $isRank1 ? 'highlight-rank-1' : '' ?>">
                            <td style="text-align: center;">
                                <?php if ($row['ranking'] == 1): ?>
                                    <span style="font-weight: 600; color: var(--text-muted); font-size: 13px;">1</span>
                                <?php else: ?>
                                    <span style="font-weight: 600; color: var(--text-muted); font-size: 13px;"><?= $row['ranking'] ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span style="font-family: monospace; font-size: 12.5px; font-weight: 600; padding: 2px 6px; background: var(--bg-subtle); border-radius: var(--radius-sm); border: 1px solid var(--border-color);">
                                    <?= $kodeAlt ?>
                                </span>
                            </td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <strong style="color: <?= $isRank1 ? '#065F46' : 'var(--text-heading)' ?>; font-size: 14px;">
                                        <?= htmlspecialchars($row['nama_ekspedisi']) ?>
                                    </strong>
                                </div>
                            </td>
                            <td style="text-align: center; font-size: 14px; font-family: monospace; font-weight: 700; color: <?= $isRank1 ? '#065F46' : 'var(--primary)' ?>;">
                                <?= number_format($row['skor_optimasi'], 4) ?>
                            </td>
                            <td>
                                <?= $statusBadge ?>
                            </td>
                            <td style="text-align: center;">
                                <a href="index.php?page=penilaian" class="btn btn-secondary btn-sm" title="Lihat Nilai Alternatif">
                                    <i data-lucide="sliders" style="width: 13px; height: 13px;"></i>
                                    <span>Nilai</span>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-footer" style="display: flex; justify-content: space-between; align-items: center;">
            <a href="index.php?page=cetak<?= $tglFilter ? '&tgl=' . urlencode($tglFilter) : '' ?>" target="_blank" class="btn btn-secondary btn-sm">
                <i data-lucide="printer" style="width: 14px; height: 14px;"></i>
                <span>Cetak Lembar Laporan</span>
            </a>
        </div>
    </div>

 
</div>
