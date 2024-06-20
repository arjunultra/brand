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
            $edit_product_qty = $row['product_quantity'];

        }
    }
}
// post method starts
// variables creation
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $date = ($_POST['purchase_date']);
    $partyName = ($_POST["parties_select"]);
    $brandName = ($_POST["brands_name"]);
    $productName = ($_POST["products_name"]);
    $pdtRate = ($_POST['products_rate']);
    $pdtQuantity = ($_POST['products_quantity']);
    $pdtAmount = ($_POST['products_amount']);

    // Validate Date
    if (empty($date) || (!preg_match("/^(\d{4})-(\d{2})-(\d{2})$/", $date))) {
        $dateValid = "is-invalid";
    } else {
        filter_input(INPUT_POST, 'date_picker', FILTER_SANITIZE_STRING);
        $dateValid = "is-valid";
    }
    // Validate party name
    if (empty($partyName) || !preg_match("/^[A-Za-z]+([ '-][A-Za-z]+)*$/", $partyName)) {
        $partyNameValid = "is-invalid";
    } else {
        $partyNameValid = "is-valid";
    }

    // Validate brand name
    if (isset($brandName) && is_array($brandName)) {
        foreach ($brandName as $brandItem) {
            if (!empty($brandItem) || (preg_match("/^[A-Za-z0-9]+$/", $brandItem))) {
                $brandNameValid = "is-valid";
            }
        }
    } else {
        $brandNameValid = "is-invalid";
    }
    print_r($brandName);
    print_r($brandNameValid);

    // Validate product name
    if (isset($productName) && is_array($productName)) {
        foreach ($productName as $productItem) {
            if (!empty($productItem) || (preg_match("/^[A-Za-z0-9]+$/", $productItem))) {
                $productNameValid = "is-valid";
            }
        }
    } else {
        $productNameValid = "is-invalid";
    }

    // Validate Rate
    if (isset($pdtRate) && is_array($pdtRate)) {
        foreach ($pdtRate as $rateItem) {
            if (!empty($rateItem) || (preg_match("/^-?\d+(\.\d+)?$/", $rateItem))) {
                $rateValid = "is-valid";
            }
        }
    } else {
        $rateValid = "is-invalid";
    }
    // Validate Qty
    if (isset($pdtQuantity) && is_array($pdtQuantity)) {
        foreach ($pdtQuantity as $quantityItem) {
            if (!empty($quantityItem) || (preg_match("/^-?\d+(\.\d+)?$/", $quantityItem))) {
                $qtyValid = "is-valid";
            }
        }
    } else {
        $qtyValid = "is-invalid";
    }
    // Validate Amount
    if (isset($pdtAmount) && is_array($pdtAmount)) {
        foreach ($pdtAmount as $amountItem) {
            if (!empty($amountItem) || (preg_match("/^-?\d+(\.\d+)?$/", $amountItem))) {
                $amountValid = "is-valid";
            }
        }
    } else {
        $amountValid = "is-invalid";
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
            //  check variables are not arrays before insert
            // Function to handle array to string conversion
            function ensure_string($var)
            {
                if (is_array($var)) {
                    return implode(", ", $var);
                }
                return trim($var);  // Ensuring the variable is a string and trimming it
            }

            // Ensuring all variables are strings
            $dateStr = ensure_string($date);
            $partyNameStr = ensure_string($partyName);
            $brandNameStr = ensure_string($brandName);
            $productNameStr = ensure_string($productName);
            $pdtRateStr = ensure_string($pdtRate);
            $pdtQuantityStr = ensure_string($pdtQuantity);
            $pdtAmountStr = ensure_string($pdtAmount);

            if (mysqli_query($conn, $sqlCreatePurchaseTable)) {
                // Inserting new data
                $query = "INSERT INTO purchasetable (purchase_date, party_name, brand_name, product_name, product_rate, product_qty, product_amt) 
VALUES ('$dateStr', '$partyNameStr', '$brandNameStr', '$productNameStr', '$pdtRateStr', '$pdtQuantityStr', '$pdtAmountStr')";
                if (mysqli_query($conn, $query)) {
                    // Successful insert
                    echo "Data inserted successfully.";
                } else {
                    // Error handling
                    echo "Error: " . mysqli_error($conn);
                }
            }

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
    <form id="purchase-form" class="w-50" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
        <div class="form-group">
            <label for="date">Date:</label>
            <input class="form-control" type="date" name="purchase_date" id="purchase-date">
            <!-- display validation feedback -->
            <?php if ($_SERVER["REQUEST_METHOD"] == "POST" && $dateValid == "is-invalid"): ?>
                <div class="alert alert-danger">Please enter a valid date.</div>
            <?php endif; ?>
        </div>
        <div class="row gap-0" id="select-container">
            <div class="form-group col-12 col-lg-4">
                <label for="parties">Parties:</label>
                <input type="hidden" name="party_name[]" id="party-name">
                <select class="w-100" name="parties_select" id="parties-select" onchange="getBrands(this.value)">
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
                <input type="hidden" name="brands_name" id="brand-name">
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
                <input type="hidden" name="product_name" id="product-name">
                <div id="product-select-container">
                    <select class="w-100" name="products_select" id="product-select">
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
            <div id="pdt-rate-container"><input placeholder="Product Rate" class="form-control" type="text"
                    name="product_rate" id="product-rate" value=""></div>
            <!-- display validation feedback -->
            <?php if ($_SERVER["REQUEST_METHOD"] == "POST" && $rateValid == "is-invalid"): ?>
                <div class="alert alert-danger">Please enter a valid product rate !</div>
            <?php endif; ?>
        </div>
        <div class="form-group">
            <label for="quantity">Quantity:</label>
            <input onkeyup="calculateAmount()" placeholder="Product Qty" class="form-control" type="text"
                name="product_quantity" id="product-quantity">
            <!-- display validation feedback -->
            <?php if ($_SERVER["REQUEST_METHOD"] == "POST" && $qtyValid == "is-invalid"): ?>
                <div class="alert alert-danger">Please enter a valid product quantity !</div>
            <?php endif; ?>
        </div>
        <div class="form-group">
            <label for="amount">Amount:</label>
            <input placeholder="Product Amount" class="form-control" type="text" name="product_amount"
                id="product-amount">
            <!-- display validation feedback -->
            <?php if ($_SERVER["REQUEST_METHOD"] == "POST" && $amountValid == "is-invalid"): ?>
                <div class="alert alert-danger">This field cannot be empty!</div>
            <?php endif; ?>
        </div>
        <!-- add button -->
        <button class="btn btn-outline-warning text-uppercase w-50 mt-3 d-block mx-auto" id="addrow-btn"
            type="button">Add</button>
        <!-- table -->
        <div class="container mt-5">
            <h2>Party Order Details</h2>
            <div id="productTable" class="table-responsive">
                <table id="purchase-table" class="table table-striped table-hover table-bordered">
                    <input type="hidden" id="row_count" name="row_count" value="0">
                    <thead class="table-dark bg-primary">
                        <tr>
                            <th>S.No</th>
                            <th>Brand Name</th>
                            <th>Product Name</th>
                            <th>Product Rate</th>
                            <th>Product Quantity</th>
                            <th>Grand Total</th>
                            <th>Function</th>
                        </tr>
                    </thead>
                    <tbody id="table-body">
                    </tbody>
                    <tfoot>
                        <td colspan="4" class="text-center">Subtotal</td>
                        <td>value</td>
                        <td id="sub-total"></td>
                    </tfoot>
                </table>
            </div>
        </div>
        <button class="btn btn-danger d-block w-50 mx-auto mt-3" type="submit" name="submit">Submit</button>

    </form>
    <?php
    mysqli_close($conn);
    ?>
    <!-- JQuery -->
    <script src="./JS/jquery-3.7.1.min.js"></script>
    <!-- JS -->
    <script type="text/javascript" src="JS/bootstrap.bundle.min.js"></script>
    <script>
        const addrowBtn = document.getElementById('addrow-btn')
        let pdtAmount = document.getElementById('product-amount');
        function getBrands(party_id) {
            let post_url = "purchase_form_changes.php?selected_party=" + party_id;
            fetchAndDisplay(post_url, "#brand-select-container");
        }
        function getProducts(brand_id) {

            let post_url = "purchase_form_changes.php?&selected_brands=" + brand_id;
            fetchAndDisplay(post_url, "#product-select-container");
        }
        function fetchProductRate(product_id) {
            let post_url = "purchase_form_changes.php?&selected_product=" + product_id;
            fetchAndDisplay(post_url, "#pdt-rate-container");
        }
        function calculateAmount(qty) {

            let pdtQty = document.getElementById('product-quantity');
            qty = pdtQty.value;
            let pdtRate = document.getElementById('product-rate');

            let totalAmount = pdtRate.value * pdtQty.value;
            pdtAmount.value = totalAmount;

        }
        function calculateSubtotal() {
            let amount = 0; let totalAmount = 0;
            if ($('.product-row').length > 0) {
                $('.product-row').find('.amount').each(function () {
                    amount = $(this).html();
                    totalAmount = parseInt(amount) + parseInt(totalAmount);
                });
            }
            if (totalAmount != 0 && totalAmount != "") {
                if ($('#sub-total').length > 0) {
                    $('#sub-total').html(totalAmount);
                }
            }
        }
        function DeleteRow(row_index) {
            if (row_index != "") {
                if ($('.row' + row_index).length) {
                    $('.row' + row_index).remove();
                }
            }

        }



    </script>
    <script src="./JS/filterProductsAjax.js"></script>
    <script src="./JS/purchase-form-table-ajax.js"></script>
    <script src="./JS/calculateSubtotal.js"></script>
</body>

</html>