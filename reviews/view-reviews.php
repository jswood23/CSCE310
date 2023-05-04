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

?>

<!DOCTYPE html>
<html>
<body>
<?php

// Get all items in datbase 
$sql = "SELECT reviews.*, CONCAT(accounts.name_first, ' ', accounts.name_last) AS name, items.item_title AS title FROM reviews JOIN accounts ON reviews.account_key = accounts.account_key JOIN items ON reviews.item_key = items.item_key;";

if($stmt = $mysqli->prepare($sql)){           
    // Attempt to execute the prepared statement
    if($stmt->execute()){
        $result = $stmt->get_result();
        if($result->num_rows == 0){
            echo "<br><h3>No reviews made!</h3>";
        }
        else{
          // Displaying all reviews in database
            echo "<h3>Reviews:</h3>";
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
            }
        }
    }
}

$mysqli->close();
?>
</body>
</html>