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

// Create products table if not exists
$sqlCreateProducts = "CREATE TABLE IF NOT EXISTS products (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_name VARCHAR(255) NOT NULL,
    brand VARCHAR(255) NOT NULL,
    rate_range VARCHAR(255) NOT NULL,
    selected_rate BIGINT(20) NOT NULL
)";

if (mysqli_query($conn, $sqlCreateProducts)) {
    if (mysqli_affected_rows($conn) > 0) {
        echo "Products table created successfully.<br>";
    }
} else {
    echo "Error creating products table: " . mysqli_error($conn);
}
//  Error Handling
$productNameError = $brandError = $rateRangeError = $selectedRateError = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $isValid = true;

    // Sanitize and validate product name
    if (empty($_POST['product_name'])) {
        $productNameError = "Product name is required.";
        $isValid = false;
    } else {
        // Sanitize product name by removing harmful characters
        $productName = filter_input(INPUT_POST, 'product_name', FILTER_SANITIZE_STRING);

        // Validate product name for allowed characters
        if (!preg_match("/^[a-zA-Z0-9 \-_]+$/", $productName)) {
            $productNameError = "Product name contains invalid characters.";
            $isValid = false;
        }
    }

    if (empty($_POST['brand']) || $_POST['brand'] == "") {
        $brandError = "Brand is required.";
        $isValid = false;
    }

    if (empty($_POST['rate_range']) || $_POST['rate_range'] == "") {
        $rateRangeError = "Rate range is required.";
        $isValid = false;
    }

    if (!isset($_POST['selected_rate']) || trim($_POST['selected_rate']) === '') {
        $selectedRateError = "Selected rate is required.";
        $isValid = false;
    }

    if ($isValid) {
        // Assuming you're submitting data to this same PHP script
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['product_name'])) {
            // Insert new product data from form
            $productName = mysqli_real_escape_string($conn, $_POST['product_name']);
            $brand = mysqli_real_escape_string($conn, $_POST['brand']);
            $rateRange = mysqli_real_escape_string($conn, $_POST['rate_range']);
            $selectedRate = (int) $_POST['selected_rate'];
            $rateRangeParts = explode("-", $rateRange);
            $rateRangeMin = (int) str_replace(",", "", $rateRangeParts[0]);
            $rateRangeMax = (int) str_replace(",", "", $rateRangeParts[1]);
            $selectedRateValid = true;

            // Validate selected rate against rate range
            if ($selectedRate < $rateRangeMin || $selectedRate > $rateRangeMax) {
                $selectedRateValid = false;
            }

            $sqlInsert = "INSERT INTO products (product_name, brand, rate_range, selected_rate) 
                  VALUES ('$productName', '$brand', '$rateRange', $selectedRate)";

            if (mysqli_query($conn, $sqlInsert) && $selectedRateValid) {
                echo "New record created successfully.<br>";
            } else {
                if (!$selectedRateValid) {
                    echo "Error: Selected rate out of range.<br>";
                } else {
                    echo "Error: " . $sqlInsert . "<br>" . mysqli_error($conn);
                }
            }
        }
    }
}



// Fetch brands data from brands table
$sqlBrands = "SELECT * FROM brands";
$resultBrands = mysqli_query($conn, $sqlBrands);
$brandOptions = "";

if (mysqli_num_rows($resultBrands) > 0) {
    while ($row = mysqli_fetch_assoc($resultBrands)) {
        $brandId = $row['id'];
        $brandName = $row['brand'];
        $brandOptions .= "<option value='$brandName'>$brandName</option>";
    }
}

mysqli_close($conn);
?>




<!DOCTYPE html>
<html>

<head>
    <title>Product Entry Form</title>
    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <h1>Enter Product Details</h1>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" class="form w-100 text-center">
        <div class="form-group">
            <label for="product-name">Product Name:</label>
            <input type="text" id="product-name" name="product_name" class="form-control">
            <?php if (!empty($productNameError)): ?>
                <div class="alert alert-danger mt-2"><?php echo $productNameError; ?></div>
            <?php endif; ?>
        </div>
        <br>
        <div class="form-group">
            <label for="brand">Brand:</label>
            <select id="brand" name="brand" class="form-control">
                <option value="">Select a brand</option>
                <?php echo $brandOptions; ?>
            </select>
            <?php if (!empty($brandError)): ?>
                <div class="alert alert-danger mt-2"><?php echo $brandError; ?></div>
            <?php endif; ?>
        </div>
        <br>
        <div class="form-group">
            <label for="rate-range">Rate Range:</label>
            <select id="rate-range" name="rate_range" class="form-control">
                <option value="">Select a rate range</option>
                <option value="10,000-50,000">10,000-50,000</option>
                <option value="50,000-100,000">50,000-100,000</option>
                <option value="100,000-500,000">100,000-500,000</option>
            </select>
            <?php if (!empty($rateRangeError)): ?>
                <div class="alert alert-danger mt-2"><?php echo $rateRangeError; ?></div>
            <?php endif; ?>
        </div>
        <br>
        <div class="form-group">
            <label for="selected-rate">Selected Rate:</label>
            <input type="number" id="selected-rate" name="selected_rate" class="form-control">
            <?php if (!empty($selectedRateError)): ?>
                <div class="alert alert-danger mt-2"><?php echo $selectedRateError; ?></div>
            <?php endif; ?>
        </div>
        <br>
        <input class="btn btn-primary" type="submit" value="Submit">
    </form>

    <!-- Display selected rate and rate range -->
    <?php if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['product_name']) && isset($_POST['rate_range']) && isset($_POST['selected_rate'])): ?>
        <?php
        $rateRange = $_POST['rate_range'];
        $selectedRate = $_POST['selected_rate'];
        $rateRangeParts = explode("-", $rateRange);
        if (count($rateRangeParts) == 2) {
            $rateRangeMin = str_replace(",", "", $rateRangeParts[0]);
            $rateRangeMax = str_replace(",", "", $rateRangeParts[1]);
        } else {
            // Handle incorrect format or provide default min and max values
            $rateRangeMin = 0; // Default minimum
            $rateRangeMax = 0; // Default maximum
            // Optionally, add error handling or user feedback for incorrect format
        }
        ?>
        <h2>Selected Details:</h2>
        <p>Rate Range: <?php echo htmlspecialchars($rateRangeMin); ?> - <?php echo htmlspecialchars($rateRangeMax); ?></p>
        <p>Selected Rate: <?php echo htmlspecialchars($selectedRate); ?></p>
    <?php endif; ?>
</body>

</html>