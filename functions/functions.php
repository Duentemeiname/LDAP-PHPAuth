<?php
session_start();
require_once("dbconnect.php");

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
    setcookie("identifier", $identifier, time() + (3600 * 24 * 7), '/', '', true, true); 
    setcookie("securitytoken", $securitytoken, time() + (3600 * 24 * 7), '/', '', true, true); 
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
           header("Location: dashboard.php"); 
           return true;
           
        }

     }
      
     if(!isset($_SESSION['userid'])) 
     {
        userLogout();
        header("Location: login.php"); 
        exit();
     }  
     if(isset($_SESSION['userid']) && basename($_SERVER['PHP_SELF']) !== 'dashboard.php')
     {
        header("Location: dashboard.php"); 
        exit();
     }
}

function userLogout()
{
    global $GlobalServerUrl;
    session_destroy();
    setcookie("identifier","",time()-(3600*24*7)); 
    setcookie("securitytoken","",time()-(3600*24*7)); 
    header("Location: $GlobalServerUrl index.php");
}