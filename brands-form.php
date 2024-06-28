<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "srisw";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);
// Check connection
if (!$conn) {
    die("Connection
    failed: " . mysqli_connect_error());
}
// update functionality
$update_id = "";
$edit_brand = "";

if (isset($_REQUEST['update_id'])) {
    $update_id = $_REQUEST['update_id'];
    $query = " SELECT * FROM brands WHERE id='" . $update_id . "'";
    $result = $conn->query($query);
    if ($result) {
        foreach ($result as $row) {
            $update_id = $row['id'];
            $edit_brand = $row['brand_name'];
        }
    }
}

// Create brands table if not exists
$sqlCreateBrands = " CREATE TABLE IF NOT EXISTS brands ( id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY, brand_name
    VARCHAR(255) NOT NULL )";
if (mysqli_query($conn, $sqlCreateBrands)) { // Check if the table was actually created or it already existed 
    if (mysqli_affected_rows($conn) > 0) {
        echo "Brands table created successfully.<br>";
    }
} else {
    echo "Error creating brands table: " . mysqli_error($conn);
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Brand Entry Form</title>
    <!-- Bootstrap CDN -->
    <link rel="stylesheet" href="./bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <?php include 'sidebar.php'; ?>
    <h1>Enter Brand Details</h1>
    <form method="POST" class="form w-100 text-center">
        <div class="form-group">
            <label for="brand-name">Brand Name:</label>
            <input
                value="<?php echo isset($_POST['brand_name']) ? htmlspecialchars($_POST['brand_name']) : ''; ?><?php echo $edit_brand; ?>"
                type="text" id="brand-name" name="brand_name" class="form-control">
            <div class="brandname-error">
                <?php
                // Assuming you're submitting data to this same PHP script
                if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['brand_name'])) {
                    // Validate brand name
                    $brandName = mysqli_real_escape_string($conn, $_POST['brand_name']);

                    if (empty($brandName)) {
                        echo "<div class='mt-5 alert alert-danger' role='alert'>Brand name is required</div>";
                    } else if (preg_match('/\d/', $brandName)) {
                        echo "<div class='alert alert-danger mt-5' role='alert'>Brand name cannot contain numbers</div>";
                    } else {
                        // Check if we are updating or inserting
                        if (!empty($update_id)) {
                            // Prepare an update statement
                            $sqlUpdate = "UPDATE brands SET brand_name = ? WHERE id = ?";
                            $stmt = mysqli_prepare($conn, $sqlUpdate);
                            mysqli_stmt_bind_param($stmt, "si", $brandName, $update_id);

                            if (mysqli_stmt_execute($stmt)) {
                                echo "<p class='text-bg-success p-2 mt-4'>Record updated successfully.</p><br>";
                            } else {
                                echo "Error: " . $sqlUpdate . "<br>" . mysqli_error($conn);
                            }
                        } else {
                            // Prepare an insert statement
                            $sqlInsert = "INSERT INTO brands (brand_name) VALUES (?)";
                            $stmt = mysqli_prepare($conn, $sqlInsert);
                            mysqli_stmt_bind_param($stmt, "s", $brandName);

                            if (mysqli_stmt_execute($stmt)) {
                                echo "<p class='text-bg-success p-2 mt-4'>New record created successfully.</p><br>";
                            } else {
                                echo "Error: " . $sqlInsert . "<br>" . mysqli_error($conn);
                            }
                        }
                    }
                }
                // Remember to close the prepared statement and the connection
                if (isset($stmt)) {
                    mysqli_stmt_close($stmt);
                }
                mysqli_close($conn);
                ?>


            </div>
        </div>
        <br>
        <input class="btn btn-primary" type="submit" value="Submit">
        <a class="btn btn-dark" href="brands-table.php">Go to Table</a>
    </form>
</body>

</html>