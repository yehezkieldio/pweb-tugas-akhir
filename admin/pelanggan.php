<?php
include_once '../config.php';
include_once '../session.php';

checkUserSistem();
checkRoleAdmin();

$isAdminUser = isAdmin();
$message = '';

// Handle Create Operation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create'])) {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $nomor_telpon = $_POST['nomor_telpon'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password

    $createQuery = "INSERT INTO pelanggan (nama, email, nomor_telpon, password) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($createQuery);
    $stmt->bind_param("ssss", $nama, $email, $nomor_telpon, $password);

    if ($stmt->execute()) {
        $message = "Pelanggan baru berhasil ditambahkan";
    } else {
        $message = "Error: " . $conn->error;
    }
}

// Handle Delete Operation
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $deleteQuery = "DELETE FROM pelanggan WHERE id = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $message = "Pelanggan berhasil dihapus";
    } else {
        $message = "Error: " . $conn->error;
    }
}

// Handle Edit Operation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit'])) {
    $id = $_POST['id'];
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $nomor_telpon = $_POST['nomor_telpon'];

    // Only update password if a new one is provided
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $updateQuery = "UPDATE pelanggan SET nama = ?, email = ?, nomor_telpon = ?, password = ? WHERE id = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("ssssi", $nama, $email, $nomor_telpon, $password, $id);
    } else {
        $updateQuery = "UPDATE pelanggan SET nama = ?, email = ?, nomor_telpon = ? WHERE id = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("sssi", $nama, $email, $nomor_telpon, $id);
    }

    if ($stmt->execute()) {
        $message = "Pelanggan berhasil diupdate";
    } else {
        $message = "Error: " . $conn->error;
    }
}

// Get pelanggan data for editing
$editData = null;
$showCreateForm = false;

if (isset($_GET['action'])) {
    if ($_GET['action'] == 'edit' && isset($_GET['id'])) {
        $id = $_GET['id'];
        $editQuery = "SELECT * FROM pelanggan WHERE id = ?";
        $stmt = $conn->prepare($editQuery);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $editData = $stmt->get_result()->fetch_assoc();
    } elseif ($_GET['action'] == 'create') {
        $showCreateForm = true;
    }
}

// Fetch all pelanggan
$query = "SELECT * FROM pelanggan";
$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();
$pelanggan = [];
while ($row = $result->fetch_assoc()) {
    $pelanggan[] = $row;
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIMANRES - Admin Pelanggan</title>
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
            <h1>Pelanggan</h1>

            <?php if ($message): ?>
                <div class="message"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>

            <?php if (!$editData && !$showCreateForm): ?>
                <div class="create-button">
                    <a class="button" href="pelanggan.php?action=create">Tambah Pelanggan Baru</a>
                </div>
            <?php endif; ?>

            <?php if ($showCreateForm): ?>
                <div class="form-container">
                    <h2>Tambah Pelanggan Baru</h2>
                    <form method="POST">
                        <label for="nama">Nama:</label>
                        <input type="text" id="nama" name="nama" required>

                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" required>

                        <label for="nomor_telpon">Nomor Telepon:</label>
                        <input type="text" id="nomor_telpon" name="nomor_telpon" required>

                        <label for="password">Password:</label>
                        <input type="password" id="password" name="password" required>

                        <div class="button-group">
                            <button type="submit" name="create">Simpan</button>
                            <a href="pelanggan.php" class="button">Batal</a>
                        </div>
                    </form>
                </div>
            <?php endif; ?>

            <?php if ($editData): ?>
                <div class="form-container">
                    <h2>Edit Pelanggan</h2>
                    <form method="POST">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($editData['id']) ?>">

                        <label for="nama">Nama:</label>
                        <input type="text" id="nama" name="nama" value="<?= htmlspecialchars($editData['nama']) ?>"
                            required>

                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" value="<?= htmlspecialchars($editData['email']) ?>"
                            required>

                        <label for="nomor_telpon">Nomor Telepon:</label>
                        <input type="text" id="nomor_telpon" name="nomor_telpon"
                            value="<?= htmlspecialchars($editData['nomor_telpon']) ?>" required>

                        <label for="password">Password Baru: (Kosongkan jika tidak ingin mengubah)</label>
                        <input type="password" id="password" name="password">

                        <div class="button-group">
                            <button type="submit" name="edit">Simpan Perubahan</button>
                            <a href="pelanggan.php" class="button">Batal</a>
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
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Nomor Telepon</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pelanggan as $row): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['id']) ?></td>
                                    <td><?= htmlspecialchars($row['nama']) ?></td>
                                    <td><?= htmlspecialchars($row['email']) ?></td>
                                    <td><?= htmlspecialchars($row['nomor_telpon']) ?></td>
                                    <td>
                                        <a class="button" href="pelanggan.php?action=edit&id=<?= $row['id'] ?>">Edit</a>
                                        <a class="button" href="pelanggan.php?action=delete&id=<?= $row['id'] ?>"
                                            onclick="return confirm('Apakah Anda yakin ingin menghapus pelanggan ini?')">Hapus</a>
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