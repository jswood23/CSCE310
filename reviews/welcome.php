<?php
include '../header.php';
?>

<?php
// I made this code with the help of a tutorial: https://www.tutorialrepublic.com/php-tutorial/php-mysql-login-system.php

// Check if the user is logged in, if not then redirect him to login page
if(!$loggedin){
    header("location: login.php");
    exit;
}
?>

<body>
    <h1 class="my-5">Hi, <b><?php echo htmlspecialchars($_SESSION["name_first"]); ?></b>. Let's review some books.</h1>
    <p>
        <a href="create-review.php" class="btn btn-primary ml-3">Create Review</a>
        <a href="view-reviews.php" class="btn btn-primary ml-3">View Reviews</a>
        <a href="select-review.php" class="btn btn-primary ml-3">Edit/Delete Reviews</a>
    </p>
</body>
</html>