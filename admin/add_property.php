<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: index.php");
    exit();
}

require_once 'includes/connect.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = trim($_POST['price'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $category = $_POST['category'] ?? '';
    $status = $_POST['status'] ?? 'Available';
    $latitude = $_POST['latitude'] ?? null;
    $longitude = $_POST['longitude'] ?? null;

    if (!$title || !$description || !$price || !$location || !$category) {
        $error = "Please fill in all required fields.";
    } elseif (!is_numeric($price) || $price < 0) {
        $error = "Price must be a positive number.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO properties (title, description, price, location, category, status, latitude, longitude) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        try {
            $stmt->execute([$title, $description, $price, $location, $category, $status, $latitude, $longitude]);
            $propertyId = $pdo->lastInsertId();

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

                    if ($fileError === UPLOAD_ERR_OK) {
                        if (in_array($fileType, $allowedTypes)) {
                            if ($fileSize <= 5 * 1024 * 1024) {
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
                $previewImage = null;
                foreach ($uploadedImages as $index => $imagePath) {
                    $stmtImg = $pdo->prepare("INSERT INTO property_images (property_id, image_path) VALUES (?, ?)");
                    $stmtImg->execute([$propertyId, $imagePath]);

                    if ($index === 0) {
                        $previewImage = $imagePath;
                    }
                }

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
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />

    <style>
        body {
            background-color: #f8f9fa;
            min-height: 100vh;
        }
        .container {
            max-width: 720px;
            margin-top: 40px;
        }
        #map {
            height: 400px;
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
            <input type="text" class="form-control" id="title" name="title" required minlength="3"
                   value="<?= htmlspecialchars($_POST['title'] ?? '') ?>" />
            <div class="invalid-feedback">Please enter a title (at least 3 characters).</div>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
            <textarea class="form-control" id="description" name="description" rows="4" required><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
            <div class="invalid-feedback">Please enter a description.</div>
        </div>

        <div class="mb-3">
            <label for="price" class="form-label">Price (UGX) <span class="text-danger">*</span></label>
            <input type="number" class="form-control" id="price" name="price" min="0" step="0.01" required
                   value="<?= htmlspecialchars($_POST['price'] ?? '') ?>" />
            <div class="invalid-feedback">Please enter a valid price.</div>
        </div>

        <div class="mb-3">
            <label for="location" class="form-label">Location <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="location" name="location" required
                   value="<?= htmlspecialchars($_POST['location'] ?? '') ?>" />
            <div class="invalid-feedback">Please enter a location.</div>
        </div>

        <!-- ✅ Map Section -->
        <div class="mb-3">
    <label for="map">Mark property location:</label>
    <div class="d-flex justify-content-between align-items-center mb-2">
        <button type="button" class="btn btn-outline-primary btn-sm" id="locateMeBtn">
            📍 Use My Location
        </button>
        <small class="text-muted">Or click on the map manually</small>
    </div>
    <div id="map"></div>
    <input type="hidden" id="latitude" name="latitude">
    <input type="hidden" id="longitude" name="longitude">
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
            <input type="file" class="form-control" id="images" name="images[]" multiple accept="image/*" />
            <small class="text-muted">Upload multiple images (max 5MB each).</small>
        </div>

        <button type="submit" class="btn btn-danger">Add Property</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<script>
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

// Initialize Leaflet map
var map = L.map('map').setView([0.3136, 32.5811], 13);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors'
}).addTo(map);

var marker = L.marker([0.3136, 32.5811], { draggable: true }).addTo(map);

// Set initial values
document.getElementById('latitude').value = marker.getLatLng().lat;
document.getElementById('longitude').value = marker.getLatLng().lng;

// Update values on drag
marker.on('dragend', function(e) {
    var pos = marker.getLatLng();
    document.getElementById('latitude').value = pos.lat;
    document.getElementById('longitude').value = pos.lng;
    reverseGeocode(pos.lat, pos.lng);
});

// Update values on map click
map.on('click', function(e) {
    marker.setLatLng(e.latlng);
    document.getElementById('latitude').value = e.latlng.lat;
    document.getElementById('longitude').value = e.latlng.lng;
    reverseGeocode(e.latlng.lat, e.latlng.lng);
});

// 📍 Use My Location
const locateBtn = document.getElementById('locateMeBtn');
if (locateBtn) {
    locateBtn.addEventListener('click', function () {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function (position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;

                map.setView([lat, lng], 15);
                marker.setLatLng([lat, lng]);

                document.getElementById('latitude').value = lat;
                document.getElementById('longitude').value = lng;

                // Auto-fill location input
                reverseGeocode(lat, lng);

            }, function (error) {
                alert("Failed to get your location. Please allow location access.");
            });
        } else {
            alert("Geolocation is not supported by your browser.");
        }
    });
}

// 🧠 Reverse Geocoding using Nominatim
function reverseGeocode(lat, lng) {
    fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json`)
        .then(response => response.json())
        .then(data => {
            if (data && data.display_name) {
                document.getElementById('location').value = data.display_name;
            } else {
                console.warn("No address found for coordinates.");
            }
        })
        .catch(error => {
            console.error("Reverse geocoding failed:", error);
        });
}
</script>


</body>
</html>
