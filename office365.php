<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '\functions\functions.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '\functions\LDAPfunctions.php');
include($_SERVER['DOCUMENT_ROOT'] . '\includes\header.php');
userLoggedin();
echo '
<div class="background">
<div class="seiteninhalt">';

if(checkifO365available($_SESSION["userid"]))
{
    echo '<div class="login">
<h1 class="center">Office365 steht Ihnen zu Verfügung.</h1> <hr>
<p>Der Nutzername für ' .returnCN($_SESSION["userid"]) . ', um die Office365-Produkte nutzen zu können, lautet: </p>
<p><b>' . returnEmail($_SESSION["userid"]) . '</b></p>
<p>Diese E-Mail-Adresse ist Ihre Authentifizierung bei Microsoft, damit Ihnen über die Schule kostenlos und datenschutzkonform Office365 zur Verfügung gestellt werden kann. Ihr Passwort bei Microsoft entspricht dem, welches Sie sich auch im Schulnetzwerk gegeben haben. Dieses Passwort können Sie nur im Schulnetzwerk ändern. Sollten Sie es vergessen, wenden Sie sich bitte an Ihren IT-Beauftragten.</p>
<button class="button_redirect" onclick="window.location.href=\'' . $AADLoginLink . returnEmail($_SESSION["userid"]) . '\'">Hier geht es direkt zu Office365.</button> <br>
<button class="button_redirect" onclick="window.location.href=\'#\'">Datenschutzerklärung.</button>
<button class="button_redirect" onclick="window.location.href=\'#\'">Weitere Infos zum Einsatz von Office365.</button>
</div>';
}
else
{
    echo "<div class='centerflex'><div class='fehler'> Entweder Sie haben keine Berechtigung Office365 zu verwenden, oder Ihr Profil wurde noch nicht synchronisiert. Bitte versuchen Sie es in 2 Stunden erneut oder wenden Sie sich an Ihren IT-Beauftragten. </div></div><br>" ;
}
echo "</div></div>";
include($_SERVER['DOCUMENT_ROOT'] . '\includes\footer.php');