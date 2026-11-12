<?php
/**
 * Halaman Data Jasa Pengiriman (Ekspedisi)
 * SPK Pemilihan Jasa Pengiriman MOORA - FAM Parfum
 */

$alertMsg = '';
$alertType = '';

// Handle CRUD operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? cleanInput($_POST['action']) : '';

    if ($action === 'tambah') {
        $nama = cleanInput($_POST['nama'] ?? '');
        if (!empty($nama)) {
            $stmt = $pdo->prepare("INSERT INTO ekspedisi (nama) VALUES (?)");
            $stmt->execute([$nama]);
            $newId = $pdo->lastInsertId();

            // Insert initial default ratings for this new courier across all existing criteria
            $kriteriaList = $pdo->query("SELECT id_kriteria FROM kriteria")->fetchAll();
            $stmtAlt = $pdo->prepare("INSERT INTO alternatif (id_mitra, id_kriteria, nilai) VALUES (?, ?, ?)");
            foreach ($kriteriaList as $k) {
                $stmtAlt->execute([$newId, $k['id_kriteria'], 3]); // default score 3
            }

            $alertMsg = "Jasa pengiriman '{$nama}' berhasil ditambahkan ke sistem!";
            $alertType = 'success';
        } else {
            $alertMsg = "Nama jasa pengiriman tidak boleh kosong.";
            $alertType = 'danger';
        }
    } elseif ($action === 'edit') {
        $id = (int)($_POST['id_ekspedisi'] ?? 0);
        $nama = cleanInput($_POST['nama'] ?? '');
        if ($id > 0 && !empty($nama)) {
            $stmt = $pdo->prepare("UPDATE ekspedisi SET nama = ? WHERE id_ekspedisi = ?");
            $stmt->execute([$nama, $id]);
            $alertMsg = "Data ekspedisi berhasil diperbarui!";
            $alertType = 'success';
        }
    } elseif ($action === 'hapus') {
        $id = (int)($_POST['id_ekspedisi'] ?? 0);
        if ($id > 0) {
            // Delete from alternatif, ranking, and ekspedisi
            $pdo->prepare("DELETE FROM alternatif WHERE id_mitra = ?")->execute([$id]);
            $pdo->prepare("DELETE FROM ranking WHERE id_alternatif = ?")->execute([$id]);
            $pdo->prepare("DELETE FROM ekspedisi WHERE id_ekspedisi = ?")->execute([$id]);
            $alertMsg = "Jasa pengiriman berhasil dihapus!";
            $alertType = 'success';
        }
    }
}

// Fetch all expeditions
$stmt = $pdo->query("SELECT * FROM ekspedisi ORDER BY id_ekspedisi ASC");
$ekspedisiList = $stmt->fetchAll();
?>

<div class="page-container">
    <div class="page-header">
        <div>
            <h1 class="page-title">Data Jasa Pengiriman</h1>
            <p class="page-description">Kelola data mitra jasa ekspedisi/kurir yang dievaluasi untuk pengiriman FAM Parfum (Tabel <code>ekspedisi</code>).</p>
        </div>
        <div class="page-actions">
            <button type="button" class="btn btn-primary" onclick="openModal('modalTambah')">
                <i data-lucide="plus"></i>
                <span>Tambah Ekspedisi</span>
            </button>
        </div>
    </div>

    <?php if ($alertMsg): ?>
    <div class="alert alert-<?= $alertType ?> alert-dismissible">
        <i data-lucide="<?= $alertType === 'success' ? 'check-circle' : 'alert-triangle' ?>"></i>
        <span><?= htmlspecialchars($alertMsg) ?></span>
    </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header">
            <div>
                <h3 class="card-title">
                    <i data-lucide="truck" style="color: var(--primary); width: 18px; height: 18px;"></i>
                    <span>Daftar Ekspedisi Terdaftar</span>
                </h3>
                <div class="card-subtitle">Total: <?= count($ekspedisiList) ?> Mitra Jasa Pengiriman</div>
            </div>
            <div class="search-input-wrap">
                <i data-lucide="search" class="search-icon"></i>
                <input type="text" class="form-control" placeholder="Cari ekspedisi..." data-table-search="ekspedisiTable">
            </div>
        </div>

        <div class="card-body" style="padding: 0;">
            <div class="table-responsive">
                <table class="data-table" id="ekspedisiTable">
                    <thead>
                        <tr>
                            <th style="width: 80px;">ID</th>
                            <th style="width: 130px;">Kode Alternatif</th>
                            <th>Nama Jasa Pengiriman</th>
                            <th style="width: 180px; text-align: center;">Status Penilaian</th>
                            <th style="width: 140px; text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ekspedisiList as $row): 
                            // Check if ratings exist for this courier
                            $cntStmt = $pdo->prepare("SELECT COUNT(*) FROM alternatif WHERE id_mitra = ?");
                            $cntStmt->execute([$row['id_ekspedisi']]);
                            $hasRatings = ($cntStmt->fetchColumn() > 0);
                        ?>
                        <tr>
                            <td>#<?= $row['id_ekspedisi'] ?></td>
                            <td>
                                <span style="font-family: monospace; font-weight: 600; padding: 2px 8px; background: var(--bg-subtle); border-radius: var(--radius-sm); border: 1px solid var(--border-color);">
                                    A<?= $row['id_ekspedisi'] ?>
                                </span>
                            </td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <i data-lucide="package" style="width: 16px; height: 16px; color: var(--primary);"></i>
                                    <strong style="font-size: 14px; color: var(--text-heading);">
                                        <?= htmlspecialchars($row['nama']) ?>
                                    </strong>
                                </div>
                            </td>
                            <td style="text-align: center;">
                                <?php if ($hasRatings): ?>
                                    <span class="badge badge-benefit">
                                        <i data-lucide="check" style="width: 12px; height: 12px;"></i>
                                        <span>Sudah Dinilai</span>
                                    </span>
                                <?php else: ?>
                                    <span class="badge badge-cost">
                                        <i data-lucide="alert-circle" style="width: 12px; height: 12px;"></i>
                                        <span>Belum Ada Nilai</span>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: center;">
                                <div style="display: flex; gap: 6px; justify-content: center;">
                                    <button type="button" class="btn btn-secondary btn-sm" onclick="editEkspedisi(<?= $row['id_ekspedisi'] ?>, '<?= htmlspecialchars(addslashes($row['nama'])) ?>')" title="Edit">
                                        <i data-lucide="edit-3" style="width: 13px; height: 13px;"></i>
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm" onclick="hapusEkspedisi(<?= $row['id_ekspedisi'] ?>, '<?= htmlspecialchars(addslashes($row['nama'])) ?>')" title="Hapus">
                                        <i data-lucide="trash-2" style="width: 13px; height: 13px;"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($ekspedisiList)): ?>
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 24px; color: var(--text-muted);">
                                Belum ada data ekspedisi. Silakan tambah data baru.
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Ekspedisi -->
<div id="modalTambah" class="modal-overlay">
    <div class="modal-content">
        <form method="POST">
            <input type="hidden" name="action" value="tambah">
            <div class="modal-header">
                <h3 class="modal-title">Tambah Jasa Pengiriman Baru</h3>
                <button type="button" class="modal-close" onclick="closeModal('modalTambah')">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Nama Jasa Pengiriman / Ekspedisi</label>
                    <input type="text" name="nama" class="form-control" placeholder="Contoh: J&T Cargo, Paxel, dll." required autofocus>
                    <div class="form-text">Masukkan nama resmi ekspedisi atau layanan kurir mitra FAM Parfum.</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalTambah')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Ekspedisi</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Ekspedisi -->
<div id="modalEdit" class="modal-overlay">
    <div class="modal-content">
        <form method="POST">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id_ekspedisi" id="edit_id_ekspedisi">
            <div class="modal-header">
                <h3 class="modal-title">Edit Jasa Pengiriman</h3>
                <button type="button" class="modal-close" onclick="closeModal('modalEdit')">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Nama Jasa Pengiriman</label>
                    <input type="text" name="nama" id="edit_nama" class="form-control" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalEdit')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Hapus Ekspedisi -->
<div id="modalHapus" class="modal-overlay">
    <div class="modal-content">
        <form method="POST">
            <input type="hidden" name="action" value="hapus">
            <input type="hidden" name="id_ekspedisi" id="hapus_id_ekspedisi">
            <div class="modal-header">
                <h3 class="modal-title" style="color: var(--danger);">Konfirmasi Hapus Ekspedisi</h3>
                <button type="button" class="modal-close" onclick="closeModal('modalHapus')">&times;</button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus data ekspedisi <strong id="hapus_nama_display"></strong>?</p>
                <p style="font-size: 12.5px; color: var(--danger); margin-top: 8px;">
                    Perhatian: Seluruh riwayat penilaian dan skor perankingan terkait ekspedisi ini akan ikut terhapus.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalHapus')">Batal</button>
                <button type="submit" class="btn btn-danger" style="background: var(--danger); color: #FFF;">Ya, Hapus Data</button>
            </div>
        </form>
    </div>
</div>

<script>
function editEkspedisi(id, nama) {
    document.getElementById('edit_id_ekspedisi').value = id;
    document.getElementById('edit_nama').value = nama;
    openModal('modalEdit');
}

function hapusEkspedisi(id, nama) {
    document.getElementById('hapus_id_ekspedisi').value = id;
    document.getElementById('hapus_nama_display').innerText = nama;
    openModal('modalHapus');
}
</script>
