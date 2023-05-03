<?php
include '../header.php';
?>

<?php

// Check if the user is logged in, if not then redirect him to login page
if(!$loggedin){
    header("location: ../accounts/login.php");
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
    $sqlReviewDelete = "DELETE FROM reviews WHERE reviews.review_key = ?";

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        // Get meeting id of the button pressed
        $postKeys = array_keys($_POST);
        $reviewId = $postKeys[0];
        if(strpos($reviewId, "D")){
            $reviewId = substr($reviewId, 0, -1);

            if($stmt = $mysqli->prepare($sqlReviewDelete)){
                $stmt->bind_param("s", $reviewId);
                if($stmt->execute()){
                    // Now refresh page so deleted meeting is reflected
                    header("Refresh:0");
                }
            }
        }
        else{
            // They want to edit the meeting, store ID in session, and go to single meeting update page
            $_SESSION["selected_review"] = $reviewId;
            header('Location: /reviews/single-review-edit.php');
        }
    }

?>

<?php

    if ($permission == 2) {
        $sql = "SELECT * FROM reviews;";
    } else {
        $sql = "SELECT * FROM reviews WHERE reviews.account_key = ?;";
    }

    if($stmt = $mysqli->prepare($sql)){
        // Bind variables to the prepared statement as parameters
        if ($permission == 2) {
            $stmt->bind_param("s", $_SESSION["account_key"], $_SESSION["account_key"]);
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
                    echo "<Strong>Review Key: </Strong>";
                    echo $row['review_key'];
                    echo ", ";
                    echo "<Strong>Item Key: </Strong>";
                    echo $row['item_key'];
                    echo ", ";
                    echo "<Strong>Header: </Strong>";
                    echo $row['header'];
                    echo ", ";
                    echo "<Strong>Body: </Strong>";
                    echo $row['body'];
                    echo ", ";
                    echo "<Strong>Stars: </Strong>";
                    echo $row['item_key'];
                    echo ", ";
                    echo "<Strong>Created: </Strong>";
                    echo $row['created_at'];
                    echo "<form method='post'><input type='submit' class='button' name='";
                    echo $row['review_key'];
                    echo "' value='Update review'></form>";
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
