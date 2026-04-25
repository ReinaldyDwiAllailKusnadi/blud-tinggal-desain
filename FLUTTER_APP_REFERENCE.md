# BLUD WB Web Reference for Flutter App

Dokumen ini merangkum fitur, data, alur, dan perilaku yang saat ini ada di web Laravel `belud-wb`, agar bisa dijadikan acuan saat membuat versi aplikasi Flutter.

## 1. Gambaran Umum

Produk ini adalah aplikasi informasi wisata dan booking/pengajuan sewa tempat milik BLUD Pariwisata.

Ada 2 area utama:
dddd
1. Area publik dan user:
   - Beranda
   - Daftar objek wisata
   - Detail objek wisata
   - Jadwal booking per lokasi
   - Detail jadwal per bulan
   - Informasi fasilitas dan harga sewa
   - Registrasi, login user, profil user
   - Form pengajuan sewa
   - Riwayat pengajuan user

2. Area admin:
   - Login admin
   - Dashboard
   - CRUD akun admin
   - CRUD user
   - CRUD konten wisata
   - Kelola fasilitas dan harga sewa tiap konten
   - CRUD berita
   - CRUD event
   - Review submission booking
   - Approve/reject submission
   - Pengiriman email status booking

## 2. Peran Pengguna

### Guest

- Bisa melihat beranda
- Bisa melihat daftar objek wisata
- Bisa melihat detail objek wisata
- Bisa melihat daftar jadwal
- Bisa melihat detail jadwal booking
- Bisa membuka form pengajuan, tetapi untuk submit harus login
- Bisa register dan login

### User

- Semua kemampuan guest
- Bisa submit pengajuan sewa
- Bisa melihat riwayat pengajuan
- Bisa melihat dan mengubah profil
- Bisa logout

### Admin

- Login melalui halaman admin
- Bisa melihat dashboard statistik
- Bisa mengelola akun admin
- Bisa mengelola user
- Bisa mengelola konten wisata
- Bisa mengelola fasilitas dan harga sewa
- Bisa mengelola berita
- Bisa mengelola event
- Bisa memeriksa submission booking
- Bisa approve atau reject submission

## 3. Struktur Navigasi User App

### 3.1 Beranda

Route web: `/`

Isi utama:

- Hero image
- Deskripsi BLUD Pariwisata
- Galeri horizontal dari data `content`
- Section berita "Kabar Banyumas"
- Header dengan menu:
  - Home
  - Jadwal
  - Objek Wisata
  - Booking
  - Masuk/Daftar atau menu user
- Footer dengan alamat dan sosial media

Data yang dipakai:

- `News::all()`
- `Content::all()`

### 3.2 Jadwal

Route web: `/event`

Isi utama:

- Grid semua lokasi wisata dari tabel `content`
- Tiap item mengarah ke detail jadwal booking per lokasi
- Ada section "Rules Sewa"
- Ada CTA `BOOK NOW!` ke form pengajuan

Data yang dipakai:

- `Content::all()`

### 3.3 Detail Jadwal Booking per Lokasi

Route web: `/booking/{slug}`

Isi utama:

- Daftar bulan yang memiliki event atau submission approved
- Tiap bulan bisa diklik untuk lihat detail event di bulan tersebut

Sumber data:

- Event dari tabel `event` berdasarkan `location = content.name`
- Submission dari tabel `submission` dengan `status = approved` dan `location = content.name`
- Data digabung dan dikelompokkan per bulan

### 3.4 Detail Jadwal Booking per Bulan

Route web: `/booking/{slug}/{bulan}`

Isi utama:

- Daftar event di bulan tertentu
- Menampilkan:
  - rentang tanggal
  - nama event
  - vendor
  - link PDF rundown jika ada

Sumber data:

- Event dari `event`
- Submission approved dari `submission`

Catatan:

- PDF event memakai field `file`
- PDF submission memakai field `actv_letter`

### 3.5 Daftar Objek Wisata

Route web: `/wisata`

Isi utama:

- Grid semua konten wisata
- Tiap item menampilkan gambar, nama, lokasi, tombol detail

Data yang dipakai:

- `Content::all()`

### 3.6 Detail Objek Wisata

Route web: `/wisata/{slug}`

Isi utama:

- Gambar utama
- Nama wisata
- Link sosial media
- Deskripsi
- Jam operasional
- Harga weekday dan weekend
- Lokasi teks
- Embed map jika ada
- Tombol "Info Lebih Lanjut"

Data yang dipakai:

- `Content::where('slug', $slug)->firstOrFail()`

### 3.7 Fasilitas dan Harga Sewa

Route web: `/fasilitas/{slug}`

Isi utama:

- Tabel harga sewa tempat
  - bagian
  - luas
  - harga
- List fasilitas
- CTA ke form pengajuan

Data yang dipakai:

- `Content`
- `ContentFeature` berdasarkan `location = content.id`

Catatan:

- Tabel `content_features` menyimpan dua tipe data:
  - `price`
  - `facility`

### 3.8 Registrasi User

Route web: `/register`

Field:

- `name`
- `phone`
- `email`
- `password`
- `password_confirmation`

Aturan validasi:

- `name`: wajib, min 3
- `phone`: opsional, harus diawali `08` atau `628`
- `email`: wajib, unik
- `password`: wajib, confirmed, min 6

Perilaku:

- Username dibuat otomatis dari nama tanpa spasi dan lowercase
- Jika username sudah ada, ditambah angka di belakang

### 3.9 Login User

Route web: `/login`

Field:

- `email`
- `password`

Perilaku:

- Login memakai guard default user
- Redirect ke home jika sukses

### 3.10 Profil User

Route web: `/profil`

Isi utama:

- Menampilkan username, email, nama lengkap, nomor HP
- Modal edit profil

Field update:

- `name`
- `email`
- `phone`
- `password` opsional

Aturan validasi backend:

- `name`: wajib
- `email`: wajib, email valid, unik kecuali user sendiri
- `phone`: opsional
- `password`: opsional, min 8

### 3.11 Form Pengajuan Sewa

Route web:

- GET `/penyewaan`
- POST `/penyewaan`

Field input:

- `namePIC`
- `no_hp`
- `address`
- `vendor`
- `location`
- `start_date`
- `end_date`
- `name_event`
- `file` (proposal PDF, opsional)
- `ktp` (PDF, wajib)
- `appl_letter` (PDF, opsional)
- `actv_letter` (PDF, opsional)

Aturan validasi backend:

- `namePIC`: wajib, string, max 100
- `no_hp`: wajib, string, max 12
- `address`: wajib
- `vendor`: wajib
- `location`: wajib, harus ada di tabel `content.name`
- `start_date`: wajib, date
- `end_date`: wajib, date
- `name_event`: wajib
- `file`: PDF, max 5048 KB
- `ktp`: PDF, wajib, max 5048 KB
- `appl_letter`: PDF, max 5048 KB
- `actv_letter`: PDF, max 5048 KB

Perilaku:

- Hanya user login yang bisa submit
- Status awal otomatis `pending`
- `apply_date` diisi waktu sekarang
- File disimpan ke storage

### 3.12 Riwayat Pengajuan User

Route web: `/history`

Isi utama:

- List submission milik user login
- Menampilkan:
  - tanggal pengajuan
  - vendor
  - lokasi
  - nama kegiatan
  - link dokumen
  - status
  - catatan admin

Status yang ada:

- `pending`
- `approved`
- `rejected`

## 4. Struktur Navigasi Admin App

### 4.1 Login Admin

Route web: `/login/admin`

Field:

- `username`
- `password`

Perilaku:

- Menggunakan guard `admin`
- Jika sukses redirect ke `/admin/dashboard`

### 4.2 Dashboard

Route web: `/admin/dashboard`

Widget utama:

- Chart statistik pengajuan
- Kalender bulan berjalan
- Aktivitas admin terbaru
- Jumlah akun user terdaftar

Data yang dipakai:

- `User::count()`
- `Activity::with('admin')->latest()->take(5)`
- Count submission per hari untuk 5 hari terakhir

### 4.3 Manajemen Akun Admin

Base route: `/admin/account`

Fitur:

- List admin
- Cari admin berdasarkan username
- Tambah admin
- Edit admin
- Hapus admin

Field admin:

- `username`
- `password`
- `name`
- `email`
- `phone`
- `photo`

Catatan:

- Admin yang sedang login tidak boleh dihapus

### 4.4 Manajemen User

Base route: `/admin/user`

Fitur:

- List user
- Cari user berdasarkan username
- Edit user
- Hapus user

Field editable:

- `name`
- `email`
- `phone`

### 4.5 Manajemen Konten Wisata

Base route: `/admin/content`

Fitur:

- List konten
- Cari konten berdasarkan nama
- Tambah konten
- Edit konten
- Hapus konten

Field konten:

- `name`
- `slug`
- `description`
- `price_weekday`
- `price_weekend`
- `open_time`
- `close_time`
- `location`
- `location_embed`
- `image`
- `instagram`
- `tiktok`
- `whatsapp` muncul saat update/edit

Perilaku:

- `slug` dibentuk dari `name`
- Setelah create, admin diarahkan ke halaman fasilitas

### 4.6 Manajemen Fasilitas dan Harga

Route terkait:

- `GET /admin/content/{id}/facilities`
- `POST /admin/features`
- `POST /admin/feature/edit`

Jenis data:

- `type = facility`
- `type = price`

Field `facility`:

- `facility_name`

Field `price`:

- `bagian`
- `luas`
- `price`

Perilaku:

- Satu konten bisa punya banyak fasilitas dan banyak baris harga
- Update fitur menggunakan sinkronisasi:
  - item yang tidak dikirim lagi akan dihapus

### 4.7 Manajemen Berita

Base route: `/admin/news`

Fitur:

- List berita
- Cari berita berdasarkan title
- Tambah berita
- Edit berita
- Hapus berita

Field berita:

- `title`
- `content`
- `image`
- `source`
- `upload_time` diisi otomatis oleh sistem

### 4.8 Manajemen Event

Base route: `/admin/event`

Fitur:

- List event
- Search berdasarkan vendor
- Tambah event
- Edit event
- Hapus event

Field event:

- `location`
- `vendor`
- `start_date`
- `end_date`
- `name_event`
- `file` PDF opsional

Perilaku penting:

- Sistem mengecek bentrok tanggal event
- Event dianggap bentrok jika range tanggal overlap
- List event admin menggabungkan:
  - data dari tabel `event`
  - data dari `submission` yang sudah approved

### 4.9 Manajemen Submission Booking

Route utama:

- `GET /admin/submission`
- `GET /admin/submission/approved`
- `GET /admin/submission/rejected`
- `GET /admin/submission/{id}/edit`
- `PUT /admin/submission/{id}`
- `PUT /admin/submission/{id}/approve`
- `PUT /admin/submission/{id}/reject`

Fitur:

- List pending
- List approved
- List rejected
- Search berdasarkan vendor
- Lihat dan edit detail submission
- Approve submission
- Reject submission dengan catatan

Perilaku:

- Saat approve, status jadi `approved`
- Saat reject, status jadi `rejected` dan `notes` wajib diisi
- Setelah approve/reject, sistem mengirim email ke user jika email tersedia

Dokumen submission:

- `file` proposal
- `ktp`
- `appl_letter`
- `actv_letter`

## 5. Model Data

### 5.1 users

Kolom penting:

- `id`
- `username`
- `name`
- `email`
- `phone`
- `password`

Dipakai untuk:

- autentikasi user
- pemilik submission

### 5.2 admins

Kolom penting:

- `id`
- `username`
- `password`
- `name`
- `email`
- `phone`
- `photo`

Dipakai untuk:

- autentikasi admin
- relasi activity

### 5.3 content

Kolom penting:

- `id`
- `name`
- `slug`
- `description`
- `price_weekday`
- `price_weekend`
- `open_time`
- `close_time`
- `location`
- `location_embed`
- `image`
- `instagram`
- `tiktok`
- `created_at`
- `updated_at`

Dipakai untuk:

- daftar wisata
- detail wisata
- sumber lokasi booking/event/submission

### 5.4 content_features

Kolom penting:

- `id`
- `location` -> foreign key ke `content.id`
- `type` -> `price` atau `facility`
- `bagian`
- `luas`
- `price`
- `facility_name`
- `icon`

Dipakai untuk:

- harga sewa tempat
- daftar fasilitas

### 5.5 event

Kolom penting:

- `id`
- `vendor`
- `start_date`
- `end_date`
- `name_event`
- `file`
- `location` -> nama lokasi, mengacu ke `content.name`

Dipakai untuk:

- jadwal booking
- detail jadwal
- list event admin

### 5.6 news

Kolom penting:

- `id`
- `title`
- `content`
- `upload_time`
- `source`
- `image`

Dipakai untuk:

- section berita di beranda

### 5.7 submission

Kolom penting:

- `id`
- `user_id`
- `namePIC`
- `no_hp`
- `address`
- `vendor`
- `location` -> nama lokasi, mengacu ke `content.name`
- `apply_date`
- `start_date`
- `end_date`
- `name_event`
- `file`
- `ktp`
- `appl_letter`
- `actv_letter`
- `status`
- `notes`

Dipakai untuk:

- form booking user
- riwayat pengajuan
- review admin
- jadwal booking jika status approved

### 5.8 activities

Kolom penting:

- `id`
- `admin_id`
- `description`
- `created_at`

Dipakai untuk:

- aktivitas terbaru di dashboard admin

## 6. File dan Media

Aplikasi memakai asset statis dan file upload.

### Asset statis utama

Lokasi:

- `public/assets/img`
- `public/assets/svg`
- `public/assets/css`
- `public/assets/js`

Contoh asset:

- logo BLUD
- hero/banner
- icon SVG
- gambar login

### File upload dinamis

Jenis file yang dipakai:

- gambar konten wisata
- gambar berita
- foto admin
- PDF rundown event
- PDF proposal submission
- PDF KTP
- PDF surat pengajuan
- PDF surat kegiatan

Folder logical yang dipakai:

- `assets/content`
- `assets/news`
- `assets/profile`
- `assets/rundowns`
- `assets/ktp`
- `assets/appl_letters`
- `assets/actv_letters`

## 7. Validasi dan Aturan Bisnis Penting

### User

- Login user memakai email + password
- Register otomatis membuat username unik
- Profil bisa ganti password, tapi opsional

### Admin

- Login admin memakai username + password
- Admin aktif tidak boleh menghapus dirinya sendiri

### Konten

- Nama konten harus unik
- Slug dibuat dari nama

### Event

- `end_date` harus lebih besar atau sama dengan `start_date`
- Sistem menolak event yang bentrok tanggal

### Submission

- Hanya user login yang bisa submit
- `ktp` wajib
- Semua dokumen harus PDF
- Status default `pending`
- `reject` wajib mengisi catatan
- Submission approved ikut tampil di jadwal booking

## 8. Integrasi Eksternal

### Google Login

Route ada:

- `/auth/google`
- `/auth/google/callback`

Artinya aplikasi mendukung login Google, walau detail tampilan Flutter nanti perlu disesuaikan.

### Email Status Booking

Mailable:

- `BookingStatusMail`

Digunakan saat:

- approve submission
- reject submission

### Sitemap

Route:

- `/sitemap.xml`

Fungsi:

- untuk SEO web
- tidak terlalu relevan untuk Flutter app native

## 9. Daftar Route Penting untuk Dipetakan ke Flutter

### Public/User

- `/`
- `/event`
- `/booking/{slug}`
- `/booking/{slug}/{bulan}`
- `/wisata`
- `/wisata/{slug}`
- `/fasilitas/{slug}`
- `/login`
- `/register`
- `/profil`
- `/history`
- `/penyewaan`
- `/logout`
- `/auth/google`

### Admin

- `/login/admin`
- `/admin/dashboard`
- `/admin/account`
- `/admin/user`
- `/admin/content`
- `/admin/content/{id}/facilities`
- `/admin/news`
- `/admin/event`
- `/admin/submission`
- `/admin/submission/approved`
- `/admin/submission/rejected`

## 10. Rekomendasi Mapping ke Flutter Screen

### User app

- Splash screen
- Onboarding opsional
- Home screen
- News detail or webview external source
- Wisata list screen
- Wisata detail screen
- Fasilitas dan pricelist screen
- Jadwal lokasi screen
- Jadwal bulanan screen
- Login screen
- Register screen
- Profile screen
- Edit profile screen
- Submission form screen
- Submission history screen
- PDF preview/download screen

### Admin app atau admin mode

- Admin login screen
- Admin dashboard screen
- Admin list screen
- User list screen
- Content list/create/edit screen
- Content feature management screen
- News list/create/edit screen
- Event list/create/edit screen
- Submission pending list screen
- Submission approved list screen
- Submission rejected list screen
- Submission detail/review screen

## 11. Catatan Teknis untuk Versi Flutter

Saat ini project berbasis server-rendered Laravel, bukan API-first. Untuk Flutter, idealnya dibuat API yang mencakup:

- auth user
- auth admin
- profile user
- list content
- content detail
- content features
- list news
- list booking by location
- list booking by month
- create submission multipart upload
- list submission user
- admin dashboard summary
- admin CRUD content/news/event
- admin review submission

Hal yang perlu diperhatikan saat memigrasikan:

- Beberapa relasi masih memakai `location = content.name`, bukan `content_id`
- Upload file perlu endpoint multipart
- PDF perlu strategy preview/download di mobile
- Role user dan admin lebih aman dipisah jelas di auth/token
- Jika ingin satu app untuk user dan admin, perlu role-based navigation

## 12. Ringkasan Inti

Secara fungsional, web ini adalah kombinasi dari:

- aplikasi informasi wisata
- katalog lokasi dan fasilitas
- sistem kalender booking lokasi
- sistem pengajuan sewa dengan upload dokumen
- panel admin untuk operasional konten, event, berita, user, dan approval booking

Dokumen ini bisa dipakai sebagai dasar untuk:

- menyusun requirement Flutter
- menyusun API contract
- membuat daftar screen dan flow
- menentukan model data di Flutter
