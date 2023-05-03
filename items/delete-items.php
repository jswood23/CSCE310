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

    $sqlDeleteItem = "DELETE FROM items WHERE item_key = ?";

    $curAccount = $_SESSION["account_key"];
    $curUserEmail = $_SESSION["email"];

    $itemk = "";
    $itemk_err = "";

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        
        if(empty(trim($_POST["Title"]))){
            $itemk_err = "Please enter the item key you wish to delete.";
        } else{
            $itemk = trim($_POST["Title"]);
        }

        if(empty($itemk_err)){
            // Now get meeting key that was just created
            if($stmt = $mysqli->prepare($sqlDeleteItem)){
                $stmt->bind_param("i", $itemk);
                                
                // Attempt to execute the prepared statement
                $stmt->execute();

                
            }

        }

      header('Location: /accounts/welcome.php');
    }

    //$mysqli->close();
?>
<br>
<h3>Delete Item:</h3>
<br>
<form method ="post" name="trial">
    
    Item Key of item you want to delete: <input type="number" name="Title" id="testId">
    <br><br>

    <input type="submit" value="Submit">

</form>

</body>
</html>