<?php

include_once '../config.php';
include_once '../session.php';

checkUserSistem();
checkRoleAdmin();

$isAdminUser = isAdmin();

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIMANRES - Administrasi</title>
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