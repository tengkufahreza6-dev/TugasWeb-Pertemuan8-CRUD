<?php
require_once 'config/Database.php';
require_once 'functions.php';

// Inisialisasi Koneksi Database via Singleton
$db = Database::getInstance()->getConnection();

// 1. Logika Pagination & Pencarian
$limit = 5; 
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page > 1) ? ($page * $limit) - $limit : 0;

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$searchParam = "%{$search}%";

// 2. Query Total Data untuk Pagination (Prepared Statement)
$countSql = "SELECT COUNT(*) FROM produk p 
             JOIN kategori k ON p.kategori_id = k.id 
             JOIN supplier s ON p.supplier_id = s.id 
             WHERE p.nama_produk LIKE :search1 OR p.kode_produk LIKE :search2";
$countStmt = $db->prepare($countSql);
$countStmt->bindValue(':search1', $searchParam, PDO::PARAM_STR);
$countStmt->bindValue(':search2', $searchParam, PDO::PARAM_STR);
$countStmt->execute();
$totalData = $countStmt->fetchColumn();
$totalPages = ceil($totalData / $limit);

// 3. Query Utama dengan JOIN 2 Tabel & Limit Pagination (Prepared Statement)
$sql = "SELECT p.*, k.nama_kategori, s.nama_supplier 
        FROM produk p
        JOIN kategori k ON p.kategori_id = k.id
        JOIN supplier s ON p.supplier_id = s.id
        WHERE p.nama_produk LIKE :search1 OR p.kode_produk LIKE :search2
        ORDER BY p.id DESC 
        LIMIT :start, :limit";

$stmt = $db->prepare($sql);
$stmt->bindValue(':search1', $searchParam, PDO::PARAM_STR);
$stmt->bindValue(':search2', $searchParam, PDO::PARAM_STR);
$stmt->bindValue(':start', (int)$start, PDO::PARAM_INT);
$stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
$stmt->execute();
$produkList = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>📦</text></svg>">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Inventaris Barang — TR 8</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h2>📦 Data Inventaris Barang</h2>

        <!-- Flash Message Output -->
        <?= displayFlashMessage(); ?>

        <div class="top-bar">
            <a href="create.php" class="btn btn-primary">+ Tambah Produk Baru</a>
            
            <!-- Form Pencarian -->
            <form action="index.php" method="GET" class="search-form">
                <input type="text" name="search" placeholder="Cari nama / kode produk..." value="<?= e($search); ?>">
                <button type="submit" class="btn btn-secondary">Cari</button>
                <?php if (!empty($search)): ?>
                    <a href="index.php" class="btn btn-reset">Reset</a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Tabel Data Produk dengan JOIN -->
        <table class="data-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode</th>
                    <th>Nama Produk</th>
                    <th>Kategori</th>
                    <th>Supplier</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th>Dibuat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($produkList) > 0): ?>
                    <?php $no = $start + 1; foreach ($produkList as $row): ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><strong><?= e($row['kode_produk']); ?></strong></td>
                            <td><?= e($row['nama_produk']); ?></td>
                            <td><span class="badge badge-cat"><?= e($row['nama_kategori']); ?></span></td>
                            <td><span class="badge badge-sup"><?= e($row['nama_supplier']); ?></span></td>
                            <td>Rp <?= number_format($row['harga'], 0, ',', '.'); ?></td>
                            
                            <!-- Indikator Stok Kritis -->
                            <td>
                                <?php if ($row['stok'] <= 5): ?>
                                    <span style="color: #ef4444; font-weight: 700;">⚠️ <?= e($row['stok']); ?></span>
                                <?php else: ?>
                                    <?= e($row['stok']); ?>
                                <?php endif; ?>
                            </td>

                            <!-- Format Tanggal Pembuatan -->
                            <td style="color: #94a3b8; font-size: 0.85rem;">
                                <?= date('d/m/Y H:i', strtotime($row['created_at'])); ?>
                            </td>

                            <td class="action-cell">
                                <a href="edit.php?id=<?= $row['id']; ?>" class="btn-sm btn-edit">Edit</a>
                                <a href="delete.php?id=<?= $row['id']; ?>" class="btn-sm btn-delete" onclick="return confirm('Yakin ingin menghapus produk ini?')">Hapus</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" style="text-align: center; color: #94a3b8;">Data produk tidak ditemukan.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Navigasi Pagination -->
        <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="index.php?page=<?= $i; ?>&search=<?= urlencode($search); ?>" class="<?= ($i == $page) ? 'active' : ''; ?>">
                        <?= $i; ?>
                    </a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>