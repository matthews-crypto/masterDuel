<?php
// Fichier: includes/auth.php

require_once 'db_connect.php';

function authenticateAdmin($username, $password) {
    $db = getDB();
    $stmt = $db->prepare("SELECT id, password FROM admins WHERE username = :username");
    $stmt->execute(['username' => $username]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password'])) {
        return $admin['id'];
    }
    return false;
}

function isAdminLoggedIn() {
    return isset($_SESSION['admin_id']);
}

function requireAdminLogin() {
    if (!isAdminLoggedIn()) {
        header("Location: login.php");
        exit();
    }
}

function loginAdmin($admin_id) {
    $_SESSION['admin_id'] = $admin_id;
}

function logoutAdmin() {
    unset($_SESSION['admin_id']);
    session_destroy();
}
?>