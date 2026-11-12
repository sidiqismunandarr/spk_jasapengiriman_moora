# Product Requirements Document (PRD)
# Sistem Pendukung Keputusan (SPK) Pemilihan Jasa Pengiriman
# Metode MOORA (Multi-Objective Optimization on the basis of Ratio Analysis)
# Studi Kasus: Bisnis Online FAM Parfum

## 1. Pendahuluan & Latar Belakang
FAM Parfum merupakan bisnis online yang menjual berbagai produk wewangian dan parfum retail. Dalam operasionalnya, pengiriman produk parfum membutuhkan perhatian khusus:
1. **Karakteristik Produk Khusus:** Cairan dalam botol kaca rentan pecah (*fragile*) sehingga membutuhkan penanganan (*handling*) dan garansi keamanan yang tinggi.
2. **Sensitivitas Ongkos Kirim:** Biaya pengiriman mempengaruhi keputusan beli konsumen.
3. **Kecepatan & Ketepatan Pengantaran:** Keterlambatan pengiriman dapat menurunkan kepuasan pelanggan (*customer satisfaction*).
4. **Jangkauan Area & Pelayanan:** Kemudahan pick-up paket dan sistem pelacakan (*tracking*) yang akurat mempermudah operasional tim FAM Parfum.

Sistem Pendukung Keputusan ini dibangun menggunakan metode **MOORA** untuk memberikan rekomendasi jasa pengiriman terbaik secara objektif dan matematis.

---

## 2. Struktur Basis Data (Entity Relationship)
Database: `spk_jasapengiriman_moora`

### 1. Tabel `admin`
Menyimpan akun pengguna sistem:
* `id_admin` (INT, Primary Key, Auto Increment)
* `username` (VARCHAR(25))
* `password` (VARCHAR(20))
* `nama` (VARCHAR(50))

### 2. Tabel `ekspedisi`
Daftar alternatif jasa pengiriman yang dievaluasi:
* `id_ekspedisi` (INT, Primary Key, Auto Increment)
* `nama` (VARCHAR(50))

### 3. Tabel `kriteria`
Variabel penentu keputusan dengan klasifikasi Benefit atau Cost:
* `id_kriteria` (INT, Primary Key, Auto Increment)
* `kode_kriteria` (VARCHAR(5))
* `nama_kriteria` (VARCHAR(50))
* `bobot` (INT) — Total bobot terdistribusi 100%
* `jenis` (VARCHAR(10)) — `Benefit` atau `Cost`

### 4. Tabel `alternatif`
Matriks nilai mentah penilaian alternatif per kriteria:
* `id_alternatif` (INT, Primary Key, Auto Increment)
* `id_mitra` (INT) — Foreign Key ke `ekspedisi.id_ekspedisi`
* `id_kriteria` (INT) — Foreign Key ke `kriteria.id_kriteria`
* `nilai` (INT) — Skala penilaian 1 hingga 5

### 5. Tabel `ranking`
Hasil akhir skor optimasi MOORA dan urutan peringkat:
* `id_ranking` (INT, Primary Key, Auto Increment)
* `id_alternatif` (INT) — Foreign Key ke `ekspedisi.id_ekspedisi`
* `skor_optimasi` (DECIMAL(10,4)) — Nilai $Y_i$
* `ranking` (INT) — Peringkat (1, 2, 3, ...)
* `tgl_hitung` (DATE) — Tanggal perhitungan SPK

---

## 3. Formulasi Matematis Metode MOORA
Metode MOORA terdiri atas 5 langkah:

1. **Matriks Keputusan ($X$):**
   $$X = \begin{bmatrix} x_{11} & x_{12} & \dots & x_{1n} \\ x_{21} & x_{22} & \dots & x_{2n} \\ \vdots & \vdots & \ddots & \vdots \\ x_{m1} & x_{m2} & \dots & x_{mn} \end{bmatrix}$$

2. **Normalisasi Matriks ($R$):**
   $$r_{ij} = \frac{x_{ij}}{\sqrt{\sum_{i=1}^{m} x_{ij}^2}}$$

3. **Matriks Normalisasi Terbobot ($Y$):**
   $$y_{ij} = r_{ij} \times W_j$$
   di mana $W_j = \frac{w_j}{\sum w}$.

4. **Nilai Optimasi ($Y_i$):**
   $$Y_i = \sum_{j \in Benefit} y_{ij} - \sum_{j \in Cost} y_{ij}$$

5. **Perankingan ($Rank$):**
   Mengurutkan nilai $Y_i$ secara menurun (*descending*). Nilai tertinggi merupakan alternatif terbaik (Peringkat 1).

---

## 4. Panduan Desain Antarmuka (Design Guidelines)
* **Navy Blue:** `#0F172A` (Sidebar & Header)
* **Primary Blue:** `#2563EB` (Aksi Utama, Tombol, & Aksen Aktif)
* **Background:** `#F8FAFC`
* **Kartu & Kontainer:** `#FFFFFF` (White cards) dengan batas halus (`#E2E8F0`)
* **Teks:** Slate (`#334155` teks utama, `#64748B` teks sekunder)
* **Tipografi:** Google Font *Inter*
* **Ikon:** Lucide Icons
* **Tata Letak:** Persistent left sidebar pada desktop, responsive drawer pada mobile, serta horizontal scroll pada data table.

---

## 5. Fitur dan Menu Sistem
1. **Dashboard:** 4 kartu ringkasan, hero card rekomendasi juara 1, pratinjau 3 besar, dan tombol aksi cepat.
2. **Hasil Perankingan:** Tabel perankingan lengkap dengan skor optimasi, badge status rekomendasi, search filter, dan cetak hasil.
3. **Data Jasa Pengiriman:** CRUD lengkap mitra kurir ekspedisi.
4. **Data Kriteria:** CRUD kriteria, validasi bobot total 100%, klasifikasi Benefit/Cost.
5. **Penilaian Alternatif:** Matriks editor nilai mentah skala 1-5 dengan panduan penilaian.
6. **Proses MOORA:** Transparansi 5 tahapan rumus matematis MOORA dan tombol simpan perankingan ke database.
7. **Riwayat Perhitungan:** Log arsip perankingan berdasarkan tanggal perhitungan.
8. **Pengaturan Akun:** Profil administrator dan perubahan kata sandi admin.
9. **Cetak Laporan:** Laporan resmi berkop surat FAM Parfum dan tanda tangan pengesahan manajemen.
10. **Autentikasi (Login & Logout):** Sistem proteksi sesi administrator (`admin`), verifikasi kredensial aman, fitur *show/hide password*, tombol keluar (*logout*) konfirmatif di header & sidebar, serta pengalihan otomatis (*authentication guard*).
