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
    // Delete query to delete item with given item key
    $sqlDeleteItem = "DELETE FROM items WHERE item_key = ?";

    $itemk = "";
    $itemk_err = "";

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        if(empty(trim($_POST["Title"]))){
            $itemk_err = "Please enter the item key you wish to delete.";
        } else{
            $itemk = trim($_POST["Title"]);
        }
        if(empty($itemk_err)){
            // Now delete the item with the given key
            if($stmt = $mysqli->prepare($sqlDeleteItem)){
                $stmt->bind_param("i", $itemk);
                // Attempt to execute the prepared statement
                $stmt->execute();
            }
        }
      header('Location: /accounts/welcome.php');
    }
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