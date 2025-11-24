<?php
session_start();
include 'config/koneksi.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'pelanggan') {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Pelanggan</title>
</head>
<body>
    <h2>Selamat Datang, <?php echo $_SESSION['nama']; ?>!</h2>
    <a href="logout.php">Logout</a>
    <hr>

    <h3>Menu Coffee Shop</h3>
    <table border="1" cellpadding="6">
        <tr>
            <th>ID</th><th>Nama Produk</th><th>Harga</th><th>Stok</th><th>Aksi</th>
        </tr>
        <?php
        $produk = mysqli_query($conn, "SELECT * FROM produk");
        while ($p = mysqli_fetch_assoc($produk)) {
            echo "<tr>
                    <td>{$p['id_produk']}</td>
                    <td>{$p['nama_produk']}</td>
                    <td>Rp " . number_format($p['harga'], 0, ',', '.') . "</td>
                    <td>{$p['stok']}</td>
                    <td>
                        <form method='POST' action='tambah_keranjang.php'>
                            <input type='hidden' name='id_produk' value='{$p['id_produk']}'>
                            <input type='number' name='jumlah' value='1' min='1' style='width:60px'>
                            <input type='text' name='catatan' placeholder='Catatan (Opsional)'>
                            <button type='submit'>Tambah ke Keranjang</button>
                        </form>
                    </td>
                  </tr>";
        }
        ?>
    </table>

    <br>
    <a href="keranjang.php">🛒 Lihat Keranjang Saya</a>
</body>
</html>
