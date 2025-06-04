<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: index.php");
    exit();
}

require_once 'includes/connect.php';  // Make sure this path is correct to your connect.php

// Initialize variables for form feedback
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and sanitize input
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = trim($_POST['price'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $category = $_POST['category'] ?? '';
    $status = $_POST['status'] ?? 'Available';

    // Basic validation
    if (!$title || !$description || !$price || !$location || !$category) {
        $error = "Please fill in all required fields.";
    } elseif (!is_numeric($price) || $price < 0) {
        $error = "Price must be a positive number.";
    } else {
        // Insert into properties table without preview_image yet
        $stmt = $pdo->prepare("INSERT INTO properties (title, description, price, location, category, status) VALUES (?, ?, ?, ?, ?, ?)");
        try {
            $stmt->execute([$title, $description, $price, $location, $category, $status]);
            $propertyId = $pdo->lastInsertId();

            // Handle images upload
            $uploadedImages = [];
            if (!empty($_FILES['images']['name'][0])) {
                $uploadDir = 'uploads/properties/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                foreach ($_FILES['images']['tmp_name'] as $key => $tmpName) {
                    $fileName = basename($_FILES['images']['name'][$key]);
                    $fileType = $_FILES['images']['type'][$key];
                    $fileTmpName = $tmpName;
                    $fileError = $_FILES['images']['error'][$key];
                    $fileSize = $_FILES['images']['size'][$key];

                    // Validate file
                    if ($fileError === UPLOAD_ERR_OK) {
                        if (in_array($fileType, $allowedTypes)) {
                            if ($fileSize <= 5 * 1024 * 1024) { // max 5MB per image
                                // Generate unique filename
                                $ext = pathinfo($fileName, PATHINFO_EXTENSION);
                                $newFileName = uniqid('prop_' . $propertyId . '_') . '.' . $ext;
                                $destination = $uploadDir . $newFileName;

                                if (move_uploaded_file($fileTmpName, $destination)) {
                                    $uploadedImages[] = $destination;
                                } else {
                                    $error = "Failed to move uploaded file: $fileName";
                                    break;
                                }
                            } else {
                                $error = "File size too large for $fileName. Max 5MB allowed.";
                                break;
                            }
                        } else {
                            $error = "Invalid file type for $fileName. Only JPG, PNG, GIF allowed.";
                            break;
                        }
                    } else {
                        $error = "Error uploading file: $fileName";
                        break;
                    }
                }
            }

            if (!$error) {
                // Insert images paths into property_images table
                $previewImage = null;
                foreach ($uploadedImages as $index => $imagePath) {
                    $stmtImg = $pdo->prepare("INSERT INTO property_images (property_id, image_path) VALUES (?, ?)");
                    $stmtImg->execute([$propertyId, $imagePath]);

                    if ($index === 0) {
                        $previewImage = $imagePath; // First image is preview
                    }
                }

                // Update properties table with preview image path
                if ($previewImage) {
                    $stmtUpdate = $pdo->prepare("UPDATE properties SET preview_image = ? WHERE id = ?");
                    $stmtUpdate->execute([$previewImage, $propertyId]);
                }

                $success = "Property added successfully!";
            }

        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Add Property | StonePath Estates Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />

    <style>
        body {
            background-color: #f8f9fa;
            min-height: 100vh;
        }
        .container {
            max-width: 720px;
            margin-top: 40px;
        }
    </style>
</head>
<body>
<?php include 'includes/header.php'; ?>

<div class="container">
    <h1 class="mb-4">Add New Property</h1>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php elseif ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form action="add_property.php" method="POST" enctype="multipart/form-data" novalidate>
        <div class="mb-3">
            <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
            <input
                type="text"
                class="form-control"
                id="title"
                name="title"
                required
                minlength="3"
                value="<?= htmlspecialchars($_POST['title'] ?? '') ?>"
            />
            <div class="invalid-feedback">Please enter a title (at least 3 characters).</div>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
            <textarea
                class="form-control"
                id="description"
                name="description"
                rows="4"
                required
            ><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
            <div class="invalid-feedback">Please enter a description.</div>
        </div>

        <div class="mb-3">
            <label for="price" class="form-label">Price (UGX) <span class="text-danger">*</span></label>
            <input
                type="number"
                class="form-control"
                id="price"
                name="price"
                min="0"
                step="0.01"
                required
                value="<?= htmlspecialchars($_POST['price'] ?? '') ?>"
            />
            <div class="invalid-feedback">Please enter a valid price.</div>
        </div>

        <div class="mb-3">
            <label for="location" class="form-label">Location <span class="text-danger">*</span></label>
            <input
                type="text"
                class="form-control"
                id="location"
                name="location"
                required
                value="<?= htmlspecialchars($_POST['location'] ?? '') ?>"
            />
            <div class="invalid-feedback">Please enter a location.</div>
        </div>

        <div class="mb-3">
            <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
            <select class="form-select" id="category" name="category" required>
                <option value="" disabled <?= !isset($_POST['category']) ? 'selected' : '' ?>>Select category</option>
                <option value="Rent" <?= (($_POST['category'] ?? '') === 'Rent') ? 'selected' : '' ?>>Rent</option>
                <option value="Sell" <?= (($_POST['category'] ?? '') === 'Sell') ? 'selected' : '' ?>>Sell</option>
                <option value="Airbnb" <?= (($_POST['category'] ?? '') === 'Airbnb') ? 'selected' : '' ?>>Airbnb</option>
                <option value="Other" <?= (($_POST['category'] ?? '') === 'Other') ? 'selected' : '' ?>>Other</option>
            </select>
            <div class="invalid-feedback">Please select a category.</div>
        </div>

        <div class="mb-3">
            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
            <select class="form-select" id="status" name="status" required>
                <option value="Available" <?= (($_POST['status'] ?? '') === 'Available') ? 'selected' : '' ?>>Available</option>
                <option value="Sold" <?= (($_POST['status'] ?? '') === 'Sold') ? 'selected' : '' ?>>Sold</option>
                <option value="Pending" <?= (($_POST['status'] ?? '') === 'Pending') ? 'selected' : '' ?>>Pending</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="images" class="form-label">Property Images</label>
            <input
                type="file"
                class="form-control"
                id="images"
                name="images[]"
                multiple
                accept="image/*"
            />
            <small class="text-muted">Upload multiple images (max 5MB each).</small>
        </div>

        <button type="submit" class="btn btn-danger">Add Property</button>
    </form>
</div>
<?php include 'includes/footer.php'; ?>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Client-side validation bootstrap style
(() => {
    'use strict';

    const form = document.querySelector('form');

    form.addEventListener('submit', event => {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        }
        form.classList.add('was-validated');
    });
})();
</script>
</body>
</html>
