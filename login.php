<?php

include_once 'config.php';
include_once 'session.php';

alreadyLogin();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // validate all input fields
    $error = "";

    if (empty($email) || empty($password)) {
        $error = "Email dan password harus diisi.";
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format email tidak valid.";
    }

    if (empty($error)) {
        $query = "SELECT * FROM pelanggan WHERE email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'nama' => $user['nama'],
                    'email' => $user['email']
                ];

                header("Location: reservasi.php");
                exit();
            } else {
                $error = "Password salah.";
            }
        } else {
            $error = "Email tidak terdaftar.";

        }
    }
}

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIMANRES - Login</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div>
        <header class="form-header">
            <h2>Login</h2>
            <p>
                Belum punya akun? <a href="register.php">Register</a>
            </p>
        </header>
        <?php if (isset($error))
            echo "<p class='error-form'>$error</p>"; ?>
        <form class="register-form" method="POST">
            <div>
                <label>Email:</label>
                <input type="email" name="email" required>
            </div>
            <div>
                <label>Password:</label>
                <input type="password" name="password" required>
            </div>
            <button class="button" type="submit">Masuk</button>
            <a class="button button-outline" href="index.php">Kembali</a>
        </form>
    </div>
</body>

</html>