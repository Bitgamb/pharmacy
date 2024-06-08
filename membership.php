<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page or any other appropriate action
    header("Location: login.php");
    exit();
}

// Database connection
include "includes/functions.php"; // Include your database connection file

// Check if user's membership is already active
$user_id = $_SESSION['user_id'];
$sql = "SELECT membership_status FROM user WHERE user_id = ?";
$stmt = $connection->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($membership_status);
$stmt->fetch();
$stmt->close();

// If membership status is active, redirect to index.php
if ($membership_status == 'active') {
    echo "<script>alert('You have already subscribed.');</script>";
    header("Location: membership_success.php");
    exit();
}
// Razorpay payment integration
// Define your Razorpay key and other options
$razorpay_key = "rzp_test_5uPzGreHV3wiCs";
$membership_amount = 500; // Example membership amount in INR

// Check if payment is successful
if (isset($_POST['razorpay_payment_id'])) {
    $payment_id = $_POST['razorpay_payment_id'];

    // Update membership status in the database
    $user_id = $_SESSION['user_id'];
    $sql = "UPDATE user SET membership_status = 'active' WHERE user_id = ?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    // Close the database connection
    $stmt->close();
    $connection->close();

    // Redirect to membership success page
    header("Location: membership_success.php");
    exit();
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Membership Subscription</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        h1 {
            color: #007bff;
            text-align: center;
        }

        .container {
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: center;
            max-width: 400px;
            width: 100%;
        }

        p {
            margin-bottom: 20px;
            color: #333;
        }

        .offer {
            background-color: #e9ecef;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .offer:hover {
            background-color: #ced4da;
        }

        form {
            margin-top: 20px;
        }

        input[type="submit"] {
            background-color: #007bff;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Membership Subscription</h1>
        
        <div>
            <p>Unlock exclusive benefits and savings with our pharmacy membership program, designed to enhance your healthcare experience and prioritize your well-being.</p>
            <div class="offer">
                <p>Membership Fee: ₹<?php echo $membership_amount; ?></p>
                <p>Special Offer: Get 15% discount on your first subscription!</p>
            </div>

            <!-- Razorpay payment form -->
            <form action="" method="POST">
                <script src="https://checkout.razorpay.com/v1/checkout.js"
                        data-key="<?php echo $razorpay_key; ?>"
                        data-amount="<?php echo $membership_amount * 100; ?>"
                        data-currency="INR"
                        data-buttontext="Pay Now"
                        data-name="Pharmacy Membership"
                        data-description="Unlock exclusive benefits"
                        data-prefill.name="<?php echo $_SESSION['user_fname'] . ' ' . $_SESSION['user_lname']; ?>"
                        data-prefill.email="<?php echo $_SESSION['email']; ?>"
                        data-theme.color="#F37254"
                        data-order_id="your_order_id">
                </script>
                <input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id">
            </form>
        </div>
    </div>
</body>
</html>
