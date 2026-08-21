<?php
require_once __DIR__ . '/../includes/auth.php';

if (isAdminLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Please enter both username and password.';
    } elseif (attemptAdminLogin($username, $password)) {
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Invalid username or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Bellina Cosmetics</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="login-wrap">
    <div class="login-card">
        <h1>Admin Login</h1>
        <p class="subtitle">Bellina Cosmetics Store Management</p>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo h($error); ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required autofocus>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-block">Login</button>
        </form>
        <p style="text-align:center;margin-top:16px;"><a href="../index.php">&larr; Back to store</a></p>
        <p class="product-meta" style="text-align:center;">First time? Run <code>seed_admin.php</code> once to create the default admin (admin / admin123).</p>
    </div>
</div>
</body>
</html>
