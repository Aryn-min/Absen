/////code absen/////
cara download dan menjalankan kalian hanya perlu mendownload 2 file .zip di atas untuk menjalankan sistem ini cara nya mudah kalian hanya perlu memiliki domain atau server yg bisa digunakan untuk menyimpan data sqlny file ini, hanya perlu dipindahkan dan diganti dibagian semua keamanan dan cara sistemnya bekerja dgn cara berikut : 
Berikut adalah penjelasan mengenai **cara kerja sistem** serta **panduan langkah demi langkah** untuk memindahkan dan menjalankan aplikasi web absensi ini ke hosting atau domain baru, disusun dengan bahasa yang mudah dipahami.

---

### 1. Cara Kerja Sistem Absensi

Secara sederhana, aplikasi ini terdiri dari 3 bagian utama yang saling bekerja sama:

```
[Pengguna / Browser] 
       ↕ (Mengisi form / Melihat status)
[Halaman Web & Skrip PHP] (index.php, absenpembekalan.php, pemantau.php)
       ↕ (Menyimpan / Mengambil data)
[Database MySQL] (Tempat penyimpanan data absensi)

```

1. **Halaman Formulir (`index.php` / `absenpembekalan.php`)**
Berfungsi sebagai antarmuka tempat pengguna mengisi data diri (nama, identitas, sesi, dsb.).
2. **Skrip Pemroses (PHP)**
Menerima data yang dikirim dari formulir, memvalidasi datanya, lalu menyimpannya ke dalam database.
3. **Database (`if0_..._4bsenn.sql`)**
Tempat penyimpanan digital (tabel) yang mencatat setiap riwayat data absensi secara permanen.
4. **Halaman Monitoring (`pemantau.php`)**
Membaca data dari database dan menampilkannya dalam bentuk tabel/rekapitulasi agar admin atau panitia bisa memantau siapa saja yang sudah absen.
5. **Berkas Pendukung (`bgweb.jpeg`, `logodona.jpg`, `.htaccess`)**
Menyediakan gambar latar, logo tampilan, dan konfigurasi rute/keamanan web server.

---

### 2. Mengapa Perlu Penyesuaian Saat Pindah Hosting?

Setiap penyedia hosting memiliki kredensial database yang berbeda. Database lama Anda (terlihat dari awalan `if0_41262517_...` yang khas pada hosting InfinityFree) tidak akan bisa diakses dari hosting baru.

Oleh karena itu, ada **2 hal utama** yang harus dilakukan:

1. Membuat database baru di hosting baru dan memasukkan (import) struktur data dari file `.sql`.
2. Menyesuaikan nama database, username, dan password di dalam file kode PHP.

---

### 3. Langkah-Langkah Pemasangan di Hosting / Domain Baru

#### Langkah 1: Siapkan File

Pastikan Anda memiliki:

* File arsip web (`Absen.zip`) yang berisi file `.php`, gambar, dan `.htaccess`.
* File database (`if0_41262517_4bsenn.sql`).

---

#### Langkah 2: Buat Database di Hosting Baru

1. Masuk ke panel kontrol hosting Anda (misalnya **cPanel**, **DirectAdmin**, **hPanel**, atau sejenisnya).
2. Cari menu **MySQL Databases** (atau *Basis Data MySQL*).
3. Buat database baru, misalnya dengan nama `db_absen`.
4. Buat pengguna database baru (user), misalnya `user_absen`, beserta password-nya (catat password ini).
5. Hubungkan user tersebut ke database yang baru dibuat (**Add User to Database**), lalu centang opsi **ALL PRIVILEGES** (Semua Hak Akses), kemudian simpan.

---

#### Langkah 3: Impor File `.sql` ke phpMyAdmin

1. Di panel hosting, buka menu **phpMyAdmin**.
2. Klik nama database baru yang tadi Anda buat di panel sebelah kiri.
3. Pilih tab **Import** di bagian atas.
4. Klik tombol **Choose File** / **Pilih File**, lalu pilih file `if0_41262517_4bsenn.sql`.
5. Gulir ke bawah dan klik tombol **Go** / **Impor**. Tunggu hingga muncul pesan berhasil.

---

#### Langkah 4: Sesuaikan Konfigurasi Database pada File PHP

Buka file PHP yang berisi perintah koneksi database (biasanya di bagian atas file `index.php`, `absenpembekalan.php`, `pemantau.php`, atau file `koneksi.php` jika ada).

Cari baris koneksi yang formatnya menyerupai:

```php
$host = "localhost";        // Umumnya tetap "localhost" (atau host DB dari penyedia hosting)
$user = "user_database_baru"; 
$pass = "password_database_baru";
$db   = "nama_database_baru";

$koneksi = mysqli_connect($host, $user, $pass, $db);

```

> **Catatan:** Ganti nilai `$user`, `$pass`, dan `$db` sesuai dengan nama database dan pengguna yang Anda buat pada **Langkah 2**.

---

#### Langkah 5: Unggah File Web ke Hosting

1. Di panel hosting, buka menu **File Manager** (Pengelola Berkas).
2. Masuk ke folder root domain utama, biasanya bernama:
* `public_html` (pada cPanel / DirectAdmin / hPanel)
* `htdocs` (pada vPanel / InfinityFree)
*(Jika ingin diletakkan di subdomain/folder khusus, masuk ke folder tujuan tersebut).*


3. Unggah file `Absen.zip`.
4. Klik kanan pada file `Absen.zip`, lalu pilih **Extract** (Ekstrak).
5. Pastikan file-file seperti `index.php`, `absenpembekalan.php`, `pemantau.php`, `bgweb.jpeg`, dan `logodona.jpg` berada langsung di dalam folder tersebut (tidak bertumpuk di dalam subfolder ekstraksi).

---

#### Langkah 6: Pengujian Sistem

1. Buka browser dan ketik alamat domain Anda (contoh: `[https://namadomainanda.com](https://namadomainanda.com)` atau `[https://namadomainanda.com/absenpembekalan.php](https://namadomainanda.com/absenpembekalan.php)`).
2. Coba lakukan input absensi satu kali untuk menguji apakah data berhasil masuk.
3. Buka halaman `pemantau.php` (contoh: `[https://namadomainanda.com/pemantau.php](https://namadomainanda.com/pemantau.php)`) untuk memastikan data yang baru saja dimasukkan langsung muncul di daftar pemantau.

---

### 4. Hal yang Sering Terjadi (Troubleshooting)

* **Pesan error `Access Denied` atau `Connection Failed**`:
Nama pengguna (username), password, atau nama database di file PHP belum sesuai dengan data di panel hosting, atau user belum diberikan hak akses (*Privileges*).
* **Gambar latar / logo tidak muncul**:
Pastikan nama file gambar huruf besar-kecilnya sama persis (misalnya `bgweb.jpeg` dan `logodona.jpg`), karena server Linux bersifat *case-sensitive*.
* **Halaman 404 / Error 500**:
Periksa isi file `.htaccess` jika ada baris pengaturan konfigurasi server lama yang tidak kompatibel dengan server baru.
