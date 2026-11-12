<?php
/**
 * Halaman Cetak Laporan Hasil Perankingan MOORA
 * SPK Pemilihan Jasa Pengiriman MOORA - FAM Parfum
 */

$moora = new MooraHelper($pdo);
$tglFilter = isset($_GET['tgl']) ? cleanInput($_GET['tgl']) : null;
$rankings = $moora->getStoredRanking($tglFilter);

if (empty($rankings)) {
    $calcResult = $moora->calculate();
    if ($calcResult['success']) {
        $moora->saveResults($calcResult['ranking']);
        $rankings = $moora->getStoredRanking();
    }
}

$topOne = !empty($rankings) ? $rankings[0] : null;
$tglHitung = $topOne ? $topOne['tgl_hitung'] : date('Y-m-d');
$kriteriaList = $pdo->query("SELECT * FROM kriteria ORDER BY id_kriteria ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Hasil Perankingan SPK MOORA - FAM Parfum</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            color: #0F172A;
            background: #F8FAFC;
            margin: 0;
            padding: 24px;
            font-size: 13px;
            line-height: 1.5;
        }

        .print-sheet {
            max-width: 900px;
            margin: 0 auto;
            background: #FFFFFF;
            padding: 40px;
            border-radius: 8px;
            border: 1px solid #E2E8F0;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        }

        /* Letterhead / Kop Surat */
        .kop-surat {
            display: flex;
            align-items: center;
            border-bottom: 3px double #0F172A;
            padding-bottom: 16px;
            margin-bottom: 24px;
            gap: 20px;
        }

        .kop-logo {
            width: 70px;
            height: 70px;
            border-radius: 12px;
            background: #0F172A;
            color: #FFFFFF;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            font-weight: 800;
            letter-spacing: -1px;
            flex-shrink: 0;
        }

        .kop-text {
            flex: 1;
            text-align: center;
        }

        .kop-text h2 {
            margin: 0;
            font-size: 20px;
            font-weight: 800;
            letter-spacing: 0.05em;
            color: #0F172A;
        }

        .kop-text p {
            margin: 3px 0 0;
            font-size: 12px;
            color: #475569;
        }

        .doc-title {
            text-align: center;
            margin-bottom: 24px;
        }

        .doc-title h3 {
            margin: 0;
            font-size: 16px;
            text-transform: uppercase;
            font-weight: 700;
            color: #0F172A;
            letter-spacing: 0.02em;
        }

        .doc-title span {
            font-size: 12px;
            color: #64748B;
        }

        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 16px 0 24px;
            font-size: 12.5px;
        }

        th, td {
            border: 1px solid #CBD5E1;
            padding: 8px 12px;
            text-align: left;
        }

        th {
            background-color: #F1F5F9;
            font-weight: 700;
            color: #0F172A;
        }

        .highlight-win {
            background-color: #F0FDF4;
            font-weight: 600;
        }

        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
        }

        .badge-win {
            background: #DCFCE7;
            color: #166534;
        }

        /* Recommendation Box */
        .recom-box {
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-left: 4px solid #2563EB;
            padding: 14px 18px;
            border-radius: 6px;
            margin-bottom: 28px;
        }

        /* Signature block */
        .ttd-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            margin-top: 40px;
            padding-top: 20px;
        }

        .ttd-box {
            text-align: center;
        }

        .ttd-space {
            height: 70px;
        }

        .ttd-name {
            font-weight: 700;
            text-decoration: underline;
        }

        .no-print-bar {
            max-width: 900px;
            margin: 0 auto 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            font-size: 13px;
            font-weight: 600;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            border: 1px solid transparent;
        }

        .btn-print {
            background: #2563EB;
            color: #FFFFFF;
        }

        .btn-back {
            background: #FFFFFF;
            color: #334155;
            border-color: #CBD5E1;
        }

        @media print {
            body {
                background: #FFFFFF;
                padding: 0;
            }
            .print-sheet {
                border: none;
                box-shadow: none;
                padding: 0;
                max-width: 100%;
            }
            .no-print-bar {
                display: none;
            }
        }
    </style>
</head>
<body>

    <div class="no-print-bar">
        <a href="index.php?page=ranking" class="btn btn-back">&larr; Kembali ke Sistem</a>
        <button onclick="window.print()" class="btn btn-print">Cetak Laporan / Simpan PDF</button>
    </div>

    <div class="print-sheet">
        <!-- Kop Surat -->
        <div class="kop-surat">
            <div class="kop-logo">FAM</div>
            <div class="kop-text">
                <h2>FAM PARFUM INDONESIA</h2>
                <p>E-Commerce & Retail Fragrance Logistics Department</p>
                <p>Jl. Melati Raya No. 45, Jakarta | Email: contact@famparfum.com | Telp: (021) 789-2341</p>
            </div>
        </div>

        <!-- Judul Dokumen -->
        <div class="doc-title">
            <h3>Laporan Rekomendasi Pemilihan Jasa Pengiriman</h3>
            <span>Sistem Pendukung Keputusan Metode MOORA (Multi-Objective Optimization by Ratio Analysis)</span>
        </div>

        <div style="display: flex; justify-content: space-between; font-size: 12px; margin-bottom: 16px;">
            <div>
                <div><strong>No. Laporan:</strong> SPK-MOORA/FAM/<?= date('Ym', strtotime($tglHitung)) ?>/<?= rand(100, 999) ?></div>
                <div><strong>Tanggal Evaluasi:</strong> <?= formatTanggalIndo($tglHitung) ?></div>
            </div>
            <div style="text-align: right;">
                <div><strong>Jumlah Alternatif Dievaluasi:</strong> <?= count($rankings) ?> Ekspedisi</div>
                <div><strong>Jumlah Kriteria:</strong> <?= count($kriteriaList) ?> Variabel</div>
            </div>
        </div>

        <!-- Rekomendasi Utama Callout -->
        <?php if ($topOne): ?>
        <div class="recom-box">
            <div style="font-size: 11.5px; text-transform: uppercase; font-weight: 700; color: #2563EB; letter-spacing: 0.05em;">
                Rekomendasi Terbaik (Peringkat 1)
            </div>
            <div style="font-size: 18px; font-weight: 800; color: #0F172A; margin: 4px 0;">
                <?= htmlspecialchars($topOne['nama_ekspedisi']) ?> &mdash; Skor Optimasi: <?= number_format($topOne['skor_optimasi'], 4) ?>
            </div>
            <div style="font-size: 12px; color: #475569;">
                Berdasarkan normalisasi matriks dan pembobotan kriteria Cost vs Benefit, jasa ekspedisi ini terbukti memiliki rasio performa terbaik untuk menjaga keutuhan botol parfum serta efisiensi operasional.
            </div>
        </div>
        <?php endif; ?>

        <!-- Tabel Perankingan Lengkap -->
        <h4 style="margin: 0 0 8px; font-size: 13.5px; color: #0F172A;">Hasil Perankingan Menyeluruh</h4>
        <table>
            <thead>
                <tr>
                    <th style="width: 70px; text-align: center;">Peringkat</th>
                    <th style="width: 100px;">Kode</th>
                    <th>Nama Jasa Pengiriman</th>
                    <th style="width: 150px; text-align: right;">Skor MOORA ($Y_i$)</th>
                    <th style="width: 170px; text-align: center;">Status Rekomendasi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rankings as $row): 
                    $isTop = ($row['ranking'] == 1);
                ?>
                <tr class="<?= $isTop ? 'highlight-win' : '' ?>">
                    <td style="text-align: center;">
                        <strong><?= $row['ranking'] ?></strong>
                    </td>
                    <td>A<?= $row['id_alternatif'] ?></td>
                    <td>
                        <strong><?= htmlspecialchars($row['nama_ekspedisi']) ?></strong>
                        <?php if ($isTop): ?> (Utama)<?php endif; ?>
                    </td>
                    <td style="text-align: right; font-family: monospace; font-weight: 700;">
                        <?= number_format($row['skor_optimasi'], 4) ?>
                    </td>
                    <td style="text-align: center;">
                        <?php if ($row['ranking'] == 1): ?>
                            <span class="badge badge-win">Sangat Direkomendasikan</span>
                        <?php elseif ($row['ranking'] <= 3): ?>
                            <span class="badge" style="background: #E0E7FF; color: #3730A3;">Direkomendasikan</span>
                        <?php else: ?>
                            <span class="badge" style="background: #F1F5F9; color: #475569;">Alternatif Cadangan</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Kriteria Rujukan -->
        <h4 style="margin: 0 0 8px; font-size: 13px; color: #0F172A;">Bobot & Jenis Kriteria yang Diterapkan</h4>
        <table>
            <thead>
                <tr>
                    <th style="width: 60px;">Kode</th>
                    <th>Kriteria Penilaian</th>
                    <th style="width: 90px; text-align: center;">Bobot (%)</th>
                    <th style="width: 100px; text-align: center;">Sifat</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($kriteriaList as $k): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($k['kode_kriteria']) ?></strong></td>
                    <td><?= htmlspecialchars($k['nama_kriteria']) ?></td>
                    <td style="text-align: center;"><?= $k['bobot'] ?>%</td>
                    <td style="text-align: center;"><strong><?= htmlspecialchars($k['jenis']) ?></strong></td>
                    <td style="font-size: 11.5px; color: #475569;">
                        <?= strtolower($k['jenis']) === 'cost' ? 'Minimalkan (Ongkos Kirim Murah)' : 'Maksimalkan (Kualitas & Kecepatan Pengiriman)' ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Lembar Pengesahan / Tanda Tangan -->
        <div class="ttd-grid">
            <div class="ttd-box">
                <div>Mengetahui,</div>
                <div style="font-weight: 600;">Kepala Gudang & Logistik</div>
                <div class="ttd-space"></div>
                <div class="ttd-name">Rian Pratama, S.Log.</div>
                <div style="font-size: 11.5px; color: #64748B;">NIP: FAM-2022-094</div>
            </div>

            <div class="ttd-box">
                <div>Ditetapkan di Jakarta, <?= formatTanggalIndo($tglHitung) ?></div>
                <div style="font-weight: 600;">Direktur Operasional FAM Parfum</div>
                <div class="ttd-space"></div>
                <div class="ttd-name">Fajar Maulana, S.M.</div>
                <div style="font-size: 11.5px; color: #64748B;">Founder & Managing Director</div>
            </div>
        </div>
    </div>

</body>
</html>
