<?php
session_start();
require_once __DIR__ . '/includes/connect.php';  // Adjust path if needed

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and sanitize inputs
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Basic validations
    if (empty($username) || empty($password) || empty($confirm_password)) {
        header("Location: register.php?error=" . urlencode("All fields are required."));
        exit();
    }

    if ($password !== $confirm_password) {
        header("Location: register.php?error=" . urlencode("Passwords do not match."));
        exit();
    }

    if (strlen($password) < 6) {
        header("Location: register.php?error=" . urlencode("Password must be at least 6 characters long."));
        exit();
    }

    try {
        // Check if username already exists
        $stmt = $pdo->prepare("SELECT id FROM admins WHERE username = :username LIMIT 1");
        $stmt->execute(['username' => $username]);
        if ($stmt->fetch()) {
            header("Location: register.php?error=" . urlencode("Username already taken."));
            exit();
        }

        // Hash the password securely
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        // Insert new admin user
        $stmt = $pdo->prepare("INSERT INTO admins (username, password) VALUES (:username, :password)");
        $stmt->execute([
            'username' => $username,
            'password' => $passwordHash
        ]);

        // Redirect to login with success message
        header("Location: index.php?success=" . urlencode("Registration successful. Please login."));
        exit();

    } catch (PDOException $e) {
        // Log error in real application
        header("Location: register.php?error=" . urlencode("An error occurred. Please try again."));
        exit();
    }
} else {
    // If accessed without POST
    header("Location: register.php");
    exit();
}
