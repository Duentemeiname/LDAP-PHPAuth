<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '\LDAP\functions\functions.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '\LDAP\functions\LDAPfunctions.php');
include($_SERVER['DOCUMENT_ROOT'] . '\LDAP\includes\header.php');
userLoggedin();
if(!islehrer())
{
    header('Location dashboard.php');
    exit;
}
echo'
<div class="background">
<div class="seiteninhalt">
<div class="center">
<img src="img\2807188.jpg" height="800" width="auto">
<h2>Kommt voraussichtlich in Update v1.4.x</h2>
</div></div></div>
';
include($_SERVER['DOCUMENT_ROOT'] . '\LDAP\includes\footer.php');