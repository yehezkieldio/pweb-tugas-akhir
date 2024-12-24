<?php

include_once 'config.php';
include_once 'session.php';

checkLogin();

if (!isset($_SESSION['reservasi'])) {
    header("Location: reservasi.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $reservasi = $_SESSION['reservasi'];
    error_log(print_r($reservasi, true));

    $query = "INSERT INTO reservasi (tanggal, waktu, jumlah_tamu, meja_id, pelanggan_id, status)
              VALUES (?, ?, ?, ?, ?, 'terkonfirmasi')";

    $stmt = $conn->prepare($query);
    $stmt->bind_param(
        "ssiis",
        $reservasi['tanggal'],
        $reservasi['waktu'],
        $reservasi['jumlah_tamu'],
        $reservasi['meja_id'],
        $reservasi['pelanggan_id']
    );

    if ($stmt->execute()) {
        $_SESSION['reservasi_id'] = $stmt->insert_id;
        header("Location: pesan_makanan.php");
    } else {
        $error = "Gagal menyimpan reservasi.";
    }
}

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIMANRES - Konfirmasi Reservasi</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div>
        <header class="form-header">
            <h2>
                Konfirmasi Reservasi
            </h2>
        </header>
        <div class="konfirmasi-konten">
            <div class="table-wrapper">
                <table>
                    <tbody>
                        <tr>
                            <th>Tanggal</th>
                            <td><?php echo $_SESSION['reservasi']['tanggal']; ?></td>
                        </tr>
                        <tr>
                            <th>Waktu</th>
                            <td><?php echo $_SESSION['reservasi']['waktu']; ?></td>
                        </tr>
                        <tr>
                            <th>Jumlah Tamu</th>
                            <td><?php echo $_SESSION['reservasi']['jumlah_tamu']; ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <form method="POST">
                <button class="button" type="submit">Konfirmasi Reservasi</button>
            </form>
        </div>


    </div>

</body>

</html>