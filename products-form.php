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

// Error Handling
$productNameError = $brandError = $rateRangeError = $selectedRateError = "";

// Fetch product data for update if update_id is set
$update_id = "";
$update_product_name = "";
$update_brand = "";
$update_rate_range = "";
$update_selected_rate = "";

if (isset($_REQUEST['update_id'])) {
    $update_id = $_REQUEST['update_id'];
    $query = "SELECT * FROM products WHERE id='" . $update_id . "'";
    $result = $conn->query($query);
    if ($result) {
        foreach ($result as $row) {
            $update_product_name = $row['product_name'];
            $update_brand = $row['brand'];
            $update_rate_range = $row['rate_range'];
            $update_selected_rate = $row['selected_rate'];
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $isValid = true;

    // Sanitize and validate product name
    if (empty($_POST['product_name'])) {
        $productNameError = "Product name is required.";
        $isValid = false;
    } else {
        // Sanitize product name by removing invalid characters
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
    } else {
        $selectedRate = (int) $_POST['selected_rate'];
        $rateRangeParts = explode("-", $_POST['rate_range']);
        $rateRangeMin = (int) str_replace(",", "", $rateRangeParts[0]);
        $rateRangeMax = (int) str_replace(",", "", $rateRangeParts[1]);

        // Validate selected rate against rate range
        if ($selectedRate < $rateRangeMin || $selectedRate > $rateRangeMax) {
            $selectedRateError = "Selected rate out of range.";
            $isValid = false;
        }
    }

    if ($isValid) {
        $productName = mysqli_real_escape_string($conn, $_POST['product_name']);
        $brand = mysqli_real_escape_string($conn, $_POST['brand']);
        $rateRange = mysqli_real_escape_string($conn, $_POST['rate_range']);
        // debugging line
        echo "<script>console.log('Update ID: $update_id');</script>";

        if (!empty($update_id)) {
            // If update_id is set, perform an update operation
            $sqlUpdate = "UPDATE products SET product_name='$productName', brand='$brand', rate_range='$rateRange', selected_rate=$selectedRate WHERE id=$update_id";

            if (mysqli_query($conn, $sqlUpdate)) {
                echo "<script>alert('Record updated successfully.');</script>";
            } else {
                echo "Error: " . $sqlUpdate . "<br>" . mysqli_error($conn);
            }
        } else {
            // Otherwise, perform an insert operation
            $sqlInsert = "INSERT INTO products (product_name, brand, rate_range, selected_rate) 
                      VALUES ('$productName', '$brand', '$rateRange', $selectedRate)";

            if (mysqli_query($conn, $sqlInsert)) {
                echo "<script>
            alert('New record created successfully.');
            setTimeout(function() {
                window.location.href = 'products-form.php';
            }, 3000);
          </script>";
            } else {
                echo "Error: " . $sqlInsert . "<br>" . mysqli_error($conn);
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
        $brandName = $row['brand_name'];
        $isSelected = ($update_brand == $brandName) ? "selected" : "";
        $brandOptions .= "<option value='$brandName' $isSelected>$brandName</option>";
    }
}
// Fetch rate range data from products table
$sqlRateRanges = "SELECT rate_range FROM products";
$resultRateRanges = mysqli_query($conn, $sqlRateRanges);
$rateRangeOptions = "";

if (mysqli_num_rows($resultRateRanges) > 0) {
    while ($row = mysqli_fetch_assoc($resultRateRanges)) {
        $rateRange = $row['rate_range'];
        $isSelected = ($update_rate_range == $rateRange) ? "selected" : "";
        $rateRangeOptions .= "<option value='$rateRange' $isSelected>$rateRange</option>";
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
            <input type="hidden" name="update_id" value="<?php echo $update_id; ?>">
        </div>
        <div class="form-group">
            <label for="product-name">Product Name:</label>
            <input
                value="<?php echo isset($_POST['product_name']) ? htmlspecialchars($_POST['product_name']) : ''; ?><?php echo $update_product_name; ?>"
                type="text" id="product-name" name="product_name" class="form-control">
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
                <option value="10,000-50,000" <?php if ($update_rate_range == '10,000-50,000')
                    echo 'selected'; ?>>
                    10,000-50,000</option>
                <option value="50,000-100,000" <?php if ($update_rate_range == '50,000-100,000')
                    echo 'selected'; ?>>
                    50,000-100,000</option>
                <option value="100,000-500,000" <?php if ($update_rate_range == '100,000-500,000')
                    echo 'selected'; ?>>
                    100,000-500,000</option>
            </select>
            <?php if (!empty($rateRangeError)): ?>
                <div class="alert alert-danger mt-2"><?php echo $rateRangeError; ?></div>
            <?php endif; ?>
        </div>

        <br>
        <div class="form-group">
            <label for="selected-rate">Selected Rate:</label>
            <input
                value="<?php echo isset($_POST['selected_rate']) ? htmlspecialchars($_POST['selected_rate']) : ''; ?><?php echo $update_selected_rate; ?>"
                type="number" id="selected-rate" name="selected_rate" class="form-control">
            <?php if (!empty($selectedRateError)): ?>
                <div class="alert alert-danger mt-2"><?php echo $selectedRateError; ?></div>
            <?php endif; ?>
        </div>
        <br>
        <input class="btn btn-primary" type="submit" value="Submit">
        <a target="_blank" class="btn btn-dark" href="products-table.php">Go to Table</a>
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