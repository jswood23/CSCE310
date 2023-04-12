<?php
include '../header.php';
?>

<?php
// I made this code with the help of a tutorial: https://www.tutorialrepublic.com/php-tutorial/php-mysql-login-system.php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}


// Include config file
require_once "../config.php";

$curUserId = $_SESSION["id"]

?>

<!DOCTYPE html>
<html>
<body>
<?php
// Get all of the elements from the bridge table using the logged in user's Account_Key. Then use this to get all of the relevant meeting keys, and use them in the Meeting Table.

$sql = "SELECT * FROM users WHERE id = ?";

if($stmt = $mysqli->prepare($sql)){
    // Bind variables to the prepared statement as parameters
    $stmt->bind_param("s", $curUserId);
            
    // Attempt to execute the prepared statement
    if($stmt->execute()){
        // Store result
        $stmt->store_result();
    }
}


$mysqli->close();
?>
<
</body>
</html>