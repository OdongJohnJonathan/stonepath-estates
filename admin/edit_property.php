<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: index.php");
    exit();
}

require_once 'includes/connect.php';

if (!isset($_GET['id'])) {
    header("Location: manage_properties.php");
    exit();
}

$propertyId = (int)$_GET['id'];

// Fetch property data
$stmt = $pdo->prepare("SELECT * FROM properties WHERE id = ?");
$stmt->execute([$propertyId]);
$property = $stmt->fetch();

if (!$property) {
    die("Property not found.");
}

// Fetch existing images
$stmtImg = $pdo->prepare("SELECT * FROM property_images WHERE property_id = ?");
$stmtImg->execute([$propertyId]);
$images = $stmtImg->fetchAll();

// Initialize error and success messages
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate inputs
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $location = trim($_POST['location'] ?? '');
    $status = $_POST['status'] ?? '';
    $category = $_POST['category'] ?? '';
    $preview_image_id = $_POST['preview_image'] ?? null; // This is the image id to be preview

    // Basic validation
    if (strlen($title) < 3) {
        $errors[] = "Title must be at least 3 characters.";
    }
    if ($price <= 0) {
        $errors[] = "Price must be a positive number.";
    }
    if (!in_array($status, ['available', 'sold', 'pending'])) {
        $errors[] = "Invalid status selected.";
    }
    if (!in_array($category, ['rent', 'sell', 'airbnb', 'other'])) {
        $errors[] = "Invalid category selected.";
    }

    // If no errors, proceed with update
    if (empty($errors)) {
        try {
            // Start transaction
            $pdo->beginTransaction();

            // Update property main data (without changing preview_image yet)
            $stmtUpdate = $pdo->prepare("UPDATE properties SET title = ?, description = ?, price = ?, location = ?, status = ?, category = ? WHERE id = ?");
            $stmtUpdate->execute([$title, $description, $price, $location, $status, $category, $propertyId]);

            // Handle image deletions
            if (!empty($_POST['delete_images'])) {
                foreach ($_POST['delete_images'] as $delImgId) {
                    $delImgId = (int)$delImgId;

                    // Fetch image path (relative)
                    $stmtImgPath = $pdo->prepare("SELECT image_path FROM property_images WHERE id = ? AND property_id = ?");
                    $stmtImgPath->execute([$delImgId, $propertyId]);
                    $imageDbPath = $stmtImgPath->fetchColumn();

                    if ($imageDbPath) {
                        // Delete physical file
                        $fullPath = '../' . $imageDbPath; // Adjust '../' if your folder structure differs
                        if (file_exists($fullPath)) {
                            unlink($fullPath);
                        }

                        // If deleted image was preview image, reset preview_image_id
                        if ($property['preview_image'] === $imageDbPath) {
                            $preview_image_id = null;
                        }
                    }

                    // Delete DB record
                    $stmtDelImg = $pdo->prepare("DELETE FROM property_images WHERE id = ?");
                    $stmtDelImg->execute([$delImgId]);
                }
            }

            // Handle new image uploads
            if (!empty($_FILES['new_images']) && !empty($_FILES['new_images']['name'][0])) {
                $uploadDir = 'uploads/properties/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                foreach ($_FILES['new_images']['tmp_name'] as $key => $tmpName) {
                    $name = basename($_FILES['new_images']['name'][$key]);
                    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                    $allowedExts = ['jpg', 'jpeg', 'png', 'gif'];

                    if (in_array($ext, $allowedExts)) {
                        $newFileName = uniqid('prop_', true) . '.' . $ext;
                        $targetPath = $uploadDir . $newFileName;

                        if (move_uploaded_file($tmpName, $targetPath)) {
                            // Insert new image record
                            $stmtInsertImg = $pdo->prepare("INSERT INTO property_images (property_id, image_path) VALUES (?, ?)");
                            $stmtInsertImg->execute([$propertyId, $targetPath]);

                            // If preview_image_id is null, set it to first uploaded image
                            if ($preview_image_id === null) {
                                $preview_image_id = $pdo->lastInsertId();
                            }
                        }
                    }
                }
            }

            // Set preview image path in properties table based on chosen image id
            if ($preview_image_id) {
                // Find image path for the chosen preview image id
                $stmtPreviewImg = $pdo->prepare("SELECT image_path FROM property_images WHERE id = ? AND property_id = ?");
                $stmtPreviewImg->execute([$preview_image_id, $propertyId]);
                $previewPath = $stmtPreviewImg->fetchColumn();

                if ($previewPath) {
                    // Update preview_image in properties table
                    $stmtUpdatePreview = $pdo->prepare("UPDATE properties SET preview_image = ? WHERE id = ?");
                    $stmtUpdatePreview->execute([$previewPath, $propertyId]);
                }
            } else {
                // If no preview image selected or all deleted, clear preview_image
                $stmtClearPreview = $pdo->prepare("UPDATE properties SET preview_image = NULL WHERE id = ?");
                $stmtClearPreview->execute([$propertyId]);
            }

            $pdo->commit();
            $success = true;

            // Refresh property and images data after update
            $stmt = $pdo->prepare("SELECT * FROM properties WHERE id = ?");
            $stmt->execute([$propertyId]);
            $property = $stmt->fetch();

            $stmtImg = $pdo->prepare("SELECT * FROM property_images WHERE property_id = ?");
            $stmtImg->execute([$propertyId]);
            $images = $stmtImg->fetchAll();

        } catch (Exception $e) {
            $pdo->rollBack();
            $errors[] = "Update failed: " . $e->getMessage();
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Edit Property | StonePath Estates Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />

    <style>
        .thumb-img {
            width: 100px;
            height: 75px;
            object-fit: cover;
            border-radius: 5px;
        }
        .form-check-label {
            user-select: none;
        }
    </style>
</head>
<body>
<?php include 'includes/header.php'; ?>

<div class="container mt-4">
    <h1>Edit Property</h1>
    <a href="manage_properties.php" class="btn btn-secondary mb-3"><i class="bi bi-arrow-left"></i> Back to List</a>

    <?php if ($success): ?>
        <div class="alert alert-success">Property updated successfully!</div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="edit_property.php?id=<?= $propertyId ?>" method="POST" enctype="multipart/form-data" novalidate>
        <div class="mb-3">
            <label for="title" class="form-label">Title *</label>
            <input type="text" id="title" name="title" class="form-control" required minlength="3" value="<?= htmlspecialchars($property['title']) ?>">
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description *</label>
            <textarea id="description" name="description" class="form-control" rows="4" required><?= htmlspecialchars($property['description']) ?></textarea>
        </div>

        <div class="mb-3">
            <label for="price" class="form-label">Price (UGX) *</label>
            <input type="number" id="price" name="price" min="0" step="0.01" class="form-control" required value="<?= htmlspecialchars($property['price']) ?>">
        </div>

        <div class="mb-3">
            <label for="location" class="form-label">Location *</label>
            <input type="text" id="location" name="location" class="form-control" required value="<?= htmlspecialchars($property['location']) ?>">
        </div>

        <div class="mb-3">
            <label for="status" class="form-label">Status *</label>
            <select id="status" name="status" class="form-select" required>
                <?php
                $statuses = ['available' => 'Available', 'sold' => 'Sold', 'pending' => 'Pending'];
                foreach ($statuses as $val => $label) {
                    $selected = $property['status'] === $val ? 'selected' : '';
                    echo "<option value=\"$val\" $selected>$label</option>";
                }
                ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="category" class="form-label">Category *</label>
            <select id="category" name="category" class="form-select" required>
                <?php
                $categories = ['rent' => 'Rent', 'sell' => 'Sell', 'airbnb' => 'Airbnb', 'other' => 'Other'];
                foreach ($categories as $val => $label) {
                    $selected = $property['category'] === $val ? 'selected' : '';
                    echo "<option value=\"$val\" $selected>$label</option>";
                }
                ?>
            </select>
        </div>

        <div class="mb-4">
            <label class="form-label">Existing Images</label>
            <?php if (count($images) === 0): ?>
                <p>No images uploaded for this property yet.</p>
            <?php else: ?>
                <div class="row g-3">
                    <?php foreach ($images as $img): ?>
                        <div class="col-md-3 text-center border rounded p-2 position-relative">
                            <img src="<?= htmlspecialchars($img['image_path']) ?>" alt="Image" class="thumb-img mb-2" />
                            <div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" 
                                        name="preview_image" 
                                        id="preview_<?= $img['id'] ?>" 
                                        value="<?= $img['id'] ?>"
                                        <?= ($property['preview_image'] === $img['image_path']) ? 'checked' : '' ?>
                                    >
                                    <label class="form-check-label" for="preview_<?= $img['id'] ?>">
                                        Preview Image
                                    </label>
                                </div>

                                <div class="form-check mt-1">
                                    <input class="form-check-input" type="checkbox" name="delete_images[]" id="delete_<?= $img['id'] ?>" value="<?= $img['id'] ?>">
                                    <label class="form-check-label text-danger" for="delete_<?= $img['id'] ?>">Delete</label>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="mb-4">
            <label for="new_images" class="form-label">Add New Images (You can select multiple)</label>
            <input type="file" id="new_images" name="new_images[]" class="form-control" accept="image/*" multiple>
        </div>

        <button type="submit" class="btn btn-primary">Update Property</button>
    </form>
</div>
<?php include 'includes/footer.php'; ?>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
