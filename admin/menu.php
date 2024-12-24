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
    $harga = $_POST['harga'];

    $createQuery = "INSERT INTO menu (nama, harga) VALUES (?, ?)";
    $stmt = $conn->prepare($createQuery);
    $stmt->bind_param("si", $nama, $harga);

    if ($stmt->execute()) {
        $message = "Menu baru berhasil ditambahkan";
    } else {
        $message = "Error: " . $conn->error;
    }
}

// Handle Delete Operation
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $deleteQuery = "DELETE FROM menu WHERE id = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $message = "Menu berhasil dihapus";
    } else {
        $message = "Error: " . $conn->error;
    }
}

// Handle Edit Operation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit'])) {
    $id = $_POST['id'];
    $nama = $_POST['nama'];
    $harga = $_POST['harga'];

    $updateQuery = "UPDATE menu SET nama = ?, harga = ? WHERE id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("sii", $nama, $harga, $id);

    if ($stmt->execute()) {
        $message = "Menu berhasil diupdate";
    } else {
        $message = "Error: " . $conn->error;
    }
}

// Get menu data for editing
$editData = null;
$showCreateForm = false;

if (isset($_GET['action'])) {
    if ($_GET['action'] == 'edit' && isset($_GET['id'])) {
        $id = $_GET['id'];
        $editQuery = "SELECT * FROM menu WHERE id = ?";
        $stmt = $conn->prepare($editQuery);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $editData = $stmt->get_result()->fetch_assoc();
    } elseif ($_GET['action'] == 'create') {
        $showCreateForm = true;
    }
}

// Fetch all menu items
$query = "SELECT * FROM menu ORDER BY nama";
$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();
$menu_items = [];
while ($row = $result->fetch_assoc()) {
    $menu_items[] = $row;
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIMANRES - Admin Menu</title>
    <link rel="stylesheet" href="../style.css">
</head>

<body>
    <div class="layout">
        <?php include_once "../sidebar.php" ?>
        <div class="main-content">
            <h1>Menu</h1>

            <?php if ($message): ?>
                <div class="message"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>

            <?php if (!$editData && !$showCreateForm): ?>
                <div class="create-button">
                    <a class="button" href="menu.php?action=create">Tambah Menu Baru</a>
                </div>
            <?php endif; ?>

            <?php if ($showCreateForm): ?>
                <div class="form-container">
                    <h2>Tambah Menu Baru</h2>
                    <form method="POST">
                        <label for="nama">Nama Menu:</label>
                        <input type="text" id="nama" name="nama" required>

                        <label for="harga">Harga:</label>
                        <input type="number" id="harga" name="harga" required>

                        <div class="button-group">
                            <button type="submit" name="create">Simpan</button>
                            <a href="menu.php" class="button">Batal</a>
                        </div>
                    </form>
                </div>
            <?php endif; ?>

            <?php if ($editData): ?>
                <div class="form-container">
                    <h2>Edit Menu</h2>
                    <form method="POST">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($editData['id']) ?>">

                        <label for="nama">Nama Menu:</label>
                        <input type="text" id="nama" name="nama"
                            value="<?= htmlspecialchars($editData['nama']) ?>" required>

                        <label for="harga">Harga:</label>
                        <input type="number" id="harga" name="harga"
                            value="<?= htmlspecialchars($editData['harga']) ?>" required>

                        <div class="button-group">
                            <button type="submit" name="edit">Simpan Perubahan</button>
                            <a href="menu.php" class="button">Batal</a>
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
                                <th>Nama Menu</th>
                                <th>Harga</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($menu_items as $key => $item): ?>
                                <tr>
                                    <td><?= htmlspecialchars($item['id']) ?></td>
                                    <td><?= htmlspecialchars($item['nama']) ?></td>
                                    <td>Rp<?= number_format($item['harga'], 0, ',', '.') ?></td>
                                    <td>
                                        <a class="button" href="menu.php?action=edit&id=<?= $item['id'] ?>">Edit</a>
                                        <a class="button" href="menu.php?action=delete&id=<?= $item['id'] ?>"
                                            onclick="return confirm('Apakah Anda yakin ingin menghapus menu ini?')">Hapus</a>
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