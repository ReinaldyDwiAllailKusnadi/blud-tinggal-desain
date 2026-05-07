# AGENT RULES & GUIDELINES (BLUD PARIWISATA)

File ini berisi aturan wajib yang HARUS dipatuhi oleh AI Agent saat bekerja pada proyek ini. Aturan ini memastikan konsistensi kode, keamanan sistem, dan kelancaran alur kerja skripsi.

## 1. Komunikasi & Bahasa
*   **Interaksi User**: Selalu gunakan **Bahasa Indonesia** yang sopan dan profesional saat berkomunikasi dengan User.
*   **Coding**: Nama variabel, fungsi, class, komentar kode, dan dokumentasi teknis wajib menggunakan **Bahasa Inggris**.
*   **UI/UX**: Teks yang muncul di layar aplikasi (Mobile/Web) harus menggunakan **Bahasa Indonesia** sesuai permintaan User.

## 2. Aturan Struktur & Path
*   **Laravel Workspace**: `f:\skripsiii\bellllud`
*   **Flutter Workspace**: `f:\skripsiii\flutter_app`
*   **Pathing**: Selalu gunakan path absolut jika menjalankan command terminal di Windows.

## 3. Aturan Manajemen Aset (KRITIKAL) ⚠️
*   **Logika Gambar**: Jangan pernah mengubah fungsi `asset()` atau `url()` di Controller/View Laravel. Proyek ini menggunakan sistem *Symlink* khusus di hosting.
    *   *Standard*: `$content->image_url = $content->image ? url($content->image) : null;`
*   **Upload**: File baru di-upload ke `storage/app/public/assets/content`. Jangan ubah folder tujuan tanpa alasan yang sangat kuat.

## 4. Standar Desain (Aesthetics First) ✨
*   **Web Admin**: Wajib mendukung **Dark Mode** dengan palette Slate-900 (`#0f172a`). Gunakan komponen `sort-header` untuk tabel.
*   **Flutter UI**: 
    *   Wajib **Native Full-Screen**. Dilarang menggunakan *Phone Mockup/Container fixed width*.
    *   Wajib menggunakan `SafeArea`.
    *   Gunakan Google Fonts (Inter/Outfit) dan palette warna premium (bukan warna dasar).
    *   Gunakan `shared_auth_widgets.dart` untuk menjaga prinsip DRY pada halaman autentikasi.

## 5. Sinkronisasi Backend-Frontend
*   **REST API**: Semua endpoint API harus berada di `routes/api.php` dan controllernya di `app/Http/Controllers/Api/`.
*   **Sanctum**: Pastikan route yang diproteksi menggunakan middleware `auth:sanctum`.
*   **Database**: Jika menemukan error `Unknown column`, prioritaskan perbaikan via migrasi Laravel dan update `$fillable` di Model terkait.

## 6. Testing & Debugging
*   **CORS**: Jika user mengeluh gambar tidak muncul di Chrome (Flutter Web), jelaskan bahwa itu isu CORS dan arahkan untuk testing di **Android Emulator** atau **HP Fisik**.
*   **Local Testing**: Ingat bahwa `php artisan serve` berjalan di `127.0.0.1:8000`. Untuk Android Emulator, gunakan IP `10.0.2.2:8000`.

## 7. Filosofi Kerja
*   **Minimalist Change**: Jangan mengubah fitur Web Admin yang sudah bekerja (CRUD lama) kecuali untuk perbaikan visual/bug. Fokus utama adalah **sinkronisasi data** untuk kebutuhan skripsi.
*   **Documentation**: Selalu update `agent.md` dan `AGENT_HANDOVER.md` setelah melakukan perubahan signifikan.
