<?php
require_once 'includes/connect.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid property ID.");
}

$property_id = (int)$_GET['id'];

// Fetch property info
$stmt = $pdo->prepare("SELECT * FROM properties WHERE id = ?");
$stmt->execute([$property_id]);
$property = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$property) {
    die("Property not found.");
}

// Prepare images array (assuming 'images' field holds comma-separated image filenames)
$images = [];
if (!empty($property['images'])) {
    $images = explode(',', $property['images']);
} elseif (!empty($property['preview_image'])) {
    $images[] = $property['preview_image'];
}

// Coordinates for map
$hasCoords = !empty($property['latitude']) && !empty($property['longitude']);
$latitude = $hasCoords ? $property['latitude'] : null;
$longitude = $hasCoords ? $property['longitude'] : null;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?= htmlspecialchars($property['title']) ?> | StonePath Estates</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        .property-image {
            height: 400px;
            object-fit: cover;
            width: 100%;
        }
        .gallery-thumbs img {
            height: 80px;
            width: 120px;
            object-fit: cover;
            cursor: pointer;
            margin-right: 10px;
            border: 2px solid transparent;
        }
        .gallery-thumbs img.active {
            border-color: #0d6efd;
        }
        #map {
            height: 300px;
            width: 100%;
            margin-top: 20px;
        }
    </style>
</head>
<body>
<?php include 'includes/header.php'; ?>

<div class="container py-5">
    <h1><?= htmlspecialchars($property['title']) ?></h1>
    <p class="text-muted">
        <strong>Price:</strong> Ugx. <?= number_format($property['price']) ?><br>
        <strong>Location:</strong> <?= htmlspecialchars($property['location']) ?><br>
        <strong>Category:</strong> <?= htmlspecialchars(ucfirst($property['category'])) ?><br>
        <strong>Status:</strong> <?= htmlspecialchars(ucfirst($property['status'])) ?>
    </p>

    <!-- Image Gallery -->
    <div>
        <?php if (count($images) > 0): ?>
            <img id="mainImage" src="admin/includes/properties/<?= htmlspecialchars(trim($images[0])) ?>" alt="Property Image" class="property-image mb-3" />
            <?php if (count($images) > 1): ?>
                <div class="gallery-thumbs d-flex">
                    <?php foreach ($images as $index => $img): ?>
                        <img src="admin/includes/properties/<?= htmlspecialchars(trim($img)) ?>" 
                             class="<?= $index === 0 ? 'active' : '' ?>" 
                             onclick="changeImage(this)" alt="Thumb" />
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <p>No images available.</p>
        <?php endif; ?>
    </div>

    <!-- Description -->
    <div class="mt-4">
        <h4>Description</h4>
        <p><?= nl2br(htmlspecialchars($property['description'])) ?></p>
    </div>

    <!-- Google Map -->
    <?php if ($hasCoords): ?>
        <div id="map"></div>
    <?php endif; ?>

    <!-- Inquiry Form -->
    <div class="mt-5">
        <h4>Contact About This Property</h4>
        <form action="send_inquiry.php" method="POST">
            <input type="hidden" name="property_id" value="<?= $property['id'] ?>">
            <div class="mb-3">
                <label for="name" class="form-label">Your Name</label>
                <input required type="text" id="name" name="name" class="form-control" />
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Your Email</label>
                <input required type="email" id="email" name="email" class="form-control" />
            </div>
            <div class="mb-3">
                <label for="message" class="form-label">Message</label>
                <textarea required id="message" name="message" class="form-control" rows="4">I am interested in property: <?= htmlspecialchars($property['title']) ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Send Inquiry</button>
        </form>
    </div>

    <!-- Book/Interest Button -->
    <div class="mt-4">
        <button class="btn btn-success" onclick="alert('Thank you for your interest! We will contact you shortly.')">
            Book / Show Interest
        </button>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Change main image when clicking a thumbnail
    function changeImage(el) {
        const mainImage = document.getElementById('mainImage');
        mainImage.src = el.src;

        document.querySelectorAll('.gallery-thumbs img').forEach(img => {
            img.classList.remove('active');
        });
        el.classList.add('active');
    }

    // Google Maps Initialization
    <?php if ($hasCoords): ?>
    function initMap() {
        const pos = { lat: parseFloat('<?= $latitude ?>'), lng: parseFloat('<?= $longitude ?>') };
        const map = new google.maps.Map(document.getElementById('map'), {
            zoom: 15,
            center: pos,
        });
        new google.maps.Marker({
            position: pos,
            map: map,
            title: "<?= htmlspecialchars(addslashes($property['title'])) ?>"
        });
    }
    <?php endif; ?>
</script>

<?php if ($hasCoords): ?>
<script async src="https://maps.googleapis.com/maps/api/js?key=YOUR_GOOGLE_MAPS_API_KEY&callback=initMap"></script>
<?php endif; ?>

</body>
</html>
