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


/* =========================================
   DATABASE CONNECTION
========================================= */

$database = new Database();
$db = $database->connect();

$productModel = new Product($db);


/* =========================================
   GET CUSTOMER INFORMATION
========================================= */

$customer_id = (int) $_SESSION["customer_id"];

$customerQuery = "
    SELECT
        id,
        full_name,
        email,
        contact_number
    FROM customers
    WHERE id = :id
    LIMIT 1
";

$customerStmt = $db->prepare($customerQuery);

$customerStmt->bindValue(
    ":id",
    $customer_id,
    PDO::PARAM_INT
);

$customerStmt->execute();

$customer = $customerStmt->fetch();


/* =========================================
   CUSTOMER NOT FOUND
========================================= */

if (!$customer) {

    session_destroy();

    header("Location: login.php");
    exit;

}


/* =========================================
   GET CART
========================================= */

$cart = $_SESSION["cart"] ?? [];


/* =========================================
   EMPTY CART
========================================= */

if (empty($cart)) {

    header("Location: cart.php");
    exit;

}


/* =========================================
   PREPARE ORDER ITEMS
========================================= */

$orderItems = [];

$total = 0;


foreach ($cart as $product_id => $quantity) {

    $product_id = (int) $product_id;
    $quantity = (int) $quantity;


    if ($quantity <= 0) {
        continue;
    }


    $product = $productModel->getById($product_id);


    /* PRODUCT DOES NOT EXIST */

    if (!$product) {
        continue;
    }


    /* PRODUCT IS INACTIVE */

    if ($product["status"] !== "Active") {
        continue;
    }


    /* PRODUCT IS OUT OF STOCK */

    if ((int) $product["stock"] <= 0) {
        continue;
    }


    /* DO NOT EXCEED CURRENT STOCK */

    if ($quantity > (int) $product["stock"]) {

        $quantity = (int) $product["stock"];

    }


    if ($quantity <= 0) {
        continue;
    }


    $price = (float) $product["price"];

    $subtotal = $price * $quantity;

    $total += $subtotal;


    $orderItems[] = [

        "product_id" => $product_id,

        "quantity" => $quantity,

        "price" => $price,

        "subtotal" => $subtotal,

        "product_name" => $product["product_name"]

    ];

}


/* =========================================
   VALIDATE ORDER ITEMS
========================================= */

if (empty($orderItems)) {

    $_SESSION["cart"] = [];

    header("Location: cart.php");
    exit;

}


/* =========================================
   ORDER VARIABLES
========================================= */

$orderSuccess = false;

$order_id = null;

$errorMessage = "";


/* =========================================
   PLACE ORDER
========================================= */

if ($_SERVER["REQUEST_METHOD"] === "POST") {


    try {

        /* START TRANSACTION */

        $db->beginTransaction();


        /* =====================================
           CREATE ORDER
        ===================================== */

        $orderQuery = "
            INSERT INTO orders
            (
                customer_id,
                total_amount,
                status
            )
            VALUES
            (
                :customer_id,
                :total_amount,
                'Pending'
            )
        ";


        $orderStmt = $db->prepare($orderQuery);


        $orderStmt->bindValue(
            ":customer_id",
            $customer_id,
            PDO::PARAM_INT
        );


        $orderStmt->bindValue(
            ":total_amount",
            $total
        );


        $orderStmt->execute();


        /* GET ORDER ID */

        $order_id = (int) $db->lastInsertId();


        /* =====================================
           INSERT ORDER ITEMS
        ===================================== */

        $itemQuery = "
            INSERT INTO order_items
            (
                order_id,
                product_id,
                quantity,
                price,
                subtotal
            )
            VALUES
            (
                :order_id,
                :product_id,
                :quantity,
                :price,
                :subtotal
            )
        ";


        $itemStmt = $db->prepare($itemQuery);


        /* =====================================
           UPDATE PRODUCT STOCK
        ===================================== */

        $stockQuery = "
            UPDATE products
            SET stock = stock - :quantity
            WHERE id = :product_id
            AND stock >= :quantity
        ";


        $stockStmt = $db->prepare($stockQuery);


        foreach ($orderItems as $item) {


            /* INSERT ORDER ITEM */

            $itemStmt->bindValue(
                ":order_id",
                $order_id,
                PDO::PARAM_INT
            );


            $itemStmt->bindValue(
                ":product_id",
                $item["product_id"],
                PDO::PARAM_INT
            );


            $itemStmt->bindValue(
                ":quantity",
                $item["quantity"],
                PDO::PARAM_INT
            );


            $itemStmt->bindValue(
                ":price",
                $item["price"]
            );


            $itemStmt->bindValue(
                ":subtotal",
                $item["subtotal"]
            );


            $itemStmt->execute();


            /* REDUCE PRODUCT STOCK */

            $stockStmt->bindValue(
                ":quantity",
                $item["quantity"],
                PDO::PARAM_INT
            );


            $stockStmt->bindValue(
                ":product_id",
                $item["product_id"],
                PDO::PARAM_INT
            );


            $stockStmt->execute();


            /* CHECK STOCK UPDATE */

            if ($stockStmt->rowCount() === 0) {

                throw new Exception(
                    "Not enough stock available for " .
                    $item["product_name"]
                );

            }

        }


        /* =====================================
           COMPLETE TRANSACTION
        ===================================== */

        $db->commit();


        /* CLEAR CART */

        $_SESSION["cart"] = [];


        $orderSuccess = true;


    } catch (Exception $e) {


        /* ROLLBACK IF SOMETHING FAILED */

        if ($db->inTransaction()) {

            $db->rollBack();

        }


        $errorMessage = $e->getMessage();

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
        Checkout | NAVA Fade Studio
    </title>


    <link
        rel="stylesheet"
        href="assets/css/style.css"
    >


    <style>

        /* =====================================================
           CHECKOUT PAGE
        ===================================================== */

        .checkout-page {

            min-height: 100vh;

            padding: 70px 8%;

            background:
                url("assets/images/pattern2.png")
                center center / cover fixed;

            color: #ffffff;

        }


        /* =====================================================
           CHECKOUT HEADER
        ===================================================== */

        .checkout-page-header {

            text-align: center;

            margin-bottom: 50px;

        }


        .checkout-page-header span {

            display: block;

            color: #b8862c;

            font-size: 14px;

            font-weight: bold;

            letter-spacing: 4px;

            margin-bottom: 10px;

        }


        .checkout-page-header h1 {

            margin: 0;

            font-size: 42px;

            font-weight: bold;

        }


        .checkout-page-header p {

            margin-top: 12px;

            color: #aaaaaa;

        }


        /* =====================================================
           CHECKOUT CONTAINER
        ===================================================== */

        .checkout-container {

            max-width: 1100px;

            margin: 0 auto;

            display: grid;

            grid-template-columns: 1fr 1fr;

            gap: 25px;

        }


        /* =====================================================
           CHECKOUT BOX
        ===================================================== */

        .checkout-box {

            background: #0e1423;

            border: 1px solid #555;

            border-radius: 12px;

            padding: 30px;

            box-sizing: border-box;

        }


        .checkout-box h2 {

            margin: 0 0 25px;

            color: #ffffff;

            font-size: 24px;

        }


        /* =====================================================
           CUSTOMER INFORMATION
        ===================================================== */

        .customer-info-row {

            padding: 15px 0;

            border-bottom: 1px solid #333;

        }


        .customer-info-row:last-child {

            border-bottom: none;

        }


        .customer-info-label {

            display: block;

            margin-bottom: 5px;

            color: #aaaaaa;

            font-size: 13px;

        }


        .customer-info-value {

            color: #ffffff;

            font-size: 16px;

        }


        /* =====================================================
           ORDER ITEMS
        ===================================================== */

        .checkout-product {

            display: flex;

            justify-content: space-between;

            align-items: center;

            gap: 20px;

            padding: 18px 0;

            border-bottom: 1px solid #333;

        }


        .checkout-product-info {

            flex: 1;

        }


        .checkout-product-info h3 {

            margin: 0 0 7px;

            color: #ffffff;

            font-size: 17px;

        }


        .checkout-product-info p {

            margin: 4px 0;

            color: #aaaaaa;

            font-size: 13px;

        }


        .checkout-product-subtotal {

            color: #f0b429;

            font-size: 17px;

            font-weight: bold;

            white-space: nowrap;

        }


        /* =====================================================
           TOTAL
        ===================================================== */

        .checkout-total {

            display: flex;

            justify-content: space-between;

            align-items: center;

            margin-top: 25px;

            padding-top: 20px;

            border-top: 1px solid #555;

        }


        .checkout-total span:first-child {

            color: #cccccc;

            font-size: 17px;

        }


        .checkout-total span:last-child {

            color: #f0b429;

            font-size: 28px;

            font-weight: bold;

        }


        /* =====================================================
           BUTTONS
        ===================================================== */

        .checkout-actions {

            max-width: 1100px;

            margin: 30px auto 0;

            display: flex;

            justify-content: flex-end;

            gap: 15px;

        }


        .checkout-back {

            display: inline-block;

            padding: 13px 24px;

            background: #151c2d;

            border: 1px solid #555;

            border-radius: 8px;

            color: #ffffff;

            text-decoration: none;

            font-size: 14px;

            font-weight: bold;

            transition: 0.2s;

        }


        .checkout-back:hover {

            border-color: #b8862c;

            color: #b8862c;

        }


        .checkout-place-btn {

            padding: 13px 24px;

            background: #b8862c;

            border: none;

            border-radius: 8px;

            color: #0e1423;

            font-size: 14px;

            font-weight: bold;

            cursor: pointer;

            transition: 0.2s;

        }


        .checkout-place-btn:hover {

            background: #d4a33a;

        }


        /* =====================================================
           ERROR MESSAGE
        ===================================================== */

        .checkout-error {

            max-width: 1100px;

            margin: 0 auto 25px;

            padding: 18px 22px;

            background: #2a1515;

            border: 1px solid #8b3a3a;

            border-radius: 10px;

            box-sizing: border-box;

        }


        .checkout-error strong {

            color: #ffffff;

        }


        .checkout-error p {

            margin: 8px 0 0;

            color: #dddddd;

        }


        /* =====================================================
           SUCCESS PAGE
        ===================================================== */

        .checkout-success {

            max-width: 650px;

            margin: 50px auto;

            padding: 45px;

            text-align: center;

            background: #0e1423;

            border: 1px solid #b8862c;

            border-radius: 15px;

            box-sizing: border-box;

        }


        .checkout-success-icon {

            font-size: 50px;

            margin-bottom: 15px;

        }


        .checkout-success h2 {

            margin: 0 0 15px;

            color: #ffffff;

            font-size: 30px;

        }


        .checkout-success p {

            color: #cccccc;

            line-height: 1.6;

        }


        .checkout-success strong {

            color: #f0b429;

        }


        .checkout-success-btn {

            display: inline-block;

            margin-top: 20px;

            padding: 13px 24px;

            background: #b8862c;

            color: #0e1423;

            border-radius: 8px;

            text-decoration: none;

            font-weight: bold;

            font-size: 14px;

            transition: 0.2s;

        }


        .checkout-success-btn:hover {

            background: #d4a33a;

        }


        /* =====================================================
           RESPONSIVE
        ===================================================== */

        @media (max-width: 768px) {

            .checkout-page {

                padding: 50px 20px;

            }


            .checkout-page-header h1 {

                font-size: 32px;

            }


            .checkout-container {

                grid-template-columns: 1fr;

            }


            .checkout-actions {

                flex-direction: column;

            }


            .checkout-back,
            .checkout-place-btn {

                width: 100%;

                text-align: center;

                box-sizing: border-box;

            }


            .checkout-product {

                align-items: flex-start;

            }


            .checkout-success {

                padding: 30px 20px;

            }

        }

    </style>

</head>


<body>


<main class="checkout-page">


<?php if ($orderSuccess): ?>


    <!-- =====================================
         ORDER SUCCESS
    ====================================== -->

    <div class="checkout-success">


        <div class="checkout-success-icon">
            ✓
        </div>


        <h2>
            Order Placed Successfully!
        </h2>


        <p>
            Thank you,
            <strong>
                <?= htmlspecialchars($customer["full_name"]) ?>
            </strong>.
        </p>


        <p>
            Your order has been successfully placed
            and is currently
            <strong>Pending</strong>.
        </p>


        <p>

            Order Number:

            <strong>
                #<?= (int) $order_id ?>
            </strong>

        </p>


        <p>

            Total Amount:

            <strong>
                ₱<?= number_format($total, 2) ?>
            </strong>

        </p>


        <a
            href="shop.php"
            class="checkout-success-btn"
        >
            Continue Shopping
        </a>


    </div>


<?php else: ?>


    <!-- =====================================
         CHECKOUT HEADER
    ====================================== -->

    <div class="checkout-page-header">

        <span>
            NAVA FADE STUDIO
        </span>


        <h1>
            Checkout
        </h1>


        <p>
            Review your information and order before placing it.
        </p>

    </div>


    <?php if (!empty($errorMessage)): ?>


        <!-- =================================
             ERROR
        ================================== -->

        <div class="checkout-error">

            <strong>
                Order Failed
            </strong>


            <p>
                <?= htmlspecialchars($errorMessage) ?>
            </p>

        </div>


    <?php endif; ?>


    <!-- =====================================
         CHECKOUT CONTENT
    ====================================== -->

    <div class="checkout-container">


        <!-- =================================
             CUSTOMER INFORMATION
        ================================== -->

        <div class="checkout-box">


            <h2>
                Customer Information
            </h2>


            <div class="customer-info-row">

                <span class="customer-info-label">
                    Full Name
                </span>


                <span class="customer-info-value">

                    <?= htmlspecialchars($customer["full_name"]) ?>

                </span>

            </div>


            <div class="customer-info-row">

                <span class="customer-info-label">
                    Email
                </span>


                <span class="customer-info-value">

                    <?= htmlspecialchars($customer["email"]) ?>

                </span>

            </div>


            <div class="customer-info-row">

                <span class="customer-info-label">
                    Contact Number
                </span>


                <span class="customer-info-value">

                    <?= htmlspecialchars($customer["contact_number"]) ?>

                </span>

            </div>


        </div>


        <!-- =================================
             ORDER SUMMARY
        ================================== -->

        <div class="checkout-box">


            <h2>
                Order Summary
            </h2>


            <?php foreach ($orderItems as $item): ?>


                <div class="checkout-product">


                    <div class="checkout-product-info">


                        <h3>
                            <?= htmlspecialchars($item["product_name"]) ?>
                        </h3>


                        <p>

                            Quantity:
                            <?= (int) $item["quantity"] ?>

                        </p>


                        <p>

                            ₱<?= number_format($item["price"], 2) ?>
                            each

                        </p>


                    </div>


                    <div class="checkout-product-subtotal">

                        ₱<?= number_format($item["subtotal"], 2) ?>

                    </div>


                </div>


            <?php endforeach; ?>


            <!-- TOTAL -->

            <div class="checkout-total">


                <span>
                    Total
                </span>


                <span>

                    ₱<?= number_format($total, 2) ?>

                </span>


            </div>


        </div>


    </div>


    <!-- =====================================
         CHECKOUT ACTIONS
    ====================================== -->

    <div class="checkout-actions">


        <a
            href="cart.php"
            class="checkout-back"
        >
            ← Back to Cart
        </a>


        <form
            method="POST"
            action="checkout.php"
        >


            <button
                type="submit"
                class="checkout-place-btn"
            >
                Place Order
            </button>


        </form>


    </div>


<?php endif; ?>


</main>


</body>

</html>