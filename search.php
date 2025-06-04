<?php
// search.php

require_once 'includes/connect.php';

// Sanitize input and assign default values
$keyword = $_GET['keyword'] ?? '';
$category = $_GET['category'] ?? '';
$location = $_GET['location'] ?? '';
$min_price = $_GET['min_price'] ?? '';
$max_price = $_GET['max_price'] ?? '';

// Build SQL with filters
$sql = "SELECT * FROM properties WHERE status = 'available'";
$params = [];

if (!empty($keyword)) {
    $sql .= " AND (title LIKE :keyword OR description LIKE :keyword)";
    $params[':keyword'] = '%' . $keyword . '%';
}

if (!empty($category)) {
    $sql .= " AND category = :category";
    $params[':category'] = $category;
}

if (!empty($location)) {
    $sql .= " AND location LIKE :location";
    $params[':location'] = '%' . $location . '%';
}

if (!empty($min_price)) {
    $sql .= " AND price >= :min_price";
    $params[':min_price'] = $min_price;
}

if (!empty($max_price)) {
    $sql .= " AND price <= :max_price";
    $params[':max_price'] = $max_price;
}

$sql .= " ORDER BY id DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Search Results | StonePath Estates</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        .property-card img {
            height: 200px;
            object-fit: cover;
        }
    </style>
</head>
<body>

<?php include 'includes/header.php'; ?>

<div class="container py-5">
    <h2 class="mb-4">Search Properties</h2>

    <!-- Filter Form -->
    <form class="row g-3 mb-5" method="GET" action="search.php">
        <div class="col-md-3">
            <input type="text" name="keyword" class="form-control" placeholder="Keyword" value="<?= htmlspecialchars($keyword) ?>">
        </div>
        <div class="col-md-2">
            <select name="category" class="form-select">
                <option value="">All Categories</option>
                <option value="rent" <?= $category == 'rent' ? 'selected' : '' ?>>Rent</option>
                <option value="sell" <?= $category == 'sell' ? 'selected' : '' ?>>Buy</option>
                <option value="airbnb" <?= $category == 'airbnb' ? 'selected' : '' ?>>Airbnb</option>
                <option value="other" <?= $category == 'other' ? 'selected' : '' ?>>Other</option>
            </select>
        </div>
        <div class="col-md-2">
            <input type="number" name="min_price" class="form-control" placeholder="Min Price" value="<?= htmlspecialchars($min_price) ?>">
        </div>
        <div class="col-md-2">
            <input type="number" name="max_price" class="form-control" placeholder="Max Price" value="<?= htmlspecialchars($max_price) ?>">
        </div>
        <div class="col-md-2">
            <input type="text" name="location" class="form-control" placeholder="Location" value="<?= htmlspecialchars($location) ?>">
        </div>
        <div class="col-md-1">
            <button type="submit" class="btn btn-primary w-100">Filter</button>
        </div>
    </form>

    <?php if (count($results) > 0): ?>
        <p class="mb-4"><strong><?= count($results) ?></strong> properties found.</p>
        <div class="row g-4">
            <?php foreach ($results as $property): ?>
                <div class="col-md-4">
                    <div class="card property-card h-100">
                        <img src="admin/includes/properties/<?= htmlspecialchars($property['preview_image']) ?>" class="card-img-top" alt="Property">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?= htmlspecialchars($property['title']) ?></h5>
                            <p class="card-text text-muted">$<?= number_format($property['price']) ?> - <?= htmlspecialchars($property['location']) ?></p>
                            <a href="property_details.php?id=<?= $property['id'] ?>" class="mt-auto btn btn-outline-primary">View Details</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-warning">No properties match your search.</div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
