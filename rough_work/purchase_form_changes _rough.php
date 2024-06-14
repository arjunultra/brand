<?php
if (isset($_REQUEST['selected_brand'])) {
    $brandName = $_REQUEST['selected_brand'];
    $productList = [];
    $productArr = [];
    $product_query = "SELECT product_name FROM partyorder WHERE brand_name = '$brandName'";
    $productList = mysqli_query($conn, $product_query);
    $row = mysqli_fetch_assoc($productList);
    if ($row) {
        $productArr = explode(',', $row['product_name']);
        var_dump($productArr);
        if (!empty($productArr)) { ?>
            <select class="w-100" id="products-select" name="products_select">
                <?php foreach ($productArr as $productName) {
                    $productName = trim($productName);
                    ?>
                    <option value="<?php echo ($productName); ?>"><?php echo ($productName); ?></option>
                <?php } ?>
            </select>
        <?php }
    }
}