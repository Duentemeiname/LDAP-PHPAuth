<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/functions/connect.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/functions/functions.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/config.php');

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
            throw new Exception("Benutzer nicht gefunden: $suchFilter");
        }
        else if ($entries['count'] > 1)
        {
            throw new Exception("Benutzername nicht eindeutig. Suchfilter liefert mehrere Ergebnisse:  $suchFilter");
        }

        return $entries;
    }
    catch (Exception $e) 
    {
        insertlog("LDAP", $e->getMessage());
        userLogout();
        return false;
    }

}
function doldapsearch($suchFilter, $attribute)
{
    global $ldapConn, $ldapBaseDn;
    try
    {

        $searchResult = ldap_search($ldapConn, $ldapBaseDn, $suchFilter, $attribute);
        if (!$searchResult)
        {
            throw new Exception("LDAP-Suche fehlgeschlagen: " . ldap_error($ldapConn));
        }

        $entries = ldap_get_entries($ldapConn, $searchResult);

        return $entries;
    }
    catch (Exception $e) 
    {
        insertlog("LDAP", $e->getMessage());
        return false;
    }
}

function doldapsearchnew($suchFilter, $attribute, $ldapnewBaseDn)
{
    global $ldapConn;

    try
    {

        $searchResult = ldap_search($ldapConn, $ldapnewBaseDn, $suchFilter, $attribute);
        if (!$searchResult)
        {
            throw new Exception("LDAP-Suche fehlgeschlagen: " . ldap_error($ldapConn));
        }

        $entries = ldap_get_entries($ldapConn, $searchResult);

        return $entries;
    }
    catch (Exception $e) 
    {
        insertlog("LDAP", $e->getMessage());
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
    if(empty($_SESSION["firstletters"]))
    {
    $entries = getuserarray($username);
    
    $vorname = $entries[0]['givenname'][0];
    $nachname = $entries[0]['sn'][0];

    $firstlettervorname = $vorname[0];
    $firstletternachname = $nachname[0];

    $beide = $firstlettervorname.$firstletternachname;
    $beideUP = strtoupper($beide);
    $_SESSION["firstletters"] = $beideUP;
    return $beideUP;
    }
    else
    return $_SESSION["firstletters"];
}

function checkifO365available($username)
{
    global $ldapSecurityGroupAAD;

    $filter = "(&(objectClass=user)(sAMAccountName=$username)(memberof=$ldapSecurityGroupAAD))";
    $attribute = array("samaccountname");
    $entries = doldapsearch($filter, $attribute);
    if ($entries['count'] != 1) 
    {
        return false;
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

function returnuserarray($username)
{
    $echoindex = [
        "whenchanged",
        "accountexpires",
        "userprincipalname",
    ];

    $entries = getuserarray($username);

    $result = [];
    foreach ($echoindex as $key) 
    {
        $result[$key] = $entries[0][$key][0];
    }
        $adDate = $result["whenchanged"];
        $year = substr($adDate, 0, 4);
        $month = substr($adDate, 4, 2);
        $day = substr($adDate, 6, 2);
        $hour = substr($adDate, 8, 2);
        $minute = substr($adDate, 10, 2);
        $second = substr($adDate, 12, 2);
        
        $timestamp = strtotime("$year-$month-$day $hour:$minute:$second");
        $lastchange = date("d-m-Y H:i:s", $timestamp);
        $result["whenchanged"] = $lastchange;

        $AccountExpiresFormatted = date("d-m-Y H:i:s", $result["accountexpires"]/10000000-11644473600);
        $result["accountexpires"] =  $AccountExpiresFormatted;

    return $result;
}

function getpapercutid($username)
{

    $attribute = array("employeeNumber");
    $filter = "(sAMAccountName=$username)";
    $entries = doldapsearch($filter, $attribute);
    $papercutid = $entries[0]["employeeNumber"][0];
    return $papercutid;
}

function isitbeauftragter()
{
    global $ldapSecurityGroupITBeauftragte, $ldapSecurityGroupDomänenAdmins;

    $userid = $_SESSION['userid'];
    $attribute = array("samaccountname");
    $filter = "(&(objectClass=user)(sAMAccountName=$userid)(| (memberof=$ldapSecurityGroupDomänenAdmins) (memberof=$ldapSecurityGroupITBeauftragte)))";

    $entries = doldapsearch($filter, $attribute);
    if ($entries['count'] != 1) 
    {
        return false;
    } 
    else
    {
        return true;
    }
    
}

function islehrer()
{
    global $ldapSecurityGroupLehrer;
    
    $userid = $_SESSION['userid'];
    $attribute = array("samaccountname");
    $filter = "(&(objectClass=user)(sAMAccountName=$userid)(memberof=$ldapSecurityGroupLehrer))";
    $entries = doldapsearch($filter, $attribute);
   
    if ($entries['count'] != 1) 
    {
        return false;
    } 
    else
    {
        return true;
    }
}

function searchuser($username, $vorname, $nachname)
{
    global $ldapSecurityGroupLehrer, $ldapSecurityGroupSuS;

    $filter = "(&(objectClass=user)";
    $filterinfos = array();

    if (!empty(checkLDAPInjektion($username)))
    {
        $filterinfos[] = "(sAMAccountName=$username*)";
    }

    if (!empty(checkLDAPInjektion($vorname)))
    {
        $filterinfos[] = "(givenName=$vorname*)";
    }

    if (!empty(checkLDAPInjektion($nachname)))
    {
        $filterinfos[] = "(sn=$nachname*)";
    }
    if (!empty($filterinfos)) 
    {
        $filter .= "(&" . implode("", $filterinfos) . ")";
    }
    else
    {
        return false;
    }
    
    $attribute = array("samaccountname", "cn", "givenname", "sn", "mail", "userprincipalname", "employeeNumber");
    $filter .= " (| (memberof=$ldapSecurityGroupLehrer) (memberof=$ldapSecurityGroupSuS) ))";
    $entries = doldapsearch($filter, $attribute);

    $sizeoutarray = $entries["count"];

    $returnarray = array($sizeoutarray + 1);
    $returnarray["count"] = (int)$sizeoutarray;

    for($i = 0; $i < $sizeoutarray; $i++)
    {
        $returnarray[$i]                        = array(7);
        $returnarray[$i]["count"]               = (int) 6;
        $returnarray[$i]["cn"]                  = $entries[$i]["cn"][0];
        $returnarray[$i]["samaccountname"]      = $entries[$i]["samaccountname"][0];
        $returnarray[$i]["sn"]                  = $entries[$i]["sn"][0];
        $returnarray[$i]["givenname"]           = $entries[$i]["givenname"][0];
        $returnarray[$i]["userprincipalname"]   = $entries[$i]["userprincipalname"][0];
        $returnarray[$i]["mail"]                = $entries[$i]["mail"][0];
        $returnarray[$i]["papercut"]            = $entries[$i]["employeeNumber"][0];
    }
    return($returnarray);
}

function getmembersecuritygroups($groupDn)
{
    global $ldapOUSecurityGroupClasses;
    $groupDn = "CN=".$groupDn.",".$ldapOUSecurityGroupClasses;

    $filter = "(&(objectClass=user)(memberOf=$groupDn))";
    $attribute = array("samaccountname", "cn", "givenname", "sn", "mail", "userprincipalname", "employeeNumber");
    $entries = doldapsearch($filter, $attribute);

    $sizeoutarray = $entries["count"];

    $returnarray = array($sizeoutarray + 1);
    $returnarray["count"] = (int)$sizeoutarray;

    for($i = 0; $i < $sizeoutarray; $i++)
    {
        $returnarray[$i]                        = array(7);
        $returnarray[$i]["count"]               = (int) 6;
        $returnarray[$i]["cn"]                  = $entries[$i]["cn"][0];
        $returnarray[$i]["samaccountname"]      = $entries[$i]["samaccountname"][0];
        $returnarray[$i]["sn"]                  = $entries[$i]["sn"][0];
        $returnarray[$i]["givenname"]           = $entries[$i]["givenname"][0];
        $returnarray[$i]["userprincipalname"]   = $entries[$i]["userprincipalname"][0];
        $returnarray[$i]["mail"]                = $entries[$i]["mail"][0];
        $returnarray[$i]["papercut"]            = $entries[$i]["employeeNumber"][0];
    }
    return($returnarray);
}
function getsecuritygroups()
{

    global $ldapSecurityGroupLehrer, $ldapSecurityGroupSuS, $ldapOUSecurityGroupClasses;

    preg_match("/CN=([^,]+)/", $ldapSecurityGroupLehrer, $matches);
    $cnleherer = $matches[1];
    preg_match("/CN=([^,]+)/", $ldapSecurityGroupSuS, $matches);
    $cnsus = $matches[1];


    $kurzel_select = '<option value="' .$cnleherer. '">Alle Lehrer</option><option value="' .$cnsus. '">Alle Schüler</option>';

    $filter = "(objectClass=group)";
    $attribute = array("cn", "name");
    $entries = doldapsearchnew($filter, $attribute, $ldapOUSecurityGroupClasses);

    for($i = 0; $i < $entries["count"]; $i++)
    {
        $kurzel_select .= '<option value="' .$entries[$i]["cn"][0].'">' .$entries[$i]["name"][0]. '</option>';
    }
    return $kurzel_select;
}

function searchupn($upn)
{
    global $ldapSecurityGroupSuS;

    $attribute = array("samaccountname", "cn", "givenname", "sn", "mail", "userprincipalname", "employeeNumber");
    $filter = "(&(objectClass=user)(mail=$upn*)(memberof=$ldapSecurityGroupSuS))";
    $entries = doldapsearch($filter, $attribute);

    $sizeoutarray = $entries["count"];

    $returnarray = array($sizeoutarray + 1);
    $returnarray["count"] = (int)$sizeoutarray;

    for($i = 0; $i < $sizeoutarray; $i++)
    {
        $returnarray[$i]                        = array(7);
        $returnarray[$i]["count"]               = (int) 6;
        $returnarray[$i]["cn"]                  = $entries[$i]["cn"][0];
        $returnarray[$i]["samaccountname"]      = $entries[$i]["samaccountname"][0];
        $returnarray[$i]["sn"]                  = $entries[$i]["sn"][0];
        $returnarray[$i]["givenname"]           = $entries[$i]["givenname"][0];
        $returnarray[$i]["userprincipalname"]   = $entries[$i]["userprincipalname"][0];
        $returnarray[$i]["mail"]                = $entries[$i]["mail"][0];
        $returnarray[$i]["papercut"]            = $entries[$i]["employeeNumber"][0];
    }
    return($returnarray);
}
