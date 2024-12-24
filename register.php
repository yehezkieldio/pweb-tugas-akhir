<?php

include_once 'config.php';
include_once 'session.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $nomor_telpon = $_POST['nomor_telpon'];
    $password = $_POST['password'];

    // validate all input fields
    $error = "";

    if (empty($nama) || empty($email) || empty($nomor_telpon) || empty($password)) {
        $error = "Semua input harus diisi.";
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format email tidak valid.";
    } else if (strlen($password) < 8) {
        $error = "Password minimal 8 karakter.";
    }

    $password = password_hash($password, PASSWORD_DEFAULT);

    $query = "SELECT * FROM pelanggan WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $error = "Email sudah terdaftar.";
    }

    if (empty($error)) {
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

?>


<!DOCTYPE html>
<html>

<head>
    <title>Reservasi Meja</title>
</head>

<body>
    <h2>Form Reservasi</h2>
    <?php if (isset($error))
        echo "<p style='color: red'>$error</p>"; ?>
    <form method="POST">
        <div>
            <label>Nama:</label>
            <input type="text" name="nama" required>
        </div>
        <div>
            <label>Email:</label>
            <input type="email" name="email" required>
        </div>
        <div>
            <label>Nomor Telepon:</label>
            <input type="text" name="nomor_telpon" required>
        </div>
        <div>
            <label>Password:</label>
            <input type="password" name="password" required>
        </div>
        <button type="submit">Register</button>
    </form>
</body>

</html>