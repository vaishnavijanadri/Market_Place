<?php

session_start();

if(!isset($_SESSION['user']))
{
    header("Location:login.php");
    exit();
}

include 'db.php';

$my_id = $_SESSION['user_id'];

// Show only items posted by OTHER users, not mine
if(isset($_GET['search']) && $_GET['search'] != "")
{
    $search = mysqli_real_escape_string($conn, $_GET['search']);

    $query = "SELECT items.*, categories.category_name 
              FROM items
              INNER JOIN categories ON items.category_id = categories.category_id
              WHERE items.seller_id != '$my_id'
              AND items.title LIKE '%$search%'";
}
else
{
    $query = "SELECT items.*, categories.category_name 
              FROM items
              INNER JOIN categories ON items.category_id = categories.category_id
              WHERE items.seller_id != '$my_id'";
}

$result = mysqli_query($conn, $query);

?>

<!DOCTYPE html>
<html>
<head>
<title>Browse Items</title>

<style>

body{
    font-family:Arial;
    background:#f4f6f9;
    padding:20px;
}

h2{
    text-align:center;
    color:#4b49ac;
    margin-bottom:20px;
}

.top-buttons{
    text-align:center;
    margin-bottom:20px;
}

.top-buttons a{
    background:#4b49ac;
    color:white;
    padding:12px 20px;
    text-decoration:none;
    border-radius:6px;
    font-size:16px;
    margin:0 5px;
}

.top-buttons a:hover{
    background:#3734a9;
}

.search-box{
    text-align:center;
    margin-bottom:20px;
}

.search-box input{
    padding:10px;
    width:300px;
    border-radius:5px;
    border:1px solid gray;
}

.search-box button{
    padding:10px 15px;
    background:#4b49ac;
    color:white;
    border:none;
    border-radius:5px;
    cursor:pointer;
}

.search-box button:hover{
    background:#3734a9;
}

table{
    width:100%;
    background:white;
    border-collapse:collapse;
    box-shadow:0px 0px 10px rgba(0,0,0,0.2);
}

th{
    background:#4b49ac;
    color:white;
    padding:15px;
}

td{
    padding:12px;
    text-align:center;
    border-bottom:1px solid #ddd;
}

tr:hover{
    background:#f1f1f1;
}

.buy{
    background:#007bff;
    color:white;
    padding:8px 12px;
    text-decoration:none;
    border-radius:5px;
}

.buy:hover{
    background:#0056b3;
}

.available{
    color:green;
    font-weight:bold;
}

.sold{
    color:red;
    font-weight:bold;
}

.notavailable{
    color:red;
    font-weight:bold;
}

.no-items{
    text-align:center;
    padding:30px;
    color:gray;
    font-size:18px;
}

</style>

</head>

<body>

<h2>📦 Browse Items</h2>

<div class="top-buttons">

    <a href="dashboard.php">
        ⬅ Dashboard
    </a>

</div>

<div class="search-box">

    <form method="GET">

        <input type="text"
               name="search"
               placeholder="Search by item title"
               value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">

        <button type="submit">
            Search
        </button>

    </form>

</div>

<table>

    <tr>
        <th>Item ID</th>
        <th>Title</th>
        <th>Description</th>
        <th>Category</th>
        <th>Price</th>
        <th>Condition</th>
        <th>Status</th>
        <th>Buy</th>
    </tr>

    <?php

    if(mysqli_num_rows($result) > 0)
    {
        while($row = mysqli_fetch_assoc($result))
        {
    ?>

    <tr>

        <td><?php echo $row['item_id']; ?></td>

        <td><?php echo $row['title']; ?></td>

        <td><?php echo $row['description']; ?></td>

        <td><?php echo htmlspecialchars($row['category_name']); ?></td>

        <td>₹<?php echo $row['price']; ?></td>

        <td><?php echo $row['item_condition']; ?></td>

        <td>
            <?php
            if($row['status'] == "Available")
            {
                echo "<span class='available'>Available</span>";
            }
            else
            {
                echo "<span class='sold'>Sold</span>";
            }
            ?>
        </td>

        <td>
            <?php
            if($row['status'] == "Available")
            {
            ?>
                <a class="buy"
                   href="payment.php?id=<?php echo $row['item_id']; ?>">
                    Buy Now
                </a>
            <?php
            }
            else
            {
                echo "<span class='notavailable'>Sold</span>";
            }
            ?>
        </td>

    </tr>

    <?php
        }
    }
    else
    {
        echo "<tr><td colspan='8' class='no-items'>No items available from other sellers</td></tr>";
    }
    ?>

</table>

</body>
</html>