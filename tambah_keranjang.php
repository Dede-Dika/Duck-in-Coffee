<?php
session_start();
include 'config/koneksi.php';

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

$id_user = $_SESSION['id_user'];
$id_produk = $_POST['id_produk'];
$jumlah = $_POST['jumlah'];
$catatan = mysqli_real_escape_string($conn, $_POST['catatan']);

// ambil nama produk dari tabel produk
$getNama = mysqli_query($conn, "SELECT nama_produk FROM produk WHERE id_produk='$id_produk'");
$namaData = mysqli_fetch_assoc($getNama);
$nama_produk = $namaData['nama_produk'];

// cek apakah produk sudah ada di keranjang
$cek = mysqli_query($conn, "SELECT * FROM keranjang WHERE id_user='$id_user' AND id_produk='$id_produk'");
if (mysqli_num_rows($cek) > 0) {
    $row = mysqli_fetch_assoc($cek);
    $jumlah_baru = $row['jumlah'] + $jumlah;

    if (!empty($catatan)) {
        mysqli_query($conn, "UPDATE keranjang 
                             SET jumlah='$jumlah_baru', catatan='$catatan'
                             WHERE id_user='$id_user' AND id_produk='$id_produk'");
    } else {
        mysqli_query($conn, "UPDATE keranjang 
                             SET jumlah='$jumlah_baru'
                             WHERE id_user='$id_user' AND id_produk='$id_produk'");
    }

} else {
    mysqli_query($conn, "INSERT INTO keranjang (id_user, id_produk, nama_produk, jumlah, catatan)
                         VALUES ('$id_user', '$id_produk', '$nama_produk', '$jumlah', '$catatan')");
}

header("Location: dashboard_pelanggan.php");
exit;
?>
