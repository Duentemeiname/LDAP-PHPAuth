<?php
ob_start();
require_once($_SERVER['DOCUMENT_ROOT'] . '/functions/functions.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/functions/userlogin.php');
checkie();
include($_SERVER['DOCUMENT_ROOT'] . '/includes/header.php');

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

    if(empty($username) || empty($passwort))
    {
        echo "<div class='centerflex'><div class='fehler'> Ungültiger Benutzername oder Passwort. <br> Bitte versuchen Sie es erneut.</div></div>";
    }
    else
    {
        $ergebnis = userlogin($username, $passwort);
        $fehler = $ergebnis['error'];
        if(!empty($fehler))
        {
            echo "<div class='centerflex'><div class='fehler'> $fehler </div></div>" ;
        }
    }

}
if(isset($_SESSION['userid'])) 
{
    header('Location: dashboard.php');
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
    <img class="img" src="img\Windows-Default-Avatar-448x400.png">
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
include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php');
ob_end_flush();
