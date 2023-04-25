<?php
include '../header.php';
?>

<?php
// I made this code with the help of a tutorial: https://www.tutorialrepublic.com/php-tutorial/php-mysql-login-system.php
// Include config file
require_once "../config.php";

// determine whether there are any preexisting accounts
$first_account = false;
$sql = "SELECT account_key FROM accounts;";
if($stmt = $mysqli->prepare($sql)){
    if($stmt->execute()){
        $result = $stmt->get_result();
        if($result->num_rows == 0){
            $first_account = true;
        }
    }
}

// Define variables and initialize with empty values
$email = $name_first = $name_last = $password = $confirm_password = "";
$email_err = $name_first_err = $name_last_err = $password_err = $confirm_password_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Validate email
    if(empty(trim($_POST["email"]))){
        $email_err = "Please enter an email.";
    } else {
        // Prepare a select statement
        $sql = "SELECT account_key FROM accounts WHERE email = ?";
        
        if($stmt = $mysqli->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("s", $param_email);
            
            // Set parameters
            $param_email = trim($_POST["email"]);
            
            // Attempt to execute the prepared statement
            if($stmt->execute()){
                // store result
                $stmt->store_result();
                
                if($stmt->num_rows == 1){
                    $email_err = "This email is already taken.";
                } else {
                    $email = trim($_POST["email"]);
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            $stmt->close();
        }
    }

    // Validate first name
    if(empty(trim($_POST["name_first"]))){
        $name_first_err = "Please enter a first name.";
    } else{
        $name_first = trim($_POST["name_first"]);
    }

    // Validate last name
    if(empty(trim($_POST["name_last"]))){
        $name_last_err = "Please enter a last name.";
    } else{
        $name_last = trim($_POST["name_last"]);
    }
    
    // Validate password
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter a password.";     
    } elseif(strlen(trim($_POST["password"])) < 6){
        $password_err = "Password must have atleast 6 characters.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Validate confirm password
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Please confirm password.";     
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($password_err) && ($password != $confirm_password)){
            $confirm_password_err = "Password did not match.";
        }
    }
    
    // Check input errors before inserting in database
    if(empty($email_err) && empty($name_first_err) && empty($name_last_err) && empty($password_err) && empty($confirm_password_err)){
        
        // Prepare an insert statement
        $sql = "INSERT INTO accounts (email, name_first, name_last, permission, password) VALUES (?, ?, ?, ?, ?)";
         
        if($stmt = $mysqli->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("sssss", $param_email, $param_name_first, $param_name_last, $param_permission, $param_password);
            
            // Set parameters
            $param_email = $email;
            $param_name_first = $name_first;
            $param_name_last = $name_last;
            $param_permission = $first_account ? 2 : 0;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
            
            // Attempt to execute the prepared statement
            if($stmt->execute()){
                $sql = "SELECT account_key FROM accounts WHERE email = ?";

                if($stmt = $mysqli->prepare($sql)){
                    // Bind variables to the prepared statement as parameters
                    $stmt->bind_param("s", $param_email);
                    
                    // Set parameters
                    $param_email = trim($_POST["email"]);
                    
                    // Attempt to execute the prepared statement
                    if($stmt->execute()){
                        // store result
                        $stmt->store_result();
                        $stmt->bind_result($account_key);
                        // log in the user
                        session_start();
                                    
                        // Store data in session variables
                        $_SESSION["loggedin"] = true;
                        $_SESSION["account_key"] = $account_key;
                        $_SESSION["permission"] = $param_permission;
                        $_SESSION["email"] = $email;
                        $_SESSION["name_first"] = $name_first;
                    }
                }                    
                
                // Redirect user to welcome page
                header("location: welcome.php");
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            $stmt->close();
        }
    }
    
    // Close connection
    $mysqli->close();
}
?>
 
<body>
    <div class="wrapper">
        <h2>Sign Up</h2>
        <p>Please fill this form to create an account.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>Email</label>
                <input type="text" name="email" autocomplete="on" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $email; ?>">
                <span class="invalid-feedback"><?php echo $email_err; ?></span>
            </div>
            <div class="form-group">
                <label>First Name</label>
                <input type="text" name="name_first" autocomplete="on" class="form-control <?php echo (!empty($name_first_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $name_first; ?>">
                <span class="invalid-feedback"><?php echo $name_first_err; ?></span>
            </div>
            <div class="form-group">
                <label>Last Name</label>
                <input type="text" name="name_last" autocomplete="on" class="form-control <?php echo (!empty($name_last_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $name_last; ?>">
                <span class="invalid-feedback"><?php echo $name_last_err; ?></span>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $password; ?>">
                <span class="invalid-feedback"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $confirm_password; ?>">
                <span class="invalid-feedback"><?php echo $confirm_password_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Submit">
                <input type="reset" class="btn btn-secondary ml-2" value="Reset">
            </div>
            <p>Already have an account? <a href="login.php">Login here</a>.</p>
        </form>
    </div>    
</body>
</html>