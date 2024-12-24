<?php
include_once '../config.php';
include_once '../session.php';

checkUserSistem();
checkRoleAdmin();

$isAdminUser = isAdmin();
$message = '';

// Handle Create Operation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create'])) {
    $reservasi_id = $_POST['reservasi_id'];
    $menu_id = $_POST['menu_id'];
    $jumlah = $_POST['jumlah'];
    $status = $_POST['status'];

    $createQuery = "INSERT INTO pesanan (reservasi_id, menu_id, jumlah, status) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($createQuery);
    $stmt->bind_param("iiis", $reservasi_id, $menu_id, $jumlah, $status);

    if ($stmt->execute()) {
        $message = "Pesanan baru berhasil ditambahkan";
    } else {
        $message = "Error: " . $conn->error;
    }
}

// Handle Delete Operation
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $deleteQuery = "DELETE FROM pesanan WHERE id = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $message = "Pesanan berhasil dihapus";
    } else {
        $message = "Error: " . $conn->error;
    }
}

// Handle Edit Operation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit'])) {
    $id = $_POST['id'];
    $reservasi_id = $_POST['reservasi_id'];
    $menu_id = $_POST['menu_id'];
    $jumlah = $_POST['jumlah'];
    $status = $_POST['status'];

    $updateQuery = "UPDATE pesanan SET reservasi_id = ?, menu_id = ?, jumlah = ?, status = ? WHERE id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("iiisi", $reservasi_id, $menu_id, $jumlah, $status, $id);

    if ($stmt->execute()) {
        $message = "Pesanan berhasil diupdate";
    } else {
        $message = "Error: " . $conn->error;
    }
}

// Get pesanan data for editing
$editData = null;
$showCreateForm = false;

if (isset($_GET['action'])) {
    if ($_GET['action'] == 'edit' && isset($_GET['id'])) {
        $id = $_GET['id'];
        $editQuery = "SELECT * FROM pesanan WHERE id = ?";
        $stmt = $conn->prepare($editQuery);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $editData = $stmt->get_result()->fetch_assoc();
    } elseif ($_GET['action'] == 'create') {
        $showCreateForm = true;
    }
}

// Fetch all pesanan items with related data
$query = "SELECT p.*, m.nama as menu_nama,
          r.tanggal, r.waktu, r.jumlah_tamu, r.meja_id
          FROM pesanan p
          JOIN menu m ON p.menu_id = m.id
          JOIN reservasi r ON p.reservasi_id = r.id
          ORDER BY p.id DESC";
$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();
$pesanan_items = [];
while ($row = $result->fetch_assoc()) {
    $pesanan_items[] = $row;
}

// Fetch menu items for dropdown
$menuQuery = "SELECT id, nama FROM menu ORDER BY nama";
$menuStmt = $conn->prepare($menuQuery);
$menuStmt->execute();
$menuResult = $menuStmt->get_result();
$menu_items = [];
while ($row = $menuResult->fetch_assoc()) {
    $menu_items[] = $row;
}

// Fetch reservasi items for dropdown
$reservasiQuery = "SELECT id, CONCAT('Meja ', meja_id, ' - ', DATE_FORMAT(tanggal, '%d/%m/%Y'), ' ', waktu) as nama
                   FROM reservasi
                   ORDER BY id DESC";
$reservasiStmt = $conn->prepare($reservasiQuery);
$reservasiStmt->execute();
$reservasiResult = $reservasiStmt->get_result();
$reservasi_items = [];
while ($row = $reservasiResult->fetch_assoc()) {
    $reservasi_items[] = $row;
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIMANRES - Admin Pesanan</title>
    <link rel="stylesheet" href="../style.css">
</head>

<body>
    <div class="layout">
        <?php include_once "../sidebar.php" ?>
        <div class="main-content">
            <h1>Pesanan</h1>

            <?php if ($message): ?>
                <div class="message"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>

            <?php if (!$editData && !$showCreateForm): ?>
                <div class="create-button">
                    <a class="button" href="pesanan.php?action=create">Tambah Pesanan Baru</a>
                </div>
            <?php endif; ?>

            <?php if ($showCreateForm): ?>
                <div class="form-container">
                    <h2>Tambah Pesanan Baru</h2>
                    <form method="POST">
                        <label for="reservasi_id">Reservasi:</label>
                        <select id="reservasi_id" name="reservasi_id" required>
                            <?php foreach ($reservasi_items as $reservasi): ?>
                                <option value="<?= $reservasi['id'] ?>"><?= htmlspecialchars($reservasi['nama']) ?></option>
                            <?php endforeach; ?>
                        </select>

                        <label for="menu_id">Menu:</label>
                        <select id="menu_id" name="menu_id" required>
                            <?php foreach ($menu_items as $menu): ?>
                                <option value="<?= $menu['id'] ?>"><?= htmlspecialchars($menu['nama']) ?></option>
                            <?php endforeach; ?>
                        </select>

                        <label for="jumlah">Jumlah:</label>
                        <input type="number" id="jumlah" name="jumlah" required min="1">

                        <label for="status">Status:</label>
                        <select id="status" name="status" required>
                            <option value="menunggu">Menunggu</option>
                            <option value="selesai">Selesai</option>
                        </select>

                        <div class="button-group">
                            <button type="submit" name="create">Simpan</button>
                            <a href="pesanan.php" class="button">Batal</a>
                        </div>
                    </form>
                </div>
            <?php endif; ?>

            <?php if ($editData): ?>
                <div class="form-container">
                    <h2>Edit Pesanan</h2>
                    <form method="POST">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($editData['id']) ?>">

                        <label for="reservasi_id">Reservasi:</label>
                        <select id="reservasi_id" name="reservasi_id" required>
                            <?php foreach ($reservasi_items as $reservasi): ?>
                                <option value="<?= $reservasi['id'] ?>" <?= $editData['reservasi_id'] == $reservasi['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($reservasi['nama']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <label for="menu_id">Menu:</label>
                        <select id="menu_id" name="menu_id" required>
                            <?php foreach ($menu_items as $menu): ?>
                                <option value="<?= $menu['id'] ?>" <?= $editData['menu_id'] == $menu['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($menu['nama']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <label for="jumlah">Jumlah:</label>
                        <input type="number" id="jumlah" name="jumlah" value="<?= htmlspecialchars($editData['jumlah']) ?>"
                            required min="1">

                        <label for="status">Status:</label>
                        <select id="status" name="status" required>
                            <option value="menunggu" <?= $editData['status'] == 'menunggu' ? 'selected' : '' ?>>Menunggu
                            </option>
                            <option value="selesai" <?= $editData['status'] == 'selesai' ? 'selected' : '' ?>>Selesai</option>
                        </select>

                        <div class="button-group">
                            <button type="submit" name="edit">Simpan Perubahan</button>
                            <a href="pesanan.php" class="button">Batal</a>
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
                                <th>Reservasi</th>
                                <th>Menu</th>
                                <th>Jumlah</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pesanan_items as $item): ?>
                                <tr>
                                    <td><?= htmlspecialchars($item['id']) ?></td>
                                    <td><?= htmlspecialchars($item['reservasi_id']) ?></td>
                                    <td><?= htmlspecialchars($item['menu_nama']) ?></td>
                                    <td><?= htmlspecialchars($item['jumlah']) ?></td>
                                    <td><?= htmlspecialchars($item['status']) ?></td>
                                    <td>
                                        <a class="button" href="pesanan.php?action=edit&id=<?= $item['id'] ?>">Edit</a>
                                        <a class="button" href="pesanan.php?action=delete&id=<?= $item['id'] ?>"
                                            onclick="return confirm('Apakah Anda yakin ingin menghapus pesanan ini?')">Hapus</a>
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