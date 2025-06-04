<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Add Bootstrap CSS CDN -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script defer src="script.js"></script>
    <meta content=" " description=" ">
    <title>Services</title>
</head>
<body>

<!-- Include Navigation -->
<?php include './includes/nav.php'; ?>

<!-- Services Heading -->
<h1 class="text-center my-4">Services</h1>

<!-- Services Section with Bootstrap grid -->
<div id="services" class="container">
    <div class="row">
        <!-- First row of services -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <img src="./assets/images/h3.jpeg" alt="image 1" class="card-img-top">
                <div class="card-body">
                    <a href="reg_form.php" class="btn btn-primary">Buy this property</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card">
                <img src="./assets/images/h2.jpeg" alt="image 2" class="card-img-top">
                <div class="card-body">
                    <a href="reg_form.php" class="btn btn-primary">Buy this property</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card">
                <img src="./assets/images/h4.jpeg" alt="image 3" class="card-img-top">
                <div class="card-body">
                    <a href="reg_form.php" class="btn btn-primary">Buy this property</a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <!-- Second row of services -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <img src="./assets/images/h5.jpeg" alt="image 4" class="card-img-top">
                <div class="card-body">
                    <a href="reg_form.php" class="btn btn-primary">Buy this property</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card">
                <img src="./assets/images/h6.jpeg" alt="image 5" class="card-img-top">
                <div class="card-body">
                    <a href="reg_form.php" class="btn btn-primary">Buy this property</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card">
                <img src="./assets/images/h9.jpeg" alt="image 6" class="card-img-top">
                <div class="card-body">
                    <a href="reg_form.php" class="btn btn-primary">Buy this property</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include Footer -->
<?php include './includes/footer.php'; ?>

<!-- Bootstrap JS and dependencies (Popper.js and jQuery) -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
