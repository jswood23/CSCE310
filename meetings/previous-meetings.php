<?php
include '../header.php';
?>

<?php
 
// Check if the user is logged in, if not then redirect him to login page
if(!$loggedin){
    header("location: login.php");
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
$sql = "SELECT m.* FROM meetings m INNER JOIN bridges b ON m.meeting_key = b.meeting_key INNER JOIN accounts a ON a.account_key = b.account_key WHERE a.account_key = ?;";

if($stmt = $mysqli->prepare($sql)){
    // Bind variables to the prepared statement as parameters
    $stmt->bind_param("s", $_SESSION["account_key"]);
            
    // Attempt to execute the prepared statement
    if($stmt->execute()){
        $result = $stmt->get_result();
        if($result->num_rows == 0){
            echo "No previous meetings! You are user with id: ";
            echo $_SESSION["account_key"];
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