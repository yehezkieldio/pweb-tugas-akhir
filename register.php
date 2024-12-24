<?php

include_once 'config.php';
include_once 'session.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $nomor_telpon = $_POST['nomor_telpon'];
    $password = $_POST['password'];

    $error = "";

    if (empty($nama) || empty($email) || empty($nomor_telpon) || empty($password)) {
        $error = "Semua input harus diisi.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format email tidak valid.";
    } elseif (strlen($password) < 8) {
        $error = "Password minimal 8 karakter.";
    } elseif (!preg_match("/^[0-9]*$/", $nomor_telpon)) {
        $error = "Nomor telepon harus berupa angka.";
    } elseif (!preg_match("/^(^\+62\s?|^0)(\d{3,4}-?){2}\d{3,4}$/", $nomor_telpon)) {
        $error = "Nomor telepon tidak valid, gunakan format +62 atau 0.";
    } else {
        $password = password_hash($password, PASSWORD_DEFAULT);

        $query = "SELECT * FROM pelanggan WHERE email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = "Email sudah terdaftar.";
        } else {
            $query = "INSERT INTO pelanggan (nama, email, nomor_telpon, password)
            VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ssss", $nama, $email, $nomor_telpon, $password);
            $stmt->execute();

            $_SESSION['user'] = [
                'id' => $stmt->insert_id,
                'nama' => $nama,
                'email' => $email
            ];

            header("Location: reservasi.php");
        }
    }
}

?>


<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIMANRES - Register</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div>
        <header class="form-header">
            <h2>
                Registrasi
            </h2>
            <p>
                Sudah punya akun? <a href="login.php">Login</a>
            </p>
        </header>
        <?php if (isset($error))
            echo "<p class='error-form'>$error</p>"; ?>
        <form class="register-form" method="POST" autocomplete="off">
            <div>
                <label>Nama:</label>
                <input type="text" name="nama" required autocomplete="off">
            </div>
            <div>
                <label>Email:</label>
                <input type="email" name="email" required autocomplete="off">
            </div>
            <div>
                <label>Nomor Telepon:</label>
                <input type="text" name="nomor_telpon" required autocomplete="off">
            </div>
            <div>
                <label>Password:</label>
                <input type="password" name="password" required autocomplete="new-password">
            </div>
            <button class="button" type="submit">Buat Akun</button>
            <a class="button button-outline" href="index.php">Kembali</a>
        </form>
    </div>
</body>

</html>