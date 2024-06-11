<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Form</title>
    <!-- Bootstrap -->
    <link rel="stylesheet" href="bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "srisw";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);
// Check connection
if (!$conn) {
    die("Connection
    failed: " . mysqli_connect_error());
}
// create purchase_table
$sqlCreatePurchaseTable = " CREATE TABLE IF NOT EXISTS purchasetable ( id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,purchase_date DATE,party_name VARCHAR(255) NOT NULL,brand_name VARCHAR(255) NOT NULL,product_name VARCHAR(255) NOT NULL,product_rate VARCHAR(255) NOT NULL,product_qty VARCHAR(255) NOT NULL,product_amt VARCHAR(255) NOT NULL
     )";
if (mysqli_query($conn, $sqlCreatePurchaseTable)) {

    echo "Purchase table created successfully.<br>";
} else {
    echo "Error creating brands table: " . mysqli_error($conn);
}
?>

<body class="d-flex flex-column justify-content-center align-items-center">
    <h1>Purchase Form</h1>
    <form class="w-50" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <div class="form-group">
            <label for="date">Date:</label>
            <input class="form-control" type="date" name="date_picker" id="date">
        </div>
        <div class="form-group">
            <label for="parties">Parties:</label>
            <select class="form-control" name="parties_select" id="ourParties">
                <option selected value="">Select a party</option>
            </select>
        </div>
        <div class="form-group">
            <label for="brand">Brand:</label>
            <select class="form-control" name="brand_select" id="ourBrands">
                <option selected value="">Select a Brand</option>
            </select>
        </div>

        <div class="form-group">
            <label for="product">Product:</label>
            <select class="form-control" name="product_select" id="ourProducts">
                <option selected value="">Select a Product</option>
            </select>
        </div>
        <div class="form-group">
            <label for="rate">Rate:</label>
            <input placeholder="Product Rate" class="form-control" type="text" name="rate_product" id="rate">
        </div>
        <div class="form-group">
            <label for="quantity">Quantity:</label>
            <input placeholder="Product Qty" class="form-control" type="text" name="quantity_product" id="quantity">
        </div>
        <div class="form-group">
            <label for="amount">Amount:</label>
            <input placeholder="Product Amt" class="form-control" type="text" name="amount_product" id="amount">
        </div>
        <!-- add button -->
        <button class="btn btn-outline-success w-50 mt-3 d-block mx-auto" type="button">Add</button>

    </form>

    </div>

    </form>
    <!-- JS -->
    <script type="module" src="bootstrap.bundle.min.js"></script>

</body>

</html>