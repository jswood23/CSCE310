<?php
include '../header.php';
?>

<?php

// Check if the user is logged in, if not then redirect him to login page
if(!$loggedin){
    header("location: ../accounts/login.php");
    exit;
}

// Check if user has correct permissions
if($_SESSION["permission"] == 0){
    header('Location: /accounts/welcome.php');
    exit;
}


// Include config file
require_once "../config.php";

$curAccount = $_SESSION["account_key"];
$curUserEmail = $_SESSION["email"];
$permission = $_SESSION["permission"]

?>

<!DOCTYPE html>
<html>
<body>

<?php
    // Base command before parameters are binded
    $sqlReviewDelete = "DELETE FROM reviews WHERE reviews.review_key = ?";

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        // Get review id of the button pressed
        $postKeys = array_keys($_POST);
        $reviewId = $postKeys[0];
        if(strpos($reviewId, "D")){
            $reviewId = substr($reviewId, 0, -1);

            if($stmt = $mysqli->prepare($sqlReviewDelete)){
                $stmt->bind_param("s", $reviewId);
                if($stmt->execute()){
                    // Now refresh page so deleted review is reflected
                    header("Refresh:0");
                }
            }
        }
        else{
            // They want to edit the review, store ID in session, and go to single review update page
            $_SESSION["selected_review"] = $reviewId;
            header('Location: /reviews/single-review-edit.php');
        }
    }

?>

<?php

    // Base command before parameters are binded
    // Only get reviews the current user is allowed to edit or delete.
    if ($permission == 2) {
        $sql = "SELECT reviews.*, CONCAT(accounts.name_first, ' ', accounts.name_last) AS name, items.item_title AS title FROM reviews JOIN accounts ON reviews.account_key = accounts.account_key JOIN items ON reviews.item_key = items.item_key;";
    } else {
        $sql = "SELECT reviews.*, CONCAT(accounts.name_first, ' ', accounts.name_last) AS name, items.item_title AS title FROM reviews JOIN accounts ON reviews.account_key = accounts.account_key JOIN items ON reviews.item_key = items.item_key WHERE reviews.account_key = ?;";
    }

    if($stmt = $mysqli->prepare($sql)){
        // Bind variables to the prepared statement as parameters
        if ($permission !== 2) {
            $stmt->bind_param("s", $curAccount);
        }

        // Attempt to execute the prepared statement
        if($stmt->execute()){
            $result = $stmt->get_result();
            if($result->num_rows == 0){
                echo "<br><h3>You have not created an reviews!</h3>";
            }
            else{
                echo "<br><h3>Reviews:</h3>";
                // Present all reviews to the user
                foreach ($result as $row) {
                    echo "<b>Review Key: </b>";
                    echo $row['review_key'];
                    echo ",  ";
                    echo "<b>User: </b>";
                    echo $row['name'];
                    echo ",  ";
                    echo "<b>Item Title: </b>";
                    echo $row['title'];
                    echo ",  ";
                    echo "<b>Header: </b>";
                    echo $row['header'];
                    echo ",  ";
                    echo "<b>Body: </b>";
                    echo $row['body'];
                    echo ",  ";
                    echo "<b>Stars: </b> ";
                    echo $row["stars"];
                    echo ",  ";
                    echo "<b>Created On:  </b>";
                    echo $row['created_at'];
                    echo "<br><br>";
                    echo "<form method='post'><input type='submit' class='button' name='";
                    echo $row['review_key'];
                    echo "' value='Edit review'></form>";
                    echo "<form method='post'><input type='submit' class='button' name='";
                    echo $row['review_key'];
                    echo "D' value='Delete review'></form>";
                    echo "<br><br>";
                }
            }
        }
    }


    $mysqli->close();
?>
</body>
</html>
