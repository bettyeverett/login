<?php

include("database.php");

// Create Login form in HTML on page
// PHP form handling
// Check user input
// Retrieve data from database
// Compare data to form (remembering hashed password)
// If match...
// Set status of log in to active
// Send to account page
// If don't match...
// Throw error message

// Create global variables
$email = "";
$password = "";

$error = false;
$error_message = [];

// Set up PHP form handling
if ($_POST) {

    $email = $_POST["email"];
    $password = $_POST["password"];

    // Checking if email input has value and is a valid email address
    if ($email === "") {

        $error = true;
        $error_message[] = "Please complete email field.";

    }    

    // Checking if password input has values
    if ($password === "") {

        $error = true;
        $error_message[] = "Please complete password field.";

    }

    if ($error === false) {

        $clean_email = mysqli_real_escape_string($db_connection, $email);

        //Fetching data
        $query = "SELECT * FROM `users` WHERE `email` = '$clean_email'";

        $result = mysqli_query($db_connection, $query); // Runs the query on the database

        // If there is a result that matches the query 
        if (mysqli_num_rows($result) > 0){

            // Setting the fetched data (row) to a variable, so we can access it
            $row = mysqli_fetch_assoc($result);

            if (password_verify($password, $row['password'])){

                // session_start(); // Start session

                // $_SESSION['logged_in'] = 'YES'; //Log in

                // if (isset($_SESSION['logged_in'])){
                //     if ('YES' == $_SESSION['logged_in']){
                //         echo 'Welcome to your account!';
                //     }
                // }
                
                setcookie('logged_in', 'YES', time()+3600);

                header("Location: account.php");             

            } else {

                $error = true;
                $error_message[] = "Incorrect password. Please try again.";

            }

        } else {

            $error = true;
            $error_message[] = "You don't seem to have an account with us. Please <a href='register.php'>Register Here</a>.";

        }

    } else {

        $error = true;
        $error_message[] = "Please fill in all fields.";
    
    }

} 

?>

<html>

<h1>Log In</h1>

<form action="" method="POST">
        <p>
            <label for="email">Email:*</label>
            <input type="email" name="email" id="email" placeholder="me@myemail.country" minlength="8" maxlength="50" required>
        </p>
        <p>
            <label for="password">Password:*</label>
            <input type="password" name="password" id="password" placeholder="********" required>
        </p>
        <p>
            <input type="submit" value="Login">
        </p>
    </form>

    <?php 

        foreach($error_message AS $message) {

            echo "<p>$message</p>";
    
        }

    ?>

</html>