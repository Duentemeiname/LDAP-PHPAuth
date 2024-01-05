<?php
ob_start();
require_once($_SERVER['DOCUMENT_ROOT'] . '/functions/functions.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/functions/LDAPfunctions.php');
include($_SERVER['DOCUMENT_ROOT'] . '/includes/header.php');
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
<p>Dieser Nutzername ist Ihre Authentifizierung bei Microsoft, damit Ihnen über die Schule kostenlos Office für Mobilgeräte und lokale Rechner zur Verfügung gestellt werden kann. Zukünftig wird dieser Nutzer auch bei weiteren Diensten eingesetzt. <br> Ihr Passwort bei Microsoft entspricht dem, welches Sie sich auch im Schulnetzwerk gegeben haben. Dieses Passwort können Sie nur im Schulnetzwerk ändern. Sollten Sie es vergessen, wenden Sie sich bitte an Ihren IT-Beauftragten.</p>
<button class="button_redirect" onclick="window.open(\'' . $AADLoginLink . returnEmail($_SESSION["userid"]) . '\', \'_blank\')">Hier geht es direkt zu Office365.</button>
<button class="button_redirect" onclick="window.location.href=\'' . $AADLoginLink . returnEmail($_SESSION["userid"]) . '\'">Wie funktioniert der Login bei Microsoft?</button>
</div>';
}
else
{
    echo "<div class='centerflex'><div class='fehler'> Entweder Sie haben keine Berechtigung Office365 zu verwenden, oder Ihr Profil wurde noch nicht synchronisiert. Abhängig von Ihrer Schule steht diese Funktion nur SuS zu Verfügung. Wenden Sie sich bei Fragen bitte an den IT-Beauftragten.<br> Fehlercode: email-is-empty</div></div><br>" ;
}
echo "</div></div>";
include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php');
ob_end_flush();