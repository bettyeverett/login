<?php

include("database.php");

$logged_in = false;

if (isset($_COOKIE['logged_in'])){

    if ('YES' == $_COOKIE['logged_in']) {  

        $logged_in = true;

    }

}

if ($logged_in){

    ?>

        <html>

        <h1>Logged In</h1>

        <p>Congratulations; you have logged in!</p>

        <p>You are now in your account.</p>

        <a href="logout.php">Log Out</a>

    <?php 

} else {
    ?>

        <h1>You are not logged in!</h1>

    <?php 
}

?>

</html>