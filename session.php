<?php

session_start();

function checkLogin()
{
    if (!isset($_SESSION['user'])) {
        header('Location: login.php');
        exit();
    }
}

function checkIfAlreadyLogin(): bool
{
    return isset($_SESSION['user']);
} {
    if (isset($_SESSION['user'])) {
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

function logout()
{
    session_destroy();
    header('Location: login.php');
    exit();
}

function logoutUser()
{
    session_destroy();
    header('Location: index.php');
}

/* -------------------------------------------------------------------------- */

function checkUserSistem()
{
    if (!isset($_SESSION['admin'])) {
        header('Location: login.php');
        exit();
    }
}

function checkRoleKasir()
{
    $isKasirRole = isset($_SESSION['role']) && $_SESSION['role'] == 'kasir';
    if (!$isKasirRole) {
        header('Location: /');
        exit();
    }
}

function checkRoleAdmin()
{
    $isAdminRole = isset($_SESSION['role']) && $_SESSION['role'] == 'admin';
    if (!$isAdminRole) {
        header('Location: /');
        exit();
    }
}

function isAdmin(): bool
{
    return isset($_SESSION['role']) && $_SESSION['role'] == 'admin';
}

function isKasir(): bool
{
    return isset($_SESSION['role']) && $_SESSION['role'] == 'kasir';
}