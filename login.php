<?php
require_once 'includes/connect.php';
session_start();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username_email = trim($_POST['username_email']);
    $password = $_POST['password'];

    // Corrected binding key with colon
    $stmt = $pdo->prepare("SELECT * FROM public_users WHERE username = :username OR email = :email LIMIT 1");
    $stmt->execute([
    'username' => $username_email,
    'email' => $username_email
]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        header("Location: index.php");
        exit;
    } else {
        $error = 'Invalid login credentials.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            background-image: url('./assets/Images/1.jpeg');
            background-size: cover;
            background-position: center;
            height: 100vh;
        }
        .login-wrapper {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            backdrop-filter: blur(6px);
        }
        .login-form {
            background-color: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 10px;
            width: 100%;
            max-width: 400px;
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="login-form">
            <h2 class="text-center mb-4">Login</h2>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <form method="post">
                <input type="text" name="username_email" class="form-control mb-3" placeholder="Username or Email" required>
                <input type="password" name="password" class="form-control mb-3" placeholder="Password" required>
                <button type="submit" class="btn btn-primary w-100 mb-2">Login</button>
                <a href="signup.php" class="btn btn-secondary w-100">Sign Up</a>
            </form>
        </div>
    </div>
</body>
</html>
