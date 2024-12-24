<?php

include_once '../config.php';
include_once '../session.php';

checkUserSistem();
checkRoleKasir();

$isKasirUser = isKasir();

$query = "SELECT * FROM pembayaran";
$stmt = $conn->prepare($query);
$stmt->execute();

$result = $stmt->get_result();

$pembayaran = [];

while ($row = $result->fetch_assoc()) {
    $pembayaran[] = $row;
}

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIMANRES - Kasir</title>
    <link rel="stylesheet" href="../style.css">
</head>

<body>
    <div class="layout">
        <?php include_once "../sidebar.php" ?>
        <div class="main-content">
            <h1>Pembayaran</h1>
            <div class="kasir-pembayaran">
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Pesanan ID</th>
                                <th>Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pembayaran as $key => $value): ?>
                                <tr>
                                    <td><?= $value['id'] ?></td>
                                    <td><?= $value['pesanan_id'] ?></td>
                                    <td><?= $value['total'] ?></td>
                                    <td><?= $value['status'] ?></td>

                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</body>

</html>