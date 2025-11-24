<?php
include 'config/koneksi.php';

if (isset($_POST['signup'])) {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $no_hp = $_POST['no_hp'];
    $alamat = $_POST['alamat'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = "pelanggan"; // default role pelanggan

    $cek = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
    if (mysqli_num_rows($cek) > 0) {
        echo "Email sudah digunakan!";
    } else {
        $query = "INSERT INTO users (nama, email, password, no_hp, alamat, role, created_at)
                  VALUES ('$nama', '$email', '$password', '$no_hp', '$alamat', '$role', NOW())";
        if (mysqli_query($conn, $query)) {
            echo "Registrasi berhasil! Silakan <a href='login.php'>login</a>.";
        } else {
            echo "Gagal mendaftar: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar - Coffee Shop</title>
</head>
<body>
    <h2>Form Pendaftaran</h2>
    <form method="POST">
        <label>Nama:</label><br>
        <input type="text" name="nama" required><br><br>

        <label>Email:</label><br>
        <input type="email" name="email" required><br><br>

        <label>No HP:</label><br>
        <input type="text" name="no_hp" required><br><br>

        <label>Alamat:</label><br>
        <textarea name="alamat" required></textarea><br><br>

        <label>Password:</label><br>
        <input type="password" name="password" required><br><br>

        <button type="submit" name="signup">Daftar</button>
    </form>

    <p>Sudah punya akun? <a href="login.php">Login di sini</a></p>
</body>
</html>
