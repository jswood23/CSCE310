<?php
include '../header.php';
require_once "../config.php";

// Check if the user is logged in, if not then redirect him to login page
if(!$loggedin){
    header("location: login.php");
    exit;
}

// Check if the user has proper permissions, if not then redirect him to welcome page
if($_SESSION["permission"] < 2){
    header("location: welcome.php");
    exit;
}

$this_account = $_GET["account"];
$new_permission = $_GET["permission"];

if ($new_permission == -1) {
  // delete account
  // Prepare a deletion statement
  $sql = "DELETE FROM accounts WHERE account_key = ?";
  if($stmt = $mysqli->prepare($sql)){
    $stmt->bind_param("i", $param_set_permission_account);
    
    $param_set_permission_account = $this_account;

    $stmt->execute();
  }
} elseif ($new_permission >= 0 && $new_permission <= 2) {
  // Prepare an update statement
  $sql = "UPDATE accounts SET permission = ? WHERE account_key = ?";
  if($stmt = $mysqli->prepare($sql)){
    // insert parameters into query
    $stmt->bind_param("ii", $param_set_permission, $param_set_permission_account);
    
    // set parameters
    $param_set_permission = $new_permission;
    $param_set_permission_account = $this_account;

    // execute the sql query
    $stmt->execute();
  }
}

// Redirect to manage accounts page
header("location: manage-accounts.php");
exit;
?>