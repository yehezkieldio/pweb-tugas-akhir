<?php

include_once 'config.php';
include_once 'session.php';

$logged_in = checkIfAlreadyLogin();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIMANRES</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div>
        <?php include_once 'navbar.php'; ?>
        <header class="index-header">
            <img src="Header.jpg" alt="Header">
            <h1>Selamat Datang di SIMANRES</h1>
            <p>Sistem Informasi Manajemen Reservasi</p>
            <div>
                <?php if ($logged_in): ?>
                    <a class="button" href="reservasi.php">Buat Reservasi</a>
                    <a class="button" href="logout.php">Logout</a>
                <?php else: ?>
                    <a class="button" href="login.php">Login</a>
                    <a class="button" href="register.php">Register</a>
                <?php endif; ?>
            </div>
        </header>

    </div>
</body>

</html>