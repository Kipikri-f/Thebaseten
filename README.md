# 🎓 BasisData2026 — TheBaseTen

Sistem Informasi Manajemen Data Akademik berbasis **PHP & MySQL**, dibuat oleh **Kelompok 10** Universitas Djuanda.

---

## 📁 Struktur Proyek

```
kelompok10/
├── index.php               # Shell utama aplikasi (layout + routing)
├── login.php               # Halaman login
├── logout.php              # Handler logout
│
├── pages/                  # Halaman konten (di-include oleh index.php)
│   ├── home.php            # Dashboard / halaman utama
│   ├── mahasiswa.php       # CRUD Data Mahasiswa
│   ├── dosen.php           # CRUD Data Dosen
│   ├── matakuliah.php      # CRUD Data Mata Kuliah
│   ├── querynilai.php      # CRUD + Kalkulasi Nilai Mahasiswa
│   ├── dopem.php           # CRUD Dosen Pembimbing
│   └── anggota.php         # CRUD Anggota Kelompok
│
├── includes/               # Helper & konfigurasi
│   ├── koneksi.php         # Koneksi database MySQL
│   ├── auth.php            # Auth guard (cek session login)
│   └── helpers.php         # Fungsi tanggal Indonesia
│
├── assets/
│   └── style.css           # Stylesheet utama (responsive)
│
└── gambar/
    ├── unida.png           # Logo UNIDA (topbar & login)
    └── unidano.jpg         # Logo UNIDA (alternatif)
```

---

## ⚙️ Persyaratan

| Komponen | Versi Minimum |
|---|---|
| PHP | 7.4+ |
| MySQL / MariaDB | 5.7+ |
| Web Server | Apache / Nginx / XAMPP |

---

## 🚀 Cara Instalasi

### 1. Clone / Ekstrak Proyek

Letakkan folder `kelompok10` di dalam direktori web server kamu:
- **XAMPP**: `C:/xampp/htdocs/kelompok10`
- **LAMP**: `/var/www/html/kelompok10`

### 2. Import Database

Buka phpMyAdmin atau MySQL CLI, lalu jalankan:

```sql
CREATE DATABASE IF NOT EXISTS (namadatabase)
  CHARACTER SET utf8
  COLLATE utf8_general_ci;

USE (namadatabase);

-- Tabel Mahasiswa
CREATE TABLE tbl_mhs (
    nim      VARCHAR(20) PRIMARY KEY,
    namamhs  VARCHAR(100) NOT NULL
);

-- Tabel Dosen
CREATE TABLE tbl_dosen (
    nid        VARCHAR(20) PRIMARY KEY,
    namadosen  VARCHAR(100) NOT NULL
);

-- Tabel Mata Kuliah
CREATE TABLE tbl_matakuliah (
    kodemk  VARCHAR(20) PRIMARY KEY,
    namamk  VARCHAR(100) NOT NULL,
    sks     TINYINT NOT NULL
);

-- Tabel Nilai
CREATE TABLE tbl_nilai (
    nim     VARCHAR(20) PRIMARY KEY,
    tugas   FLOAT NOT NULL,
    uts     FLOAT NOT NULL,
    uas     FLOAT NOT NULL,
    akhir   FLOAT NOT NULL,
    hm      VARCHAR(2) NOT NULL,
    status  VARCHAR(15) NOT NULL,
    FOREIGN KEY (nim) REFERENCES tbl_mhs(nim)
);

-- Tabel Dosen Pembimbing
CREATE TABLE tbl_dopem (
    nim  VARCHAR(20) PRIMARY KEY,
    nid  VARCHAR(20) NOT NULL,
    FOREIGN KEY (nim) REFERENCES tbl_mhs(nim),
    FOREIGN KEY (nid) REFERENCES tbl_dosen(nid)
);

-- Tabel Anggota Kelompok
CREATE TABLE tbl_anggota (
    nim      VARCHAR(20) PRIMARY KEY,
    namamhs  VARCHAR(100) NOT NULL,
    jurusan  VARCHAR(100) NOT NULL
);
```

### 3. Konfigurasi Database

Edit file `includes/koneksi.php` sesuai konfigurasi MySQL kamu:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');          // isi password jika ada
define('DB_NAME', 'learnclidatabase');
```

### 4. Akses Aplikasi

Buka browser dan akses:

```
http://localhost/Thebaseten/login.php
```

---

## 🔐 Akun Default

| Username | Password |
|---|---|
| `admin` | `admin123` |
| `memberten` | `baseten123` |

> ⚠️ **Ubah credential ini** di file `login.php` sebelum deploy ke server publik. Idealnya gunakan database dengan password hash (bcrypt).

---

## ✨ Fitur Aplikasi

| Fitur | Keterangan |
|---|---|
| **Login / Logout** | Autentikasi berbasis session PHP |
| **Auth Guard** | Semua halaman terlindungi — redirect ke login jika belum masuk |
| **Manajemen Mahasiswa** | CRUD: tambah, edit, hapus data mahasiswa |
| **Manajemen Dosen** | CRUD data dosen pengajar |
| **Manajemen Mata Kuliah** | CRUD kurikulum beserta bobot SKS |
| **Nilai Mahasiswa** | Input nilai tugas/UTS/UAS dengan kalkulasi otomatis (A–E) |
| **Dosen Pembimbing** | Relasi mahasiswa ke dosen pembimbing |
| **Anggota Kelompok** | Data anggota kelompok 10 |
| **Responsive** | Tampilan adaptif: desktop, tablet, dan mobile |
| **Flash Messages** | Notifikasi sukses/gagal tanpa `alert()` JavaScript |

---

## 📐 Formula Perhitungan Nilai

```
Nilai Akhir = (Tugas × 20%) + (UTS × 35%) + (UAS × 45%)
```

| Nilai Akhir | Huruf Mutu | Status |
|---|---|---|
| ≥ 85 | A | Lulus |
| 70 – 84 | B | Lulus |
| 55 – 69 | C | Lulus |
| 40 – 54 | D | Tidak Lulus |
| < 40 | E | Tidak Lulus |

---

## 🔧 Perubahan dari Versi Sebelumnya

- **Refactor struktur**: file dipisah ke folder `pages/`, `includes/`, `assets/`
- **Login & Logout**: halaman login baru + session-based auth guard di semua halaman
- **Ganti layout tabel HTML** → flexbox + CSS modern yang responsive
- **Sidebar mobile**: hamburger menu dengan overlay
- **Flash messages**: menggantikan `alert()` JavaScript dengan notifikasi inline
- **Hapus duplikasi**: `koneksi.php` hanya di-include sekali per halaman via `includes/`
- **Kode lebih bersih**: pemisahan logika PHP (backend) dari markup HTML

---

## 🛡️ Keamanan (Basic)

- Semua input menggunakan `mysqli_real_escape_string()`
- Output menggunakan `htmlspecialchars()` untuk mencegah XSS
- Session-based authentication di setiap halaman
- Credential login tersimpan di server-side

> Untuk keamanan produksi: gunakan prepared statements (PDO/MySQLi), password hashing (bcrypt), dan HTTPS.

---

## 👥 Kelompok 10

**TheBaseTen** — Mahasiswa Ilmu Komputer 
Universitas Djuanda (UNIDA) — 2026

---

*Made with ☕ and PHP*
