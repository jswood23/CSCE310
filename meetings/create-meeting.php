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



?>

<!DOCTYPE html>
<html>
<body>
<?php

    $sqlMeetings = "INSERT INTO meetings (address, end_time, item_key, meet_desc, organizer, start_time) VALUES (?,?,?,?,?,?);";
    $sqlGetNewMeetingKey = "SELECT meeting_key FROM meetings WHERE address = ? AND end_time = ? AND item_key = ? AND meet_desc = ? AND organizer = ? AND start_time = ?";
    $sqlBridge = "INSERT INTO bridge (account_key, meeting_key) VALUES (?,?);"; // For adding attendees to newly created meeting one-by-one

    $curAccount = $_SESSION["account_key"];
    $curUserEmail = $_SESSION["email"];

    $address = $endtime = $itemkey = $md = $oi = $st = $nmk = "";
    $address_err = $endtime_err = $itemkey_err = $md_err = $oi_err = $st_err = "";

    if($_SERVER["REQUEST_METHOD"] == "POST"){

        if(empty(trim($_POST["Address"]))){
            $address_err = "Please enter username.";
        } else{
            $address = trim($_POST["Address"]);
        }

        if(empty(trim($_POST["EndTime"]))){
            $endtime_err = "Please enter username.";
        } else{
            $endtime = trim($_POST["EndTime"]);
        }

        if(empty(trim($_POST["ItemKey"]))){
            $itemkey_err = "Please enter username.";
        } else{
            $itemkey = trim($_POST["ItemKey"]);
        }

        if(empty(trim($_POST["MD"]))){
            $md_err = "Please enter username.";
        } else{
            $md = trim($_POST["MD"]);
        }

        if(empty(trim($_POST["OI"]))){
            $oi_err = "Please enter username.";
        } else{
            $oi = trim($_POST["OI"]);
        }

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
                                $stmt->bind_param("ss", $curAccount, $nmk);
                                        
                                // Attempt to execute the prepared statement
                                if($stmt->execute()){
                                    // Works! Now add the other attendees :)
                                    $values = $_POST['ary'];

                                    foreach ($values as $a){
                                        $stmt = $mysqli->prepare($sqlBridge);
                                        $stmt->bind_param("ss", $a, $nmk);
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

    //$mysqli->close();
?>

Create New Meeting:
<br>
<form method ="post" name="trial">

    Address: <input type="text" name="Address" id="testId">
    <br><br>
    Start Time: <input type="datetime-local" name="ST" id="testId">
    <br><br>
    End Time: <input type="datetime-local" name="EndTime" id="testId">
    <br><br>
    Item Key: <input type="text" name="ItemKey" id="testId">
    <br><br>
    Meeting Description: <input type="text" name="MD" id="testId">
    <br><br>
    Organizer's ID: <input type="text" name="OI" id="testId">
    <br><br>
    Select All Attendees: <br>
    <select name="ary[]" multiple="multiple">
        <?php

            $sqlGetPossibleAttendees = "SELECT * FROM users";

            if($stmt = $mysqli->prepare($sqlGetPossibleAttendees)){
                // Get list of possible attendees
                if($stmt->execute()){
                    $result = $stmt->get_result();
                    if($result->num_rows == 0){
                        echo "No other users in this database! You will be the only attendee, nothing wrong with a solo meeting :) ";
                    }
                    else{
                        foreach ($result as $row) {
                            echo "<option value='";
                            echo $row['id'];
                            echo "'>";
                            echo $row['username'];
                            echo "</option>";
                            
                        }
                    }
                }
            }

            $mysqli->close();
        ?> 
    </select>
    
    <br><br>
    <input type="submit" value="Submit">

</form>

</body>
</html>