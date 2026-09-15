<?php
require_once 'config/Database.php';
require_once 'functions.php';

$db = Database::getInstance()->getConnection();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// 1. Ambil Data Produk Lama (Pre-filled Form)
$stmt = $db->prepare("SELECT * FROM produk WHERE id = :id");
$stmt->execute([':id' => $id]);
$produk = $stmt->fetch();

if (!$produk) {
    setFlashMessage('danger', 'Data produk tidak ditemukan!');
    header('Location: index.php');
    exit;
}

// 2. Ambil Data Dropdown Kategori & Supplier
$kategoriList = $db->query("SELECT * FROM kategori ORDER BY nama_kategori ASC")->fetchAll();
$supplierList = $db->query("SELECT * FROM supplier ORDER BY nama_supplier ASC")->fetchAll();

// 3. Handling Submit Form Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode_produk = trim($_POST['kode_produk']);
    $nama_produk = trim($_POST['nama_produk']);
    $kategori_id = (int)$_POST['kategori_id'];
    $supplier_id = (int)$_POST['supplier_id'];
    $harga = (float)$_POST['harga'];
    $stok = (int)$_POST['stok'];

    if (empty($kode_produk) || empty($nama_produk) || empty($kategori_id) || empty($supplier_id) || $harga <= 0) {
        setFlashMessage('danger', 'Semua field wajib diisi dengan benar!');
    } else {
        try {
            // Update Data dengan Prepared Statement (Keamanan No. 7)
            $sql = "UPDATE produk 
                    SET kode_produk = :kode, nama_produk = :nama, kategori_id = :kategori, 
                        supplier_id = :supplier, harga = :harga, stok = :stok 
                    WHERE id = :id";
            $stmtUpdate = $db->prepare($sql);
            $stmtUpdate->execute([
                ':kode'     => $kode_produk,
                ':nama'     => $nama_produk,
                ':kategori' => $kategori_id,
                ':supplier' => $supplier_id,
                ':harga'    => $harga,
                ':stok'     => $stok,
                ':id'       => $id
            ]);

            setFlashMessage('success', 'Data produk berhasil diperbarui!');
            header('Location: index.php'); // PRG Pattern
            exit;
        } catch (PDOException $e) {
            setFlashMessage('danger', 'Gagal memperbarui produk (Kode Produk mungkin duplikat).');
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
    <title>Edit Produk — TR 8</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container" style="max-width: 600px;">
        <h2>✏️ Edit Data Produk</h2>

        <?= displayFlashMessage(); ?>

        <form action="edit.php?id=<?= $id; ?>" method="POST">
            <div class="form-group">
                <label>Kode Produk</label>
                <input type="text" name="kode_produk" value="<?= e($produk['kode_produk']); ?>" required>
            </div>

            <div class="form-group">
                <label>Nama Produk</label>
                <input type="text" name="nama_produk" value="<?= e($produk['nama_produk']); ?>" required>
            </div>

            <div class="form-group">
                <label>Kategori</label>
                <select name="kategori_id" required>
                    <?php foreach ($kategoriList as $k): ?>
                        <option value="<?= $k['id']; ?>" <?= ($k['id'] == $produk['kategori_id']) ? 'selected' : ''; ?>>
                            <?= e($k['nama_kategori']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Supplier</label>
                <select name="supplier_id" required>
                    <?php foreach ($supplierList as $s): ?>
                        <option value="<?= $s['id']; ?>" <?= ($s['id'] == $produk['supplier_id']) ? 'selected' : ''; ?>>
                            <?= e($s['nama_supplier']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Harga (Rp)</label>
                <input type="number" name="harga" min="0" step="100" value="<?= $produk['harga']; ?>" required>
            </div>

            <div class="form-group">
                <label>Stok</label>
                <input type="number" name="stok" min="0" value="<?= $produk['stok']; ?>" required>
            </div>

            <div style="display: flex; gap: 10px; margin-top: 24px;">
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                <a href="index.php" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</body>
</html>