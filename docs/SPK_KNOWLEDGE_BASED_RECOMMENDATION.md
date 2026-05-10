# Perancangan Fitur SPK Knowledge-Based Recommendation
> Lokasi File: `docs/SPK_KNOWLEDGE_BASED_RECOMMENDATION.md`

## 1. Tujuan Fitur Rekomendasi
Fitur ini dikembangkan untuk membantu pengguna dalam memilih lokasi sewa wisata yang paling sesuai dengan kebutuhan kegiatan mereka. Rekomendasi ini diberikan sebelum pengguna melakukan pengajuan sewa, sehingga meminimalisir kesalahan pemilihan lokasi dan meningkatkan kepuasan pengguna.

## 2. Konsep Metode
Sistem ini menggunakan metode **Knowledge-Based Recommendation** dengan pendekatan **Similarity (Kemiripan)**. Sistem akan mencocokkan kebutuhan yang diinputkan oleh pengguna (knowledge requirement) dengan karakteristik atau fitur yang dimiliki oleh lokasi wisata (knowledge domain). Lokasi dengan skor kemiripan tertinggi akan direkomendasikan kepada pengguna.

## 3. Sumber Data Rekomendasi
Perhitungan rekomendasi melibatkan data dari beberapa tabel yang sudah tersedia di sistem:
- **Input User (Flutter)**: Kebutuhan kegiatan, tanggal, budget, jumlah peserta, fasilitas, dan preferensi.
- **Tabel `content`**: Menyediakan data dasar lokasi wisata.
- **Tabel `content_features`**: Menyediakan data detail fasilitas dan harga sewa per bagian lokasi.
- **Tabel `event`**: Menyediakan data jadwal kegiatan yang sudah ada.
- **Tabel `submission`**: Menyediakan data pengajuan sewa dengan status `approved` untuk pengecekan ketersediaan tanggal.

## 4. Kriteria Rekomendasi
Kriteria yang digunakan dalam proses filter dan perhitungan adalah:
1. **Tanggal Kegiatan** (Constraint/Filter Wajib)
2. **Budget / Harga Sewa**
3. **Fasilitas yang Dibutuhkan**
4. **Kapasitas / Luas Lokasi**
5. **Jenis Kegiatan / Preferensi Lokasi** (Contoh: Indoor/Outdoor)

## 5. Constraint Ketersediaan Tanggal
Tanggal kegiatan bersifat **wajib (mandatory)**. Sebelum masuk ke perhitungan similarity, sistem melakukan filtering:
- Jika lokasi memiliki bentrokan jadwal pada tabel `event` atau `submission` (status `approved`) pada rentang tanggal yang dipilih, maka lokasi tersebut **tidak tersedia** dan tidak akan masuk ke perhitungan similarity.
- Hanya lokasi yang tersedia pada tanggal tersebut yang akan dihitung skor kemiripannya.

## 6. Kriteria Berbobot
Setelah lolos filter tanggal, sistem menghitung skor berdasarkan kriteria berikut:
- **Budget / Harga Sewa**: Kesesuaian harga sewa dengan anggaran user.
- **Fasilitas**: Kesesuaian jumlah fasilitas tersedia dengan permintaan user.
- **Kapasitas / Luas**: Kecukupan kapasitas lokasi untuk jumlah peserta user.
- **Jenis Kegiatan / Preferensi Lokasi**: Kesesuaian tipe venue dengan preferensi user.

## 7. Rancangan Bobot Awal
Bobot ditentukan berdasarkan tingkat kepentingan kriteria dalam pemilihan lokasi sewa:

| Kriteria | Poin | Bobot (W) |
| :--- | :---: | :---: |
| Budget / Harga Sewa | 30 | 0.30 |
| Fasilitas | 30 | 0.30 |
| Kapasitas / Luas | 25 | 0.25 |
| Jenis Kegiatan / Preferensi | 15 | 0.15 |
| **Total** | **100** | **1.00** |

> [!NOTE]
> Rancangan bobot ini bersifat awal dan perlu divalidasi lebih lanjut melalui wawancara/konfirmasi dengan pihak pengelola BLUD menggunakan metode *point allocation*.

## 8. Rumus Similarity
Skor kemiripan dihitung menggunakan rumus:

$$Sim(user, lokasi) = (0.30 \times S_{budget}) + (0.30 \times S_{fasilitas}) + (0.25 \times S_{kapasitas}) + (0.15 \times S_{jenis})$$

## 9. Definisi Nilai Skor (S)
- **$S_{budget}$**: Bernilai 1 jika harga $\leq$ budget user. Jika harga $>$ budget, nilai menurun secara proporsional atau menggunakan fungsi penalti.
- **$S_{fasilitas}$**: Dihitung dari $\frac{\text{jumlah fasilitas cocok}}{\text{jumlah fasilitas yang diminta user}}$.
- **$S_{kapasitas}$**: Bernilai 1 jika kapasitas lokasi $\geq$ jumlah peserta, bernilai 0 jika kapasitas tidak mencukupi.
- **$S_{jenis}$**: Bernilai 1 jika jenis/preferensi lokasi sesuai (misal: Indoor/Outdoor), bernilai 0 jika tidak sesuai.

## 10. Contoh Perhitungan Sederhana
### Input User:
- **Kegiatan**: Seminar
- **Peserta**: 100 orang
- **Budget**: Rp2.000.000
- **Fasilitas**: Aula, Kursi, Sound System (Total 3)
- **Preferensi**: Indoor
- **Tanggal**: 20 Mei 2026

### Data Lokasi A:
- **Tanggal**: Tersedia (Tidak ada bentrok)
- **Harga**: Rp1.500.000
- **Fasilitas Cocok**: 2 dari 3 (Aula dan Sound System tersedia)
- **Kapasitas**: 150 orang
- **Tipe**: Indoor

### Langkah Perhitungan:
1. **$S_{budget}$** = 1 (Karena Rp1.500.000 $\leq$ Rp2.000.000)
2. **$S_{fasilitas}$** = 2 / 3 = **0.67**
3. **$S_{kapasitas}$** = 1 (Karena 150 $\geq$ 100)
4. **$S_{jenis}$** = 1 (Karena Indoor = Indoor)

### Skor Akhir:
$$Total = (0.30 \times 1) + (0.30 \times 0.67) + (0.25 \times 1) + (0.15 \times 1)$$
$$Total = 0.30 + 0.201 + 0.25 + 0.15 = \mathbf{0.901}$$

**Skor Akhir: 90.1 / 100**

## 11. Klasifikasi Status Rekomendasi
| Skor (Persen) | Status Rekomendasi |
| :--- | :--- |
| 85 – 100 | Sangat Direkomendasikan |
| 70 – 84 | Direkomendasikan |
| 50 – 69 | Cukup Sesuai |
| < 50 | Kurang Direkomendasikan |
| Bentrok Tanggal | Tidak Tersedia |

## 12. Rencana Implementasi (TODO)
Berikut adalah langkah-langkah untuk tahap selanjutnya:
- [ ] Cek struktur database yang sudah ada di Laravel.
- [ ] Tambahkan kolom `capacity` dan `venue_type` pada tabel `content` via Migration jika belum tersedia.
- [ ] Buat `RecommendationService` di Laravel untuk menampung logika perhitungan.
- [ ] Buat API endpoint `POST /api/recommendation`.
- [ ] Uji coba endpoint menggunakan Postman.
- [ ] Rancang dan implementasi screen Rekomendasi di Flutter.
- [ ] Hubungkan UI Flutter dengan API rekomendasi Laravel.
