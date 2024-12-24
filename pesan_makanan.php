<?php

include_once 'config.php';
include_once 'session.php';

checkLogin();

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

$query = "SELECT * FROM menu ORDER BY nama LIMIT 5";
$menu_result = mysqli_query($conn, $query);

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIMANRES - Pesan Makanan</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .menu-grid {
            display: flex;
            justify-content: space-between;
            padding: 20px;
            gap: 20px;
        }

        .menu-column {
            flex: 0 0 33.33%;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .menu-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
    </style>
</head>

<body>
    <div>
        <header class="form-header">
            <h2>
                Menu Makanan
            </h2>
        </header>
        <div>
            <form class="register-form" method="POST">
                <?php while ($menu = mysqli_fetch_assoc($menu_result)): ?>
                    <div>
                        <label><?php echo $menu['nama']; ?> -
                            Rp<?php echo number_format($menu['harga'], 0, ',', '.'); ?></label>
                        <input type="number" name="menu[<?php echo $menu['id']; ?>]" value="0" min="0">
                    </div>
                <?php endwhile; ?>
                <button class="button" type="submit">Pesan</button>
            </form>
        </div>
    </div>
</body>

</html>