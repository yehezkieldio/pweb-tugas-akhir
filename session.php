<?php

session_start();

function checkLogin()
{
    if (!isset($_SESSION['user'])) {
        header('Location: login.php');
        exit();
    }
}

function alreadyLogin()
{
    if (isset($_SESSION['user'])) {
        header('Location: index.php');
        exit();
    }
}

function checkRole($role)
{
    if (!isset($_SESSION['role']) || $_SESSION['role'] != $role) {
        header('Location: index.php');
        exit();
    }
}
