# STATUS TERKINI API & BACKEND (Untuk Agent Flutter)

Halo Agent Flutter! Dokumen ini berisi ringkasan *state* dari Backend (Laravel) dan konfigurasi Server Hosting saat ini, agar kamu bisa membantu *user* menyelesaikan masalah gambar di aplikasi Flutter.

## 1. Arsitektur API & Image URL
*   **Base URL Backend**: `https://bludtesting.my.id/api`
*   Saat ini, API Backend mengirimkan respons JSON yang di dalamnya terdapat field `image` (path database) dan `image_url` (URL lengkap).
*   Ada 2 jenis URL gambar yang dihasilkan oleh API Backend:
    1.  **Data Lama (Seeder)**: `https://bludtesting.my.id/assets/img/nama_gambar.jpg`
    2.  **Data Baru (Upload Admin)**: `https://bludtesting.my.id/storage/assets/content/nama_gambar.png`
*   Backend menggunakan *ternary operator* untuk memastikan URL yang dikirim ke Flutter sudah terbentuk dengan benar sesuai letak filenya di hosting cPanel.

## 2. Struktur Hosting (cPanel)
*   **Data Lama** diletakkan langsung di dalam folder `/public_html/assets/img/`.
*   **Data Baru** diletakkan di `/laravel/storage/app/public/assets/content/`.
*   Server menggunakan *Cronjob / Symlink* untuk menghubungkan folder storage:
    `public_html/storage` ➔ `laravel/storage/app/public`

## 3. Masalah Utama (The Bug)
*   User mengalami error pada console Flutter:
    ```
    EncodingError: The source image cannot be decoded.
    HTTP request failed, statusCode: 0
    ```
*   **Penyebabnya sudah diketahui**: User menjalankan perintah `flutter run` menggunakan **Chrome (Web)**.
*   Browser Chrome memiliki aturan keamanan **CORS (Cross-Origin Resource Sharing)** yang ketat. File gambar statis yang disajikan oleh Apache Hosting (terutama yang melewati folder *symlink* `storage/`) tidak memiliki *header* `Access-Control-Allow-Origin: *`.
*   Akibatnya, *XMLHTTPRequest* (atau Canvas rendering) dari Chrome diblokir, menghasilkan `statusCode: 0` dan gagal di-*decode* oleh widget `CachedNetworkImage` atau `Image.network`.

## 4. Instruksi untuk Agent Flutter
Tugas kamu sekarang adalah memandu user menyelesaikan masalah ini dari sisi Flutter:
1.  Jelaskan kepada user bahwa `EncodingError` ini adalah isu spesifik **Flutter Web (CORS)**, bukan masalah API atau *symlink* yang rusak. API sudah mengembalikan URL yang benar.
2.  Jika user masih ingin *testing* di Chrome, berikan panduan cara men-disable security web (misal: menjalankan flutter dengan `--web-browser-flag "--disable-web-security"`).
3.  Berikan panduan cara *testing* di Android Emulator atau mem-*build* APK-nya secara langsung agar *user* bisa melihat bahwa aplikasi Mobile-nya sebenarnya berjalan dengan sempurna tanpa terganggu CORS.
4.  Pastikan *property* `imageUrl` pada `CachedNetworkImage` di-binding dengan benar (tidak ada penambahan string/URL yang redundan).
