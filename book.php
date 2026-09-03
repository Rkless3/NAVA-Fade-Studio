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


/* =========================================
   GET LOGGED-IN CUSTOMER
========================================= */

$customer_id = $_SESSION["customer_id"];

$customer_query = $db->prepare("
    SELECT
        full_name,
        email,
        contact_number
    FROM customers
    WHERE id = :id
    LIMIT 1
");

$customer_query->execute([
    ":id" => $customer_id
]);

$customer_data = $customer_query->fetch(PDO::FETCH_ASSOC);


/* =========================================
   GET SERVICES FROM DATABASE
========================================= */

$stmt = $db->prepare("
    SELECT
        id,
        service_name,
        description,
        price,
        duration
    FROM services
    ORDER BY id ASC
");

$stmt->execute();

$services = $stmt->fetchAll(PDO::FETCH_ASSOC);


$success = "";
$error = "";


/* =========================================
   HANDLE BOOKING
========================================= */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $selected_services = $_POST["services"] ?? [];

    $appointment_date =
        $_POST["appointment_date"] ?? "";

    $appointment_time =
        $_POST["appointment_time"] ?? "";

    $notes =
        trim($_POST["notes"] ?? "");


    /* =========================================
       VALIDATION
    ========================================= */

    if (
        empty($selected_services) ||
        empty($appointment_date) ||
        empty($appointment_time)
    ) {

        $error =
            "Please select at least one service, date, and time.";

    } else {

        try {

            /* =========================================
               GET SELECTED SERVICES
            ========================================= */

            $placeholders = implode(
                ",",
                array_fill(
                    0,
                    count($selected_services),
                    "?"
                )
            );


            $service_query = $db->prepare("
                SELECT
                    service_name,
                    price,
                    duration
                FROM services
                WHERE id IN ($placeholders)
            ");


            $service_query->execute(
                $selected_services
            );


            $selected_data =
                $service_query->fetchAll(
                    PDO::FETCH_ASSOC
                );


            /* =========================================
               COMBINE SERVICE NAMES
            ========================================= */

            $selected_service_names = [];


            foreach ($selected_data as $service) {

                $selected_service_names[] =
                    $service["service_name"];

            }


            $service_names = implode(
                ", ",
                $selected_service_names
            );


            /* =========================================
               INSERT APPOINTMENT
            ========================================= */

            $booking_query = $db->prepare("
                INSERT INTO appointments (

                    customer_id,
                    service,
                    appointment_date,
                    appointment_time,
                    notes,
                    status

                )

                VALUES (

                    :customer_id,
                    :service,
                    :appointment_date,
                    :appointment_time,
                    :notes,
                    'Pending'

                )
            ");


            $booking_query->execute([

                ":customer_id" =>
                    $customer_id,

                ":service" =>
                    $service_names,

                ":appointment_date" =>
                    $appointment_date,

                ":appointment_time" =>
                    $appointment_time,

                ":notes" =>
                    $notes

            ]);


            $success =
                "Your appointment has been booked successfully!";

        } catch (PDOException $e) {

            $error =
                "Unable to submit your booking. Please try again.";

        }

    }

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
        Book an Appointment | NAVA Fade Studio
    </title>


    <link
        rel="stylesheet"
        href="assets/css/style.css"
    >

</head>


<body>


<section class="booking-page">


    <div class="booking-container">


        <!-- =========================================
             BOOKING HEADER
        ========================================= -->

        <div class="booking-header">

            <h1>NAVA FADE STUDIO</h1>

            <h2>BOOK AN APPOINTMENT</h2>

            <p>
                Welcome,
                <?= htmlspecialchars(
                    $customer_data["full_name"]
                ) ?>!
                Reserve your time with us.
            </p>

        </div>



        <!-- =========================================
             SUCCESS MESSAGE
        ========================================= -->

        <?php if (!empty($success)): ?>

            <div class="success-message">

                <?= htmlspecialchars($success) ?>

            </div>

        <?php endif; ?>



        <!-- =========================================
             ERROR MESSAGE
        ========================================= -->

        <?php if (!empty($error)): ?>

            <div class="error-message">

                <?= htmlspecialchars($error) ?>

            </div>

        <?php endif; ?>



        <!-- =========================================
             BOOKING FORM
        ========================================= -->

        <form method="POST">


            <!-- =========================================
                 CUSTOMER INFORMATION
            ========================================= -->

            <div class="customer-booking-info">

                <h3>
                    Your Information
                </h3>


                <p>

                    <strong>Name:</strong>

                    <?= htmlspecialchars(
                        $customer_data["full_name"]
                    ) ?>
                    <strong><br></estrong>


                    <strong>Email:</strong>

                    <?= htmlspecialchars(
                        $customer_data["email"]
                    ) ?>
                    <strong><br></estrong>

           
                    <strong>Contact:</strong>

                    <?= htmlspecialchars(
                        $customer_data["contact_number"]
                    ) ?>
                    <strong><br></estrong>

                </p>

            </div>



            <!-- =========================================
                 SELECT SERVICES
            ========================================= -->

            <div class="form-group">

                <label>
                    Select Service(s)
                </label>


                <p class="service-selection-note">

                    You may select more than one service.

                </p>


                <div class="booking-services">


                    <?php foreach ($services as $service_item): ?>


                        <label class="booking-service-card">


                            <!-- CHECKBOX -->

                            <input
                                type="checkbox"
                                name="services[]"
                                value="<?= $service_item['id'] ?>"
                                class="service-checkbox"
                                data-name="<?= htmlspecialchars(
                                    $service_item['service_name']
                                ) ?>"
                                data-price="<?= $service_item['price'] ?>"
                            >



                            <!-- SERVICE INFO -->

                            <div class="booking-service-info">

                                    <h3>

                                        <?= htmlspecialchars(
                                            $service_item['service_name']
                                        ) ?>

                                    </h3>


                                    <span class="service-price">

                                        ₱<?= number_format(
                                            $service_item['price'],
                                            0
                                        ) ?>

                                    </span>


                                <span class="service-duration">

                                    <?= htmlspecialchars(
                                        $service_item['duration']
                                    ) ?>

                                </span>


                            </div>


                        </label>


                    <?php endforeach; ?>


                </div>


            </div>



            <!-- =========================================
                 SELECTED SERVICES SUMMARY
            ========================================= -->

            <div class="selected-services-summary">


                <h3>
                    Selected Services
                </h3>


                <div
                    id="selectedServices"
                    class="selected-services-list"
                >

                    <p class="no-service">

                        No service selected yet.

                    </p>

                </div>



                <div class="booking-total">


                    <span>
                        Total
                    </span>


                    <strong id="totalPrice">
                        ₱0
                    </strong>


                </div>


            </div>



            <!-- =========================================
                 DATE AND TIME
            ========================================= -->

            <div class="booking-row">


                <div class="form-group">


                    <label for="appointment_date">
                        Date
                    </label>


                    <input
                        type="date"
                        id="appointment_date"
                        name="appointment_date"
                        min="<?= date('Y-m-d') ?>"
                        required
                    >


                </div>



                <div class="form-group">


                    <label for="appointment_time">
                        Time
                    </label>


                    <input
                        type="time"
                        id="appointment_time"
                        name="appointment_time"
                        required
                    >


                </div>


            </div>



            <!-- =========================================
                 NOTES
            ========================================= -->

            <div class="form-group">


                <label for="notes">
                    Additional Notes
                </label>


                <textarea
                    id="notes"
                    name="notes"
                    rows="4"
                    placeholder="Optional"
                ></textarea>


            </div>



            <!-- BOOK BUTTON -->

            <button
                type="submit"
                class="booking-btn"
            >

                BOOK NOW

            </button>


        </form>



        <!-- BACK BUTTON -->

        <div class="booking-back">

            <a href="index.php">

                ← Back to Home

            </a>

        </div>


    </div>


</section>



<!-- =========================================
     SERVICE SELECTION JAVASCRIPT
========================================= -->

<script>

const serviceCheckboxes =
    document.querySelectorAll(
        ".service-checkbox"
    );


const selectedServices =
    document.getElementById(
        "selectedServices"
    );


const totalPrice =
    document.getElementById(
        "totalPrice"
    );



function updateSelectedServices() {


    let total = 0;

    let selectedHTML = "";

    let selectedCount = 0;



    serviceCheckboxes.forEach(
        function(checkbox) {


            if (checkbox.checked) {


                const name =
                    checkbox.dataset.name;


                const price =
                    parseFloat(
                        checkbox.dataset.price
                    );


                total += price;

                selectedCount++;


                selectedHTML += `

                    <div class="selected-service-item">

                        <span>
                            ${name}
                        </span>


                        <strong>
                            ₱${price.toFixed(0)}
                        </strong>


                    </div>

                `;

            }


        }
    );



    if (selectedCount === 0) {


        selectedServices.innerHTML = `

            <p class="no-service">

                No service selected yet.

            </p>

        `;


    } else {


        selectedServices.innerHTML =
            selectedHTML;

    }



    totalPrice.textContent =
        "₱" + total.toFixed(0);


}



/* =========================================
   CHECKBOX EVENT LISTENERS
========================================= */

serviceCheckboxes.forEach(
    function(checkbox) {


        checkbox.addEventListener(
            "change",
            updateSelectedServices
        );


    }
);

</script>


</body>

</html>