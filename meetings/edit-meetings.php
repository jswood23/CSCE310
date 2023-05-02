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
    $sqlMeetingDelete = "DELETE FROM meetings WHERE meetings.meeting_key = ?"; 
    $sqlBridgeDelete = "DELETE FROM bridges WHERE bridges.account_key = ? AND bridges.meeting_key = ?";
    $sqlGetCurrentAttendees = "SELECT * FROM bridges WHERE bridges.meeting_key = ?"; 

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        // Get meeting id of the button pressed
        $postKeys = array_keys($_POST);
        $meetingId = $postKeys[0]; 
        if(strpos($meetingId, "D")){
            // They want to delete the meeting selected
            $meetingId = substr($meetingId, 0, -1);

            // Remove all bridge entries for meeting
            if($stmt = $mysqli->prepare($sqlGetCurrentAttendees)){
                // Get list of current attendess
                $stmt->bind_param("s", $meetingId);
                if($stmt->execute()){
                    $curAttendees = $stmt->get_result();
                }
            }
            $stmt = $mysqli->prepare($sqlBridgeDelete);
            $stmt->bind_param("ss", $curAccount, $meetingId);
            $stmt->execute();
            foreach (mysqli_fetch_array($curAttendees) as $a){
                $stmt = $mysqli->prepare($sqlBridgeDelete);
                $stmt->bind_param("ss", $a, $meetingId);
                $stmt->execute();
            }

            // Now delete the meeting itself
            if($stmt = $mysqli->prepare($sqlMeetingDelete)){
                $stmt->bind_param("s", $meetingId);
                if($stmt->execute()){
                    // Now refresh page so deleted meeting is reflected
                    header("Refresh:0");
                }
            }
        }
        else{
            // They want to edit the meeting, store ID in session, and go to single meeting update page
            $_SESSION["selected_meeting"] = $meetingId;
            header('Location: /meetings/single-meeting-edit.php');
        }
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
                // Present all meetings to the user
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
                    echo "<form method='post'><input type='submit' class='button' name='";
                    echo $row['meeting_key'];
                    echo "D' value='Delete meeting'></form>";
                    echo "<br><br>";
                }
            }
        }
    }


    $mysqli->close();
?>
</body>
</html>