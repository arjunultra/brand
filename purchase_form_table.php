<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "srisw";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection Failed: " . mysqli_connect_error());
}

// Fetching data from purchasetable
$sql = "SELECT * FROM purchasetable";
$result = mysqli_query($conn, $sql);

// Fetching parties from partyorder
$sqlParties = "SELECT * FROM partyorder";
$resultParties = mysqli_query($conn, $sqlParties);
$partiesOptions = "";

if (mysqli_num_rows($resultParties) > 0) {
    while ($row = mysqli_fetch_assoc($resultParties)) {
        $partiesName = $row['party_name'];
        $partiesOptions .= "<option value='$partiesName'>$partiesName</option>";
    }
}
// Fetching brands from brands table
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
// Fetching Products from products table
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

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Purchase Form Data</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

</head>

<body>
    <div class="container mt-5">
        <h1>Purchase Form Data</h1>
        <p class="text-bg-warning d-inline-block text-uppercase p-2">Filters</p>
        <div class="filterselect-container mb-3">
            <select class="me-3" name="party_select" id="party-select">
                <option selected value="">Select a Party</option>
                <?= $partiesOptions ?>
            </select>
            <select name="brand_select" id="brand-select">
                <option selected value="">Select a Brand</option>
                <?= $brandOptions ?>
            </select>
            <select class="ms-3" name="product_select" id="products-select">
                <option selected value="">Select a Product</option>
                <?= $productOptions ?>
            </select>
        </div>
        <div class="table-responsive">
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
                        <th class="text-center">Function</th>
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
                                <td class='d-flex align-items-center justify-content-center'>
                                    <a target="_blank" class="btn btn-outline-primary w-50 me-2"
                                        href="purchase_form.php?update_id=<?php echo $row['id']; ?>">UPDATE</a>
                                    <a class="btn btn-danger w-50"
                                        href="purchase_form_table.php?delete_id=<?php echo $row['id']; ?>">DELETE</a>
                                </td>
                            </tr>
                        <?php }
                    } else {
                        echo "<tr><td class='bg-danger text-light text-center fw-bold h1' colspan='9'>No results found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>

            <?php
            // Delete functionality
            if (isset($_GET['delete_id']) && !empty($_GET['delete_id'])) {
                $delete_id = $_GET['delete_id'];
                $sql = "DELETE FROM purchasetable WHERE id=$delete_id";
                if (mysqli_query($conn, $sql)) {
                    echo "<h5 class='d-inline-block p-2 text-center text-danger fw-bold border border-danger'>Record Deleted Successfully</h5>";
                    echo "<script>window.location.href='purchase_form_table.php';</script>"; // Refresh the page
                } else {
                    echo "Error deleting record: " . mysqli_error($conn);
                }
            }
            ?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"
        integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy"
        crossorigin="anonymous"></script>
    <script>
        $(document).ready(function () {
            $("#party-select, #brand-select, #product-select").change(function () {
                let selectedParty = $("#party-select").val() || "";
                let selectedBrand = $("#brand-select").val() || "";
                let selectedProduct = $("#product-select").val() || "";

                var post_url = "purchase_table_changes.php?selected_party=" + selectedParty + "&selected_brand=" + selectedBrand + "&selected_product=" + selectedProduct;

                jQuery.ajax({
                    url: post_url,
                    success: function (result) {
                        alert(result);

                        if (result != "") {
                            $("#tBody").html(result);
                        }
                    },
                    error: function (xhr, status, error) {
                        // Handling errors
                        console.error("An error occurred: " + status + ", " + error);
                    }
                });
            });
        });
    </script>
    <?php mysqli_close($conn); ?>
</body>

</html>