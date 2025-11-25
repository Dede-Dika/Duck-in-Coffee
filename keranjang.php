<?php
session_start();
include 'config/koneksi.php'; 

// Jika user belum login
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

// Ambil keranjang
$keranjang = isset($_SESSION['keranjang']) ? $_SESSION['keranjang'] : [];

// Hapus item keranjang
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    unset($_SESSION['keranjang'][$id]);
    header("Location: keranjang.php");
    exit;
}

// ================================
//  PROSES CHECKOUT
// ================================
if (isset($_POST['checkout'])) {

    $id_user = $_SESSION['id_user'];
    $tanggal = date("Y-m-d H:i:s");
    $metode = $_POST['metode_pembayaran'];
    $catatan = $_POST['catatan'];
    $status = "Belum Dibayar";

    // Validasi metode pembayaran sesuai ENUM
    $metode_valid = ["COD", "Transfer", "ShopeePay", "OVO", "GoPay", "QRIS", "Dana", "Kartu Kredit", "Debit"];

    if (!in_array($metode, $metode_valid)) {
        die("ERROR: Metode pembayaran tidak sesuai ENUM database!");
    }

    // ================================
    //  INSERT KE TABEL PESANAN (Tanpa total_harga)
    // ================================
    $query_pesanan = "
        INSERT INTO pesanan (id_user, tanggal_pesanan, metode_pembayaran, status_pembayaran)
        VALUES ('$id_user', '$tanggal', '$metode', '$status')
    ";

    if (!mysqli_query($conn, $query_pesanan)) {
        die("Query Error PESANAN: " . mysqli_error($conn));
    }

    // Ambil ID pesanan terbaru
    $id_pesanan = mysqli_insert_id($conn);

    // ================================
    //  INSERT DETAIL PESANAN
    // ================================
    foreach ($keranjang as $id_produk => $item) {
        $jumlah = $item['jumlah'];
        $harga = $item['harga'];
        $subtotal = $harga * $jumlah;
        $keterangan = $item['keterangan'];

        $query_detail = "
            INSERT INTO detail_pesanan (id_pesanan, id_produk, jumlah, subtotal, keterangan)
            VALUES ('$id_pesanan', '$id_produk', '$jumlah', '$subtotal', '$keterangan')
        ";

        mysqli_query($conn, $query_detail);
    }

    // Kosongkan keranjang
    unset($_SESSION['keranjang']);

    header("Location: riwayat_pesanan.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Keranjang</title>
</head>
<body>

<h2>Keranjang Belanja</h2>

<?php if (empty($keranjang)) : ?>
    <p>Keranjang kosong.</p>
<?php else : ?>

<table border="1" cellpadding="7">
    <tr>
        <th>Nama Produk</th>
        <th>Harga</th>
        <th>Jumlah</th>
        <th>Keterangan</th>
        <th>Total</th>
        <th>Aksi</th>
    </tr>

    <?php
    $grand_total = 0;
    foreach ($keranjang as $id => $item) :
        $total = $item['harga'] * $item['jumlah'];
        $grand_total += $total;
    ?>
    <tr>
        <td><?= $item['nama'] ?></td>
        <td><?= $item['harga'] ?></td>
        <td><?= $item['jumlah'] ?></td>
        <td><?= $item['keterangan'] ?></td>
        <td><?= $total ?></td>
        <td><a href="keranjang.php?hapus=<?= $id ?>">Hapus</a></td>
    </tr>
    <?php endforeach; ?>

</table>

<h3>Total Pembayaran: Rp <?= number_format($grand_total, 0, ',', '.') ?></h3>

<br>

<form method="POST">
    <label>Catatan pesanan:</label><br>
    <textarea name="catatan"></textarea><br><br>

    <label>Metode Pembayaran:</label><br>
    <select name="metode_pembayaran" required>
        <option value="COD">COD</option>
        <option value="Transfer">Transfer</option>
        <option value="ShopeePay">ShopeePay</option>
        <option value="GoPay">GoPay</option>
        <option value="OVO">OVO</option>
        <option value="QRIS">QRIS</option>
        <option value="Dana">Dana</option>
        <option value="Kartu Kredit">Kartu Kredit</option>
        <option value="Debit">Debit</option>
    </select>
    <br><br>

    <button type="submit" name="checkout">Checkout</button>
</form>

<?php endif; ?>

</body>
</html>
