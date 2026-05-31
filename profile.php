<?php

session_start();

// If user is not logged in, redirect to login page
if(!isset($_SESSION['user']))
{
    header("Location:login.php");
    exit();
}

include 'db.php';

$email = $_SESSION['user'];

$query = "SELECT * FROM users
          WHERE email='$email'";

$result = mysqli_query($conn, $query);

$row = mysqli_fetch_assoc($result);

?>

<!DOCTYPE html>
<html>
<head>

<title>Profile</title>

<style>

body{
    font-family:Arial;
    background:#f4f6f9;
}

.container{
    width:450px;
    margin:80px auto;
    background:white;
    padding:30px;
    border-radius:10px;
    box-shadow:0px 0px 10px gray;
}

h2{
    text-align:center;
    color:#4b49ac;
}

p{
    font-size:18px;
    margin:15px 0;
}

a{
    display:block;
    text-align:center;
    margin-top:20px;
    padding:12px;
    background:#4b49ac;
    color:white;
    text-decoration:none;
    border-radius:6px;
}

a:hover{
    background:#3734a9;
}

</style>

</head>

<body>

<div class="container">

    <h2>👤 User Profile</h2>

    <p>

        <b>User ID:</b>
        <?php echo $row['user_id']; ?>

    </p>

    <p>

        <b>Username:</b>
        <?php echo $row['username']; ?>

    </p>

    <p>

        <b>Email:</b>
        <?php echo $row['email']; ?>

    </p>

    <a href="dashboard.php">

        ⬅ Back to Dashboard

    </a>

</div>

</body>
</html>