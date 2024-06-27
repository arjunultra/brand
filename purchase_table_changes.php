<?php
// purchase_form_table_changes.php
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
$response = "";
if (isset($_REQUEST['selected_party'])) {
    $partyName = "";
    $party_list = array();
    $partyName = $_REQUEST['selected_party'];
    $productName = $_REQUEST['selected_product'];
    $brandName = $_REQUEST['selected_brand'];

    $where = "";

    if (!empty($partyName)) {
        $where = $where . "party_name ='$partyName'";
    }

    if (!empty($brandName)) {
        if (!empty($where)) {
            $where = $where . "AND FIND_IN_SET('$brandName','brand_name')";
        } else {
            $where = "FIND_IN_SET('$brandName','brand_name')";
        }
    }

    if (!empty($productName)) {
        if (!empty($where)) {
            $where = $where . "AND FIND_IN_SET('$productName','brand_name')";
        } else {
            $where = "FIND_IN_SET('$productName','brand_name')";
        }
    }

    if (!empty($where)) {
        $query = "SELECT * FROM purchasetable WHERE " . $where . "";
    } else {
        $query = "SELECT * FROM purchasetable";
    }

    $result = mysqli_query($conn, $query);

    // Iterate through each row using a foreach loop

    if ($result) { ?>
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td><?php echo ($row["id"]); ?></td>
                <td><?php echo ($row["purchase_date"]); ?></td>
                <td><?php echo ($row["party_name"]); ?></td>
                <td><?php echo ($row["brand_name"]); ?></td>
                <td><?php echo ($row["product_name"]); ?></td>
                <td><?php echo ($row["product_rate"]); ?></td>
                <td><?php echo ($row["product_qty"]); ?></td>
                <td><?php echo ($row["product_amt"]); ?></td>
                <td class='d-flex align-items-center justify-content-center'>
                    <a target="_blank" class="btn btn-outline-primary w-50 me-2"
                        href="purchase_form.php?update_id=<?php echo $row['id']; ?>">UPDATE</a>
                    <a class="btn btn-danger w-50" href="purchase_form_table.php?delete_id=<?php echo $row['id']; ?>">DELETE</a>
                </td>
            </tr>
        <?php } ?>
    <?php }
}
