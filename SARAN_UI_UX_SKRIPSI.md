# Rekomendasi Fitur Fungsionalitas & UI/UX (Skripsi)

Untuk membuat aplikasi BLUD Pariwisata kamu terasa lebih premium, modern, dan sangat nyaman digunakan (User Experience yang mantap), berikut adalah saran fitur tambahan yang fokus pada fungsi dan tampilan:

---

## 1. Peningkatan UI/UX (Tampilan & Pengalaman Pengguna)

### A. *Empty States* (Tampilan Saat Data Kosong) yang Menarik
*   **Masalah Saat Ini**: Kalau tidak ada pengajuan baru, tabel hanya menampilkan teks biasa "Data pengajuan belum tersedia". Ini terasa sangat kaku.
*   **Solusi UX**: Ganti dengan ilustrasi gambar (SVG) berdesain modern yang lucu/ramah, disertai teks *"Yeay! Belum ada pengajuan baru hari ini. Waktunya ngopi!"*
*   **Dampak**: Membuat aplikasi terasa "hidup" dan ramah bagi petugas (admin) yang menggunakannya sehari-hari.

### B. Animasi *Skeleton Loading*
*   **Apa ini?**: Daripada menampilkan ikon "*loading muter-muter*" (spinner) saat aplikasi Flutter sedang mengambil data Wisata/Berita dari server, gunakan *Skeleton Loading* (kotak abu-abu berkedip yang menyerupai bentuk konten).
*   **Dampak**: Secara psikologis, *Skeleton Loading* membuat aplikasi terasa memuat jauh lebih cepat dibanding spinner biasa. Aplikasi besar seperti YouTube dan Instagram menggunakan teknik ini.

### C. *Dark Mode* (Mode Gelap) di Web Admin
*   **Apa ini?**: Tambahkan tombol *switch* (matahari/bulan) di pojok kanan atas Dashboard Admin untuk mengubah tema dari Terang ke Gelap.
*   **Dampak**: Fitur ini sangat *trendy*. Dosen penguji biasanya sangat *impressed* dengan fitur sederhana namun memiliki dampak visual yang drastis seperti ini, apalagi karena kamu menggunakan TailwindCSS (implementasinya sangat mudah).

---

## 2. Peningkatan Fungsionalitas (Sangat Berguna untuk Instansi)

### A. Filter Data Tingkat Lanjut (Advanced Filter)
*   **Masalah Saat Ini**: Di halaman "Daftar Pengajuan", Admin baru bisa mencari berdasarkan kolom *Search (Nama Vendor)* saja.
*   **Solusi**: Tambahkan *Dropdown Filter* di sebelah form pencarian:
    *   Filter berdasarkan **Bulan** (Januari, Februari, dll).
    *   Filter berdasarkan **Lokasi Wisata** (Baturraden, Taman Botani).
*   **Dampak**: Saat data pengajuan sudah mencapai ribuan, fitur ini hukumnya **wajib** ada. Admin akan sangat berterima kasih karena mudah mencari laporan spesifik.

### B. *Live Preview* Konten (Simulator HP)
*   **Apa ini?**: Saat Admin sedang mengetik Deskripsi Wisata atau mengunggah Gambar Wisata di Web Admin, sediakan satu kotak di sebelah kanan layar yang berbentuk *Mockup* (bingkai) HP Android.
*   **Fungsi**: Kotak HP tersebut akan langsung menampilkan apa yang sedang diketik Admin secara *real-time*. Jadi Admin tahu persis bagaimana konten tersebut akan terlihat oleh *User* di aplikasi Flutter nantinya sebelum mengklik tombol "Simpan".

### C. DataTables dengan Pagination & Sorting (Pengurutan)
*   **Apa ini?**: Saat ini tabel data (Wisata, Berita, Pengajuan) ditampilkan memanjang ke bawah.
*   **Solusi**: Terapkan *Pagination* (Halaman 1, 2, 3) dan fitur *Sorting* (Admin bisa mengklik judul kolom, misalnya kolom "Tanggal", untuk mengurutkan dari yang terbaru ke terlama).

---

### Ingin Dieksekusi?
Sama seperti sebelumnya, kalau kamu mau saya buatkan fitur **Filter Lokasi & Bulan** di tabel pengajuan, atau mau saya tambahkan **Ilustrasi Empty States** agar UI-nya makin cantik, tinggal kasih instruksi saja! 🚀
