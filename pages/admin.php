<?php
/**
 * Halaman Pengaturan Akun Admin
 * SPK Pemilihan Jasa Pengiriman MOORA - FAM Parfum
 */

$alertMsg = '';
$alertType = '';

// Handle Admin update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? cleanInput($_POST['action']) : '';

    if ($action === 'update_profile') {
        $idAdmin = (int)($_POST['id_admin'] ?? 1);
        $nama = cleanInput($_POST['nama'] ?? '');
        $username = cleanInput($_POST['username'] ?? '');

        if (!empty($nama) && !empty($username)) {
            $stmt = $pdo->prepare("UPDATE admin SET nama = ?, username = ? WHERE id_admin = ?");
            $stmt->execute([$nama, $username, $idAdmin]);
            
            // Sync session if updating current active user
            if (isset($_SESSION['admin_id']) && $idAdmin == $_SESSION['admin_id']) {
                $_SESSION['admin_nama'] = $nama;
                $_SESSION['admin_username'] = $username;
            }

            $alertMsg = "Profil akun admin berhasil diperbarui!";
            $alertType = 'success';
        } else {
            $alertMsg = "Nama dan Username tidak boleh kosong.";
            $alertType = 'danger';
        }
    } elseif ($action === 'update_password') {
        $idAdmin = (int)($_POST['id_admin'] ?? 1);
        $passwordBaru = cleanInput($_POST['password_baru'] ?? '');
        $konfirmasi = cleanInput($_POST['konfirmasi_password'] ?? '');

        if (empty($passwordBaru)) {
            $alertMsg = "Password baru tidak boleh kosong.";
            $alertType = 'danger';
        } elseif ($passwordBaru !== $konfirmasi) {
            $alertMsg = "Konfirmasi password baru tidak cocok.";
            $alertType = 'danger';
        } else {
            $stmt = $pdo->prepare("UPDATE admin SET password = ? WHERE id_admin = ?");
            $stmt->execute([$passwordBaru, $idAdmin]);
            $alertMsg = "Kata sandi admin berhasil diperbarui!";
            $alertType = 'success';
        }
    } elseif ($action === 'tambah_admin') {
        $nama = cleanInput($_POST['nama'] ?? '');
        $username = cleanInput($_POST['username'] ?? '');
        $password = cleanInput($_POST['password'] ?? '');

        if (!empty($nama) && !empty($username) && !empty($password)) {
            // Check username duplicate
            $check = $pdo->prepare("SELECT COUNT(*) FROM admin WHERE username = ?");
            $check->execute([$username]);
            if ($check->fetchColumn() > 0) {
                $alertMsg = "Username '{$username}' sudah digunakan!";
                $alertType = 'danger';
            } else {
                $stmt = $pdo->prepare("INSERT INTO admin (nama, username, password) VALUES (?, ?, ?)");
                $stmt->execute([$nama, $username, $password]);
                $alertMsg = "Admin baru berhasil ditambahkan!";
                $alertType = 'success';
            }
        }
    }
}

// Fetch current active admin account based on session
$activeAdminId = isset($_SESSION['admin_id']) ? (int)$_SESSION['admin_id'] : 1;
$stmtCurr = $pdo->prepare("SELECT * FROM admin WHERE id_admin = ?");
$stmtCurr->execute([$activeAdminId]);
$currentAdmin = $stmtCurr->fetch();

// Fetch all admin accounts
$admins = $pdo->query("SELECT * FROM admin ORDER BY id_admin ASC")->fetchAll();
if (!$currentAdmin && !empty($admins)) {
    $currentAdmin = $admins[0];
}
?>

<div class="page-container">
    <div class="page-header">
        <div>
            <h1 class="page-title">Pengaturan Akun</h1>
            <p class="page-description">Kelola profil pengguna administrator dan kredensial login operasional FAM Parfum (Tabel <code>admin</code>).</p>
        </div>
        <div class="page-actions">
            <button type="button" class="btn btn-secondary" onclick="openModal('modalTambahAdmin')">
                <i data-lucide="user-plus"></i>
                <span>Tambah Pengguna Admin</span>
            </button>
        </div>
    </div>

    <?php if ($alertMsg): ?>
    <div class="alert alert-<?= $alertType ?> alert-dismissible">
        <i data-lucide="<?= $alertType === 'success' ? 'check-circle' : 'alert-triangle' ?>"></i>
        <span><?= htmlspecialchars($alertMsg) ?></span>
    </div>
    <?php endif; ?>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
        <!-- Profil Admin Form -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i data-lucide="user" style="color: var(--primary); width: 18px; height: 18px;"></i>
                    <span>Informasi Profil Admin</span>
                </h3>
            </div>
            <div class="card-body">
                <form method="POST">
                    <input type="hidden" name="action" value="update_profile">
                    <input type="hidden" name="id_admin" value="<?= $currentAdmin['id_admin'] ?>">

                    <div class="form-group">
                        <label class="form-label">Nama Lengkap Administrator</label>
                        <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($currentAdmin['nama']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($currentAdmin['username']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Peran Sistem (Role)</label>
                        <input type="text" class="form-control" value="Pengelola SPK Logistik & Operasional" disabled style="background: var(--bg-subtle);">
                    </div>

                    <button type="submit" class="btn btn-primary" style="margin-top: 8px;">
                        <i data-lucide="save"></i>
                        <span>Simpan Profil</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Ubah Password Form -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i data-lucide="lock" style="color: var(--primary); width: 18px; height: 18px;"></i>
                    <span>Perbarui Kata Sandi</span>
                </h3>
            </div>
            <div class="card-body">
                <form method="POST">
                    <input type="hidden" name="action" value="update_password">
                    <input type="hidden" name="id_admin" value="<?= $currentAdmin['id_admin'] ?>">

                    <div class="form-group">
                        <label class="form-label">Kata Sandi Baru</label>
                        <input type="password" name="password_baru" class="form-control" placeholder="Masukkan kata sandi baru" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Konfirmasi Kata Sandi Baru</label>
                        <input type="password" name="konfirmasi_password" class="form-control" placeholder="Ulangi kata sandi baru" required>
                    </div>

                    <div style="font-size: 12px; color: var(--text-muted); margin-bottom: 16px;">
                        Gunakan kombinasi karakter yang aman untuk melindungi data keputusan logistik FAM Parfum.
                    </div>

                    <button type="submit" class="btn btn-secondary">
                        <i data-lucide="key"></i>
                        <span>Perbarui Kata Sandi</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Tabel Daftar Akun Admin Terdaftar -->
    <div class="card" style="margin-top: 24px;">
        <div class="card-header">
            <h3 class="card-title">
                <i data-lucide="users" style="color: var(--primary); width: 18px; height: 18px;"></i>
                <span>Daftar Akun Pengguna Terdaftar</span>
            </h3>
            <span class="badge badge-rank-other"><?= count($admins) ?> Akun</span>
        </div>
        <div class="card-body" style="padding: 0;">
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width: 70px;">ID</th>
                            <th>Nama Lengkap</th>
                            <th>Username</th>
                            <th>Status Akses</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($admins as $adm): ?>
                        <tr>
                            <td>#<?= $adm['id_admin'] ?></td>
                            <td>
                                <strong><?= htmlspecialchars($adm['nama']) ?></strong>
                            </td>
                            <td>
                                <span style="font-family: monospace; font-size: 13px; color: var(--text-heading);">
                                    <?= htmlspecialchars($adm['username']) ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-benefit">Aktif</span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Admin -->
<div id="modalTambahAdmin" class="modal-overlay">
    <div class="modal-content">
        <form method="POST">
            <input type="hidden" name="action" value="tambah_admin">
            <div class="modal-header">
                <h3 class="modal-title">Tambah Pengguna Admin Baru</h3>
                <button type="button" class="modal-close" onclick="closeModal('modalTambahAdmin')">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" name="nama" class="form-control" placeholder="Nama Petugas" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" placeholder="username_admin" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Password" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalTambahAdmin')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Admin</button>
            </div>
        </form>
    </div>
</div>
