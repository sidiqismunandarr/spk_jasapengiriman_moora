<?php
/**
 * Halaman Data Kriteria Penilaian
 * SPK Pemilihan Jasa Pengiriman MOORA - FAM Parfum
 */

$alertMsg = '';
$alertType = '';

// Handle CRUD operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? cleanInput($_POST['action']) : '';

    if ($action === 'tambah') {
        $kode = strtoupper(cleanInput($_POST['kode_kriteria'] ?? ''));
        $nama = cleanInput($_POST['nama_kriteria'] ?? '');
        $bobot = (int)($_POST['bobot'] ?? 0);
        $jenis = ucfirst(strtolower(cleanInput($_POST['jenis'] ?? 'Benefit')));

        if (!empty($kode) && !empty($nama) && $bobot > 0) {
            $stmt = $pdo->prepare("INSERT INTO kriteria (kode_kriteria, nama_kriteria, bobot, jenis) VALUES (?, ?, ?, ?)");
            $stmt->execute([$kode, $nama, $bobot, $jenis]);
            $newKriteriaId = $pdo->lastInsertId();

            // Populate default rating 3 for all existing expeditions
            $ekspedisiList = $pdo->query("SELECT id_ekspedisi FROM ekspedisi")->fetchAll();
            $stmtAlt = $pdo->prepare("INSERT INTO alternatif (id_mitra, id_kriteria, nilai) VALUES (?, ?, ?)");
            foreach ($ekspedisiList as $e) {
                $stmtAlt->execute([$e['id_ekspedisi'], $newKriteriaId, 3]);
            }

            $alertMsg = "Kriteria baru [{$kode}] {$nama} berhasil ditambahkan!";
            $alertType = 'success';
        } else {
            $alertMsg = "Semua field kriteria wajib diisi dan bobot harus lebih besar dari 0.";
            $alertType = 'danger';
        }
    } elseif ($action === 'edit') {
        $id = (int)($_POST['id_kriteria'] ?? 0);
        $kode = strtoupper(cleanInput($_POST['kode_kriteria'] ?? ''));
        $nama = cleanInput($_POST['nama_kriteria'] ?? '');
        $bobot = (int)($_POST['bobot'] ?? 0);
        $jenis = ucfirst(strtolower(cleanInput($_POST['jenis'] ?? 'Benefit')));

        if ($id > 0 && !empty($kode) && !empty($nama) && $bobot > 0) {
            $stmt = $pdo->prepare("UPDATE kriteria SET kode_kriteria = ?, nama_kriteria = ?, bobot = ?, jenis = ? WHERE id_kriteria = ?");
            $stmt->execute([$kode, $nama, $bobot, $jenis, $id]);
            $alertMsg = "Data kriteria berhasil diperbarui!";
            $alertType = 'success';
        }
    } elseif ($action === 'hapus') {
        $id = (int)($_POST['id_kriteria'] ?? 0);
        if ($id > 0) {
            $pdo->prepare("DELETE FROM alternatif WHERE id_kriteria = ?")->execute([$id]);
            $pdo->prepare("DELETE FROM kriteria WHERE id_kriteria = ?")->execute([$id]);
            $alertMsg = "Kriteria berhasil dihapus!";
            $alertType = 'success';
        }
    }
}

// Fetch all criteria
$stmt = $pdo->query("SELECT * FROM kriteria ORDER BY id_kriteria ASC");
$kriteriaList = $stmt->fetchAll();

$totalBobot = 0;
foreach ($kriteriaList as $k) {
    $totalBobot += (int)$k['bobot'];
}
?>

<div class="page-container">
    <div class="page-header">
        <div>
            <h1 class="page-title">Data Kriteria Penilaian</h1>
            <p class="page-description">Kelola bobot dan jenis kriteria (Benefit / Cost) untuk penentuan jasa pengiriman terbaik FAM Parfum (Tabel <code>kriteria</code>).</p>
        </div>
        <div class="page-actions">
            <button type="button" class="btn btn-primary" onclick="openModal('modalTambahKriteria')">
                <i data-lucide="plus"></i>
                <span>Tambah Kriteria</span>
            </button>
        </div>
    </div>

    <?php if ($alertMsg): ?>
    <div class="alert alert-<?= $alertType ?> alert-dismissible">
        <i data-lucide="<?= $alertType === 'success' ? 'check-circle' : 'alert-triangle' ?>"></i>
        <span><?= htmlspecialchars($alertMsg) ?></span>
    </div>
    <?php endif; ?>

    <!-- Status Bobot Info Bar -->
    <div style="background: #FFFFFF; border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 16px 20px; margin-bottom: 24px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
        <div style="display: flex; align-items: center; gap: 14px;">
            <div style="width: 40px; height: 40px; border-radius: var(--radius-md); background: <?= $totalBobot == 100 ? 'var(--success-bg)' : 'var(--warning-bg)' ?>; color: <?= $totalBobot == 100 ? 'var(--success)' : 'var(--warning)' ?>; display: flex; align-items: center; justify-content: center;">
                <i data-lucide="<?= $totalBobot == 100 ? 'check-circle-2' : 'alert-circle' ?>"></i>
            </div>
            <div>
                <div style="font-size: 14px; font-weight: 600; color: var(--text-heading);">
                    Akumulasi Total Bobot Kriteria: 
                    <span style="color: <?= $totalBobot == 100 ? 'var(--success)' : 'var(--warning)' ?>;"><?= $totalBobot ?>%</span>
                </div>
                <div style="font-size: 12px; color: var(--text-muted);">
                    <?= $totalBobot == 100 ? 'Distribusi bobot kriteria sudah sempurna (100%).' : 'Disarankan total bobot berjumlah 100% untuk proporsi standar.' ?>
                </div>
            </div>
        </div>

        <div style="display: flex; gap: 10px;">
            <span class="badge badge-cost">Cost = Minimal</span>
            <span class="badge badge-benefit">Benefit = Maksimal</span>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div>
                <h3 class="card-title">
                    <i data-lucide="sliders" style="color: var(--primary); width: 18px; height: 18px;"></i>
                    <span>Tabel Kriteria Penilaian MOORA</span>
                </h3>
                <div class="card-subtitle">Daftar variabel penentu keputusan logistik FAM Parfum</div>
            </div>
            <span class="badge badge-rank-other">Total: <?= count($kriteriaList) ?> Kriteria</span>
        </div>

        <div class="card-body" style="padding: 0;">
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width: 80px;">No</th>
                            <th style="width: 120px;">Kode</th>
                            <th>Nama Kriteria</th>
                            <th style="width: 140px; text-align: center;">Bobot (%)</th>
                            <th style="width: 160px; text-align: center;">Jenis Sifat</th>
                            <th style="width: 180px;">Karakteristik Operasional</th>
                            <th style="width: 130px; text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        foreach ($kriteriaList as $row): 
                            $isCost = (strtolower($row['jenis']) === 'cost');
                        ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td>
                                <span style="font-family: monospace; font-weight: 700; color: var(--navy-dark); padding: 2px 8px; background: var(--bg-subtle); border-radius: var(--radius-sm); border: 1px solid var(--border-color);">
                                    <?= htmlspecialchars($row['kode_kriteria']) ?>
                                </span>
                            </td>
                            <td>
                                <strong style="font-size: 13.5px; color: var(--text-heading);">
                                    <?= htmlspecialchars($row['nama_kriteria']) ?>
                                </strong>
                            </td>
                            <td style="text-align: center; font-weight: 600; font-size: 14px; color: var(--primary);">
                                <?= $row['bobot'] ?>%
                            </td>
                            <td style="text-align: center;">
                                <?php if ($isCost): ?>
                                    <span class="badge badge-cost">
                                        <i data-lucide="arrow-down" style="width: 11px; height: 11px;"></i>
                                        <span>Cost (Biaya)</span>
                                    </span>
                                <?php else: ?>
                                    <span class="badge badge-benefit">
                                        <i data-lucide="arrow-up" style="width: 11px; height: 11px;"></i>
                                        <span>Benefit (Manfaat)</span>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td style="font-size: 12px; color: var(--text-muted);">
                                <?= $isCost ? 'Nilai semakin rendah semakin baik (biaya efisien)' : 'Nilai semakin tinggi semakin menguntungkan' ?>
                            </td>
                            <td style="text-align: center;">
                                <div style="display: flex; gap: 6px; justify-content: center;">
                                    <button type="button" class="btn btn-secondary btn-sm" onclick="editKriteria(<?= $row['id_kriteria'] ?>, '<?= htmlspecialchars(addslashes($row['kode_kriteria'])) ?>', '<?= htmlspecialchars(addslashes($row['nama_kriteria'])) ?>', <?= $row['bobot'] ?>, '<?= htmlspecialchars(addslashes($row['jenis'])) ?>')" title="Edit Kriteria">
                                        <i data-lucide="edit-3" style="width: 13px; height: 13px;"></i>
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm" onclick="hapusKriteria(<?= $row['id_kriteria'] ?>, '<?= htmlspecialchars(addslashes($row['nama_kriteria'])) ?>')" title="Hapus Kriteria">
                                        <i data-lucide="trash-2" style="width: 13px; height: 13px;"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($kriteriaList)): ?>
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 24px; color: var(--text-muted);">
                                Belum ada data kriteria penilaian. Silakan tambah data.
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Kriteria -->
<div id="modalTambahKriteria" class="modal-overlay">
    <div class="modal-content">
        <form method="POST">
            <input type="hidden" name="action" value="tambah">
            <div class="modal-header">
                <h3 class="modal-title">Tambah Kriteria Baru</h3>
                <button type="button" class="modal-close" onclick="closeModal('modalTambahKriteria')">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Kode Kriteria</label>
                    <input type="text" name="kode_kriteria" class="form-control" placeholder="Contoh: C6" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Nama Kriteria</label>
                    <input type="text" name="nama_kriteria" class="form-control" placeholder="Contoh: Customer Service 24 Jam" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Bobot Nilai (%)</label>
                    <input type="number" name="bobot" class="form-control" placeholder="10" min="1" max="100" required>
                    <div class="form-text">Persentase bobot dalam pengambilan keputusan MOORA.</div>
                </div>
                <div class="form-group">
                    <label class="form-label">Jenis Sifat Kriteria</label>
                    <select name="jenis" class="form-control" required>
                        <option value="Benefit">Benefit (Semakin tinggi semakin baik)</option>
                        <option value="Cost">Cost (Semakin rendah semakin baik / biaya)</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalTambahKriteria')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Kriteria</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Kriteria -->
<div id="modalEditKriteria" class="modal-overlay">
    <div class="modal-content">
        <form method="POST">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id_kriteria" id="edit_id_kriteria">
            <div class="modal-header">
                <h3 class="modal-title">Edit Kriteria</h3>
                <button type="button" class="modal-close" onclick="closeModal('modalEditKriteria')">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Kode Kriteria</label>
                    <input type="text" name="kode_kriteria" id="edit_kode_kriteria" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Nama Kriteria</label>
                    <input type="text" name="nama_kriteria" id="edit_nama_kriteria" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Bobot Nilai (%)</label>
                    <input type="number" name="bobot" id="edit_bobot" class="form-control" min="1" max="100" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Jenis Sifat Kriteria</label>
                    <select name="jenis" id="edit_jenis" class="form-control" required>
                        <option value="Benefit">Benefit</option>
                        <option value="Cost">Cost</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalEditKriteria')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Hapus Kriteria -->
<div id="modalHapusKriteria" class="modal-overlay">
    <div class="modal-content">
        <form method="POST">
            <input type="hidden" name="action" value="hapus">
            <input type="hidden" name="id_kriteria" id="hapus_id_kriteria">
            <div class="modal-header">
                <h3 class="modal-title" style="color: var(--danger);">Konfirmasi Hapus Kriteria</h3>
                <button type="button" class="modal-close" onclick="closeModal('modalHapusKriteria')">&times;</button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus kriteria <strong id="hapus_kriteria_display"></strong>?</p>
                <p style="font-size: 12.5px; color: var(--danger); margin-top: 8px;">
                    Perhatian: Nilai alternatif untuk kriteria ini pada seluruh ekspedisi akan terhapus.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalHapusKriteria')">Batal</button>
                <button type="submit" class="btn btn-danger" style="background: var(--danger); color: #FFF;">Ya, Hapus Kriteria</button>
            </div>
        </form>
    </div>
</div>

<script>
function editKriteria(id, kode, nama, bobot, jenis) {
    document.getElementById('edit_id_kriteria').value = id;
    document.getElementById('edit_kode_kriteria').value = kode;
    document.getElementById('edit_nama_kriteria').value = nama;
    document.getElementById('edit_bobot').value = bobot;
    document.getElementById('edit_jenis').value = jenis;
    openModal('modalEditKriteria');
}

function hapusKriteria(id, nama) {
    document.getElementById('hapus_id_kriteria').value = id;
    document.getElementById('hapus_kriteria_display').innerText = nama;
    openModal('modalHapusKriteria');
}
</script>
