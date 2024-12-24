<?php

include_once '../config.php';
include_once '../session.php';

checkUserSistem();
checkRoleKasir();

$isKasirUser = isKasir();

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIMANRES - Kasir</title>
    <link rel="stylesheet" href="../style.css">
</head>

<body>
    <div class="layout">
        <?php include_once "../sidebar.php" ?>
        <div class="main-content">
            <p>
                lorem ipsum
            </p>
        </div>
    </div>

</body>

</html>