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

        if (!empty($brandArr)) { ?>
            <select id="brand-select" name="brand_select">
                <?php foreach ($brandArr as $brandName) {
                    $brandName = trim($brandName); // Trim to remove any accidental whitespace
                    ?>
                    <option value="<?php echo ($brandName); ?>"><?php echo ($brandName); ?></option>
                <?php } ?>
            </select>
        <?php }
    }
}