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
    <div>
        <?php include_once "../sidebar.php" ?>
        <div>

        </div>
    </div>

</body>

</html>