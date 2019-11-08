<?php

include("database.php");

// 1. Form
// 2. PHP form handling
// 3. Check user input
// 4. Create an activation code
// 5. Save in database (will need to CREATE TABLE first)
// 6. Send email
// 7. Account creation success error_message

// Setting empty variables for when the page loads
$email = "";
$password = "";
$confirm_password = "";

$error = false;
$error_message = [];
$success = false;

// PHP form handling
if ($_POST) {

    // Setting variables to content of input
    $email = $_POST["email"];
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    // Checking if email input has value and is a valid email address
    if ($email === "") {

        $error = true;
        $error_message[] = "Please complete email field.";
        
    } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = true;
        $error_message[] = "Please provide a valid email address.";

    }

    // Checking if password inputs have values
    if (($password === "") OR ($confirm_password === "")) {

        $error = true;
        $error_message[] = "Please complete password fields.";

    // Checking if password contains numbers, letters and symbols and is 8-50 chars long
    } elseif (!preg_match('/^(?=.*\d)(?=.*[A-Za-z])[0-9A-Za-z!@#$%]{8,50}$/', $password)) {

        $error = true;
        $error_message[] = "Your password must include uppercase, lowercase, numbers and symbols.";

    // Checking that password input is the same as confirm password input
    } elseif ($password !== $confirm_password) {

        $error = true;
        $error_message[] = "Unfortunately your passwords don't match. Please try again.";
    
    }

    if ($error === false) {

        // Unique, random, long
        $activation_code = hash('sha256', $email.time().'nbqebx0@odq0e9'.rand(0,1000000));

        // Make user data safe
        $clean_email = mysqli_real_escape_string($db_connection, $email);

        // // Don't need to do if hashing password
        // $clean_password = mysqli_real_escape_string($db_connection, $password);

        $clean_activation_code = mysqli_real_escape_string($db_connection, $activation_code);

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // CREATE TABLE `users` (
        //     `id` int(11) NOT NULL AUTO_INCREMENT,
        //     `email` varchar(255) NOT NULL,
        //     `password` varchar(255) NOT NULL,
        //     `activation code` varchar(255) NOT NULL,
        //     PRIMARY KEY (`id`)
        //   ) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

        $query = "INSERT INTO `users` 
                (`email`, `password`, `activation code`) 
                VALUES 
                ('$clean_email', '$hashed_password', '$clean_activation_code');";

        $result = mysqli_query($db_connection, $query);

        if ($result) {

            // query ran okay
            if (mysqli_affected_rows($db_connection) === 1) {
                // and we changed 1 or more rows of data
                $link = "http://scotchbox/activation.php?code=".urlencode($activation_code);
                // Send email and replace form html with a html Success page
                $headers = "From: Dev Me <team@example.com>\r\n";
                $headers .= "Reply-To: Help <help@example.com>\r\n";
                $headers .= "MIME-Version: 1.0\r\n";
                $headers .= "Content-Type: text/html;\r\n";

                $subject = "Email Verification";

                $message = "
                    <p>Hello</p>
                    <p>Click <a href='$link'>this link</a> to activate your account.</p>
                    <p>Best Wishes,</p>
                    <p>DevelopMe_ Team.</p>
                    ";

                if (mail($email, $subject, $message, $headers)) {
                    // Sent mail
                    $success = true;

                } else {

                    $error = true;
                    $error_message[] = "Oops! We had trouble sending the email, please check all registration details and try again.";
                
                }

            } else {

                $error = true;
                $error_message[] = "Oops! Something went wrong with the database.";
                // Uh oh, something went wrong
            }

        } else {

            $error = true;
            $error_message[] = "Oops! Something went wrong with the database.";
            // Uh oh, query didn't run! A problem with the query
        }

        // // For retreiving ID
        // if (mysqli_affected_rows($db_connection) > 0){
        //     echo 'New record ID is '.mysqli_insert_id($db_connection);
        // }        
        
    }

}

?>

<html>
<!-- Set up Form -->


    <h1>Register</h1>

    <?php 
    
        if ($success) { 

            echo "<h2>We've made your account</h2>

            <p>New record ID is ".mysqli_insert_id($db_connection);

            echo "<p>Now verify your email to sign in.<p>";

        } else {

    ?>

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
            <label for="password">Confirm Password:*</label>
            <input type="password" name="confirm_password" id="confirmPassword" placeholder="********" required>
        </p>
        <p>
            <input type="submit" value="Create Account">
        </p>
    </form>

    <?php 
        foreach($error_message AS $message) {
            echo "<p>$message</p>";
        }

    }

    ?>

</html>