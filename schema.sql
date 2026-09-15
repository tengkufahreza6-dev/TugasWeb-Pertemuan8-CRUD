-- Hapus tabel lama jika ada (berurutan sesuai relasi FK agar tidak conflict)
DROP TABLE IF EXISTS log_aktivitas;
DROP TABLE IF EXISTS produk;
DROP TABLE IF EXISTS supplier;
DROP TABLE IF EXISTS kategori;

-- 1. Tabel Kategori
CREATE TABLE kategori (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_kategori VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 2. Tabel Supplier
CREATE TABLE supplier (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_supplier VARCHAR(100) NOT NULL,
    kontak VARCHAR(50) NOT NULL,
    alamat TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 3. Tabel Produk (Relasi 3NF dengan Kategori & Supplier)
CREATE TABLE produk (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode_produk VARCHAR(20) UNIQUE NOT NULL,
    nama_produk VARCHAR(150) NOT NULL,
    kategori_id INT NOT NULL,
    supplier_id INT NOT NULL,
    harga DECIMAL(12, 2) NOT NULL,
    stok INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (kategori_id) REFERENCES kategori(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (supplier_id) REFERENCES supplier(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- 4. Tabel Log Aktivitas (Bonus Feature: Transaksi Delete Log)
CREATE TABLE log_aktivitas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    aksi VARCHAR(50) NOT NULL,
    keterangan TEXT NOT NULL,
    waktu TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- DATA SEEDING (Minimal 5 data per tabel)

-- Seed Kategori
INSERT INTO kategori (nama_kategori) VALUES
('Elektronik'),
('Aksesoris Komputer'),
('Peralatan Kantor'),
('Jaringan & Networking'),
('Perangkat Audio');

-- Seed Supplier
INSERT INTO supplier (nama_supplier, kontak, alamat) VALUES
('PT Tech Utama', '081234567890', 'Jl. Jend. Sudirman No. 45, Jakarta'),
('CV Nusantara Komputer', '089876543210', 'Jl. Gatot Subroto No. 12, Medan'),
('Global Supply Indo', '085211223344', 'Jl. Asia Afrika No. 88, Bandung'),
('Sinar Mandiri Tech', '087799887766', 'Jl. Pemuda No. 03, Surabaya'),
('Mega Citra Hardware', '081344556677', 'Jl. Diponegoro No. 19, Medan');

-- Seed Produk
INSERT INTO produk (kode_produk, nama_produk, kategori_id, supplier_id, harga, stok) VALUES
('PRD-001', 'Monitor LED 24 Inch 144Hz', 1, 1, 2100000.00, 15),
('PRD-002', 'Mechanical Keyboard Red Switch', 2, 2, 450000.00, 30),
('PRD-003', 'Mouse Wireless Ergonomic', 2, 2, 185000.00, 50),
('PRD-004', 'Router Wi-Fi 6 Dual Band', 4, 4, 750000.00, 10),
('PRD-005', 'Headset Bluetooth TWS Stereo', 5, 3, 299000.00, 25);