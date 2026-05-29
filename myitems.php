<?php

session_start();

if(!isset($_SESSION['user']))
{
    header("Location:login.php");
    exit();
}

include 'db.php';

$my_id = $_SESSION['user_id'];

// Handle delete - only allow deleting own items
if(isset($_GET['delete']))
{
    $id = $_GET['delete'];

    // Check if item belongs to this user
    $check = "SELECT * FROM items WHERE item_id='$id' AND seller_id='$my_id'";
    $check_result = mysqli_query($conn, $check);

    if(mysqli_num_rows($check_result) > 0)
    {
        $delete = "DELETE FROM items WHERE item_id='$id'";
        mysqli_query($conn, $delete);
    }

    header("Location:myitems.php");
    exit();
}

// Get only MY items
$query = "SELECT * FROM items WHERE seller_id='$my_id'";

$result = mysqli_query($conn, $query);

?>

<!DOCTYPE html>
<html>
<head>
<title>My Items</title>

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

.update{
    background:#28a745;
    color:white;
    padding:8px 12px;
    text-decoration:none;
    border-radius:5px;
}

.update:hover{
    background:#218838;
}

.delete{
    background:#dc3545;
    color:white;
    padding:8px 12px;
    text-decoration:none;
    border-radius:5px;
}

.delete:hover{
    background:#c82333;
}

.available{
    color:green;
    font-weight:bold;
}

.sold{
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

<h2>📋 My Posted Items</h2>

<div class="top-buttons">

    <a href="additem.php">
        ➕ Post New Item
    </a>

    <a href="dashboard.php">
        ⬅ Dashboard
    </a>

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
        <th>Update</th>
        <th>Delete</th>
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
            <a class="update"
               href="updateitem.php?id=<?php echo $row['item_id']; ?>">
                Update
            </a>
        </td>

        <td>
            <a class="delete"
               href="myitems.php?delete=<?php echo $row['item_id']; ?>"
               onclick="return confirm('Are you sure you want to delete this item?')">
                Delete
            </a>
        </td>

    </tr>

    <?php
        }
    }
    else
    {
        echo "<tr><td colspan='9' class='no-items'>You have not posted any items yet</td></tr>";
    }
    ?>

</table>

</body>
</html>
