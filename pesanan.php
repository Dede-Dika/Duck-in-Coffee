<?php
session_start();
include 'config/koneksi.php';

// Pastikan hanya pelanggan yang bisa mengakses
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'pelanggan') {
    header("Location: login.php");
    exit;
}

// Proses saat tombol pesan diklik
if (isset($_POST['pesan'])) {
    $id_user = $_SESSION['id_user'];
    $id_produk = $_POST['id_produk'];
    $jumlah = $_POST['jumlah'];

    // Ambil harga produk dari database
    $query = mysqli_query($conn, "SELECT harga FROM produk WHERE id_produk = '$id_produk'");
    $data = mysqli_fetch_assoc($query);
    $harga = $data['harga'];

    // Hitung total harga
    $total_harga = $harga * $jumlah;

    // Simpan ke tabel pesanan
    $insert_pesanan = mysqli_query($conn, "INSERT INTO pesanan (id_user, tanggal_pesanan, total_harga, metode_pembayaran, status_pembayaran)
                                            VALUES ('$id_user', NOW(), '$total_harga', 'Cash', 'Belum Bayar')");
    if ($insert_pesanan) {
        $id_pesanan = mysqli_insert_id($conn);

        // Simpan ke tabel detail_pesanan
        mysqli_query($conn, "INSERT INTO detail_pesanan (id_pesanan, id_produk, jumlah, subtotal)
                             VALUES ('$id_pesanan', '$id_produk', '$jumlah', '$total_harga')");

        echo "<script>alert('Pesanan berhasil dibuat! Total harga: Rp " . number_format($total_harga, 0, ',', '.') . "'); 
              window.location='dashboard_pelanggan.php';</script>";
    } else {
        echo "<script>alert('Gagal membuat pesanan!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Form Pesanan</title>
</head>
<body>
    <h2>Form Pemesanan Kopi</h2>

    <form method="POST" action="">
        <label>Pilih Produk:</label>
        <select name="id_produk" required>
            <option value="">-- Pilih Menu --</option>
            <?php
            $produk = mysqli_query($conn, "SELECT * FROM produk");
            while ($p = mysqli_fetch_assoc($produk)) {
                echo "<option value='{$p['id_produk']}'>{$p['nama_produk']} - Rp " . number_format($p['harga'], 0, ',', '.') . "</option>";
            }
            ?>
        </select>
        <br><br>

        <label>Jumlah:</label>
        <input type="number" name="jumlah" min="1" required>
        <br><br>

        <button type="submit" name="pesan">Pesan Sekarang</button>
    </form>
</body>
</html>
