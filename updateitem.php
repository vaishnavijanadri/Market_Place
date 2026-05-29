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
    header("Location:myitems.php");
    exit();
}

$id = $_GET['id'];
$my_id = $_SESSION['user_id'];

// Get item details ONLY if it belongs to this seller
$query = "SELECT * FROM items WHERE item_id='$id' AND seller_id='$my_id'";
$result = mysqli_query($conn, $query);

// Check if item exists and belongs to this seller
if(mysqli_num_rows($result) == 0)
{
    header("Location:myitems.php");
    exit();
}

$row = mysqli_fetch_assoc($result);

if(isset($_POST['update']))
{

    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $cond = mysqli_real_escape_string($conn, $_POST['item_condition']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    $update = "UPDATE items SET
               title='$title',
               price='$price',
               item_condition='$cond',
               status='$status'
               WHERE item_id='$id' AND seller_id='$my_id'";

    mysqli_query($conn, $update);

    header("Location:myitems.php");
    exit();

}
?>

<!DOCTYPE html>
<html>
<head>
<title>Update Item</title>

<style>

body{
    font-family:Arial;
    background:#f4f6f9;
}
.container{
    width:450px;
    margin:50px auto;
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
    background:#28a745;
    color:white;
    border:none;
    border-radius:6px;
    font-size:16px;
    cursor:pointer;
}

.btn:hover{
    background:#218838;
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

    <h2>✏ Update Item</h2>

    <form method="POST">

        <label>Title:</label>
        <input type="text" name="title"
               value="<?php echo htmlspecialchars($row['title']); ?>" required>

        <label>Price (₹):</label>
        <input type="number" name="price"
               value="<?php echo htmlspecialchars($row['price']); ?>" required>

        <label>Condition:</label>
        <select name="item_condition" required>
            <option value="New" <?php if($row['item_condition']=="New") echo "selected"; ?>>New</option>
            <option value="Like New" <?php if($row['item_condition']=="Like New") echo "selected"; ?>>Like New</option>
            <option value="Good" <?php if($row['item_condition']=="Good") echo "selected"; ?>>Good</option>
            <option value="Fair" <?php if($row['item_condition']=="Fair") echo "selected"; ?>>Fair</option>
            <option value="Poor" <?php if($row['item_condition']=="Poor") echo "selected"; ?>>Poor</option>
        </select>

        <label>Status:</label>
        <select name="status" required>
            <option value="Available" <?php if($row['status']=="Available") echo "selected"; ?>>Available</option>
            <option value="Sold" <?php if($row['status']=="Sold") echo "selected"; ?>>Sold</option>
        </select>

        <input type="submit" name="update"
               value="Update Item" class="btn">

    </form>

    <a href="myitems.php" class="back-link">⬅ Back to My Items</a>

</div>

</body>
</html>