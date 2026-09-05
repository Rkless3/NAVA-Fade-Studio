<?php

session_start();

/*
|--------------------------------------------------------------------------
| Check Admin Login
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION["admin_logged_in"]) || $_SESSION["admin_logged_in"] !== true) {
    header("Location: login.php");
    exit;
}


require_once "../config/Database.php";

$database = new Database();
$db = $database->connect();

$adminUsername = $_SESSION["admin_username"] ?? "Admin";

/* =========================================
   DASHBOARD STATISTICS
========================================= */

$reviewStmt = $db->query("
    SELECT COUNT(*) AS total_reviews
    FROM reviews
");

$totalReviews = (int) $reviewStmt->fetch()["total_reviews"];


/*
|--------------------------------------------------------------------------
| Database Connection
|--------------------------------------------------------------------------
*/

$database = new Database();
$db = $database->connect();

/*
|--------------------------------------------------------------------------
| Get Admin Username
|--------------------------------------------------------------------------
*/

$adminUsername = $_SESSION["admin_username"] ?? "Admin";

/*
|--------------------------------------------------------------------------
| Get Booking Count
|--------------------------------------------------------------------------
*/

$bookingCount = 0;

try {

    $stmt = $db->query("SELECT COUNT(*) FROM customers");
    $bookingCount = $stmt->fetchColumn();

} catch (PDOException $e) {

    $bookingCount = 0;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Dashboard | NAVA Fade Studio</title>

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
            font-size: 22px;

            color: #b8862c;

            letter-spacing: 1px;
        }


        .admin-user {
            display: flex;
            align-items: center;

            gap: 20px;
        }


        .admin-user span {
            color: #ffffff;

            font-size: 15px;
        }


        .logout-btn {
            padding: 10px 20px;

            background: transparent;

            color: #b8862c;

            border: 2px solid #b8862c;

            border-radius: 8px;

            text-decoration: none;

            font-weight: bold;

            transition: 0.3s ease;
        }


        .logout-btn:hover {
            background: #b8862c;

            color: #0e1423;
        }


        /* =========================================
           DASHBOARD LAYOUT
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

            border-right: 1px solid rgba(184, 134, 44, 0.5);
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

            border-radius: 10px;

            color: #ffffff;

            text-decoration: none;

            transition: 0.3s ease;
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


        .welcome {
            margin-bottom: 40px;
        }


        .welcome h2 {
            margin-bottom: 8px;

            font-size: 36px;

            color: #ffffff;
        }


        .welcome p {
            color: #aaa;

            font-size: 17px;
        }


        /* =========================================
           STAT CARDS
           ========================================= */

        .stats {
            display: grid;

            grid-template-columns:
                repeat(3, 1fr);

            gap: 25px;

            margin-bottom: 50px;
        }


        .stat-card {
            padding: 30px;

            background: #0e1423;

            border: 2px solid #b8862c;

            border-radius: 20px;

            transition: 0.3s ease;
        }


        .stat-card:hover {
            transform: translateY(-5px);

            box-shadow:
                0 10px 25px
                rgba(0, 0, 0, 0.3);
        }


        .stat-card h3 {
            margin-bottom: 15px;

            color: #aaa;

            font-size: 15px;

            text-transform: uppercase;

            letter-spacing: 1px;
        }


        .stat-card .number {
            color: #b8862c;

            font-size: 42px;

            font-weight: bold;
        }


        /* =========================================
           QUICK ACTIONS
           ========================================= */

        .section-title {
            margin-bottom: 25px;

            color: #ffffff;

            font-size: 25px;
        }


        .quick-actions {
            display: grid;

            grid-template-columns:
                repeat(3, 1fr);

            gap: 25px;
        }


        .action-card {
            padding: 30px;

            background: #0e1423;

            border: 1px solid #555;

            border-radius: 20px;

            text-decoration: none;

            color: #ffffff;

            transition: 0.3s ease;
        }


        .action-card:hover {
            border-color: #b8862c;

            transform: translateY(-5px);
        }


        .action-card h3 {
            margin-bottom: 10px;

            color: #b8862c;
        }


        .action-card p {
            color: #aaa;

            line-height: 1.5;
        }


        /* =========================================
           RESPONSIVE
           ========================================= */

        @media (max-width: 900px) {

            .sidebar {
                width: 200px;
            }

            .stats,
            .quick-actions {
                grid-template-columns: 1fr;
            }

            .main-content {
                padding: 30px;
            }

        }


        @media (max-width: 650px) {

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

            .welcome h2 {
                font-size: 28px;
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

        <a href="../logout.php" class="logout-btn">
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


        <a
            href="dashboard.php"
            class="active"
        >
            Dashboard
        </a>


        <a href="services.php">
            Services
        </a>


        <a href="bookings.php">
            Bookings
        </a>


        <a
            href="products.php">
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


    <!-- MAIN CONTENT -->

    <main class="main-content">


        <div class="welcome">

            <h2>
                Welcome back, <?= htmlspecialchars($adminUsername) ?>! 👋
            </h2>

            <p>
                Manage your NAVA Fade Studio website from here.
            </p>

        </div>


        <!-- STATISTICS -->

        <div class="stats">


            <div class="stat-card">

                <h3>
                    Services
                </h3>

                <div class="number">
                    8
                </div>

            </div>


            <div class="stat-card">

                <h3>
                    Bookings
                </h3>

                <div class="number">
                    <?= htmlspecialchars($bookingCount) ?>
                </div>

            </div>


            <div class="stat-card">

                <h3>
                    Reviews
                </h3>

                <div class="number">
                    <?= $totalReviews ?>
                </div>

            </div>


        </div>


        <!-- QUICK ACTIONS -->

        <h2 class="section-title">
            Quick Actions
        </h2>


        <div class="quick-actions">


            <a
                href="services.php"
                class="action-card"
            >

                <h3>
                    Manage Services
                </h3>

                <p>
                    Add, edit, or remove services
                    offered by NAVA Fade Studio.
                </p>

            </a>


            <a
                href="bookings.php"
                class="action-card"
            >

                <h3>
                    View Bookings
                </h3>

                <p>
                    Check and manage customer
                    appointment bookings.
                </p>

            </a>


            <a
                href="reviews.php"
                class="action-card"
            >

                <h3>
                    Manage Reviews
                </h3>

                <p>
                    View and manage customer
                    reviews and feedback.
                </p>

            </a>


        </div>


    </main>

</div>


</body>

</html>