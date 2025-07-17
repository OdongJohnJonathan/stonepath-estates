<?php
// index.php - Home page
require_once 'includes/connect.php'; // Make sure your DB connection is correct
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>StonePath Estates | Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <style>
        .hero {
            background: url('assets/images/1.jpeg') no-repeat center center/cover;
            color: white;
            padding: 100px 0;
        }
        .property-card img {
            height: 200px;
            object-fit: cover;
        }
        .category-icon {
            font-size: 2rem;
        }
        .main-header {
            position: sticky;
            top: 0;
            z-index: 1030;
            background-color: rgba(255, 255, 255, 0.9);
            transition: background-color 0.3s ease;
            box-shadow: none;
        }
        .main-header.scrolled {
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .category-box {
            transition: all 0.3s ease-in-out;
            border: 2px solid transparent;
        }
        .category-box:hover {
            transform: translateY(-8px);
            border-color: #0d6efd;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }
        .category-icon {
            transition: transform 0.3s ease-in-out;
        }
        .category-box:hover .category-icon {
            transform: scale(1.2);
        }

    </style>
</head>
<body>

<?php include 'includes/header.php'; ?>

<!-- Hero Section -->
<section class="hero text-center">
    <div class="container">
        <h1 class="display-4" style="color: #000; font-weight: 600; font-size: 4.2rem;">Find Your Dream Property</h1>
        <p class="lead" style="color: #000; font-weight: 200;">Buy, Rent or Book with StonePath Estates</p>
        <form class="row g-2 justify-content-center mt-4" action="search.php" method="GET">
            <div class="col-md-3">
                <input type="text" name="keyword" class="form-control" placeholder="Search properties..." />
            </div>
            <div class="col-md-3">
                <select name="category" class="form-select">
                    <option value="">All Categories</option>
                    <option value="rent">Rent</option>
                    <option value="sell">Buy</option>
                    <option value="airbnb">Airbnb</option>
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary w-100" type="submit">Search</button>
            </div>
        </form>
    </div>
</section>

<!-- Categories -->
<section class="py-5 bg-light text-center">
  <div class="container">
    <h2 class="mb-4">Explore Categories</h2>
    <div class="row g-4">
      <div class="col-md-3">
        <a href="properties.php" class="category-box text-decoration-none d-block p-4 rounded shadow-sm bg-white">
          <i class="bi bi-house-door category-icon fs-1 mb-2 d-block text-primary"></i>
          <h5 class="text-dark">Rent</h5>
        </a>
      </div>
      <div class="col-md-3">
        <a href="properties.php" class="category-box text-decoration-none d-block p-4 rounded shadow-sm bg-white">
          <i class="bi bi-currency-dollar category-icon fs-1 mb-2 d-block text-success"></i>
          <h5 class="text-dark">Buy</h5>
        </a>
      </div>
      <div class="col-md-3">
        <a href="properties.php" class="category-box text-decoration-none d-block p-4 rounded shadow-sm bg-white">
          <i class="bi bi-globe2 category-icon fs-1 mb-2 d-block text-info"></i>
          <h5 class="text-dark">Airbnb</h5>
        </a>
      </div>
      <div class="col-md-3">
        <a href="properties.php" class="category-box text-decoration-none d-block p-4 rounded shadow-sm bg-white">
          <i class="bi bi-box2 category-icon fs-1 mb-2 d-block text-warning"></i>
          <h5 class="text-dark">Other</h5>
        </a>
      </div>
    </div>
  </div>
</section>


<!-- Featured Properties -->
<section class="py-5">
    <div class="container">
        <h2 class="mb-4">Featured Properties</h2>
        <div class="row g-4">
            <?php
            try {
                $stmt = $pdo->query("SELECT * FROM properties WHERE status='available' ORDER BY id DESC LIMIT 6");
                $properties = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($properties as $row):
                    $imageFile = $row['preview_image'];
                    if (!empty($imageFile)) {
                        // Check if the path already includes the folder to avoid duplication
                        if (strpos($imageFile, 'uploads/properties/') === false) {
                            $imageUrl = "/admin/uploads/properties/" . $imageFile;
                            $imagePath = __DIR__ . "/admin/uploads/properties/" . $imageFile;
                        } else {
                            $imageUrl = "/" . $imageFile;
                            $imagePath = __DIR__ . "/" . $imageFile;
                        }
                    } else {
                        $imageUrl = "";
                        $imagePath = "";
                    }

                    // Check if file exists, else use default image
                    if (!empty($imageFile) && file_exists($imagePath)) {
                        $previewImage = htmlspecialchars($imageUrl);
                    } else {
                        $previewImage = "assets/images/no-image.png";
                    }
            ?>
            <div class="col-md-4">
                <div class="card property-card">
                    <img src="<?= $previewImage ?>" class="card-img-top" alt="Property Image" />
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($row['title']) ?></h5>
                        <p class="card-text text-muted">UGX<?= number_format($row['price']) ?> - <?= htmlspecialchars($row['location']) ?></p>
                        <a href="property_details.php?id=<?= $row['id'] ?>" class="btn btn-outline-primary">View Details</a>
                    </div>
                </div>
            </div>
            <?php
                endforeach;
            } catch (Exception $e) {
                echo "<p class='text-danger'>Error loading properties.</p>";
            }
            ?>
        </div>
    </div>
</section>

<!-- About Us -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <h2>About StonePath Estates</h2>
                <p>StonePath Estates is your trusted partner in finding the perfect property for rent, sale, or stay. We pride ourselves on excellent service and a large selection of listings tailored to your needs.</p>
            </div>
            <div class="col-md-6">
                <img src="assets/images/5.jpeg" class="img-fluid rounded" alt="About" />
            </div>
        </div>
    </div>
</section>

<!-- Testimonials -->
<section class="py-5 text-center">
    <div class="container">
        <h2 class="mb-4">What Our Clients Say</h2>
        <div class="row">
            <div class="col-md-4">
                <blockquote class="blockquote">
                    <p>"StonePath helped me find my dream apartment in just days! Highly recommend."</p>
                    <footer class="blockquote-footer">Jane D.</footer>
                </blockquote>
            </div>
            <div class="col-md-4">
                <blockquote class="blockquote">
                    <p>"Professional, quick, and friendly service. Will use again for my next move."</p>
                    <footer class="blockquote-footer">Mark T.</footer>
                </blockquote>
            </div>
            <div class="col-md-4">
                <blockquote class="blockquote">
                    <p>"Great selection of properties and responsive team."</p>
                    <footer class="blockquote-footer">Aliyah N.</footer>
                </blockquote>
            </div>
        </div>
    </div>
</section>

<!-- Newsletter Signup -->
<section class="py-5 bg-dark text-white text-center">
    <div class="container">
        <h2>Stay Updated</h2>
        <form class="row g-2 justify-content-center mt-3" action="subscribe.php" method="POST">
            <div class="col-md-4">
                <input type="email" name="email" class="form-control" placeholder="Enter your email" required />
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Subscribe</button>
            </div>
        </form>
    </div>
</section>

<!-- Contact Form -->
<section class="py-5">
    <div class="container">
        <h2 class="text-center mb-4">Contact Us</h2>
        <form action="contact.php" method="POST" class="row g-3">
            <div class="col-md-6">
                <input type="text" name="name" class="form-control" placeholder="Your Name" required />
            </div>
            <div class="col-md-6">
                <input type="email" name="email" class="form-control" placeholder="Your Email" required />
            </div>
            <div class="col-12">
                <textarea name="message" rows="5" class="form-control" placeholder="Your Message" required></textarea>
            </div>
            <div class="col-12 text-center">
                <button type="submit" class="btn btn-success">Send Message</button>
            </div>
        </form>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Navbar scroll background toggle
    const header = document.querySelector('.main-header');
    window.addEventListener('scroll', () => {
        if (window.scrollY > 50) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    });
</script>
</body>
</html>
