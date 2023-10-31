<?php
session_start();
require_once($_SERVER['DOCUMENT_ROOT'] . '/functions/dbconnect.php');


function checkLDAPInjektion($string)
{
    return ldap_escape($string, "", LDAP_ESCAPE_FILTER);
}

function checkSQLInjektion($string)
{
    global $db_link;
    return(mysqli_real_escape_string($db_link, $string));
}
function random_string() 
{
    if(function_exists('random_bytes')) 
    {
       $bytes = random_bytes(16);
       $string = bin2hex($bytes); 
    } 
    else if(function_exists('openssl_random_pseudo_bytes')) 
    {
       $bytes = openssl_random_pseudo_bytes(16);
       $string = bin2hex($bytes); 
    }
    else
    {
        return false;
    }
    return $string;
}

function setLoginCookie($username)
{
    $identifier = random_string();
    $securitytoken = random_string();
    $SHAsecuritytoken = password_hash($securitytoken, PASSWORD_BCRYPT);
    
    $DBUser = checkSQLInjektion($username);
    $Anfrage = "INSERT INTO securitytokens(user_id, identifier, securitytoken) VALUES ('$DBUser', '$identifier', '$SHAsecuritytoken')";
    SQLtoDB($Anfrage);
    setcookie("identifier", $identifier, time() + (3600 * 24 * 7), '/', '', false, true); 
    setcookie("securitytoken", $securitytoken, time() + (3600 * 24 * 7), '/', '', false, true); 
}

function userLoggedin()
{
    if(!isset($_SESSION['userid']) && isset($_COOKIE['identifier']) && isset($_COOKIE['securitytoken'])) 
    {
        $identifier = checkSQLInjektion($_COOKIE['identifier']);
        $securitytoken = checkSQLInjektion($_COOKIE['securitytoken']);
        
        $Anfrage = "SELECT * FROM securitytokens WHERE identifier = '$identifier'";
        $securitytoken_DB = SQLtoDB($Anfrage);
        if ($securitytoken_DB)
        {
            $array_securitytoken_DB = $securitytoken_DB->fetch_assoc();
        }

        if(!password_verify($securitytoken,  $array_securitytoken_DB['securitytoken']))
        {  
            
            userLogout();
            header("Location: login.php"); 
            exit();
        } 
        else
        { 
           $neuer_securitytoken = random_string();            
           $Anfrage = "UPDATE securitytokens SET securitytoken = ' $neuer_securitytoken' WHERE identifier = '$identifier'";
           SQLtoDB($Anfrage);        
           setcookie("identifier", $identifier, time() + (3600 * 24 * 7), '/', '', false, true);
           setcookie("securitytoken",  $neuer_securitytoken, time() + (3600 * 24 * 7), '/', '', false, true); 
           
           //Logge den Benutzer ein
           $_SESSION['userid'] =  $array_securitytoken_DB['user_id'];
           echo "LOGGIN";
           header("Location: dashboard.php"); 
           return true;
        }
     }
     else
     {
     if(!isset($_SESSION['userid']) && basename($_SERVER['PHP_SELF']) !== 'login.php') 
     {
        header("Location: login.php"); 
        exit();
     }  
     
     if(isset($_SESSION['userid']))
     {
        return true;
    }
}
}

function userLogout()
{
    setcookie("identifier", "", time() - 3600, "/");
    setcookie("securitytoken", "", time() - 3600, "/");
    session_destroy();
    header("Location: login.php");
    exit();
}

function getuserdevice()
{
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    $browser = "Unbekannter Browser";
    $os = "Unbekanntes Betriebssystem";
    
    // Browser identifizieren
    if (preg_match('/MSIE/i', $user_agent) && !preg_match('/Opera/i', $user_agent)) {
        $browser = 'Internet Explorer';
    } elseif (preg_match('/Firefox/i', $user_agent)) {
        $browser = 'Mozilla Firefox';
    } elseif (preg_match('/Chrome/i', $user_agent)) {
        $browser = 'Google Chrome';
    } elseif (preg_match('/Safari/i', $user_agent)) {
        $browser = 'Apple Safari';
    } elseif (preg_match('/Opera/i', $user_agent)) {
        $browser = 'Opera';
    } elseif (preg_match('/Edge/i', $user_agent)) {
        $browser = 'Microsoft Edge';
    } elseif (preg_match('/Netscape/i', $user_agent)) {
        $browser = 'Netscape';
    }
    
    // Betriebssystem identifizieren
    if (preg_match('/Windows/i', $user_agent)) {
        $os = 'Windows';
    } elseif (preg_match('/Mac/i', $user_agent)) {
        $os = 'Mac OS X';
    } elseif (preg_match('/Linux/i', $user_agent)) {
        $os = 'Linux';
    } elseif (preg_match('/Unix/i', $user_agent)) {
        $os = 'Unix';
    } elseif (preg_match('/Android/i', $user_agent)) {
        $os = 'Android';
    } elseif (preg_match('/(iPhone|iPad)/i', $user_agent)) {
        $os = 'iOS';
    }
    
    $infos_user = "Betriebssystem: ".$os." Browser: ".$browser;
    return $infos_user;
}
function getuserip()
{
    return $_SERVER['REMOTE_ADDR'];
}

function logloggin($username, $log)
{
         $username = checkSQLInjektion($username);
         $Anfrage = "INSERT INTO errorlog(typ, ip, vorfall, user, userdevice) 
           VALUES ('userlogin', '" . getuserip() . "', '" . $log . "', '" . $username . "', '" . getuserdevice() . "')";
         SQLtoDB($Anfrage);
}

function insertlog($typ, $action)
{
    $Anfrage = "INSERT INTO errorlog(typ, ip, vorfall, user, userdevice) 
      VALUES ('".$typ."', 'NONE', '" . $action . "', 'NONE', 'NONE')";
    SQLtoDB($Anfrage);
}
