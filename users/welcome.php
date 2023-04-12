<?php
include '../header.php';
?>

<?php
// I made this code with the help of a tutorial: https://www.tutorialrepublic.com/php-tutorial/php-mysql-login-system.php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
?>

<body>
    <h1 class="my-5">Hi, <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b>. Welcome to our site.</h1>
    <p>
        <a href="reset-password.php" class="btn btn-warning">Reset Your Password</a>
        <a href="logout.php" class="btn btn-danger ml-3">Sign Out of Your Account</a>
        <a href="../meetings/previous-meetings.php" class="btn btn-primary ml-3">See all meetings</a>
        <a href="../meetings/create-meeting.php" class="btn btn-info ml-3">Create new meeting</a>
    </p>
</body>
</html>