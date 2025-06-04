<?php
require_once 'includes/auth.php';


// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: index.php"); // Redirect to login page
    exit();
}
?>

<!DOCTYPE html>
<html lang="en-GB">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta content="" description="">
    <script defer src="script.js"></script>
    <title>StonePath Estates</title>
    <style>
        body {
            background-color: #1a1a1a;
            color: #fff;
            margin: 0;
            padding: 0;
        }

        /* Fullscreen Banner */
        .carousel-item {
            height: 100vh;
            background-size: cover;
            background-position: center;
            position: relative;
        }

        .carousel-item:nth-child(1) {
            background-image: url('images/1.jpeg');
        }

        .carousel-item:nth-child(2) {
            background-image: url('images/2.jpeg');
        }

        .carousel-item:nth-child(3) {
            background-image: url('images/3.jpeg');
        }

        .carousel-item:nth-child(4) {
            background-image: url('images/4.jpeg');
        }

        .carousel-item:nth-child(5) {
            background-image: url('images/5.jpeg');
        }

        .carousel-item:nth-child(6) {
            background-image: url('images/6.jpeg');
        }

        /* Banner text style */
        .banner-text {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            background: rgba(0, 0, 0, 0.5);
            padding: 20px;
            border-radius: 15px;
            color: white;
        }

        /* Carousel Controls */
        .carousel-control-prev-icon,
        .carousel-control-next-icon {
            background-color: #fff;
        }

        .carousel-fade .carousel-item {
            transition: opacity 1s ease;
        }
    </style>
</head>
<body>
    <!-- NAV AND BANNER SECTION -->
    <div class="container-fluid p-0">
        <!-- Navbar -->
        <?php include './includes/nav.php'; ?>

        <!-- Banner section with Carousel -->
        <div id="carouselBanner" class="carousel slide carousel-fade" data-bs-ride="carousel">
            <div class="carousel-inner">
                <!-- First Image -->
                <div class="carousel-item active">
                    <div class="banner-text">
                        <h1>Welcome to StonePath Estates</h1>
                        <p>We are dedicated to finding you the best properties at the best deals in town!</p>
                        <div class="mt-3">
                            <a href="services.php" class="btn btn-danger">Looking to buy a house</a>
                            <a href="services.php" class="btn btn-light">Looking to rent</a>
                        </div>
                    </div>
                </div>
                <!-- Second Image -->
                <div class="carousel-item">
                    <div class="banner-text">
                        <h1>Discover Your Dream Home</h1>
                        <p>We offer a wide range of properties just for you!</p>
                    </div>
                </div>
                <!-- Third Image -->
                <div class="carousel-item">
                    <div class="banner-text">
                        <h1>Find the Perfect Plot of Land</h1>
                        <p>Affordable and prime locations available for sale.</p>
                    </div>
                </div>
                <!-- Fourth Image -->
                <div class="carousel-item">
                    <div class="banner-text">
                        <h1>Your Ideal Home Awaits</h1>
                        <p>Explore our wide range of houses for sale and rent.</p>
                    </div>
                </div>
                <!-- Fifth Image -->
                <div class="carousel-item">
                    <div class="banner-text">
                        <h1>Exclusive Deals Just for You</h1>
                        <p>Get the best deals on properties today!</p>
                    </div>
                </div>
                <!-- Sixth Image -->
                <div class="carousel-item">
                    <div class="banner-text">
                        <h1>Trust Us with Your Next Property</h1>
                        <p>Let us help you find your dream property in no time.</p>
                    </div>
                </div>
            </div>
            <!-- Carousel controls -->
            <button class="carousel-control-prev" type="button" data-bs-target="#carouselBanner" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carouselBanner" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
    </div>
    <!-- END NAV AND BANNER SECTION -->

    <!-- ABOUT US SECTION -->
    <section class="container my-5">
        <h3 class="text-center text-dark">ABOUT US</h3>
        <div class="row align-items-center bg-light p-4 rounded">
            <div class="col-md-6">
                <img src="images/download (8).jpeg" class="img-fluid rounded" alt="About Us">
            </div>
            <div class="col-md-6">
                <h3>About Us</h3>
                <p>
                    Hey there! We are a bunch of passionate computer science students with hands-on experience in software development and embedded systems...
                    <br>
                    Welcome to Stonepath Estates! We are your trusted partner in finding the perfect home or investment property. With years of experience in the real estate industry, we pride ourselves on providing personalized, professional service to each of our clients.
                    <br>
                    At Stonepath Estates, we understand that finding the right property is a significant decision, and we’re here to guide you every step of the way. Whether you're looking to buy your dream home, sell a property, or invest in real estate, our team is dedicated to ensuring a smooth and rewarding experience.
                    <br>
                    Our expertise spans across residential, commercial, and luxury properties. We know the local market inside and out, and we’re committed to helping you find a property that matches your needs and goals. Our core values are trust, integrity, and customer satisfaction. Every interaction with our clients is built on these principles, ensuring you feel confident and informed.
                    <br>
                    With Stonepath Estates, you’re not just a client – you’re part of the family. We’re here to make your real estate journey seamless, and we look forward to being a part of your success.
                </p>
                <a href="services.php" class="btn btn-danger">Get a home now</a>
            </div>
        </div>
    </section>

    <!-- SERVICES SECTION -->
    <section class="container my-5">
        <h3 class="text-center text-dark">SERVICES</h3>
        <div class="row text-center">
            <div class="col-md-4">
                <a href="services.php"><img src="images/h11.jpeg" class="img-fluid rounded" alt="Land"></a>
                <h4><a href="services.php" class="btn btn-danger mt-2">Buy a plot of land</a></h4>
            </div>
            <div class="col-md-4">
                <a href="services.php"><img src="images/h3.jpeg" class="img-fluid rounded" alt="Land"></a>
                <h4><a href="services.php" class="btn btn-danger mt-2">Buy a plot of land</a></h4>
            </div>
            <div class="col-md-4">
                <a href="services.php"><img src="images/h8.jpeg" class="img-fluid rounded" alt="Rent"></a>
                <h4><a href="services.php" class="btn btn-danger mt-2">Rent a house</a></h4>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="bg-dark text-light text-center py-4">
        <?php include './includes/footer.php'; ?>
    </footer>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
