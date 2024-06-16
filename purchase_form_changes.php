<?php
// party-form-changes.php
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

if (isset($_REQUEST['selected_party'])) {
    $partyName = $_REQUEST['selected_party'];
    $brandArr = [];
    $brand_query = "SELECT brand_name FROM partyorder WHERE party_name = '$partyName'";
    $brandList = mysqli_query($conn, $brand_query);
    $row = mysqli_fetch_assoc($brandList);

    if ($row) {
        $brandArr = explode(',', $row['brand_name']);
        $uniqueBrandArr = array_unique($brandArr);
        if (!empty($uniqueBrandArr)) { ?>
            <select class="w-100" id="brand-select" onchange="getProducts(this.value)" name="brand_select">
                <option selected value="">Select a Brand</option>
                <?php foreach ($uniqueBrandArr as $brandName) {
                    $brandName = trim($brandName);
                    ?>
                    <option value="<?php echo ($brandName); ?>"><?php echo ($brandName); ?></option>
                <?php } ?>
            </select>
        <?php }
    }
}
if (isset($_REQUEST['selected_brand'])) {
    $brandName = $_REQUEST['selected_brand'];
    $productList = [];
    $productArr = [];
    $product_query = "SELECT product_name FROM products WHERE brand = '$brandName'";
    $productList = mysqli_query($conn, $product_query);

    if ($productList != null) {
        // $productArr = explode(',', $row['product_name']);
        if (!empty($productList)) { ?>
            <select class="w-100" id="products-select" name="products_select" onchange="fetchProductRate(this.value)">
                <option selected value="">Select a Product</option>
                <?php foreach ($productList as $data) {
                    // $productName = trim($productName);
                    ?>
                    <option value="<?php echo ($data['product_name']); ?>"><?php echo ($data['product_name']); ?></option>
                <?php } ?>
            </select>
        <?php }
    }
}
// fetching product rate based on selected product
if (isset($_REQUEST['selected_product'])) {
    $productName = "";
    $product_list = array();
    $productName = $_REQUEST['selected_product'];
    // Filtering using SQL 
    $product_query = "";
    $product_query = "SELECT selected_rate FROM products WHERE product_name='$productName' ";
    if (!empty($product_query)) {
        $product_list = mysqli_query($conn, $product_query);
    }

    if (!empty($product_list)) { ?>
        <input class="form-control" id="product-rate" name="product_rate" <?php foreach ($product_list as $data) { ?>             <?php echo $data['selected_rate'] ?>         <?php } ?> value="<?php echo $data['selected_rate'] ?>">
    <?php }

} ?>