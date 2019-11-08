<?php

include("database.php");

// Create an activated column in database with a true/false value and change value when page visited
// Link the email to a unique page from $activation_code
// Check code from url is the same as code from database
// If not display error message
// Display a success message in that activation page

//  Display a standard error page on activation.php


// Set global variables for page load
$code = "";

$error = false;
$error_message = [];

$success = false;

// PHP data handling. Getting code from the url
if (isset($_GET["code"])) {

    // Setting a variable - code to get this code from the url
    $code = $_GET["code"];

    // Making sure data is safe to enter into database
    $clean_code = mysqli_real_escape_string($db_connection, $code);

    // Fetching data from database where activation code is the same as the code from the url
    $query = "SELECT * FROM `users` 
        WHERE `activation code` = '$clean_code'";

    $result = mysqli_query($db_connection, $query);

    // If there is a result that matches the query (activation code from database is the same as code from url)
    if (mysqli_num_rows($result) > 0){

        // Setting the fetched data (row) to a variable, so we can access it
        $row = mysqli_fetch_assoc($result);

        // If account status is pending
        if ($row["account status"] === "pending") {

            $query = "UPDATE `users` 
            SET `account status` = 'active'
            WHERE `activation code` = '$clean_code';
            ";

            $result = mysqli_query($db_connection, $query);

            if ($result) {

                // query ran okay
                if (mysqli_affected_rows($db_connection) === 1) {
                    // and we changed 1 or more rows of data
                    $success = true;
                
                } else { // If the query didnt run

                    $error = true;
                    $error_message[] = "Oops! Something went wrong with the database.";

                } 
            
            } else { //If the query didn't update the status

                $error = true;
                $error_message[] = "Oops! Something went wrong with the database.";

            }

        } else { // If the account status is already active

            $error = true;
            $error_message[] = "Your account is already active, please <a href='login.php'>Log In</a> to continue.";

        }
        
    } else { // If the code link isn't the same

        $error = true;
        $error_message[] = "Oops! Something went wrong with your activation. Please re-try the link in your email.";

    }
    
} else { 

    $error = true;
    $error_message[] = "Oops! Something went wrong with your activation. Please re-try the link in your email.";

}

?>

<html>

    <h1>Activate</h1>

    <?php if ($success) { ?>

        <p>Your account has been activated.</p>

        <p>Now <a href="login.php">Log In</a>.<p>

    <?php } else { 

        foreach($error_message AS $message) {
        
            echo "<p>$message</p>";

        }

    } ?>

</html>