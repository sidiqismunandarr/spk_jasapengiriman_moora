<?php
/**
 * Halaman Penilaian Alternatif
 * SPK Pemilihan Jasa Pengiriman MOORA - FAM Parfum
 */

$alertMsg = '';
$alertType = '';

// Handle save matrix values
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_matrix') {
    $ratings = $_POST['nilai'] ?? []; // format: [id_mitra => [id_kriteria => nilai]]

    $pdo->beginTransaction();
    try {
        $stmtCheck = $pdo->prepare("SELECT id_alternatif FROM alternatif WHERE id_mitra = ? AND id_kriteria = ?");
        $stmtInsert = $pdo->prepare("INSERT INTO alternatif (id_mitra, id_kriteria, nilai) VALUES (?, ?, ?)");
        $stmtUpdate = $pdo->prepare("UPDATE alternatif SET nilai = ? WHERE id_alternatif = ?");

        foreach ($ratings as $idMitra => $kriteriaVals) {
            foreach ($kriteriaVals as $idKriteria => $val) {
                $valInt = (int)$val;
                $stmtCheck->execute([$idMitra, $idKriteria]);
                $existingId = $stmtCheck->fetchColumn();

                if ($existingId) {
                    $stmtUpdate->execute([$valInt, $existingId]);
                } else {
                    $stmtInsert->execute([$idMitra, $idKriteria, $valInt]);
                }
            }
        }
        $pdo->commit();
        $alertMsg = "Matriks penilaian alternatif ekspedisi berhasil diperbarui!";
        $alertType = 'success';
    } catch (Exception $e) {
        $pdo->rollBack();
        $alertMsg = "Gagal memperbarui nilai: " . $e->getMessage();
        $alertType = 'danger';
    }
}

// Fetch lists
$ekspedisiList = $pdo->query("SELECT * FROM ekspedisi ORDER BY id_ekspedisi ASC")->fetchAll();
$kriteriaList = $pdo->query("SELECT * FROM kriteria ORDER BY id_kriteria ASC")->fetchAll();

// Fetch current matrix
$altRows = $pdo->query("SELECT * FROM alternatif")->fetchAll();
$matrix = [];
foreach ($altRows as $row) {
    $matrix[$row['id_mitra']][$row['id_kriteria']] = (int)$row['nilai'];
}
?>

<div class="page-container">
    <div class="page-header">
        <div>
            <h1 class="page-title">Penilaian Alternatif</h1>
            <p class="page-description">Input dan perbarui data nilai mentah evaluasi ekspedisi untuk setiap kriteria penilaian (Tabel <code>alternatif</code>).</p>
        </div>
        <div class="page-actions">
            <a href="index.php?page=proses" class="btn btn-secondary">
                <i data-lucide="calculator"></i>
                <span>Jalankan Perhitungan MOORA</span>
            </a>
        </div>
    </div>

    <?php if ($alertMsg): ?>
    <div class="alert alert-<?= $alertType ?> alert-dismissible">
        <i data-lucide="<?= $alertType === 'success' ? 'check-circle' : 'alert-triangle' ?>"></i>
        <span><?= htmlspecialchars($alertMsg) ?></span>
    </div>
    <?php endif; ?>

    <!-- Penjelasan Skala Penilaian -->
    <div style="background: #FFFFFF; border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 16px 20px; margin-bottom: 24px;">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; margin-bottom: 12px;">
            <div style="font-size: 13.5px; font-weight: 600; color: var(--text-heading); display: flex; align-items: center; gap: 8px;">
                <i data-lucide="help-circle" style="width: 16px; height: 16px; color: var(--primary);"></i>
                <span>Panduan Skala Penilaian Alternatif (Skala 1 - 5)</span>
            </div>
            <span style="font-size: 12px; color: var(--text-muted);">Nilai standar operasional FAM Parfum</span>
        </div>
        <div style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 12px; font-size: 12px;">
            <div style="padding: 8px 12px; background: var(--bg-subtle); border-radius: var(--radius-sm); border-left: 3px solid #EF4444;">
                <strong>1: Sangat Kurang</strong>
                <div style="color: var(--text-muted); font-size: 11px;">Biaya sangat mahal / Layanan sangat buruk</div>
            </div>
            <div style="padding: 8px 12px; background: var(--bg-subtle); border-radius: var(--radius-sm); border-left: 3px solid #F97316;">
                <strong>2: Kurang</strong>
                <div style="color: var(--text-muted); font-size: 11px;">Biaya mahal / Pengiriman lambat</div>
            </div>
            <div style="padding: 8px 12px; background: var(--bg-subtle); border-radius: var(--radius-sm); border-left: 3px solid #EAB308;">
                <strong>3: Cukup / Sedang</strong>
                <div style="color: var(--text-muted); font-size: 11px;">Standar normal industri</div>
            </div>
            <div style="padding: 8px 12px; background: var(--bg-subtle); border-radius: var(--radius-sm); border-left: 3px solid #10B981;">
                <strong>4: Baik</strong>
                <div style="color: var(--text-muted); font-size: 11px;">Cepat, aman, jangkauan luas</div>
            </div>
            <div style="padding: 8px 12px; background: var(--bg-subtle); border-radius: var(--radius-sm); border-left: 3px solid #2563EB;">
                <strong>5: Sangat Baik</strong>
                <div style="color: var(--text-muted); font-size: 11px;">Sangat cepat, garansi cairan aman</div>
            </div>
        </div>
    </div>

    <!-- Form Matriks Penilaian -->
    <div class="card">
        <form method="POST">
            <input type="hidden" name="action" value="save_matrix">
            <div class="card-header">
                <div>
                    <h3 class="card-title">
                        <i data-lucide="edit" style="color: var(--primary); width: 18px; height: 18px;"></i>
                        <span>Matriks Nilai Evaluasi ($X$)</span>
                    </h3>
                    <div class="card-subtitle">Ubah nilai penilaian langsung pada tabel dan klik tombol simpan</div>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i data-lucide="save"></i>
                    <span>Simpan Perubahan Matriks</span>
                </button>
            </div>

            <div class="card-body" style="padding: 0;">
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th style="width: 80px;">Kode</th>
                                <th style="min-width: 180px;">Jasa Pengiriman (Mitra)</th>
                                <?php foreach ($kriteriaList as $k): ?>
                                <th style="text-align: center; min-width: 140px;">
                                    <div style="font-weight: 700; color: var(--navy-dark);"><?= htmlspecialchars($k['kode_kriteria']) ?></div>
                                    <div style="font-size: 11px; font-weight: normal; color: var(--text-muted);"><?= htmlspecialchars($k['nama_kriteria']) ?></div>
                                    <span class="badge <?= strtolower($k['jenis']) == 'cost' ? 'badge-cost' : 'badge-benefit' ?>" style="font-size: 9.5px; margin-top: 2px;">
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
                                <td>
                                    <span style="font-family: monospace; font-weight: 600; padding: 2px 6px; background: var(--bg-subtle); border-radius: var(--radius-sm); border: 1px solid var(--border-color);">
                                        A<?= $idE ?>
                                    </span>
                                </td>
                                <td>
                                    <strong style="color: var(--text-heading); font-size: 13.5px;">
                                        <?= htmlspecialchars($e['nama']) ?>
                                    </strong>
                                </td>
                                <?php foreach ($kriteriaList as $k): 
                                    $idK = $k['id_kriteria'];
                                    $val = isset($matrix[$idE][$idK]) ? $matrix[$idE][$idK] : 3;
                                ?>
                                <td style="text-align: center;">
                                    <input type="number" 
                                           name="nilai[<?= $idE ?>][<?= $idK ?>]" 
                                           value="<?= $val ?>" 
                                           min="1" 
                                           max="5" 
                                           class="form-control" 
                                           style="width: 80px; margin: 0 auto; text-align: center; font-weight: 600; font-family: monospace;" 
                                           required>
                                </td>
                                <?php endforeach; ?>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($ekspedisiList)): ?>
                            <tr>
                                <td colspan="<?= count($kriteriaList) + 2 ?>" style="text-align: center; padding: 24px; color: var(--text-muted);">
                                    Belum ada data ekspedisi. Tambahkan di menu Data Jasa Pengiriman terlebih dahulu.
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card-footer" style="display: flex; justify-content: space-between; align-items: center;">
                <span style="font-size: 12.5px; color: var(--text-muted);">
                    Data nilai yang tersimpan akan langsung dijadikan variabel perhitungan matriks keputusan metode MOORA.
                </span>
                <button type="submit" class="btn btn-primary">
                    <i data-lucide="check"></i>
                    <span>Simpan Nilai Alternatif</span>
                </button>
            </div>
        </form>
    </div>
</div>
