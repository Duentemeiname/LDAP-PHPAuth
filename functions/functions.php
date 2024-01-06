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
    if (!preg_match('/^[a-zA-Z0-9.,!]+$/', $string)) 
    {
        die("Mögliche SQL-Injektion abgefangen! Bitte laden Sie diese Seite komplett neu.");
    }
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

function checkie()
{
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    if (preg_match('/MSIE/i', $user_agent) || preg_match('/Trident/i', $user_agent)) 
    {
        echo("Der Internet-Explorer wird von dieser Anwendung nicht mehr unterstützt. Bitte nutzen Sie einen alternativen Browser.");
        exit;
    }
}

function getuserip() 
{
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])) 
    {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } 
    elseif (isset($_SERVER['HTTP_CLIENT_IP']) && !empty($_SERVER['HTTP_CLIENT_IP'])) 
    {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } 
    else 
    {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return trim(strtok($ip, ','));
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

function insertloguserdata($typ, $action, $username)
{
    $Anfrage = "INSERT INTO errorlog(typ, ip, vorfall, user, userdevice) 
      VALUES ('".$typ."', '" . getuserip() . "', '" . $action . "', '" . $username . "', '" . getuserdevice() . "')";
    SQLtoDB($Anfrage);
}

function checkuserlocked($username)
{
    $username = checkSQLInjektion($username);
    $Anfrage = "SELECT * FROM lockeduser WHERE userid = '$username'";
    $entries = SQLtoDB($Anfrage);
    $rowCount = $entries->num_rows;
    if($rowCount > 0)
    {       
        $array = $entries->fetch_assoc();
        $lastTryTimestamp = strtotime($array["lasttry"]);
        $currentTime = time();

        $timeDifference = $currentTime - $lastTryTimestamp;

        if ($timeDifference > 300) 
        {
            $Anfrage = "DELETE FROM lockeduser WHERE userid = '$username'";
            SQLtoDB($Anfrage);
            return true;
        } 
        else 
        {
            if($array["tries"] < 5)
            {
                return true;
            }
            else
            {
                return false;
            }
        }
    }
    else
    {
        return true;
    }
}
function deletefalsepassword($username)
{
    $username = checkSQLInjektion($username);
    $Anfrage = "SELECT * FROM lockeduser WHERE userid = '$username'";
    $entries = SQLtoDB($Anfrage);

    $rowCount = $entries->num_rows;

    if($rowCount > 0)
    {     
        $Anfrage = "DELETE FROM lockeduser WHERE userid = '$username'";
        SQLtoDB($Anfrage);
        return true;
    }
    else
    {
        return true;
    }
}
function addfalsepassword($username)
{
    $username = checkSQLInjektion($username);
    $Anfrage = "SELECT * FROM lockeduser WHERE userid = '$username'";
    $entries = SQLtoDB($Anfrage);

    $rowCount = $entries->num_rows;

    if($rowCount > 0)
    {
        $Anfrage = "UPDATE lockeduser SET tries = tries + 1, lasttry = '" . date("Y-m-d H:i:s") . "' WHERE userid = '" . $username . "'";
        SQLtoDB($Anfrage);
    }
    else
    {
        $Anfrage = "INSERT INTO lockeduser(userid, tries, lasttry) 
        VALUES ('".$username."', '1', '" . date("Y-m-d H:i:s") . "')";
        SQLtoDB($Anfrage);
    }
    
}

function getlockedusers()
{
    $Anfrage = "DELETE FROM lockeduser WHERE TIMESTAMPDIFF(MINUTE, lasttry, NOW()) > 5";
    SQLtoDB($Anfrage);

    $Anfrage = "SELECT * FROM lockeduser WHERE tries = 5";
    $entries = SQLtoDB($Anfrage);
    $rowCount = $entries->num_rows;

    $returnarray = array(
        "count" => $rowCount
    );
    if ($rowCount > 0) 
    {
    
        while ($row = $entries->fetch_assoc()) 
        {
            $returnarray[] = array(
                "id" => $row["id"],
                "userid" => $row["userid"],
                "lasttry" => $row["lasttry"] 
            );
        }
    }
    return $returnarray;
}
function unlockuser($id)
{
    $id = checkSQLInjektion($id);
    $Anfrage = "DELETE FROM lockeduser WHERE id = '" . $id . "'";
    SQLtoDB($Anfrage);
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}

function getlog($table)
{

    (int)$table = checkSQLInjektion($table);

    $width = 30 * $table;
    $Anfrage = "SELECT * FROM errorlog ORDER BY id DESC LIMIT $width";
    $entries = SQLtoDB($Anfrage);

    $Ausgabe = '        
    <table class="Tabelle"> 
    <tr> 
        <th>Vorfall</th> 
        <th>User</th>
        <th>IP</th>
        <th>Device</th>
        <th>Datum</th>
    </tr>';

    if($entries->num_rows > 0)
    {
        while ($row = $entries->fetch_assoc()) 
        {
            $Ausgabe .= '
            <tr>
                <td>'.$row["vorfall"].'</td>
                <td>'.$row["user"].'</td>
                <td>'.$row["ip"].'</td>
                <td>'.$row["userdevice"].'</td>
                <td>'.$row["created_at"].'</td>
            <tr>';

        }
    }
    else
    {
        $Ausgabe .= '<td colspan="5">Keine Einträge vorhanden.</td>';
    }
    $Ausgabe .= '</table>';
    return $Ausgabe;
}
function externallog($username, $status)
{
    // Wurde aus Datenschutzgründen nicht eingesetzt. Wird jetzt durch HTTP Statuscodes angezeigt.
    // $logDatei = 'log.txt';
    // if (!file_exists($logDatei)) 
    // {
    //     $neueLogDatei = fopen($logDatei, 'w');

    //     if ($neueLogDatei)  
    //     {
    //         fclose($neueLogDatei);
    //         insertlog("Create logFile", "Log Datei wurde erfolgreich erstellt.");
    //     } else 
    //     {
    //         insertlog("Create logFile", "Log Datei konnte nicht erstellt werden.");
    //     }
    // }

    // if (is_writable($logDatei)) 
    // {
    //     $logText = "Date:" . date('Y-m-d H:i:s') . ";IP:".getuserip().";User:".$username.";Loginstatus:".$status. PHP_EOL;
    //     file_put_contents($logDatei, $logText, FILE_APPEND);
    // } 
    // else 
    // {
    //     insertlog("Create logFile", "Log Datei konnte nicht beschrieben werden.");
    // }
}


function export($result)
{
    if(!$result)
    {
        echo "<div class='fehler centerflex'>Es ist ein unbekannter Fehler bei der Suche aufgetreten.</div>" ;
        exit;
    }
    else
    {
        $f = fopen('php://memory', 'w'); 
        $delimiter = ","; 

        $fields = array('Name', 'Benutzername', 'Nachname', 'Vorname', 'UPN-AD', 'UPN-AAD', 'PaperCut-ID'); 
        fputcsv($f, $fields, $delimiter); 

        $sizeoutarray = $result["count"];

        if($sizeoutarray < 1)
        {
            echo "Fehler beim Export. Bitte versuchen Sie es erneut!";
            exit;
        }
        
        for($i = 0; $i < $sizeoutarray; $i++)
        { 
            $lineData = array(
                $result[$i]["cn"], 
                $result[$i]["samaccountname"], 
                $result[$i]["sn"], 
                $result[$i]["givenname"], 
                $result[$i]["userprincipalname"], 
                $result[$i]["mail"], 
                $result[$i]["papercut"]); 
            fputcsv($f, $lineData, $delimiter); 
        } 
        
        fseek($f, 0); 
        
        header('Content-Type: text/csv'); 
        header('Content-Disposition: attachment; filename="export.csv"');
        
        fpassthru($f); 
    }
}

function exportclass($result)
{
    if(!$result)
    {
        echo "<div class='fehler centerflex'>Es ist ein unbekannter Fehler bei der Suche aufgetreten.</div>" ;
        exit;
    }
    else
    {
        $f = fopen('php://memory', 'w'); 
        $delimiter = ","; 

        $fields = array(" Exportierte Klasse: " . $result["klasse"]); 
        fputcsv($f, $fields, $delimiter); 
        $fields = array('Name', 'Benutzername', 'Nachname', 'Vorname', 'UPN-AD', 'UPN-AAD', 'PaperCut-ID'); 
        fputcsv($f, $fields, $delimiter); 

        $sizeoutarray = $result["member"];

        if($sizeoutarray < 1)
        {
            echo "Fehler beim Export. Bitte versuchen Sie es erneut!";
            exit;
        }
        
        for($i = 0; $i < $sizeoutarray; $i++)
        { 
            $lineData = array(
                $result[$i]["cn"], 
                $result[$i]["samaccountname"], 
                $result[$i]["sn"], 
                $result[$i]["givenname"], 
                $result[$i]["userprincipalname"], 
                $result[$i]["mail"], 
                $result[$i]["papercut"]); 
            fputcsv($f, $lineData, $delimiter); 
        } 
        
        fseek($f, 0); 
        
        header('Content-Type: text/csv'); 
        header('Content-Disposition: attachment; filename="export.csv"');
        
        fpassthru($f); 
    }
}