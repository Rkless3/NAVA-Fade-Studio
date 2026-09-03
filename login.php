<?php

session_start();

require_once "config/Database.php";
require_once "classes/Customer.php";

$database = new Database();
$db = $database->connect();

$customer = new Customer($db);

$error = "";


if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";


    if (empty($email) || empty($password)) {

        $error = "Please enter your email and password.";

    } else {

        $logged_customer = $customer->login(
            $email,
            $password
        );


        if ($logged_customer) {

            // Store customer information in session

            $_SESSION["customer_id"] =
                $logged_customer["id"];

            $_SESSION["customer_name"] =
                $logged_customer["full_name"];

            $_SESSION["customer_email"] =
                $logged_customer["email"];


            // Redirect to booking page

            header("Location: index.php");
            exit();

        } else {

            $error = "Invalid email or password.";

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

    <title>Login | NAVA Fade Studio</title>

    <link
        rel="stylesheet"
        href="assets/css/style.css"
    >

</head>

<body>

    <section class="booking-page">

        <div class="booking-container">

            <div class="booking-header">

                <h1>NAVA FADE STUDIO</h1>

                <h2>Welcome Back</h2>

                <p>
                    Login to your account to book an appointment.
                </p>

            </div>


            <?php if (!empty($error)): ?>

                <div class="error-message">

                    <?= htmlspecialchars($error) ?>

                </div>

            <?php endif; ?>


            <form method="POST" action="">

                <!-- EMAIL -->

                <div class="form-group">

                    <label for="email">
                        Email Address
                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="Enter your email address"
                        required
                    >

                </div>


                <!-- PASSWORD -->

                <div class="form-group">

                    <label for="password">
                        Password
                    </label>

                    <div class="password-wrapper">

                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Enter your password"
                            required
                        >

                        <button
                            type="button"
                            class="toggle-password"
                            data-target="password"
                        >
                            👁
                        </button>

                    </div>

                </div>


                <button
                    type="submit"
                    class="booking-btn"
                >
                    LOGIN
                </button>

            </form>


            <div class="booking-back">

                <p>
                    Don't have an account?

                    <a href="register.php">
                        Create one here
                    </a>
                </p>

                <br>

                <a href="index.php">
                    ← Back to Home
                </a>

            </div>

        </div>

    </section>

    <script>

    const passwordButtons =
        document.querySelectorAll(".toggle-password");

    passwordButtons.forEach(function(button) {

        button.addEventListener("click", function() {

            const targetId = button.dataset.target;

            const passwordInput =
                document.getElementById(targetId);

            if (passwordInput.type === "password") {

                passwordInput.type = "text";
                button.textContent = "🙈";

            } else {

                passwordInput.type = "password";
                button.textContent = "👁";

            }

        });

    });

    </script>

</body>

</html>