<?php
require_once("connect.php");
require_once("functions.php");

function userlogin($username, $userpasswort)
{
    $echoindex = array("error" => "",);
    global $ldapConn, $DownLevelLogonName; 

    $ldapConnAuth = $ldapConn;

    $ldapBindAuthUser = ldap_bind($ldapConnAuth, $DownLevelLogonName.$username, $userpasswort);
    if($ldapBindAuthUser)
        {
            $_SESSION['userid'] = $username;
            setLoginCookie($username); //functions/functions.php
            header("Location: dashboard.php"); 
            echo "Login User erfolgreich! Jetzt erfolgt die LDAP Abfrage. <br>";
        }
    else
        {
            $echoindex["error"] = "Ungültiger Benutzername oder Passwort. <br> Bitte versuchen Sie es erneut.";
        }
    ldap_close($ldapConnAuth);
    return $echoindex;
}
