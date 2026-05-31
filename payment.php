<?php

session_start();

// If user is not logged in, redirect to login page
if(!isset($_SESSION['user']))
{
    header("Location:login.php");
    exit();
}

include 'db.php';

// Check if item id is provided
if(!isset($_GET['id']))
{
    header("Location:viewitems.php");
    exit();
}

$id = $_GET['id'];

$query = "SELECT * FROM items WHERE item_id='$id'";
$result = mysqli_query($conn, $query);

// Check if item exists
if(mysqli_num_rows($result) == 0)
{
    header("Location:viewitems.php");
    exit();
}

$row = mysqli_fetch_assoc($result);

if(isset($_POST['pay']))
{
    // Mark as Sold (or update status)
    $update = "UPDATE items
               SET status='Sold'
               WHERE item_id='$id'";
    mysqli_query($conn, $update);

    // Create an Order record
    $buyer_id = $_SESSION['user_id'];
    $amount = $row['price'];
    $payment_method = "UPI";
    if (isset($_POST['card_number']) && !empty($_POST['card_number']) && empty($_POST['upi_id'])) {
        $payment_method = "Card";
    } else if (isset($_POST['upi_id']) && !empty($_POST['upi_id']) && isset($_POST['card_number']) && !empty($_POST['card_number'])) {
        $payment_method = "UPI & Card"; // standard fallback if both are submitted
    }
    
    $order_query = "INSERT INTO orders (buyer_id, item_id, amount, payment_method) 
                    VALUES ('$buyer_id', '$id', '$amount', '$payment_method')";
    mysqli_query($conn, $order_query);

    // Notify the seller
    $seller_id = $row['seller_id'];
    $buyer_name = $_SESSION['username'];
    $item_title = $row['title'];
    $msg = "Your item '$item_title' has been sold to '$buyer_name'.";
    $msg_escaped = mysqli_real_escape_string($conn, $msg);
    
    $notif_query = "INSERT INTO notifications (user_id, message, is_read) 
                    VALUES ('$seller_id', '$msg_escaped', 'Unread')";
    mysqli_query($conn, $notif_query);

    $success = true;
}

?>

<!DOCTYPE html>
<html>
<head>

<title>Payment</title>

<style>

body{
    font-family:Arial;
    background:#f4f6f9;
}

.container{
    width:450px;
    margin:60px auto;
    background:white;
    padding:30px;
    border-radius:10px;
    box-shadow:0px 0px 10px gray;
}

h2{
    text-align:center;
    color:#4b49ac;
}

h3{
    color:#333;
}

input{
    width:100%;
    padding:10px;
    margin-top:10px;
    margin-bottom:15px;
    border:1px solid #ccc;
    border-radius:5px;
    box-sizing:border-box;
}

button{
    width:100%;
    padding:12px;
    background:#28a745;
    color:white;
    border:none;
    font-size:16px;
    border-radius:6px;
    cursor:pointer;
}

button:hover{
    background:#218838;
}

.sold{
    text-align:center;
    color:red;
    font-size:22px;
    font-weight:bold;
}

.success{
    text-align:center;
    color:green;
    font-size:20px;
}

.notification{
    text-align:center;
    color:blue;
    font-size:16px;
}

.back-link{
    display:block;
    text-align:center;
    margin-top:20px;
    background:#4b49ac;
    color:white;
    padding:12px 20px;
    text-decoration:none;
    border-radius:6px;
    font-size:16px;
}

.back-link:hover{
    background:#3734a9;
}

</style>

</head>

<body>

<div class="container">

    <h2>💳 Payment Gateway</h2>

    <?php

    if(isset($success))
    {
    ?>

        <p class="success">✅ Payment Successful!</p>

        <p class="notification">
            📢 Seller Notification: Item Sold Successfully
        </p>

        <a href="viewitems.php" class="back-link">
            📦 Browse Items
        </a>

    <?php
    }
    else if($row['status'] == "Sold")
    {
    ?>

        <div class="sold">
            ❌ Item Already Sold
        </div>

        <a href="viewitems.php" class="back-link">
            ⬅ Back to Browse Items
        </a>

    <?php
    }
    else
    {
    ?>

        <h3>Item: <?php echo $row['title']; ?></h3>

        <h3>Price: ₹<?php echo $row['price']; ?></h3>

        <form method="POST">

            <input type="text"
                   name="upi_id"
                   placeholder="Enter UPI ID"
                   required>

            <input type="text"
                   name="card_number"
                   placeholder="Enter Card Number"
                   required>

            <button type="submit"
                    name="pay">

                Pay Now

            </button>

        </form>

        <a href="viewitems.php" class="back-link">
            ⬅ Back to Browse Items
        </a>

    <?php
    }
    ?>

</div>

</body>
</html>