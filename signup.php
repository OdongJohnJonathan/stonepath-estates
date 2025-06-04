<?php
require_once 'includes/connect.php';
$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $gender = $_POST['gender'] ?? '';
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Password validation
    if ($password !== $confirm_password) {
        $errors[] = 'Passwords do not match.';
    }
    if (
        strlen($password) < 8 ||
        !preg_match('/[A-Z]/', $password) ||
        !preg_match('/\d/', $password) ||
        !preg_match('/[\W_]/', $password)
    ) {
        $errors[] = 'Password must be at least 8 characters and include an uppercase letter, a number, and a special character.';
    }

    // Check if username or email exists
    $stmt = $pdo->prepare("SELECT id FROM public_users WHERE username = :username OR email = :email");
    $stmt->execute(['username' => $username, 'email' => $email]);
    if ($stmt->fetch()) {
        $errors[] = 'Username or email already exists.';
    }

    // Register user
    if (empty($errors)) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO public_users (username, email, phone, gender, password) VALUES (:username, :email, :phone, :gender, :password)");
        $stmt->execute([
            'username' => $username,
            'email' => $email,
            'phone' => $phone,
            'gender' => $gender,
            'password' => $hashed
        ]);
        $success = 'Account created successfully. <a href="login.php">Login</a>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Sign Up</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            background: url('./assets/Images/1.jpeg') no-repeat center center fixed;
            background-size: cover;
        }
        .form-container {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 0 15px rgba(0,0,0,0.2);
        }
        .centered {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>
<body>
<div class="container centered">
    <div class="form-container" style="max-width: 500px; width: 100%;">
        <h2 class="text-center mb-4">Sign Up</h2>
        <?php foreach ($errors as $error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endforeach; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>
        <form method="post">
            <input type="text" name="username" class="form-control mb-3" placeholder="Username" required>
            <input type="email" name="email" class="form-control mb-3" placeholder="Email Address" required>
            <input type="tel" name="phone" class="form-control mb-3" placeholder="Phone Number" required>
            <select name="gender" class="form-control mb-3" required>
                <option value="">-- Select Gender --</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
            </select>
            <input type="password" name="password" class="form-control mb-3" placeholder="Password" required>
            <input type="password" name="confirm_password" class="form-control mb-3" placeholder="Confirm Password" required>
            <button type="submit" class="btn btn-primary w-100">Create Account</button>
        </form>
    </div>
</div>
</body>
</html>
