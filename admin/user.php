<?php
include_once '../config.php';
include_once '../session.php';

checkUserSistem();
checkRoleAdmin();

$isAdminUser = isAdmin();
$message = '';

// Handle Create Operation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password
    $role = $_POST['role'];

    $createQuery = "INSERT INTO user (username, password, role) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($createQuery);
    $stmt->bind_param("sss", $username, $password, $role);

    if ($stmt->execute()) {
        $message = "User baru berhasil ditambahkan";
    } else {
        $message = "Error: " . $conn->error;
    }
}

// Handle Delete Operation
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $deleteQuery = "DELETE FROM user WHERE id = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $message = "User berhasil dihapus";
    } else {
        $message = "Error: " . $conn->error;
    }
}

// Handle Edit Operation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit'])) {
    $id = $_POST['id'];
    $username = $_POST['username'];
    $role = $_POST['role'];

    // Check if password is being updated
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $updateQuery = "UPDATE users SET username = ?, password = ?, role = ? WHERE id = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("sssi", $username, $password, $role, $id);
    } else {
        $updateQuery = "UPDATE users SET username = ?, role = ? WHERE id = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("ssi", $username, $role, $id);
    }

    if ($stmt->execute()) {
        $message = "User berhasil diupdate";
    } else {
        $message = "Error: " . $conn->error;
    }
}

// Get user data for editing
$editData = null;
$showCreateForm = false;

if (isset($_GET['action'])) {
    if ($_GET['action'] == 'edit' && isset($_GET['id'])) {
        $id = $_GET['id'];
        $editQuery = "SELECT id, username, role FROM user WHERE id = ?";
        $stmt = $conn->prepare($editQuery);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $editData = $stmt->get_result()->fetch_assoc();
    } elseif ($_GET['action'] == 'create') {
        $showCreateForm = true;
    }
}

// Fetch all users
$query = "SELECT id, username, role FROM user";
$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();
$users = [];
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIMANRES - Admin User</title>
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

        .form-container input,
        .form-container select {
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

        .password-note {
            font-size: 0.9em;
            color: #666;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <div class="layout">
        <?php include_once "../sidebar.php" ?>
        <div class="main-content">
            <h1>User</h1>

            <?php if ($message): ?>
                <div class="message"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>

            <?php if (!$editData && !$showCreateForm): ?>
                <div class="create-button">
                    <a class="button" href="user.php?action=create">Tambah User Baru</a>
                </div>
            <?php endif; ?>

            <?php if ($showCreateForm): ?>
                <div class="form-container">
                    <h2>Tambah User Baru</h2>
                    <form method="POST">
                        <label for="username">Username:</label>
                        <input type="text" id="username" name="username" required>

                        <label for="password">Password:</label>
                        <input type="password" id="password" name="password" required>

                        <label for="role">Role:</label>
                        <select id="role" name="role" required>
                            <option value="admin">Admin</option>
                            <option value="kasir">Kasir</option>
                        </select>

                        <div class="button-group">
                            <button type="submit" name="create">Simpan</button>
                            <a href="user.php" class="button">Batal</a>
                        </div>
                    </form>
                </div>
            <?php endif; ?>

            <?php if ($editData): ?>
                <div class="form-container">
                    <h2>Edit User</h2>
                    <form method="POST">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($editData['id']) ?>">

                        <label for="username">Username:</label>
                        <input type="text" id="username" name="username"
                            value="<?= htmlspecialchars($editData['username']) ?>" required>

                        <label for="password">Password:</label>
                        <input type="password" id="password" name="password">
                        <div class="password-note">Biarkan kosong jika tidak ingin mengubah password</div>

                        <label for="role">Role:</label>
                        <select id="role" name="role" required>
                            <option value="admin" <?= $editData['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                            <option value="kasir" <?= $editData['role'] == 'kasir' ? 'selected' : '' ?>>Kasir</option>
                        </select>

                        <div class="button-group">
                            <button type="submit" name="edit">Simpan Perubahan</button>
                            <a href="user.php" class="button">Batal</a>
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
                                <th>Username</th>
                                <th>Role</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?= htmlspecialchars($user['id']) ?></td>
                                    <td><?= htmlspecialchars($user['username']) ?></td>
                                    <td><?= htmlspecialchars($user['role']) ?></td>
                                    <td>
                                        <a class="button" href="user.php?action=edit&id=<?= $user['id'] ?>">Edit</a>
                                        <a class="button" href="user.php?action=delete&id=<?= $user['id'] ?>"
                                            onclick="return confirm('Apakah Anda yakin ingin menghapus user ini?')">Hapus</a>
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