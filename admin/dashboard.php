<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard | StonePath Estates</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            min-height: 100vh;
            background-color: #f8f9fa;
        }
        .sidebar {
            min-height: 100vh;
            background-color: #343a40;
        }
        .sidebar a {
            color: #ffffff;
            display: block;
            padding: 0.75rem 1rem;
            text-decoration: none;
        }
        .sidebar a:hover, .sidebar .active {
            background-color: #dc3545;
            color: white;
        }
        .dashboard-card {
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
<?php include 'includes/header.php'; ?>


<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav class="col-md-2 d-none d-md-block sidebar">
            <div class="pt-4">
                <h4 class="text-center text-white">Admin</h4>
                <a href="dashboard.php" class="active"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
                <a href="properties.php"><i class="bi bi-house-door me-2"></i>Properties</a>
                <a href="#"><i class="bi bi-people me-2"></i>Users</a>
                <a href="#"><i class="bi bi-person-circle me-2"></i>Profile</a>
                <a href="logout.php" class="text-danger"><i class="bi bi-box-arrow-right me-2"></i>Logout</a>
            </div>
        </nav>

        <!-- Main -->
        <main class="col-md-10 ms-sm-auto px-4 py-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center mb-4">
                <h2>Dashboard</h2>
                <span class="text-muted">Welcome, <?= htmlspecialchars($_SESSION['admin_username']) ?></span>
            </div>

            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card text-bg-primary dashboard-card p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5>Total Properties</h5>
                                <h3>25</h3>
                            </div>
                            <i class="bi bi-building fs-1"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-bg-success dashboard-card p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5>Registered Users</h5>
                                <h3>102</h3>
                            </div>
                            <i class="bi bi-people-fill fs-1"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-bg-warning dashboard-card p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5>Pending Requests</h5>
                                <h3>7</h3>
                            </div>
                            <i class="bi bi-envelope-open fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-5">
                <h4>Recent Activity</h4>
                <p>This section can be filled with recent logins, property updates, or new user registrations.</p>
                <!-- Example Table -->
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Activity</th>
                            <th>Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>2025-05-27</td>
                            <td>New User Registered</td>
                            <td>Username: jdoe</td>
                        </tr>
                        <tr>
                            <td>2025-05-26</td>
                            <td>Property Added</td>
                            <td>House in Entebbe</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</div>
<?php include 'includes/footer.php'; ?>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
