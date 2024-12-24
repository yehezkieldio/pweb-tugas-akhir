<?php
include_once 'config.php';
include_once 'session.php';

$logged_in = checkIfAlreadyLogin();


checkLogin();

$user_id = $_SESSION['user']['id'];

$query = "SELECT * FROM reservasi WHERE pelanggan_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();

$reservasi = [];

while ($row = $result->fetch_assoc()) {
    $reservasi[] = $row;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIMANRES - List Reservasi</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div>
        <?php include_once 'navbar.php'; ?>
        <div class="list">
            <header class="center-header">
                <h2>List Reservasi</h2>
            </header>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal Reservasi</th>
                            <th>Jam Reservasi</th>
                            <th>Jumlah Orang</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reservasi as $key => $value): ?>
                            <tr>
                                <td><?= $key + 1 ?></td>
                                <td><?= date('l, d F Y', strtotime($value['tanggal'])) ?></td>
                                <td><?= $value['waktu'] ?></td>
                                <td><?= $value['jumlah_tamu'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

</html>