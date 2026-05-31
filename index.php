<?php

session_start();

// If user is already logged in, go to dashboard
if(isset($_SESSION['user']))
{
    header("Location:dashboard.php");
    exit();
}

?>

<!DOCTYPE html>
<html>
<head>
<title>RE-SELL</title>

<style>

body{
    font-family:Arial;
    background:#f4f6f9;
    margin:0;
    padding:0;
}

.header{
    background:#4b49ac;
    color:white;
    padding:20px;
    text-align:center;
    font-size:35px;
    font-weight:bold;
}
.container{
    width:400px;
    margin:80px auto;
    background:white;
    padding:40px;
    border-radius:12px;
    box-shadow:0px 0px 15px rgba(0,0,0,0.2);
    text-align:center;
}

.btn{
    display:block;
    background:#4b49ac;
    color:white;
    padding:15px;
    margin:20px 0;
    text-decoration:none;
    border-radius:8px;
    font-size:18px;
    transition:0.3s;
}
.btn:hover{
    background:#3734a9;
}

</style>
</head>

<body>

<div class="header">
    🛒 RE-SELL
</div>

<div class="container">

    <h2>Welcome</h2>

    <a href="login.php" class="btn">🔑 Login</a>

    <a href="register.php" class="btn">📝 Register</a>

</div>

</body>
</html>