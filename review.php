<?php

session_start();

if (!isset($_SESSION["customer_id"])) {
    header("Location: login.php");
    exit;
}

require_once "config/Database.php";
require_once "classes/Review.php";

$database = new Database();
$db = $database->connect();

$review = new Review($db);

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $rating = (int) ($_POST["rating"] ?? 0);
    $comment = trim($_POST["comment"] ?? "");

    if ($rating < 1 || $rating > 5) {
        $error = "Please select a rating from 1 to 5 stars.";
    } elseif ($comment === "") {
        $error = "Please write a comment.";
    } else {

        $review->customer_id = $_SESSION["customer_id"];
        $review->rating = $rating;
        $review->comment = $comment;

        if ($review->create()) {
            $success = "Thank you! Your review has been submitted and is waiting for approval.";
        } else {
            $error = "Something went wrong. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Write a Review | NAVA Fade Studio</title>

    <link rel="stylesheet" href="assets/css/style.css">

    <style>
        .review-page {
            min-height: 80vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 70px 20px;
        }

        .review-container {
            width: 100%;
            max-width: 650px;
            background: #fff;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
        }

        .review-container h1 {
            text-align: center;
            margin-bottom: 10px;
            color: #111;
        }

        .review-subtitle {
            text-align: center;
            color: #777;
            margin-bottom: 30px;
        }

        .message {
            padding: 14px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }

        .success-message {
            background: #e8f7e8;
            color: #287a28;
        }

        .error-message {
            background: #ffe8e8;
            color: #b22222;
        }

        .rating-section {
            margin-bottom: 25px;
        }

        .rating-section label,
        .comment-section label {
            display: block;
            font-weight: bold;
            margin-bottom: 10px;
            color: #222;
        }

        .stars {
            display: flex;
            gap: 8px;
            flex-direction: row-reverse;
            justify-content: flex-end;
        }

        .stars input {
            display: none;
        }

        .stars label {
            font-size: 38px;
            color: #ccc;
            cursor: pointer;
            transition: 0.2s;
        }

        .stars label:hover,
        .stars label:hover ~ label,
        .stars input:checked ~ label {
            color: #d4a017;
        }

        .comment-section {
            margin-bottom: 25px;
        }

        .comment-section textarea {
            width: 100%;
            min-height: 150px;
            resize: vertical;
            padding: 14px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-family: inherit;
            font-size: 15px;
            outline: none;
        }

        .comment-section textarea:focus {
            border-color: #b8862c;
        }

        .submit-review-btn {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 8px;
            background: #b8862c;
            color: #fff;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.2s;
        }

        .submit-review-btn:hover {
            background: #956f22;
        }

        .back-home {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #b8862c;
            text-decoration: none;
        }

        .back-home:hover {
            text-decoration: underline;
        }

        @media (max-width: 600px) {
            .review-container {
                padding: 25px;
            }

            .stars label {
                font-size: 32px;
            }
        }
    </style>
</head>

<body>

<?php include "includes/header.php"; ?>

<div class="review-page">

    <div class="review-container">

        <h1>Write a Review</h1>

        <p class="review-subtitle">
            Tell us about your experience at NAVA Fade Studio.
        </p>

        <?php if ($success): ?>

            <div class="message success-message">
                <?= htmlspecialchars($success) ?>
            </div>

            <a href="index.php" class="back-home">
                ← Back to Home
            </a>

        <?php else: ?>

            <?php if ($error): ?>
                <div class="message error-message">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST">

                <div class="rating-section">

                    <label>Your Rating</label>

                    <div class="stars">

                        <input type="radio" id="star5" name="rating" value="5">
                        <label for="star5">★</label>

                        <input type="radio" id="star4" name="rating" value="4">
                        <label for="star4">★</label>

                        <input type="radio" id="star3" name="rating" value="3">
                        <label for="star3">★</label>

                        <input type="radio" id="star2" name="rating" value="2">
                        <label for="star2">★</label>

                        <input type="radio" id="star1" name="rating" value="1">
                        <label for="star1">★</label>

                    </div>

                </div>

                <div class="comment-section">

                    <label for="comment">Your Review</label>

                    <textarea
                        id="comment"
                        name="comment"
                        placeholder="Share your experience..."
                        required
                    ></textarea>

                </div>

                <button type="submit" class="submit-review-btn">
                    Submit Review
                </button>

            </form>

            <a href="index.php" class="back-home">
                ← Back to Home
            </a>

        <?php endif; ?>

    </div>

</div>

</body>
</html>