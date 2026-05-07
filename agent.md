# STATUS PROYEK & PANDUAN AGENT (BLUD PARIWISATA)
*Terakhir diupdate: 30 April 2026*

Dokumen ini adalah panduan "Single Source of Truth" bagi AI Agent untuk memahami konteks, arsitektur, dan progres terkini proyek Skripsi BLUD Pariwisata.

## 1. Arsitektur & Tech Stack
*   **Backend (Laravel 12+)**: `f:\skripsiii\bellllud`
    *   Fungsi: Web Admin (Blade) & REST API (Sanctum).
    *   Hosting: `https://bludtesting.my.id` (cPanel).
*   **Frontend Mobile (Flutter)**: `f:\skripsiii\flutter_app`
    *   Fungsi: Aplikasi User (Informasi Wisata & Booking).
    *   State Management: `Provider`.
    *   HTTP Client: `Dio`.

## 2. Progress Utama (Selesai ✅)

### Web Admin (Laravel)
*   **Global Dark Mode**: Implementasi palet *Slate-900* (#0f172a) yang konsisten di seluruh halaman admin (Sidebar, Card, Table, Form, Modal).
*   **Pagination & Sorting**: Sistem sortir kolom (`id`, `name`, `created_at`) dan paginasi (10 data per halaman) sudah aktif di semua modul utama (Content, News, Event, User, Admin, Submission).
*   **Sistem Gambar (Symlink)**: Sudah stabil menggunakan symlink di hosting. **Aturan baku: Gunakan `url($item->image)` atau `asset($item->image)` saja.**

### Mobile App (Flutter)
*   **Konversi UI Auth**: Halaman `WelcomeScreen`, `LoginScreen`, dan `RegisterScreen` telah dikonversi dari desain TSX menjadi kode Dart yang *pixel-perfect*.
*   **Native Full-Screen**: UI telah dibersihkan dari "phone mockup" dan status bar palsu. Sekarang menggunakan native `Scaffold` + `SafeArea`.
*   **Shared Auth Widgets**: Komponen seperti `LogoBlud`, `GradientButton`, dan CustomPainters (Logo & Google Icon) telah diekstrak ke `lib/screens/auth/shared_auth_widgets.dart` untuk efisiensi.
*   **API Integration**: Tombol Login, Register, dan Google Sign-in sudah terhubung ke `AuthProvider` dan siap memproses data dari/ke Laravel API.

## 3. Komponen Penting Flutter
*   `lib/core/constants/constants.dart`: Lokasi `baseUrl` API.
*   `lib/screens/auth/shared_auth_widgets.dart`: Design tokens dan reusable auth components.
*   `lib/data/providers/auth_provider.dart`: Logika utama autentikasi.

## 4. Tugas Mendatang (Prioritas 🚀)
1.  **Migrasi Database (WhatsApp)**: Menambahkan kolom `whatsapp` ke tabel `content` di hosting untuk memperbaiki bug `Unknown column 'whatsapp'`.
2.  **Empty States**: Menambahkan ilustrasi SVG/Message saat tabel data di Web Admin kosong.
3.  **RBAC (Multilevel Admin)**: Evaluasi kebutuhan Role-Based Access Control jika diperlukan oleh dosen.
4.  **Flutter UI Optimization**: Melanjutkan konversi halaman lain (Detail Wisata, Booking, Profile) agar senada dengan UI Auth yang baru.

## 5. Instruksi Khusus Agent
*   **BAHASA**: Gunakan Bahasa Indonesia saat mengobrol dengan user, namun gunakan Bahasa Inggris untuk *coding* (variable, function, class).
*   **KONSERVATIF**: Jangan mengubah logic CRUD atau struktur route Laravel yang sudah ada kecuali diminta, karena berisiko merusak integrasi dengan Flutter.
*   **FLUTTER WEB CORS**: Ingatkan user bahwa isu gambar tidak muncul di Chrome Web adalah masalah CORS, solusinya gunakan Android Emulator.
