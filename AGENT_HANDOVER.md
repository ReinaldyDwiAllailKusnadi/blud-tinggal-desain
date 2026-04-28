# PROYEK BLUD PARIWISATA: STATUS & HANDOVER UNTUK AGENT

Halo Agent! Ini adalah ringkasan konteks lengkap dari proyek *BLUD Pariwisata* (Skripsi) yang sedang berjalan. Mohon baca dengan teliti dan pahami arsitekturnya sebelum melakukan tindakan atau menyarankan modifikasi kode.

## 1. Arsitektur Proyek
Proyek ini terdiri dari dua sistem yang saling terhubung:
*   **Backend (Laravel 12+)**: Berfungsi sebagai penyedia REST API dan dashboard Web Admin (Blade). Saat ini sudah mengudara (*live*) di hosting cPanel dengan domain `https://bludtesting.my.id`. Direktori lokal: `f:\skripsiii\bellllud`.
*   **Frontend Mobile (Flutter)**: Aplikasi mobile *end-user* untuk *booking* tiket dan informasi wisata. Terkoneksi ke API *production* tersebut. Direktori lokal: `f:\skripsiii\flutter_app`.

## 2. Penanganan Aset & Gambar (PENTING)
Sebelumnya terjadi kendala di mana gambar tempat wisata baru (hasil *upload* Admin) terkena error 404 atau *EncodingError* di Flutter karena percampuran path penyimpanan di Shared Hosting.
*   **Solusi Final (Telah Aktif)**: *User* telah mengakalinya menggunakan *Symlink* via Cronjob cPanel yang spesifik langsung menargetkan isi folder.
    *   *Perintah Symlink Server*: `ln -s /home/bludtest/laravel/storage/app/public/assets/content /home/bludtest/public_html/assets/content`
*   **Aturan Kode (JANGAN DIUBAH)**: Karena *symlink* tersebut, logika pada Controller API dan Blade View saat ini **sangat disederhanakan** murni menggunakan helper `url($item->image)` atau `asset($item->image)`. **Tolong jangan sarankan untuk menambahkan logika pengecekan folder `storage/` atau *ternary logic* lagi, karena kodingan saat ini sudah terbukti bekerja 100% sempurna di Flutter.**

## 3. Tugas / Blocker Saat Ini (The Active Task)
Saat *user* mencoba meng-edit atau menambahkan data tempat wisata di Web Admin, muncul error dari sisi Database:
```sql
SQLSTATE[42S22]: Column not found: 1054 Unknown column 'whatsapp' in 'SET' (Connection: mysql, SQL: update `content` set `whatsapp` = https://wa.me/087666352633 ... where `id` = 9).
```

### Panduan untuk Agent Baru:
Tugas kamu sekarang adalah memandu *user* membereskan fitur WhatsApp ini dari sisi Backend hingga ke Frontend Flutter. Langkah yang harus kamu tempuh:
1.  **Selesaikan Error Database**: Pandu user untuk membuat file *Migration* (contoh: `php artisan make:migration add_whatsapp_to_contents_table`) untuk menambahkan kolom `whatsapp` (tipe string, *nullable*) ke tabel `content`.
2.  **Cek Model Laravel**: Pastikan kolom `whatsapp` sudah ditambahkan ke dalam `$fillable` di file `app/Models/Content.php`.
3.  **Eksekusi di Hosting**: Instruksikan *user* bagaimana cara menjalankan *migration* tersebut di database cPanel (bisa via phpMyAdmin atau Terminal cPanel).
4.  **Update Sisi Flutter**: Setelah API berhasil mengirim field `whatsapp`, pandu *user* untuk meng-update `ContentModel` di Flutter (file `models.dart`) agar properti WhatsApp bisa ditangkap dan ditampilkan menjadi tombol "*Hubungi via WhatsApp*" di tampilan aplikasinya.

Selamat bekerja, dan pastikan setiap langkah instruksi jelas dan mudah diikuti oleh *user*!
