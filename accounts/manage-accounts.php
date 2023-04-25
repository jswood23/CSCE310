<?php
include '../header.php';
?>

<?php

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

function get_permission_name($p) {
  switch ($p) {
    case 0:
      return "None";
      break;
    case 1:
      return "Member";
      break;
    case 2:
      return "Admin";
      break;
    default:
      return "Unkown";
      break;
  }
}

function accounts_as_table_rows() {
  require_once "../config.php";
  // get all accounts as list of rows
  $sql = "SELECT account_key, email, name_first, name_last, permission FROM accounts";
  if($stmt = $mysqli->prepare($sql)){
    if($stmt->execute()){
      // Store result
      $stmt->store_result();
      $num_rows = $stmt->num_rows;
      // echo "Numer of rows: ";
      // echo $num_rows;

      for ($i = 0; $i < $num_rows; $i++) {
        $stmt->bind_result($ans_account_key, $ans_email, $ans_name_first, $ans_name_last, $ans_permission);
        $res = $stmt->fetch();
        // start table row
        echo "<tr>";
        // show name
        echo "<td>$ans_name_first $ans_name_last</td>";
        // show email
        echo "<td>$ans_email</td>";
        // show permissions
        $this_user_permission = get_permission_name($ans_permission);
        echo "<td>$this_user_permission</td>";
        // show actions for each user
        if ($ans_permission == 2) {
          echo "<td><a class='btn btn-info ml-3' href='#'>Remove Admin Permissions</a></td>";
        } else if ($ans_permission == 1) {
          echo "<td><a class='btn btn-info ml-3' href='#'>Add Admin Permissions</a></td>";
          echo "<td><a class='btn btn-info ml-3' href='#'>Remove Member Permissions</a></td>";
        } else if ($ans_permission == 0) {
          echo "<td><a class='btn btn-info ml-3' href='#'>Add Member Permissions</a></td>";
          echo "<td><a class='btn btn-info ml-3' href='#'>Delete User Account</a></td>";
        }
        echo "</tr>";
      }
    }
  }
}

?>

<body>
  <h1>Manage Accounts</h1>
  <table>
    <tr>
      <th>Name</th>
      <th>Email</th>
      <th>Permission</th>
      <th>Actions</th>
    </tr>
    <?php accounts_as_table_rows() ?>
  </table>
</body>