<?php
include 'koneksi.php';
session_start();

$id_user = $_SESSION['id_user'];
$tanggal = date('Y-m-d H:i:s');
$metode = 'cash'; // bisa ubah nanti dari form
$status = 'belum dibayar';

// Ambil data keranjang user
$keranjang = mysqli_query($koneksi, "SELECT k.*, p.harga, p.nama_produk 
                                    FROM keranjang k 
                                    JOIN produk p ON k.id_produk = p.id_produk 
                                    WHERE k.id_user='$id_user'");

if (mysqli_num_rows($keranjang) == 0) {
    echo "<script>alert('Keranjang masih kosong!'); window.location='keranjang.php';</script>";
    exit;
}

// 1. Tambahkan ke tabel pesanan
mysqli_query($koneksi, "INSERT INTO pesanan (id_user, tanggal_pesanan, total_harga, status_pembayaran, metode_pembayaran) 
                        VALUES ('$id_user', '$tanggal', 0, '$status', '$metode')");
$id_pesanan = mysqli_insert_id($koneksi);

$total = 0;

// 2. Pindahkan data dari keranjang ke detail_pesanan
while ($row = mysqli_fetch_assoc($keranjang)) {
    $subtotal = $row['harga'] * $row['jumlah'];
    $total += $subtotal;
    mysqli_query($koneksi, "INSERT INTO detail_pesanan (id_pesanan, id_produk, jumlah, catatan, subtotal) 
                            VALUES ('$id_pesanan', '{$row['id_produk']}', '{$row['jumlah']}', '{$row['catatan']}', '$subtotal')");
}

// 3. Update total harga di pesanan
mysqli_query($koneksi, "UPDATE pesanan SET total_harga='$total' WHERE id_pesanan='$id_pesanan'");

// 4. Hapus isi keranjang user
mysqli_query($koneksi, "DELETE FROM keranjang WHERE id_user='$id_user'");

echo "<script>alert('Pesanan berhasil dibuat!'); window.location='riwayat_pesanan.php';</script>";
?>
