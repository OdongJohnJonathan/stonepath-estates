<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Redirect if not logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: index.php");
    exit();
}

// Get current page for active link highlighting
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>StonePath Estates Admin Panel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />

    <style>
        body {
            min-height: 100vh;
            background-color: #f8f9fa;
        }
        /* Sidebar styles if you still want sidebar, else remove */
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

<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
  <div class="container-fluid">
    <a class="navbar-brand" href="dashboard.php">StonePath Estates Admin</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar" aria-controls="adminNavbar" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
  
    <div class="collapse navbar-collapse" id="adminNavbar">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link <?= $currentPage === 'dashboard.php' ? 'active' : '' ?>" href="dashboard.php">Dashboard</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= in_array($currentPage, ['manage_properties.php', 'add_property.php', 'edit_property.php']) ? 'active' : '' ?>" href="manage_properties.php">Manage Properties</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= $currentPage === 'add_property.php' ? 'active' : '' ?>" href="add_property.php">Add Property</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= in_array($currentPage, ['manage_users.php', 'add_user.php']) ? 'active' : '' ?>" href="manage_users.php">Manage Users</a>
        </li>
      </ul>

      <ul class="navbar-nav ms-auto">
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <?= htmlspecialchars($_SESSION['admin_username'] ?? 'Admin') ?>
          </a>
          <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="profile.php">Profile</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item text-danger" href="logout.php">Logout</a></li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>
