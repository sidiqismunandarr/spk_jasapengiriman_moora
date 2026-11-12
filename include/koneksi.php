<?php
/**
 * Database Connection for SPK Jasa Pengiriman MOORA - FAM Parfum
 */

$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'spk_jasapengiriman_moora';

try {
    $pdo = new PDO("mysql:host={$db_host};dbname={$db_name};charset=utf8mb4", $db_user, $db_pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    die("Koneksi database gagal: " . htmlspecialchars($e->getMessage()));
}

// Fallback mysqli connection if needed
$koneksi = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
if (!$koneksi) {
    // handled by pdo catch, but ensure charset
    die("Koneksi MySQLi gagal: " . mysqli_connect_error());
}
mysqli_set_charset($koneksi, "utf8mb4");

// Helper function to safely escape strings or get parameter
function cleanInput($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

// Helper to format date in Indonesian
function formatTanggalIndo($tanggal) {
    if (!$tanggal || $tanggal == '0000-00-00') return '-';
    $bulan = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    $pecahkan = explode('-', date('Y-m-d', strtotime($tanggal)));
    return $pecahkan[2] . ' ' . $bulan[(int)$pecahkan[1]] . ' ' . $pecahkan[0];
}
