<?php

include_once 'config.php';
include_once 'session.php';

if (!isset($_SESSION['reservasi_id'])) {
    header("Location: reservasi.php");
    exit();
}

$query = "SELECT m.nama, m.harga, p.jumlah, (m.harga * p.jumlah) as subtotal
          FROM pesanan p
          JOIN menu m ON p.menu_id = m.id
          WHERE p.reservasi_id = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $_SESSION['reservasi_id']);
$stmt->execute();
$result = $stmt->get_result();

$total = 0;

?>

<!DOCTYPE html>
<html>

<head>
    <title>Pembayaran</title>
</head>

<body>
    <h2>Detail Pembayaran</h2>
    <table border="1">
        <tr>
            <th>Menu</th>
            <th>Harga</th>
            <th>Jumlah</th>
            <th>Subtotal</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <?php $total += $row['subtotal']; ?>
            <tr>
                <td><?php echo $row['nama']; ?></td>
                <td>Rp<?php echo number_format($row['harga'], 0, ',', '.'); ?></td>
                <td><?php echo $row['jumlah']; ?></td>
                <td>Rp<?php echo number_format($row['subtotal'], 0, ',', '.'); ?></td>
            </tr>
        <?php endwhile; ?>
        <tr>
            <td colspan="3"><strong>Total</strong></td>
            <td><strong>Rp<?php echo number_format($total, 0, ',', '.'); ?></strong></td>
        </tr>
    </table>

    <form action="proses_pembayaran.php" method="POST">
        <input type="hidden" name="total" value="<?php echo $total; ?>">
        <button type="submit">Bayar Sekarang</button>
    </form>
</body>

</html>