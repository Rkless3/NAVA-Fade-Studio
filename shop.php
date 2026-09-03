<?php

session_start();

require_once "config/Database.php";
require_once "classes/Product.php";

$database = new Database();
$db = $database->connect();

$productModel = new Product($db);
$products = $productModel->getActive();

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Shop | NAVA Fade Studio</title>

    <link
        rel="stylesheet"
        href="assets/css/style.css"
    >

</head>


<body>


<!-- =====================================================
     HEADER
===================================================== -->

<header class="header">

    <div class="container navbar">

        <a href="index.php" class="logo">

            <img
                src="assets/images/logo.png"
                alt="NAVA Fade Studio Logo"
            >

        </a>


        <nav>

            <a href="index.php">
                Home
            </a>

            <a href="about.php">
                About Us
            </a>

            <a href="index.php#services">
                Service
            </a>

            <a href="shop.php" class="active">
                Shop
            </a>

            <a href="blog.php">
                Blog
            </a>

            <a href="book.php" class="nav-button">
                Book Now
            </a>

            <?php if (isset($_SESSION["customer_id"])): ?>

                <div class="customer-menu">

                    <button
                        class="customer-menu-btn"
                        type="button"
                        onclick="toggleCustomerMenu()"
                    >

                        👤 <?= htmlspecialchars($_SESSION["customer_name"]) ?>

                        <span class="dropdown-arrow">▼</span>

                    </button>


                    <div
                        class="customer-dropdown"
                        id="customerDropdown"
                    >

                        <a href="profile.php">
                            👤 My Profile
                        </a>

                        <a href="appointments.php">
                            📅 My Appointments
                        </a>

                    <div class="dropdown-divider"></div>

                        <a
                            href="logout.php"
                            class="logout-link"
                        >
                            🚪 Logout
                        </a>

                    </div>

                </div>

            <?php else: ?>

                <a
                    href="register.php"
                    class="nav-button"
                >
                    Register
                </a>

            <?php endif; ?>

        </nav>

    </div>

</header>



<!-- =========================================
     SHOP HERO
========================================= -->

<section class="shop-hero">

    <div class="shop-hero-content">

        <span>
            NAVA FADE STUDIO
        </span>

        <h1>
            OUR SHOP
        </h1>

        <p>
            Discover premium grooming products
            designed to keep you looking sharp
            every day.
        </p>

    </div>

</section>



<!-- =========================================
     PRODUCTS
========================================= -->

<section class="shop-page">

    <div class="shop-page-header">

        <span>
            PREMIUM GROOMING
        </span>

        <h2>
            OUR PRODUCTS
        </h2>


    </div>


    <div class="shop-grid">


        <div class="shop-grid">

            <?php if (!empty($products)): ?>

                <?php foreach ($products as $item): ?>

                    <div class="shop-product-card1">

                        <img
                            src="assets/images/<?= htmlspecialchars($item["image"]) ?>"
                            alt="<?= htmlspecialchars($item["product_name"]) ?>"
                        >

                        <div class="product-info">

                            <h3>
                                <?= htmlspecialchars($item["product_name"]) ?>
                            </h3>

                            <div class="product-rating">
                                ★★★★★
                            </div>

                            <p>
                                <?= htmlspecialchars($item["description"]) ?>
                            </p>

                            <div class="shop-product-bottom">

                                <span class="shop-price">
                                    ₱<?= number_format($item["price"], 2) ?>
                                </span>

                                <?php if ($item["stock"] > 0): ?>

                                    <button
                                        class="shop-add-btn"
                                        type="button"
                                    >
                                        ADD TO CART
                                    </button>

                                <?php else: ?>

                                    <button
                                        class="shop-add-btn"
                                        type="button"
                                        disabled
                                    >
                                        OUT OF STOCK
                                    </button>

                                <?php endif; ?>

                            </div>

                        </div>

                    </div>

                <?php endforeach; ?>

            <?php else: ?>

                <p>
                    No products available at the moment.
                </p>

            <?php endif; ?>

        </div>


    </div>

</section>



<!-- =====================================================
     FOOTER
===================================================== -->

<footer class="footer">


    <div class="footer-content">


        <!-- BRAND -->

        <div>

            <img
                src="assets/images/logo.png"
                class="footer-logo"
                alt="NAVA Fade Studio Logo"
            >


            <p>

                NAVA Fade Studio is dedicated to
                delivering clean, modern, and
                personalized grooming experiences.

            </p>

        </div>



        <!-- ABOUT LINKS -->

        <div>

            <h3>
                About
            </h3>


            <a href="about.php">
                About Us
            </a>

            <a href="#services">
                Services
            </a>

            <a href="#shop">
                Shop
            </a>

            <a href="blog.php">
                Blog
            </a>

        </div>



        <!-- SUPPORT -->

        <div>

            <h3>
                Support
            </h3>


            <a href="about.php">
                Who We Are
            </a>

            <a href="#services">
                Our Services
            </a>

            <a href="book.php">
                Book Appointment
            </a>

            <a href="#">
                Contact Us
            </a>

        </div>



        <!-- ADDRESS -->

        <div>

            <h3>
                Address
            </h3>


            <!-- KEEP YOUR EXISTING ADDRESS -->

            <p>
                Your current address here
            </p>


            <p>
                📧 navafadestudio@gmail.com
            </p>


            <p>
                📞 0969 407 4629
            </p>

        </div>


    </div>


</footer>



<script>

function toggleCustomerMenu() {

    document
        .getElementById("customerDropdown")
        .classList
        .toggle("show");

}


document.addEventListener(
    "click",
    function(event) {

        const menu =
            document.querySelector(".customer-menu");


        if (
            menu &&
            !menu.contains(event.target)
        ) {

            document
                .getElementById("customerDropdown")
                ?.classList
                .remove("show");

        }

    }
);

</script>


</body>

</html>