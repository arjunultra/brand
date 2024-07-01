// echo $partyName . "///";
$where = "";
global $conn;

if (!empty($partyName)) {
$where = $where . "party_name ='$partyName'";
}

if (!empty($brandName)) {
if (!empty($where)) {
$where = $where . " AND FIND_IN_SET ('$brandName',brand_name)";
} else {
$where = " FIND_IN_SET('$brandName',brand_name)";
}
}

if (!empty($productName)) {
if (!empty($where)) {
$where .= " AND FIND_IN_SET('$productName',product_name)";
} else {
$where = "FIND_IN_SET('$productName',product_name)";
}
}

if (!empty($where)) {
$query = "SELECT * FROM purchasetable WHERE " . $where . "";
} else {
$query = "SELECT * FROM purchasetable";
}
$result = mysqli_query($conn, $query);

return $result;

<!-- find in set -->
<?php
function getPurchaseList($partyName, $brandName, $productName)
{
    $where = "";
    global $conn;

    if (!empty($partyName)) {
        $where .= "party_name ='$partyName'";
    }

    if (!empty($brandName)) {
        if (!empty($where)) {
            $where .= " AND FIND_IN_SET('$brandName', brand_name)";
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

    $query = !empty($where) ? "SELECT * FROM purchasetable WHERE $where" : "SELECT * FROM purchasetable";

    // Debugging: Print the query to see what it looks like
    echo "Query: $query\n";

    $result = mysqli_query($conn, $query);

    if (!$result) {
        echo "Error executing query: " . mysqli_error($conn);
    }

    return $result;
}

//  like clause and wildcard

function getPurchaseListUsingLike($partyName, $brandName, $productName)
{
    $where = "";
    global $conn;

    if (!empty($partyName)) {
        $where .= "party_name ='$partyName'";
    }

    if (!empty($brandName)) {
        if (!empty($where)) {
            $where .= " AND brand_name LIKE '%$brandName%'";
        } else {
            $where = "brand_name LIKE '%$brandName%'";
        }
    }

    if (!empty($productName)) {
        if (!empty($where)) {
            $where .= " AND product_name LIKE '%$productName%'";
        } else {
            $where = "product_name LIKE '%$productName%'";
        }
    }

    $query = !empty($where) ? "SELECT * FROM purchasetable WHERE $where" : "SELECT * FROM purchasetable";
    $result = mysqli_query($conn, $query);

    return $result;
}
