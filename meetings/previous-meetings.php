<?php
include '../header.php';
?>

<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}


// Include config file
require_once "../config.php";

$curUserId = $_SESSION["id"];
$curUserEmail = $_SESSION["username"];

?>

<!DOCTYPE html>
<html>
<body>
<?php
// Get all of the elements from the bridge table using the logged in user's Account_Key. Then use this to get all of the relevant meeting keys, and use them in the Meeting Table.

/* // whenever accounts table is implemented
$sql = "SELECT m.*
        FROM meeting m
        INNER JOIN bridge b ON m.meeting_key = b.meeting_key
        INNER JOIN accounts a ON a.account_key = b.account_key
        WHERE a.account_id = ?";
*/
// Bridge tables fun :)
$sql = "SELECT m.* FROM meeting m INNER JOIN bridge b ON m.meeting_key = b.meeting_key INNER JOIN users a ON a.id = b.account_key WHERE a.id = ?;";

if($stmt = $mysqli->prepare($sql)){
    // Bind variables to the prepared statement as parameters
    $stmt->bind_param("s", $_SESSION["id"]);
            
    // Attempt to execute the prepared statement
    if($stmt->execute()){
        $result = $stmt->get_result();
        if($result->num_rows == 0){
            echo "No previous meetings! You are user with id: ";
            echo $_SESSION["id"];
        }
        else{
            echo "Previous meetings found: <br>";
            foreach ($result as $row) {
                echo "Meeting Key: ";
                echo $row['meeting_key'];
                echo ", ";
                echo "Start Time: ";
                echo $row['start_time'];
                echo ", ";
                echo "End Time: ";
                echo $row['end_time'];
                echo ", ";
                echo "Address: ";
                echo $row['address'];
                echo ", ";
                echo "Key of item being discussed: ";
                echo $row['item_key'];
                echo ", ";
                echo "Meeting Description: ";
                echo $row['meet_desc'];
                echo ", Organizer: ";
                echo $row['organizer'];
                echo "<br>";
            }
        }
    }
}


$mysqli->close();
?>
</body>
</html>