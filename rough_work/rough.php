<?php
include 'includes/db.php';
include 'includes/functions.php';

$dateValid = $partyNameValid = $brandNameValid = $productNameValid = $rateValid = $qtyValid = $amountValid = "";
$edit_date = $edit_party_name = $edit_brand_name = $edit_product_name = $edit_product_rate = $edit_product_qty = $update_id = "";

if (isset($_REQUEST['update_id'])) {
    $update_id = $_REQUEST['update_id'];
    $query = "SELECT * FROM purchasetable WHERE id='$update_id'";
    $result = $conn->query($query);
    if ($result) {
        foreach ($result as $row) {
            $update_id = $row['id'];
            $edit_date = $row['purchase_date'];
            $edit_party_name = $row['party_name'];
            $edit_brand_name = $row['brand_name'];
            $edit_product_name = $row['product_name'];
            $edit_product_rate = $row['product_rate'];
            $edit_product_qty = $row['product_qty'];
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $date = $_POST['purchase_date'];
    $partyName = $_POST["parties_select"];
    $brandName = $_POST["brands_name"];
    $productName = $_POST["products_name"];
    $pdtRate = $_POST['products_rate'];
    $pdtQuantity = $_POST['products_quantity'];
    $pdtAmount = $_POST['products_amount'];

    // Validate and sanitize inputs
    $dateValid = validate_date($date) ? "is-valid" : "is-invalid";
    $partyNameValid = validate_text($partyName) ? "is-valid" : "is-invalid";
    $brandNameValid = validate_array($brandName, 'text') ? "is-valid" : "is-invalid";
    $productNameValid = validate_array($productName, 'text') ? "is-valid" : "is-invalid";
    $rateValid = validate_array($pdtRate, 'number') ? "is-valid" : "is-invalid";
    $qtyValid = validate_array($pdtQuantity, 'number') ? "is-valid" : "is-invalid";
    $amountValid = validate_array($pdtAmount, 'number') ? "is-valid" : "is-invalid";

    if ($dateValid == "is-valid" && $partyNameValid == "is-valid" && $brandNameValid == "is-valid" && $productNameValid == "is-valid" && $rateValid == "is-valid" && $qtyValid == "is-valid" && $amountValid == "is-valid") {
        if (isset($_POST['update_id']) && !empty($_POST['update_id'])) {
            $update_id = $_POST['update_id'];
            $stmt = $conn->prepare("UPDATE purchasetable SET purchase_date=?, party_name=?, brand_name=?, product_name=?, product_rate=?, product_qty=?, product_amt=? WHERE id=?");
            $stmt->bind_param("sssssssi", $date, $partyName, $brandName, $productName, $pdtRate, $pdtQuantity, $pdtAmount, $update_id);
            $stmt->execute();
        } else {
            $sqlCreatePurchaseTable = "CREATE TABLE IF NOT EXISTS purchasetable (
                id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                purchase_date DATE,
                party_name VARCHAR(255) NOT NULL,
                brand_name VARCHAR(255) NOT NULL,
                product_name VARCHAR(255) NOT NULL,
                product_rate VARCHAR(255) NOT NULL,
                product_qty VARCHAR(255) NOT NULL,
                product_amt VARCHAR(255) NOT NULL
            )";
            if ($conn->query($sqlCreatePurchaseTable)) {
                $query = "INSERT INTO purchasetable (purchase_date, party_name, brand_name, product_name, product_rate, product_qty, product_amt) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("sssssss", $date, $partyName, ensure_string($brandName), ensure_string($productName), ensure_string($pdtRate), ensure_string($pdtQuantity), ensure_string($pdtAmount));
                $stmt->execute();
                echo "Data inserted successfully.";
            } else {
                echo "Error: " . $conn->error;
            }
        }
    } else {
        echo "<script>alert('All fields are required and must be valid.')</script>";
    }
}

function validate_date($date)
{
    return preg_match("/^(\d{4})-(\d{2})-(\d{2})$/", $date);
}

function validate_text($text)
{
    return !empty($text) && preg_match("/^[A-Za-z]+([ '-][A-Za-z]+)*$/", $text);
}

function validate_array($array, $type)
{
    if (!is_array($array))
        return false;
    foreach ($array as $item) {
        if ($type == 'text' && !validate_text($item))
            return false;
        if ($type == 'number' && !preg_match("/^-?\d+(\.\d+)?$/", $item))
            return false;
    }
    return true;
}

// Fetch options for brands, products, and parties
$brandOptions = get_options($conn, 'brands', 'brand_name');
$productOptions = get_options($conn, 'products', 'product_name');
$partiesOptions = get_options($conn, 'partyorder', 'party_name');

function get_options($conn, $table, $column)
{
    $sql = "SELECT * FROM $table";
    $result = $conn->query($sql);
    $options = "";
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $value = $row[$column];
            $options .= "<option value='$value'>$value</option>";
        }
    }
    return $options;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Project</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>

<body>
    <h1>My Project</h1>
    <form method="POST" action="">
        <!-- Add form fields here -->
        <input type="hidden" name="update_id" value="<?= $update_id ?>">
        <label for="purchase_date">Purchase Date:</label>
        <input type="date" id="purchase_date" name="purchase_date" value="<?= $edit_date ?>" class="<?= $dateValid ?>">

        <label for="parties_select">Party Name:</label>
        <select id="parties_select" name="parties_select" class="<?= $partyNameValid ?>">
            <?= $partiesOptions ?>
        </select>

        <label for="brands_name">Brand Name:</label>
        <select id="brands_name" name="brands_name[]" multiple class="<?= $brandNameValid ?>">
            <?= $brandOptions ?>
        </select>

        <label for="products_name">Product Name:</label>
        <select id="products_name" name="products_name[]" multiple class="<?= $productNameValid ?>">
            <?= $productOptions ?>
        </select>

        <label for="products_rate">Product Rate:</label>
        <input type="text" id="products_rate" name="products_rate[]" value="<?= $edit_product_rate ?>"
            class="<?= $rateValid ?>">

        <label for="products_quantity">Product Quantity:</label>
        <input type="text" id="products_quantity" name="products_quantity[]" value="<?= $edit_product_qty ?>"
            class="<?= $qtyValid ?>">

        <label for="products_amount">Product Amount:</label>
        <input type="text" id="products_amount" name="products_amount[]"
            value="<?= $edit_product_qty * $edit_product_rate ?>" class="<?= $amountValid ?>">

        <button type="submit" name="submit">Submit</button>
    </form>

    <!-- Include JQuery and custom script -->
    <script>
        function calculateSubtotal() {
            let totalAmount = 0;

            // Sum amounts from .amount elements within .product-row
            if ($('.product-row').length > 0) {
                $('.product-row').find('.amount').each(function () {
                    let amount = $(this).html();
                    totalAmount += parseFloat(amount); // Using parseFloat in case of decimal values
                });
            }

            // Sum values from products_amount[] inputs
            $('input[name="products_amount[]"]').each(function () {
                let inputAmount = $(this).val();
                totalAmount += parseFloat(inputAmount); // Using parseFloat in case of decimal values
            });

            // Update the sub-total element
            $('#sub-total').text(totalAmount.toFixed(2)); // Assuming currency format with two decimal places
        }

        // Trigger calculation when value changes in any of the products_amount[] inputs
        $(document).on('change', 'input[name="products_amount[]"]', calculateSubtotal);

        // Initial calculation on page load
        $(document).ready(function () {
            calculateSubtotal();
        });

    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="assets/js/script.js"></script>
</body>

</html>