<?php
// Database credentials
$host = 'localhost';
$db   = 'stonepath_estates'; // Use your actual database name
$user = 'root';              // Default for XAMPP
$pass = '';                  // Default for XAMPP (no password)
$charset = 'utf8mb4';

// Set up DSN and connection
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // throw exceptions
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // return associative arrays
    PDO::ATTR_EMULATE_PREPARES   => false,                  // use native prepares if supported
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // You can customize this error message or log it
    die('Database connection failed: ' . $e->getMessage());
}
