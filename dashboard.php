<?php

session_start();

if(!isset($_SESSION['user']))
{
    header("Location:login.php");
    exit();
}

include 'db.php';

// Count unread notifications
$notif_query = "SELECT COUNT(*) as count FROM notifications 
                WHERE user_id='" . $_SESSION['user_id'] . "' 
                AND is_read='Unread'";
$notif_result = mysqli_query($conn, $notif_query);
$notif_row = mysqli_fetch_assoc($notif_result);
$unread_count = $notif_row['count'];

?>

<!DOCTYPE html>
<html>
<head>

<title>Dashboard</title>

<style>

body{
    font-family:Arial;
    background:#f4f6f9;
    text-align:center;
    padding-top:80px;
}

.card{
    width:500px;
    margin:auto;
    background:white;
    padding:40px;
    border-radius:10px;
    box-shadow:0px 0px 10px gray;
}

h1{
    color:#4b49ac;
}

h3{
    color:gray;
}

a{
    display:block;
    margin:20px;
    padding:15px;
    background:#4b49ac;
    color:white;
    text-decoration:none;
    border-radius:6px;
    font-size:18px;
}

a:hover{
    background:#3734a9;
}

.badge{
    background:red;
    color:white;
    padding:3px 8px;
    border-radius:50%;
    font-size:14px;
    margin-left:5px;
}

</style>

</head>

<body>

<div class="card">

    <h1>🛒 RE-SELL</h1>

    <h3>

        Welcome:
        <?php echo $_SESSION['username']; ?>

    </h3>

    <a href="additem.php">

        ➕ Post Item

    </a>

    <a href="myitems.php">

        📋 My Items

    </a>

    <a href="viewitems.php">

        📦 Browse Items

    </a>

    <a href="myorders.php">

        🛒 My Orders & Sales

    </a>

    <a href="notifications.php">

        🔔 Notifications
        <?php
        if($unread_count > 0)
        {
            echo "<span class='badge'>$unread_count</span>";
        }
        ?>

    </a>

    <a href="profile.php">

        👤 My Profile

    </a>

    <a href="project_study_guide.md" download="Project_Study_Guide.md" style="background:#28a745;">

        📄 Download Study Guide

    </a>

    <a href="logout.php">

        🚪 Logout

    </a>

</div>

</body>
</html>