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
$dateValid = $partyNameValid = $brandNameValid = $productNameValid = $rateValid = $qtyValid = $amountValid = "";
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
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $date = trim($_POST['date_picker']);
    $partyName = $_POST["parties_select"];
    $brandName = trim($_POST["brand_select"]);
    $productName = trim($_POST["product_select"]);
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
    // Validate party name
    if (empty($partyName)) {
        $partyNameValid = "is-invalid";
    } else {
        $partyName = $_POST["parties_select"];
    }

    // Validate brand name
    if (empty($brandName)) {
        $brandNameValid = "is-invalid";
    } else {
        $brandNameValid = "is-valid";
    }
    // Validate product name
    if (empty($productName)) {
        $productNameValid = "is-invalid";
    } else {
        $productNameValid = "is-valid";
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

    if ($dateValid == "is-invalid" || $partyNameValid == "is-invalid" || $brandNameValid == "is-invalid" || $productNameValid == "is-invalid" || $rateValid == "is-invalid" || $qtyValid == "is-invalid" || $amountValid == "is-invalid") {
        echo "<script>alert('All fields are required and must be valid.')</script>";
    } else {
        // Check if it's an update operation
        if (isset($_POST['update_id']) && !empty($_POST['update_id'])) {
            $update_id = $_POST['update_id'];
            $stmt = mysqli_prepare($conn, "UPDATE feedback_form SET name=?, email=?, age=? WHERE id=?");
            mysqli_stmt_bind_param($stmt, "ssii", $name, $email, $age, $update_id);
            // header("location:feedback_table.php");
        } else {
            // Attempt to create table only if it doesn't exist
            $sqlCreatePurchaseTable = " CREATE TABLE IF NOT EXISTS purchasetable ( id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,purchase_date DATE,party_name VARCHAR(255) NOT NULL,brand_name VARCHAR(255) NOT NULL,product_name VARCHAR(255) NOT NULL,product_rate VARCHAR(255) NOT NULL,product_qty VARCHAR(255) NOT NULL,product_amt VARCHAR(255) NOT NULL
     )";

            if (mysqli_query($conn, $sqlCreatePurchaseTable)) {
                // Inserting new data
                $stmt = mysqli_prepare($conn, "INSERT INTO purchasetable (purchase_date, party_name,brand_name,product_name,product_rate,product_qty,product_amt) VALUES (?, ?, ?, ?, ?, ?, ?)");
                mysqli_stmt_bind_param($stmt, "sssssss", $date, $partyName, $brandName, $productName, $pdtRate, $pdtQuantity, $pdtAmount);

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
    <h1>Purchase <span id="main-title-span">Form</span></h1>
    <form class="w-50" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
        <div class="form-group">
            <label for="date">Date:</label>
            <input class="form-control" type="date" name="date_picker" id="date">
            <!-- display validation feedback -->
            <?php if ($_SERVER["REQUEST_METHOD"] == "POST" && $dateValid == "is-invalid"): ?>
                <div class="alert alert-danger">Please enter a valid date.</div>
            <?php endif; ?>
        </div>
        <div class="row gap-0" id="select-container">
            <div class="form-group col-12 col-lg-4">
                <label for="parties">Parties:</label>
                <input type="hidden" name="party_name[]" id="party-name">
                <select class="w-100" name="parties_select" id="ourParties" onchange="getBrands(this.value)">
                    <option selected value="">Select a party</option>
                    <?php echo $partiesOptions; ?>
                </select>
                <!-- display validation feedback -->
                <?php if ($_SERVER["REQUEST_METHOD"] == "POST" && $partyNameValid == "is-invalid"): ?>
                    <div class="alert alert-danger">Please select a party !</div>
                <?php endif; ?>
            </div>
            <div class="form-group col-12 col-lg-4" id="brand-container">
                <label for="brand">Brand:</label>
                <input type="hidden" name="brand_name[]" id="brand-name">
                <div id="brand-select-container">
                    <select class="w-100" name="brand_select" id="brand-select" onchange="getProducts(this.value)">
                        <option selected value="">Select a Brand</option>
                        <?php echo $brandOptions; ?>
                    </select>
                </div>
                <!-- display validation feedback -->
                <?php if ($_SERVER["REQUEST_METHOD"] == "POST" && $brandNameValid == "is-invalid"): ?>
                    <div class="alert alert-danger">Please select a brand !</div>
                <?php endif; ?>
            </div>

            <div class="form-group col-12 col-lg-4" id="product-container">
                <label for="product">Product:</label>
                <input type="hidden" name="product_name[]" id="product-name">
                <div id="product-select-container">
                    <select class="w-100" name="products_select" id="products-select">
                        <option selected value="">Select a Product</option>
                        <?php echo $productOptions; ?>
                    </select>
                </div>
                <!-- display validation feedback -->
                <?php if ($_SERVER["REQUEST_METHOD"] == "POST" && $productNameValid == "is-invalid"): ?>
                    <div class="alert alert-danger">Please select a product !</div>
                <?php endif; ?>
            </div>
        </div>
        <div class="form-group col">
            <label for="rate">Rate:</label>
            <input placeholder="Product Rate" class="form-control" type="text" name="rate_product" id="rate">
            <!-- display validation feedback -->
            <?php if ($_SERVER["REQUEST_METHOD"] == "POST" && $rateValid == "is-invalid"): ?>
                <div class="alert alert-danger">Please enter a valid product rate !</div>
            <?php endif; ?>
        </div>
        <div class="form-group">
            <label for="quantity">Quantity:</label>
            <input placeholder="Product Qty" class="form-control" type="text" name="quantity_product" id="quantity">
            <!-- display validation feedback -->
            <?php if ($_SERVER["REQUEST_METHOD"] == "POST" && $qtyValid == "is-invalid"): ?>
                <div class="alert alert-danger">Please enter a valid product quantity !</div>
            <?php endif; ?>
        </div>
        <div class="form-group">
            <label for="amount">Amount:</label>
            <input placeholder="Product Amt" class="form-control" type="text" name="amount_product" id="amount">
            <!-- display validation feedback -->
            <?php if ($_SERVER["REQUEST_METHOD"] == "POST" && $amountValid == "is-invalid"): ?>
                <div class="alert alert-danger">This field cannot be empty!</div>
            <?php endif; ?>
        </div>
        <!-- add button -->
        <button class="btn btn-outline-warning text-uppercase w-50 mt-3 d-block mx-auto" type="button">Add</button>
        <button class="btn btn-danger d-block w-50 mx-auto mt-3" type="submit" name="submit">Submit</button>

    </form>

    </div>

    </form>
    <!-- JQuery -->
    <script src="./JS/jquery-3.7.1.min.js"></script>
    <!-- JS -->
    <script type="module" src="JS/bootstrap.bundle.min.js"></script>
    <script>
        function getBrands(party_id) {
            let post_url = "purchase_form_changes.php?selected_party=" + party_id;
            fetchAndDisplay(post_url, "#brand-select-container");
        }
        function getProducts(brand_id) {

            let post_url = "purchase_form_changes.php?&selected_brand=" + brand_id;
            fetchAndDisplay(post_url, "#product-select-container");
        }
    </script>
    <script src="./JS/filterProductsAjax.js"></script>

</body>

</html>