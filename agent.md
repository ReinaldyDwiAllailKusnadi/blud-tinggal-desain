# STATUS PROYEK & PANDUAN AGENT (BLUD PARIWISATA)
*Terakhir diupdate: 11 Mei 2026*

Dokumen ini adalah panduan "Single Source of Truth" bagi AI Agent untuk memahami konteks, arsitektur, dan progres terkini proyek Skripsi BLUD Pariwisata.

## 1. Arsitektur & Tech Stack
*   **Backend (Laravel 12+)**: `f:\skripsiii\bellllud`
    *   Fungsi: Web Admin (Blade) & REST API (Sanctum).
    *   Hosting: `https://bludtesting.my.id` (cPanel).
*   **Frontend Mobile (Flutter)**: `f:\skripsiii\flutter_app`
    *   Fungsi: Aplikasi User (Informasi Wisata & Booking).
    *   State Management: `Provider`.
    *   HTTP Client: `Dio`.
    *   Repo: `https://github.com/ReinaldyDwiAllailKusnadi/app-blud-1-05-2025`

## 2. Progress Utama (Selesai ✅)

### Web Admin (Laravel)
*   **Global Dark Mode**: Implementasi palet *Slate-900* (#0f172a) yang konsisten di seluruh halaman admin.
*   **Pagination & Sorting**: Sistem sortir kolom dan paginasi (10 data per halaman) aktif di semua modul.
*   **Sistem Gambar (Symlink)**: Sudah stabil. **Aturan baku: `url($item->image)` atau `asset($item->image)`.**
*   **SPK Recommendation Simulation**: Admin bisa menguji rekomendasi lokasi lewat `/admin/recommendation/simulation`.
*   **Submission PDF Export**: Route `submission/export-pdf` tersedia.

### REST API (Laravel → Flutter)
*   **Auth API**: Login, Register, Google Sign-In, Logout — semua via Sanctum token.
*   **Forgot Password API**: 3-step flow: `POST /forgot-password` → `POST /verify-reset-code` → `POST /reset-password`. Kode 6 digit via email, expire 15 menit, token di-hash.
*   **Content API**: Home, Wisata list, Wisata detail, Fasilitas — dengan caching 1 jam.
*   **Booking API**: Jadwal event + submission approved, by location, by month.
*   **Submission API**: Create (multipart), History, Download lampiran (ownership checked).
*   **Recommendation API**: `POST /recommendation` — Knowledge-Based Similarity Scoring.
*   **Profile API**: GET + PUT/POST update profil user.

### Mobile App (Flutter)
*   **Semua screen utama selesai**: Home, Destinasi List, Destinasi Detail, Rekomendasi SPK, Form Pengajuan, Riwayat Pengajuan, Profile, Jadwal.
*   **Auth flow lengkap**: Welcome → Login → Register → Forgot Password → Verify Code → New Password.
*   **Native Full-Screen**: `Scaffold` + `SafeArea`, tanpa phone mockup.
*   **Shared Auth Widgets**: `lib/screens/auth/shared_auth_widgets.dart` (Logo, Button, Painter).
*   **Download & Open dokumen**: `submission_download_service.dart` + `open_filex`.
*   **Pull-to-refresh**: Home, Riwayat.
*   **Skeleton loading & Empty states**: Sudah diterapkan.

## 3. Komponen Penting Flutter
*   `lib/core/constants/constants.dart`: Lokasi `baseUrl` API & string constants.
*   `lib/core/network/dio_client.dart`: HTTP client dengan interceptor token Sanctum.
*   `lib/core/theme/app_theme.dart`: Design tokens (warna, gradient, radius).
*   `lib/screens/auth/shared_auth_widgets.dart`: Reusable auth components.
*   `lib/providers/auth_provider.dart`: Logika autentikasi & profile.
*   `lib/providers/submission_provider.dart`: Logika pengajuan & riwayat.
*   `lib/providers/recommendation_provider.dart`: Logika SPK rekomendasi.
*   `lib/services/`: Service layer (auth, content, event, submission, recommendation, download).

## 4. Komponen Penting Laravel API
*   `routes/api.php`: Semua endpoint Flutter (public + auth:sanctum).
*   `app/Http/Controllers/Api/`: 8 controller API terpisah dari web controller.
*   `app/Services/RecommendationService.php`: Logika SPK similarity scoring (bobot: budget 0.30, fasilitas 0.30, kapasitas 0.25, jenis 0.15).
*   `app/Mail/UserResetPasswordCodeMail.php`: Mailable untuk kode reset password.

## 5. Audit & Perbaikan Terakhir (11 Mei 2026)
Telah dilakukan audit lengkap dari 7 sudut pandang. Perbaikan yang sudah di-commit:
1.  ✅ **Security**: Hapus `debug` data leak (user ID/email) dari API response `/history`.
2.  ✅ **Security**: Hapus route test `/tes-mailtrap` dari `web.php`.
3.  ✅ **UX**: Fix copywriting form "PDF/Gambar" → "PDF (maks 2MB per file)".
4.  ✅ **Code Quality**: Bersihkan 20+ `debugPrint` berlebihan di `submission_provider.dart`.
5.  ✅ **Data Integrity**: Normalisasi status filter di `BookingApiController::schedules()` — hanya `approved`.

### Belum Dilakukan (Butuh Akses Hosting):
*   ❗ Set `APP_DEBUG=false` di `.env` hosting.
*   ❗ Jalankan migrasi kolom `whatsapp` di hosting.
*   ❗ Set Sanctum token expiration di `config/sanctum.php` (`'expiration' => 1440`).

## 6. Tugas Mendatang (Prioritas 🚀)
1.  **Hosting Config**: 3 item di atas (APP_DEBUG, migrasi whatsapp, Sanctum expiry).
2.  **Empty States Web Admin**: Tambah ilustrasi/message saat tabel kosong.
3.  **Cache Invalidation**: Clear cache saat admin CRUD data (home_data, wisata_all).
4.  **Hapus File Duplikat**: `wisata_list_screen.dart` vs `destination_list_screen.dart` — pilih satu.
5.  **Hapus Route Duplikat**: `POST /profile/update` (sudah ada `PUT /profile`).
6.  **Persiapan Sidang**: Dokumen test case, kuesioner SUS, justifikasi bobot SPK.

## 7. Instruksi Khusus Agent
*   **BAHASA**: Gunakan Bahasa Indonesia saat mengobrol, Bahasa Inggris untuk kode.
*   **KONSERVATIF**: Jangan ubah logic CRUD atau route Laravel yang sudah ada kecuali diminta.
*   **FLUTTER WEB CORS**: Isu CORS di Chrome masih ada — ingatkan user untuk tes di Android Emulator/device fisik.
*   **JANGAN TAMBAH DEBUG**: Jangan sisipkan `debugPrint` atau `dd()` di kode production. Gunakan `Log::error` di Laravel untuk error saja.
*   **API RESPONSE**: Selalu gunakan format `{ success, message, data }`. Jangan tambah key `debug` atau `error` di response.
*   **FILE UPLOAD**: Semua upload ke disk `public_html_storage`. Validasi: `mimes:pdf|max:2048`.
*   **STATUS SUBMISSION**: Gunakan string `pending`, `approved`, `rejected` saja. Jangan pakai variasi lain.
