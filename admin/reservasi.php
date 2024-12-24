<?php
include_once '../config.php';
include_once '../session.php';

checkUserSistem();
checkRoleAdmin();

$isAdminUser = isAdmin();
$message = '';

// Handle Create Operation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create'])) {
    $tanggal = $_POST['tanggal'];
    $time = $_POST['time'];
    $jumlah_tamu = $_POST['jumlah_tamu'];
    $meja_id = $_POST['meja_id'];
    $pelanggan_id = $_POST['pelanggan_id'];
    $status = $_POST['status'];

    $createQuery = "INSERT INTO reservasi (tanggal, time, jumlah_tamu, meja_id, pelanggan_id, status) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($createQuery);
    $stmt->bind_param("ssiiss", $tanggal, $time, $jumlah_tamu, $meja_id, $pelanggan_id, $status);

    if ($stmt->execute()) {
        $message = "Reservasi baru berhasil ditambahkan";
    } else {
        $message = "Error: " . $conn->error;
    }
}

// Handle Delete Operation
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $deleteQuery = "DELETE FROM reservasi WHERE id = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $message = "Reservasi berhasil dihapus";
    } else {
        $message = "Error: " . $conn->error;
    }
}

// Handle Edit Operation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit'])) {
    $id = $_POST['id'];
    $tanggal = $_POST['tanggal'];
    $time = $_POST['time'];
    $jumlah_tamu = $_POST['jumlah_tamu'];
    $meja_id = $_POST['meja_id'];
    $pelanggan_id = $_POST['pelanggan_id'];
    $status = $_POST['status'];

    $updateQuery = "UPDATE reservasi SET tanggal = ?, time = ?, jumlah_tamu = ?, meja_id = ?, pelanggan_id = ?, status = ? WHERE id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("ssiissi", $tanggal, $time, $jumlah_tamu, $meja_id, $pelanggan_id, $status, $id);

    if ($stmt->execute()) {
        $message = "Reservasi berhasil diupdate";
    } else {
        $message = "Error: " . $conn->error;
    }
}

// Get data for editing
$editData = null;
$showCreateForm = false;

if (isset($_GET['action'])) {
    if ($_GET['action'] == 'edit' && isset($_GET['id'])) {
        $id = $_GET['id'];
        $editQuery = "SELECT * FROM reservasi WHERE id = ?";
        $stmt = $conn->prepare($editQuery);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $editData = $stmt->get_result()->fetch_assoc();
    } elseif ($_GET['action'] == 'create') {
        $showCreateForm = true;
    }
}

// Fetch all meja for dropdown
$queryMeja = "SELECT * FROM meja";
$stmtMeja = $conn->prepare($queryMeja);
$stmtMeja->execute();
$resultMeja = $stmtMeja->get_result();
$mejaList = [];
while ($row = $resultMeja->fetch_assoc()) {
    $mejaList[] = $row;
}

// Fetch all pelanggan for dropdown
$queryPelanggan = "SELECT * FROM pelanggan";
$stmtPelanggan = $conn->prepare($queryPelanggan);
$stmtPelanggan->execute();
$resultPelanggan = $stmtPelanggan->get_result();
$pelangganList = [];
while ($row = $resultPelanggan->fetch_assoc()) {
    $pelangganList[] = $row;
}

$query = "SELECT r.*, m.nomor_meja, p.nama as nama_pelanggan
          FROM reservasi r
          LEFT JOIN meja m ON r.meja_id = m.id
          LEFT JOIN pelanggan p ON r.pelanggan_id = p.id
          ORDER BY r.tanggal DESC, r.waktu DESC";
$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();
$reservasi = [];
while ($row = $result->fetch_assoc()) {
    $reservasi[] = $row;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIMANRES - Admin Reservasi</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        .message {
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
            background-color: #d4edda;
            color: #155724;
        }
        .form-container {
            max-width: 500px;
            margin: 20px 0;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .form-container label {
            display: block;
            margin-bottom: 5px;
        }
        .form-container input, .form-container select {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .form-container button {
            padding: 8px 15px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-right: 10px;
        }
        .create-button {
            margin-bottom: 20px;
        }
        .button-group {
            margin-top: 10px;
        }
        .status-menunggu {
            color: #856404;
            background-color: #fff3cd;
            padding: 3px 8px;
            border-radius: 4px;
        }
        .status-terkonfirmasi {
            color: #155724;
            background-color: #d4edda;
            padding: 3px 8px;
            border-radius: 4px;
        }
        .status-selesai {
            color: #1b1e21;
            background-color: #d6d8d9;
            padding: 3px 8px;
            border-radius: 4px;
        }
    </style>
</head>

<body>
    <div class="layout">
        <?php include_once "../sidebar.php" ?>
        <div class="main-content">
            <h1>Reservasi</h1>

            <?php if ($message): ?>
                <div class="message"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>

            <?php if (!$editData && !$showCreateForm): ?>
                <div class="create-button">
                    <a class="button" href="reservasi.php?action=create">Tambah Reservasi Baru</a>
                </div>
            <?php endif; ?>

            <?php if ($showCreateForm || $editData): ?>
                <div class="form-container">
                    <h2><?= $editData ? 'Edit Reservasi' : 'Tambah Reservasi Baru' ?></h2>
                    <form method="POST">
                        <?php if ($editData): ?>
                            <input type="hidden" name="id" value="<?= htmlspecialchars($editData['id']) ?>">
                        <?php endif; ?>

                        <label for="tanggal">Tanggal:</label>
                        <input type="date" id="tanggal" name="tanggal"
                               value="<?= $editData ? htmlspecialchars($editData['tanggal']) : '' ?>" required>

                        <label for="time">Waktu:</label>
                        <input type="time" id="time" name="time"
                               value="<?= $editData ? htmlspecialchars($editData['waktu']) : '' ?>" required>

                        <label for="jumlah_tamu">Jumlah Tamu:</label>
                        <input type="number" id="jumlah_tamu" name="jumlah_tamu"
                               value="<?= $editData ? htmlspecialchars($editData['jumlah_tamu']) : '' ?>" required>

                        <label for="meja_id">Meja:</label>
                        <select id="meja_id" name="meja_id" required>
                            <option value="">Pilih Meja</option>
                            <?php foreach ($mejaList as $meja): ?>
                                <option value="<?= $meja['id'] ?>"
                                    <?= ($editData && $editData['meja_id'] == $meja['id']) ? 'selected' : '' ?>>
                                    Meja <?= htmlspecialchars($meja['nomor_meja']) ?> (Kapasitas: <?= $meja['kapasitas'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <label for="pelanggan_id">Pelanggan:</label>
                        <select id="pelanggan_id" name="pelanggan_id" required>
                            <option value="">Pilih Pelanggan</option>
                            <?php foreach ($pelangganList as $pelanggan): ?>
                                <option value="<?= $pelanggan['id'] ?>"
                                    <?= ($editData && $editData['pelanggan_id'] == $pelanggan['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($pelanggan['nama']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <label for="status">Status:</label>
                        <select id="status" name="status" required>
                            <option value="menunggu" <?= ($editData && $editData['status'] == 'menunggu') ? 'selected' : '' ?>>
                                Menunggu
                            </option>
                            <option value="terkonfirmasi" <?= ($editData && $editData['status'] == 'terkonfirmasi') ? 'selected' : '' ?>>
                                Terkonfirmasi
                            </option>
                            <option value="selesai" <?= ($editData && $editData['status'] == 'selesai') ? 'selected' : '' ?>>
                                Selesai
                            </option>
                        </select>

                        <div class="button-group">
                            <button type="submit" name="<?= $editData ? 'edit' : 'create' ?>">
                                <?= $editData ? 'Simpan Perubahan' : 'Simpan' ?>
                            </button>
                            <a href="reservasi.php" class="button">Batal</a>
                        </div>
                    </form>
                </div>
            <?php endif; ?>

            <div class="admin-table">
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tanggal</th>
                                <th>Waktu</th>
                                <th>Jumlah Tamu</th>
                                <th>Meja</th>
                                <th>Pelanggan</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reservasi as $value): ?>
                                <tr>
                                    <td><?= htmlspecialchars($value['id']) ?></td>
                                    <td><?= htmlspecialchars(date('d/m/Y', strtotime($value['tanggal']))) ?></td>
                                    <td><?= htmlspecialchars(date('H:i', strtotime($value['waktu']))) ?></td>
                                    <td><?= htmlspecialchars($value['jumlah_tamu']) ?></td>
                                    <td><?= htmlspecialchars($value['nomor_meja']) ?></td>
                                    <td><?= htmlspecialchars($value['nama_pelanggan']) ?></td>
                                    <td>
                                        <span class="status-<?= $value['status'] ?>">
                                            <?= ucfirst(htmlspecialchars($value['status'])) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a class="button" href="reservasi.php?action=edit&id=<?= $value['id'] ?>">Edit</a>
                                        <a class="button" href="reservasi.php?action=delete&id=<?= $value['id'] ?>"
                                           onclick="return confirm('Apakah Anda yakin ingin menghapus reservasi ini?')">Hapus</a>
                                    </td>
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