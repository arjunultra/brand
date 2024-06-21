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
if (isset($_REQUEST['selected_brands'])) {
    $brandName = $_REQUEST['selected_brands'];
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

}
// add to table
if (isset($_REQUEST['selected_products'])) {
    $selected_product = "";
    $selected_brand = "";
    $product_rate = "";
    $selected_product = $_REQUEST['selected_products'];
    $selected_brand = $_REQUEST['selected_brand'];
    $selected_product_rate = $_REQUEST['product_rate'];
    $selected_product_quantity = $_REQUEST['product_quantity'];
    $row_index = $_REQUEST['row_index'];
    $selected_product_amount = $_REQUEST['product_amount'];
    if (!empty($selected_brand) && (!empty($selected_product))) { ?>
        <tr class="product-row row<?php echo $row_index ?>">
            <td class="row-index"><?php echo $row_index ?></td>
            <td><?php echo $selected_brand ?>
                <input type="hidden" name="brands_name[]" value="<?php echo $selected_brand ?>">
            </td>
            <td><?php echo $selected_product ?>
                <input type="hidden" name="products_name[]" value="<?php echo $productName ?>">
            </td>
            <td>
                <input onkeyup="" class="w-75 product-rate" type="text" name="products_rate[]"
                    value="<?php echo $selected_product_rate ?>">
            </td>
            <td>
                <input onkeyup="" class="w-75 product-quantity" type="text" name="products_quantity[]"
                    value="<?php echo $selected_product_quantity ?>">
            </td>
            <td class="amount"><?php echo $selected_product_amount ?>
                <input readonly type="hidden" name="products_amount[]" value="<?php echo $selected_product_amount ?>">
            </td>
            <td class="function">
                <input type="hidden" name="function">
                <button type="button" class="btn btn-outline-danger delete-btn"
                    onclick="DeleteRow(<?php echo $row_index; ?>)">Delete</button>
            </td>
        </tr>
    <?php }
}
