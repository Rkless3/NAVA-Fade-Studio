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


            // Redirect after successful login
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


    <!-- =========================================
         FONT AWESOME
    ========================================== -->

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >


    <style>

        /* =========================================
           RESET
        ========================================= */

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }


        /* =========================================
           PAGE
        ========================================= */

        body {

            min-height: 100vh;

            display: flex;

            align-items: center;

            justify-content: center;

            padding: 30px 20px;

            font-family:
                Bahnschrift,
                "Myriad Pro",
                Arial,
                sans-serif;

            background-image:
                linear-gradient(
                    rgba(7, 14, 29, 0.30),
                    rgba(7, 14, 29, 0.30)
                ),
                url("assets/images/pattern2.png");

            background-size: cover;

            background-position: center;

            background-repeat: no-repeat;

            background-attachment: fixed;

            color: white;

        }


        /* =========================================
           LOGIN CONTAINER
        ========================================= */

        .login-container {

            width: 100%;

            max-width: 450px;

            padding: 45px;

            background: #0e1423;

            border: 3px solid #b8862c;

            border-radius: 25px;

            box-shadow:
                0 15px 40px
                rgba(0, 0, 0, 0.45);

            text-align: center;

        }


        /* =========================================
           LOGO
        ========================================= */

        .login-logo {

            width: 300px;

            max-width: 100%;

            height: auto;

            display: block;

            margin: 0 auto 30px;

        }


        /* =========================================
           WELCOME TITLE
        ========================================= */

        .login-container h1 {

            margin-bottom: 8px;

            font-size: 32px;

            color: #b8862c;

            font-weight: 700;

        }


        /* =========================================
           SUBTITLE
        ========================================= */

        .login-container .subtitle {

            margin-bottom: 30px;

            color: #ddd;

            font-size: 15px;

            line-height: 1.5;

        }


        /* =========================================
           FORM GROUP
        ========================================= */

        .form-group {

            text-align: left;

            margin-bottom: 20px;

        }


        /* =========================================
           LABEL
        ========================================= */

        .form-group label {

            display: block;

            margin-bottom: 8px;

            color: #fff;

            font-weight: 600;

            font-size: 15px;

        }


        /* =========================================
           INPUT
        ========================================= */

        .form-group input {

            width: 100%;

            padding: 14px 16px;

            border: 2px solid #555;

            border-radius: 10px;

            background: #fff;

            color: #111;

            font-size: 16px;

            outline: none;

            transition:
                border-color 0.25s ease,
                box-shadow 0.25s ease;

        }


        .form-group input:focus {

            border-color: #b8862c;

            box-shadow:
                0 0 0 3px
                rgba(184, 134, 44, 0.15);

        }


        /* =========================================
           PASSWORD WRAPPER
        ========================================= */

        .password-wrapper {

            position: relative;

        }


        .password-wrapper input {

            width: 100%;

            padding-right: 50px;

        }


        /* =========================================
           PASSWORD TOGGLE
        ========================================= */

        .toggle-password {

            position: absolute;

            right: 12px;

            top: 50%;

            transform: translateY(-50%);

            width: 34px;

            height: 34px;

            display: flex;

            align-items: center;

            justify-content: center;

            background: transparent;

            border: none;

            border-radius: 50%;

            cursor: pointer;

            color: #0e1423;

            font-size: 17px;

            padding: 0;

            transition:
                color 0.2s ease,
                background-color 0.2s ease;

        }


        .toggle-password:hover {

            color: #d4a33a;

            background-color:
                rgba(184, 134, 44, 0.10);

        }


        .toggle-password:focus {

            outline: none;

            box-shadow:
                0 0 0 2px
                rgba(184, 134, 44, 0.25);

        }


        /* =========================================
           LOGIN BUTTON
        ========================================= */

        .login-btn {

            width: 100%;

            padding: 15px;

            margin-top: 5px;

            border: none;

            border-radius: 10px;

            background: #b8862c;

            color: #0e1423;

            font-size: 17px;

            font-weight: bold;

            cursor: pointer;

            transition:
                background-color 0.3s ease,
                transform 0.3s ease;

        }


        .login-btn:hover {

            background: #d4a33a;

            transform: translateY(-2px);

        }


        /* =========================================
           ERROR MESSAGE
        ========================================= */

        .error-message {

            margin-bottom: 20px;

            padding: 12px;

            border-radius: 8px;

            background: #8b2020;

            color: white;

            font-size: 14px;

            line-height: 1.4;

        }


        /* =========================================
           BOTTOM LINKS
        ========================================= */

        .booking-back {

            margin-top: 20px;

            color: #fff;

            font-size: 15px;

            line-height: 1.5;

        }


        .booking-back a {

            color: #b8862c;

            text-decoration: none;

            font-weight: 600;

        }


        .booking-back a:hover {

            color: #d4a33a;

            text-decoration: underline;

        }


        /* =========================================
           RESPONSIVE
        ========================================= */

        @media (max-width: 600px) {

            body {

                padding: 20px 15px;

            }


            .login-container {

                padding: 35px 25px;

                border-radius: 20px;

            }


            .login-logo {

                width: 250px;

                margin-bottom: 25px;

            }


            .login-container h1 {

                font-size: 28px;

            }


            .login-container .subtitle {

                font-size: 14px;

            }

        }


        @media (max-width: 400px) {

            .login-container {

                padding: 30px 20px;

            }


            .login-logo {

                width: 220px;

            }


            .login-container h1 {

                font-size: 25px;

            }


            .form-group input {

                padding: 13px 14px;

                font-size: 15px;

            }


            .login-btn {

                padding: 14px;

                font-size: 16px;

            }

        }

    </style>

</head>


<body>


    <div class="login-container">


        <!-- =====================================
             NAVA LOGO
        ====================================== -->

        <img
            src="assets/images/logo.png"
            alt="NAVA Fade Studio Logo"
            class="login-logo"
        >


        <!-- =====================================
             WELCOME
        ====================================== -->

        <div class="subtitle">

            <h1>Welcome Back</h1>

            <p>
                Login to your account to book an appointment.
            </p>

        </div>


        <!-- =====================================
             ERROR MESSAGE
        ====================================== -->

        <?php if (!empty($error)): ?>

            <div class="error-message">

                <?= htmlspecialchars($error) ?>

            </div>

        <?php endif; ?>


        <!-- =====================================
             LOGIN FORM
        ====================================== -->

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
                    value="<?= htmlspecialchars($_POST["email"] ?? "") ?>"
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
                        aria-label="Show password"
                    >

                        <i class="fa-solid fa-eye"></i>

                    </button>

                </div>

            </div>


            <!-- LOGIN -->

            <button
                type="submit"
                class="login-btn"
            >
                LOGIN
            </button>


        </form>


        <!-- =====================================
             REGISTER / HOME LINKS
        ====================================== -->

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


    <!-- =========================================
         PASSWORD TOGGLE
    ========================================= -->

    <script>

        const passwordButtons =
            document.querySelectorAll(".toggle-password");


        passwordButtons.forEach(function(button) {

            button.addEventListener("click", function() {

                const targetId =
                    button.dataset.target;

                const passwordInput =
                    document.getElementById(targetId);

                const icon =
                    button.querySelector("i");


                if (passwordInput.type === "password") {

                    passwordInput.type = "text";

                    icon.classList.remove("fa-eye");

                    icon.classList.add("fa-eye-slash");

                    button.setAttribute(
                        "aria-label",
                        "Hide password"
                    );

                } else {

                    passwordInput.type = "password";

                    icon.classList.remove("fa-eye-slash");

                    icon.classList.add("fa-eye");

                    button.setAttribute(
                        "aria-label",
                        "Show password"
                    );

                }

            });

        });

    </script>


</body>

</html>