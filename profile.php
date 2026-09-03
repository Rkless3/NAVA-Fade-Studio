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

$success = "";
$error = "";


/* =========================================
   UPDATE PROFILE
========================================= */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $full_name = trim($_POST["full_name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $contact_number = trim($_POST["contact_number"] ?? "");


    if (
        empty($full_name) ||
        empty($email) ||
        empty($contact_number)
    ) {

        $error = "Please fill in all fields.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = "Please enter a valid email address.";

    } else {

        try {

            /* Check if email belongs to another customer */

            $email_check = $db->prepare("
                SELECT id
                FROM customers
                WHERE email = :email
                AND id != :id
                LIMIT 1
            ");


            $email_check->execute([

                ":email" => $email,

                ":id" => $customer_id

            ]);


            if ($email_check->fetch()) {

                $error =
                    "This email address is already being used.";

            } else {

                /* Update customer profile */

                $update_query = $db->prepare("
                    UPDATE customers
                    SET

                        full_name = :full_name,
                        email = :email,
                        contact_number = :contact_number

                    WHERE id = :id
                ");


                $update_query->execute([

                    ":full_name" => $full_name,

                    ":email" => $email,

                    ":contact_number" => $contact_number,

                    ":id" => $customer_id

                ]);


                /* Update session */

                $_SESSION["customer_name"] = $full_name;

                $_SESSION["customer_email"] = $email;


                $success =
                    "Your profile has been updated successfully!";

            }

        } catch (PDOException $e) {

            $error =
                "Unable to update your profile. Please try again.";

        }

    }

}


/* =========================================
   GET CUSTOMER DATA
========================================= */

$customer_query = $db->prepare("
    SELECT

        full_name,
        email,
        contact_number,
        created_at

    FROM customers

    WHERE id = :id

    LIMIT 1
");


$customer_query->execute([

    ":id" => $customer_id

]);


$customer = $customer_query->fetch(PDO::FETCH_ASSOC);


/* =========================================
   SAFETY CHECK
========================================= */

if (!$customer) {

    session_destroy();

    header("Location: login.php");
    exit();

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
        My Profile | NAVA Fade Studio
    </title>


    <link
        rel="stylesheet"
        href="assets/css/style.css"
    >

</head>


<body>


<section class="booking-page">


    <div class="booking-container">


        <!-- PROFILE HEADER -->

        <div class="booking-header">

            <h1>
                NAVA FADE STUDIO
            </h1>


            <h2>
                MY PROFILE
            </h2>


            <p>
                Manage your account information.
            </p>

        </div>



        <!-- SUCCESS MESSAGE -->

        <?php if (!empty($success)): ?>

            <div class="success-message">

                <?= htmlspecialchars($success) ?>

            </div>

        <?php endif; ?>



        <!-- ERROR MESSAGE -->

        <?php if (!empty($error)): ?>

            <div class="error-message">

                <?= htmlspecialchars($error) ?>

            </div>

        <?php endif; ?>



        <!-- PROFILE FORM -->

        <form method="POST">


            <!-- FULL NAME -->

            <div class="form-group">

                <label for="full_name">

                    Full Name

                </label>


                <input
                    type="text"
                    id="full_name"
                    name="full_name"
                    value="<?= htmlspecialchars(
                        $customer["full_name"]
                    ) ?>"
                    required
                >

            </div>



            <!-- EMAIL -->

            <div class="form-group">

                <label for="email">

                    Email Address

                </label>


                <input
                    type="email"
                    id="email"
                    name="email"
                    value="<?= htmlspecialchars(
                        $customer["email"]
                    ) ?>"
                    required
                >

            </div>



            <!-- CONTACT NUMBER -->

            <div class="form-group">

                <label for="contact_number">

                    Contact Number

                </label>


                <input
                    type="tel"
                    id="contact_number"
                    name="contact_number"
                    value="<?= htmlspecialchars(
                        $customer["contact_number"]
                    ) ?>"
                    required
                >

            </div>



            <!-- MEMBER SINCE -->

            <div class="profile-member-info">

                <span>
                    Member since:
                </span>

                <strong>

                    <?= date(
                        "F j, Y",
                        strtotime(
                            $customer["created_at"]
                        )
                    ) ?>

                </strong>

            </div>



            <!-- UPDATE BUTTON -->

            <button
                type="submit"
                class="booking-btn"
            >

                UPDATE PROFILE

            </button>


        </form>



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