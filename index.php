<?php
session_start();


require_once "config/Database.php";
require_once "classes/Service.php";

$database = new Database();
$db = $database->connect();

$serviceObject = new Service($db);
$services = $serviceObject->getAll();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>NAVA Fade Studio</title>

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

            <a href="index.php" class="active">
                Home
            </a>

            <a href="about.php">
                About Us
            </a>

            <a href="#services">
                Service
            </a>

            <a href="shop.php">
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



<!-- =====================================================
     HERO SECTION
===================================================== -->

<section id="home" class="hero">

    <div class="hero-content">


        <!-- HERO TEXT -->

        <div class="hero-text">

            <h1>

                Sharp Style. Fresh <br>Look.

                <span>Confidence.</span>

            </h1>


            <div class="gold-line"></div>


            <p>

                Discover a style that fits you perfectly.
                Our professional barbers <br>
                are dedicated to
                giving you a clean, confident look that
                suits your <br>personality.

            </p>


            <a
                href="#services"
                class="hero-button"
            >

                Explore More

                <span>➜</span>

            </a>


            <!-- OPENING HOURS -->

            <div class="opening-hours">

                <h3>
                    Opening & Closing Time
                </h3>


                <div class="hours">


                    <div>

                        <strong>
                            Monday - Friday
                        </strong>

                        <p>
                            9:00 am - 8:00 pm
                        </p>

                    </div>


                    <div class="divider"></div>


                    <div>

                        <strong>
                            Saturday - Sunday
                        </strong>

                        <p>
                            10:00 am - 7:00 pm
                        </p>

                    </div>


                </div>

            </div>

        </div>



        <!-- HERO IMAGE -->

        <div class="hero-image">

            <img
                src="assets/images/hero.png"
                alt="NAVA Fade Studio Barber"
            >

        </div>


    </div>

</section>



<!-- =====================================================
     SERVICE HIGHLIGHTS / TICKER
===================================================== -->

<div class="service-ticker">

    <div class="ticker-content">

        HAIR CUT
        &nbsp;
        &nbsp;
        +
        &nbsp;
        &nbsp;
        BEARD TRIMMING
        &nbsp;
        +
        &nbsp;
        HAIR STYLING
        &nbsp;
        +
        &nbsp;
        CLEAN SHAVE
        &nbsp;
        +
        &nbsp;
        FACIAL & WASH
        &nbsp;
        +
        &nbsp;
        HAIR TREATMENT
        &nbsp;
        +
        &nbsp;
        HOT TOWEL
        &nbsp;
        +
        &nbsp;
        HAIR CUT
        &nbsp;
        +
        &nbsp;
        BEARD TRIMMING
        &nbsp;
        +
        &nbsp;
        HAIR STYLING
        &nbsp;
        +
        &nbsp;
        CLEAN SHAVE
        &nbsp;
        +
        &nbsp;
        FACIAL & WASH
        &nbsp;
        +
        &nbsp;
        HAIR TREATMENT
        &nbsp;
        +
        &nbsp;
        HOT TOWEL
        &nbsp;
        +

    </div>

</div>



<!-- =====================================================
     OUR BARBER SERVICES
===================================================== -->

<section
    id="services"
    class="services"
>


    <div class="section-title">

        <h2>

            Our Barber <span>Services</span>

        </h2>


        <p>

            Professional grooming services designed
            to keep you looking sharp and confident.

        </p>

    </div>



    <div class="service-grid">


        <?php foreach ($services as $service): ?>


            <div class="service-card">


                <!-- SERVICE IMAGE -->

                <div class="service-image">

                    <img
                        src="assets/images/<?=
                        htmlspecialchars(
                            $service['image']
                        )
                        ?>"
                        alt="<?=
                        htmlspecialchars(
                            $service['service_name']
                        )
                        ?>"
                    >

                </div>



                <!-- SERVICE INFORMATION -->

                <div class="service-info">


                    <h3>

                        <?= htmlspecialchars(
                            $service['service_name']
                        ) ?>

                    </h3>


                    <p>

                        <?= htmlspecialchars(
                            $service['description']
                        ) ?>

                    </p>



                    <div class="service-bottom">


                        <div>

                            <strong>

                                ₱<?= number_format(
                                    $service['price'],
                                    0
                                ) ?>

                            </strong>


                            <span>

                                <?= htmlspecialchars(
                                    $service['duration']
                                ) ?>

                            </span>

                        </div>



                        <a
                            href="book.php?service=<?= $service['id'] ?>"
                            class="book-button"
                        >

                            Book Now

                        </a>


                    </div>


                </div>


            </div>


        <?php endforeach; ?>


    </div>

</section>



<!-- =========================================
     WE ARE HAPPY TO MAKE YOU HANDSOME
     ========================================= -->

<section class="about-preview" id="about-preview">

    <!-- LEFT SIDE: PHOTO + REVIEW -->
    <div class="about-preview-visual">

        <!-- Beard Trimming Photo -->
        <div class="about-preview-image">
            <img
                src="assets/images/beard-trimming.png"
                alt="Professional beard trimming at NAVA Fade Studio"
            >
        </div>

        <!-- Customer Review -->
        <div class="about-review-card">

            <div class="review-header">

                <div class="review-avatar">
                    M
                </div>

                <div class="review-user">
                    <strong>Rouilo B.</strong>
                    <span>1 review</span>
                </div>

            </div>

            <div class="review-rating">
                <span class="stars">★★★★★</span>
                <span class="review-date">2 months ago</span>
            </div>

            <p>
                Just got a haircut and beard trim at NAVA Fade Studio
                and I couldn’t be happier with the results! The barber
                was professional, precise, and paid attention to every
                detail. He made sure everything was perfect and took
                the time to shape my beard exactly how I wanted.
            </p>

            <p>
                The shop is clean, has a great atmosphere, and everyone
                is welcoming. You can tell they take pride in their work.
                Highly recommended to anyone looking for a fresh cut
                and top-quality service!
            </p>

        </div>

    </div>


    <!-- RIGHT SIDE: CONTENT -->
    <div class="about-preview-content">

        <h2>
            We Are Happy to Make You Handsome
        </h2>

        <p>
            Discover a style that fits you perfectly. Our professional
            barbers are dedicated to helping you look and feel your best
            with personalized haircuts, beard grooming, and quality
            barbering services.
        </p>

        <p>
            Whether you're going for a clean classic look or a modern
            style, we'll make sure every detail is carefully crafted
            to suit your personality and preferences.
        </p>

        <a href="#services" class="about-book-btn">
            Book Now
        </a>

    </div>

</section>



<!-- =====================================================
     LATEST SHOP
===================================================== -->

<section
    id="shop"
    class="shop"
>


    <div class="section-title">

        <h2>

            Our Latest
            <span>Shop</span>

        </h2>


        <p>

            Quality grooming products to help
            you maintain your style at home.

        </p>

    </div>



    <div class="shop-grid">


        <!-- PRODUCT 1 -->

        <div class="shop-product-card">

            <img
                src="assets/images/shampoo.png"
                alt="NAVA Shampoo"
            >

            <div class="product-info">

                <h3>
                    NAVA Shampoo
                </h3>

                <div class="product-rating">
                    ★★★★★
                </div>

                <div class="product-price">
                    ₱350
                </div>

                <div class="shop-buttons">

                    <a 
                    href="shop.php" class="add-cart-btn">
                        Add to Cart
                    </a>

                    <button class="view-btn">
                        View Details
                    </button>

                </div>

            </div>

        </div>



        <!-- PRODUCT 2 -->

        <div class="shop-product-card">


            <img
                src="assets/images/hair-clay.png"
                alt="NAVA Hair Wax"
            >

            <div class="product-info">

                <h3>
                    NAVA Hair Wax
                </h3>

                <div class="product-rating">
                    ★★★★★
                </div>

                <div class="product-price">
                    ₱280
                </div>

                <div class="shop-buttons">
  
                    <a 
                    href="shop.php" class="add-cart-btn">
                        Add to Cart
                    </a>

                    <button class="view-btn">
                        View Details
                    </button>

                </div>

            </div>

        </div>



        <!-- PRODUCT 3 -->

        <div class="shop-product-card">

            <img
                src="assets/images/hair-spray.png"
                alt="NAVA Hair Spray"
            >

            <div class="product-info">

                <h3>
                    NAVA Hair Spray
                </h3>

                <div class="product-rating">
                    ★★★★★
                </div>

                <div class="product-price">
                    ₱300
                </div>

                <div class="shop-buttons">

                    <a 
                    href="shop.php" class="add-cart-btn">
                        Add to Cart
                    </a>

                    <button class="view-btn">
                        View Details
                    </button>

                </div>

            </div>

        </div>


    </div>

</section>



<!-- =====================================================
     DISCOUNT / BOOKING CTA
===================================================== -->

<section class="discount">


    <h2>

        Get
        <span>20% Off</span>
        Your First Booking

    </h2>


    <p>

        Ready for a fresh new look?
        Book your appointment with NAVA Fade Studio
        and enjoy 20% off your first visit.

    </p>


    <a
        href="book.php"
        class="cta-button"
    >

        Book An Appointment

    </a>


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

            <a href="shop.php">
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



<!-- =====================================================
     JAVASCRIPT
===================================================== -->

<script src="assets/js/script.js"></script>

<script>

function toggleCustomerMenu() {

    document
        .getElementById("customerDropdown")
        .classList
        .toggle("show");

}


/* Close dropdown when clicking outside */

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