<?php
require_once('connect.php');
require_once('functions.php');
require_once('LDAPfunctions.php');

function userlogin($username, $userpasswort)
{

    $echoindex = array("error" => "",);
    global $ldapConn, $DownLevelLogonName; 

    if(!loginallowed($username))
    {
        $echoindex["error"] = "Ungültiger Benutzername oder Passwort. <br> Bitte versuchen Sie es erneut.";
        header("HTTP/1.1 401 Unauthorized");
        return $echoindex;
        exit;
    }

    if(!checkuserlocked($username))
    {
        $echoindex["error"] = "Ihr Account ist gesperrt! <br> Bitte versuchen Sie es in 10min erneut. ";
        return $echoindex;
        exit;
    }

    $ldapConnAuth = $ldapConn;

    $ldapBindAuthUser = ldap_bind($ldapConnAuth, $DownLevelLogonName.$username, $userpasswort);
    if($ldapBindAuthUser)
        {
            deletefalsepassword($username);
            externallog($username, "true");
            logloggin($username, "Login zugelassen");
            $_SESSION['userid'] = $username;
            setLoginCookie($username); //functions/functions.php
            header("Location: dashboard.php"); 
        }
    else
        {
            addfalsepassword($username);
            externallog($username, "false");
            header("HTTP/1.1 401 Unauthorized");
            logloggin($username, "Login abgelehnt");
            $echoindex["error"] = "Ungültiger Benutzername oder Passwort. <br> Bitte versuchen Sie es erneut.";
		    return $echoindex;
        }

    ldap_close($ldapConnAuth);
}
