<?php

/*skapa tillstånd mellan klient och server*/
session_start();

/*Kollar om användaren redan här inloggad*/
if(isset( $_SESSION['user_id'] )) {
    $message = 'Användaren är redan inloggad.';
}
/* kolla att användarnamn och lösenord är satta*/
if(!isset( $_POST['tasty_username'], $_POST['tasty_password'])) {
    $message = 'Ange giltigt användarnamn och lösenord!';
}
/* kolla att användarnman har rätt längd*/
elseif (strlen( $_POST['tasty_username']) > 20 || strlen($_POST['tasty_username']) < 4) {
    $message = 'Fel användarnamn';
}
/*kolla att lösenord har rätt längd*/
elseif (strlen( $_POST['tasty_password']) > 20 || strlen($_POST['tasty_password']) < 4) {
    $message = 'Fel lösenord';
}
/*kolla att användarnamn är på rätt form*/
elseif (ctype_alnum($_POST['tasty_username']) != true) {
    /*** if there is no match ***/
    $message = "Fel användarnamn";
}
/* kolla att lösenord är på rätt form */
elseif (ctype_alnum($_POST['tasty_password']) != true) {
        /*** if there is no match ***/
        $message = "Fel lösenord";
}
else {
    /*** data kan hämtas eller läggas in i databas*/
    $tasty_username = filter_var($_POST['tasty_username'], FILTER_SANITIZE_STRING);
    $tasty_password = filter_var($_POST['tasty_password'], FILTER_SANITIZE_STRING);

    /*kryptera lösenord*/
    $tasty_password = sha1( $tasty_password );
    
    /*uppkoppling mot databas*/
    
    $mysql_hostname = 'localhost';

    
    $mysql_username = 'root';

    
    $mysql_password = 'admin';

   
    $mysql_dbname = 'tastyrecipes';

    try {
        
        $dbh = new PDO("mysql:host=$mysql_hostname;dbname=$mysql_dbname", $mysql_username, $mysql_password);
        /*** $message = a message saying we have connected ***/

        /*** sätt rätt felmeddelanden*/
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        /*sql statement*/
        $stmt = $dbh->prepare("SELECT tasty_user_id, tasty_username, tasty_password FROM tasty_users 
                    WHERE tasty_username = :tasty_username AND tasty_password = :tasty_password");

        /*bind parametrar*/
        $stmt->bindParam(':tasty_username', $tasty_username, PDO::PARAM_STR);
        $stmt->bindParam(':tasty_password', $tasty_password, PDO::PARAM_STR, 40);

        /*ekekvera*/
        $stmt->execute();

        /*kolla resultat*/
        $user_id = $stmt->fetchColumn();

       
        if($user_id == false) {
                $message = 'login misslyckades';
        } else {
                $_SESSION['user_id'] = $user_id;
                $message = 'Du är inloggad!';
        }


    }
    catch(Exception $e) {
        /*kastas om vi inte kan kontakta databasservern*/
        $message = 'Just nu kan vi inte ta hand om din förfrågan. Försök igen vid ett senare tillfälle';
    }
}
?>

<html>
<head>
<title>Tasty Recipes Login</title>
</head>
<body>
<p><?php echo $message; ?>
</body>
</html>