<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "srisw";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Create brands table if not exists
$sqlCreateBrands = "CREATE TABLE IF NOT EXISTS brands (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    brand_name VARCHAR(255) NOT NULL
)";

if (mysqli_query($conn, $sqlCreateBrands)) {
    // Check if the table was actually created or it already existed
    if (mysqli_affected_rows($conn) > 0) {
        echo "Brands table created successfully.<br>";
    }
} else {
    echo "Error creating brands table: " . mysqli_error($conn);
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>Brand Entry Form</title>
    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <h1>Enter Brand Details</h1>
    <form method="POST" class="form w-100 text-center">
        <div class="form-group">
            <label for="brand-name">Brand Name:</label>
            <input type="text" id="brand-name" name="brand_name" class="form-control">
            <div class="brandname-error">
                <?php
                // Assuming you're submitting data to this same PHP script
                if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['brand_name'])) {
                    // Validate brand name
                    $brandName = mysqli_real_escape_string($conn, $_POST['brand_name']);

                    if (empty($brandName)) {
                        echo "<div class='mt-5 alert alert-danger' role='alert'>Brand name is required</div>";
                    } else if (preg_match('/\d/', $brandName)) {
                        echo "<div class='alert alert-danger mt-5' role='alert'>Brand name cannot contain numbers</div>";
                    } else {
                        // Insert new brand data from form
                        $sqlInsert = "INSERT INTO brands (brand) VALUES ('$brandName')";

                        if (mysqli_query($conn, $sqlInsert)) {
                            echo "<p class='text-bg-success p-2 mt-4'>New record created successfully.</p><br>";
                        } else {
                            echo "Error: " . $sqlInsert . "<br>" . mysqli_error($conn);
                        }
                    }
                }
                mysqli_close($conn);
                ?>

            </div>
        </div>
        <br>
        <input class="btn btn-primary" type="submit" value="Submit">
    </form>
</body>

</html>