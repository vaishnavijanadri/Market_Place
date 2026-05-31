<?php

include 'db.php';

$error = "";

if(isset($_POST['register']))
{

    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check if email already exists
    $check = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($conn, $check);

    if(mysqli_num_rows($result) > 0)
    {
        $error = "Email already registered!";
    }
    else
    {
        $query = "INSERT INTO users(username, email, password)
                  VALUES('$username', '$email', '$password')";

        mysqli_query($conn, $query);

        header("Location:login.php");
        exit();
    }

}

?>

<!DOCTYPE html>
<html>
<head>

<title>Register</title>

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

    <h2>Create Account</h2>

    <?php
    if($error != "")
    {
        echo "<p class='error'>$error</p>";
    }
    ?>

    <form method="POST">

        <input type="text"
               name="username"
               placeholder="Enter Username"
               required>

        <input type="email"
               name="email"
               placeholder="Enter Email"
               required>

        <input type="password"
               name="password"
               placeholder="Enter Password"
               required>

        <button type="submit"
                name="register">

            Register

        </button>

    </form>

    <br>

    <a href="login.php">

        Already have an account? Login

    </a>

</div>

</body>
</html>