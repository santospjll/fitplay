<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['tipo']) && $_SESSION['tipo'] == 'admin';
}

function isPersonal() {
    return isset($_SESSION['tipo']) && $_SESSION['tipo'] == 'personal';
}

function isCliente() {
    return isset($_SESSION['tipo']) && $_SESSION['tipo'] == 'cliente';
}

function isPersonalApproved() {
    return isset($_SESSION['tipo']) && $_SESSION['tipo'] == 'personal' && isset($_SESSION['aprovado']) && $_SESSION['aprovado'] == 1;
}

function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: ../login.php");
        exit();
    }
}

function redirectByUserType() {
    if (isLoggedIn()) {
        $type = $_SESSION['tipo'];
        header("Location: $type/dashboard.php");
        exit();
    }
}
?>