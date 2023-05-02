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
    if($_SERVER["REQUEST_METHOD"] == "POST"){

        // Get meeting id to be updated, store it in session, and go to single meeting update page
        $postKeys = array_keys($_POST);
        $meetingId = $postKeys[0]; 
        $_SESSION["selected_meeting"] = $meetingId;
        header('Location: /meetings/single-meeting-edit.php');
    }

?>
    
<?php

    // Get all meetings that this user is the organizer for
    $sql = "SELECT m.* FROM meetings m INNER JOIN bridges b ON m.meeting_key = b.meeting_key INNER JOIN accounts a ON a.account_key = b.account_key WHERE a.account_key = ? AND m.organizer = ?;";

    if($stmt = $mysqli->prepare($sql)){
        // Bind variables to the prepared statement as parameters
        $stmt->bind_param("ss", $_SESSION["account_key"], $_SESSION["account_key"]);
                
        // Attempt to execute the prepared statement
        if($stmt->execute()){
            $result = $stmt->get_result();
            if($result->num_rows == 0){
                echo "<br><h3>You are not the organizer of any meetings!</h3>";
            }
            else{
                echo "<br><h3>Meetings You Organize:</h3>";
                foreach ($result as $row) {
                    echo "<Strong>Meeting Key: </Strong>";
                    echo $row['meeting_key'];
                    echo ", ";
                    echo "<Strong>Start Time: </Strong>";
                    echo $row['start_time'];
                    echo ", ";
                    echo "<Strong>End Time: </Strong>";
                    echo $row['end_time'];
                    echo ", ";
                    echo "<Strong>Address: </Strong>";
                    echo $row['address'];
                    echo ", ";
                    echo "<Strong>Key of item being discussed: </Strong>";
                    echo $row['item_key'];
                    echo ", ";
                    echo "<Strong>Meeting Description: </Strong>";
                    echo $row['meet_desc'];
                    echo ", <Strong>Organizer ID: </Strong>";
                    echo $row['organizer'];
                    echo "<form method='post'><input type='submit' class='button' name='";
                    echo $row['meeting_key'];
                    echo "' value='Update meeting'></form>";
                    echo "<br><br>";
                }
            }
        }
    }


    $mysqli->close();
?>
</body>
</html>