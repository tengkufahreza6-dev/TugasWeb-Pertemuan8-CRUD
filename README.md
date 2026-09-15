# 📦 Sistem Inventaris Barang — Tugas Rutin 8 (TR 8)

![PHP](https://img.shields.io/badge/PHP-8.x-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![Architecture](https://img.shields.io/badge/Architecture-3NF-00599C?style=for-the-badge)
![Security](https://img.shields.io/badge/Security-Prepared%20Statements-success?style=for-the-badge)

Aplikasi web manajemen inventaris barang berbasis **PHP Native** tanpa *framework*, dirancang dengan arsitektur database **3NF (Third Normal Form)**, menerapkan pola desain **Singleton PDO**, serta menggunakan pengamanan data **Prepared Statements** dan sanitasi **XSS Protection**.

---

## 🔗 Tautan Repositori

- **GitHub Repository:** [TugasWeb-Pertemuan8-CRUD](https://github.com/tengkufahreza6-dev/TugasWeb-Pertemuan8-CRUD)
- **Live Demo / Deployment:** *(Opsional — isi jika sudah deploy)*

---

## 👤 Identitas Mahasiswa

| Keterangan | Detail |
| :--- | :--- |
| **Nama** | Tengku Fahreza |
| **NIM** | 4252550005 |
| **Kelas** | PSIK 25B |
| **Program Studi** | S1 Ilmu Komputer |
| **Mata Kuliah** | Pemrograman Web |
| **Instansi** | Universitas Negeri Medan (UNIMED) |

---

## 🚀 Fitur Utama

- **CRUD Operations (Create, Read, Update, Delete):**
  - **Read:** Menampilkan daftar produk terurut (*latest first*) dengan relasi `JOIN` ke tabel Kategori dan Supplier.
  - **Create:** Menambah produk baru menggunakan *dynamic dropdown selection*.
  - **Update:** Memperbarui data produk melalui form *pre-filled*.
  - **Delete:** Menghapus data produk dilengkapi konfirmasi modal JavaScript.

- **Keamanan & Standar Pengodean:**
  - **PDO Singleton Pattern:** Menjamin efisiensi koneksi database (hanya 1 instance per sesi).
  - **SQL Injection Prevention:** 100% query input/bind menggunakan *Prepared Statements*.
  - **XSS Protection:** Output data disanitasi menggunakan helper `htmlspecialchars()`.
  - **PRG Pattern (Post-Redirect-Get):** Mencegah duplikasi submit form saat refresh halaman.

- **Fitur Tambahan & UI/UX:**
  - **Fitur Pencarian:** Filter data berdasarkan Nama Produk atau Kode Produk.
  - **Pagination:** Navigasi halaman data terbagi secara dinamis.
  - **DB Transaction & Logging (Bonus):** Aktivitas penghapusan produk dicatat otomatis ke tabel `log_aktivitas` menggunakan *Database Transaction* (`beginTransaction`, `commit`, `rollBack`).
  - **Indikator Stok Kritis:** Peringatan visual otomatis untuk stok barang ≤ 5 unit.
  - **Responsive Dark Glassmorphism UI:** Antarmuka modern yang nyaman diakses dari berbagai ukuran layar.

---

## 📷 Tangkapan Layar Antarmuka

| Halaman Utama (Read Data) | Fitur Pencarian Data |
| :---: | :---: |
| ![Halaman Utama](Screenshot-Halaman-Utama.png) | ![Pencarian Data](Screenshot-Pencarian-Data.png) |

| Form Tambah Produk (Create) | Form Edit Produk (Update) |
| :---: | :---: |
| ![Form Tambah](Screenshot-Form-Tambah-Produk.png) | ![Form Edit](Screenshot-Form-Edit-Produk.png) |

| Flash Message Sukses | Konfirmasi Hapus Data |
| :---: | :---: |
| ![Flash Sukses](Screenshot-Flash-Message-Sukses.png) | ![Konfirmasi Hapus](Screenshot-Konfirmasi-Hapus.png) |

| Struktur Tabel Database (HeidiSQL) |
| :---: |
| ![Tabel Database](Screenshot-Tabel-Database-Heidisql.png) |

---

## 🗄️ Skema & Struktur Database (3NF)

Aplikasi ini menggunakan 3 tabel utama yang terintegrasi via *Foreign Key* (FK), serta 1 tabel untuk audit *log*:

| No | Nama Tabel | Kolom Utama |
| :-: | :--- | :--- |
| 1 | `kategori` | `id`, `nama_kategori`, `created_at` |
| 2 | `supplier` | `id`, `nama_supplier`, `kontak`, `alamat`, `created_at` |
| 3 | `produk` | `id`, `kode_produk`, `nama_produk`, `kategori_id`, `supplier_id`, `harga`, `stok`, `created_at` |
| 4 | `log_aktivitas` | `id`, `aksi`, `keterangan`, `waktu` |

**Relasi:**
- `produk.kategori_id` → `kategori.id` (FK, ON DELETE CASCADE)
- `produk.supplier_id` → `supplier.id` (FK, ON DELETE CASCADE)

---

## 🛠️ Teknologi yang Digunakan

- **PHP Native 8.x** — Backend logic, session management, dan PDO database connection.
- **MySQL / MariaDB** — Database relasional dengan skema 3NF.
- **PDO (PHP Data Objects)** — Koneksi database dengan *prepared statements*.
- **HTML5 & CSS3** — Struktur semantik dan styling Dark Glassmorphism.
- **JavaScript (Vanilla)** — Konfirmasi hapus via `confirm()` dialog.

---

## 📂 Struktur Direktori Proyek

```text
TugasWeb-Pertemuan8-CRUD/
├── config/
│   └── Database.php         (Singleton Pattern PDO)
├── css/
│   └── style.css            (Dark Glassmorphism Styling)
├── index.php                (Read — Daftar Produk + Pagination + Search)
├── create.php               (Create — Form Tambah Produk)
├── edit.php                 (Update — Form Edit Produk)
├── delete.php               (Delete — Hapus Produk + Logging)
├── functions.php            (Helper: Sanitasi & Flash Message)
├── schema.sql               (Dump Database + Seeder Data)
├── README.md
└── screenshot-*.png         (Dokumentasi Tangkapan Layar)