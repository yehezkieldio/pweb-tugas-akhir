<div class="sidebar">
    <a class="title" href="index.php">SIMANRES</a>
    <?php if (isset($isKasirUser) && $isKasirUser): ?>
        <a href="pembayaran.php">Pembayaran</a>
    <?php elseif (isset($isAdminUser) && $isAdminUser): ?>
        <a href="meja.php">Meja</a>
        <a href="pembayaran.php">Pembayaran</a>
        <a href="menu.php">Menu</a>
        <a href="pelanggan.php">Pelanggan</a>
        <a href="pesanan.php">Pesanan</a>
        <a href="user.php">User</a>
        <a href="reservasi.php">Reservasi</a>
    <?php endif; ?>
    <a href="logout.php" class="back-link button">Logout</a>
</div>