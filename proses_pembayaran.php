<?php

include_once 'session.php';

if (!isset($_SESSION['reservasi_id']) || !isset($_POST['total'])) {
    header("Location: test.php");
    exit();
}

include_once 'config.php';

try {
    mysqli_begin_transaction($conn);

    // 1. Ambil semua pesanan untuk reservasi ini
    $query = "SELECT id FROM pesanan WHERE reservasi_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $_SESSION['reservasi_id']);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        // 2. Buat record pembayaran untuk setiap pesanan
        $pesanan_id = $row['id'];
        $total = $_POST['total'];

        $query_pembayaran = "INSERT INTO pembayaran (pesanan_id, total, status) VALUES (?, ?, 'lunas')";
        $stmt_pembayaran = $conn->prepare($query_pembayaran);
        $stmt_pembayaran->bind_param("id", $pesanan_id, $total);
        $stmt_pembayaran->execute();

        // 3. Update status pesanan menjadi 'selesai'
        $query_update_pesanan = "UPDATE pesanan SET status = 'selesai' WHERE id = ?";
        $stmt_update_pesanan = $conn->prepare($query_update_pesanan);
        $stmt_update_pesanan->bind_param("i", $pesanan_id);
        $stmt_update_pesanan->execute();
    }

    // 4. Update status reservasi menjadi 'selesai'
    $query_update_reservasi = "UPDATE reservasi SET status = 'selesai' WHERE id = ?";
    $stmt_update_reservasi = $conn->prepare($query_update_reservasi);
    $stmt_update_reservasi->bind_param("i", $_SESSION['reservasi_id']);
    $stmt_update_reservasi->execute();

    // Commit transaksi
    mysqli_commit($conn);

    // 5. Bersihkan session
    unset($_SESSION['reservasi_id']);
    unset($_SESSION['reservasi']);

    // 6. Redirect ke halaman sukses
    $_SESSION['success_message'] = "Pembayaran berhasil dilakukan!";
    header("Location: pembayaran_sukses.php");
    exit();

} catch (Exception $e) {
    mysqli_rollback($conn);

    error_log($e->getMessage());

    $_SESSION['error_message'] = "Terjadi kesalahan dalam proses pembayaran: " . $e->getMessage();
    header("Location: bayar.php");
    exit();
}
?>