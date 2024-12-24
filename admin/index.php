<?php

include_once '../config.php';
include_once '../session.php';

checkUserSistem();
checkRoleAdmin();

$isAdminUser = isAdmin();

// Query to get the counts
$orderCountQuery = "SELECT COUNT(*) as count FROM pesanan";
$tableCountQuery = "SELECT COUNT(*) as count FROM meja";
$reservationCountQuery = "SELECT COUNT(*) as count FROM reservasi";

// Execute the queries
$orderCountResult = $conn->query($orderCountQuery);
$tableCountResult = $conn->query($tableCountQuery);
$reservationCountResult = $conn->query($reservationCountQuery);

// Fetch the counts
$orderCount = $orderCountResult->fetch_assoc()['count'];
$tableCount = $tableCountResult->fetch_assoc()['count'];
$reservationCount = $reservationCountResult->fetch_assoc()['count'];

// Close the connection
$conn->close();

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIMANRES - Administrasi</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        .card-container {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .card {
            background: #0f172a;
            border: 1px solid #1e293b;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 20px;
            width: 300px;
            text-align: center;
            transition: transform 0.2s;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card-title {
            font-size: 1.2em;
            margin-bottom: 10px;
            font-weight: bold;
        }

        .card-content {
            font-weight: bold;
        }

        .card-action a {
            text-decoration: none;
            color: #007bff;
            font-weight: bold;
        }

        .card-action a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="layout">
        <?php include_once "../sidebar.php" ?>
        <div class="main-content">
            <div class="card-container">
                <div class="card">
                    <div class="card-title">Jumlah Pesanan</div>
                    <div class="card-content"><?php echo $orderCount; ?></div>
                </div>
                <div class="card">
                    <div class="card-title">Jumlah Meja</div>
                    <div class="card-content"><?php echo $tableCount; ?></div>
                </div>
                <div class="card">
                    <div class="card-title">Total Reservasi</div>
                    <div class="card-content"><?php echo $reservationCount; ?></div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>