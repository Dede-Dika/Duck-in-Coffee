<?php
session_start();
include 'config/koneksi.php';

// Jika user belum login
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

$id_user = $_SESSION['id_user'];

// Ambil daftar pesanan user
$query = "
    SELECT p.id_pesanan, p.tanggal_pesanan, p.metode_pembayaran, p.status_pembayaran
    FROM pesanan p
    WHERE p.id_user = '$id_user'
    ORDER BY p.id_pesanan DESC
";

$pesanan = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Riwayat Pesanan</title>
    <style>
        body { font-family: Arial; background:#f5f5f5; padding:20px; }
        .card { background:white; padding:20px; border-radius:10px; box-shadow:0 2px 10px rgba(0,0,0,0.2); }
        table { width:100%; border-collapse:collapse; margin-top:15px; }
        th { background:#333; color:white; padding:10px; }
        td { padding:10px; background:white; border-bottom:1px solid #ccc; }
        a { text-decoration:none; color:#007bff; }
    </style>
</head>
<body>

<div class="card">
    <h2>📌 Riwayat Pesanan Anda</h2>

    <?php if (mysqli_num_rows($pesanan) == 0): ?>
        <p>Belum ada pesanan.</p>
    <?php else: ?>

    <table>
        <tr>
            <th>ID Pesanan</th>
            <th>Tanggal</th>
            <th>Metode</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>

        <?php while ($p = mysqli_fetch_assoc($pesanan)) { ?>
        <tr>
            <td><?= $p['id_pesanan'] ?></td>
            <td><?= $p['tanggal_pesanan'] ?></td>
            <td><?= $p['metode_pembayaran'] ?></td>
            <td><?= $p['status_pembayaran'] ?></td>
            <td>
                <a href="detail_pesanan.php?id=<?= $p['id_pesanan'] ?>">Lihat Detail</a>
            </td>
        </tr>
        <?php } ?>

    </table>

    <?php endif; ?>
</div>

</body>
</html>
