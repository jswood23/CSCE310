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

$sqlGetSelectedMeeting = "SELECT * FROM meetings WHERE meeting_key = ?";
$sqlGetCurrentAttendees = "SELECT * FROM bridges WHERE bridges.meeting_key = ?";
$address = $endtime = $itemkey = $md = $oi = $st = "";

// Load in original details of the selected meeting from last page, so they can be presented to user
if($stmt = $mysqli->prepare($sqlGetSelectedMeeting)){

    // Get meeting
    $stmt->bind_param("s", $_SESSION["selected_meeting"]);
            
    // Attempt to execute the prepared statement
    if($stmt->execute()){
        // Save original meeting details
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $address = $row['address'];
        $endtime = $row['end_time'];
        $itemkey = $row['item_key'];
        $md = $row['meet_desc'];
        $oi = $row['organizer'];
        $st = $row['start_time'];
    }
}

?>

<!DOCTYPE html>
<html>
<body>
<?php

    $sqlUpdateMeeting = "UPDATE meetings SET start_time = ?, end_time = ?, address = ?, item_key = ?, meet_desc = ?, organizer = ? WHERE meetings.meeting_key = ?;";
    $sqlBridgeInsert = "INSERT INTO bridges (account_key, meeting_key) VALUES (?,?);"; 
    $sqlBridgeDelete = "DELETE FROM bridges WHERE bridges.account_key = ? AND bridges.meeting_key = ?"; 

    $curAccount = $_SESSION["account_key"];
    $curUserEmail = $_SESSION["email"];

    $address_err = $endtime_err = $itemkey_err = $md_err = $oi_err = $st_err = "";

    if($_SERVER["REQUEST_METHOD"] == "POST"){

        // Make sure that every field is populated
        if(empty(trim($_POST["Address"]))){
            $address_err = "Please enter address.";
        } else{
            $address = trim($_POST["Address"]);
        }

        if(empty(trim($_POST["EndTime"]))){
            $endtime_err = "Please enter endtime.";
        } else{
            $endtime = trim($_POST["EndTime"]);
        }

        if(empty(trim($_POST["ary2"]))){
            $itemkey_err = "Please select item.";
        } else{
            $itemkey = trim($_POST["ary2"]);
        }

        if(empty(trim($_POST["MD"]))){
            $md_err = "Please enter meeting description.";
        } else{
            $md = trim($_POST["MD"]);
        }

        if(empty(trim($_POST["OI"]))){
            $oi_err = "Please enter organizare id's.";
        } else{
            $oi = trim($_POST["OI"]);
        }

        if(empty(trim($_POST["ST"]))){
            $st_err = "Please enter start time.";
        } else{
            $st = trim($_POST["ST"]);
        }

        if(empty($address_err) && empty($endtime_err) && empty($itemkey_err) && empty($md_err) && empty($oi_err) && empty($st_err)){
            if($stmt = $mysqli->prepare($sqlUpdateMeeting)){

                // Update the meeting table itself for this specific meeting
                $stmt->bind_param("sssssss", $st, $endtime, $address, $itemkey, $md, $oi, $_SESSION["selected_meeting"]);
                        
                // Attempt to execute the prepared statement
                if($stmt->execute()){
                    // Meetings table is now updated, now to focus on bridges table
                    // First remove current bridge entries (so as to not have duplicates)
                    if($stmt = $mysqli->prepare($sqlGetCurrentAttendees)){
                        // Get list of current attendess
                        $stmt->bind_param("s", $_SESSION["selected_meeting"]);
                        if($stmt->execute()){
                            $curAttendees = $stmt->get_result();
                        }
                    }
                    // Remove current user from bridge table
                    $stmt = $mysqli->prepare($sqlBridgeDelete);
                    $stmt->bind_param("ss", $curAccount, $_SESSION["selected_meeting"]);
                    $stmt->execute();
                    // Remove all other attendees from bridge table
                    foreach (mysqli_fetch_array($curAttendees) as $a){
                        $stmt = $mysqli->prepare($sqlBridgeDelete);
                        $stmt->bind_param("ss", $a, $_SESSION["selected_meeting"]);
                        $stmt->execute();
                    }
                    
                    // Now add new list of attendees to bridge table 
                    if($stmt = $mysqli->prepare($sqlBridgeInsert)){
                        // First add meeting creator as attendee
                        $stmt->bind_param("ss", $curAccount, $_SESSION["selected_meeting"]);
                                
                        // Attempt to execute the prepared statement
                        if($stmt->execute()){
                            // Works! Now add the other attendees :)
                            $values = $_POST['ary'];

                            foreach ($values as $a){
                                $stmt = $mysqli->prepare($sqlBridgeInsert);
                                $stmt->bind_param("ss", $a, $_SESSION["selected_meeting"]);
                                $stmt->execute();
                            }
                        }
                    }
                }
            }
            header('Location: /accounts/welcome.php');
        }
    }

?>
<br>
<h3>Update Meeting:</h3>
<br>
<form method ="post" name="trial">
    
    Address: <input type="text" name="Address" id="testId" value="<?php echo $address;?>">
    <br><br>
    Start Time: <input type="datetime-local" name="ST" id="testId" value="<?php echo $st;?>">
    <br><br>
    End Time: <input type="datetime-local" name="EndTime" id="testId" value="<?php echo $endtime;?>">
    <br><br>
    Select Item for Meeting Topic: <br>
    <select name="ary2">
        <?php

            $sqlGetPossibleAttendees = "SELECT * FROM items";

            if($stmt = $mysqli->prepare($sqlGetPossibleAttendees)){
                // Get list of possible items
                if($stmt->execute()){
                    $result = $stmt->get_result();
                    if($result->num_rows == 0){
                        echo "No items in this database! You need to have a topic for your meeting... so create an item first.";
                    }
                    else{
                        foreach ($result as $row) {
                            if($row['item_key'] != $itemkey){
                                echo "<option value='";
                                echo $row['item_key'];
                                echo "'>";
                                echo $row['item_title'];
                                echo "</option>";
                            }
                            else{
                                echo "<option value='";
                                echo $row['item_key'];
                                echo "' selected>";
                                echo $row['item_title'];
                                echo "</option>";
                            }  
                        }
                    }
                }
            }

        ?> 
    </select>
    <br><br>
    Meeting Description: <input type="text" name="MD" id="testId" value="<?php echo $md;?>">
    <br><br>
    Select the Organizer of Meeting:
    <select name="OI">
        <?php

            $sqlGetPossibleAttendees = "SELECT * FROM accounts";

            if($stmt = $mysqli->prepare($sqlGetPossibleAttendees)){
                // Get list of possible attendees
                if($stmt->execute()){
                    $result = $stmt->get_result();
                        foreach ($result as $row) {
                            if($row['account_key'] != $oi){
                                echo "<option value='";
                                echo $row['account_key'];
                                echo "'>";
                                echo $row['name_first'];
                                echo " ";
                                echo $row['name_last'];
                                echo "</option>";
                            }
                            else{
                                echo "<option value='";
                                echo $row['account_key'];
                                echo "' selected>";
                                echo $row['name_first'];
                                echo " ";
                                echo $row['name_last'];
                                echo "</option>";
                            }
                        }
                    
                }
            }

        ?> 
    </select>
    <br><br>
    Select All Attendees (you are an attendee by default): <br>
    <select name="ary[]" multiple="multiple">
        <?php

            $sqlGetPossibleAttendees = "SELECT * FROM accounts";

            if($stmt = $mysqli->prepare($sqlGetCurrentAttendees)){
                // Get list of current attendess to autofill them for the user to easily see
                $stmt->bind_param("s", $_SESSION["selected_meeting"]);
                if($stmt->execute()){
                    $curAttendees = $stmt->get_result();

                    // Now get list of possible attendees
                    if($stmt = $mysqli->prepare($sqlGetPossibleAttendees)){
                        if($stmt->execute()){
                            $result = $stmt->get_result();
                            if($result->num_rows == 0){
                                echo "No other users in this database! You will be the only attendee, but thats alright :) ";
                            }
                            else{
                                foreach ($result as $row) {
                                    if($row['account_key'] != $_SESSION["account_key"]){
                                        if(in_array($row['account_key'], mysqli_fetch_array($curAttendees))){
                                            echo "<option value='";
                                            echo $row['account_key'];
                                            echo "' selected>";
                                            echo $row['name_first'];
                                            echo " ";
                                            echo $row['name_last'];
                                            echo "</option>";
                                        }
                                        else{
                                            echo "<option value='";
                                            echo $row['account_key'];
                                            echo "'>";
                                            echo $row['name_first'];
                                            echo " ";
                                            echo $row['name_last'];
                                            echo "</option>";
                                        }
                                    }
                                }
                            }
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