<?php
include '../header.php';
?>

<?php

// Check if the user is logged in, if not then redirect him to login page
if(!$loggedin){
    header("location: ../accounts/login.php");
    exit;
}

if($_SESSION["permission"] < 2){
    header('Location: /accounts/welcome.php');
    exit;
}

// Include config file
require_once "../config.php";



?>

<!DOCTYPE html>
<html>
<body>
<?php

    $sqlReviews = "INSERT INTO reviews (account_key, item_key, header, body, stars) VALUES (?,?,?,?,?);";
    $sqlGetNewReviewKey = "SELECT review_key FROM reviews WHERE account_key = ? AND item_key = ? AND header = ? AND body = ? AND stars = ?";

    $curAccount = $_SESSION["account_key"];
    $curUserEmail = $_SESSION["email"];

    $item_key = $header = $body = $stars = $created_at = "";
    $item_key_err = $header_err = $body_err = $stars_err = $created_at_err = "";

    if($_SERVER["REQUEST_METHOD"] == "POST"){

        // Make sure that every field is populated
        if(empty(trim($_POST["item_key"]))){
            $item_key_err = "Please select something to review.";
        } else{
            $item_key = trim($_POST["item_key"]);
        }

        if(empty(trim($_POST["header"]))){
            $header_err = "Please enter header text.";
        } else{
            $header = trim($_POST["header"]);
        }

        if(empty(trim($_POST["body"]))){
            $body_err = "Please enter body text.";
        } else{
            $body = trim($_POST["body"]);
        }

        $stars_validate = trim($_POST["stars"]);
        if(empty(trim($_POST["stars"]))){
            $stars_err = "Please enter a number of stars.";
        } else if (filter_var($stars, FILTER_VALIDATE_INT, array('options' => array('min_range' => 0, 'max_range' => 5))) == false){
            $stars_err = "Please enter a number of stars that is an integer between of 0 and 5 inclusive.";
        } else {
            $stars = intval($stars_validate);
        }

        if(empty($item_key_err) && empty($header_err) && empty($body_err) && empty($stars_err)){
            if($stmt = $mysqli->prepare($sqlReviews)){

                // Create meeting
                $stmt->bind_param("sssss", $curAccount, $item_key, $header, $body, $stars);
                $stmt->execute()
            }
            header('Location: /accounts/welcome.php');
        }
    }

?>
<br>
<h3>Create New Review:</h3>
<br>
<form method ="post" name="">

    Select Item to Review: <br>
    <select name="item_key">
        <?php

            $sqlGetPossibleItems = "SELECT * FROM items";

            if($stmt = $mysqli->prepare($sqlGetPossibleItems)){
                // Get list of possible items
                if($stmt->execute()){
                    $result = $stmt->get_result();
                    if($result->num_rows == 0){
                        echo "No items in this database! You need have an item to review... so create an item first.";
                    }
                    else{
                        // Show selector for items
                        foreach ($result as $row) {
                            echo "<option value='";
                            echo $row['item_key'];
                            echo "'>";
                            echo $row['item_title'];
                            echo "</option>";

                        }
                    }
                }
            }

        ?>
    </select>
    <br><br>
    Header: <input type="text" name="header" id="testId">
    <br><br>
    Body: <input type="text" name="body" id="testId">
    <br><br>
    Stars (0-5): <input type="text" name="stars" id="testId">
    <br><br>
    <input type="submit" value="Submit">

</form>

</body>
</html>