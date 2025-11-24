<?php
session_start();
include 'config/koneksi.php';

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

$id_user = $_SESSION['id_user'];

// Hapus item dari keranjang
if (isset($_GET['hapus'])) {
    $id_keranjang = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM keranjang WHERE id_keranjang='$id_keranjang'");
    header("Location: keranjang.php");
    exit;
}

// Proses checkout
if (isset($_POST['checkout'])) {
    $pesanan = mysqli_query($conn, "INSERT INTO pesanan (id_user, tanggal_pesanan, total_harga, metode_pembayaran, status_pembayaran)
                                    VALUES ('$id_user', NOW(), 0, 'Sudah Dibayar', 'Belum Dibayar')");
    $id_pesanan = mysqli_insert_id($conn);

    $total_harga = 0;
    $keranjang = mysqli_query($conn, "SELECT * FROM keranjang WHERE id_user='$id_user'");
    while ($k = mysqli_fetch_assoc($keranjang)) {
        $produk = mysqli_query($conn, "SELECT harga FROM produk WHERE id_produk='{$k['id_produk']}'");
        $p = mysqli_fetch_assoc($produk);
        $subtotal = $p['harga'] * $k['jumlah'];
        $total_harga += $subtotal;

        mysqli_query($conn, "INSERT INTO detail_pesanan (id_pesanan, id_produk, jumlah, subtotal)
                             VALUES ('$id_pesanan', '{$k['id_produk']}', '{$k['jumlah']}', '$subtotal')");
    }

    // Update total harga pesanan
    mysqli_query($conn, "UPDATE pesanan SET total_harga='$total_harga' WHERE id_pesanan='$id_pesanan'");

    // Kosongkan keranjang
    mysqli_query($conn, "DELETE FROM keranjang WHERE id_user='$id_user'");

    echo "<script>alert('Checkout berhasil! Total harga: Rp " . number_format($total_harga, 0, ',', '.') . "'); 
          window.location='dashboard_pelanggan.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Keranjang Saya</title>
</head>
<body>
    <h2>Keranjang Belanja Anda</h2>
    <a href="dashboard_pelanggan.php">⬅ Kembali ke Menu</a>
    <hr>

    <form method="POST" action="">
    <table border="1" cellpadding="6">
        <tr>
            <th>Nama Produk</th><th>Jumlah</th><th>Harga Satuan</th><th>Subtotal</th><th>Catatan</th><th>Aksi</th>
        </tr>
        <?php
        $keranjang = mysqli_query($conn, "SELECT k.*, p.nama_produk, p.harga FROM keranjang k JOIN produk p ON k.id_produk=p.id_produk WHERE k.id_user='$id_user'");
        $total = 0;
        while ($row = mysqli_fetch_assoc($keranjang)) {
            $subtotal = $row['harga'] * $row['jumlah'];
            $total += $subtotal;
            echo "<tr>
                    <td>{$row['nama_produk']}</td>
                    <td>{$row['jumlah']}</td>
                    <td>Rp " . number_format($row['harga'], 0, ',', '.') . "</td>
                    <td>Rp " . number_format($subtotal, 0, ',', '.') . "</td>
                    <td>{$row['catatan']}</td>
                    <td><a href='keranjang.php?hapus={$row['id_keranjang']}'>Hapus</a></td>
                  </tr>";
        }
        ?>
        <tr>
            <td colspan="3" align="right"><strong>Total:</strong></td>
            <td colspan="3"><strong>Rp <?php echo number_format($total, 0, ',', '.'); ?></strong></td>
        </tr>
    </table>
    <br>
    <button type="submit" name="checkout">Checkout</button>
    </form>
</body>
</html>
