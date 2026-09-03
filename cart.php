<?php

session_start();


/* =========================================
   LOGIN REQUIREMENT
========================================= */

if (!isset($_SESSION["customer_id"])) {

    header("Location: login.php");
    exit;

}


require_once "config/Database.php";
require_once "classes/Product.php";

$database = new Database();
$db = $database->connect();

$productModel = new Product($db);


/* =========================================
   CART ACTIONS
========================================= */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $product_id = (int) ($_POST["product_id"] ?? 0);


    /* =====================================
       ADD QUANTITY
    ===================================== */

    if (isset($_POST["increase"])) {

        if (isset($_SESSION["cart"][$product_id])) {

            $product = $productModel->getById($product_id);

            if ($product) {

                if ($_SESSION["cart"][$product_id] < $product["stock"]) {

                    $_SESSION["cart"][$product_id]++;

                }

            }

        }

    }


    /* =====================================
       DECREASE QUANTITY
    ===================================== */

    if (isset($_POST["decrease"])) {

        if (isset($_SESSION["cart"][$product_id])) {

            $_SESSION["cart"][$product_id]--;

            if ($_SESSION["cart"][$product_id] <= 0) {

                unset($_SESSION["cart"][$product_id]);

            }

        }

    }


    /* =====================================
       REMOVE PRODUCT
    ===================================== */

    if (isset($_POST["remove"])) {

        unset($_SESSION["cart"][$product_id]);

    }


    /* =====================================
       REFRESH CART
    ===================================== */

    header("Location: cart.php");
    exit;

}


/* =========================================
   GET CART
========================================= */

$cart = $_SESSION["cart"] ?? [];

$total = 0;

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
        Cart | NAVA Fade Studio
    </title>


    <!-- MAIN WEBSITE CSS -->

    <link
        rel="stylesheet"
        href="assets/css/style.css"
    >


    <!-- CART CSS -->

    <link
        rel="stylesheet"
        href="assets/css/cart.css"
    >

</head>


<body>


<main class="cart-page">


    <!-- =====================================
         CART HEADER
    ====================================== -->

    <div class="cart-page-header">

        <span>
            NAVA FADE STUDIO
        </span>

        <h1>
            Your Cart
        </h1>

    </div>


    <div class="cart-container">


        <?php if (empty($cart)): ?>


            <!-- =================================
                 EMPTY CART
            ================================== -->

            <div class="cart-empty">

                <h2>
                    Your Cart is Empty
                </h2>

                <p>
                    You haven't added any products to your cart yet.
                </p>

                <a
                    href="shop.php"
                    class="cart-continue"
                >
                    Continue Shopping
                </a>

            </div>


        <?php else: ?>


            <!-- =================================
                 CART PRODUCTS
            ================================== -->

            <?php foreach ($cart as $product_id => $quantity): ?>


                <?php

                $product = $productModel->getById((int) $product_id);


                /*
                 * Skip product if it no longer exists.
                 */

                if (!$product) {
                    continue;
                }


                $subtotal = $product["price"] * $quantity;

                $total += $subtotal;

                ?>


                <div class="cart-item">


                    <!-- =================================
                         PRODUCT INFORMATION
                    ================================== -->

                    <div class="cart-item-info">

                        <h3>
                            <?= htmlspecialchars($product["product_name"]) ?>
                        </h3>

                        <p>
                            Price:
                            ₱<?= number_format($product["price"], 2) ?>
                        </p>

                    </div>


                    <!-- =================================
                         QUANTITY
                    ================================== -->

                    <form
                        method="POST"
                        action="cart.php"
                        class="cart-quantity"
                    >

                        <input
                            type="hidden"
                            name="product_id"
                            value="<?= (int) $product_id ?>"
                        >


                        <!-- DECREASE -->

                        <button
                            type="submit"
                            name="decrease"
                            title="Decrease quantity"
                        >
                            −
                        </button>


                        <!-- CURRENT QUANTITY -->

                        <span>
                            <?= (int) $quantity ?>
                        </span>


                        <!-- INCREASE -->

                        <button
                            type="submit"
                            name="increase"
                            title="Increase quantity"
                        >
                            +
                        </button>

                    </form>


                    <!-- =================================
                         SUBTOTAL
                    ================================== -->

                    <div class="cart-subtotal">

                        ₱<?= number_format($subtotal, 2) ?>

                    </div>


                    <!-- =================================
                         REMOVE
                    ================================== -->

                    <form
                        method="POST"
                        action="cart.php"
                        class="cart-remove"
                    >

                        <input
                            type="hidden"
                            name="product_id"
                            value="<?= (int) $product_id ?>"
                        >


                        <button
                            type="submit"
                            name="remove"
                        >
                            Remove
                        </button>

                    </form>


                </div>


            <?php endforeach; ?>


        <?php endif; ?>


    </div>


    <?php if (!empty($cart)): ?>


        <!-- =====================================
             CART SUMMARY
        ====================================== -->

        <div class="cart-summary">


            <div class="cart-summary-box">


                <h2>
                    Cart Summary
                </h2>


                <div class="cart-total">

                    <span>
                        Total
                    </span>

                    <span>
                        ₱<?= number_format($total, 2) ?>
                    </span>

                </div>


                <!-- CONTINUE SHOPPING -->

                <a
                    href="shop.php"
                    class="cart-continue"
                >
                    Continue Shopping
                </a>


                <!-- CHECKOUT -->

                <a
                    href="checkout.php"
                    class="cart-continue"
                >
                    Checkout
                </a>


            </div>


        </div>


    <?php endif; ?>


</main>


</body>

</html>