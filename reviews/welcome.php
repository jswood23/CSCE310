<?php
include '../header.php';
?>

<?php

// Check if the user is logged in, if not then redirect him to login page
if(!$loggedin){
    header("location: login.php");
    exit;
}

// Check if user has correct permissions
if($_SESSION["permission"] == 0){
    header('Location: /accounts/welcome.php');
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
