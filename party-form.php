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
// create table
$sql = "CREATE TABLE IF NOT EXISTS partyorder(id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,party_name VARCHAR(255) NOT NULL,party_mobile VARCHAR(255),brand_name VARCHAR(255) NOT NULL,product_name VARCHAR(255) NOT NULL )";
if (mysqli_query($conn, $sql)) {

    echo "Table partyorder created successfully";
} else {
    echo "Error creating table: " . mysqli_error($conn);
}
// initializing variables
$partyName = $partyMobile = $brandName = $productName = "";
$isValid = true;
// error handling variables
$nameErr = $mobileErr = $brandErr = $productErr = "";
// Update
// Fetch party data for update if update_id is set
$update_id = "";
$update_party_name = "";
$update_party_mobile = "";
$update_brand = "";
$update_brands = [];
$update_product = "";
$update_products = [];

if (isset($_REQUEST['update_id'])) {
    $update_id = $_REQUEST['update_id'];
    $updateQuery = "SELECT * FROM partyorder WHERE id='" . $update_id . "'";
    $result = $conn->query($updateQuery);
    if ($result) {
        foreach ($result as $row) {
            $update_party_name = $row['party_name'];
            $update_party_mobile = $row['party_mobile'];
            $update_brand = $row['brand_name'];
            $update_product = $row['product_name'];
        }
    }
}
// creating variables
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit"])) {
    $partyName = isset($_POST["party_name"]) ? $_POST["party_name"] : "";
    $partyMobile = isset($_POST["party_mobile"]) ? $_POST["party_mobile"] : "";
    $brandName = isset($_POST["brand_name"]) ? $_POST["brand_name"] : "";
    $productName = isset($_POST["product_name"]) ? $_POST["product_name"] : "";
    // Party Name Validation
    if (empty($partyName) || !preg_match("/^[a-zA-Z]*$/", $partyName)) {
        $nameErr = "Please enter a valid name !";
        $isValid = false;
    } else {
        filter_input(INPUT_POST, 'party_name', FILTER_SANITIZE_STRING);
        $isValid = true;
    }
    // Party Mobile Validation 
    if (empty($partyMobile)) {
        $mobileErr = "Please enter your mobile number";
    } elseif (!preg_match('/^[0-9]{10}+$/', $partyMobile)) {
        $mobileErr = "Entered mobile number is invalid";
        $isValid = false;
    } else {
        filter_input(INPUT_POST, "party_mobile", FILTER_SANITIZE_STRING);
        $isValid = true;
    }
    if ($isValid) {
        $partyName = mysqli_real_escape_string($conn, $_POST['party_name']);
        $partyMobile = mysqli_real_escape_string($conn, $_POST['party_mobile']);
        if (is_array($_POST["brand_name"])) {
            $stringValue = implode(',', $_POST['brand_name']);
            $brandValue = mysqli_real_escape_string($conn, $stringValue);
        } else {
            $brandValue = mysqli_real_escape_string($conn, $_POST["brand_name"]);
        }
        if (is_array($_POST["product_name"])) {
            $stringValue = implode(',', $_POST['product_name']);
            $productValue = mysqli_real_escape_string($conn, $stringValue);
        } else {
            $productValue = mysqli_real_escape_string($conn, $_POST["product_name"]);
        }
        // debugging line
        echo "<script>console.log('Update ID: $update_id');</script>";
    }
    if (!empty($update_id)) {
        // Convert $productName array to a string if it's an array
        $productNameStr = is_array($productName) ? implode(', ', $productName) : $productName;
        $brandNameStr = is_array($brandName) ? implode(', ', $brandName) : $brandName;
        // If update_id is set, perform an update operation
        $sqlUpdate = "UPDATE partyorder SET party_name='$partyName', party_mobile='$partyMobile', brand_name='$brandNameStr', product_name='$productNameStr' WHERE id=$update_id";
        if (mysqli_query($conn, $sqlUpdate)) {
            echo "<script>alert('Record updated successfully.');</script>";
        } else {
            echo "Error: " . $sqlUpdate . "<br>" . mysqli_error($conn);
        }
    } else {
        // Otherwise, perform an insert operation
        $sqlInsert = "INSERT INTO partyorder (party_name, party_mobile, brand_name, product_name) 
    VALUES ('$partyName', '$partyMobile', '$brandValue', '$productValue') ";
        if (mysqli_query($conn, $sqlInsert)) {
            echo "<script>
    alert('New record created successfully.');
  </script>";
            header('Location: party-form.php');
            exit;

        } else {
            echo "Error: " . $sqlInsert . "<br>" . mysqli_error($conn);
        }
    }

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

?>

<body>
    <h1>Party Form</h1>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" class="form w-100 text-center">
        <div class="form-group">
            <input type="hidden" name="update_id" value="<?php echo $update_id; ?>">
        </div>
        <div class="form-group">
            <label for="product-name">Party Name:</label>
            <input
                value="<?php echo isset($_POST['party_name']) ? htmlspecialchars($_POST['party_name']) : ''; ?><?php echo $update_party_name; ?>"
                type="text" id="party-name" name="party_name" class="form-control">
            <?php if (!empty($nameErr)): ?>
                <div class="alert alert-danger mt-2"><?php echo $nameErr; ?></div>
            <?php endif; ?>
            <label for="mobile">Mobile Number:</label>
            <input
                value="<?php echo isset($_POST['party_mobile']) ? htmlspecialchars($_POST['party_mobile']) : ''; ?><?php echo $update_party_mobile; ?>"
                type="text" id="mobile" name="party_mobile" class="form-control">
            <?php if (!empty($mobileErr)): ?>
                <div class="alert alert-danger mt-2"><?php echo $mobileErr; ?></div>
            <?php endif; ?>
        </div>
        <div class="select-container row mt-5">
            <div id="brand-container" class="form-group col">
                <!-- <label for="brand">Brand:</label> -->
                <select id="brand-select" name="brand" class="mb-5" onchange="getProducts(this.value)">
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
            <div id="addProduct" class="btn-container mt-3 row"><button type="button"
                    class="align-self-center btn btn-success mb-5 ms-2 ms-md-0" href="#">ADD</button></div>
        </div>

        <!-- Table -->
        <div class="container mt-5">
            <h2>Party Order Details</h2>
            <div id="productTable" class="table-responsive">
                <table id="pdtTable" class="table table-striped table-hover table-bordered">
                    <thead class="table-dark bg-primary">
                        <tr>
                            <th>Brand Name</th>
                            <th>Product Name</th>
                        </tr>
                    </thead>
                    <tbody id="table-body">
                        <?php
                        if (!empty($update_brand)) {
                            $update_brands = explode(',', $update_brand);
                            $update_products = explode(',', $update_product);
                            for ($i = 0; $i < count($update_brands); $i++) { ?>
                                <tr>
                                    <td><?php echo $update_brands[$i] ?>
                                        <input type="hidden" name="brand_name[]" value="<?php echo $update_brands[$i] ?>">
                                    </td>
                                    <td><?php echo $update_products[$i] ?>
                                        <input type="hidden" name="product_name[]" value="<?php echo $update_products[$i] ?>">
                                    </td>
                                    <?php
                            }
                        } ?>

                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <input name="submit" class="btn btn-outline-danger" type="submit">
    </form>
    <!-- JQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script>
        function getProducts(brand_id) {
            var post_url = "party-form-changes.php?selected_brand=" + brand_id;

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

        $('#addProduct').click(function () {
            let selectedBrand = "";
            if ($("#brand-select").length > 0) {
                selectedBrand = $("#brand-select").val();
            }
            let selectedProduct = "";
            if ($("#product-select").length > 0) {
                selectedProduct = $("#product-select").val();
            }
            // alert(selectedProduct)
            var post_url = "party-form-changes.php?selected_product=" + selectedProduct + "&selected_brands=" + selectedBrand;
            jQuery.ajax({
                url: post_url, success: function (result) {
                    // alert(result)
                    if (result != "") {
                        if ($("#table-body").find("tr").length > 0) {
                            $("#table-body").find("tr:first").before(result);
                        } else {
                            $("#table-body").append(result);
                        }

                    }
                }
            });
        });

    </script>
    <script type="module" src="main.js"></script>

</body>

</html>