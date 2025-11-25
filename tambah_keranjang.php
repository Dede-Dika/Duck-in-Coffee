<?php
session_start();
include 'config/koneksi.php';

// Jika user belum login
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'pelanggan') {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $id_produk = $_POST['id_produk'];
    $jumlah = $_POST['jumlah'];
    $catatan = $_POST['catatan'];

    // Ambil data produk dari database
    $result = mysqli_query($conn, "SELECT * FROM produk WHERE id_produk='$id_produk'");
    $produk = mysqli_fetch_assoc($result);

    if (!$produk) {
        die("Produk tidak ditemukan!");
    }

    // Jika keranjang belum ada, buat
    if (!isset($_SESSION['keranjang'])) {
        $_SESSION['keranjang'] = [];
    }

    // Jika produk sudah ada di keranjang → jumlah ditambah
    if (isset($_SESSION['keranjang'][$id_produk])) {
        $_SESSION['keranjang'][$id_produk]['jumlah'] += $jumlah;
    } else {
        // Masukkan produk ke keranjang
        $_SESSION['keranjang'][$id_produk] = [
            'nama' => $produk['nama_produk'],
            'harga' => $produk['harga'],
            'jumlah' => $jumlah,
            'keterangan' => $catatan
        ];
    }

    header("Location: dashboard_pelanggan.php?success=1");
    exit;
}
?>
