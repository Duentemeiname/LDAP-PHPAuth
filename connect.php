<?php
require_once("config.php");
require_once("functions.php");

$DownLevelLogonName = "$ldapDomainName\\"; 
$ldapNutzer = $DownLevelLogonName.$ldapNutzername;

$echoindex = array(
    "error" => "",
    "cn" => "",
    "mail" => "",
);
$debug = "";
$full_debug = "";
$testusername = "";
$testpasswort = "";

bool:$debug = $_GET["debug"];
$authdebug = $_GET["pw"];
$full_debug = $_GET["full"];
$testusername = checkLDAPInjektion($_GET["user"]);
$testpasswort = checkLDAPInjektion($_GET["passwort"]);

if($debug == "true" && $authdebug == $PWDebug)
{
    $debug = true;
}
else if ($authdebug != $PWDebug)
{
    echo "Authentifizierung für das Debug-Modul fehlgeschlagen.";
    $debug = false;
}
else
{
    $debug = false;
}

if($debug)
{
    echo "Angegebener Server: " .$ldapServer . ":" . $ldapPort . "<br>";
    echo "Angegebener Service-Nutzer: " .$ldapNutzer . "<br>";

    if(!empty($testusername))
    {
        echo "Angegebener Test-Nutzer: $testusername. <br> ";



        if(!empty($testpasswort))
        {
            echo "Angegebenes Passwort: $testpasswort für Test-Nutzer: $testusername. <br>";
            connectwithUser($testusername, $testpasswort);
        }
        else 
        {
           echo "Sie haben kein Passwort angegeben, dieser Test wird einen Authentifzierungsfehler beim Nutzer zurückmelden. <br>";
           connectwithUser($testusername, "a");
        }
    }
    else
    {
        echo "Da Sie keinen Testnutzer ausgewählt haben, wird dieser Test mit dem Service-Nutzer ausgeführt.<br>
        Um einen Testnutzer und das Passwort anzugeben, fügen Sie folgenden Code in die URL hinzu: <br>
        &user=USERNAME&passwort=PASSWORT. Ersetzen Sie USERNAME und PASSWORT entsprechend.<br>";
    }

    if($full_debug == true)
    {
        ldap_set_option(NULL, LDAP_OPT_DEBUG_LEVEL, 7);
        ini_set('display_errors', 1);
        error_reporting(E_ALL);
        echo "Sie haben debug auf full gesetzt, sehen Sie jetzt keinen Fehler, gibt es keine PHP-Information zu etweigen Problemen. <br>";
    }
    if(empty($testusername))
    connectwithUser($ldapNutzername, $ldapPasswort);
}

function connectwithUser($username, $userpasswort)
{
    global $ldapNutzer, $DownLevelLogonName, $ldapServer, $ldapDomainName, $ldapBaseDn, $ldapSecurityGroup, $ldapNutzername, $ldapPasswort, $ldapPort, $GlobalServerUrl, $debug, $echoindex;

    $op = fsockopen($ldapServer, $ldapPort, $errno, $errstr, $timeout=null);
    if (!$op)
    {
        if($debug)
        {
        $ipLDAPServer = gethostbyname($ldapServer);
        echo "LDAP-Server unter IP-Adresse: $ipLDAPServer nicht erreichbar! Steht hier eine URL, dann konnte die IP-Adresse des ldapServer nicht aufgelöst werden. <br>";
        }
        if(!$debug)
        {
            $echoindex["error"] = "Es ist ein schwerwiegender Fehler aufgetreten. Bitte informieren Sie Ihren IT-Beauftragten. FehlerCode: LDAP-Server nicht verfügbar.";
            return $echoindex;
        }
        exit;
    }
    else 
    {
        fclose($op);
        if($debug)
        {
        $ipLDAPServer = gethostbyname($ldapServer);
        echo "LDAP-Server unter IP-Adresse: $ipLDAPServer erreichbar! <br>";
        }
    }

    // Verbindung zum LDAP-Server herstellen
    $ldapConn = ldap_connect($ldapServer, $ldapPort);

    if ($ldapConn) 
    {
        // if($LDAPS)
        // {
        // if(!@ldap_start_tls($ldapConn))
        // {
        //     if($debug)
        //     {
        //         echo "Fehler bei der Herstellung einer TLS Verbindung. <br>";
        //         var_dump($ldapConn);
        //     }
        //     $echoindex["error"] = "Es ist ein schwerwiegender Fehler aufgetreten. Bitte informieren Sie Ihren IT-Beauftragten. FehlerCode: TLSnotAvailable.";
        //     return $echoindex;
        // }
        // }
        if($debug)
        {
        echo "Übergebene Parameter sind Plausibel. Es wurde noch KEINE Verbindung mit dem Server hergestellt, dies erfolgt im nächsten Schritt. <br>";
        var_dump($ldapConn);
        echo "<br>";
        }

        ldap_set_option($ldapConn , LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($ldapConn , LDAP_OPT_REFERRALS, 0);
        
        $ldapBindServiceUser = ldap_bind($ldapConn, $ldapNutzer, $ldapPasswort);

        if ($ldapBindServiceUser) 
        {
            if($debug)
            {
            echo "Verbindung mit dem Server erfolgreich, Authentifizierung durch den ServiceAccount: $ldapNutzername erfolgreich. Nun LDAP-Abfrage durchführen. <br>";
            }
            $filterNutzerinAD = "(&(objectClass=user)(sAMAccountName=$username))";
            $resultNutzerinAD = ldap_search($ldapConn, $ldapBaseDn, $filterNutzerinAD);
        
            if ($resultNutzerinAD) {
                $vorhandenNutzerinAD = ldap_get_entries($ldapConn, $resultNutzerinAD);
                if ($vorhandenNutzerinAD['count'] == 0) 
                {
                    $echoindex["error"] = "Dieser Account exisitiert nicht. Bitte nutzen Sie das Login des Pädagogischen Netzes oder wenden Sie sich an Ihren IT-Beauftragten.";
                    if($debug)
                    echo "Der Benutzer $username existiert nicht im AD: $ldapBaseDn";
                    return $echoindex;
                } 
                else if ($vorhandenNutzerinAD['count'] == 1) 
                {
                    if($debug)
                    echo "Der Benutzer $username existiert im AD: $ldapBaseDn <br>";
                }
                else 
                {
                    $echoindex["error"] = "Es ist ein schwerwiegender Fehler aufgetreten. Bitte informieren Sie Ihren IT-Beauftragten. FehlerCode: filterNutzerinAD";
                    if($debug)
                    echo "Die suche nach dem Nutzer: $filterNutzerinAD war nicht eindeutig und hat mehr als ein Ergebnis geliefert <br>";
                    return $echoindex;
                }
            } 
            else 
            {
                $echoindex["error"] = "Fehler bei der LDAP-Suche.";
                if($debug)
                echo "Fehler bei der LDAP-Suche.<br>";
                return $echoindex;
            }

            $filter = "(&(objectClass=user)(sAMAccountName=$username)(memberof=$ldapSecurityGroup))";
            $result = ldap_search($ldapConn, $ldapBaseDn, $filter);
        
            if ($result) {
                $entries = ldap_get_entries($ldapConn, $result);
                if ($entries['count'] == 1) 
                {
                    if($debug)
                    echo "Der Benutzer $username ist Mitglied der Sicherheitsgruppe: $ldapSecurityGroup. <br> Nach diesem Schritt erfolgt die Verfikikation des Nutzerpasswortes. <br>";
                } 
                else 
                {
                    $echoindex["error"] = "Sie haben keine Berechtigung sich anzumelden. Bitten Sie Ihren IT-Beauftragen um Zugriff auf Office365.";
                        if($debug)
                        {
                            echo "Der Benutzer $username ist kein Mitglied der Sicherheitsgruppe: $ldapSecurityGroup. <br>";
                        if ($entries['count'] > 1)
                        {
                            if($debug)
                            echo "Mehr als 1 Nutzerprofil wurden bei der Suche gefunden.";
                        }
                        }
                    return $echoindex;
                }
            } else 
            {
                $echoindex["error"] = "Fehler bei der LDAP-Suche.";
                if($debug)
                echo "Fehler bei der LDAP-Suche.";
                return $echoindex;
            }


            $ldapBindAuthUser = ldap_bind($ldapConn, $DownLevelLogonName.$username, $userpasswort);
            if($ldapBindAuthUser)
            {
                if($debug)
                {
                echo "Login User erfolgreich! Jetzt erfolgt die LDAP Abfrage. <br>";
                }
                $suchFilter = "(sAMAccountName=$username)";
                $searchResult = ldap_search($ldapConn, $ldapBaseDn, $suchFilter);
                $objinGroup = ldap_get_entries($ldapConn, $searchResult);

                if(!$searchResult)
                {
                    $echoindex["error"] = "Es ist ein schwerwiegender Fehler aufgetreten. Bitte informieren Sie Ihren IT-Beauftragten. FehlerCode: ldap_search => false";
                    if($debug)
                    echo "Es ist ein schwerwiegender Fehler aufgetreten. Bitte informieren Sie Ihren IT-Beauftragten. FehlerCode: ldap_search => false";
                    return $echoindex;
                }

                $entries = ldap_get_entries($ldapConn, $searchResult);

                if(!$entries)
                {
                    $echoindex["error"] = "Es ist ein schwerwiegender Fehler aufgetreten. Bitte informieren Sie Ihren IT-Beauftragten. FehlerCode: ldap_get_entries => false";
                    if($debug)
                    echo "Es ist ein schwerwiegender Fehler aufgetreten. Bitte informieren Sie Ihren IT-Beauftragten. FehlerCode: ldap_get_entries => false";
                    return $echoindex;
                }
                

                $email = $entries[0]['mail'][0];
                $CN = $entries[0]['cn'][0];

                if($debug)
                {
                    if(empty($email))
                    echo "Der Nutzer hat im AD noch keine E-Mail-Adresse, deshalb wir der Login im Front-End verweigert. Der Nutzer ist jedoch in der richtigen Sicherheitsgruppe, <br>
                          welche bestimmt, wer zu AAD synchronisiert wird. Die Synchronisierung müsste demnächst durchgeführt werden.<br>";
                    echo "Mail: $email <br> CN: $CN<br>";
                }
               if(empty($email))
                {
                    $echoindex["error"] = "Ihr Account wurde noch nicht synchronisieriert, bitte probieren Sie es später erneut.";
                    return $echoindex;
                }
                $echoindex["mail"] = $email;
                $echoindex["cn"] = $CN;
                ldap_unbind($ldapConn);
                return $echoindex;
                
            }
            else
            {
                $echoindex["error"] = "Sie haben ein falsches Passwort eingegeben.";
                if($debug)
                echo "Falsches Nutzerpasswort.";
                return $echoindex;
            }


        }
        else 
        {
            if($debug)
            {
                echo "LDAP-Bind fehlgeschlagen. Für weitere Informationenen geben Sie in der Adresszeile ".$GlobalServerUrl."connect.php?debug=true&full=true ein.<br>";
            }
            
        }
    } 
    else 
    {
        if($debug)
        {
            echo "Übergebene Parameter sind nicht Plausibel. Es wird KEINE Verbindung mit dem Server hergestellt. Weiter Informationen <a href='https://www.php.net/manual/de/function.ldap-connect.php'> erhalten Sie auf der PHP-Homepage.</a> <br>";
            var_dump($ldapConn);
            echo "<br>";
        }
    }
    ldap_unbind($ldapConn);
}
?>



