<?php
require_once 'config/Database.php';
require_once 'functions.php';

$db = Database::getInstance()->getConnection();

// Ambil Data Kategori & Supplier untuk Dropdown Form
$kategoriList = $db->query("SELECT * FROM kategori ORDER BY nama_kategori ASC")->fetchAll();
$supplierList = $db->query("SELECT * FROM supplier ORDER BY nama_supplier ASC")->fetchAll();

// Handling Submit Form (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode_produk = trim($_POST['kode_produk']);
    $nama_produk = trim($_POST['nama_produk']);
    $kategori_id = (int)$_POST['kategori_id'];
    $supplier_id = (int)$_POST['supplier_id'];
    $harga = (float)$_POST['harga'];
    $stok = (int)$_POST['stok'];

    // Validasi Sederhana
    if (empty($kode_produk) || empty($nama_produk) || empty($kategori_id) || empty($supplier_id) || $harga <= 0) {
        setFlashMessage('danger', 'Semua field wajib diisi dengan benar!');
    } else {
        try {
            // Insert Data dengan Prepared Statement (Keamanan No. 7)
            $sql = "INSERT INTO produk (kode_produk, nama_produk, kategori_id, supplier_id, harga, stok) 
                    VALUES (:kode, :nama, :kategori, :supplier, :harga, :stok)";
            $stmt = $db->prepare($sql);
            $stmt->execute([
                ':kode'     => $kode_produk,
                ':nama'     => $nama_produk,
                ':kategori' => $kategori_id,
                ':supplier' => $supplier_id,
                ':harga'    => $harga,
                ':stok'     => $stok
            ]);

            setFlashMessage('success', 'Produk berhasil ditambahkan!');
            header('Location: index.php'); // PRG Pattern (Redirect)
            exit;
        } catch (PDOException $e) {
            setFlashMessage('danger', 'Gagal menambahkan produk (Kode Produk mungkin sudah ada).');
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>📦</text></svg>">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Produk — TR 8</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container" style="max-width: 600px;">
        <h2>➕ Tambah Produk Baru</h2>

        <?= displayFlashMessage(); ?>

        <form action="create.php" method="POST">
            <div class="form-group">
                <label>Kode Produk</label>
                <input type="text" name="kode_produk" placeholder="Contoh: PRD-006" required>
            </div>

            <div class="form-group">
                <label>Nama Produk</label>
                <input type="text" name="nama_produk" placeholder="Nama lengkap produk" required>
            </div>

            <div class="form-group">
                <label>Kategori</label>
                <select name="kategori_id" required>
                    <option value="">-- Pilih Kategori --</option>
                    <?php foreach ($kategoriList as $k): ?>
                        <option value="<?= $k['id']; ?>"><?= e($k['nama_kategori']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Supplier</label>
                <select name="supplier_id" required>
                    <option value="">-- Pilih Supplier --</option>
                    <?php foreach ($supplierList as $s): ?>
                        <option value="<?= $s['id']; ?>"><?= e($s['nama_supplier']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Harga (Rp)</label>
                <input type="number" name="harga" min="0" step="100" placeholder="0" required>
            </div>

            <div class="form-group">
                <label>Stok</label>
                <input type="number" name="stok" min="0" placeholder="0" required>
            </div>

            <div style="display: flex; gap: 10px; margin-top: 24px;">
                <button type="submit" class="btn btn-primary">Simpan Produk</button>
                <a href="index.php" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</body>
</html>