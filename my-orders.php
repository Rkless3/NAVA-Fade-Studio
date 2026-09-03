<?php

session_start();


/* =========================================
   CHECK IF CUSTOMER IS LOGGED IN
========================================= */

if (!isset($_SESSION["customer_id"])) {
    header("Location: login.php");
    exit();
}


/* =========================================
   DATABASE
========================================= */

require_once "config/Database.php";

$database = new Database();
$db = $database->connect();

$customer_id = $_SESSION["customer_id"];


/* =========================================
   GET CUSTOMER ORDERS
========================================= */

$order_query = $db->prepare("
    SELECT
        id,
        total_amount,
        status,
        created_at
    FROM orders
    WHERE customer_id = :customer_id
    ORDER BY id DESC
");

$order_query->execute([
    ":customer_id" => $customer_id
]);

$orders = $order_query->fetchAll(PDO::FETCH_ASSOC);


/* =========================================
   GET ORDER ITEMS
========================================= */

$order_items = [];

if (!empty($orders)) {

    $item_query = $db->prepare("
        SELECT
            oi.order_id,
            oi.quantity,
            oi.price,
            oi.subtotal,
            COALESCE(
                p.product_name,
                'Product no longer available'
            ) AS product_name,
            p.image
        FROM order_items oi
        LEFT JOIN products p
            ON p.id = oi.product_id
        WHERE oi.order_id = :order_id
        ORDER BY oi.id ASC
    ");

    foreach ($orders as $order) {

        $item_query->execute([
            ":order_id" => $order["id"]
        ]);

        $order_items[$order["id"]] =
            $item_query->fetchAll(PDO::FETCH_ASSOC);
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

    <title>My Orders | NAVA Fade Studio</title>

    <link
        rel="stylesheet"
        href="assets/css/style.css"
    >


    <style>

        /* =========================================
           MY ORDERS PAGE
        ========================================= */

        .orders-page {
            min-height: 100vh;

            padding: 140px 20px 80px;

            background:
                linear-gradient(
                    rgba(14, 20, 35, 0.93),
                    rgba(14, 20, 35, 0.93)
                ),
                url("assets/images/pattern2.png");

            background-size: 300px;
        }


        .orders-container {
            max-width: 1100px;
            margin: 0 auto;
        }


        /* =========================================
           HEADER
        ========================================= */

        .orders-header {
            text-align: center;
            margin-bottom: 45px;
        }

        .orders-header h1 {
            color: #ffffff;
            font-size: 42px;
            margin-bottom: 8px;
        }

        .orders-header h1 span {
            color: #c8942f;
        }

        .orders-header p {
            color: #aeb5c3;
            font-size: 16px;
        }


        /* =========================================
           ORDER CARD
        ========================================= */

        .order-card {

            background: rgba(14, 20, 35, 0.96);

            border: 1px solid #b8862c;

            border-radius: 15px;

            padding: 25px;

            margin-bottom: 25px;

            box-shadow:
                0 10px 30px rgba(0, 0, 0, 0.25);
        }


        /* =========================================
           ORDER HEADER
        ========================================= */

        .order-top {

            display: flex;

            justify-content: space-between;

            align-items: center;

            gap: 20px;

            margin-bottom: 20px;

            padding-bottom: 18px;

            border-bottom:
                1px solid
                rgba(255, 255, 255, 0.08);
        }


        .order-number {

            color: #ffffff;

            font-size: 19px;

            font-weight: bold;
        }


        .order-date {

            color: #9fa7b8;

            font-size: 14px;

            margin-top: 5px;
        }


        /* =========================================
           STATUS
        ========================================= */

        .order-status {

            display: inline-block;

            padding: 8px 15px;

            border-radius: 20px;

            font-size: 13px;

            font-weight: bold;
        }


        .status-pending {

            background: rgba(255, 193, 7, 0.15);

            color: #ffc107;
        }


        .status-confirmed {

            background: rgba(0, 188, 212, 0.15);

            color: #00bcd4;
        }


        .status-processing {

            background: rgba(33, 150, 243, 0.15);

            color: #2196f3;
        }


        .status-completed {

            background: rgba(76, 175, 80, 0.15);

            color: #4caf50;
        }


        .status-cancelled {

            background: rgba(244, 67, 54, 0.15);

            color: #f44336;
        }


        /* =========================================
           PRODUCT ITEM
        ========================================= */

        .order-item {

            display: flex;

            align-items: center;

            gap: 18px;

            padding: 15px 0;

            border-bottom:
                1px solid
                rgba(255, 255, 255, 0.06);
        }


        .order-item:last-child {

            border-bottom: none;
        }


        .order-item-image {

            width: 75px;

            height: 75px;

            border-radius: 10px;

            overflow: hidden;

            background: #151d30;

            flex-shrink: 0;
        }


        .order-item-image img {

            width: 100%;

            height: 100%;

            object-fit: cover;
        }


        .order-item-info {

            flex: 1;
        }


        .order-item-name {

            color: #ffffff;

            font-size: 16px;

            font-weight: bold;

            margin-bottom: 5px;
        }


        .order-item-details {

            color: #9fa7b8;

            font-size: 14px;
        }


        .order-item-subtotal {

            color: #d19a2a;

            font-weight: bold;

            font-size: 16px;
        }


        /* =========================================
           ORDER FOOTER
        ========================================= */

        .order-bottom {

            display: flex;

            justify-content: flex-end;

            align-items: center;

            margin-top: 20px;

            padding-top: 18px;

            border-top:
                1px solid
                rgba(255, 255, 255, 0.08);
        }


        .order-total {

            color: #ffffff;

            font-size: 17px;

            font-weight: bold;
        }


        .order-total span {

            color: #d19a2a;

            font-size: 21px;

            margin-left: 8px;
        }


        /* =========================================
           EMPTY ORDERS
        ========================================= */

        .empty-orders {

            text-align: center;

            padding: 70px 20px;

            background: rgba(14, 20, 35, 0.96);

            border: 1px solid #b8862c;

            border-radius: 15px;
        }


        .empty-orders-icon {

            font-size: 50px;

            margin-bottom: 15px;
        }


        .empty-orders h2 {

            color: #ffffff;

            margin-bottom: 10px;
        }


        .empty-orders p {

            color: #9fa7b8;

            margin-bottom: 25px;
        }


        .shop-btn {

            display: inline-block;

            background: #c8942f;

            color: #0e1423;

            text-decoration: none;

            padding: 13px 25px;

            border-radius: 8px;

            font-weight: bold;

            transition: 0.3s;
        }


        .shop-btn:hover {

            background: #e0aa3b;
        }


        /* =========================================
           BACK BUTTON
        ========================================= */

        .orders-back {

            text-align: center;

            margin-top: 35px;
        }


        .orders-back a {

            color: #c8942f;

            text-decoration: none;

            font-weight: bold;
        }


        .orders-back a:hover {

            text-decoration: underline;
        }


        /* =========================================
           RESPONSIVE
        ========================================= */

        @media (max-width: 600px) {

            .orders-page {

                padding: 120px 15px 60px;
            }


            .orders-header h1 {

                font-size: 32px;
            }


            .order-card {

                padding: 18px;
            }


            .order-top {

                align-items: flex-start;

                flex-direction: column;
            }


            .order-item {

                align-items: flex-start;
            }


            .order-item-image {

                width: 60px;

                height: 60px;
            }


            .order-item-subtotal {

                font-size: 14px;
            }

        }

    </style>

</head>


<body>


<section class="orders-page">

    <div class="orders-container">


        <!-- =========================================
             HEADER
        ========================================= -->

        <div class="orders-header">

            <h1>
                My <span>Orders</span>
            </h1>

            <p>
                View your purchased products and order status.
            </p>

        </div>


        <?php if (empty($orders)): ?>


            <!-- =====================================
                 NO ORDERS
            ====================================== -->

            <div class="empty-orders">

                <div class="empty-orders-icon">
                    🛍️
                </div>

                <h2>
                    No Orders Yet
                </h2>

                <p>
                    You haven't purchased any products from
                    NAVA Fade Studio yet.
                </p>

                <a
                    href="shop.php"
                    class="shop-btn"
                >
                    SHOP NOW
                </a>

            </div>


        <?php else: ?>


            <!-- =====================================
                 ORDERS
            ====================================== -->

            <?php foreach ($orders as $order): ?>

                <div class="order-card">


                    <!-- ORDER HEADER -->

                    <div class="order-top">

                        <div>

                            <div class="order-number">

                                Order
                                #<?= (int) $order["id"] ?>

                            </div>

                            <div class="order-date">

                                <?= date(
                                    "F j, Y • h:i A",
                                    strtotime($order["created_at"])
                                ) ?>

                            </div>

                        </div>


                        <?php

                        $statusClass =
                            strtolower($order["status"]);

                        ?>


                        <span
                            class="order-status status-<?= htmlspecialchars($statusClass) ?>"
                        >

                            <?= htmlspecialchars($order["status"]) ?>

                        </span>

                    </div>


                    <!-- PRODUCTS -->

                    <?php foreach (
                        $order_items[$order["id"]] ?? []
                        as $item
                    ): ?>

                        <div class="order-item">


                            <!-- PRODUCT IMAGE -->

                            <div class="order-item-image">

                                <?php if (!empty($item["image"])): ?>

                                    <img
                                        src="assets/images/<?= htmlspecialchars($item["image"]) ?>"
                                        alt="<?= htmlspecialchars($item["product_name"]) ?>"
                                    >

                                <?php endif; ?>

                            </div>


                            <!-- PRODUCT INFORMATION -->

                            <div class="order-item-info">

                                <div class="order-item-name">

                                    <?= htmlspecialchars(
                                        $item["product_name"]
                                    ) ?>

                                </div>


                                <div class="order-item-details">

                                    ₱<?= number_format(
                                        (float) $item["price"],
                                        2
                                    ) ?>

                                    ×

                                    <?= (int) $item["quantity"] ?>

                                </div>

                            </div>


                            <!-- SUBTOTAL -->

                            <div class="order-item-subtotal">

                                ₱<?= number_format(
                                    (float) $item["subtotal"],
                                    2
                                ) ?>

                            </div>


                        </div>

                    <?php endforeach; ?>


                    <!-- ORDER TOTAL -->

                    <div class="order-bottom">

                        <div class="order-total">

                            Total:

                            <span>

                                ₱<?= number_format(
                                    (float) $order["total_amount"],
                                    2
                                ) ?>

                            </span>

                        </div>

                    </div>


                </div>

            <?php endforeach; ?>


        <?php endif; ?>


        <!-- BACK -->

        <div class="orders-back">

            <a href="index.php">
                ← Back to Home
            </a>

        </div>


    </div>

</section>


</body>

</html>