<?php

session_start();

// If already logged in, go to dashboard
if(isset($_SESSION['user']))
{
    header("Location:dashboard.php");
    exit();
}

include 'db.php';

$error = "";

if(isset($_POST['login']))
{

    $email = $_POST['email'];
    $password = $_POST['password'];

    $query = "SELECT * FROM users
              WHERE email='$email'
              AND password='$password'";

    $result = mysqli_query($conn, $query);

    $count = mysqli_num_rows($result);

    if($count > 0)
    {
        $row = mysqli_fetch_assoc($result);

        $_SESSION['user'] = $email;
        $_SESSION['username'] = $row['username'];
        $_SESSION['user_id'] = $row['user_id'];

        header("Location:dashboard.php");
        exit();
    }
    else
    {
        $error = "Invalid Email or Password!";
    }

}

?>

<!DOCTYPE html>
<html>
<head>

<title>Login</title>

<style>

body{
    font-family:Arial;
    background:#f4f6f9;
}

.container{
    width:400px;
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

.error{
    color:red;
    text-align:center;
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
    background:#4b49ac;
    color:white;
    border:none;
    font-size:16px;
    border-radius:6px;
    cursor:pointer;
}

button:hover{
    background:#3734a9;
}

a{
    text-decoration:none;
    color:#4b49ac;
}

</style>

</head>

<body>

<div class="container">

    <h2>User Login</h2>

    <?php
    if($error != "")
    {
        echo "<p class='error'>$error</p>";
    }
    ?>

    <form method="POST">

        <input type="email"
               name="email"
               placeholder="Enter Email"
               required>

        <input type="password"
               name="password"
               placeholder="Enter Password"
               required>

        <button type="submit"
                name="login">

            Login

        </button>

    </form>

    <br>

    <a href="register.php">

        Create New Account

    </a>

</div>

</body>
</html>