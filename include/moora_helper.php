<?php
/**
 * MOORA Helper - Multi-Objective Optimization on the basis of Ratio Analysis
 * SPK Pemilihan Jasa Pengiriman FAM Parfum
 */

class MooraHelper {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Fetch all criteria from DB
     */
    public function getKriteria() {
        $stmt = $this->pdo->query("SELECT * FROM kriteria ORDER BY id_kriteria ASC");
        return $stmt->fetchAll();
    }

    /**
     * Fetch all expeditions/alternatives from DB
     */
    public function getEkspedisi() {
        $stmt = $this->pdo->query("SELECT * FROM ekspedisi ORDER BY id_ekspedisi ASC");
        return $stmt->fetchAll();
    }

    /**
     * Fetch raw alternatives evaluation matrix
     * Returns array: [id_ekspedisi => [id_kriteria => nilai]]
     */
    public function getMatrixRaw() {
        $stmt = $this->pdo->query("SELECT * FROM alternatif");
        $rows = $stmt->fetchAll();
        $matrix = [];
        foreach ($rows as $r) {
            $matrix[$r['id_mitra']][$r['id_kriteria']] = (float)$r['nilai'];
        }
        return $matrix;
    }

    /**
     * Perform complete MOORA calculation
     */
    public function calculate() {
        $kriteriaList = $this->getKriteria();
        $ekspedisiList = $this->getEkspedisi();
        $rawMatrix = $this->getMatrixRaw();

        if (empty($kriteriaList) || empty($ekspedisiList)) {
            return [
                'success' => false,
                'message' => 'Data Kriteria atau Ekspedisi masih kosong.'
            ];
        }

        // 1. Calculate sum of weights for normalization
        $totalBobot = 0;
        foreach ($kriteriaList as $k) {
            $totalBobot += (float)$k['bobot'];
        }
        if ($totalBobot == 0) $totalBobot = 1;

        // 2. Step 1: Decision Matrix (X)
        // Ensure every cell has a value (default 0 if not set)
        $matrixX = [];
        foreach ($ekspedisiList as $e) {
            $idE = $e['id_ekspedisi'];
            foreach ($kriteriaList as $k) {
                $idK = $k['id_kriteria'];
                $matrixX[$idE][$idK] = isset($rawMatrix[$idE][$idK]) ? $rawMatrix[$idE][$idK] : 0;
            }
        }

        // 3. Step 2: Denominators sqrt(sum(x_ij^2)) per criterion
        $denominators = [];
        foreach ($kriteriaList as $k) {
            $idK = $k['id_kriteria'];
            $sumSq = 0;
            foreach ($ekspedisiList as $e) {
                $idE = $e['id_ekspedisi'];
                $val = $matrixX[$idE][$idK];
                $sumSq += ($val * $val);
            }
            $denominators[$idK] = sqrt($sumSq);
        }

        // Normalized Matrix (R)
        $matrixR = [];
        foreach ($ekspedisiList as $e) {
            $idE = $e['id_ekspedisi'];
            foreach ($kriteriaList as $k) {
                $idK = $k['id_kriteria'];
                $denom = $denominators[$idK];
                $matrixR[$idE][$idK] = ($denom > 0) ? ($matrixX[$idE][$idK] / $denom) : 0;
            }
        }

        // 4. Step 3: Weighted Normalized Matrix (Y)
        // y_ij = r_ij * (bobot / totalBobot)
        $matrixY = [];
        $weights = [];
        foreach ($kriteriaList as $k) {
            $idK = $k['id_kriteria'];
            $weights[$idK] = (float)$k['bobot'] / $totalBobot;
        }

        foreach ($ekspedisiList as $e) {
            $idE = $e['id_ekspedisi'];
            foreach ($kriteriaList as $k) {
                $idK = $k['id_kriteria'];
                $matrixY[$idE][$idK] = $matrixR[$idE][$idK] * $weights[$idK];
            }
        }

        // 5. Step 4: Optimization Value (Yi)
        // Yi = Sum(Benefit) - Sum(Cost)
        $optimasi = [];
        foreach ($ekspedisiList as $e) {
            $idE = $e['id_ekspedisi'];
            $sumBenefit = 0;
            $sumCost = 0;

            foreach ($kriteriaList as $k) {
                $idK = $k['id_kriteria'];
                $jenis = strtolower(trim($k['jenis']));
                $yVal = $matrixY[$idE][$idK];

                if ($jenis === 'cost') {
                    $sumCost += $yVal;
                } else {
                    $sumBenefit += $yVal;
                }
            }

            $yi = $sumBenefit - $sumCost;

            $optimasi[$idE] = [
                'id_ekspedisi' => $idE,
                'nama' => $e['nama'],
                'sum_benefit' => $sumBenefit,
                'sum_cost' => $sumCost,
                'skor_optimasi' => $yi,
            ];
        }

        // 6. Step 5: Ranking (Sort descending by skor_optimasi)
        uasort($optimasi, function($a, $b) {
            if ($a['skor_optimasi'] == $b['skor_optimasi']) return 0;
            return ($a['skor_optimasi'] > $b['skor_optimasi']) ? -1 : 1;
        });

        $ranking = [];
        $rankNum = 1;
        foreach ($optimasi as $idE => $row) {
            $status = 'Alternatif Lain';
            if ($rankNum === 1) {
                $status = 'Sangat Direkomendasikan';
            } elseif ($rankNum <= 3) {
                $status = 'Direkomendasikan';
            }

            $ranking[$idE] = array_merge($row, [
                'ranking' => $rankNum,
                'status' => $status
            ]);
            $rankNum++;
        }

        return [
            'success' => true,
            'kriteria' => $kriteriaList,
            'ekspedisi' => $ekspedisiList,
            'matrixX' => $matrixX,
            'denominators' => $denominators,
            'matrixR' => $matrixR,
            'weights' => $weights,
            'matrixY' => $matrixY,
            'optimasi' => $optimasi,
            'ranking' => $ranking,
            'totalBobot' => $totalBobot,
            'tgl_hitung' => date('Y-m-d')
        ];
    }

    /**
     * Save MOORA results to `ranking` table
     */
    public function saveResults($rankingData, $tglHitung = null) {
        if (!$tglHitung) {
            $tglHitung = date('Y-m-d');
        }

        // Delete existing rankings for today to avoid duplicate entries for the same date
        $del = $this->pdo->prepare("DELETE FROM ranking WHERE tgl_hitung = ?");
        $del->execute([$tglHitung]);

        // Insert new ranking rows
        $stmt = $this->pdo->prepare("
            INSERT INTO ranking (id_alternatif, skor_optimasi, ranking, tgl_hitung)
            VALUES (?, ?, ?, ?)
        ");

        foreach ($rankingData as $row) {
            $stmt->execute([
                $row['id_ekspedisi'],
                $row['skor_optimasi'],
                $row['ranking'],
                $tglHitung
            ]);
        }

        return true;
    }

    /**
     * Get stored ranking from DB joined with ekspedisi
     */
    public function getStoredRanking($tglHitung = null) {
        if ($tglHitung) {
            $stmt = $this->pdo->prepare("
                SELECT r.*, e.nama as nama_ekspedisi 
                FROM ranking r 
                JOIN ekspedisi e ON r.id_alternatif = e.id_ekspedisi 
                WHERE r.tgl_hitung = ? 
                ORDER BY r.ranking ASC
            ");
            $stmt->execute([$tglHitung]);
        } else {
            // Get latest calculation date
            $latestStmt = $this->pdo->query("SELECT MAX(tgl_hitung) as latest FROM ranking");
            $latest = $latestStmt->fetchColumn();
            if (!$latest) return [];

            $stmt = $this->pdo->prepare("
                SELECT r.*, e.nama as nama_ekspedisi 
                FROM ranking r 
                JOIN ekspedisi e ON r.id_alternatif = e.id_ekspedisi 
                WHERE r.tgl_hitung = ? 
                ORDER BY r.ranking ASC
            ");
            $stmt->execute([$latest]);
        }

        return $stmt->fetchAll();
    }

    /**
     * Get summary statistics for dashboard
     */
    public function getDashboardStats() {
        // Total ekspedisi
        $totalEkspedisi = (int)$this->pdo->query("SELECT COUNT(*) FROM ekspedisi")->fetchColumn();
        // Total kriteria
        $totalKriteria = (int)$this->pdo->query("SELECT COUNT(*) FROM kriteria")->fetchColumn();
        
        // Latest calculation date
        $latestDate = $this->pdo->query("SELECT MAX(tgl_hitung) FROM ranking")->fetchColumn();
        
        // Top 1 expedition
        $topEkspedisi = null;
        if ($latestDate) {
            $topStmt = $this->pdo->prepare("
                SELECT r.*, e.nama as nama_ekspedisi 
                FROM ranking r 
                JOIN ekspedisi e ON r.id_alternatif = e.id_ekspedisi 
                WHERE r.tgl_hitung = ? AND r.ranking = 1 
                LIMIT 1
            ");
            $topStmt->execute([$latestDate]);
            $topEkspedisi = $topStmt->fetch();
        }

        return [
            'total_ekspedisi' => $totalEkspedisi,
            'total_kriteria' => $totalKriteria,
            'tgl_hitung_terakhir' => $latestDate,
            'top_ekspedisi' => $topEkspedisi
        ];
    }
}
