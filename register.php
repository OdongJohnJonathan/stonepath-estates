<?php
// Database connection
include 'connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and get user input
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $dob = mysqli_real_escape_string($conn, $_POST['dob']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash password securely

    // Check if username or email already exists
    $checkUser = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $checkUser->bind_param("ss", $username, $email);
    $checkUser->execute();
    $checkUser->store_result();

    if ($checkUser->num_rows > 0) {
        echo "Username or email already taken. Please choose another.";
    } else {
        // Insert user into database using prepared statement
        $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, username, gender, dob, phone, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssss", $first_name, $last_name, $email, $username, $gender, $dob, $phone, $password);
        
        if ($stmt->execute()) {
            // Redirect to login page after successful signup
            header("Location: index.php");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    }

    $checkUser->close();
}

$conn->close();
?>
