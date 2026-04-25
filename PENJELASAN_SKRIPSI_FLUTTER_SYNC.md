# Penjelasan Skripsi: Menambahkan Aplikasi Flutter pada Website Laravel yang Sudah Berjalan

Dokumen ini dibuat untuk membantu memahami apakah website Laravel yang sudah ada bisa dikembangkan menjadi aplikasi mobile Flutter, dan bagaimana data website serta aplikasi mobile bisa tetap sinkron.

## 1. Situasi Proyek Saat Ini

Saat ini sudah ada sistem berbasis web menggunakan PHP Laravel untuk instansi pemerintah BLUD yang mengelola:

- informasi tempat wisata
- jadwal booking
- pengajuan penyewaan tempat
- manajemen data oleh admin
- data user dan data admin

Artinya fondasi sistem sebenarnya sudah bagus, karena:

- backend sudah ada
- database sudah ada
- logika bisnis sudah ada
- admin panel sudah ada

Yang belum ada adalah aplikasi mobile untuk user atau admin, atau keduanya.

## 2. Apakah Bisa Ditambah Aplikasi Flutter?

Jawabannya: bisa.

Bahkan ini pendekatan yang cukup umum.

Arsitekturnya nanti menjadi seperti ini:

1. Laravel tetap menjadi pusat backend dan database.
2. Website Laravel tetap berjalan seperti biasa.
3. Flutter menjadi client tambahan yang mengambil dan mengirim data ke backend Laravel.

Jadi Flutter tidak menggantikan Laravel.

Flutter hanya menjadi aplikasi mobile yang memakai data dari backend yang sama.

## 3. Apakah Data Website dan Flutter Bisa Sinkron?

Jawabannya: bisa, asalkan website dan Flutter memakai database dan backend yang sama.

Konsep sederhananya seperti ini:

- admin menambah event di website Laravel
- data event masuk ke database MySQL
- aplikasi Flutter mengambil data event dari Laravel
- user di Flutter langsung melihat data terbaru

Contoh lain:

- user mengirim booking dari aplikasi Flutter
- data dikirim ke Laravel
- Laravel menyimpan ke database yang sama
- admin langsung bisa melihat pengajuan itu di website

Jadi sinkronnya bukan karena website dan Flutter saling kirim langsung.

Sinkronnya terjadi karena:

- keduanya memakai backend Laravel yang sama
- keduanya memakai database yang sama

## 4. Supaya Sinkron, Flutter Harus Ambil Data Lewat Apa?

Flutter tidak cocok mengambil data langsung dari tampilan Blade Laravel.

Cara yang benar adalah Laravel menyediakan API.

Berarti nantinya Laravel punya dua peran:

- web server untuk website yang sekarang
- API server untuk aplikasi Flutter

Contoh:

- website membuka route Blade seperti `/wisata`
- Flutter memanggil API seperti `/api/wisata`

Isi datanya sama, tetapi formatnya berbeda:

- website menerima HTML
- Flutter menerima JSON

## 5. Alur Arsitektur yang Disarankan

Arsitektur yang paling aman untuk skripsi ini:

### Tetap pakai Laravel sebagai backend utama

Laravel tetap menangani:

- autentikasi user
- autentikasi admin jika dibutuhkan
- logika booking
- validasi data
- upload file
- approval submission
- pengiriman email
- akses database

### Tambahkan API di Laravel

Buat endpoint API untuk data yang akan dipakai Flutter.

Contoh endpoint yang masuk akal:

- `POST /api/login`
- `POST /api/register`
- `GET /api/home`
- `GET /api/wisata`
- `GET /api/wisata/{slug}`
- `GET /api/fasilitas/{slug}`
- `GET /api/jadwal`
- `GET /api/booking/{slug}`
- `GET /api/booking/{slug}/{bulan}`
- `POST /api/submission`
- `GET /api/history`
- `GET /api/profile`
- `POST /api/profile/update`

### Flutter sebagai frontend mobile

Flutter hanya fokus pada:

- tampilan mobile
- state management
- request ke API Laravel
- penyimpanan token login
- upload file dari device
- menampilkan data dan status booking

## 6. Kenapa Tidak Langsung Flutter ke Database?

Ini tidak disarankan.

Alasannya:

- berbahaya dari sisi keamanan
- susah mengatur validasi
- logika bisnis jadi terpecah
- admin web dan mobile bisa tidak konsisten
- database tidak boleh diakses langsung dari aplikasi client

Yang benar:

- Flutter -> API Laravel -> Database

Bukan:

- Flutter -> Database langsung

## 7. Mekanisme Sinkronisasi Data

Istilah "sinkron" di sini sebenarnya berarti:

- data yang diubah di website akan bisa dibaca di mobile
- data yang dikirim dari mobile akan muncul di website

Cara kerjanya:

### Pola sinkron dasar

1. Semua data disimpan di satu database yang sama.
2. Laravel menjadi satu-satunya pintu masuk data.
3. Website dan Flutter sama-sama membaca dari Laravel.

### Data real-time atau near real-time

Untuk skripsi, Anda tidak perlu membuat sinkronisasi super rumit seperti WebSocket.

Biasanya cukup:

- saat halaman dibuka, Flutter memanggil API terbaru
- saat user melakukan pull-to-refresh, Flutter ambil ulang data
- setelah submit booking, Flutter reload riwayat

Ini sudah cukup dianggap sinkron untuk kebutuhan skripsi.

## 8. Contoh Sinkronisasi Nyata pada Sistem Anda

### Kasus 1: Admin menambah event

Alur:

1. Admin login ke website Laravel.
2. Admin menambah event baru.
3. Data event masuk ke tabel `event`.
4. User membuka aplikasi Flutter.
5. Flutter memanggil endpoint jadwal.
6. Event baru langsung muncul di aplikasi.

### Kasus 2: User booking dari aplikasi Flutter

Alur:

1. User login di Flutter.
2. User isi form pengajuan.
3. Flutter kirim data ke API Laravel.
4. Laravel validasi dan simpan ke tabel `submission`.
5. Admin membuka website.
6. Pengajuan user langsung terlihat di dashboard admin atau halaman submission.

### Kasus 3: Admin menyetujui pengajuan

Alur:

1. Admin approve di website.
2. Status submission berubah menjadi `approved`.
3. User membuka riwayat di aplikasi Flutter.
4. Flutter memanggil ulang API history.
5. Status terbaru tampil di aplikasi.

## 9. Apakah Website Harus Diubah Besar-besaran?

Tidak harus.

Yang paling masuk akal adalah:

- website tetap dipakai seperti sekarang
- backend Laravel ditambah layer API
- Flutter dibangun bertahap dari API tersebut

Jadi Anda tidak perlu membuang web yang sudah jadi.

Anda cukup mengembangkan sistem menjadi multi-platform:

- platform web
- platform mobile

Ini justru bagus untuk nilai skripsi, karena menunjukkan pengembangan sistem, bukan membangun ulang dari nol.

## 10. Apa Saja yang Perlu Ditambah di Laravel?

Supaya Flutter bisa terhubung dengan baik, Laravel perlu ditambah beberapa hal.

### 10.1 API Routes

Buat file route API untuk mobile.

Biasanya di:

- `routes/api.php`

### 10.2 Controller API

Pisahkan controller web dan controller API agar rapi.

Contoh struktur:

- `App\Http\Controllers\User\HomeController` untuk web
- `App\Http\Controllers\Api\User\HomeApiController` untuk mobile

### 10.3 Response JSON

Semua data untuk Flutter sebaiknya dikirim dalam format JSON.

Contoh sederhana:

```json
{
  "success": true,
  "message": "Data wisata berhasil diambil",
  "data": [
    {
      "id": 1,
      "name": "Menara Teratai",
      "slug": "menara-teratai",
      "image": "https://domainanda.com/storage/assets/img/menara.jpg"
    }
  ]
}
```

### 10.4 Authentication Token

Untuk Flutter, login sebaiknya memakai token, bukan session web biasa.

Yang paling cocok di Laravel:

- Laravel Sanctum

Dengan Sanctum:

- user login
- Laravel mengembalikan token
- Flutter menyimpan token
- setiap request berikutnya token dikirim di header

Contoh header:

```http
Authorization: Bearer TOKEN_USER
```

### 10.5 Upload File API

Karena sistem Anda memakai dokumen PDF dan mungkin gambar, API harus mendukung:

- multipart/form-data

Contoh upload:

- proposal PDF
- KTP PDF
- surat pengajuan
- surat kegiatan

Flutter bisa mengirim file ini memakai package seperti:

- `dio`
- `http`
- `file_picker`
- `image_picker` jika perlu

## 11. Bagian Mana yang Sebaiknya Masuk Aplikasi Flutter?

Untuk skripsi, saran terbaik adalah fokus dulu pada area user.

### Flutter untuk user

Yang paling cocok dimasukkan:

- home
- daftar wisata
- detail wisata
- jadwal booking
- detail jadwal per bulan
- fasilitas dan harga sewa
- login/register
- profil user
- form pengajuan
- riwayat pengajuan

Ini sudah kuat sebagai topik skripsi.

### Admin tetap di web

Untuk tahap awal, admin tidak perlu dibuat dalam Flutter.

Alasannya:

- admin biasanya lebih nyaman di web
- fitur admin lebih kompleks
- pengerjaan skripsi jadi lebih fokus
- Anda bisa menjelaskan bahwa web admin tetap dipakai untuk operasional internal

Kalau dipaksakan admin juga masuk Flutter, scope skripsi bisa terlalu besar.

## 12. Saran Scope Skripsi yang Lebih Aman

Scope yang saya sarankan:

**Judul/arah sistem:**

Pengembangan aplikasi mobile berbasis Flutter yang terintegrasi dengan sistem informasi dan booking wisata berbasis Laravel pada BLUD Pariwisata.

Fokus implementasi:

- Laravel tetap sebagai backend utama
- Web admin tetap dipakai
- Flutter dibuat untuk user/public service
- Sinkronisasi data memakai REST API dan database terpusat

Ini kuat secara akademik karena Anda bisa membahas:

- integrasi web dan mobile
- client-server architecture
- REST API
- autentikasi token
- sinkronisasi data antar platform

## 13. Keuntungan Pendekatan Ini

- Tidak membuang sistem lama
- Lebih efisien waktu pengerjaan
- Data tetap satu sumber
- Admin dan mobile user tetap terhubung
- Lebih realistis untuk instansi pemerintah
- Lebih mudah dipresentasikan saat sidang

## 14. Tantangan yang Perlu Diantisipasi

Beberapa hal yang perlu diperhatikan:

### Relasi data saat ini

Di project sekarang ada beberapa bagian yang masih menghubungkan data memakai nama lokasi, bukan `content_id`.

Contoh:

- `event.location` mengacu ke `content.name`
- `submission.location` juga mengacu ke `content.name`

Ini masih bisa jalan, tapi untuk mobile dan API biasanya lebih rapi jika memakai ID.

Kalau tidak sempat refactor besar, sistem sekarang tetap bisa dipakai.

### Upload file di mobile

Upload PDF dari Flutter perlu perhatian khusus:

- izin akses file
- validasi ukuran file
- format multipart

### Login web dan mobile

Web sekarang memakai session.

Flutter lebih cocok memakai token.

Berarti sistem auth perlu dipisahkan:

- web: session
- mobile: token

### URL file

Saat mengirim data ke Flutter, file dan gambar harus berupa URL lengkap, bukan path lokal saja.

Contoh yang benar:

- `https://domainanda.com/storage/assets/news/file1.jpg`

Bukan:

- `assets/news/file1.jpg`

## 15. Rekomendasi Teknis yang Paling Masuk Akal

Kalau saya sarankan langkah teknisnya, urutannya begini:

1. Rapikan dulu data dan fitur web yang sudah ada.
2. Buat API Laravel untuk fitur user.
3. Pasang Laravel Sanctum untuk login mobile.
4. Uji API dengan Postman.
5. Baru bangun aplikasi Flutter.
6. Flutter konsumsi API yang sama.
7. Tambahkan upload file dan history booking.

Ini jauh lebih aman daripada langsung membuat Flutter dulu.

## 16. Contoh Desain Sistem yang Bisa Dijelaskan di Skripsi

### Arsitektur sistem

```text
Admin Web Browser
        |
        v
   Laravel Web + API
        |
        v
     MySQL Database
        ^
        |
        v
  Flutter Mobile App
```

Penjelasannya:

- Laravel menjadi server pusat
- Website admin dan aplikasi Flutter memakai backend yang sama
- Semua data tersimpan di database yang sama
- Sinkronisasi data terjadi karena semua perubahan melewati server yang sama

## 17. Jawaban Singkat untuk Pertanyaan Utama

### Apakah website Laravel yang sudah ada bisa dibuat versi mobile dengan Flutter?

Bisa.

### Apakah data website dan Flutter bisa sinkron?

Bisa, jika keduanya memakai backend Laravel dan database yang sama.

### Bagaimana cara sinkronnya?

Dengan membuat API di Laravel, lalu Flutter membaca dan mengirim data melalui API tersebut.

### Apakah perlu membuat backend baru?

Tidak perlu. Laravel yang sekarang cukup dikembangkan menjadi backend web sekaligus backend API.

### Apakah admin perlu dibuat di Flutter juga?

Tidak wajib. Untuk skripsi, lebih aman jika admin tetap di web dan Flutter fokus ke user.

## 18. Saran Akhir

Untuk skripsi, pendekatan terbaik adalah:

- pertahankan website Laravel yang sudah berjalan
- jadikan Laravel sebagai pusat backend dan database
- tambahkan REST API
- buat aplikasi Flutter untuk layanan user
- sinkronkan data lewat API dan database terpusat

Dengan pendekatan ini, proyek Anda:

- realistis
- kuat secara teknis
- tidak terlalu berisiko
- cocok untuk dijelaskan secara akademik

## 19. Kesimpulan

Anda tidak perlu memilih antara web atau mobile.

Yang paling tepat adalah menjadikan keduanya berjalan bersama.

Website Laravel tetap dipakai untuk operasional dan admin.
Aplikasi Flutter dipakai untuk akses user dari perangkat mobile.
Keduanya tetap sinkron karena memakai backend Laravel dan database yang sama.

Ini adalah model pengembangan sistem yang wajar, profesional, dan sangat layak dijadikan skripsi.
