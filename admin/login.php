<?php

include_once '../config.php';
include_once '../session.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // validate all input fields
    $error = "";

    if (empty($username) || empty($password)) {
        $error = "Username dan password harus diisi.";
    }

    if (empty($error)) {
        $query = "SELECT * FROM user WHERE username = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                if ($user['role'] != 'admin') {
                    $error = "Anda tidak memiliki akses.";
                    exit();
                }

                $_SESSION['admin'] = [
                    'id' => $user['id'],
                    'nama' => $user['nama'],
                    'username' => $user['username'],
                    'role' => $user['role']
                ];
                $_SESSION['role'] = $user['role'];

                header("Location: index.php");
                exit();
            } else {
                $error = "Password salah.";
            }
        } else {
            $error = "Username tidak terdaftar.";

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
    <link rel="stylesheet" href="../style.css">
</head>

<body>
    <div>
        <header class="form-header">
            <h2>Login Sistem</h2>
        </header>
        <?php if (isset($error))
            echo "<p class='error-form'>$error</p>"; ?>
        <form class="register-form" method="POST">
            <div>
                <label>Username:</label>
                <input type="text" name="username" required>
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