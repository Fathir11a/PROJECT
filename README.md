# Halaman Login 
![Alt Text](https://github.com/Fathir11a/project/blob/main/image/image.png)


## Fitur Halaman Login 
- **Autentikasi Pengguna**:
Pengguna dapat login dengan memasukkan username, password, dan email.Script memvalidasi kredensial pengguna terhadap database menggunakan query SQL dengan prepared statement.

- **Role-Based Redirection**:
Setelah login berhasil:
Admin diarahkan ke halaman pilih.php.
User diarahkan ke halaman hello.php#home.

- **Session Management**:
Setelah login berhasil, data pengguna (seperti username dan role) disimpan ke session PHP ($_SESSION).

- **Error Handling**:
Menampilkan pesan error jika:
Username atau email tidak ditemukan.
Password salah.
Role tidak valid.

- **Keamanan Query (Prepared Statement)**:
Menggunakan prepared statement untuk mencegah serangan SQL Injection.

- **Form Validation**:
Menggunakan atribut HTML required untuk memastikan semua input harus diisi.

- **Responsive Login Form**:
Antarmuka login dirancang dengan CSS, termasuk fitur berikut:
Desain responsif dengan tata letak tengah.
Warna gelap dengan elemen UI modern.
Button hover untuk efek interaktif.

- **Register Button**:
Menyediakan tombol untuk mengarahkan pengguna ke halaman registrasi (register.php).

- **Desain UI dengan CSS**:
Desain modern dengan:
Font Roboto dari Google Fonts.
Background dengan pola dari Transparent Textures.
Penggunaan warna dan efek hover.

## Cara Kerja Script Ini:
- **Form Login**:
Pengguna memasukkan username, password, dan email.
Data dikirim menggunakan metode POST ke script PHP yang sama.

- **Proses Login**:
Mengecek apakah username dan email ada di database.
Memverifikasi apakah password cocok.

- **Role Check**:
Berdasarkan peran pengguna (admin atau user), pengguna diarahkan ke halaman yang sesuai.

- **Error Handling**:
Jika login gagal, pesan error ditampilkan di halaman.

- **Antarmuka**:
Menggunakan elemen HTML sederhana untuk form input dan tombol, dihias dengan CSS.
___
# Halaman Registrasi
![alt text](https://github.com/Fathir11a/project/blob/main/image/image-1.png?raw=true)

## Fitur Halaman Registrasi
**Pengguna dapat memasukkan data seperti**:
- Username
- Password
- Email
- Nama Lengkap
- Data dikirimkan melalui metode POST.

**Validasi Input**
- **Validasi kekuatan password**: Harus minimal 8 karakter dan mengandung angka.

- **Validasi email**: Menggunakan fungsi PHP bawaan filter_var untuk memastikan email valid.
Cek input kosong: Semua kolom wajib diisi.

**Pencegahan CSRF (Cross-Site Request Forgery)**

**Menggunakan CSRF token**:

- Token dibuat dengan random_bytes dan disimpan di session.
- Token diverifikasi setiap kali formulir dikirim.
- Mencegah serangan manipulasi data dari sumber tidak sah.

**Error Handling**
- **Menampilkan pesan kesalahan jika**:
- CSRF token tidak valid.
- Input tidak memenuhi kriteria (misalnya, password terlalu lemah).
- Kesalahan saat menyimpan data ke database.

**Pengelolaan Password**:
Password langsung disimpan tanpa hash (penggunaan hashing direkomendasikan, tetapi tidak diimplementasikan di sini).

**Penyimpanan Data ke Database**

- Menggunakan prepared statement untuk mencegah SQL Injection:
**$stmt = $conn->prepare("INSERT INTO project (username, password, email, full_name) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $username, $hashed_password, $email, $full_name);**

- Data yang dimasukkan adalah username, password, email, dan full_name.

**Redirect Setelah Registrasi**:
Setelah data berhasil disimpan, pengguna diarahkan ke halaman login (index.php).

**Desain Antarmuka yang Modern**

**Menggunakan CSS untuk mempercantik formulir:**
- Background gelap dengan pola.
- Elemen UI seperti tombol hover.
- Font modern dari Google Fonts (Roboto).

**Debugging dan Error Reporting**

- **Mengaktifkan error reporting untuk mempermudah debugging selama pengembangan:** 
**ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);**

**Session Management**

- **Memastikan sesi aktif menggunakan:**:
**php
Salin kode
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}**

## Cara Kerja Script Ini
**Pengguna Membuka Formulir**   

- Formulir pendaftaran ditampilkan dengan kolom input untuk username, password, email, dan nama lengkap.

**Validasi Input**

**Script memeriksa apakah input valid:**
- Semua kolom diisi.
- Format email benar.
- Password memenuhi kriteria.

**Pengecekan CSRF:**
Script memvalidasi token CSRF untuk mencegah manipulasi permintaan.

**Menyimpan Data ke Database:**
Jika validasi berhasil, data pengguna disimpan ke tabel project dalam database.

**Redirect:**
Jika proses pendaftaran berhasil, pengguna diarahkan ke halaman login.

**Menampilkan Pesan Kesalahan:**
Jika ada masalah, pesan error ditampilkan di bawah formulir.
___
# Cyber Security Admin Panel

Cyber Security Admin Panel adalah antarmuka berbasis web yang memungkinkan admin untuk mengarahkan pengguna ke halaman yang berbeda berdasarkan pilihan yang tersedia. Script ini dibuat menggunakan PHP, HTML, dan CSS dengan desain modern.

![alt text](https://github.com/Fathir11a/project/blob/main/image/image-2.png)

## Fitur Utama

1. **Autentikasi Admin**
   - Memastikan hanya pengguna yang sudah login sebagai admin yang dapat mengakses halaman ini.
   - Jika pengguna tidak terautentikasi atau bukan admin, akan diarahkan ke halaman login (`index.php`).

2. **Pilihan Redirect**
   - Admin diberikan opsi untuk memilih salah satu halaman berikut:
     - **Hello Page** (`hello.php`)
     - **Dashboard Page** (`dashboard.php`)
   - Redirect dilakukan secara dinamis berdasarkan pilihan admin.

3. **Error Handling**
   - Menampilkan pesan error jika admin tidak memilih halaman sebelum menekan submit.

4. **Tampilan Modern dengan Tema Cybersecurity**
   - Desain responsif dengan fitur-fitur berikut:
     - Background futuristik.
     - Palet warna hijau, hitam, dan putih.
     - Efek hover dan transisi animasi untuk elemen interaktif.

5. **Manajemen Session**
   - Menggunakan session PHP untuk memvalidasi status login dan peran admin.

6. **Navigasi Logout**
   - Link untuk logout tersedia, memungkinkan admin mengakhiri sesi dan kembali ke halaman login.

## Teknologi yang Digunakan

- **PHP**: Untuk logika server-side dan pengelolaan session.
- **HTML & CSS**: Untuk membangun antarmuka pengguna yang responsif dan menarik.
- **Google Fonts**: Untuk tampilan font modern.
- **Responsive Design**: Mendukung berbagai perangkat dan ukuran layar.

## Cara Kerja

1. **Akses Halaman**:
   - Admin harus login terlebih dahulu untuk dapat mengakses halaman ini.
   - Jika tidak terautentikasi, pengguna akan diarahkan ke halaman login.

2. **Pilihan Halaman**:
   - Admin dapat memilih salah satu opsi untuk diarahkan ke halaman tertentu:
     - **Hello Page** atau **Dashboard Page**.

3. **Error Handling**:
   - Jika admin tidak memilih opsi apa pun, pesan error akan ditampilkan.

4. **Logout**:
   - Link logout disediakan untuk mengakhiri sesi admin.
___
# Admin Dashboard 

Dashboard admin ini dirancang untuk mengelola pengguna dan mengatur peran (role) pengguna dalam sistem. Aplikasi ini memastikan bahwa hanya admin yang memiliki akses ke fungsi-fungsi tertentu. Berikut adalah fitur lengkapnya:

![alt text](https://github.com/Fathir11a/project/blob/main/image/image-3.png)

## Fitur Utama

### 1. **Keamanan Akses**
   - **Session Authentication**:
     - Menggunakan session PHP untuk memastikan bahwa pengguna telah login sebelum mengakses dashboard.
   - **Role-based Access Control**:
     - Memastikan hanya admin yang dapat mengakses halaman ini.
     - Pengguna non-admin yang mencoba mengakses halaman ini akan diarahkan ke pesan *Akses Ditolak*.

### 2. **Manajemen Pengguna**
   - **Lihat Daftar Pengguna**:
     - Admin dapat melihat daftar semua pengguna dengan informasi:
       - Username
       - Peran saat ini (role)
   - **Mengubah Role Pengguna**:
     - Admin dapat mengubah peran pengguna menjadi "admin" atau "user".
     - Role dapat diperbarui langsung dari dashboard.

### 3. **Notifikasi**
   - Pesan keberhasilan atau kesalahan akan ditampilkan setelah admin melakukan perubahan:
     - Perubahan role berhasil.
     - Gagal melakukan perubahan (dengan alasan spesifik seperti kegagalan database).

### 4. **Keamanan Data**
   - **Escape Output**:
     - Semua data pengguna yang ditampilkan dieksekusi dengan fungsi `htmlspecialchars()` untuk mencegah serangan XSS.
   - **Form dengan POST Method**:
     - Menggunakan metode `POST` untuk semua operasi perubahan role, memastikan keamanan.

### 5. **Tampilan Modern dan Responsif**
   - **Desain Cybersecurity**:
     - Warna dominan hijau dan hitam memberikan nuansa keamanan.
   - **Tabel Pengguna**:
     - Menampilkan daftar pengguna dalam format tabel yang rapi dan responsif.
     - Baris tabel memiliki efek hover untuk pengalaman pengguna yang lebih baik.
   - **Form dan Tombol**:
     - Tersedia opsi perubahan role dalam bentuk dropdown.
     - Tombol responsif dengan efek hover.

### 6. **Logout**
   - Admin dapat mengakhiri sesi dengan menekan tombol logout.

---

## Teknologi yang Digunakan

- **PHP**: Backend logic dan pengelolaan sesi.
- **PDO**: Pengelolaan database dengan pendekatan aman untuk mencegah SQL Injection.
- **HTML & CSS**: Untuk antarmuka pengguna.
- **Responsive Design**: Mendukung berbagai ukuran layar.

---

## Cara Menjalankan

1. Pastikan server lokal seperti **XAMPP** atau **Laragon** berjalan.
2. Impor database menggunakan SQL file yang sesuai.
3. Atur kredensial database di script:
   ```php
   $host = 'localhost';
   $db = 'membuat_laman_login';
   $user = 'root';
   $pass = '';

___

# Update Role

# Sistem Manajemen Peran Pengguna

Sistem ini dirancang untuk memfasilitasi admin dalam mengelola peran pengguna dengan fitur keamanan tingkat tinggi. Berikut adalah deskripsi fitur utama yang disediakan oleh script.

---

## Fitur Utama

### 1. Keamanan Otentikasi
   - Hanya pengguna yang login dengan role `admin` yang dapat mengakses fitur ini.
   - Menggunakan session PHP untuk memastikan pengguna tetap terautentikasi selama mengakses fitur.

### 2. Manajemen Peran Pengguna
   - Admin dapat mengubah peran pengguna (role) menjadi:
     - **User**
     - **Admin**
   - Perubahan dilakukan melalui form yang terintegrasi dengan database.

### 3. Validasi Input
   - Data `user_id` divalidasi sebagai angka positif.
   - Data `new_role` divalidasi agar sesuai dengan nilai yang diizinkan: `user` atau `admin`.

### 4. Pengelolaan Database
   - Menggunakan **PDO** untuk koneksi aman dan fleksibel ke database.
   - Mendukung mekanisme *prepared statements* untuk mencegah serangan SQL Injection.

### 5. Pesan Informasi
   - Menampilkan pesan keberhasilan jika perubahan role berhasil dilakukan.
   - Menampilkan pesan kesalahan yang detail untuk membantu debugging jika terjadi kegagalan.

### 6. Debugging
   - Mengaktifkan laporan error dengan `error_reporting(E_ALL)` selama proses pengembangan.

---

## Teknologi yang Digunakan

- **PHP**: Backend logic untuk otentikasi, validasi, dan manipulasi data.
- **PDO**: Koneksi aman ke database MySQL.
- **HTML & CSS**: Form sederhana untuk input data.

---

## Instalasi dan Konfigurasi

1. **Konfigurasi Database**:
   - Pastikan Anda memiliki tabel bernama `project` di database Anda dengan kolom:
     - `id` (INT)
     - `username` (VARCHAR)
     - `role` (VARCHAR)
   - Atur koneksi database dalam script:
     ```php
     $host = 'localhost';
     $db = 'membuat_laman_login';
     $user = 'root';
     $pass = '';
     ```

2. **Jalankan Server**:
   - Gunakan server lokal seperti **XAMPP** atau **Laragon**.

3. **Akses Halaman**:
   - Login ke sistem menggunakan akun admin.
   - Navigasikan ke halaman untuk mengubah peran pengguna.

___

# Dashboard PT Sekuriti Siber Indonesia

Aplikasi ini adalah sistem dashboard berbasis web untuk pengguna PT Sekuriti Siber Indonesia. Sistem ini menyediakan fitur manajemen profil, komunikasi melalui pesan, dan informasi layanan keamanan siber.

# image
## Home ![Screen Shot 2024-12-12 at 15 30 02](https://github.com/user-attachments/assets/c20c8d54-f88a-4fda-8665-4fd604528ad1)
## Profil ![alt text](https://github.com/Fathir11a/project/blob/main/image/image-5.png)
## Pesan ![alt text](https://github.com/Fathir11a/project/blob/main/image/image-6.png)

## Fitur Utama

1. **Autentikasi Pengguna**
   - Hanya pengguna yang sudah login dapat mengakses dashboard.
   - Pengguna tidak login akan diarahkan ke halaman login.

2. **Profil Pengguna**
   - Menampilkan informasi pengguna:
     - Nama lengkap
     - Email
     - Nomor telepon
     - Alamat
     - Foto profil
   - Link untuk mengedit data profil.

3. **Manajemen Pesan**
   - Kirim pesan ke pengguna lain yang terdaftar di sistem.
   - Menampilkan daftar pesan masuk dan keluar.
   - Fitur filter untuk melihat pesan dari pengguna tertentu.

4. **Daftar Layanan Siber**
   - Menampilkan layanan keamanan siber dalam bentuk kartu informatif:
     - **Cyber Security**: Melindungi sistem informasi dari ancaman.
     - **Data Encryption**: Keamanan data dengan enkripsi.
     - **Penetration Testing**: Identifikasi celah keamanan.
     - **Incident Response**: Tanggapan cepat terhadap insiden siber.
     - **Threat Intelligence**: Analisis ancaman terbaru.
     - **Cloud Security**: Keamanan data berbasis cloud.

5. **Navigasi Sidebar**
   - Sidebar interaktif untuk navigasi ke berbagai bagian aplikasi:
     - Home
     - Profil
     - Kirim Pesan
     - Data
     - Logout

6. **Navigasi Berdasarkan URL Hash**
   - Konten ditampilkan atau disembunyikan secara dinamis tanpa reload halaman.

7. **Antarmuka Modern dan Responsif**
   - Desain UI/UX modern dengan CSS yang responsif.

---

## Teknologi yang Digunakan

- **Bahasa Pemrograman**: PHP, HTML, CSS, JavaScript
- **Database**: MySQL
- **Framework dan Library**:
  - Font Awesome (untuk ikon)
- **Server**: Apache (XAMPP atau LAMP)

---

## Struktur Database

### Tabel `project` (Pengguna)
| Kolom            | Tipe Data        | Deskripsi                          |
|-------------------|------------------|------------------------------------|
| `id`             | INT (Primary Key, Auto Increment) | ID unik pengguna                 |
| `username`       | VARCHAR(50)      | Nama pengguna                      |
| `email`          | VARCHAR(100)     | Alamat email                       |
| `full_name`      | VARCHAR(100)     | Nama lengkap                       |
| `phone`          | VARCHAR(15)      | Nomor telepon                      |
| `address`        | TEXT             | Alamat pengguna                    |
| `profile_picture`| VARCHAR(255)     | Nama file foto profil              |

### Tabel `messages` (Pesan)
| Kolom            | Tipe Data        | Deskripsi                          |
|-------------------|------------------|------------------------------------|
| `id`             | INT (Primary Key, Auto Increment) | ID unik pesan                     |
| `sender_id`      | INT              | ID pengirim (relasi ke `project.id`)|
| `receiver_id`    | INT              | ID penerima (relasi ke `project.id`)|
| `message`        | TEXT             | Isi pesan                          |

---

## Cara Penggunaan
**Login:**
- Masukkan nama pengguna dan password untuk login.
- Sistem akan mengarahkan ke dashboard jika berhasil login.

**Profil**
- Klik pada menu Profil di sidebar untuk melihat detail informasi pribadi.
- Klik Edit Profil untuk memperbarui data.

**Mengirim Pesan:**
- Pilih menu Pesan di sidebar.
- Pilih pengguna tujuan, tuliskan pesan, lalu klik Kirim Pesan.

**Melihat Pesan:**
- Semua pesan yang diterima dan dikirim akan ditampilkan di bagian pesan.
- Gunakan fitur filter untuk menampilkan pesan dari pengguna tertentu.
----



# Kirim Pesan

Skrip PHP ini digunakan untuk mengirim pesan antara pengguna dalam sistem. Skrip ini menangani pengiriman pesan dari pengirim (sender) ke penerima (receiver) dan menyimpannya dalam database.

## Kirim Pesan ![alt text](https://github.com/Fathir11a/project/blob/main/image/image-6.png)

## Fitur Utama

1. **Pengiriman Pesan:**
   - Pengguna dapat mengirim pesan melalui formulir yang disediakan di halaman web.
   - Pengguna harus menyediakan `sender_id` (ID pengirim), `receiver_id` (ID penerima), dan isi pesan (`message`).

2. **Penyimpanan Pesan ke Database:**
   - Pesan yang dikirim akan disimpan dalam tabel `messages` di database.
   - Data yang disimpan meliputi `sender_id`, `receiver_id`, dan `message`.
   
3. **Keamanan dengan Prepared Statement:**
   - Skrip menggunakan `prepared statement` untuk mencegah serangan SQL injection, dengan mengikat parameter pada query SQL (`sender_id`, `receiver_id`, dan `message`).
   
4. **Redirect Setelah Pengiriman Pesan:**
   - Setelah pesan berhasil dikirim, pengguna akan diarahkan kembali ke halaman `hello.php#send_message` menggunakan `header("Location: hello.php#send_message");`.
   
5. **Notifikasi Keberhasilan atau Kegagalan:**
   - Jika pengiriman pesan berhasil, pengguna akan diberi tahu dengan pesan "Pesan berhasil dikirim!".
   - Jika pengiriman pesan gagal, pengguna akan diberi tahu dengan pesan "Gagal mengirim pesan.".

## Alur Kerja

1. Pengguna mengisi formulir dengan `sender_id`, `receiver_id`, dan pesan yang ingin dikirim.
2. Data pesan dikirim menggunakan metode POST ke server.
3. Skrip PHP ini memproses data, menyimpannya dalam database, dan memberi notifikasi tentang status pengiriman pesan.
4. Jika pengiriman pesan berhasil, pengguna akan diarahkan kembali ke bagian yang sesuai di halaman `hello.php` untuk melihat pesan atau melanjutkan aktivitas.

## Persyaratan

- **Database MySQL:** Skrip ini membutuhkan database MySQL dan tabel `messages` yang memiliki kolom:
    - `sender_id` (int)
    - `receiver_id` (int)
    - `message` (text)
    
- **Koneksi Database:** Pastikan file `koneksi.php` memiliki pengaturan yang benar untuk menghubungkan ke database MySQL.

#  Reading Data

Skrip PHP ini memungkinkan pengguna untuk memilih dan menampilkan isi file yang ada pada server, khususnya file dalam direktori yang telah ditentukan. Fitur yang tersedia dalam skrip ini adalah:

## Fitur Utama

1. **Pilih File dari Direktori:**
   - Pengguna dapat memilih file yang tersedia dalam direktori `files/` melalui dropdown list.
   - File yang tersedia akan ditampilkan secara dinamis, dengan menghindari folder dan file yang tidak relevan (seperti `.` dan `..`).

2. **Validasi Keberadaan File:**
   - Skrip memeriksa apakah file yang dipilih oleh pengguna ada di direktori.
   - Jika file ditemukan, skrip akan membaca dan menampilkan isi file tersebut.

3. **Tampilkan Isi File:**
   - Jika file ditemukan, isi file akan ditampilkan pada halaman.
   - Untuk file dengan format CSV, data akan diproses dan ditampilkan dalam bentuk tabel.
   - Untuk file selain CSV, isi file akan ditampilkan sebagai teks biasa dalam elemen `<pre>`.

4. **Pemisahan dan Pemrosesan CSV:**
   - Jika file yang dipilih adalah file CSV, skrip akan mengubah data CSV menjadi array dan menampilkannya dalam bentuk tabel HTML yang terstruktur.
   - Header dari file CSV akan digunakan sebagai judul kolom dalam tabel, dan data akan ditampilkan dalam baris-baris yang sesuai.

5. **Pesan Error:**
   - Jika file yang dipilih tidak ditemukan, pesan error akan ditampilkan untuk memberi tahu pengguna bahwa file tidak ada di server.

6. **Desain Responsif dan Interaktif:**
   - Halaman menggunakan CSS untuk memberikan desain yang bersih, modern, dan responsif.
   - Antarmuka pengguna dirancang agar mudah digunakan dengan elemen interaktif seperti dropdown untuk memilih file dan tombol untuk mengonfirmasi pilihan.
   - Ketika pengguna memilih file, efek hover dan transisi pada tombol memberikan pengalaman pengguna yang lebih menyenangkan.

## Cara Penggunaan

1. Pastikan file PHP ini dapat mengakses folder `files/` di server Anda, dan folder tersebut berisi file yang ingin Anda tampilkan.
2. Pilih file dari dropdown yang disediakan dan klik tombol "Baca File".
3. Jika file yang dipilih adalah CSV, data akan ditampilkan dalam bentuk tabel.
4. Jika file bukan CSV, maka isi file akan ditampilkan dalam bentuk teks biasa.

## Persyaratan

- Server web yang mendukung PHP (misalnya, Apache atau Nginx).
- Folder `files/` harus ada dan berisi file yang dapat dibaca.
- File CSV harus memiliki format yang benar untuk dapat diproses dan ditampilkan dengan benar.

