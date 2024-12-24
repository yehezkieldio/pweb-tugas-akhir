<?php
include_once '../config.php';
include_once '../session.php';

checkUserSistem();
checkRoleAdmin();

$isAdminUser = isAdmin();
$message = '';

// Handle Create Operation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create'])) {
    $nomor_meja = $_POST['nomor_meja'];
    $kapasitas = $_POST['kapasitas'];

    $createQuery = "INSERT INTO meja (nomor_meja, kapasitas) VALUES (?, ?)";
    $stmt = $conn->prepare($createQuery);
    $stmt->bind_param("si", $nomor_meja, $kapasitas);

    if ($stmt->execute()) {
        $message = "Meja baru berhasil ditambahkan";
    } else {
        $message = "Error: " . $conn->error;
    }
}

// Handle Delete Operation
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $deleteQuery = "DELETE FROM meja WHERE id = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $message = "Meja berhasil dihapus";
    } else {
        $message = "Error: " . $conn->error;
    }
}

// Handle Edit Operation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit'])) {
    $id = $_POST['id'];
    $nomor_meja = $_POST['nomor_meja'];
    $kapasitas = $_POST['kapasitas'];

    $updateQuery = "UPDATE meja SET nomor_meja = ?, kapasitas = ? WHERE id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("sii", $nomor_meja, $kapasitas, $id);

    if ($stmt->execute()) {
        $message = "Meja berhasil diupdate";
    } else {
        $message = "Error: " . $conn->error;
    }
}

// Get meja data for editing
$editData = null;
$showCreateForm = false;

if (isset($_GET['action'])) {
    if ($_GET['action'] == 'edit' && isset($_GET['id'])) {
        $id = $_GET['id'];
        $editQuery = "SELECT * FROM meja WHERE id = ?";
        $stmt = $conn->prepare($editQuery);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $editData = $stmt->get_result()->fetch_assoc();
    } elseif ($_GET['action'] == 'create') {
        $showCreateForm = true;
    }
}

// Fetch all meja
$query = "SELECT * FROM meja";
$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();
$meja = [];
while ($row = $result->fetch_assoc()) {
    $meja[] = $row;
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIMANRES - Admin Meja</title>
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
            <h1>Meja</h1>

            <?php if ($message): ?>
                <div class="message"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>

            <?php if (!$editData && !$showCreateForm): ?>
                <div class="create-button">
                    <a class="button" href="meja.php?action=create">Tambah Meja Baru</a>
                </div>
            <?php endif; ?>

            <?php if ($showCreateForm): ?>
                <div class="form-container">
                    <h2>Tambah Meja Baru</h2>
                    <form method="POST">
                        <label for="nomor_meja">Nomor Meja:</label>
                        <input type="text" id="nomor_meja" name="nomor_meja" required>

                        <label for="kapasitas">Kapasitas:</label>
                        <input type="number" id="kapasitas" name="kapasitas" required>

                        <div class="button-group">
                            <button type="submit" name="create">Simpan</button>
                            <a href="meja.php" class="button">Batal</a>
                        </div>
                    </form>
                </div>
            <?php endif; ?>

            <?php if ($editData): ?>
                <div class="form-container">
                    <h2>Edit Meja</h2>
                    <form method="POST">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($editData['id']) ?>">

                        <label for="nomor_meja">Nomor Meja:</label>
                        <input type="text" id="nomor_meja" name="nomor_meja"
                            value="<?= htmlspecialchars($editData['nomor_meja']) ?>" required>

                        <label for="kapasitas">Kapasitas:</label>
                        <input type="number" id="kapasitas" name="kapasitas"
                            value="<?= htmlspecialchars($editData['kapasitas']) ?>" required>

                        <div class="button-group">
                            <button type="submit" name="edit">Simpan Perubahan</button>
                            <a href="meja.php" class="button">Batal</a>
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
                                <th>Nomor Meja</th>
                                <th>Kapasitas</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($meja as $key => $value): ?>
                                <tr>
                                    <td><?= htmlspecialchars($value['id']) ?></td>
                                    <td><?= htmlspecialchars($value['nomor_meja']) ?></td>
                                    <td><?= htmlspecialchars($value['kapasitas']) ?></td>
                                    <td>
                                        <a class="button" href="meja.php?action=edit&id=<?= $value['id'] ?>">Edit</a>
                                        <a class="button" href="meja.php?action=delete&id=<?= $value['id'] ?>"
                                            onclick="return confirm('Apakah Anda yakin ingin menghapus meja ini?')">Hapus</a>
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