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
require_once "../classes/Service.php";


/*
|--------------------------------------------------------------------------
| Database Connection
|--------------------------------------------------------------------------
*/
$database = new Database();
$db = $database->connect();

$service = new Service($db);


/*
|--------------------------------------------------------------------------
| Delete Service
|--------------------------------------------------------------------------
*/

if (isset($_GET["delete"])) {

    $id = intval($_GET["delete"]);

    if ($service->delete($id)) {
        header("Location: services.php?message=deleted");
        exit;
    }

    header("Location: services.php?message=error");
    exit;
}


/*
|--------------------------------------------------------------------------
| Add Service
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $price = trim($_POST["price"] ?? "");
    $duration = trim($_POST["duration"] ?? "");
    $image = trim($_POST["image"] ?? "");

    if (
        !empty($name) &&
        !empty($description) &&
        !empty($price) &&
        !empty($duration)
    ) {

        $service->name = $name;
        $service->description = $description;
        $service->price = $price;
        $service->duration = $duration;
        $service->image = $image;

        if ($service->create()) {
            header("Location: services.php?message=created");
            exit;
        }

    }

    header("Location: services.php?message=error");
    exit;
}


/*
|--------------------------------------------------------------------------
| Get All Services
|--------------------------------------------------------------------------
*/

$services = $service->getAll();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Manage Services | NAVA Fade Studio</title>


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

            background: #b8862c;

            color: #0e1423;

            border-radius: 8px;

            font-weight: bold;

        }


        /* =========================================
           SERVICES TABLE
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

            min-width: 900px;

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


        .service-image {

            width: 90px;

            height: 60px;

            object-fit: cover;

            border-radius: 8px;

            border: 2px solid #b8862c;

        }


        .price {

            color: #f0c34e;

            font-weight: bold;

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
                $_SESSION["admin_username"]
                ?? "Admin"
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


        <a
            href="services.php"
            class="active"
        >
            Services
        </a>


        <a href="bookings.php">
            Bookings
        </a>


        <a href="products.php">
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
                    Services
                </h2>

                <p>
                    Manage the services offered by
                    NAVA Fade Studio.
                </p>

            </div>


            <a
                href="#add-service"
                class="add-btn"
            >
                + Add Service
            </a>

        </div>



        <!-- =================================
             MESSAGE
             ================================= -->

        <?php if (isset($_GET["message"])): ?>

            <?php if ($_GET["message"] === "created"): ?>

                <div class="message">
                    Service added successfully!
                </div>

            <?php elseif ($_GET["message"] === "deleted"): ?>

                <div class="message">
                    Service deleted successfully!
                </div>

            <?php elseif ($_GET["message"] === "error"): ?>

                <div class="message">
                    Something went wrong.
                </div>

            <?php endif; ?>

        <?php endif; ?>



        <!-- =================================
             SERVICES TABLE
             ================================= -->

        <div class="table-container">

            <table>

                <thead>

                    <tr>

                        <th>
                            Image
                        </th>

                        <th>
                            Service
                        </th>

                        <th>
                            Description
                        </th>

                        <th>
                            Price
                        </th>

                        <th>
                            Duration
                        </th>

                        <th>
                            Actions
                        </th>

                    </tr>

                </thead>


                <tbody>

                    <?php if (!empty($services)): ?>

                        <?php foreach ($services as $item): ?>

                            <tr>


                                <td>

                                    <?php if (!empty($item["image"])): ?>

                                        <img
                                            src="../assets/images/<?= htmlspecialchars($item["image"]) ?>"
                                            alt="<?= htmlspecialchars($item["service_name"]) ?>"
                                            class="service-image"
                                        >

                                    <?php else: ?>

                                        No image

                                    <?php endif; ?>

                                </td>


                                <td>

                                    <strong>
                                        <?= htmlspecialchars(
                                            $item["service_name"]
                                        ) ?>
                                    </strong>

                                </td>


                                <td>

                                    <?= htmlspecialchars(
                                        $item["description"]
                                    ) ?>

                                </td>


                                <td class="price">

                                    ₱<?= number_format(
                                        (float)$item["price"],
                                        2
                                    ) ?>

                                </td>


                                <td>

                                    <?= htmlspecialchars(
                                        $item["duration"]
                                    ) ?>

                                </td>


                                <td>

                                    <a
                                        href="edit-service.php?id=<?= $item["id"] ?>"
                                        class="edit-btn"
                                    >
                                        Edit
                                    </a>


                                    <a
                                        href="services.php?delete=<?= $item["id"] ?>"
                                        class="delete-btn"
                                        onclick="return confirm('Are you sure you want to delete this service?');"
                                    >
                                        Delete
                                    </a>

                                </td>


                            </tr>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <tr>

                            <td
                                colspan="6"
                                style="text-align:center;"
                            >

                                No services found.

                            </td>

                        </tr>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>


    </main>

</div>


</body>

</html>