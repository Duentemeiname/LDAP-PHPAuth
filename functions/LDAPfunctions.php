<?php
require_once("connect.php");
require_once("config.php");
require_once("functions.php");

function getuserarray($username)
{
    global $ldapConn, $ldapBaseDn;

    $suchFilter = "(sAMAccountName=$username)";
    try
    {
        $searchResult = ldap_search($ldapConn, $ldapBaseDn, $suchFilter);
        if(!$searchResult)
        {
            throw new Exception("LDAP-Suche fehlgeschlagen: " . ldap_error($ldapConn));
        }

        $entries = ldap_get_entries($ldapConn, $searchResult);
        if ($entries['count'] == 0) 
        {
            throw new Exception("Benutzer nicht gefunden.");
        }
        else if ($entries['count'] > 1)
        {
            throw new Exception("Benutzername nicht eindeutig. Suchfilter liefert mehrere Ergebnisse:  $suchFilter");
        }

        return $entries;
    }
    catch (Exception $e) 
    {
        userLogout(); //User wird ausgeloggt um unerwartetes Verhalten zu vermeiden.
        //Zukünftig Eintrag in DB
        echo "Fehler: " . $e->getMessage();
        return false;
    }

}
function returnCN($username)
{
    $entries = getuserarray($username);
    $CN = $entries[0]['cn'][0];
    return $CN;
}
function returnfirstLetters($username)
{
    $entries = getuserarray($username);
    
    $vorname = $entries[0]['givenname'][0];
    $nachname = $entries[0]['sn'][0];

    $firstlettervorname = $vorname[0];
    $firstletternachname = $nachname[0];

    $beide = $firstlettervorname.$firstletternachname;
    $beideUP = strtoupper($beide);
    return $beideUP;
}

function checkifO365available($username)
{
    global $ldapConn, $ldapBaseDn, $ldapSecurityGroupAAD;

    $filter = "(&(objectClass=user)(sAMAccountName=$username)(memberof=$ldapSecurityGroupAAD))";
    $result = ldap_search($ldapConn, $ldapBaseDn, $filter);

    if ($result) {
        $entries = ldap_get_entries($ldapConn, $result);
        if ($entries['count'] != 1) 
        {
            return false;
        } 
    }

    $entries = getuserarray($username);
    $email = $entries[0]['mail'][0];
    if(empty($email))
    {
        return false;
    }    
    return true;
}
function returnEmail($username)
{
    $entries = getuserarray($username);
    $email = $entries[0]['mail'][0];
    return $email;
}