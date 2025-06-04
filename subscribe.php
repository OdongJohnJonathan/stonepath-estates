<?php
// subscribe.php

require_once 'includes/connect.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email address.']);
        exit;
    }

    // Check for duplicates
    $stmt = $pdo->prepare("SELECT id FROM subscribers WHERE email = ?");
    $stmt->execute([$email]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => false, 'message' => 'You are already subscribed.']);
        exit;
    }

    // Insert new subscription
    $stmt = $pdo->prepare("INSERT INTO subscribers (email, subscribed_at) VALUES (?, NOW())");
    $stmt->execute([$email]);

    echo json_encode(['success' => true, 'message' => 'Subscription successful.']);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid request.']);
