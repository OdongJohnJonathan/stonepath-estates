<?php
require_once 'includes/connect.php';

// Handle filter input
$keyword = $_GET['keyword'] ?? '';
$category = $_GET['category'] ?? '';
$min_price = $_GET['min_price'] ?? '';
$max_price = $_GET['max_price'] ?? '';
$location = $_GET['location'] ?? '';

// Build the WHERE clause dynamically
$where = "WHERE status = 'available'";
$params = [];

if (!empty($keyword)) {
    $where .= " AND (LOWER(title) LIKE LOWER(:keyword_title) OR LOWER(description) LIKE LOWER(:keyword_desc))";
    $params[':keyword_title'] = "%$keyword%";
    $params[':keyword_desc'] = "%$keyword%";
}
if (!empty($category)) {
    $where .= " AND category = :category";
    $params[':category'] = $category;
}
if (!empty($min_price)) {
    $where .= " AND price >= :min_price";
    $params[':min_price'] = $min_price;
}
if (!empty($max_price)) {
    $where .= " AND price <= :max_price";
    $params[':max_price'] = $max_price;
}
if (!empty($location)) {
    $where .= " AND location LIKE :location";
    $params[':location'] = "%$location%";
}

$sql = "SELECT * FROM properties $where ORDER BY id DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$properties = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>All Properties | StonePath Estates</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.Default.css" />
    <style>
        .property-card img {
            height: 200px;
            object-fit: cover;
        }
        #full-map {
            height: 400px;     
            width: 100%;
            border-radius: 8px;
            margin-bottom: 40px;
            overflow: hidden;
        }
        /* Ensure map container takes full height */
        #full-map .leaflet-container {
            height: 100% !important;
            width: 100% !important;
        }
    </style>
</head>
<body>

<?php include 'includes/header.php'; ?>

<section class="py-5 bg-light">
    <div class="container">
        <h2 class="mb-4">Browse Properties</h2>

        <!-- Full Clustered Map -->
        <div id="full-map"></div>

        <!-- Filter Form -->
        <form class="row g-3 mb-5" method="GET">
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

        <!-- Property Listings -->
        <div class="row g-4">
            <?php if (count($properties) > 0): ?>
                <?php foreach ($properties as $row): ?>
                    <div class="col-md-4">
                        <div class="card property-card h-100">
                            <img src="admin/includes/properties/<?= htmlspecialchars($row['preview_image']) ?>" class="card-img-top" alt="Property Image" />
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?= htmlspecialchars($row['title']) ?></h5>
                                <p class="card-text text-muted">UGX<?= number_format($row['price']) ?> - <?= htmlspecialchars($row['location']) ?></p>
                                <a href="property_details.php?id=<?= urlencode($row['id']) ?>" class="mt-auto btn btn-outline-primary">View Details</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-warning text-center">No properties found. Try different filters.</div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.markercluster/dist/leaflet.markercluster.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const map = L.map('full-map').setView([0.3476, 32.5825], 12); // Kampala center

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    const markers = L.markerClusterGroup();

    <?php foreach ($properties as $row): 
        if (!empty($row['latitude']) && !empty($row['longitude'])):
            $lat = floatval($row['latitude']);
            $lng = floatval($row['longitude']);
            $title = htmlspecialchars($row['title'], ENT_QUOTES);
            $price = number_format($row['price']);
            $img = htmlspecialchars('admin/includes/properties/' . $row['preview_image'], ENT_QUOTES);
            $link = 'property_details.php?id=' . urlencode($row['id']);
    ?>
    const marker = L.marker([<?= $lat ?>, <?= $lng ?>]);
    marker.bindPopup(`
        <div style="max-width: 200px;">
            <img src="<?= $img ?>" alt="Image" style="width: 100%; height: 100px; object-fit: cover; border-radius: 4px;">
            <strong><?= $title ?></strong><br>
            UGX<?= $price ?><br>
            <a href="<?= $link ?>">View Details</a>
        </div>
    `);
    markers.addLayer(marker);
    <?php endif; endforeach; ?>

    map.addLayer(markers);

    setTimeout(() => {
        map.invalidateSize();
        console.log('Map invalidated');
    }, 200);
});
</script>


</body>
</html>
