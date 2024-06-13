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
    $product_query = "SELECT * FROM partyorder WHERE brand_name='$brandName' ";
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