<?php
/**
 * Admin authentication helpers.
 */
require_once __DIR__ . '/functions.php';

function isAdminLoggedIn() {
    return isset($_SESSION['admin_id']);
}

function requireAdminLogin() {
    if (!isAdminLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

function attemptAdminLogin($username, $password) {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = :username");
    $stmt->execute([':username' => $username]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_name'] = $admin['full_name'];
        $_SESSION['admin_username'] = $admin['username'];
        return true;
    }
    return false;
}

function adminLogout() {
    unset($_SESSION['admin_id'], $_SESSION['admin_name'], $_SESSION['admin_username']);
}
