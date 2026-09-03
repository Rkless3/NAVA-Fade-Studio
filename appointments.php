<?php

session_start();


/* =========================================
   CHECK IF CUSTOMER IS LOGGED IN
========================================= */

if (!isset($_SESSION["customer_id"])) {

    header("Location: login.php");
    exit();

}


require_once "config/Database.php";

$database = new Database();
$db = $database->connect();


$customer_id = $_SESSION["customer_id"];


/* =========================================
   GET CUSTOMER APPOINTMENTS
========================================= */

try {

    $appointment_query = $db->prepare("
        SELECT
            id,
            service,
            appointment_date,
            appointment_time,
            notes,
            status,
            created_at

        FROM appointments

        WHERE customer_id = :customer_id

        ORDER BY
            appointment_date DESC,
            appointment_time DESC
    ");


    $appointment_query->execute([

        ":customer_id" => $customer_id

    ]);


    $appointments =
        $appointment_query->fetchAll(PDO::FETCH_ASSOC);


} catch (PDOException $e) {

    $appointments = [];

    $error =
        "Unable to load your appointments.";

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
        My Appointments | NAVA Fade Studio
    </title>


    <link
        rel="stylesheet"
        href="assets/css/style.css"
    >

</head>


<body>


<section class="booking-page">


    <div class="booking-container appointments-container">


        <!-- HEADER -->

        <div class="booking-header">

            <h1>
                NAVA FADE STUDIO
            </h1>


            <h2>
                MY APPOINTMENTS
            </h2>


            <p>
                View and manage your appointment bookings.
            </p>

        </div>



        <!-- ERROR MESSAGE -->

        <?php if (!empty($error)): ?>

            <div class="error-message">

                <?= htmlspecialchars($error) ?>

            </div>

        <?php endif; ?>



        <!-- APPOINTMENTS -->

        <?php if (!empty($appointments)): ?>


            <div class="appointments-list">


                <?php foreach ($appointments as $appointment): ?>


                    <div class="appointment-card">


                        <div class="appointment-top">


                            <h3>

                                <?= htmlspecialchars(
                                    $appointment["service"]
                                ) ?>

                            </h3>


                            <span
                                class="
                                    appointment-status
                                    status-<?= strtolower(
                                        $appointment["status"]
                                    ) ?>
                                "
                            >

                                <?= htmlspecialchars(
                                    $appointment["status"]
                                ) ?>

                            </span>


                        </div>



                        <div class="appointment-details">


                            <p>

                                📅

                                <strong>Date:</strong>

                                <?= date(
                                    "F j, Y",
                                    strtotime(
                                        $appointment[
                                            "appointment_date"
                                        ]
                                    )
                                ) ?>

                            </p>


                            <p>

                                🕒

                                <strong>Time:</strong>

                                <?= date(
                                    "g:i A",
                                    strtotime(
                                        $appointment[
                                            "appointment_time"
                                        ]
                                    )
                                ) ?>

                            </p>


                            <?php if (
                                !empty(
                                    $appointment["notes"]
                                )
                            ): ?>


                                <p class="appointment-notes">

                                    <strong>
                                        Notes:
                                    </strong>

                                    <?= htmlspecialchars(
                                        $appointment["notes"]
                                    ) ?>

                                </p>


                            <?php endif; ?>


                        </div>


                    </div>


                <?php endforeach; ?>


            </div>


        <?php else: ?>


            <!-- NO APPOINTMENTS -->

            <div class="no-appointments">


                <div class="no-appointments-icon">

                    📅

                </div>


                <h3>
                    No Appointments Yet
                </h3>


                <p>

                    You haven't booked an appointment yet.

                </p>


                <a
                    href="book.php"
                    class="booking-btn appointment-book-btn"
                >

                    BOOK AN APPOINTMENT

                </a>


            </div>


        <?php endif; ?>



        <!-- BACK -->

        <div class="booking-back">


            <a href="index.php">

                ← Back to Home

            </a>


        </div>


    </div>


</section>


</body>

</html>