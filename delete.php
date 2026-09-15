<?php
require_once 'config/Database.php';
require_once 'functions.php';

$db = Database::getInstance()->getConnection();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    try {
        // Ambil info nama produk dulu untuk catatan log
        $stmtInfo = $db->prepare("SELECT nama_produk, kode_produk FROM produk WHERE id = :id");
        $stmtInfo->execute([':id' => $id]);
        $produk = $stmtInfo->fetch();

        if ($produk) {
            // Memulai DB Transaction (Bonus Feature)
            $db->beginTransaction();

            // 1. Hapus Produk
            $stmtDel = $db->prepare("DELETE FROM produk WHERE id = :id");
            $stmtDel->execute([':id' => $id]);

            // 2. Catat Aktivitas ke Log
            $stmtLog = $db->prepare("INSERT INTO log_aktivitas (aksi, keterangan) VALUES (:aksi, :ket)");
            $stmtLog->execute([
                ':aksi' => 'DELETE',
                ':ket'  => "Menghapus produk: {$produk['nama_produk']} ({$produk['kode_produk']})"
            ]);

            // Commit Transaksi jika kedua query sukses
            $db->commit();

            setFlashMessage('success', 'Produk berhasil dihapus dan aktivitas dicatat ke log!');
        } else {
            setFlashMessage('danger', 'Produk tidak ditemukan!');
        }
    } catch (Exception $e) {
        // Rollback jika terjadi kesalahan query
        $db->rollBack();
        setFlashMessage('danger', 'Gagal menghapus produk: ' . $e->getMessage());
    }
} else {
    setFlashMessage('danger', 'ID Produk tidak valid!');
}

// PRG Pattern (Redirect ke index)
header('Location: index.php');
exit;