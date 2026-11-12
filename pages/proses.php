<?php
/**
 * Halaman Proses Perhitungan MOORA
 * SPK Pemilihan Jasa Pengiriman MOORA - FAM Parfum
 */

$moora = new MooraHelper($pdo);
$alertMsg = '';
$alertType = '';

// Handle save ranking request
if (isset($_POST['action']) && $_POST['action'] === 'save_ranking') {
    $res = $moora->calculate();
    if ($res['success']) {
        $moora->saveResults($res['ranking']);
        $alertMsg = 'Hasil perhitungan MOORA berhasil disimpan ke basis data tabel ranking!';
        $alertType = 'success';
    } else {
        $alertMsg = 'Gagal menyimpan: ' . $res['message'];
        $alertType = 'danger';
    }
}

$res = $moora->calculate();
$kriteriaList = $res['kriteria'] ?? [];
$ekspedisiList = $res['ekspedisi'] ?? [];
$matrixX = $res['matrixX'] ?? [];
$denominators = $res['denominators'] ?? [];
$matrixR = $res['matrixR'] ?? [];
$weights = $res['weights'] ?? [];
$matrixY = $res['matrixY'] ?? [];
$ranking = $res['ranking'] ?? [];
$totalBobot = $res['totalBobot'] ?? 100;
?>

<div class="page-container">
    <!-- Header Halaman -->
    <div class="page-header">
        <div>
            <h1 class="page-title">Proses MOORA</h1>
            <p class="page-description">Tahapan matematis algoritma Multi-Objective Optimization on the basis of Ratio Analysis secara transparan.</p>
        </div>
        <div class="page-actions">
            <form method="POST" style="margin: 0;">
                <input type="hidden" name="action" value="save_ranking">
                <button type="submit" class="btn btn-primary">
                    <i data-lucide="save"></i>
                    <span>Simpan & Perbarui Ranking ke Database</span>
                </button>
            </form>
            <a href="index.php?page=ranking" class="btn btn-secondary">
                <i data-lucide="award"></i>
                <span>Lihat Hasil Akhir</span>
            </a>
        </div>
    </div>

    <?php if ($alertMsg): ?>
    <div class="alert alert-<?= $alertType ?> alert-dismissible">
        <i data-lucide="<?= $alertType === 'success' ? 'check-circle' : 'alert-triangle' ?>"></i>
        <span><?= htmlspecialchars($alertMsg) ?></span>
    </div>
    <?php endif; ?>

    <!-- Step Wizard Nav -->
    <div class="steps-nav">
        <div class="step-item active" data-step="1" onclick="switchMooraStep(1)">
            <div class="step-number">1</div>
            <div>
                <div style="font-weight: 600;">Matriks Keputusan</div>
                <div style="font-size: 11px;">Nilai Mentah ($X$)</div>
            </div>
        </div>
        <div class="step-item" data-step="2" onclick="switchMooraStep(2)">
            <div class="step-number">2</div>
            <div>
                <div style="font-weight: 600;">Normalisasi Matriks</div>
                <div style="font-size: 11px;">Rasio Pembagi ($R$)</div>
            </div>
        </div>
        <div class="step-item" data-step="3" onclick="switchMooraStep(3)">
            <div class="step-number">3</div>
            <div>
                <div style="font-weight: 600;">Matriks Terbobot</div>
                <div style="font-size: 11px;">Normalisasi & Bobot ($Y$)</div>
            </div>
        </div>
        <div class="step-item" data-step="4" onclick="switchMooraStep(4)">
            <div class="step-number">4</div>
            <div>
                <div style="font-weight: 600;">Nilai Optimasi</div>
                <div style="font-size: 11px;">Benefit - Cost ($Y_i$)</div>
            </div>
        </div>
        <div class="step-item" data-step="5" onclick="switchMooraStep(5)">
            <div class="step-number">5</div>
            <div>
                <div style="font-weight: 600;">Perankingan Akhir</div>
                <div style="font-size: 11px;">Tabel `ranking`</div>
            </div>
        </div>
    </div>

    <!-- ==================== TAHAP 1: MATRIKS KEPUTUSAN (X) ==================== -->
    <div class="moora-step-content" data-step="1">
        <div class="card">
            <div class="card-header">
                <div>
                    <h3 class="card-title">
                        <i data-lucide="grid" style="color: var(--primary); width: 18px; height: 18px;"></i>
                        <span>1. Matriks Keputusan ($X$)</span>
                    </h3>
                    <div class="card-subtitle">Menampilkan nilai mentah dari tabel <code>alternatif</code> berdasarkan <code>id_kriteria</code> dan <code>id_ekspedisi</code></div>
                </div>
                <div style="font-size: 12.5px; color: var(--text-muted);">
                    Formula: $X = [x_{ij}]_{m \times n}$
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th style="width: 70px;">Kode</th>
                                <th>Alternatif Jasa Pengiriman</th>
                                <?php foreach ($kriteriaList as $k): ?>
                                <th style="text-align: center;">
                                    <?= htmlspecialchars($k['kode_kriteria']) ?>
                                    <div style="font-size: 10.5px; font-weight: normal; color: var(--text-muted);"><?= htmlspecialchars($k['nama_kriteria']) ?></div>
                                    <span class="badge <?= strtolower($k['jenis']) == 'cost' ? 'badge-cost' : 'badge-benefit' ?>" style="font-size: 10px;">
                                        <?= htmlspecialchars($k['jenis']) ?> (<?= $k['bobot'] ?>%)
                                    </span>
                                </th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($ekspedisiList as $e): 
                                $idE = $e['id_ekspedisi'];
                            ?>
                            <tr>
                                <td><strong>A<?= $idE ?></strong></td>
                                <td><strong><?= htmlspecialchars($e['nama']) ?></strong></td>
                                <?php foreach ($kriteriaList as $k): 
                                    $idK = $k['id_kriteria'];
                                    $val = isset($matrixX[$idE][$idK]) ? $matrixX[$idE][$idK] : 0;
                                ?>
                                <td style="text-align: center; font-family: monospace; font-size: 13.5px;">
                                    <?= $val ?>
                                </td>
                                <?php endforeach; ?>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer" style="display: flex; justify-content: flex-end;">
                <button type="button" class="btn btn-primary btn-sm" onclick="switchMooraStep(2)">
                    <span>Lanjut ke Langkah 2: Normalisasi Matriks</span>
                    <i data-lucide="arrow-right" style="width: 14px; height: 14px;"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- ==================== TAHAP 2: NORMALISASI MATRIKS (R) ==================== -->
    <div class="moora-step-content" data-step="2" style="display: none;">
        <div class="card">
            <div class="card-header">
                <div>
                    <h3 class="card-title">
                        <i data-lucide="divide" style="color: var(--primary); width: 18px; height: 18px;"></i>
                        <span>2. Normalisasi Matriks ($R$)</span>
                    </h3>
                    <div class="card-subtitle">Perhitungan akar dari jumlah kuadrat nilai per kriteria: $r_{ij} = \frac{x_{ij}}{\sqrt{\sum_{i=1}^m x_{ij}^2}}$</div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th style="width: 70px;">Kode</th>
                                <th>Alternatif Jasa Pengiriman</th>
                                <?php foreach ($kriteriaList as $k): ?>
                                <th style="text-align: center;">
                                    <?= htmlspecialchars($k['kode_kriteria']) ?>
                                    <div style="font-size: 10.5px; font-weight: normal; color: var(--text-muted);"><?= htmlspecialchars($k['nama_kriteria']) ?></div>
                                </th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($ekspedisiList as $e): 
                                $idE = $e['id_ekspedisi'];
                            ?>
                            <tr>
                                <td><strong>A<?= $idE ?></strong></td>
                                <td><?= htmlspecialchars($e['nama']) ?></td>
                                <?php foreach ($kriteriaList as $k): 
                                    $idK = $k['id_kriteria'];
                                    $rVal = isset($matrixR[$idE][$idK]) ? $matrixR[$idE][$idK] : 0;
                                ?>
                                <td style="text-align: center; font-family: monospace; font-size: 13px;">
                                    <?= number_format($rVal, 4) ?>
                                </td>
                                <?php endforeach; ?>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr style="background: var(--bg-subtle); font-weight: 600;">
                                <td colspan="2" style="text-align: right;">
                                    Pembagi $\sqrt{\sum x_{ij}^2}$:
                                </td>
                                <?php foreach ($kriteriaList as $k): 
                                    $idK = $k['id_kriteria'];
                                ?>
                                <td style="text-align: center; font-family: monospace; color: var(--primary); font-weight: 700;">
                                    <?= number_format($denominators[$idK] ?? 0, 4) ?>
                                </td>
                                <?php endforeach; ?>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="card-footer" style="display: flex; justify-content: space-between;">
                <button type="button" class="btn btn-secondary btn-sm" onclick="switchMooraStep(1)">
                    <i data-lucide="arrow-left" style="width: 14px; height: 14px;"></i>
                    <span>Kembali ke Langkah 1</span>
                </button>
                <button type="button" class="btn btn-primary btn-sm" onclick="switchMooraStep(3)">
                    <span>Lanjut ke Langkah 3: Matriks Terbobot</span>
                    <i data-lucide="arrow-right" style="width: 14px; height: 14px;"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- ==================== TAHAP 3: MATRIKS TERBOBOT (Y) ==================== -->
    <div class="moora-step-content" data-step="3" style="display: none;">
        <div class="card">
            <div class="card-header">
                <div>
                    <h3 class="card-title">
                        <i data-lucide="scale" style="color: var(--primary); width: 18px; height: 18px;"></i>
                        <span>3. Matriks Ternormalisasi Terbobot ($Y$)</span>
                    </h3>
                    <div class="card-subtitle">Perkalian elemen matriks normalisasi dengan bobot kriteria: $y_{ij} = r_{ij} \times W_j$</div>
                </div>
                <div style="font-size: 12.5px; color: var(--text-muted);">
                    Total Bobot: <strong><?= $totalBobot ?>%</strong>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th style="width: 70px;">Kode</th>
                                <th>Alternatif Jasa Pengiriman</th>
                                <?php foreach ($kriteriaList as $k): 
                                    $idK = $k['id_kriteria'];
                                ?>
                                <th style="text-align: center;">
                                    <?= htmlspecialchars($k['kode_kriteria']) ?> (W=<?= number_format($weights[$idK] ?? 0, 2) ?>)
                                    <div style="font-size: 10.5px; font-weight: normal; color: var(--text-muted);"><?= htmlspecialchars($k['nama_kriteria']) ?></div>
                                    <span class="badge <?= strtolower($k['jenis']) == 'cost' ? 'badge-cost' : 'badge-benefit' ?>" style="font-size: 9.5px;">
                                        <?= htmlspecialchars($k['jenis']) ?>
                                    </span>
                                </th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($ekspedisiList as $e): 
                                $idE = $e['id_ekspedisi'];
                            ?>
                            <tr>
                                <td><strong>A<?= $idE ?></strong></td>
                                <td><?= htmlspecialchars($e['nama']) ?></td>
                                <?php foreach ($kriteriaList as $k): 
                                    $idK = $k['id_kriteria'];
                                    $yVal = isset($matrixY[$idE][$idK]) ? $matrixY[$idE][$idK] : 0;
                                ?>
                                <td style="text-align: center; font-family: monospace; font-size: 13px;">
                                    <?= number_format($yVal, 4) ?>
                                </td>
                                <?php endforeach; ?>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer" style="display: flex; justify-content: space-between;">
                <button type="button" class="btn btn-secondary btn-sm" onclick="switchMooraStep(2)">
                    <i data-lucide="arrow-left" style="width: 14px; height: 14px;"></i>
                    <span>Kembali ke Langkah 2</span>
                </button>
                <button type="button" class="btn btn-primary btn-sm" onclick="switchMooraStep(4)">
                    <span>Lanjut ke Langkah 4: Nilai Optimasi</span>
                    <i data-lucide="arrow-right" style="width: 14px; height: 14px;"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- ==================== TAHAP 4: NILAI OPTIMASI (Yi) ==================== -->
    <div class="moora-step-content" data-step="4" style="display: none;">
        <div class="card">
            <div class="card-header">
                <div>
                    <h3 class="card-title">
                        <i data-lucide="calculator" style="color: var(--primary); width: 18px; height: 18px;"></i>
                        <span>4. Nilai Optimasi MOORA ($Y_i$)</span>
                    </h3>
                    <div class="card-subtitle">Pengurangan total nilai kriteria <em>Benefit</em> dengan kriteria <em>Cost</em>: $Y_i = \sum_{j \in Benefit} y_{ij} - \sum_{j \in Cost} y_{ij}$</div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th style="width: 70px;">Kode</th>
                                <th>Alternatif Jasa Pengiriman</th>
                                <th style="text-align: right; width: 180px;">Total Benefit ($\sum y_{benefit}$)</th>
                                <th style="text-align: right; width: 180px;">Total Cost ($\sum y_{cost}$)</th>
                                <th style="text-align: right; width: 220px;">Nilai Optimasi ($Y_i$)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($ranking as $idE => $r): ?>
                            <tr>
                                <td><strong>A<?= $idE ?></strong></td>
                                <td><strong><?= htmlspecialchars($r['nama']) ?></strong></td>
                                <td style="text-align: right; font-family: monospace; color: var(--success); font-weight: 600;">
                                    <?= number_format($r['sum_benefit'], 4) ?>
                                </td>
                                <td style="text-align: right; font-family: monospace; color: var(--danger); font-weight: 600;">
                                    <?= number_format($r['sum_cost'], 4) ?>
                                </td>
                                <td style="text-align: right; font-family: monospace; font-size: 14px; font-weight: 700; color: var(--primary);">
                                    <?= number_format($r['skor_optimasi'], 4) ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer" style="display: flex; justify-content: space-between;">
                <button type="button" class="btn btn-secondary btn-sm" onclick="switchMooraStep(3)">
                    <i data-lucide="arrow-left" style="width: 14px; height: 14px;"></i>
                    <span>Kembali ke Langkah 3</span>
                </button>
                <button type="button" class="btn btn-primary btn-sm" onclick="switchMooraStep(5)">
                    <span>Lanjut ke Langkah 5: Perankingan Akhir</span>
                    <i data-lucide="arrow-right" style="width: 14px; height: 14px;"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- ==================== TAHAP 5: PERANKINGAN AKHIR ==================== -->
    <div class="moora-step-content" data-step="5" style="display: none;">
        <div class="card">
            <div class="card-header">
                <div>
                    <h3 class="card-title">
                        <i data-lucide="award" style="color: var(--primary); width: 18px; height: 18px;"></i>
                        <span>5. Hasil Perankingan Akhir</span>
                    </h3>
                    <div class="card-subtitle">Pemetaan skor optimasi $Y_i$ ke tabel perankingan (Tabel <code>ranking</code>)</div>
                </div>
                <form method="POST" style="margin: 0;">
                    <input type="hidden" name="action" value="save_ranking">
                    <button type="submit" class="btn btn-success btn-sm">
                        <i data-lucide="check" style="width: 14px; height: 14px;"></i>
                        <span>Terapkan & Simpan Hasil Ini</span>
                    </button>
                </form>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th style="width: 90px; text-align: center;">Peringkat</th>
                                <th style="width: 120px;">Kode Alternatif</th>
                                <th>Nama Jasa Pengiriman</th>
                                <th style="text-align: right; width: 180px;">Skor Optimasi MOORA ($Y_i$)</th>
                                <th style="width: 220px;">Status Rekomendasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($ranking as $idE => $r): 
                                $isFirst = ($r['ranking'] == 1);
                            ?>
                            <tr class="<?= $isFirst ? 'highlight-rank-1' : '' ?>">
                                <td style="text-align: center;">
                                    <?php if ($r['ranking'] == 1): ?>
                                        <span class="badge badge-rank-1">Rank 1</span>
                                    <?php elseif ($r['ranking'] == 2): ?>
                                        <span class="badge badge-rank-2">Rank 2</span>
                                    <?php elseif ($r['ranking'] == 3): ?>
                                        <span class="badge badge-rank-3">Rank 3</span>
                                    <?php else: ?>
                                        <span style="font-weight: 600; color: var(--text-muted);">#<?= $r['ranking'] ?></span>
                                    <?php endif; ?>
                                </td>
                                <td><strong>A<?= $idE ?></strong></td>
                                <td>
                                    <strong><?= htmlspecialchars($r['nama']) ?></strong>
                                    <?php if ($isFirst): ?>
                                        <span style="font-size: 11px; color: var(--success); font-weight: 600; margin-left: 6px;">(Rekomendasi Utama FAM Parfum)</span>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align: right; font-family: monospace; font-size: 14px; font-weight: 700; color: <?= $isFirst ? '#065F46' : 'var(--primary)' ?>;">
                                    <?= number_format($r['skor_optimasi'], 4) ?>
                                </td>
                                <td>
                                    <?php if ($r['ranking'] == 1): ?>
                                        <span class="badge badge-rank-1">Sangat Direkomendasikan</span>
                                    <?php elseif ($r['ranking'] <= 3): ?>
                                        <span class="badge badge-benefit">Direkomendasikan</span>
                                    <?php else: ?>
                                        <span class="badge badge-rank-other">Alternatif Cadangan</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer" style="display: flex; justify-content: space-between;">
                <button type="button" class="btn btn-secondary btn-sm" onclick="switchMooraStep(4)">
                    <i data-lucide="arrow-left" style="width: 14px; height: 14px;"></i>
                    <span>Kembali ke Langkah 4</span>
                </button>
                <a href="index.php?page=ranking" class="btn btn-primary btn-sm">
                    <span>Lihat Halaman Hasil Perankingan</span>
                    <i data-lucide="arrow-right" style="width: 14px; height: 14px;"></i>
                </a>
            </div>
        </div>
    </div>
</div>
