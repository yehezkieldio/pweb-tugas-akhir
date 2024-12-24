<div class="navbar">
    <div class="brand">
        <a href="index.php">SIMANRES</a>
    </div>
    <div class="menu">
        <?php if ($logged_in): ?>
            <a href="reservasi.php">Buat Reservasi</a>
            <a href="list_reservasi.php">List Reservasi</a>
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="login.php">Login</a>
            <a href="register.php">Register</a>
        <?php endif; ?>
    </div>
</div>