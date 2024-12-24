<?php

include_once 'config.php';
include_once 'session.php';

if (!isset($_SESSION['reservasi_id'])) {
    header("Location: reservasi.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    foreach ($_POST['menu'] as $menu_id => $jumlah) {
        if ($jumlah > 0) {
            $query = "INSERT INTO pesanan (reservasi_id, menu_id, jumlah, status)
                      VALUES (?, ?, ?, 'menunggu')";

            $stmt = $conn->prepare($query);
            $stmt->bind_param(
                "iii",
                $_SESSION['reservasi_id'],
                $menu_id,
                $jumlah
            );
            $stmt->execute();
        }
    }
    header("Location: bayar.php");
}

// Ambil daftar menu
$query = "SELECT * FROM menu ORDER BY nama";
$menu_result = mysqli_query($conn, $query);

?>

<!DOCTYPE html>
<html>

<head>
    <title>Pesan Makanan</title>
</head>

<body>
    <h2>Menu Makanan</h2>
    <form method="POST">
        <?php while ($menu = mysqli_fetch_assoc($menu_result)): ?>
            <div>
                <label><?php echo $menu['nama']; ?> - Rp<?php echo number_format($menu['harga'], 0, ',', '.'); ?></label>
                <input type="number" name="menu[<?php echo $menu['id']; ?>]" value="0" min="0">
            </div>
        <?php endwhile; ?>
        <button type="submit">Pesan</button>
    </form>
</body>

</html>