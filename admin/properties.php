<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: index.php");
    exit();
}

// Include your existing database connection
require_once __DIR__ . '/includes/connect.php';

// Fetch all properties
try {
    $stmt = $pdo->query("SELECT * FROM properties ORDER BY created_at DESC");
    $properties = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error fetching properties: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Properties Management | StonePath Estates Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
</head>
<body>
<?php include 'includes/header.php'; ?>


<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav class="col-md-2 d-none d-md-block bg-dark sidebar">
            <div class="pt-4">
                <h4 class="text-center text-white">Admin</h4>
                <a href="dashboard.php" class="list-group-item list-group-item-action text-white">Dashboard</a>
                <a href="properties.php" class="list-group-item list-group-item-action active">Properties</a>
                <a href="users.php" class="list-group-item list-group-item-action text-white">Users</a>
                <a href="profile.php" class="list-group-item list-group-item-action text-white">Profile</a>
                <a href="logout.php" class="list-group-item list-group-item-action text-danger">Logout</a>
            </div>
        </nav>

        <!-- Main content -->
        <main class="col-md-10 ms-sm-auto px-4 py-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2>Properties</h2>
                <a href="add_property.php" class="btn btn-success">
                    <i class="bi bi-plus-circle"></i> Add New Property
                </a>
            </div>

            <?php if (empty($properties)): ?>
                <div class="alert alert-info">No properties found. Click "Add New Property" to create one.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Location</th>
                                <th>Price</th>
                                <th>Status</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($properties as $property): ?>
                            <tr>
                                <td><?= htmlspecialchars($property['id']) ?></td>
                                <td><?= htmlspecialchars($property['title']) ?></td>
                                <td><?= htmlspecialchars($property['location']) ?></td>
                                <td><?= htmlspecialchars(number_format($property['price'], 2)) ?></td>
                                <td><?= htmlspecialchars($property['status']) ?></td>
                                <td><?= htmlspecialchars($property['created_at']) ?></td>
                                <td>
                                    <a href="edit_property.php?id=<?= $property['id'] ?>" class="btn btn-primary btn-sm">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </a>
                                    <a href="delete_property.php?id=<?= $property['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this property?');">
                                        <i class="bi bi-trash"></i> Delete
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </main>
    </div>
</div>
<?php include 'includes/footer.php'; ?>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
