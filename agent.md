# STATUS PROYEK & PANDUAN AGENT (BLUD PARIWISATA)

Dokumen ini berisi *state* terkini dari pengembangan aplikasi BLUD Pariwisata (Laravel Backend & Flutter Frontend) untuk menjadi panduan kerja AI Agent selanjutnya.

## 1. Deskripsi Proyek
*   **Tujuan**: Pengembangan platform terintegrasi BLUD Pariwisata Banyumas.
*   **Tech Stack**: Laravel 12+ (Backend, API, Web Admin) dan Flutter (Mobile Frontend).
*   **Target Skripsi**: Sinkronisasi data yang sempurna antara dashboard admin (manajemen konten) dan aplikasi mobile (konsumsi API).

## 2. Pencapaian & Status Saat Ini (Selesai)
*   **Deployment Hosting**: Backend dan API sudah sukses mengudara di *production server* (`https://bludtesting.my.id`).
*   **Koneksi Flutter**: Aplikasi mobile sudah terhubung ke *endpoint* *production* menggunakan Dio/HTTP client.
*   **Manajemen Gambar (Resolved)**:
    *   *Problem*: Terjadi *EncodingError* di Flutter dan 404 Not Found di Web Admin akibat konflik penempatan file (public_html vs folder laravel/storage).
    *   *Solusi Brilian User*: Melakukan *Symlink* spesifik langsung ke folder aset dengan perintah Cronjob:
        `ln -s /home/bludtest/laravel/storage/app/public/assets/content /home/bludtest/public_html/assets/content`
    *   *Kondisi Kode*: Logika *URL/Asset* di API Controllers dan Blade Views telah disederhanakan murni menggunakan `url($item->image)` atau `asset($item->image)`. **PENTING: Jangan ubah kode ini lagi karena sudah bekerja sempurna dengan symlink.**

## 3. Apa yang Sedang Dibuat / Dikerjakan?
Saat ini kita berada pada fase **Integrasi & Validasi Fitur Lanjutan**:
1.  **Testing CRUD Admin**: Memastikan Admin dapat meng-*upload* tempat wisata baru, event, dan berita, lalu fotonya langsung tersinkronisasi ke aplikasi Flutter.
2.  **Penambahan Fitur Data (WhatsApp)**: Mengintegrasikan input tambahan dari Web Admin (seperti link WhatsApp `https://wa.me/...`) agar bisa tersimpan ke Database dan disajikan di UI Flutter. (Kemarin sempat muncul error `Unknown column 'whatsapp'`, yang membutuhkan penyesuaian/migrasi database di hosting).
3.  **End-to-End Flow**: Menguji alur dari sisi *Web Admin (Input)* ➔ *Database* ➔ *REST API* ➔ *Aplikasi Flutter (Display & Booking)*.

## 4. Instruksi Khusus untuk Agent Selanjutnya
*   **JANGAN MENGUBAH ALUR GAMBAR**: Biarkan Controller menggunakan `$content->image_url = $content->image ? url($content->image) : null;`. Logika ini sudah tervalidasi dan sukses dirender oleh Flutter.
*   **Prioritas Database**: Jika ada laporan error `Column not found` atau `SQLSTATE`, itu berarti ada *mismatch* (ketidakcocokan) antara *input form* di Controller dengan struktur tabel di MySQL hosting. Solusinya adalah memandu user menambahkan kolom tersebut dan menjalankan `php artisan migrate` di cPanel.
*   **Bypass CORS**: Jika *user* melakukan *testing* Flutter di Web (Chrome) dan mengeluh gambar tidak muncul, ingatkan kembali bahwa itu adalah isu keamanan CORS Chrome, bukan *bug* kode. Arahkan *user* untuk *testing* di Android Emulator.
