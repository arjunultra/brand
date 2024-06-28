<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports</title>
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
    die("Connection failed: " . mysqli_connect_error());
}
// creating variables
$partyName = "";
$brandName = "";
$productName = "";
$userSelectedParty = "";
$userSelectedBrand = "";
$userSelectedProduct = "";
// post method variables
// if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['party_select'])) {
$partyName = isset($_POST["party_select"]) ? $_POST["party_select"] : "";
$brandName = isset($_POST["brand_select"]) ? $_POST["brand_select"] : "";
$productName = isset($_POST["product_select"]) ? $_POST["product_select"] : "";

// For parties
$sqlParties = "SELECT * FROM partyorder";
$resultParties = mysqli_query($conn, $sqlParties);
$partiesOptions = "";


foreach ($resultParties as $row) {
    $selected = ($row['party_name'] === $partyName) ? 'selected' : '';
    $partiesOptions .= "<option value='{$row['party_name']}' {$selected}>{$row['party_name']}</option>";
}

// For brands
$sqlBrands = "SELECT * FROM brands";
$resultBrands = mysqli_query($conn, $sqlBrands);
$brandOptions = "";
foreach ($resultBrands as $row) {
    $selected = ($row['brand_name'] === $brandName) ? 'selected' : '';
    $brandOptions .= "<option value='{$row['brand_name']}' {$selected}>{$row['brand_name']}</option>";
}

// For products
$sqlProducts = "SELECT * FROM products";
$resultProducts = mysqli_query($conn, $sqlProducts);
$productOptions = "";
foreach ($resultProducts as $row) {
    $selected = ($row['product_name'] === $productName) ? 'selected' : '';
    $productOptions .= "<option value='{$row['product_name']}' {$selected}>{$row['product_name']}</option>";
}
function getPurchaseList($partyName, $brandName, $productName)
{
    // echo $partyName . "///";
    $where = "";
    global $conn;

    if (!empty($partyName)) {
        $where = $where . "party_name ='$partyName'";
    }

    if (!empty($brandName)) {
        if (!empty($where)) {
            $where = $where . " AND FIND_IN_SET ('$brandName',brand_name)";
        } else {
            $where = "FIND_IN_SET('$brandName',brand_name)";
        }
    }

    if (!empty($productName)) {
        if (!empty($where)) {
            $where .= "AND FIND_IN_SET('$productName',product_name)";
        } else {
            $where = "FIND_IN_SET('$productName',product_name)";
        }
    }

    if (!empty($where)) {
        $query = "SELECT * FROM purchasetable WHERE " . $where . "";
    } else {
        $query = "SELECT * FROM purchasetable";
    }
    $result = mysqli_query($conn, $query);

    return $result;
}




// echo $partyName . "/" . $brandName . "/" . $productName;

$result = array();

$result = getPurchaseList($partyName, $brandName, $productName);

// print_r($result);


?>
<script src="./JS/jquery-3.7.1.min.js"></script>

<body>
    <?php include_once 'sidebar.php'; ?>
    <h1 class="text-center">Reports</h1>
    <div class="form-container d-flex align-items-center justify-content-center">
        <form method="post" id="report-form" class="row" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
            <div class="form-group col">
                <label for="party-select"></label>
                <select onchange="formSubmitOnChange();" class="me-3" name="party_select" id="party-select">
                    <option value="">Select a Party</option>
                    <?= $partiesOptions ?>
                </select>
            </div>
            <div class="form-group col">
                <label for="brand-select"></label>
                <select onchange="formSubmitOnChange()" class="me-3" name="brand_select" id="brand-select">
                    <option value="">Select a Brand</option>
                    <?= $brandOptions ?>
                </select>
            </div>
            <div class="form-group col">
                <label for="product-select"></label>
                <select onchange="formSubmitOnChange()" class="me-3" name="product_select" id="product-select">
                    <option value="">Select a Product</option>
                    <?= $productOptions ?>
                </select>
            </div>
        </form>
    </div>
    <!-- Table -->
    <div class="table-container">
        <table class="table table-striped table-hover table-bordered">
            <thead class="table-dark bg-primary">
                <tr>
                    <th>ID</th>
                    <th>Purchase Date</th>
                    <th>Party Name</th>
                    <th>Brand Name</th>
                    <th>Product Name</th>
                    <th>Product Rate</th>
                    <th>Product Quantity</th>
                    <th>Grand Total</th>
                </tr>
            </thead>
            <tbody id="tBody">
                <?php
                if (mysqli_num_rows($result) > 0) {
                    // Fetch all data at once and store it in an associative array
                    $allRows = mysqli_fetch_all($result, MYSQLI_ASSOC);

                    // Iterate through each row using a foreach loop
                    foreach ($allRows as $row) { ?>
                        <tr data-party="<?php echo $row["party_name"] ?>">
                            <td><?php echo $row["id"] ?></td>
                            <td><?php echo $row["purchase_date"] ?></td>
                            <td><?php echo $row["party_name"] ?></td>
                            <td><?php echo $row["brand_name"] ?></td>
                            <td><?php echo $row["product_name"] ?></td>
                            <td><?php echo $row["product_rate"] ?></td>
                            <td><?php echo $row["product_qty"] ?></td>
                            <td><?php echo $row["product_amt"] ?></td>
                        </tr>
                    <?php }
                } else {
                    echo "<tr><td class='bg-danger text-light text-center fw-bold h1' colspan='9'>No results found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
    <script>
        function formSubmitOnChange() {
            if ($('#report-form').length > 0) {
                $('#report-form').submit();
            }
        }
    </script>
</body>

</html>