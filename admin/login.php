<?php
session_start();

require_once "../config/Database.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($username === "Archilles" && $password === "030305") {

        $_SESSION["admin_logged_in"] = true;
        $_SESSION["admin_username"] = $username;

        header("Location: dashboard.php");
        exit;

    } else {

        $error = "Invalid username or password.";

    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Admin Login | NAVA Fade Studio</title>

    <style>

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            min-height: 100vh;

            display: flex;
            align-items: center;
            justify-content: center;

            font-family: Bahnschrift, Myriad Pro;

            background:
               
                url("../assets/images/pattern2.png");

            background-size: cover;
            background-position: center;

            color: white;
        }

        .login-container {
            width: 100%;
            max-width: 450px;

            padding: 45px;

            background: #0e1423;

            border: 3px solid #b8862c;
            border-radius: 25px;

            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.4);

            text-align: center;
        }

        .login-logo {

            width: 300px;
            height: auto;
            display: block;
            margin: 0 auto 30px;

        }

        .login-container h1 {
            margin-bottom: 8px;

            font-size: 32px;

            color: #b8862c;
        }

        .login-container .subtitle {
            margin-bottom: 30px;

            color: #ddd;

            font-size: 15px;
        }

        .form-group {
            text-align: left;

            margin-bottom: 20px;
        }

        .form-group label {
            display: block;

            margin-bottom: 8px;

            color: #fff;

            font-weight: 600;
        }

        .form-group input {
            width: 100%;

            padding: 14px 16px;

            border: 2px solid #555;
            border-radius: 10px;

            background: #fff;
            color: #111;

            font-size: 16px;

            outline: none;
        }

        .form-group input:focus {
            border-color: #b8862c;
        }

        .login-btn {
            width: 100%;

            padding: 15px;

            margin-top: 5px;

            border: none;
            border-radius: 10px;

            background: #b8862c;
            color: #0e1423;
k
            font-size: 17px;
            font-weight: bold;

            cursor: pointer;

            transition: 0.3s ease;
        }

        .login-btn:hover {
            background: #d4a33a;
            transform: translateY(-2px);
        }

        .error-message {
            margin-bottom: 20px;

            padding: 12px;

            border-radius: 8px;

            background: #8b2020;
            color: white;

            font-size: 14px;
        }

        .back-link {
            display: inline-block;

            margin-top: 25px;

            color: #b8862c;

            text-decoration: none;

            font-size: 14px;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        .password-wrapper {
            position: relative;
        }


        .password-wrapper input {
            width: 100%;
        }


        .toggle-password {
            position: absolute;

            right: 12px;
            top: 50%;

            transform: translateY(-50%);

            background: transparent;
            border: none;

            cursor: pointer;

            font-size: 18px;

            padding: 5px;

            color: #b8862c;
        }

    </style>

</head>

<body>

    <div class="login-container">

        <img
            src="../assets/images/logo.png"
            alt="NAVA Fade Studio Logo"
            class="login-logo"
        >

        <h1>NAVA Fade Studio</h1>

        <p class="subtitle">
            Administrator Login
        </p>

        <?php if (!empty($error)): ?>

            <div class="error-message">
                <?= htmlspecialchars($error) ?>
            </div>

        <?php endif; ?>

        <form method="POST" action="">

            <div class="form-group">

                <label for="username">
                    Username
                </label>

                <input
                    type="text"
                    id="username"
                    name="username"
                    placeholder="Enter username"
                    required
                >

            </div>

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
                class="login-btn"
            >
                LOGIN
            </button>

        </form>

        <a
            href="../index.php"
            class="back-link"
        >
            ← Back to NAVA Fade Studio
        </a>

    </div>

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