<?php

session_start();


/*
|--------------------------------------------------------------------------
| Check Admin Login
|--------------------------------------------------------------------------
*/

if (
    !isset($_SESSION["admin_logged_in"]) ||
    $_SESSION["admin_logged_in"] !== true
) {
    header("Location: login.php");
    exit;
}


require_once "../config/Database.php";
require_once "../classes/Product.php";


/*
|--------------------------------------------------------------------------
| Database Connection
|--------------------------------------------------------------------------
*/

$database = new Database();
$db = $database->connect();

$product = new Product($db);


/*
|--------------------------------------------------------------------------
| Delete Product
|--------------------------------------------------------------------------
*/

if (isset($_GET["delete"])) {

    $id = intval($_GET["delete"]);

    if ($product->delete($id)) {
        header("Location: products.php?message=deleted");
        exit;
    }

    header("Location: products.php?message=error");
    exit;
}


/*
|--------------------------------------------------------------------------
| Add Product
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $price = trim($_POST["price"] ?? "");
    $stock = trim($_POST["stock"] ?? "");
    $status = trim($_POST["status"] ?? "Active");

    $image = "";


    /*
    |--------------------------------------------------------------------------
    | Upload Product Image
    |--------------------------------------------------------------------------
    */

    if (
        isset($_FILES["image"]) &&
        $_FILES["image"]["error"] === UPLOAD_ERR_OK
    ) {

        $file = $_FILES["image"];

        $originalName = $file["name"];
        $tmpName = $file["tmp_name"];

        $extension = strtolower(
            pathinfo($originalName, PATHINFO_EXTENSION)
        );


        $allowedExtensions = [
            "jpg",
            "jpeg",
            "png",
            "webp"
        ];


        if (!in_array($extension, $allowedExtensions, true)) {

            header("Location: products.php?message=invalid_image");
            exit;
        }


        /*
        |--------------------------------------------------------------------------
        | Create Unique Filename
        |--------------------------------------------------------------------------
        */

        $image = uniqid("product_", true) . "." . $extension;


        /*
        |--------------------------------------------------------------------------
        | Image Destination
        |--------------------------------------------------------------------------
        */

        $uploadPath =
            "../assets/images/" . $image;


        if (!move_uploaded_file($tmpName, $uploadPath)) {

            header("Location: products.php?message=upload_error");
            exit;
        }

    }


    /*
    |--------------------------------------------------------------------------
    | Validate Product Information
    |--------------------------------------------------------------------------
    */

    if (
        !empty($name) &&
        !empty($description) &&
        $price !== "" &&
        $stock !== "" &&
        !empty($image)
    ) {

        $product->product_name = $name;
        $product->description = $description;
        $product->price = (float) $price;
        $product->stock = (int) $stock;
        $product->image = $image;
        $product->status = $status;


        if ($product->create()) {

            header(
                "Location: products.php?message=created"
            );

            exit;
        }

    }


    header(
        "Location: products.php?message=error"
    );

    exit;
}




/*
|--------------------------------------------------------------------------
| Get All Products
|--------------------------------------------------------------------------
*/

$products = $product->getAll();

?>


<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Manage Products | NAVA Fade Studio</title>


    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }


        body {

            min-height: 100vh;

            font-family: Bahnschrift, Myriad Pro;

            background:
                url("../assets/images/pattern3.png");

            background-size: cover;

            background-position: center;

            color: #ffffff;

        }


        /* =========================================
           HEADER
        ========================================= */

        .admin-header {

            width: 100%;

            height: 95px;

            display: flex;
            align-items: center;
            justify-content: space-between;

            padding: 0 50px;

            background: #0e1423;

            border-bottom: 2px solid #b8862c;

            position: sticky;
            top: 0;

            z-index: 1000;

        }


        .admin-logo {

            display: flex;

            align-items: center;

            gap: 15px;

        }


        .admin-logo img {

            width: 165px;

            height: 165px;

            object-fit: contain;

        }


        .admin-logo h1 {

            color: #b8862c;

            font-size: 22px;

            letter-spacing: 1px;

        }


        .admin-user {

            display: flex;

            align-items: center;

            gap: 20px;

        }


        .admin-user span {

            font-size: 15px;

        }


        .logout-btn {

            padding: 10px 20px;

            color: #b8862c;

            border: 2px solid #b8862c;

            border-radius: 8px;

            text-decoration: none;

            font-weight: bold;

        }


        .logout-btn:hover {

            background: #b8862c;

            color: #0e1423;

        }


        /* =========================================
           LAYOUT
        ========================================= */

        .dashboard {

            display: flex;

            min-height: calc(100vh - 80px);

        }


        /* =========================================
           SIDEBAR
        ========================================= */

        .sidebar {

            width: 250px;

            padding: 35px 20px;

            background: rgba(14, 20, 35, 0.95);

            border-right: 1px solid
                rgba(184, 134, 44, 0.5);

        }


        .sidebar-title {

            margin-bottom: 25px;

            padding-left: 15px;

            color: #888;

            font-size: 13px;

            text-transform: uppercase;

            letter-spacing: 2px;

        }


        .sidebar a {

            display: block;

            padding: 15px 18px;

            margin-bottom: 8px;

            color: #ffffff;

            text-decoration: none;

            border-radius: 10px;

            transition: 0.3s;

        }


        .sidebar a:hover,
        .sidebar a.active {

            background: #b8862c;

            color: #0e1423;

        }


        /* =========================================
           MAIN CONTENT
        ========================================= */

        .main-content {

            flex: 1;

            padding: 50px;

        }


        .page-header {

            display: flex;

            align-items: center;

            justify-content: space-between;

            margin-bottom: 35px;

        }


        .page-header h2 {

            font-size: 36px;

        }


        .page-header p {

            margin-top: 8px;

            color: #aaa;

        }


        .add-btn {

            padding: 13px 25px;

            background: #b8862c;

            color: #0e1423;

            border: none;

            border-radius: 8px;

            font-weight: bold;

            text-decoration: none;

            cursor: pointer;

        }


        .add-btn:hover {

            background: #d4a33a;

            transform: translateY(-2px);

        }


        /* =========================================
           MESSAGE
        ========================================= */

        .message {

            padding: 15px 20px;

            margin-bottom: 25px;
            background: rgba(46, 204, 113, 0.12);

            color: #2ecc71;

            border: 1px solid #2ecc71;

            border-radius: 8px;

            font-weight: bold;

        }


        /* =========================================
           PRODUCTS TABLE
        ========================================= */

        .table-container {

            width: 100%;

            overflow-x: auto;

            background: #0e1423;

            border: 1px solid #555;

            border-radius: 15px;

        }


        table {

            width: 100%;

            border-collapse: collapse;

            min-width: 1000px;

        }


        th {

            padding: 18px;

            text-align: left;

            background: #b8862c;

            color: #0e1423;

        }


        td {

            padding: 18px;

            border-bottom: 1px solid #333;

            vertical-align: middle;

        }


        tr:last-child td {

            border-bottom: none;

        }


        .product-image {

            width: 80px;

            height: 80px;

            object-fit: contain;

            background: #ffffff;

            border-radius: 8px;

            border: 2px solid #b8862c;

            padding: 5px;

        }


        .price {

            color: #f0c34e;

            font-weight: bold;

        }


        .stock {

            font-weight: bold;

        }


        .in-stock {

            color: #6fcf97;

        }


        .low-stock {

            color: #f0c34e;

        }


        .out-of-stock {

            color: #e57373;

        }


        /* =========================================
           STATUS
        ========================================= */

        .status {

            display: inline-block;

            padding: 6px 12px;

            border-radius: 20px;

            font-size: 12px;

            font-weight: bold;

        }


        .status-active {

            background: rgba(111, 207, 151, 0.15);

            color: #6fcf97;

        }


        .status-inactive {

            background: rgba(229, 115, 115, 0.15);

            color: #e57373;

        }


        /* =========================================
           ACTION BUTTONS
        ========================================= */

        .edit-btn,
        .delete-btn {

            display: inline-block;

            padding: 8px 14px;

            border-radius: 6px;

            text-decoration: none;

            font-size: 13px;

            font-weight: bold;

        }


        .edit-btn {

            background: #b8862c;

            color: #fff;

        }


        .delete-btn {

            background: #8b3030;

            color: #ffffff;

        }


        .edit-btn:hover,
        .delete-btn:hover {

            opacity: 0.8;

            color: #ffffff;

        }


        /* =========================================
           EMPTY STATE
        ========================================= */

        .empty-message {

            padding: 40px;

            text-align: center;

            color: #aaa;

        }
        
        /* =========================================
        ADD PRODUCT FORM
        ========================================= */

        .add-product-section {

            margin-top: 50px;

            padding: 35px;

            background: #0e1423;

            border: 1px solid #555;

            border-radius: 15px;

        }       


        .add-product-header {

            margin-bottom: 30px;

        }


        .add-product-header h2 {

            font-size: 28px;

            margin-bottom: 8px;

        }


        .add-product-header p {

            color: #aaa;

        }


        /* FORM */

        .product-form {

            max-width: 850px;

        }


        .form-group {

            display: flex;

            flex-direction: column;

            margin-bottom: 20px;

        }


        .form-group label {

            margin-bottom: 8px;

            color: #ffffff;

            font-weight: bold;

        }


        .form-group input,
        .form-group textarea,
        .form-group select {

            width: 100%;

            padding: 13px 15px;

            background: #151c2d;

            color: #ffffff;

            border: 1px solid #555;

            border-radius: 8px;

            font-family: inherit;

            font-size: 14px;

            outline: none;

        }


        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {

            border-color: #b8862c;

        }


        .form-group textarea {

            resize: vertical;

        }


        .form-group small {

            margin-top: 7px;

            color: #888;

        }


        .form-row {

            display: grid;

            grid-template-columns: 1fr 1fr;

            gap: 20px;

        }


        .save-product-btn {

            padding: 13px 25px;

            background: #b8862c;

            color: #0e1423;

            border: none;

            border-radius: 8px;

            font-weight: bold;

            cursor: pointer;

            transition: 0.3s ease;

        }


        .save-product-btn:hover {

            background: #d4a33a;

            transform: translateY(-2px);

        }


        /* =========================================
           RESPONSIVE
        ========================================= */

        @media (max-width: 800px) {

            .admin-header {

                padding: 0 20px;

            }


            .admin-logo h1 {

                display: none;

            }


            .admin-user span {

                display: none;

            }


            .dashboard {

                flex-direction: column;

            }


            .sidebar {

                width: 100%;

                display: flex;

                gap: 5px;

                overflow-x: auto;

                padding: 15px;

            }


            .sidebar-title {

                display: none;

            }


            .sidebar a {

                white-space: nowrap;

                margin: 0;

            }


            .main-content {

                padding: 25px 20px;

            }


            .page-header {

                flex-direction: column;

                align-items: flex-start;

                gap: 20px;

            }

            .form-row {
                grid-template-columns: 1fr;
            }

        }

    </style>

</head>


<body>


<!-- =========================================
     HEADER
========================================= -->

<header class="admin-header">

    <div class="admin-logo">

        <img
            src="../assets/images/logo.png"
            alt="NAVA Fade Studio"
        >

    </div>


    <div class="admin-user">

        <span>

            Welcome,
            <?= htmlspecialchars(
                $_SESSION["admin_username"] ?? "Admin"
            ) ?>

        </span>


        <a
            href="logout.php"
            class="logout-btn"
        >
            Logout
        </a>

    </div>

</header>



<!-- =========================================
     DASHBOARD
========================================= -->

<div class="dashboard">


    <!-- SIDEBAR -->

    <aside class="sidebar">

        <div class="sidebar-title">
            Admin Panel
        </div>


        <a href="dashboard.php">
            Dashboard
        </a>


        <a href="services.php">
            Services
        </a>


        <a href="bookings.php">
            Bookings
        </a>


        <a
            href="products.php"
            class="active"
        >
            Products
        </a>


        <a href="orders.php">
            Orders
        </a>


        <a href="reviews.php">
            Reviews
        </a>


        <a href="#">
            Settings
        </a>

    </aside>



    <!-- =====================================
         MAIN CONTENT
    ===================================== -->

    <main class="main-content">


        <div class="page-header">

            <div>

                <h2>
                    Products
                </h2>

                <p>
                    Manage the products sold by
                    NAVA Fade Studio.
                </p>

            </div>

        </div>



        <!-- =================================
             MESSAGE
        ================================= -->

        <?php if (isset($_GET["message"])): ?>

            <?php if ($_GET["message"] === "created"): ?>

                <div class="message">
                    Product added successfully!
                </div>


            <?php elseif ($_GET["message"] === "deleted"): ?>

                <div class="message">
                    Product deleted successfully!
                </div>


            <?php elseif ($_GET["message"] === "invalid_image"): ?>

                <div class="message">
                    Invalid image format.
                    Please use JPG, JPEG, PNG, or WEBP.
                </div>


            <?php elseif ($_GET["message"] === "upload_error"): ?>

                <div class="message">
                    Image upload failed.
                    Please try again.
                </div>


            <?php elseif ($_GET["message"] === "error"): ?>

                <div class="message">
                    Something went wrong.
                </div>

            <?php endif; ?>

        <?php endif; ?>



        <!-- =================================
             PRODUCTS TABLE
        ================================= -->

        <div class="table-container">

            <table>

                <thead>

                    <tr>

                        <th>
                            Image
                        </th>

                        <th>
                            Product
                        </th>

                        <th>
                            Description
                        </th>

                        <th>
                            Price
                        </th>

                        <th>
                            Stock
                        </th>

                        <th>
                            Status
                        </th>

                        <th>
                            Actions
                        </th>

                    </tr>

                </thead>


                <tbody>

                    <?php if (!empty($products)): ?>

                        <?php foreach ($products as $item): ?>

                            <tr>


                                <!-- IMAGE -->

                                <td>

                                    <?php if (!empty($item["image"])): ?>

                                        <img
                                            src="../assets/images/<?= htmlspecialchars($item["image"]) ?>"
                                            alt="<?= htmlspecialchars($item["product_name"]) ?>"
                                            class="product-image"
                                        >

                                    <?php else: ?>

                                        No image

                                    <?php endif; ?>

                                </td>



                                <!-- PRODUCT -->

                                <td>

                                    <strong>
                                        <?= htmlspecialchars(
                                            $item["product_name"]
                                        ) ?>
                                    </strong>

                                </td>



                                <!-- DESCRIPTION -->

                                <td>

                                    <?= htmlspecialchars(
                                        $item["description"]
                                    ) ?>

                                </td>



                                <!-- PRICE -->

                                <td class="price">

                                    ₱<?= number_format(
                                        (float)$item["price"],
                                        2
                                    ) ?>

                                </td>



                                <!-- STOCK -->

                                <td class="stock">

                                    <?php

                                    $stock = (int)$item["stock"];

                                    if ($stock <= 0) {

                                        echo '<span class="out-of-stock">
                                                Out of stock
                                              </span>';

                                    } elseif ($stock <= 5) {

                                        echo '<span class="low-stock">'
                                            . $stock .
                                            ' left</span>';

                                    } else {

                                        echo '<span class="in-stock">'
                                            . $stock .
                                            '</span>';

                                    }

                                    ?>

                                </td>



                                <!-- STATUS -->

                                <td>

                                    <?php if ($item["status"] === "Active"): ?>

                                        <span class="status status-active">
                                            Active
                                        </span>

                                    <?php else: ?>

                                        <span class="status status-inactive">
                                            Inactive
                                        </span>

                                    <?php endif; ?>

                                </td>



                                <!-- ACTIONS -->

                                <td>

                                    <a
                                        href="edit-product.php?id=<?= $item["id"] ?>"
                                        class="edit-btn"
                                    >
                                        Edit
                                    </a>


                                    <a
                                        href="products.php?delete=<?= $item["id"] ?>"
                                        class="delete-btn"
                                        onclick="return confirm('Are you sure you want to delete this product?');"
                                    >
                                        Delete
                                    </a>

                                </td>


                            </tr>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <tr>

                            <td
                                colspan="7"
                                class="empty-message"
                            >

                                No products found.

                            </td>

                        </tr>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

        <!-- =================================
        ADD PRODUCT FORM
        ================================= -->

        <section
            class="add-product-section"
            id="add-product"
        >

            <div class="add-product-header">

                <h2>
                    Add New Product
                </h2>

                <p>
                    Add a new product to the NAVA Fade Studio shop.
                </p>

            </div>


            <form
                method="POST"
                class="product-form"
                enctype="multipart/form-data"
            >


                <!-- PRODUCT NAME -->

                <div class="form-group">

                    <label for="name">
                        Product Name
                    </label>

                    <input
                        type="text"
                        id="name"
                        name="name"
                        placeholder="e.g. NAVA Hair Wax"
                        required
                    >

                </div>



                <!-- DESCRIPTION -->

                <div class="form-group">

                    <label for="description">
                        Description
                    </label>

                    <textarea
                        id="description"
                        name="description"
                        rows="4"
                        placeholder="Enter product description..."
                        required
                    ></textarea>

                </div>



                <!-- PRICE -->

                <div class="form-row">

                    <div class="form-group">

                        <label for="price">
                            Price
                        </label>

                        <input
                            type="number"
                            id="price"
                            name="price"
                            min="0"
                            step="0.01"
                            placeholder="250.00"
                            required
                        >

                    </div>



                    <!-- STOCK -->

                    <div class="form-group">

                        <label for="stock">
                            Stock
                        </label>

                        <input
                            type="number"
                            id="stock"
                            name="stock"
                            min="0"
                            placeholder="20"
                            required
                        >

                    </div>

                </div>



                <!-- IMAGE -->

                <div class="form-group">

                    <label for="image">
                        Product Image
                    </label>

                    <input
                        type="file"
                        id="image"
                        name="image"
                        accept="image/png, image/jpeg, image/webp"
                        required
                    >

                    <small>
                        Choose a product image from your computer.
                    </small>

                </div>



                <!-- STATUS -->

                <div class="form-group">

                    <label for="status">
                        Status
                    </label>

                    <select
                        id="status"
                        name="status"
                    >

                        <option value="Active">
                            Active
                        </option>

                        <option value="Inactive">
                            Inactive
                        </option>

                    </select>

                </div>



                <!-- SUBMIT -->

                <button
                    type="submit"
                    class="save-product-btn"
                >
                    + Add Product
                </button>


            </form>

        </section>


    </main>

</div>


</body>

</html>