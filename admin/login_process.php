<?php
session_start();
require_once __DIR__ . '/includes/connect.php';  // Adjust path if necessary

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        header("Location: index.php?error=" . urlencode("Please enter both username and password."));
        exit();
    }

    try {
        // Fetch user by username
        $stmt = $pdo->prepare("SELECT id, username, password FROM admins WHERE username = :username LIMIT 1");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Password is correct, create session
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_username'] = $user['username'];

            header("Location: dashboard.php");
            exit();
        } else {
            // Invalid credentials
            header("Location: index.php?error=" . urlencode("Invalid username or password."));
            exit();
        }
    } catch (PDOException $e) {
        // Log error securely in production
        header("Location: index.php?error=" . urlencode("An error occurred. Please try again."));
        exit();
    }
} else {
    header("Location: index.php");
    exit();
}
