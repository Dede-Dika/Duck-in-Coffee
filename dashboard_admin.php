<?php
session_start();
include 'config/koneksi.php';

// Pastikan hanya admin yang bisa mengakses halaman ini
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin - Coffee Shop</title>
</head>
<body>
    <h2>Dashboard Admin Coffee Shop</h2>
    <p>Selamat datang, <strong><?php echo $_SESSION['nama']; ?></strong>!</p>
    <a href="logout.php">Logout</a>
    <hr>

    <!-- Daftar Pengguna -->
    <h3>👥 Daftar Pengguna</h3>
    <table border="1" cellpadding="5" cellspacing="0">
        <tr>
            <th>ID</th><th>Nama</th><th>Email</th><th>Role</th><th>Tanggal Dibuat</th>
        </tr>
        <?php
        $users = mysqli_query($conn, "SELECT * FROM users ORDER BY id_user ASC");
        while ($u = mysqli_fetch_assoc($users)) {
            echo "<tr>
                    <td>{$u['id_user']}</td>
                    <td>{$u['nama']}</td>
                    <td>{$u['email']}</td>
                    <td>{$u['role']}</td>
                    <td>{$u['created_at']}</td>
                  </tr>";
        }
        ?>
    </table>

    <hr>

    <!-- Daftar Produk -->
    <h3>☕ Daftar Produk Kopi</h3>
    <table border="1" cellpadding="5" cellspacing="0">
        <tr>
            <th>ID</th><th>Nama Produk</th><th>Harga</th><th>Stok</th>
        </tr>
        <?php
        $produk = mysqli_query($conn, "SELECT * FROM produk ORDER BY id_produk ASC");
        while ($p = mysqli_fetch_assoc($produk)) {
            echo "<tr>
                    <td>{$p['id_produk']}</td>
                    <td>{$p['nama_produk']}</td>
                    <td>Rp " . number_format($p['harga'], 0, ',', '.') . "</td>
                    <td>{$p['stok']}</td>
                  </tr>";
        }
        ?>
    </table>

    <hr>

    <!-- Daftar Pesanan -->
    <h3>🧾 Daftar Pesanan Pelanggan</h3>
    <table border="1" cellpadding="5" cellspacing="0">
        <tr>
            <th>ID Pesanan</th>
            <th>Nama Pelanggan</th>
            <th>Tanggal Pesanan</th>
            <th>Metode Pembayaran</th>
            <th>Status Pembayaran</th>
            <th>Total Harga</th>
            <th>Detail</th>
        </tr>
        <?php
        $pesanan = mysqli_query($conn, "
            SELECT p.*, u.nama 
            FROM pesanan p 
            JOIN users u ON p.id_user = u.id_user 
            ORDER BY p.tanggal_pesanan DESC
        ");
        while ($row = mysqli_fetch_assoc($pesanan)) {
            echo "<tr>
                    <td>{$row['id_pesanan']}</td>
                    <td>{$row['nama']}</td>
                    <td>{$row['tanggal_pesanan']}</td>
                    <td>{$row['metode_pembayaran']}</td>
                    <td>{$row['status_pembayaran']}</td>
                    <td>Rp " . number_format($row['total_harga'], 0, ',', '.') . "</td>
                    <td><a href='detail_pesanan_admin.php?id={$row['id_pesanan']}'>Lihat</a></td>
                  </tr>";
        }
        ?>
    </table>

</body>
</html>
