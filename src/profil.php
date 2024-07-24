<?php
ob_start();
require_once($_SERVER['DOCUMENT_ROOT'] . '/functions/functions.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/functions/LDAPfunctions.php');
include($_SERVER['DOCUMENT_ROOT'] . '/includes/header.php');
userLoggedin();

$entries = returnuserarray($_SESSION["userid"]);

$Rolle = "Schüler";
if(isLehrer())
{
    $Rolle = "Lehrer";
}
if(isitbeauftragter())
{
    $Rolle = "IT-Beauftragter";
}

echo '
<div class="background">
<div class="seiteninhalt">';
echo '<div class="login">
<h1 class="center">Nutzerprofil von '.returnCN($_SESSION["userid"]).' ('.$ldapDomainName.')</h1> <hr>
<h3> Bitte beachte, dass Änderungen nur an den lokalen Rechnern im Schulnetzwerk möglich sind.</h3>
<p>Profil aus Active Directory '.$ldapServer.' geladen.</p><br>
<p>
Ihre aktuelle Rolle: '.$Rolle.'<br>
UPN: '.$entries["userprincipalname"].'<br><br>

Weitere Informationen zu der Verwendung Ihres Profils: <br><br>
Account zuletzt bearbeitet: '.$entries["whenchanged"].'<br>
Account-Ablaufdatum: '.$entries["accountexpires"].'
</p>

</div></div></div>';

include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php');
ob_end_flush();