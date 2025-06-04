<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap CSS CDN -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script defer src="script.js"></script>
    <title>Registration Form</title>
</head>
<body>
    <!-- Include Navigation -->
    <?php include './includes/nav.php'; ?>

    <div id="registration_form" class="container mt-5" style="margin-bottom: 40px;">
        <h2 class="text-center mb-4">Registration Form</h2>
        <div class="row justify-content-center">
            <div class="col-md-8">
                <form action="property_reg.php" method="POST">
                    <div class="form-group">
                        <label for="first_name">First Name:</label>
                        <input type="text" id="first_name" name="first_name" class="form-control" placeholder="Michael" required>
                    </div>
                    <div class="form-group">
                        <label for="last_name">Last Name:</label>
                        <input type="text" id="last_name" name="last_name" class="form-control" placeholder="Katwe" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" class="form-control" placeholder="Katwemichael@example.com" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Telephone:</label>
                        <input type="tel" id="phone" name="phone" class="form-control" placeholder="+256770456789" required>
                    </div>
                    <div class="form-group">
                        <label class="mr-2">Gender:</label>
                        <div class="form-check form-check-inline">
                            <input type="radio" id="male" name="gender" value="Male" class="form-check-input" required>
                            <label for="male" class="form-check-label">Male</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="radio" id="female" name="gender" value="Female" class="form-check-input" required>
                            <label for="female" class="form-check-label">Female</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="property_location">Property Location:</label>
                        <input type="text" id="property_location" name="property_location" class="form-control" placeholder="Kampala" required>
                    </div>
                    <div class="form-group">
                        <label for="property_id">Property ID:</label>
                        <input type="text" id="property_id" name="property_id" class="form-control" placeholder="P_id001" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Submit</button>
                </form>
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
