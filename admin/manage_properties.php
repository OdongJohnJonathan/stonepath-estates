<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: index.php");
    exit();
}

require_once 'includes/connect.php'; // Update path if needed

// Handle deletion if requested
if (isset($_GET['delete_id'])) {
    $deleteId = (int)$_GET['delete_id'];

    // Delete property images from server and DB
    $stmtImg = $pdo->prepare("SELECT image_path FROM property_images WHERE property_id = ?");
    $stmtImg->execute([$deleteId]);
    $images = $stmtImg->fetchAll();

    foreach ($images as $img) {
        $imgPath = '../' . $img['image_path'];  // Adjust this prefix to your folder structure
        if (file_exists($imgPath)) {
            unlink($imgPath);
        }
    }

    $stmtDeleteImages = $pdo->prepare("DELETE FROM property_images WHERE property_id = ?");
    $stmtDeleteImages->execute([$deleteId]);

    // Delete property preview image file if exists
    $stmtPreview = $pdo->prepare("SELECT preview_image FROM properties WHERE id = ?");
    $stmtPreview->execute([$deleteId]);
    $preview = $stmtPreview->fetchColumn();
    $previewPath = '../' . $preview; // Adjust path prefix if needed
    if ($preview && file_exists($previewPath)) {
        unlink($previewPath);
    }

    // Delete the property itself
    $stmtDeleteProp = $pdo->prepare("DELETE FROM properties WHERE id = ?");
    $stmtDeleteProp->execute([$deleteId]);

    header("Location: manage_properties.php?deleted=1");
    exit();
}

// Fetch all properties
$stmt = $pdo->query("SELECT * FROM properties ORDER BY id DESC");
$properties = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Manage Properties | StonePath Estates Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />

    <style>
        .thumb-img {
            width: 80px;
            height: 60px;
            object-fit: cover;
            border-radius: 5px;
        }
    </style>
</head>
<body>
<?php include 'includes/header.php'; ?>

<div class="container mt-4">
    <h1 class="mb-4">Manage Properties</h1>

    <?php if (isset($_GET['deleted'])): ?>
        <div class="alert alert-success">Property deleted successfully.</div>
    <?php endif; ?>

    <a href="add_property.php" class="btn btn-danger mb-3">
        <i class="bi bi-plus-lg"></i> Add New Property
    </a>

    <?php if (count($properties) === 0): ?>
        <p>No properties found.</p>
    <?php else: ?>
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-danger">
                <tr>
                    <th>ID</th>
                    <th>Preview</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Price (UGX)</th>
                    <th>Location</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($properties as $property): ?>
                    <tr>
                        <td><?= htmlspecialchars($property['id']) ?></td>
                        <td>
                            <?php
                            $previewPath = '../' . $property['preview_image'];
                            if ($property['preview_image'] && file_exists($previewPath)): ?>
                                <img src="<?= htmlspecialchars($property['preview_image']) ?>" alt="Preview" class="thumb-img" />
                            <?php else: ?>
                                <span class="text-muted">No Image</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($property['title']) ?></td>
                        <td><?= htmlspecialchars($property['category']) ?></td>
                        <td><?= number_format($property['price'], 2) ?></td>
                        <td><?= htmlspecialchars($property['location']) ?></td>
                        <td><?= htmlspecialchars($property['status']) ?></td>
                        <td>
                            <a href="edit_property.php?id=<?= $property['id'] ?>" class="btn btn-sm btn-primary" title="Edit">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                            <a href="manage_properties.php?delete_id=<?= $property['id'] ?>"
                               class="btn btn-sm btn-danger"
                               title="Delete"
                               onclick="return confirm('Are you sure you want to delete this property? This action cannot be undone.')">
                                <i class="bi bi-trash"></i>
                            </a>
                            <a href="view_property.php?id=<?= $property['id'] ?>" class="btn btn-sm btn-info" title="View Details">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
