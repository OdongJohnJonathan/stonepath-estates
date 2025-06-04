<?php
session_start();
if (isset($_SESSION['admin_logged_in'])) {
    header("Location: dashboard.php");
    exit();
}

$error = $_GET['error'] ?? '';
$success = $_GET['success'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Register | StonePath Estates</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background: 
                linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), 
                url('../assets/images/1.jpeg') no-repeat center center fixed;
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center center;
            min-height: 100vh;
            color: #fff;
        }
        .register-container {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 2rem 1rem;
        }
        .card {
            border-radius: 16px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            background-color: rgba(255, 255, 255, 0.95);
            color: #000;
            max-width: 480px;
            width: 100%;
            padding: 2rem;
        }
        .form-control:focus {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.2rem rgba(220,53,69,.25);
        }
    </style>
</head>
<body>
<?php include 'includes/header.php'; ?>


<div class="register-container">
    <div class="card">
        <h3 class="text-center mb-4">Admin Registration</h3>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form id="registerForm" action="register_process.php" method="POST" novalidate>
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" required minlength="3" autofocus>
                <div class="invalid-feedback">
                    Please enter a username (at least 3 characters).
                </div>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <div class="input-group">
                    <input type="password" class="form-control" id="password" name="password" required minlength="8" aria-describedby="togglePassword1">
                    <button class="btn btn-outline-secondary" type="button" id="togglePassword1" tabindex="-1">
                        <i class="bi bi-eye"></i>
                    </button>
                    <div class="invalid-feedback">
                        Password must be at least 8 characters, include 1 uppercase letter and 1 special symbol.
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="confirm_password" class="form-label">Confirm Password</label>
                <div class="input-group">
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required minlength="8" aria-describedby="togglePassword2">
                    <button class="btn btn-outline-secondary" type="button" id="togglePassword2" tabindex="-1">
                        <i class="bi bi-eye"></i>
                    </button>
                    <div class="invalid-feedback" id="confirmPasswordFeedback">
                        Please confirm your password and ensure it matches.
                    </div>
                </div>
            </div>

            <div class="d-grid mb-3">
                <button type="submit" class="btn btn-danger">Register</button>
            </div>
            <p class="text-center">Already have an account? <a href="index.php">Login</a></p>
        </form>
    </div>
</div>
<?php include 'includes/footer.php'; ?>


<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Toggle password visibility -->
<script>
    function togglePasswordVisibility(toggleId, inputId) {
        const toggleBtn = document.getElementById(toggleId);
        const input = document.getElementById(inputId);
        const icon = toggleBtn.querySelector('i');

        toggleBtn.addEventListener('click', () => {
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        });
    }

    togglePasswordVisibility('togglePassword1', 'password');
    togglePasswordVisibility('togglePassword2', 'confirm_password');
</script>

<!-- Client-side validation -->
<script>
    (function () {
        'use strict';

        const form = document.getElementById('registerForm');

        form.addEventListener('submit', function (event) {
            // Clear previous validation states
            const inputs = form.querySelectorAll('input');
            inputs.forEach(input => input.classList.remove('is-invalid'));

            let valid = true;

            // Username: required, min length 3
            const username = form.username;
            if (!username.value.trim() || username.value.trim().length < 3) {
                username.classList.add('is-invalid');
                valid = false;
            }

            // Password validation: min 8 chars, 1 uppercase, 1 special char
            const password = form.password;
            const passwordVal = password.value;
            const uppercaseRegex = /[A-Z]/;
            const specialCharRegex = /[!@#$%^&*(),.?":{}|<>]/;

            if (!passwordVal || passwordVal.length < 8 || !uppercaseRegex.test(passwordVal) || !specialCharRegex.test(passwordVal)) {
                password.classList.add('is-invalid');
                valid = false;
            }

            // Confirm password: required, matches password
            const confirmPassword = form.confirm_password;
            if (!confirmPassword.value || confirmPassword.value !== passwordVal) {
                confirmPassword.classList.add('is-invalid');
                valid = false;
            }

            if (!valid) {
                event.preventDefault();
                event.stopPropagation();
            }
        }, false);
    })();
</script>

</body>
</html>
