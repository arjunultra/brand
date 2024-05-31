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
        $brandOptions .= "<option value='$brandName' $isSelected>$brandName</option>";
    }
}
$sqlProducts = "SELECT * FROM products";
$resultProducts = mysqli_query($conn, $sqlProducts);
$productOptions = "";

if (mysqli_num_rows($resultProducts) > 0) {
    while ($row = mysqli_fetch_assoc($resultProducts)) {
        $productId = $row['id'];
        $productName = $row['product_name'];
        $isSelected = ($productName) ? "selected" : "";
        $productOptions .= "<option value='$productName' $isSelected>$productName</option>";
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
        <div class="form-group mt-5">
            <!-- <label for="brand">Brand:</label> -->
            <select id="brand" name="brand" class="">
                <option value="">Select a brand</option>
                <?php echo $brandOptions; ?>
            </select>
            <!-- <label for="product">Product:</label> -->
            <select id="product" name="product" class="">
                <option value="">Select a Product</option>
                <?php echo $productOptions; ?>
            </select>
            <!-- Add Button -->
            <a id="addProduct" class="product-add-btn btn btn-success px-5 py-0 py-1" href="#">ADD</a>
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
    <script></script>
</body>

</html>