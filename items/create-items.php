<?php
include '../header.php';
?>

<?php
 
// Check if the user is logged in, if not then redirect him to login page
if(!$loggedin){
    header("location: ../accounts/login.php");
    exit;
}

if($_SESSION["permission"] < 2){
    header('Location: /accounts/welcome.php');
    exit;
}


// Include config file
require_once "../config.php";



?>

<!DOCTYPE html>
<html>
<body>
<?php
    // Query to insert items with the given values into the table items in the database 
    $sqlItems = "INSERT INTO items (item_title, author, isbn, date_added, summary) VALUES (?,?,?,?,?);";

    // variables used to store the given values
    // from the user to add to the database 
    $title = $author = $isbn = $summary = $itemkey = "";
    $title_err = $author_err = $isbn_err = $summary_err = "";
    // Update time for new edit
    $date_added = date("Y-m-d h:i:sa", time());

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

        if(empty($item_err) && empty($title_err) && empty($author_err) && empty($isbn_err) && empty($summary_err)){
            if($stmt = $mysqli->prepare($sqlItems)){

                // Create item
                $stmt->bind_param("ssiss", $title, $author, $isbn, $date_added, $summary);
                        
                // Attempt to execute the prepared statement
                $stmt->execute()
            }
        }
        header('Location: /accounts/welcome.php');
    }

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