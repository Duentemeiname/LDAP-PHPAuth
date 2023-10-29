<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '\LDAP\functions\functions.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '\LDAP\functions\LDAPfunctions.php');
include($_SERVER['DOCUMENT_ROOT'] . '\LDAP\includes\header.php');
userLoggedin();

$entries = returnuserarray($_SESSION["userid"]);

echo '
<div class="background">
<div class="seiteninhalt">';
echo '<div class="login">
<h1 class="center">Nutzerprofil von '.returnCN($_SESSION["userid"]).' ('.$ldapDomainName.')</h1> <hr>
<h3> Bitte beachte, dass Änderungen nur an den lokalen Rechnern im Schulnetzwerk möglich sind.</h3>
<p>Profil aus Active Directory '.$ldapServer.' geladen.</p><br>
<p>
UPN: '.$entries["userprincipalname"].'<br><br>
Weitere Informationen zu der Verwendung Ihres Profils: <br><br>
Account zuletzt bearbeitet: '.$entries["whenchanged"].'<br>
Account-Ablaufdatum: '.$entries["accountexpires"].'
</p>

</div></div></div>';

include($_SERVER['DOCUMENT_ROOT'] . '\LDAP\includes\footer.php');