<?php
session_start(); // Start the session

// Destroy all session data to log the user out
session_unset();
session_destroy();

// Redirect to the login page or homepage after logout
header("Location: index.php"); // Or "login.php" if you prefer
exit();
?>
