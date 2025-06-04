<?php
session_start(); // Place session_start() at the very top!

// Enable error reporting for debugging purposes
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
include 'connect.php';

// Create a new database connection
$conn = new mysqli($host, $user, $pass, $db);

// Check if the connection was successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and get user input
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $property_location = mysqli_real_escape_string($conn, $_POST['property_location']);
    $property_id = mysqli_real_escape_string($conn, $_POST['property_id']);

    // Prepare the SQL statement to insert the form data
    $stmt = $conn->prepare("INSERT INTO property_registrations (first_name, last_name, email, phone, gender, property_location, property_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $first_name, $last_name, $email, $phone, $gender, $property_location, $property_id);

    // Execute the query and check if the insertion was successful
    if ($stmt->execute()) {
        //Set both session variables.
        $_SESSION['registered'] = true;
        $_SESSION['username'] = $first_name; // Or any other suitable value.

        // Close the statement and connection
        $stmt->close();
        $conn->close();

        // Redirect to dashboard.php to prevent form resubmission
        header("Location: dashboard.php");
        exit(); // Make sure no further code is executed after the redirect
    } else {
        // Display error message if insertion failed
        echo "Error: " . $stmt->error;
    }

    // Close the statement
    $stmt->close();
}

// Close the connection
$conn->close();
?>