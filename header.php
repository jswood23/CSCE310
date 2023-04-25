<!DOCTYPE html>
<html lang="en">

<?php
// I made this code with the help of a tutorial: https://www.tutorialrepublic.com/php-tutorial/php-mysql-login-system.php
// Initialize the session
session_start();


// Check if the user is logged in
$loggedin = false;
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] == true){
    $loggedin = true;
}
?>

<head>
    <meta charset="UTF-8">
    <title>Sign Up</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body{ font: 14px sans-serif; }
        .wrapper{ width: 360px; padding: 20px; }
    </style>
</head>
<body>
    <h1><a class="my-5" href="/" style="none">CSCE 310 Group 20 Book Club</a></h1>
    <?php if($loggedin) : ?>
        <a href="/accounts/welcome.php">Welcome</a>
        <a href="/meetings/previous-meetings.php">Meetings</a>
        <br/>
    <?php else : ?>
        <a href="/accounts/register.php">Register</a>
        <a href="/accounts/login.php">login</a>
        <br/>
    <?php endif; ?>
</body>