<?php

/*** begin the session ***/
session_start();

if(!isset($_SESSION['user_id'])) {
    $message = 'Du måste vara inloggad för att komma åt den här sidan!';
}
else {
    try {
        /*** connect to database ***/
        /*** mysql hostname ***/
        $mysql_hostname = 'localhost';

        /*** mysql username ***/
        $mysql_username = 'root';

        /*** mysql password ***/
        $mysql_password = 'admin';

        /*** database name ***/
        $mysql_dbname = 'tastyrecipes';


        /*** select the users name from the database ***/
        $dbh = new PDO("mysql:host=$mysql_hostname;dbname=$mysql_dbname", $mysql_username, $mysql_password);
        /*** $message = a message saying we have connected ***/

        /*** set the error mode to excptions ***/
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        /*** prepare the insert ***/
        $stmt = $dbh->prepare("SELECT tasty_username FROM tasty_users 
        WHERE tasty_user_id = :tasty_user_id");

        /*** bind the parameters ***/
        $stmt->bindParam(':tasty_user_id', $_SESSION['user_id'], PDO::PARAM_INT);

        /*** execute the prepared statement ***/
        $stmt->execute();

        /*** check for a result ***/
        $tasty_username = $stmt->fetchColumn();

        /*** if we have no something is wrong ***/
        if($tasty_username == false) {
            $message = 'Åtkomst nekad';
        } else {
            $message = 'Välkommen '.$tasty_username;
        }
    }
    catch (Exception $e) {
        /*** if we are here, something is wrong in the database ***/
        $message = 'Just nu kan vi inte ta hand om din förfrågan. Försök igen vid ett senare tillfälle';
    }
}

?>

<html>
<head>
<title>Endast för medlemmar</title>
</head>
<body>
<h2><?php echo $message; ?></h2>
</body>
</html>