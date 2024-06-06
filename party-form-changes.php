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

if (isset($_REQUEST['selected_brand'])) {
    $brandName = "";
    $product_list = array();
    $brandName = $_REQUEST['selected_brand'];
    // Filtering using SQL 
    $product_query = "";
    $product_query = "SELECT * FROM products WHERE brand='$brandName' ";
    if (!empty($product_query)) {
        $product_list = mysqli_query($conn, $product_query);
    }

    if (!empty($product_list)) { ?>
        <select id="product-select" name="product">
            <?php foreach ($product_list as $data) { ?>
                <option value="<?php echo $data['product_name'] ?>"><?php echo $data['product_name'] ?></option>
            <?php } ?>
        </select>
    <?php }
}
if (isset($_REQUEST['selected_product'])) {
    $product_name = "";
    $brand_name = "";
    $product_name = $_REQUEST['selected_product'];
    $brand_name = $_REQUEST['selected_brands'];

    if (!empty($product_name) && (!empty($brand_name))) { ?>
        <tr>
            <td><?php echo $brand_name ?>
                <input type="hidden" name="brand_name[]" value="<?php echo $brand_name ?>">
            </td>
            <td><?php echo $product_name ?>
                <input type="hidden" name="product_name[]" value="<?php echo $product_name ?>">
            </td>
        </tr>
    <?php }
}
?>