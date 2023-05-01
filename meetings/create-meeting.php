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



?>

<!DOCTYPE html>
<html>
<body>
<?php

    $sqlMeetings = "INSERT INTO meetings (address, end_time, item_key, meet_desc, organizer, start_time) VALUES (?,?,?,?,?,?);";
    $sqlGetNewMeetingKey = "SELECT meeting_key FROM meetings WHERE address = ? AND end_time = ? AND item_key = ? AND meet_desc = ? AND organizer = ? AND start_time = ?";
    $sqlBridge = "INSERT INTO bridges (account_key, meeting_key) VALUES (?,?);"; // For adding attendees to newly created meeting one-by-one

    $curAccount = $_SESSION["account_key"];
    $curUserEmail = $_SESSION["email"];

    $address = $endtime = $itemkey = $md = $oi = $st = $nmk = "";
    $address_err = $endtime_err = $itemkey_err = $md_err = $oi_err = $st_err = "";

    if($_SERVER["REQUEST_METHOD"] == "POST"){

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
        header('Location: /accounts/welcome.php');
    }

?>
<br>
<h3>Create New Meeting:</h3>
<br>
<form method ="post" name="trial">
    
    Address: <input type="text" name="Address" id="testId">
    <br><br>
    Start Time: <input type="datetime-local" name="ST" id="testId">
    <br><br>
    End Time: <input type="datetime-local" name="EndTime" id="testId">
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
                            echo "<option value='";
                            echo $row['item_key'];
                            echo "'>";
                            echo $row['item_title'];
                            echo "</option>";
                            
                        }
                    }
                }
            }

        ?> 
    </select>
    <br><br>
    Meeting Description: <input type="text" name="MD" id="testId">
    <br><br>
    Select the Organizer of Meeting:
    <select name="OI">
        <?php

            $sqlGetPossibleAttendees = "SELECT * FROM accounts";

            if($stmt = $mysqli->prepare($sqlGetPossibleAttendees)){
                // Get list of possible attendees
                if($stmt->execute()){
                    $result = $stmt->get_result();
                    if($result->num_rows == 0){
                        echo "No other users in this database! You will be the only attendee, but thats alright :) ";
                    }
                    else{
                        foreach ($result as $row) {
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

        ?> 
    </select>
    <br><br>
    Select All Attendees (you are an attendee by default): <br>
    <select name="ary[]" multiple="multiple">
        <?php

            $sqlGetPossibleAttendees = "SELECT * FROM accounts";

            if($stmt = $mysqli->prepare($sqlGetPossibleAttendees)){
                // Get list of possible attendees
                if($stmt->execute()){
                    $result = $stmt->get_result();
                    if($result->num_rows == 0){
                        echo "No other users in this database! You will be the only attendee, but thats alright :) ";
                    }
                    else{
                        foreach ($result as $row) {
                            if($row['account_key'] != $_SESSION["account_key"]){
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

            $mysqli->close();
        ?> 
    </select>
    
    <br><br>
    <input type="submit" value="Submit">

</form>

</body>
</html>