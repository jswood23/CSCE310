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



?>

<!DOCTYPE html>
<html>
<body>
<?php

$sqlMeetings = "INSERT INTO meeting (address, end_time, item_key, meet_desc, organizer, start_time) VALUES (?,?,?,?,?,?);";
$sqlGetNewMeetingKey = "SELECT meeting_key FROM meeting WHERE address = ? AND end_time = ? AND item_key = ? AND meet_desc = ? AND organizer = ? AND start_time = ?";
$sqlBridge = "INSERT INTO bridge (account_key, meeting_key) VALUES (?,?);"; // get meeting key from just created meeting, add attendees one by one

$curUserId = $_SESSION["id"];
$curUserEmail = $_SESSION["username"];

$address = $endtime = $itemkey = $md = $oi = $st = $nmk = "";
$address_err = $endtime_err = $itemkey_err = $md_err = $oi_err = $st_err = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){

    // Check if username is empty
    if(empty(trim($_POST["Address"]))){
        $address_err = "Please enter username.";
    } else{
        $address = trim($_POST["Address"]);
    }

    // Check if username is empty
    if(empty(trim($_POST["EndTime"]))){
        $endtime_err = "Please enter username.";
    } else{
        $endtime = trim($_POST["EndTime"]);
    }

    // Check if username is empty
    if(empty(trim($_POST["ItemKey"]))){
        $itemkey_err = "Please enter username.";
    } else{
        $itemkey = trim($_POST["ItemKey"]);
    }

    // Check if username is empty
    if(empty(trim($_POST["MD"]))){
        $md_err = "Please enter username.";
    } else{
        $md = trim($_POST["MD"]);
    }

    // Check if username is empty
    if(empty(trim($_POST["OI"]))){
        $oi_err = "Please enter username.";
    } else{
        $oi = trim($_POST["OI"]);
    }

    // Check if username is empty
    if(empty(trim($_POST["ST"]))){
        $st_err = "Please enter username.";
    } else{
        $st = trim($_POST["ST"]);
    }

    if(empty($address_err) && empty($endtime_err) && empty($itemkey_err) && empty($md_err) && empty($oi_err) && empty($st_err)){
        if($stmt = $mysqli->prepare($sqlMeetings)){

            // Create meeting
            $stmt->bind_param("ssssss", $address, $endtime, $itemkey, $md, $oi, $st);
                    
            // Attempt to execute the prepared statement
            if($stmt->execute()){

                // Now get meeting key that was just created
                if($stmt = $mysqli->prepare($sqlGetNewMeetingKey)){
                    $stmt->bind_param("ssssss", $address, $endtime, $itemkey, $md, $oi, $st);
                            
                    // Attempt to execute the prepared statement
                    if($stmt->execute()){
                        $result = $stmt->get_result();
                        $row = $result->fetch_assoc();
                        $nmk = $row['meeting_key'];

                        // Now add attendees to bridge table 
                        if($stmt = $mysqli->prepare($sqlBridge)){
                            // First add meeting creator as attendee
                            $stmt->bind_param("ss", $curUserId, $nmk);
                                    
                            // Attempt to execute the prepared statement
                            if($stmt->execute()){
                                // Works! Now add the other attendees :)
                                $str_arr = explode (",", $_POST["Dees"]); 
                                foreach ($str_arr as $dee) {
                                    $stmt = $mysqli->prepare($sqlBridge);
                                    $stmt->bind_param("ss", $dee, $nmk);
                                    $stmt->execute();
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    header('Location: /users/welcome.php');
}

$mysqli->close();
?>

Create New Meeting:
<form method ="post" name="trial">

Address: <input type="text" name="Address" id="testId">
<br>
Start Time: <input type="datetime-local" name="ST" id="testId">
<br>
End Time: <input type="datetime-local" name="EndTime" id="testId">
<br>
Item Key: <input type="text" name="ItemKey" id="testId">
<br>
Meeting Description: <input type="text" name="MD" id="testId">
<br>
Organizer's ID: <input type="text" name="OI" id="testId">
<br>
Attendee IDs (seperated by comma): <input type="text" name="Dees" id="testId"><br>
<input type="submit" value="Submit">

</form>

</body>
</html>