# Desain Antarmuka Web Sistem Pendukung Keputusan (SPK) 
# Pemilihan Jasa Pengiriman Menggunakan Metode MOORA untuk Bisnis Online FAM Parfum

## 1. Panduan Visual (Design Direction)
* **Warna Utama:** 
  * Navy Blue: `#0F172A` (Sidebar & Header)
  * Primary Blue: `#2563EB` (Aksi Utama, Tombol, & Aksen Aktif)
  * Background: `#F8FAFC`
  * Kartu & Kontainer: `#FFFFFF` (White cards)
  * Teks: Slate text colors (`#334155` untuk teks utama, `#64748B` untuk teks sekunder)
* **Tipografi:** Inter font family.
* **Ikonografi:** Lucide-style icons.
* **Gaya Komponen:** Batas tipis (`border-slate-200`), jarak konsisten, dan bayangan minimal (*subtle shadows*). Menghindari gradien berlebihan atau estetika AI yang terlalu rumit.

## 2. Tata Letak (Layout)
* Menggunakan *persistent left sidebar* pada mode desktop, *top header* yang kompak, serta area konten utama yang luas (`#F8FAFC`).
* Menu Navigasi Sesuai Database & Kebutuhan Sistem:
  1. Dashboard
  2. Hasil Perankingan (Tabel `ranking` & `ekspedisi`)
  3. Data Jasa Pengiriman (Tabel `ekspedisi`)
  4. Data Kriteria (Tabel `kriteria`)
  5. Penilaian Alternatif (Tabel `alternatif`)
  6. Proses MOORA (Matriks Keputusan, Normalisasi, Terbobot, & Optimasi)
  7. Riwayat Perhitungan (Log berdasarkan `tgl_hitung`)
  8. Pengaturan Akun (Tabel `admin`)

## 3. Halaman Utama (Main Dashboard)
* **Judul Halaman:** "Dashboard"
* **Deskripsi:** "Ringkasan data dan rekomendasi jasa pengiriman untuk operasional pengiriman barang FAM Parfum."
* **4 Kartu Ringkasan (Summary Cards):**
  1. Total Alternatif Ekspedisi (Menghitung jumlah data pada tabel `ekspedisi`)
  2. Total Kriteria Penilaian (Menghitung jumlah data pada tabel `kriteria`)
  3. Perhitungan Terakhir (`tgl_hitung` terbaru dari tabel `ranking`)
  4. Alternatif Peringkat 1 (Ekspedisi terbaik saat ini)
* **Kartu Rekomendasi Unggulan:** Menampilkan mitra ekspedisi teratas untuk FAM Parfum beserta skor optimasi MOORA-nya.
* **Tabel Ringkasan Peringkat:** Pratinjau 3 besar alternatif terbaik.
* **Tombol Aksi:** 
  * Utama: "Lihat Hasil Perankingan"
  * Sekunder: "Mulai Perhitungan"

## 4. Halaman Hasil Perankingan (Ranking Page)
* **Judul Halaman:** "Hasil Perankingan" (Halaman Utama Hasil Analisis SPK).
* **Komponen Atas (Kartu Rekomendasi Utama):**
  * Lencana Peringkat 1 (`Rank 1`).
  * Nama Jasa Pengiriman (Relasi `ekspedisi.nama`).
  * Skor Optimasi MOORA (`skor_optimasi`).
  * Jumlah alternatif yang dievaluasi.
  * Tanggal Perhitungan (`tgl_hitung`).
  * Tombol: "Lihat Detail Perhitungan".
* **Tabel Perankingan:**
  * Kolom: Peringkat (`ranking`), Kode Alternatif, Nama Jasa Pengiriman (`ekspedisi.nama`), Nilai Optimasi MOORA (`skor_optimasi`), Status Rekomendasi, dan Aksi.
  * Pengurutan (*sorting*) berdasarkan `skor_optimasi` secara menurun (*descending*).
  * Aksen warna hijau/biru tipis untuk peringkat pertama.
  * Dilengkapi fitur pencarian (*search*), filter, dan tombol cetak laporan ("Cetak Hasil").
* **Keterangan Sistem:** Penjelasan singkat bahwa nilai optimasi tertinggi ditentukan berdasarkan matriks keputusan terhadap bobot kriteria *Benefit* dan *Cost* di dalam basis data.

## 5. Halaman Proses MOORA (Calculation Page)
Menampilkan tahapan matematis sistem secara transparan sesuai entitas basis data:
1. **Matriks Keputusan ($X$):** Menampilkan nilai mentah dari tabel `alternatif` berdasarkan `id_kriteria` dan `id_ekspedisi`.
2. **Normalisasi Matriks ($R$):** Perhitungan akar dari jumlah kuadrat nilai per kriteria.
3. **Matriks Ternormalisasi Terbobot ($Y$):** Perkalian matriks normalisasi dengan bobot kriteria (`kriteria.bobot`).
4. **Nilai Optimasi MOORA ($Y_i$):** Pengurangan total kriteria *benefit* dengan kriteria *cost*.
5. **Hasil Perankingan Akhir:** Pemetaan skor ke tabel `ranking`.

## 6. Desain Responsif & Konsistensi Komponen
* **Responsif:** Menggunakan *navigation drawer* pada perangkat mobile serta fitur *horizontal scroll* pada tabel data yang lebar tanpa mengurangi ukuran teks secara berlebihan.
* **Konsistensi:** Mempertahankan keseragaman tombol, kartu, header tabel, *badge* status, dialog, dan penamaan menggunakan **Bahasa Indonesia** penuh demi menjaga kredibilitas akademik tugas akhir.