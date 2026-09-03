<?php

require_once "config/Database.php";
require_once "classes/Customer.php";

$database = new Database();
$db = $database->connect();

$customer = new Customer($db);

$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $full_name = trim($_POST["full_name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $contact_number = trim($_POST["contact_number"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirm_password = $_POST["confirm_password"] ?? "";


    // =========================================
    // VALIDATION
    // =========================================

    if (
        empty($full_name) ||
        empty($email) ||
        empty($contact_number) ||
        empty($password) ||
        empty($confirm_password)
    ) {

        $error = "Please fill in all required fields.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = "Please enter a valid email address.";

    } elseif (strlen($password) < 6) {

        $error = "Password must be at least 6 characters.";

    } elseif ($password !== $confirm_password) {

        $error = "Passwords do not match.";

    } elseif ($customer->emailExists($email)) {

        $error = "This email is already registered.";

    } else {

        try {

            $customer->register(
                $full_name,
                $email,
                $contact_number,
                $password
            );

            $success = "Account created successfully! You can now log in.";

        } catch (PDOException $e) {

            $error = "Registration failed. Please try again.";

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

    <title>Register | NAVA Fade Studio</title>

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

                <h2>Create Your Account</h2>

                <p>
                    Sign up to book an appointment and manage your reservations.
                </p>

            </div>


            <?php if (!empty($success)): ?>

                <div class="success-message">

                    <?= htmlspecialchars($success) ?>

                </div>

            <?php endif; ?>


            <?php if (!empty($error)): ?>

                <div class="error-message">

                    <?= htmlspecialchars($error) ?>

                </div>

            <?php endif; ?>


            <form method="POST" action="">

                <!-- FULL NAME -->

                <div class="form-group">

                    <label for="full_name">
                        Full Name
                    </label>

                    <input
                        type="text"
                        id="full_name"
                        name="full_name"
                        placeholder="Enter your full name"
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
                        placeholder="Enter your email address"
                        required
                    >

                </div>


                <!-- CONTACT -->

                <div class="form-group">

                    <label for="contact_number">
                        Contact Number
                    </label>

                    <input
                        type="tel"
                        id="contact_number"
                        name="contact_number"
                        placeholder="09XXXXXXXXX"
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
                                placeholder="Create a password"
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


                <!-- CONFIRM PASSWORD -->

                <div class="form-group">

                    <label for="confirm_password">
                        Confirm Password
                    </label>

                    <div class="password-wrapper">

                        <input
                            type="password"
                            id="confirm_password"
                            name="confirm_password"
                            placeholder="Confirm your password"
                            required
                        >

                        <button
                            type="button"
                            class="toggle-password"
                            data-target="confirm_password"
                        >
                            👁
                        </button>

                    </div>

                </div>


                <button
                    type="submit"
                    class="booking-btn"
                >
                    CREATE ACCOUNT
                </button>

            </form>


            <div class="booking-back">

                <p>
                    Already have an account?

                    <a href="login.php">
                        Login here
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

        const targetId =
            button.dataset.target;

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