<?php

session_start();


/* =========================================
   ADMIN LOGIN CHECK
========================================= */

if (
    !isset($_SESSION["admin_logged_in"]) ||
    $_SESSION["admin_logged_in"] !== true
) {

    header("Location: login.php");
    exit();

}


require_once "../config/Database.php";

$database = new Database();
$db = $database->connect();


$success = "";
$error = "";


/* =========================================
   UPDATE BOOKING STATUS
========================================= */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $appointment_id = $_POST["appointment_id"] ?? "";
    $status = $_POST["status"] ?? "";


    $allowed_statuses = [
        "Pending",
        "Confirmed",
        "Completed",
        "Cancelled"
    ];


    if (
        empty($appointment_id) ||
        !in_array($status, $allowed_statuses)
    ) {

        $error = "Invalid booking status.";

    } else {

        try {

            $update = $db->prepare("
                UPDATE appointments
                SET status = :status
                WHERE id = :id
            ");


            $update->execute([

                ":status" => $status,

                ":id" => $appointment_id

            ]);


            $success =
                "Booking status updated successfully!";


        } catch (PDOException $e) {

            $error =
                "Unable to update booking status.";

        }

    }

}


/* =========================================
   GET ALL BOOKINGS
========================================= */

try {

    $query = $db->prepare("
        SELECT

            appointments.id,

            appointments.service,

            appointments.appointment_date,

            appointments.appointment_time,

            appointments.notes,

            appointments.status,

            appointments.created_at,

            customers.full_name,

            customers.email,

            customers.contact_number

        FROM appointments

        INNER JOIN customers
            ON appointments.customer_id = customers.id

        ORDER BY
            appointments.appointment_date DESC,
            appointments.appointment_time DESC
    ");


    $query->execute();


    $bookings = $query->fetchAll(
        PDO::FETCH_ASSOC
    );


} catch (PDOException $e) {

    $bookings = [];

    $error =
        "Unable to load bookings.";

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

    <title>
        Bookings | NAVA Fade Studio Admin
    </title>

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
              
                url("../assets/images/patterns.png");

            background-size: cover;
            background-position: center;

            color: #ffffff;
        }


        /* =========================================
           HEADER
           ========================================= */

        .admin-header {
            width: 100%;

            height: 80px;

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
     ADMIN HEADER
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


        <a
            href="dashboard.php">
            Dashboard
        </a>


        <a href="services.php">
            Services
        </a>


        <a 
        href="bookings.php"
        class="active">
            Bookings
        </a>


        <a href="#">
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

                Welcome,
                <?= htmlspecialchars(
                    $_SESSION["admin_username"]
                ?? "Admin"
                ) ?>

            </h2>


            <p>
                View customer appointments and update their booking status.
            </p>

        </div>



        <!-- SUCCESS MESSAGE -->

        <link
        rel="stylesheet"
        href="../assets/css/admin.css"
        >

        <?php if (!empty($success)): ?>

            <div class="admin-success">

                <?= htmlspecialchars($success) ?>

            </div>

        <?php endif; ?>



        <!-- ERROR MESSAGE -->

        <?php if (!empty($error)): ?>

            <div class="admin-error">

                <?= htmlspecialchars($error) ?>

            </div>

        <?php endif; ?>



        <!-- BOOKINGS -->

        <?php if (!empty($bookings)): ?>


            <div class="bookings-table-container">


                <table class="bookings-table">


                    <thead>

                        <tr>

                            <th>
                                Customer
                            </th>

                            <th>
                                Service
                            </th>

                            <th>
                                Date & Time
                            </th>

                            <th>
                                Notes
                            </th>

                            <th>
                                Status
                            </th>

                            <th>
                                Action
                            </th>

                        </tr>

                    </thead>



                    <tbody>


                        <?php foreach ($bookings as $booking): ?>


                            <tr>


                                <!-- CUSTOMER -->

                                <td>

                                    <strong>

                                        <?= htmlspecialchars(
                                            $booking["full_name"]
                                        ) ?>

                                    </strong>


                                    <span class="customer-contact">

                                        <?= htmlspecialchars(
                                            $booking["email"]
                                        ) ?>

                                    </span>


                                    <span class="customer-contact">

                                        <?= htmlspecialchars(
                                            $booking[
                                                "contact_number"
                                            ]
                                        ) ?>

                                    </span>

                                </td>



                                <!-- SERVICE -->

                                <td>

                                    <?= htmlspecialchars(
                                        $booking["service"]
                                    ) ?>

                                </td>



                                <!-- DATE & TIME -->

                                <td>

                                    <strong>

                                        <?= date(
                                            "M d, Y",
                                            strtotime(
                                                $booking[
                                                    "appointment_date"
                                                ]
                                            )
                                        ) ?>

                                    </strong>


                                    <br>


                                    <span class="booking-time">

                                        <?= date(
                                            "g:i A",
                                            strtotime(
                                                $booking[
                                                    "appointment_time"
                                                ]
                                            )
                                        ) ?>

                                    </span>

                                </td>



                                <!-- NOTES -->

                                <td class="booking-notes">

                                    <?php if (
                                        !empty(
                                            $booking["notes"]
                                        )
                                    ): ?>

                                        <?= htmlspecialchars(
                                            $booking["notes"]
                                        ) ?>

                                    <?php else: ?>

                                        <span class="no-notes">

                                            No notes

                                        </span>

                                    <?php endif; ?>

                                </td>



                                <!-- STATUS -->

                                <td>

                                    <span
                                        class="
                                            booking-status
                                            status-<?= strtolower(
                                                $booking["status"]
                                            ) ?>
                                        "
                                    >

                                        <?= htmlspecialchars(
                                            $booking["status"]
                                        ) ?>

                                    </span>

                                </td>



                                <!-- UPDATE -->

                                <td>


                                    <form
                                        method="POST"
                                        class="status-form"
                                    >


                                        <input
                                            type="hidden"
                                            name="appointment_id"
                                            value="<?= $booking["id"] ?>"
                                        >


                                        <select
                                            name="status"
                                            class="status-select"
                                        >


                                            <option
                                                value="Pending"
                                                <?= $booking["status"] === "Pending"
                                                    ? "selected"
                                                    : "" ?>
                                            >

                                                Pending

                                            </option>


                                            <option
                                                value="Confirmed"
                                                <?= $booking["status"] === "Confirmed"
                                                    ? "selected"
                                                    : "" ?>
                                            >

                                                Confirmed

                                            </option>


                                            <option
                                                value="Completed"
                                                <?= $booking["status"] === "Completed"
                                                    ? "selected"
                                                    : "" ?>
                                            >

                                                Completed

                                            </option>


                                            <option
                                                value="Cancelled"
                                                <?= $booking["status"] === "Cancelled"
                                                    ? "selected"
                                                    : "" ?>
                                            >

                                                Cancelled

                                            </option>


                                        </select>


                                        <button
                                            type="submit"
                                            class="update-status-btn"
                                        >

                                            Update

                                        </button>


                                    </form>


                                </td>


                            </tr>


                        <?php endforeach; ?>


                    </tbody>


                </table>


            </div>


        <?php else: ?>


            <div class="no-bookings">

                <h2>
                    No Bookings Yet
                </h2>


                <p>
                    Customer appointments will appear here.
                </p>

            </div>


        <?php endif; ?>


    </main>


</div>


</body>

</html>