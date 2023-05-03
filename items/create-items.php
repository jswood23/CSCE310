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

    $sqlItems = "INSERT INTO items (item_title, author, isbn, date_added, summary) VALUES (?,?,?,?,?);";
    $sqlGetNewItemKey = "SELECT item_title FROM items WHERE item_title = ? AND author = ? AND isbn = ? AND summary = ?";

    $curAccount = $_SESSION["account_key"];
    $curUserEmail = $_SESSION["email"];

    $title = $author = $isbn = $summary = $nmk = "";
    $title_err = $author_err = $isbn_err = $summary_err = "";
    $date_added = date("Y-m-d", time());

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        
        if(empty(trim($_POST["Title"]))){
            $title_err = "Please enter the title.";
        } else{
            $title = trim($_POST["Title"]);
        }

        if(empty(trim($_POST["Author"]))){
            $author_err = "Please enter the author.";
        } else{
            $author = trim($_POST["Author"]);
        }

        if(empty(trim($_POST["ISBN"]))){
            $isbn_err = "Please enter the ISBN.";
        } else{
            $isbn = trim($_POST["ISBN"]);
        }

        if(empty(trim($_POST["Summary"]))){
            $summary_err = "Please enter a summary.";
        } else{
            $summary = trim($_POST["Summary"]);
        }

        if(empty($item_err) && empty($title_err) && empty($author_err) && empty($isbn_err)&& empty($summary_err)){
            if($stmt = $mysqli->prepare($sqlItems)){

                // Create meeting
                $stmt->bind_param("ssiss", $title, $author, $isbn, $date_added, $summary);
                        
                // Attempt to execute the prepared statement
                if($stmt->execute()){

                    // Now get meeting key that was just created
                    if($stmt = $mysqli->prepare($sqlGetNewItemKey)){
                        $stmt->bind_param("ssis", $title, $author, $isbn, $summary);
                                
                        // Attempt to execute the prepared statement
                        if($stmt->execute()){
                            $result = $stmt->get_result();
                            $row = $result->fetch_assoc();
                            $nmk = $row['item_key'];

                        }
                    }
                }
            }
        }
        header('Location: /accounts/welcome.php');
    }

    //$mysqli->close();
?>
<br>
<h3>Add New Item:</h3>
<br>
<form method ="post" name="trial">
    
    Title: <input type="text" name="Title" id="testId">
    <br><br>
    Author: <input type="text" name="Author" id="testId">
    <br><br>
    ISBN: <input type="number" name="ISBN" id="testId">
    <br><br>
    Summary of item: <input type="text" name="Summary" id="testId">
    <br><br>

    <input type="submit" value="Submit">

</form>

</body>
</html>