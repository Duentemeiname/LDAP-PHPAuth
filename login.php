<?php
require_once("functions/functions.php");
require_once("functions/userlogin.php");
include("includes/header.php");
echo '
<div class="background">
<div class="seiteninhalt-login">';

$logout = $_GET["logout"];

if($logout)
{
    userLogout();
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") 
{

    $username = checkLDAPInjektion($_POST["nutzername"]);
    $passwort = checkLDAPInjektion($_POST["passwort"]);

    $ergebnis = userlogin($username, $passwort);
    $fehler = $ergebnis['error'];
    if(!empty($fehler))
    {
        echo "<div class='fehler center'> $fehler </div>" ;
    }

}
if(isset($_SESSION['userid'])) 
{
    header("Location: dashboard.php"); 
    exit();
}  
else 
{
    showLogin();
}



function showlogin()
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
include("includes/footer.php");

