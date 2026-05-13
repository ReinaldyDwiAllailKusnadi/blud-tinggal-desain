# AGENT HANDOVER (BLUD PARIWISATA)
*Timestamp: 11 Mei 2026, 19:51*

## 📍 Konteks Terkini
Proyek telah melewati **audit lengkap** dari 7 sudut pandang (UI/UX, Security, User, Admin, Akademik, Developer). Semua fitur utama Flutter sudah selesai. Sisa pekerjaan adalah hosting config, persiapan sidang, dan polish minor.

## 🛠️ Pekerjaan Terakhir (Sesi Ini)
1.  **Audit Komprehensif**:
    *   28 temuan total: 3 Critical, 9 High, 10 Medium, 6 Low.
    *   Dokumen audit lengkap tersimpan di conversation artifact.
2.  **Security Fixes (5 item)**:
    *   Hapus `debug` data leak dari API `/history` response.
    *   Hapus route test `/tes-mailtrap` dari `web.php`.
    *   Normalisasi status query di `BookingApiController::schedules()`.
    *   Bersihkan 20+ `debugPrint` berlebihan di `submission_provider.dart`.
    *   Fix copywriting form pengajuan ("PDF/Gambar" → "PDF maks 2MB").
3.  **Commit & Push Flutter**:
    *   Commit `0ce1818` — "update apps 11/05" (17 files, +1282 -376 lines).
    *   Pushed ke `origin/main`.

## 📁 File Penting & Lokasi

### Flutter
*   **Auth Screens**: `lib/screens/auth/` (8 file: welcome, login, register, forgot, verify, new_password, wrapper, shared)
*   **Main Navigation**: `lib/screens/main/main_screen.dart` (5 tabs: Home, Destinasi, Rekomendasi, Jadwal, Profile)
*   **Booking**: `lib/screens/booking/` (submission_form, submission_history)
*   **Providers**: `lib/providers/` (auth, content, event, submission, recommendation)
*   **Services**: `lib/services/` (auth, content, event, submission, recommendation, download)
*   **Config**: `lib/core/constants/constants.dart` (baseUrl: production)

### Laravel API
*   **API Routes**: `routes/api.php` (14 endpoints, public + auth:sanctum)
*   **API Controllers**: `app/Http/Controllers/Api/` (8 controllers)
*   **SPK Service**: `app/Services/RecommendationService.php`
*   **Admin Simulation**: Route `/admin/recommendation/simulation`

## ⚠️ Peringatan (Blocker & Risks)
*   **❗ APP_DEBUG=true**: Masih aktif di hosting `.env`. Stack trace bisa bocor. **HARUS diubah ke `false`.**
*   **❗ Migrasi whatsapp**: Kolom `whatsapp` belum ada di tabel `content` hosting. **HARUS jalankan `php artisan migrate`.**
*   **❗ Token tanpa expiry**: Sanctum token hidup selamanya. Set `'expiration' => 1440` di `config/sanctum.php`.
*   **API Base URL**: `lib/core/constants/constants.dart` menunjuk ke production (`bludtesting.my.id`). Untuk tes lokal: `http://10.0.2.2:8000/api`.
*   **CORS**: Isu CORS di Flutter Web (Chrome) masih ada. Gunakan Android Emulator atau device fisik.
*   **Cache**: `home_data` dan `wisata_all` di-cache 1 jam tanpa invalidation saat admin update.

## 🚀 Langkah Selanjutnya
1.  **Hosting**: Set APP_DEBUG=false, migrasi whatsapp, Sanctum expiry.
2.  **Hapus file duplikat**: `wisata_list_screen.dart` (tidak dipakai, sudah diganti `destination_list_screen.dart`).
3.  **Empty States Web Admin**: Tambah pesan saat tabel kosong.
4.  **Persiapan Sidang**: Dokumen test case (20+ skenario), kuesioner SUS, justifikasi bobot SPK.
5.  **Opsional**: Search/filter di destinasi list, push notification, multi-step form wizard.

## 📋 Aturan Baru Agent (Post-Audit)
*   **JANGAN** sisipkan `debugPrint` atau data `debug` di API response.
*   **STATUS** submission: hanya gunakan `pending`, `approved`, `rejected`.
*   **UPLOAD**: Validasi `mimes:pdf|max:2048`, disk `public_html_storage`.
*   **API RESPONSE**: Format `{ success, message, data }` — tanpa key tambahan.
