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

// Pagination settings
$itemsPerPage = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max($page, 1); // Ensure page is at least 1

// Calculate total pages
$totalItemsQuery = "SELECT COUNT(*) as total FROM menu";
$totalItemsResult = $conn->query($totalItemsQuery);
$totalItems = $totalItemsResult->fetch_assoc()['total'];
$totalPages = ceil($totalItems / $itemsPerPage);

// Calculate offset for current page
$offset = ($page - 1) * $itemsPerPage;

// Fetch menu items with limit and offset, ordered by id in ascending order
$query = "SELECT * FROM menu ORDER BY id ASC LIMIT ? OFFSET ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $itemsPerPage, $offset);
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
    <style>
        .pagination {
            margin-top: 20px;
            text-align: center;
        }

        .pagination ul {
            list-style: none;
            padding: 0;
            display: inline-block;
        }

        .pagination ul li {
            display: inline;
            margin: 0 5px;
        }

        .pagination ul li a {
            text-decoration: none;
            padding: 5px 10px;
            background: #0f172a;
            border: 1px solid #1e293b;
            border-radius: 8px;
            color: #fff;
            transition: background-color 0.3s;
        }

        .pagination ul li a:hover {
            background-color:rgb(25, 36, 63);
        }

        .pagination ul li a.active {
            background-color: #007bff;
            color: #fff;
            pointer-events: none;
            border-color: #007bff;
        }
    </style>
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

            <div class="pagination">
                <?php if ($totalPages > 1): ?>
                    <ul>
                        <?php if ($page > 1): ?>
                            <li><a href="menu.php?page=<?= $page - 1 ?>">Sebelumnya</a></li>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li>
                                <a href="menu.php?page=<?= $i ?>"
                                    class="<?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($page < $totalPages): ?>
                            <li><a href="menu.php?page=<?= $page + 1 ?>">Berikutnya</a></li>
                        <?php endif; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>

</html>
