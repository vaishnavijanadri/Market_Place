<?php

session_start();

// If user is not logged in, redirect to login page
if(!isset($_SESSION['user']))
{
    header("Location:login.php");
    exit();
}

include 'db.php';

if(isset($_POST['submit']))
{

    $seller = $_SESSION['user_id'];
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $desc = mysqli_real_escape_string($conn, $_POST['description']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $cat = mysqli_real_escape_string($conn, $_POST['category_name']);
    $cond = mysqli_real_escape_string($conn, $_POST['item_condition']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    $query = "INSERT INTO items(seller_id, title, description, price, category_name, item_condition, status)
              VALUES('$seller', '$title', '$desc', '$price', '$cat', '$cond', '$status')";

    $run = mysqli_query($conn, $query);

    if($run)
    {
        $success = "Item Posted Successfully!";
    }
    else
    {
        $error = "Failed to post item!";
    }

}

?>

<!DOCTYPE html>
<html>
<head>
<title>Post Item</title>

<style>

body{
    font-family:Arial;
    background:#f4f6f9;
}

.container{
    width:500px;
    margin:40px auto;
    background:white;
    padding:30px;
    border-radius:12px;
    box-shadow:0px 0px 12px rgba(0,0,0,0.2);
}
h2{
    text-align:center;
    color:#4b49ac;
}

label{
    font-weight:bold;
    display:block;
    margin-top:10px;
}

input, select{
    width:100%;
    padding:10px;
    margin-top:5px;
    margin-bottom:15px;
    border:1px solid #ccc;
    border-radius:6px;
    box-sizing:border-box;
}

.btn{
    width:100%;
    padding:12px;
    background:#4b49ac;
    color:white;
    border:none;
    border-radius:6px;
    font-size:16px;
    cursor:pointer;
}

.btn:hover{
    background:#3734a9;
}

.success{
    color:green;
    text-align:center;
    font-size:18px;
}

.error{
    color:red;
    text-align:center;
}

.back-link{
    display:block;
    text-align:center;
    margin-top:15px;
    color:#4b49ac;
    text-decoration:none;
}

</style>
</head>

<body>

<div class="container">

    <h2>🛍 Post Second-Hand Item</h2>

    <?php
    if(isset($success))
    {
        echo "<p class='success'>✅ $success</p>";
        echo "<a href='viewitems.php' class='back-link'>📦 View Items</a>";
    }

    if(isset($error))
    {
        echo "<p class='error'>❌ $error</p>";
    }
    ?>

    <form method="POST">

        <label>Title:</label>
        <input type="text" name="title" placeholder="Enter item title" required>

        <label>Description:</label>
        <input type="text" name="description" placeholder="Enter description" required>

        <label>Price (₹):</label>
        <input type="number" name="price" placeholder="Enter price" required>

        <label>Category Name:</label>
        <select name="category_name" required>
            <option value="">-- Select Category --</option>
            <option value="Electronics">Electronics</option>
            <option value="Books">Books</option>
            <option value="Fashion">Fashion / Clothing</option>
            <option value="Home & Furniture">Home & Furniture</option>
            <option value="Vehicles">Vehicles</option>
            <option value="Sports">Sports & Outdoors</option>
            <option value="Other">Other</option>
        </select>

        <label>Condition:</label>
        <select name="item_condition" required>
            <option value="">-- Select Condition --</option>
            <option value="New">New</option>
            <option value="Like New">Like New</option>
            <option value="Good">Good</option>
            <option value="Fair">Fair</option>
            <option value="Poor">Poor</option>
        </select>

        <label>Status:</label>
        <select name="status" required>
            <option value="Available">Available</option>
            <option value="Sold">Sold</option>
        </select>

        <input type="submit" name="submit" value="Post Item" class="btn">

    </form>

    <a href="dashboard.php" class="back-link">⬅ Back to Dashboard</a>

</div>
</body>
</html>