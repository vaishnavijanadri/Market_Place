<?php

session_start();

// If user is not logged in, redirect to login page
if(!isset($_SESSION['user']))
{
    header("Location:login.php");
    exit();
}

include 'db.php';

$user_id = $_SESSION['user_id'];

// Mark all as read if requested
if(isset($_GET['mark_all_read']))
{
    $update_query = "UPDATE notifications SET is_read='Read' WHERE user_id='$user_id'";
    mysqli_query($conn, $update_query);
    header("Location:notifications.php");
    exit();
}

// Clear all notifications if requested
if(isset($_GET['clear_all']))
{
    $delete_query = "DELETE FROM notifications WHERE user_id='$user_id'";
    mysqli_query($conn, $delete_query);
    header("Location:notifications.php");
    exit();
}

// Fetch all notifications
$query = "SELECT * FROM notifications 
          WHERE user_id='$user_id' 
          ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);

?>

<!DOCTYPE html>
<html>
<head>
<title>Notifications</title>

<style>

body{
    font-family:Arial;
    background:#f4f6f9;
    padding:20px;
}

.container{
    max-width:600px;
    margin:40px auto;
    background:white;
    padding:30px;
    border-radius:12px;
    box-shadow:0px 0px 15px rgba(0,0,0,0.1);
}

h2{
    text-align:center;
    color:#4b49ac;
    margin-bottom:20px;
}

.action-links{
    display:flex;
    justify-content:space-between;
    margin-bottom:20px;
}

.action-links a{
    color:#4b49ac;
    text-decoration:none;
    font-weight:bold;
}

.action-links a:hover{
    text-decoration:underline;
}

.notif-card{
    background:#f8f9fa;
    border-left:5px solid #4b49ac;
    padding:15px;
    margin-bottom:15px;
    border-radius:4px;
    position:relative;
}

.notif-card.unread{
    background:#eef2f7;
    border-left-color:#28a745;
}

.notif-time{
    font-size:12px;
    color:gray;
    margin-top:5px;
}

.no-notif{
    text-align:center;
    color:gray;
    padding:40px;
}

.back-btn{
    display:block;
    text-align:center;
    background:#4b49ac;
    color:white;
    padding:12px;
    text-decoration:none;
    border-radius:6px;
    margin-top:20px;
}

.back-btn:hover{
    background:#3734a9;
}

</style>
</head>

<body>

<div class="container">

    <h2>🔔 Notifications</h2>

    <div class="action-links">
        <a href="notifications.php?mark_all_read=1">Mark all as read</a>
        <a href="notifications.php?clear_all=1" onclick="return confirm('Clear all notifications?')">Clear all</a>
    </div>

    <?php
    if(mysqli_num_rows($result) > 0)
    {
        while($row = mysqli_fetch_assoc($result))
        {
            $unread_class = ($row['is_read'] == 'Unread') ? 'unread' : '';
    ?>
            <div class="notif-card <?php echo $unread_class; ?>">
                <div><?php echo htmlspecialchars($row['message']); ?></div>
                <div class="notif-time"><?php echo $row['created_at']; ?></div>
            </div>
    <?php
        }
    }
    else
    {
        echo "<div class='no-notif'>No notifications yet.</div>";
    }
    ?>

    <a href="dashboard.php" class="back-btn">⬅ Back to Dashboard</a>

</div>

</body>
</html>
