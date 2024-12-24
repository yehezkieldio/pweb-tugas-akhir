<?php

include_once 'config.php';
include_once 'session.php';

checkLogin();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tanggal = $_POST['tanggal'];
    $waktu = $_POST['waktu'];
    $jumlah_tamu = $_POST['jumlah_tamu'];

    $query = "SELECT id, nomor_meja, kapasitas FROM meja
              WHERE kapasitas >= ?
              AND id NOT IN (
                  SELECT meja_id FROM reservasi
                  WHERE tanggal = ?
                  AND waktu = ?
                  AND status != 'selesai'
              )
              ORDER BY kapasitas ASC LIMIT 1";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("iss", $jumlah_tamu, $tanggal, $waktu);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $meja = $result->fetch_assoc();
        $_SESSION['reservasi'] = [
            'tanggal' => $tanggal,
            'waktu' => $waktu,
            'jumlah_tamu' => $jumlah_tamu,
            'meja_id' => $meja['id']
        ];
        header("Location: konfirmasi_reservasi.php");
    } else {
        $error = "Maaf, tidak ada meja yang tersedia untuk jumlah tamu tersebut.";
    }
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>Reservasi Meja</title>
</head>

<body>
    <h2>Form Reservasi</h2>
    <?php if (isset($error))
        echo "<p style='color: red'>$error</p>"; ?>
    <form method="POST">
        <div>
            <label>Tanggal:</label>
            <input type="date" name="tanggal" required>
        </div>
        <div>
            <label>Waktu:</label>
            <input type="time" name="waktu" required>
        </div>
        <div>
            <label>Jumlah Tamu:</label>
            <input type="number" name="jumlah_tamu" min="1" required>
        </div>
        <button type="submit">Cek Ketersediaan</button>
    </form>
</body>

</html>