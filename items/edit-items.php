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

    $sqlUpdateItem = "UPDATE items SET item_title = ? , author = ? , isbn = ? , date_added = ? , summary = ? WHERE item_key = ?";
    $sql = "SELECT * FROM items WHERE item_key = ?;";

    $itemkey = "";
    $itemkey_err = "";

    $title = $author = $isbn = $summary = "";
    $title_err = $author_err = $isbn_err = $summary_err = "";
    $date_added = date("Y-m-d", time());


    if($_SERVER["REQUEST_METHOD"] == "POST"){
      if(empty(trim($_POST["Item"]))){

      } else {
        $itemkey = trim($_POST["Item"]);
      }
      if(empty($itemkey_err)){         
        if($stmt = $mysqli->prepare($sql)){
          $stmt->bind_param("i", $itemkey);
          if($stmt->execute()){
            $result = $stmt->get_result();
            foreach($result as $row){
              $itemkey = $row['item_key'];
              $title = $row['item_title'];
              $author = $row['author'];
              $isbn = $row['isbn'];
              $summary = $row['summary'];
            }

            if(empty(trim($_POST["Title"]))){
          
            } else{
                $title = trim($_POST["Title"]);
            }
    
            if(empty(trim($_POST["Author"]))){
    
            } else{
                $author = trim($_POST["Author"]);
            }
    
            if(empty(trim($_POST["ISBN"]))){
    
            } else{
                $isbn = trim($_POST["ISBN"]);
            }
    
            if(empty(trim($_POST["Summary"]))){
    
            } else{
                $summary = trim($_POST["Summary"]);
            }

            if(empty($title_err) && empty($author_err) && empty($isbn_err) && empty($summary_err)){
              if($stmt = $mysqli->prepare($sqlUpdateItem)){
                $stmt->bind_param("ssissi", $title, $author, $isbn, $date_added, $summary, $itemkey);            
                $stmt->execute();
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
<h3>Edit Item:</h3>
<br>
<form method ="post" name="trial">
    Item Key of item you want to edit: <input type="number" name="Item" id="testId">
    <br><br>
    New Title: <input type="text" name="Title" id="testId">
    <br><br>
    New Author: <input type="text" name="Author" id="testId">
    <br><br>
    New ISBN: <input type="number" name="ISBN" id="testId">
    <br><br>
    New Summary of item: <input type="text" name="Summary" id="testId">
    <br><br>

    <input type="submit" value="Submit" name="trial">

</form>

</body>
</html>