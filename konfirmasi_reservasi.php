<?php

include_once 'config.php';
include_once 'session.php';

if (!isset($_SESSION['reservasi'])) {
    header("Location: reservasi.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $reservasi = $_SESSION['reservasi'];

    $query = "INSERT INTO reservasi (tanggal, waktu, jumlah_tamu, meja_id, status)
              VALUES (?, ?, ?, ?, 'terkonfirmasi')";

    $stmt = $conn->prepare($query);
    $stmt->bind_param(
        "ssii",
        $reservasi['tanggal'],
        $reservasi['waktu'],
        $reservasi['jumlah_tamu'],
        $reservasi['meja_id']
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
    <title>Konfirmasi Reservasi</title>
</head>

<body>
    <h2>Konfirmasi Reservasi</h2>
    <p>Tanggal: <?php echo $_SESSION['reservasi']['tanggal']; ?></p>
    <p>Waktu: <?php echo $_SESSION['reservasi']['waktu']; ?></p>
    <p>Jumlah Tamu: <?php echo $_SESSION['reservasi']['jumlah_tamu']; ?></p>

    <form method="POST">
        <button type="submit">Konfirmasi Reservasi</button>
    </form>
</body>

</html>