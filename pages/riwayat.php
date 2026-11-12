<?php
/**
 * Halaman Riwayat Perhitungan SPK MOORA
 * SPK Pemilihan Jasa Pengiriman MOORA - FAM Parfum
 */

$alertMsg = '';
$alertType = '';

// Handle delete history action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'hapus_riwayat') {
    $tglHapus = cleanInput($_POST['tgl_hitung'] ?? '');
    if (!empty($tglHapus)) {
        $stmt = $pdo->prepare("DELETE FROM ranking WHERE tgl_hitung = ?");
        $stmt->execute([$tglHapus]);
        $alertMsg = "Riwayat perhitungan tanggal " . formatTanggalIndo($tglHapus) . " berhasil dihapus!";
        $alertType = 'success';
    }
}

// Fetch distinct calculation dates with summary
$historyDates = $pdo->query("
    SELECT tgl_hitung, COUNT(*) as total_alt, 
           MAX(skor_optimasi) as max_skor
    FROM ranking 
    GROUP BY tgl_hitung 
    ORDER BY tgl_hitung DESC
")->fetchAll();

// Check if a specific date is selected for detail view
$selectedTgl = isset($_GET['tgl']) ? cleanInput($_GET['tgl']) : ($historyDates[0]['tgl_hitung'] ?? null);

$detailRankings = [];
$winner = null;
if ($selectedTgl) {
    $stmt = $pdo->prepare("
        SELECT r.*, e.nama as nama_ekspedisi 
        FROM ranking r 
        JOIN ekspedisi e ON r.id_alternatif = e.id_ekspedisi 
        WHERE r.tgl_hitung = ? 
        ORDER BY r.ranking ASC
    ");
    $stmt->execute([$selectedTgl]);
    $detailRankings = $stmt->fetchAll();
    if (!empty($detailRankings)) {
        $winner = $detailRankings[0];
    }
}
?>

<div class="page-container">
    <div class="page-header">
        <div>
            <h1 class="page-title">Riwayat Perhitungan</h1>
            <p class="page-description">Arsip log dan catatan riwayat analisis penentuan jasa pengiriman FAM Parfum berdasarkan tanggal perhitungan (<code>tgl_hitung</code>).</p>
        </div>
        <div class="page-actions">
            <a href="index.php?page=proses" class="btn btn-primary">
                <i data-lucide="play"></i>
                <span>Hitung Siklus Baru</span>
            </a>
        </div>
    </div>

    <?php if ($alertMsg): ?>
    <div class="alert alert-<?= $alertType ?> alert-dismissible">
        <i data-lucide="<?= $alertType === 'success' ? 'check-circle' : 'alert-triangle' ?>"></i>
        <span><?= htmlspecialchars($alertMsg) ?></span>
    </div>
    <?php endif; ?>

    <div style="display: grid; grid-template-columns: 1.1fr 2fr; gap: 24px;">
        <!-- Left: Daftar Tanggal Perhitungan -->
        <div class="card" style="margin-bottom: 0;">
            <div class="card-header">
                <div>
                    <h3 class="card-title">
                        <i data-lucide="calendar" style="color: var(--primary); width: 18px; height: 18px;"></i>
                        <span>Siklus Perhitungan Tersimpan</span>
                    </h3>
                    <div class="card-subtitle">Pilih tanggal untuk melihat arsip perankingan</div>
                </div>
            </div>
            <div class="card-body" style="padding: 0;">
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Tanggal Hitung</th>
                                <th style="text-align: center;">Alternatif</th>
                                <th style="text-align: center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($historyDates as $h): 
                                $isSelected = ($h['tgl_hitung'] === $selectedTgl);
                            ?>
                            <tr style="<?= $isSelected ? 'background: #EFF6FF;' : '' ?>">
                                <td>
                                    <a href="index.php?page=riwayat&tgl=<?= urlencode($h['tgl_hitung']) ?>" style="display: block; text-decoration: none;">
                                        <div style="font-weight: 600; color: <?= $isSelected ? 'var(--primary)' : 'var(--text-heading)' ?>;">
                                            <?= formatTanggalIndo($h['tgl_hitung']) ?>
                                        </div>
                                        <div style="font-size: 11.5px; color: var(--text-muted);">
                                            <?= $h['tgl_hitung'] ?>
                                        </div>
                                    </a>
                                </td>
                                <td style="text-align: center;">
                                    <span class="badge badge-rank-other"><?= $h['total_alt'] ?> Ekspedisi</span>
                                </td>
                                <td style="text-align: center;">
                                    <div style="display: flex; gap: 4px; justify-content: center;">
                                        <a href="index.php?page=riwayat&tgl=<?= urlencode($h['tgl_hitung']) ?>" class="btn btn-secondary btn-sm" title="Buka Detail">
                                            <i data-lucide="eye" style="width: 13px; height: 13px;"></i>
                                        </a>
                                        <form method="POST" onsubmit="return confirm('Hapus seluruh data perhitungan tanggal <?= formatTanggalIndo($h['tgl_hitung']) ?>?');" style="margin: 0;">
                                            <input type="hidden" name="action" value="hapus_riwayat">
                                            <input type="hidden" name="tgl_hitung" value="<?= htmlspecialchars($h['tgl_hitung']) ?>">
                                            <button type="submit" class="btn btn-danger btn-sm" title="Hapus Riwayat">
                                                <i data-lucide="trash-2" style="width: 13px; height: 13px;"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($historyDates)): ?>
                            <tr>
                                <td colspan="3" style="text-align: center; padding: 24px; color: var(--text-muted);">
                                    Belum ada catatan riwayat perankingan tersimpan.
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right: Detail Perankingan Pada Tanggal Terpilih -->
        <div class="card" style="margin-bottom: 0;">
            <div class="card-header">
                <div>
                    <h3 class="card-title">
                        <i data-lucide="file-text" style="color: var(--primary); width: 18px; height: 18px;"></i>
                        <span>Detail Hasil: <?= $selectedTgl ? formatTanggalIndo($selectedTgl) : '-' ?></span>
                    </h3>
                    <div class="card-subtitle">
                        Rekomendasi Terpilih: <strong><?= $winner ? htmlspecialchars($winner['nama_ekspedisi']) : '-' ?></strong> (Skor: <?= $winner ? number_format($winner['skor_optimasi'], 4) : '-' ?>)
                    </div>
                </div>

                <?php if ($selectedTgl): ?>
                <a href="index.php?page=cetak&tgl=<?= urlencode($selectedTgl) ?>" target="_blank" class="btn btn-secondary btn-sm">
                    <i data-lucide="printer" style="width: 14px; height: 14px;"></i>
                    <span>Cetak Riwayat Ini</span>
                </a>
                <?php endif; ?>
            </div>

            <div class="card-body" style="padding: 0;">
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th style="width: 80px; text-align: center;">Peringkat</th>
                                <th style="width: 110px;">Kode</th>
                                <th>Nama Ekspedisi</th>
                                <th style="text-align: right; width: 140px;">Skor MOORA ($Y_i$)</th>
                                <th style="width: 160px;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($detailRankings as $row): 
                                $isFirst = ($row['ranking'] == 1);
                            ?>
                            <tr class="<?= $isFirst ? 'highlight-rank-1' : '' ?>">
                                <td style="text-align: center;">
                                    <?php if ($row['ranking'] == 1): ?>
                                        <span class="badge badge-rank-1">Rank 1</span>
                                    <?php elseif ($row['ranking'] == 2): ?>
                                        <span class="badge badge-rank-2">Rank 2</span>
                                    <?php elseif ($row['ranking'] == 3): ?>
                                        <span class="badge badge-rank-3">Rank 3</span>
                                    <?php else: ?>
                                        <span style="font-weight: 600; color: var(--text-muted);">#<?= $row['ranking'] ?></span>
                                    <?php endif; ?>
                                </td>
                                <td><strong>A<?= $row['id_alternatif'] ?></strong></td>
                                <td>
                                    <strong><?= htmlspecialchars($row['nama_ekspedisi']) ?></strong>
                                </td>
                                <td style="text-align: right; font-family: monospace; font-weight: 700; color: <?= $isFirst ? '#065F46' : 'var(--primary)' ?>;">
                                    <?= number_format($row['skor_optimasi'], 4) ?>
                                </td>
                                <td>
                                    <?php if ($row['ranking'] == 1): ?>
                                        <span class="badge badge-rank-1">Rekomendasi Utama</span>
                                    <?php elseif ($row['ranking'] <= 3): ?>
                                        <span class="badge badge-benefit">Direkomendasikan</span>
                                    <?php else: ?>
                                        <span class="badge badge-rank-other">Alternatif</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($detailRankings)): ?>
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 24px; color: var(--text-muted);">
                                    Tidak ada data untuk tanggal yang dipilih.
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
