<?php

include_once 'config.php';
include_once 'session.php';

checkLogin();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tanggal = $_POST['tanggal'];
    $waktu = $_POST['waktu'];
    $jumlah_tamu = $_POST['jumlah_tamu'];

    $error = "";

    if (!preg_match("/^[0-9]*$/", $jumlah_tamu)) {
        $error = "Jumlah tamu harus berupa angka.";
    }

    if (strtotime($tanggal) < strtotime(date("Y-m-d"))) {
        $error = "Tanggal reservasi tidak valid.";
    }

    if (strtotime($tanggal . " " . $waktu) < strtotime(date("Y-m-d H:i:s"))) {
        $error = "Waktu reservasi tidak valid.";
    }

    if ($jumlah_tamu < 1) {
        $error = "Jumlah tamu minimal 1.";
    }


    if (empty($error)) {
        try {
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

                if ($meja['kapasitas'] > $jumlah_tamu) {
                    $error = "Meja yang tersedia memiliki kapasitas lebih besar dari jumlah tamu.";
                }

                if ($meja['kapasitas'] < $jumlah_tamu) {
                    $error = "Meja yang tersedia tidak cukup untuk jumlah tamu.";
                }

                if (empty($error)) {
                    $_SESSION['reservasi'] = [
                        'tanggal' => $tanggal,
                        'waktu' => $waktu,
                        'jumlah_tamu' => $jumlah_tamu,
                        'meja_id' => $meja['id'],
                        'pelanggan_id' => $_SESSION['user']['id']
                    ];
                    header("Location: konfirmasi_reservasi.php");
                } else {
                    $error = "Maaf, tidak ada meja yang tersedia untuk jumlah tamu tersebut.";
                }
            } else {
                $error = "Maaf, tidak ada meja yang tersedia untuk jumlah tamu tersebut.";
            }
        } catch (Exception $e) {
            $error = "Terjadi kesalahan sistem. Silakan coba lagi nanti.";
            error_log($e->getMessage());
        }
    }
}

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIMANRES - Reservasi</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div>
        <header class="form-header">
            <h2>
                Mulai Reservasi
            </h2>
        </header>
        <?php if (isset($error))
            echo "<p class='error-form'>$error</p>"; ?>
        <form class="register-form" method="POST">
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
            <button class="button" type="submit">Cek Ketersediaan</button>
        </form>
    </div>
</body>

</html>