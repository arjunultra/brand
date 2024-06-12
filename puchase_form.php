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
// validation variables
$dateValid = $rateValid = $qtyValid = $amountValid = "";
// update variables
$edit_date = "";
$edit_party_name = "";
$edit_brand_name = "";
$edit_product_name = "";
$edit_product_rate = "";
$edit_product_qty = "";
$update_id = "";
// Function to redirect to the given URL
function redirect($url)
{
    header('Location: ' . $url);
    exit();
}
// update operation
if (isset($_REQUEST['update_id'])) {
    $update_id = $_REQUEST['update_id'];
    $query = "SELECT * FROM purchasetable WHERE id='" . $update_id . "'";
    $result = $conn->query($query);
    if ($result) {
        foreach ($result as $row) {
            $update_id = $row['id'];
            $edit_date = $row['date_picker'];
            $edit_party_name = $row['parties_select'];
            $edit_brand_name = $row['brand_select'];
            $edit_product_name = $row['product_select'];
            $edit_product_rate = $row['rate_product'];
            $edit_product_qty = $row['quantity_product'];

        }
    }
}
// post method starts
// variables creation
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $date = trim($_POST['date_picker']);
    $pdtRate = trim($_POST['rate_product']);
    $pdtQuantity = trim($_POST['quantity_product']);
    $pdtAmount = trim($_POST['amount_product']);

    // Validate Date
    if (empty($date) || (!preg_match("/^(\d{4})-(\d{2})-(\d{2})$/", $date))) {
        $dateValid = "is-invalid";
    } else {
        filter_input(INPUT_POST, 'date_picker', FILTER_SANITIZE_STRING);
        $dateValid = "is-valid";
    }

    // Validate Rate
    if (empty($pdtRate) || !preg_match("/^-?\d+(\.\d+)?$/", $pdtRate)) {
        $rateValid = "is-invalid";
    } else {
        $rateValid = "is-valid";
    }

    // Validate Qty
    if (empty($pdtQuantity) || !preg_match("/^\d+$/", $pdtQuantity)) {
        $qtyValid = "is-invalid";
    } else {
        $qtyValid = "is-valid";
    }
    // Validate Amount
    if (empty($pdtAmount) || !preg_match("/^-?\d+(\.\d+)?$/", $pdtAmount)) {
        $amountValid = "is-invalid";
    } else {
        $amountValid = "is-valid";
    }

    if ($nameValid == "is-invalid" || $emailValid == "is-invalid" || $ageValid == "is-invalid") {
        echo "<script>alert('All fields are required and must be valid.')</script>";
    } else {
        // Check if it's an update operation
        if (isset($_POST['update_id']) && !empty($_POST['update_id'])) {
            $update_id = $_POST['update_id'];
            $stmt = mysqli_prepare($conn, "UPDATE feedback_form SET name=?, email=?, age=? WHERE id=?");
            mysqli_stmt_bind_param($stmt, "ssii", $name, $email, $age, $update_id);
            header("location:feedback_table.php");
        } else {
            // Attempt to create table only if it doesn't exist
            $sqlCreatePurchaseTable = " CREATE TABLE IF NOT EXISTS purchasetable ( id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,purchase_date DATE,party_name VARCHAR(255) NOT NULL,brand_name VARCHAR(255) NOT NULL,product_name VARCHAR(255) NOT NULL,product_rate VARCHAR(255) NOT NULL,product_qty VARCHAR(255) NOT NULL,product_amt VARCHAR(255) NOT NULL
     )";

            if (mysqli_query($conn, $sqlCreatePurchaseTable)) {
                // Inserting new data
                $stmt = mysqli_prepare($conn, "INSERT INTO purchasetable (purchase_date, party_name,brand_name,product_name,product_rate,product_qty,product_amt) VALUES (?, ?, ?, ?, ?, ?, ?)");
                mysqli_stmt_bind_param($stmt, "sssssss", $date, $partyName, $brandName, $productName, $productRate, $productQty, $productAmt);

            } else {
                echo "Error creating table: " . mysqli_error($conn);
            }
        }
        // Execute and close statement
        if (!mysqli_stmt_execute($stmt)) {
            echo "Error: " . mysqli_stmt_error($stmt);
        } else {
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
        }
    }
}
// Retrieve brands
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
// Retrieve Products
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
// Retrieve Party
$sqlParties = "SELECT * FROM partyorder";
$resultParties = mysqli_query($conn, $sqlParties);
$partiesOptions = "";

if (mysqli_num_rows($resultParties) > 0) {
    while ($row = mysqli_fetch_assoc($resultParties)) {
        $partiesId = $row['id'];
        $partiesName = $row['party_name'];
        $partiesOptions .= "<option value='$partiesName'>$partiesName</option>";
    }
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
                <?php echo $partiesOptions; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="brand">Brand:</label>
            <select class="form-control" name="brand_select" id="ourBrands">
                <option selected value="">Select a Brand</option>
                <?php echo $brandOptions; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="product">Product:</label>
            <select class="form-control" name="product_select" id="ourProducts">
                <option selected value="">Select a Product</option>
                <?php echo $productOptions; ?>
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
        <button class="btn btn-outline-warning text-uppercase w-50 mt-3 d-block mx-auto" type="button">Add</button>

    </form>

    </div>

    </form>
    <!-- JS -->
    <script type="module" src="bootstrap.bundle.min.js"></script>

</body>

</html>