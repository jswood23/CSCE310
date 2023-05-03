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

?>

<!DOCTYPE html>
<html>
<body>
<?php

// Bridge tables are fun to deal with :)
$sql = "SELECT * FROM items;";

if($stmt = $mysqli->prepare($sql)){
    // Bind variables to the prepared statement as parameters
    //$stmt->bind_param("s", $_SESSION["account_key"]);
           
    // Attempt to execute the prepared statement
    if($stmt->execute()){
        $result = $stmt->get_result();
        if($result->num_rows == 0){
            echo "<br>No items added!";
        }
        else{
            echo "<h3>Items:</h3>";
            foreach ($result as $row) {
                echo "Item Key: ";
                echo $row['item_key'];
                echo ", ";
                echo "Title: ";
                echo $row['item_title'];
                echo ", ";
                echo "Author: ";
                echo $row['author'];
                echo ", ";
                echo "ISBN: ";
                echo $row['isbn'];
                echo ", ";
                echo "Date added: ";
                echo $row['date_added'];
                echo ", ";
                echo "Summary of "; 
                echo $row["item_title"];
                echo ": ";
                echo $row['summary'];
                echo "<br><br>";
            }
        }
    }
}

$mysqli->close();
?>
</body>
</html>