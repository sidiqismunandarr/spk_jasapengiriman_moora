<?php
/**
 * Halaman Login Sistem Pendukung Keputusan (SPK) MOORA
 * Bisnis Online FAM Parfum
 */

session_start();
require_once __DIR__ . '/include/koneksi.php';

// If already logged in, redirect to dashboard
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header("Location: index.php?page=dashboard");
    exit;
}
// test
$errorMsg = '';
$successMsg = '';

// Check URL message
if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'logout') {
        $successMsg = 'Anda telah berhasil keluar (logout) dari sistem.';
    } elseif ($_GET['msg'] === 'auth_required') {
        $errorMsg = 'Silakan masuk (login) terlebih dahulu untuk mengakses sistem.';
    }
}

// Handle login POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = cleanInput($_POST['username'] ?? '');
    $password = cleanInput($_POST['password'] ?? '');

    if (empty($username) || empty($password)) {
        $errorMsg = 'Username dan Password wajib diisi.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM admin WHERE username = ? LIMIT 1");
        $stmt->execute([$username]);
        $admin = $stmt->fetch();

        // Check password (supports plain text and hashed passwords)
        if ($admin && ($admin['password'] === $password || password_verify($password, $admin['password']))) {
            // Set session
            $_SESSION['logged_in'] = true;
            $_SESSION['admin_id'] = $admin['id_admin'];
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['admin_nama'] = $admin['nama'];

            header("Location: index.php?page=dashboard");
            exit;
        } else {
            $errorMsg = 'Username atau kata sandi yang Anda masukkan salah.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SPK MOORA FAM Parfum</title>
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            background-color: var(--bg-page);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .login-wrapper {
            max-width: 440px;
            width: 100%;
        }

        .login-brand-header {
            text-align: center;
            margin-bottom: 28px;
        }

        .login-brand-logo {
            width: 52px;
            height: 52px;
            background: var(--navy-dark);
            color: #FFFFFF;
            border-radius: var(--radius-lg);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            font-weight: 800;
            margin-bottom: 14px;
            box-shadow: 0 4px 10px rgba(15, 23, 42, 0.15);
            border: 2px solid var(--primary);
        }

        .login-title {
            font-size: 22px;
            font-weight: 700;
            color: var(--text-heading);
            letter-spacing: -0.02em;
            margin-bottom: 6px;
        }

        .login-subtitle {
            font-size: 13px;
            color: var(--text-muted);
            line-height: 1.5;
        }

        .login-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-md);
            padding: 32px 28px;
        }

        .password-toggle-wrap {
            position: relative;
        }

        .password-toggle-wrap input {
            padding-right: 40px;
        }

        .password-toggle-btn {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            padding: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .password-toggle-btn:hover {
            color: var(--text-heading);
        }

        .demo-credentials-box {
            background: #F8FAFC;
            border: 1px dashed #CBD5E1;
            border-radius: var(--radius-md);
            padding: 12px 14px;
            margin-top: 20px;
            font-size: 12px;
            color: var(--text-main);
        }

        .demo-credentials-box code {
            background: #E2E8F0;
            padding: 2px 6px;
            border-radius: 4px;
            font-weight: 600;
            color: var(--navy-dark);
            font-family: monospace;
        }
    </style>
</head>
<body>

<div class="login-wrapper">
    <!-- Brand Header -->
    <div class="login-brand-header">
        <div class="login-brand-logo">
            <i data-lucide="package-search" style="width: 28px; height: 28px; color: #FFFFFF;"></i>
        </div>
        <h1 class="login-title">FAM Parfum</h1>
        <p class="login-subtitle">
            Sistem Pendukung Keputusan Pemilihan Jasa Pengiriman<br>
            <strong>Metode MOORA</strong> (Multi-Objective Optimization by Ratio Analysis)
        </p>
    </div>

    <!-- Login Card -->
    <div class="login-card">
        <?php if ($errorMsg): ?>
        <div class="alert alert-danger" style="margin-bottom: 20px;">
            <i data-lucide="alert-circle" style="width: 18px; height: 18px; flex-shrink: 0;"></i>
            <span><?= htmlspecialchars($errorMsg) ?></span>
        </div>
        <?php endif; ?>

        <?php if ($successMsg): ?>
        <div class="alert alert-success" style="margin-bottom: 20px;">
            <i data-lucide="check-circle" style="width: 18px; height: 18px; flex-shrink: 0;"></i>
            <span><?= htmlspecialchars($successMsg) ?></span>
        </div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <div class="form-group">
                <label class="form-label" for="username">Username</label>
                <div style="position: relative;">
                    <input type="text" 
                           id="username" 
                           name="username" 
                           class="form-control" 
                           placeholder="Masukkan username" 
                           value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : 'admin' ?>" 
                           required 
                           autofocus>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Kata Sandi</label>
                <div class="password-toggle-wrap">
                    <input type="password" 
                           id="password" 
                           name="password" 
                           class="form-control" 
                           placeholder="Masukkan kata sandi" 
                           value="admin123" 
                           required>
                    <button type="button" class="password-toggle-btn" id="togglePasswordBtn" title="Lihat/Sembunyikan Kata Sandi">
                        <i data-lucide="eye" id="togglePasswordIcon" style="width: 18px; height: 18px;"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 11px 16px; font-size: 14px; font-weight: 600; margin-top: 8px;">
                <i data-lucide="log-in" style="width: 18px; height: 18px;"></i>
                <span>Masuk ke Sistem SPK</span>
            </button>
        </form>

        <!-- Demo Credentials Helper -->
        <div class="demo-credentials-box">
            <div style="font-weight: 600; color: var(--navy-dark); margin-bottom: 4px; display: flex; align-items: center; gap: 6px;">
                <i data-lucide="key" style="width: 14px; height: 14px; color: var(--primary);"></i>
                <span>Akses Akun Default:</span>
            </div>
            <div>Username: <code>admin</code> | Password: <code>admin123</code></div>
        </div>
    </div>

    <!-- Login Footer -->
    <div style="text-align: center; margin-top: 24px; font-size: 12px; color: var(--text-muted);">
        &copy; <?= date('Y') ?> FAM Parfum Indonesia &bull; Logistik & Pengiriman
    </div>
</div>

<!-- Lucide Icons -->
<script src="https://unpkg.com/lucide@latest"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        if (window.lucide) {
            lucide.createIcons();
        }

        // Toggle password visibility
        const toggleBtn = document.getElementById('togglePasswordBtn');
        const passwordInput = document.getElementById('password');

        if (toggleBtn && passwordInput) {
            toggleBtn.addEventListener('click', () => {
                const isPassword = passwordInput.getAttribute('type') === 'password';
                passwordInput.setAttribute('type', isPassword ? 'text' : 'password');

                const icon = document.getElementById('togglePasswordIcon');
                if (icon) {
                    icon.setAttribute('data-lucide', isPassword ? 'eye-off' : 'eye');
                    if (window.lucide) lucide.createIcons();
                }
            });
        }
    });
</script>
</body>
</html>
