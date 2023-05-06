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

// Base command before parameters are binded
$sqlGetSelectedReview = "SELECT * FROM reviews WHERE review_key = ?";
$account_key = $item_key = $header = $body = $stars = $created_at = "";

// Load in original details of the selected review from last page, so they can be presented to user
if($stmt = $mysqli->prepare($sqlGetSelectedReview)){

    // Get review
    $stmt->bind_param("s", $_SESSION["selected_review"]);

    // Attempt to execute the prepared statement
    if($stmt->execute()){
        // Save original review details
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $review_key = $row['review_key'];
        $item_key = $row['item_key'];
        $header = $row['header'];
        $body = $row['body'];
        $stars = $row['stars'];
    }
}

?>

<!DOCTYPE html>
<html>
<body>
<?php

    // Base command before parametes are binded.
    $sqlUpdateReview = "UPDATE reviews SET item_key = ?, header = ?, body = ?, stars = ?  WHERE review_key = ?;";

     // Get session variables
    $curAccount = $_SESSION["account_key"];
    $curUserEmail = $_SESSION["email"];

    // Initialize error variables
    $item_key_err = $header_err = $body_err = $stars_err = $created_at_err = "";

    if($_SERVER["REQUEST_METHOD"] == "POST"){

        // Make sure that every field is populated
        if(empty(trim($_POST["item_key"]))){
            $item_key_err = "Please select something to review.";
        } else{
            $item_key = trim($_POST["item_key"]);
        }

       // Make sure that every field is populated
        if(empty(trim($_POST["header"]))){
            $header_err = "Please enter header text.";
        } else{
            $header = trim($_POST["header"]);
        }

        // Make sure that every field is populated
        if(empty(trim($_POST["body"]))){
            $body_err = "Please enter body text.";
        } else{
            $body = trim($_POST["body"]);
        }

        // Make sure that every field is populated and is an int and between 0 and 5.
        $stars = trim($_POST["stars"]);
        if(!isset($stars) || $stars === ''){
            $stars_err = "Please enter a number of stars.";
        } else if (!is_numeric($stars)){
            $stars_err = "Please enter a number of stars that is an integer.";
        } else if (intval($stars) > 5 || intval($stars) < 0){
            $stars_err = "Please enter a number of stars that is between 0 and 5, inclusive.";
        } else {
            $stars = intval($stars);
        }

        // If no error, bind parameters to SQL insert statement and execute
        if(empty($item_key_err) && empty($header_err) && empty($body_err) && empty($stars_err)){

            if($stmt = $mysqli->prepare($sqlUpdateReview)){

                // Create review
                $stmt->bind_param("sssss", $item_key, $header, $body, $stars, $_SESSION["selected_review"]);
                $stmt->execute();
            }
            header('Location: /reviews/view-reviews.php');
        }
    }

?>
<br>
<h3>Update Review:</h3>
<br>
<form method ="post" name="trial">

    Select Item to Review: <br>
    <select name="item_key">
        <?php

            // We need to select all items to display to the user.
            $sqlGetPossibleItems = "SELECT * FROM items";

            if($stmt = $mysqli->prepare($sqlGetPossibleItems)){
                // Get list of possible items
                if($stmt->execute()){
                    $result = $stmt->get_result();
                    if($result->num_rows == 0){
                        echo "No items in this database! You need to have an item to review ... so create an item first.";
                    }
                    else{
                        foreach ($result as $row) {
                            if($row['item_key'] != $itemkey){
                                echo "<option value='";
                                echo $row['item_key'];
                                echo "'>";
                                echo $row['item_title'];
                                echo "</option>";
                            }
                            else{
                                echo "<option value='";
                                echo $row['item_key'];
                                echo "' selected>";
                                echo $row['item_title'];
                                echo "</option>";
                            }
                        }
                    }
                }
            }

        ?>
    </select>
    <?php echo (!empty($item_key_err)) ? "ERROR: " . $item_key_err : ''; ?>
    <br><br>
    Header: <input type="text" name="header" id="header" value="<?php echo $header;?>">
    <?php echo (!empty($header_err)) ? "ERROR: " . $header_err : ''; ?>
    <br><br>
    Body: <input type="text" name="body" id="body" value="<?php echo $body;?>">
    <?php echo (!empty($body_err)) ? "ERROR: " . $body_err : ''; ?>
    <br><br>
    Stars: <input type="text" name="stars" id="stars" value="<?php echo $stars;?>">
    <?php echo (!empty($stars_err)) ? "ERROR: " . $stars_err : ''; ?>
    <br><br>
    <input type="submit" value="Submit">

</form>

</body>
</html>
