<?php
session_start();
if (isset($_SESSION['admin_logged_in'])) {
    header("Location: dashboard.php");
    exit();
}

$error = isset($_GET['error']) ? $_GET['error'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login | StonePath Estates</title>
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
    .login-container {
        min-height: 100vh;
        padding-top: 10vh;
        padding-bottom: 10vh;
    }
    .card {
        border-radius: 16px;
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        background-color: rgba(255, 255, 255, 0.95); /* slightly transparent white */
        color: #000; /* black text inside card */
    }
    .form-control:focus {
        border-color: #dc3545;
        box-shadow: 0 0 0 0.2rem rgba(220,53,69,.25);
    }
</style>

</head>
<body>

<div class="container d-flex justify-content-center align-items-center login-container">
    <div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-5 my-4">
        <div class="card p-4 w-100">
            <h3 class="text-center mb-4">Admin Login</h3>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form id="loginForm" action="login_process.php" method="POST" novalidate>
                <div class="mb-3">
                    <label for="username" class="form-label">Admin Username</label>
                    <input type="text" class="form-control" id="username" name="username" required minlength="3" autocomplete="off" autofocus>
                    <div class="invalid-feedback">
                        Please enter your username (at least 3 characters).
                    </div>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="password" name="password" required minlength="6">
                        <button class="btn btn-outline-secondary" type="button" id="togglePassword" tabindex="-1">
                            <i class="bi bi-eye"></i>
                        </button>
                        <div class="invalid-feedback">
                            Please enter your password (at least 6 characters).
                        </div>
                    </div>
                </div>

                <div class="d-grid mb-3">
                    <button type="submit" class="btn btn-danger">Login</button>
                </div>

                <div class="text-center">
                    <a href="register.php" class="btn btn-outline-primary">Sign Up</a>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Show/hide password -->
<script>
    document.getElementById('togglePassword').addEventListener('click', function () {
        const passwordInput = document.getElementById('password');
        const icon = this.querySelector('i');
        const isPassword = passwordInput.type === 'password';

        passwordInput.type = isPassword ? 'text' : 'password';
        icon.classList.toggle('bi-eye');
        icon.classList.toggle('bi-eye-slash');
    });
</script>

<!-- Client-side validation -->
<script>
    (function () {
        'use strict';

        const form = document.getElementById('loginForm');

        form.addEventListener('submit', function (event) {
            // Clear all previous validation states
            const inputs = form.querySelectorAll('input');
            inputs.forEach(input => input.classList.remove('is-invalid'));

            let valid = true;

            // Username validation: required and min length 3
            const username = form.username;
            if (!username.value.trim() || username.value.trim().length < 3) {
                username.classList.add('is-invalid');
                valid = false;
            }

            // Password validation: required and min length 6
            const password = form.password;
            if (!password.value || password.value.length < 6) {
                password.classList.add('is-invalid');
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
