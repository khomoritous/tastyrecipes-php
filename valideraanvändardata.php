<?php
/*start session för skapa tillstånd mellan klient och server*/
session_start();
/*kollar att lösen och användarnamn är satta via form*/
if(!isset( $_POST['tasty_username'], $_POST['tasty_password'])) {
    $message = 'Skriv giltigt användarenamn och lösenord';
}
/*kollar att form_token är den rätta
elseif( $_POST['form_token'] != $_SESSION['form_token']) {
    $message = 'Felaktig form format';
}*/

/*kollar att antal tecken i användarnamn inte överskrider antal tecken i databasen.*/
elseif (strlen( $_POST['tasty_username']) > 20 || strlen($_POST['tasty_username']) < 4) {
    $message = 'Fel antal tecken';
}
/*kollar att antal tecken i lösenord inte överskrider antal tecken i databasen.*/
elseif (strlen( $_POST['tasty_password']) > 20 || strlen($_POST['tasty_password']) < 4) {
    $message = 'Fel antal tecken';
}
/*kollar att användarnamnet är alfanumerisk.*/
elseif (ctype_alnum($_POST['tasty_username']) != true) {
    $message = "Användarnamnet måste bestå av alfanumeriska tecken";
}
/*kollar att lösenorden består av alfanumeriska tecken*/
elseif (ctype_alnum($_POST['tasty_password']) != true) {
        $message = "Lösenordet måste bestå av alfanumeriska tecken";
}
else {
    /*vi städar upp användarnamne och lösenord*/
    $tasty_username = filter_var($_POST['tasty_username'], FILTER_SANITIZE_STRING);
    $tasty_password = filter_var($_POST['tasty_password'], FILTER_SANITIZE_STRING);
    $tasty_password = sha1( $tasty_password );/*skapar hash till lösenord för lagring i databasen*/
    
    /*databasuppkoppling*/
    $mysql_hostname = 'localhost';
    $mysql_username = 'root';
    $mysql_password = 'admin';
    $mysql_dbname = 'tastyrecipes';

    try {
        /*objekt med konstruktor som samlar och förbereder databasuppkoppling*/
        $dbh = new PDO("mysql:host=$mysql_hostname;dbname=$mysql_dbname", $mysql_username, $mysql_password);
        

        /*sätta felmeddelande format*/

        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        /*göra iordning sql för insättning i tabell i databasen*/
        $stmt = $dbh->prepare("INSERT INTO tasty_users (tasty_username, tasty_password ) VALUES (:tasty_username, :tasty_password )");
        $stmt->bindParam(':tasty_username', $tasty_username, PDO::PARAM_STR);
        $stmt->bindParam(':tasty_password', $tasty_password, PDO::PARAM_STR, 40);
        /*exekvera sql*/
        $stmt->execute();/*** unset the form token session variable ***/
        /*förstör form_token variabeln, vi är färdiga med dem*/
       /* unset( $_SESSION['form_token'] ); */
        /*Meddelande till användaren att det har lyckats*/
        $message = 'Ny användare har lagts till!';
    }
    catch(Exception $e) {
        /*Kastas om användaren redan finns*/
        if( $e->getCode() == 23000) {
            $message = 'Användarnamnet är redan taget!';
        }
        else {
            /*Kastas om vi inte har lyckats kontakta databasservern.*/
            $message = 'Just nu kan vi inte ta hand om din förfrågan. Försök igen vid ett senare tillfälle"';
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>TastyRecipe Login</title>
</head>
<body>
    <p><?php echo $message; ?></p>
</body>
</html>
