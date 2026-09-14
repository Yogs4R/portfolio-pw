# Praktikum 3 - Pemrograman Web: PHP & MySQL Portofolio

Website portofolio personal yang dikembangkan menggunakan **HTML5, CSS3, JavaScript murni, PHP procedural, dan basis data MySQL**. Proyek ini merupakan kelanjutan dari Praktikum 2 dengan penambahan sistem autentikasi, manajemen sesi dan cookie, serta pengelolaan karya/proyek dinamis dari database.

---

## Pemenuhan Kriteria Praktikum 3

1. **Otentikasi Login dengan Cookie & Session**
   - Menggunakan `session_start()` untuk mencatat status sesi login (`$_SESSION['login'] = true`).
   - Menggunakan `setcookie()` untuk fitur *"Ingat Saya"* (menyimpan username di cookie browser pengguna).
   - Pengalihan otomatis (*redirect*) setelah login berhasil langsung menuju laman input form proyek (`input-project.php`).
   - Proteksi laman input form dari akses langsung tanpa login.
   - Fitur logout (`logout.php`) untuk mengosongkan dan menghancurkan session.

2. **Formulir Input Data Proyek via Variabel `$_REQUEST`**
   - Form penambahan karya/proyek portofolio (Judul, Deskripsi, Pilihan Thumbnail, dan Tautan).
   - Pengolahan data input formulir menggunakan variabel superglobal `$_REQUEST['title']`, `$_REQUEST['description']`, dll. sesuai ketentuan modul praktikum.
   - Validasi data input dan proteksi injeksi SQL dasar menggunakan `mysqli_real_escape_string()`.

3. **Database Sederhana & `mysqli`**
   - Menggunakan database MySQL bernama `portfolio_db`.
   - Menggunakan ekstensi `mysqli` (`mysqli_connect`, `mysqli_query`, `mysqli_fetch_assoc`) untuk menghubungkan PHP ke basis data.
   - Menampilkan daftar karya secara dinamis di halaman portofolio (`index.php`) yang terhubung langsung ke tabel `projects`.

---

## Struktur File Proyek

```text
portfolio-pw/
├── database.sql         # Skrip pembuatan basis data & tabel
├── koneksi.php          # Koneksi MySQLi ke database portfolio_db
├── login.php            # Halaman login (Session & Cookie)
├── logout.php           # Proses penghapusan session logout
├── input-project.php    # Form input karya baru via $_REQUEST
├── index.php            # Laman portofolio dinamis dari MySQL
├── index.html           # File statis praktikum sebelumnya (cadangan)
├── style.css            # Estetika retro 90-an Windows 95
├── script.js            # Interaktivitas JavaScript murni
├── images/              # Aset gambar & GIF retro
└── README.md            # Dokumentasi proyek & panduan pengujian
```

---

## Panduan Instalasi & Pengujian (Testing Guide)

### 1. Menyiapkan Web Server & Database di XAMPP
1. Pastikan folder proyek ini berada di dalam direktori `c:\xampp\htdocs\portfolio-pw`.
2. Buka aplikasi **XAMPP Control Panel**.
3. Klik tombol **Start** pada modul **Apache** dan modul **MySQL**.
4. Buka browser dan buka phpMyAdmin di: `http://localhost/phpmyadmin/`.
5. Buat database atau import file `database.sql`:
   - Klik tab **Import** &rarr; pilih file `database.sql` &rarr; klik **Import / Go**.
   - Skrip ini akan membuat basis data `portfolio_db`, tabel `users`, dan tabel `projects`, lengkap dengan akun default:
     - **Username**: `admin`
     - **Password**: `admin123`

---

### 2. Pengujian Skenario Praktikum 3

#### A. Uji Coba Halaman Utama Portofolio (Dinamis)
- Buka tautan: `http://localhost/portfolio-pw/index.php`.
- Perhatikan bagian **Portofolio Proyek**: daftar proyek yang tampil dimuat langsung dari tabel `projects` di database MySQL.
- Pada sidebar kiri terdapat widget **Kelola Portofolio** dengan tombol **Login Admin**.

#### B. Uji Coba Autentikasi (Session & Cookie)
- Klik tombol **Login Admin** atau akses langsung: `http://localhost/portfolio-pw/login.php`.
- Coba masukkan password salah &rarr; sistem menampilkan pesan error retro.
- Masukkan username `admin` dan password `admin123`.
- Beri centang pada opsi **"Ingat Saya (Simpan Username via Cookie)"**.
- Klik tombol **Masuk / Login**.
- **Hasil:** Pengguna otomatis diarahkan ke laman form input `http://localhost/portfolio-pw/input-project.php`.
- **Verifikasi Cookie:** Buka DevTools browser (tekan tombol `F12` &rarr; pilih tab **Application** &rarr; menu **Cookies**) &rarr; cookie `remember_user` dengan nilai `admin` tersimpan.

#### C. Uji Coba Proteksi Session
- Buka jendela baru dengan mode penyamaran (*Incognito / Private Window*).
- Coba buka langsung `http://localhost/portfolio-pw/input-project.php`.
- **Hasil:** Akses ditolak dan Anda otomatis diarahkan kembali ke `login.php`.

#### D. Uji Coba Input Data Proyek via `$_REQUEST`
- Pada halaman `input-project.php` (setelah login):
  1. Masukkan judul proyek baru (misalnya: `Sistem Manajemen Toko Buku`).
  2. Masukkan deskripsi proyek.
  3. Pilih salah satu thumbnail.
  4. Klik tombol **Simpan Proyek**.
- **Hasil:** Muncul notifikasi sukses berwarna hijau, dan data proyek baru langsung tampil pada tabel di bagian bawah halaman.

#### E. Uji Coba Sinkronisasi ke Portofolio Utama
- Klik tombol **Lihat Website Portofolio** di pojok kanan atas form input.
- Scroll ke bagian **Portofolio Proyek**.
- **Hasil:** Proyek baru yang baru saja diinput sudah langsung muncul di grid portofolio.

#### F. Uji Coba Logout
- Klik tombol **Logout**.
- Session login akan dihapus dan pengguna diarahkan kembali ke `login.php`.
- Buka kembali `index.php` &rarr; status admin kembali menjadi mode pengunjung biasa.
