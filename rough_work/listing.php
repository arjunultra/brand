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

// Creating variables
$partyName = "";
$brandName = "";
$productName = "";
$userSelectedParty = "";
$userSelectedBrand = "";
$userSelectedProduct = "";

// Fetching data from purchasetable
$sql = "SELECT * FROM purchasetable";
$resultPurchase = mysqli_query($conn, $sql);

// Fetching parties from partyorder
$sqlParties = "SELECT * FROM partyorder";
$resultParties = mysqli_query($conn, $sqlParties);
$partiesOptions = "";

if (mysqli_num_rows($resultParties) > 0) {
    while ($row = mysqli_fetch_assoc($resultParties)) {
        $partiesName = $row['party_name'];
        $userSelectedParty = isset($userSelectedParty) ? $userSelectedParty : "";
        $isSelected = ($partiesName == $userSelectedParty) ? "selected" : "";
        $partiesOptions .= "<option $isSelected value='$partiesName'>$partiesName</option>";
    }
}

// Fetching brands from brands table
$sqlBrands = "SELECT * FROM brands";
$resultBrands = mysqli_query($conn, $sqlBrands);
$brandOptions = "";

if (mysqli_num_rows($resultBrands) > 0) {
    while ($row = mysqli_fetch_assoc($resultBrands)) {
        $userSelectedBrand = isset($userSelectedBrand) ? $userSelectedBrand : "";
        $brandId = $row['id'];
        $brandName = $row['brand_name'];
        $isSelected = ($brandName == $userSelectedBrand) ? "selected" : "";
        $brandOptions .= "<option $isSelected value='" . $brandName . "'>" . $brandName . "</option>";
    }
}

// Fetching Products from products table
$sqlProducts = "SELECT * FROM products";
$resultProducts = mysqli_query($conn, $sqlProducts);
$productOptions = "";

if (mysqli_num_rows($resultProducts) > 0) {
    while ($row = mysqli_fetch_assoc($resultProducts)) {
        $userSelectedProduct = isset($userSelectedProduct) ? $userSelectedProduct : "";
        $productId = $row['id'];
        $productName = $row['product_name'];
        $isSelected = ($productName == $userSelectedProduct) ? "selected" : "";
        $productOptions .= "<option $isSelected value='$productName'>$productName</option>";
    }
}

// Post method variables
if (isset($_POST["party_select"])) {
    $partyName = $_POST["party_select"];
}
$brandName = isset($_POST["brand_select"]) ? $_POST["brand_select"] : ""; // Corrected to actually assign the value if set
$productName = isset($_POST["product_select"]) ? $_POST["product_select"] : ""; // Corrected to actually assign the value if set

echo $partyName . "/" . $brandName . "/" . $productName;

function getPurchaseList($partyName, $brandName, $productName)
{
    global $conn; // Correct usage of global to access $conn within the function

    $where = "";

    if (!empty($partyName)) {
        $where = $where . "party_name ='$partyName'";
    }

    if (!empty($brandName)) {
        if (!empty($where)) {
            $where = $where . " AND FIND_IN_SET ('$brandName', brand_name)";
        } else {
            $where = "FIND_IN_SET('$brandName', brand_name)";
        }
    }

    if (!empty($productName)) {
        if (!empty($where)) {
            $where .= " AND FIND_IN_SET('$productName', product_name)";
        } else {
            $where = "FIND_IN_SET('$productName', product_name)";
        }
    }

    if (!empty($where)) {
        $query = "SELECT * FROM purchasetable WHERE " . $where;
    } else {
        $query = "SELECT * FROM purchasetable";
    }
    $result = mysqli_query($conn, $query);
    // Assuming you want to do something with $result here, like returning it or processing it further
}

// original selection
if (isset($_POST["party_select"])) {
    $partyName = $_POST["party_select"];
}
if (isset($_POST["brand_select"])) {
    $brandName = $_POST["brand_select"];
}
if (isset($_POST["product_select"])) {
    $productName = $_POST["product_select"];
}

// Fetching data from purchasetable
$sql = "SELECT * FROM purchasetable";
$resultPurchase = mysqli_query($conn, $sql);

// Fetching parties from partyorder
$sqlParties = "SELECT * FROM partyorder";
$resultParties = mysqli_query($conn, $sqlParties);
$partiesOptions = "";

if (mysqli_num_rows($resultParties) > 0) {
    while ($row = mysqli_fetch_assoc($resultParties)) {
        $partiesName = $row['party_name'];
        $userSelectedParty = isset($userSelectedParty) ? $userSelectedParty : "";
        $isSelected = ($partiesName == $userSelectedParty) ? "selected" : "";
        $partiesOptions .= "<option $isSelected value='$partiesName'>$partiesName</option>";
    }
}
// Fetching brands from brands table
$sqlBrands = "SELECT * FROM brands";
$resultBrands = mysqli_query($conn, $sqlBrands);
$brandOptions = "";

if (mysqli_num_rows($resultBrands) > 0) {
    while ($row = mysqli_fetch_assoc($resultBrands)) {
        $userSelectedBrand = isset($userSelectedBrand) ? $userSelectedBrand : "";
        $brandName = $row['brand_name'];
        $isSelected = ($brandName == $userSelectedBrand) ? "selected" : "";
        $brandOptions .= "<option $isSelected value='$brandName'>$brandName</option>";
    }
}
// Fetching Products from products table
$sqlProducts = "SELECT * FROM products";
$resultProducts = mysqli_query($conn, $sqlProducts);
$productOptions = "";

if (mysqli_num_rows($resultProducts) > 0) {
    while ($row = mysqli_fetch_assoc($resultProducts)) {
        $userSelectedProduct = isset($userSelectedProduct) ? $userSelectedProduct : "";
        $productName = $row['product_name'];
        $isSelected = ($productName == $userSelectedProduct) ? "selected" : "";
        $productOptions .= "<option $isSelected value='$productName'>$productName</option>";
    }
}



// wrong selection

// Assuming this is at the top of your script:
// Initialize variables based on current POST data
$partyName = isset($_POST["party_select"]) ? $_POST["party_select"] : "";
$brandName = isset($_POST["brand_select"]) ? $_POST["brand_select"] : "";
$productName = isset($_POST["product_select"]) ? $_POST["product_select"] : "";

// Further down, when generating the select options:
// For parties
foreach ($resultParties as $row) {
    $selected = ($row['party_name'] === $partyName) ? 'selected' : '';
    $partiesOptions .= "<option value='{$row['party_name']}' {$selected}>{$row['party_name']}</option>";
}

// For brands
foreach ($resultBrands as $row) {
    $selected = ($row['brand_name'] === $brandName) ? 'selected' : '';
    $brandOptions .= "<option value='{$row['brand_name']}' {$selected}>{$row['brand_name']}</option>";
}

// For products
foreach ($resultProducts as $row) {
    $selected = ($row['product_name'] === $productName) ? 'selected' : '';
    $productOptions .= "<option value='{$row['product_name']}' {$selected}>{$row['product_name']}</option>";
}

?>