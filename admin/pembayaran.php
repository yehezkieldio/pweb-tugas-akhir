<?php
include_once '../config.php';
include_once '../session.php';

checkUserSistem();
checkRoleAdmin();

$isAdminUser = isAdmin();
$message = '';

// Handle Create Operation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create'])) {
    $pesanan_id = $_POST['pesanan_id'];
    $total = $_POST['total'];
    $status = $_POST['status'];

    $createQuery = "INSERT INTO pembayaran (pesanan_id, total, status) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($createQuery);
    $stmt->bind_param("ids", $pesanan_id, $total, $status);

    if ($stmt->execute()) {
        $message = "Pembayaran baru berhasil ditambahkan";
    } else {
        $message = "Error: " . $conn->error;
    }
}

// Handle Delete Operation
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $deleteQuery = "DELETE FROM pembayaran WHERE id = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $message = "Pembayaran berhasil dihapus";
    } else {
        $message = "Error: " . $conn->error;
    }
}

// Handle Edit Operation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit'])) {
    $id = $_POST['id'];
    $pesanan_id = $_POST['pesanan_id'];
    $total = $_POST['total'];
    $status = $_POST['status'];

    $updateQuery = "UPDATE pembayaran SET pesanan_id = ?, total = ?, status = ? WHERE id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("idsi", $pesanan_id, $total, $status, $id);

    if ($stmt->execute()) {
        $message = "Pembayaran berhasil diupdate";
    } else {
        $message = "Error: " . $conn->error;
    }
}

// Get pembayaran data for editing
$editData = null;
$showCreateForm = false;

if (isset($_GET['action'])) {
    if ($_GET['action'] == 'edit' && isset($_GET['id'])) {
        $id = $_GET['id'];
        $editQuery = "SELECT * FROM pembayaran WHERE id = ?";
        $stmt = $conn->prepare($editQuery);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $editData = $stmt->get_result()->fetch_assoc();
    } elseif ($_GET['action'] == 'create') {
        $showCreateForm = true;
    }
}

// Fetch all pembayaran
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
    <title>SIMANRES - Admin Pembayaran</title>
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

        .form-container input {
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
    </style>
</head>

<body>
    <div class="layout">
        <?php include_once "../sidebar.php" ?>
        <div class="main-content">
            <h1>Pembayaran</h1>

            <?php if ($message): ?>
                <div class="message"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>

            <?php if (!$editData && !$showCreateForm): ?>
                <div class="create-button">
                    <a class="button" href="pembayaran.php?action=create">Tambah Pembayaran Baru</a>
                </div>
            <?php endif; ?>

            <?php if ($showCreateForm): ?>
                <div class="form-container">
                    <h2>Tambah Pembayaran Baru</h2>
                    <form method="POST">
                        <label for="pesanan_id">ID Pesanan:</label>
                        <input type="number" id="pesanan_id" name="pesanan_id" required>

                        <label for="total">Total:</label>
                        <input type="number" id="total" name="total" required>

                        <label for="status">Status:</label>
                        <select id="status" name="status" required>
                            <option value="pending">Pending</option>
                            <option value="lunas">Lunas</option>
                            <option value="batal">Batal</option>
                        </select>

                        <div class="button-group">
                            <button type="submit" name="create">Simpan</button>
                            <a href="pembayaran.php" class="button">Batal</a>
                        </div>
                    </form>
                </div>
            <?php endif; ?>

            <?php if ($editData): ?>
                <div class="form-container">
                    <h2>Edit Pembayaran</h2>
                    <form method="POST">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($editData['id']) ?>">

                        <label for="pesanan_id">ID Pesanan:</label>
                        <input type="number" id="pesanan_id" name="pesanan_id"
                            value="<?= htmlspecialchars($editData['pesanan_id']) ?>" required>

                        <label for="total">Total:</label>
                        <input type="number" id="total" name="total" value="<?= htmlspecialchars($editData['total']) ?>"
                            required>

                        <label for="status">Status:</label>
                        <select id="status" name="status" required>
                            <option value="pending" <?= $editData['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="lunas" <?= $editData['status'] == 'lunas' ? 'selected' : '' ?>>Lunas</option>
                            <option value="batal" <?= $editData['status'] == 'batal' ? 'selected' : '' ?>>Batal</option>
                        </select>

                        <div class="button-group">
                            <button type="submit" name="edit">Simpan Perubahan</button>
                            <a href="pembayaran.php" class="button">Batal</a>
                        </div>
                    </form>
                </div>
            <?php endif; ?>

            <div class="admin-table">
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>ID Pesanan</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pembayaran as $key => $value): ?>
                                <tr>
                                    <td><?= htmlspecialchars($value['id']) ?></td>
                                    <td><?= htmlspecialchars($value['pesanan_id']) ?></td>
                                    <td>Rp<?= number_format($value['total'], 0, ',', '.') ?></td>
                                    <td><?= htmlspecialchars($value['status']) ?></td>
                                    <td>
                                        <a class="button" href="pembayaran.php?action=edit&id=<?= $value['id'] ?>">Edit</a>
                                        <a class="button" href="pembayaran.php?action=delete&id=<?= $value['id'] ?>"
                                            onclick="return confirm('Apakah Anda yakin ingin menghapus pembayaran ini?')">Hapus</a>
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