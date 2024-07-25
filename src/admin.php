<?php
ob_start();
require_once('functions/functions.php');
require_once('functions/LDAPfunctions.php');
userLoggedin();
if(!isitbeauftragter())
{
    header('Location: dashboard.php');
    exit;
}

$unlockuserid = $_GET["unlock"];
$loadlog = $_GET ["i"];

if((int)$loadlog == 0)
    $loadlog = 1;

if((int)$unlockuserid == 0 && !empty($unlockuserid))
{
    insertloguserdata("software", "User send wrong data to admin.php <br> (unlock user) Expected int but got none ", $_SESSION["userid"]);
    echo "Fehler bei der Datenübertragung! Fehlermeldung: (unlock user) Expected int but got none ";    
    exit;
}

$currentURL = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

if(!empty($unlockuserid))
    unlockuser($unlockuserid);

$result = getlockedusers();

$Ausgabe = '
<table class="Tabelle"> 
            <tr> 
                <th>Nutzername:</th> 
                <th>Sperrzeitpunkt:</th>
                <th>Entsperren:</th>
            </tr>
';



if($result["count"] > 0)
{
    for($i = 0; $i < $result["count"]; $i++)
    {
        $Ausgabe .= ' 
        <tr>
        <td>'.$result[$i]["userid"].'</td>
        <td>'.$result[$i]["lasttry"].'</td>
        <td><a href="' . $currentURL . '?unlock=' . $result[$i]["id"] . '" style="color: white;">Sperre aufheben</a></td>
        </tr>';
    }
}
else
{
    $Ausgabe .= '<td colspan="3">Aktuell keine gesperrrten Nutzer!</td>';
}






include($_SERVER['DOCUMENT_ROOT'] . '/includes/header.php');
echo '
<div class="background">
<div class="seiteninhalt">
<h1 class="center">Administration für IT-Beauftragte</h1><hr>

<h2>Aktuell gesperrte Nutzer:</h2>
<p class="center">Sie sehen hier alle Nutzer, die aktuell Aufgrund von zu oft falsch eingegebenen Passwörten gesperrt sind. Durch klicken auf "Sperre aufheben" wird der Nutzer freigeschaltet. <br>
Nachdem der Nutzer sein Passwort erneut 5 mal falsch eingegeben hat, wird der Nutzer wieder für 5 Minuten gesperrt.</p>

'.$Ausgabe.'

</table>
<h2>Ereignisprotokoll:</h2>
<p class="center">Sie können die Administrativen Ereignisse der letzten 30 Tage abrufen.</p>
'.getlog($loadlog).'
<a href="/admin.php?i=' . ($loadlog + 1) . '" class="exportbutton">Mehr Anzeigen</a> <a> Aktuelle Seite: '.$loadlog.' </a>
</div></div>


';
include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php');
ob_end_flush();
