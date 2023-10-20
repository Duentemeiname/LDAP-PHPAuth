<?php
require_once("connect.php");
require_once("functions.php");
include("header.php");


echo '
<div class="background">
<div class="seiteninhalt">';

if ($_SERVER["REQUEST_METHOD"] == "POST") 
{

    $username = checkLDAPInjektion($_POST["nutzername"]);
    $passwort = checkLDAPInjektion($_POST["passwort"]);

    $ergebnis = connectwithUser($username, $passwort);

    $fehler = $ergebnis['error'];
    $email = $ergebnis['mail'];
    $CN = $ergebnis['cn'];

    if(!empty($fehler))
    {
        echo "<div class='fehler'> $fehler </div>" ;
        showLogin();
    }
    else if(empty($email))
    {
        echo "<div class='fehler'> Ihr Account wurde noch nicht synchronisieriert, bitte probieren Sie es später erneut. </div>" ;
        showLogin();
    }
    else if (isset($email) && isset($CN))
    {
        echo '<div class="login">
              <h1 class="center">Login erfolgreich.</h1> <hr>
              <p>Der Nutzername für '.$CN.', um die Office365-Produkte nutzen zu können, lautet: <p> <p><b> '.$email.' </b></p>
              <p> Diese E-Mail-Adresse ist Ihre Authentification bei Microsoft, damit Ihnen über die Schule kostenlos und Datenschutzkonform Office365 
              zu Verfügung gestellt werden kann. Ihr Passwort bei Microsoft entspricht dem, welches Sie sich auch im Schulnetzwerk gegeben habem. 
              Dieses Passwort können Sie nur im Schulnetzwerk ändern. Sollten Sie es vergessen, wenden Sie sich bitte an Ihren IT-Beauftragten. </p>
              <button class="button_redirect" onclick="window.location.href=\''.$AADLoginLink.$email.'\'";>Hier geht es direkt zu Office365.</button> <br>
              <button class="button_redirect" onclick="window.location.href=\''.$GlobalServerUrl.'\'";>Abmelden.</button>
              <button class="button_redirect" onclick="window.location.href=\'#\'";>Datenschutzerklärung.</button>
              <button class="button_redirect" onclick="window.location.href=\'#\'";>Weitere Infos zum Einsatz von Office365.</button>
              </div>
        ';
    }
    else
    {
        echo "<div class='fehler'> Ein unbekannter Fehler ist aufgetreten. </div>" ;
        showLogin();
    }

}
else 
{
    showLogin();
}
function showLogin()
{
    global $ldapDomainName;

    echo'<div class="login">
    <img class="img" src="Windows-Default-Avatar-448x400.png">
    <h1 class="center">Anderer Benutzer</h1>
    <form method="POST" class="login_form">
    <input class="status_input" type="text" name="nutzername" placeholder="Benutzername" required> </br>
    <input class="status_input" type="password" name="passwort" placeholder="Kennwort" required> </br>
    <button class="status_button" type="submit">Anmelden!</button>
    </form>
    <p> Anmelden an: '.$ldapDomainName.' </p>
    <p> Sollten Sie Probleme beim Login haben, <br> wenden Sie sich an den IT-Beauftragten Ihrer Schule.<p>';
}
        
echo "</div> </div> </div>";

include("footer.php");

