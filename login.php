<?php
session_start();
require_once 'includes/db.php';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['full_name'] = $user['full_name'];
        header('Location: index.php');
        exit;
    }
    $error = 'Invalid username or password.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Pending Works</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body class="login-body">
    <form method="POST" class="login-card">
        <div class="login-logo">PW</div>
        <h1>Pending Works</h1>
        <p>Job Order Management System</p>
        <?php if ($error): ?><div class="alert"><?= e($error) ?></div><?php endif; ?>
        <label>Username</label>
        <input type="text" name="username" required placeholder="admin">
        <label>Password</label>
        <input type="password" name="password" required placeholder="admin123">
        <button type="submit">Login</button>
        <small>Default: admin / admin123</small>
    </form>
</body>
</html>
