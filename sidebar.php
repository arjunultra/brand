<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sidebar</title>
    <link rel="stylesheet" href="./bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <button class="btn btn-primary mt-5 ms-5" type="button" data-bs-toggle="offcanvas"
        data-bs-target="#offcanvasWithBothOptions" aria-controls="offcanvasWithBothOptions">Menu</button>

    <div class="offcanvas offcanvas-start" data-bs-scroll="true" tabindex="-1" id="offcanvasWithBothOptions"
        aria-labelledby="offcanvasWithBothOptionsLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title text-center" id="offcanvasWithBothOptionsLabel">Navigation Menu</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <div class="container-fluid">
                <ul class="d-flex flex-column justify-content-evenly gap-3 align-items-center list-unstyled">
                    <li class=""><a class="text-decoration-none text-uppercase" href="brands-form.php">Brands</a></li>
                    <li class=""><a class="text-decoration-none text-uppercase" href="products-form.php">Products</a>
                    </li>
                    <li class=""><a class="text-decoration-none text-uppercase" href="party-form.php">Parties</a></li>
                    <li class=""><a class="text-decoration-none text-uppercase" href="puchase_form.php">Purchase</a>
                    </li>
                    <li class=""><a class="text-decoration-none text-uppercase" href="reports.php">Reports</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <script src="./JS/bootstrap.bundle.min.js"></script>
</body>

</html>