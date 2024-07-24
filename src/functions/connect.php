<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/config.php');

$DownLevelLogonName = "$ldapDomainName\\"; 
$ldapNutzer = $DownLevelLogonName.$ldapNutzername;


    $op = fsockopen($ldapServer, $ldapPort, $errno, $errstr, $timeout=null);
    if (!$op)
    {
        $ipLDAPServer = gethostbyname($ldapServer);
        die("LDAP-Server unter IP-Adresse: $ipLDAPServer nicht erreichbar! Steht hier eine URL, dann konnte die IP-Adresse des ldapServer nicht aufgelöst werden.");
    }
    else 
    {
        fclose($op);
    }

    $ldapConn = ldap_connect($ldapServer, $ldapPort);

    if ($ldapConn) 
    {
        if($LDAPS)
        {
            if(!ldap_start_tls($ldapConn))
            {
                die ("Fehler bei der Herstellung einer TLS Verbindung. ".ldap_error($ldapConn));
            }
        }

        ldap_set_option($ldapConn , LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($ldapConn , LDAP_OPT_REFERRALS, 0);
        
        $ldapBindServiceUser = ldap_bind($ldapConn, $ldapNutzer, $ldapPasswort);

        if (!$ldapBindServiceUser) 
        {
            die("LDAP-Bind fehlgeschlagen. Das kann an einem falschen Passwort liegen.");
        }
    } 




