<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/functions/connect.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/functions/functions.php');

function userlogin($username, $userpasswort)
{
    $echoindex = array("error" => "",);
    global $ldapConn, $DownLevelLogonName; 

    $ldapConnAuth = $ldapConn;

    $ldapBindAuthUser = ldap_bind($ldapConnAuth, $DownLevelLogonName.$username, $userpasswort);
    if($ldapBindAuthUser)
        {
            logloggin($username, "Login zugelassen");
            $_SESSION['userid'] = $username;
            setLoginCookie($username); //functions/functions.php
            header("Location: dashboard.php"); 
        }
    else
        {
            logloggin($username, "Login abgelehnt");
            $echoindex["error"] = "Ungültiger Benutzername oder Passwort. <br> Bitte versuchen Sie es erneut.";
		return $echoindex;
        }
    ldap_close($ldapConnAuth);
}
