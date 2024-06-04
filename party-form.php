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
$sql = "CREATE TABLE IF NOT EXISTS partyorder (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    party_name VARCHAR(255)NOT NULL,
    party_mobile BIGINT NOT NULL,
    brand VARCHAR(255)NOT NULL,
    product VARCHAR(255)NOT NULL
)";

if (mysqli_query($conn, $sql)) {
    echo "Table partyorder created successfully";
} else {
    echo "Error creating table: " . mysqli_error($conn);
}
// Retrieve brands
// Fetch brands data from brands table
$sqlBrands = "SELECT * FROM brands";
$resultBrands = mysqli_query($conn, $sqlBrands);
$brandOptions = "";

if (mysqli_num_rows($resultBrands) > 0) {
    while ($row = mysqli_fetch_assoc($resultBrands)) {
        $brandId = $row['id'];
        $brandName = $row['brand_name'];
        $isSelected = ($brandName) ? "selected" : "";
        $brandOptions .= "<option value='" . $brandName . "'>" . $brandName . "</option>";
    }
}
$sqlProducts = "SELECT * FROM products";
$resultProducts = mysqli_query($conn, $sqlProducts);
$productOptions = "";

if (mysqli_num_rows($resultProducts) > 0) {
    while ($row = mysqli_fetch_assoc($resultProducts)) {
        $productId = $row['id'];
        $productName = $row['product_name'];
        $productOptions .= "<option value='$productName'>$productName</option>";
    }
}
// Close connection
mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Party Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <h1>Party Form</h1>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" class="form w-100 text-center">
        <div class="form-group">
            <label for="product-name">Party Name:</label>
            <input type="text" id="party-name" name="party_name" class="form-control">
            <label for="mobile">Mobile Number:</label>
            <input type="tel" id="mobile" name="mobile_number" class="form-control">
        </div>
        <div class="select-container row mt-5">
            <div id="brand-container" class="form-group col">
                <!-- <label for="brand">Brand:</label> -->
                <select id="brand-select" name="brand" class="mb-5" onchange="Javascript: getProducts();">
                    <option selected value="">Select a brand</option>
                    <?php echo $brandOptions; ?>
                </select>
            </div>
            <!-- <label for="product">Product:</label> -->
            <div class="col" id="product-container">
                <select id="product-select" name="product" class="">
                    <option selected value="">Select a Product</option>
                    <?php echo $productOptions; ?>
                </select>
            </div>
            <!-- Add Button -->
            <div class="btn-container mt-3 row"><a id="addProduct"
                    class=" product-add-btn align-self-center btn btn-success mb-5 ms-2 ms-md-0" href="#">ADD</a></div>
        </div>

        <!-- Table -->
        <div class="container mt-5">
            <h2>Party Order Details</h2>
            <div id="productTable" class="table-responsive">
                <table class="table table-striped table-hover table-bordered">
                    <thead class="table-dark bg-primary">
                        <tr>
                            <th>Order ID</th>
                            <th>Brand Name</th>
                            <th>Product Name</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>

            </div>
        </div>

    </form>
    <script>
        function getProducts() {
            let brand = "";
            if ($("#brand-select").length > 0) {
                brand = $("#brand-select").val();
            }

            var post_url = "party-form-changes.php?selected_brand=" + brand;

            jQuery.ajax({
                url: post_url, success: function (result) {
                    if (result != "") {
                        if ($("#product-container").length > 0) {
                            $("#product-container").html(result);
                        }
                    }
                }
            });
        }
    </script>
    <!-- JQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
</body>

</html>