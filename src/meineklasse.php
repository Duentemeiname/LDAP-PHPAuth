<?php
ob_start();
require_once($_SERVER['DOCUMENT_ROOT'] . '/functions/functions.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/functions/LDAPfunctions.php');
$export = $_GET["export"];
$index = $_GET["i"];

userLoggedin();
if(!islehrer())
{
    header('Location: dashboard.php');
    exit;
}
$currentURL = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

$value = getclassesbytutor($_SESSION["userid"]);


if($export == true && isset($index) && $index <= $value["count"])
{
    exportclass($value[$index]);
    exit;
}

if ($value["count"] > 0) {
    for ($i = 0; $i < $value["count"]; $i++) 
    {
        $Ausgabe .= '<h2>' . $value[$i]["klasse"] . '</h2>
        
        <a href="' . $currentURL . '?export=true&i=' . $i . '" class="exportbutton">Exportieren</a>
        <table class="Tabelle"> 
        <tr> 
            <th>Name:</th> 
            <th>Benutzername:</th>
            <th>Nachname:</th>
            <th>Vorname:</th>
            <th>UPN-AD:</th>
            <th>UPN-AAD:</th>
            <th>PaperCut-ID:</th>
        </tr>';

        if ($value[$i]["member"] > 0) {
            for ($y = 0; $y < $value[$i]["member"]; $y++) 
            {
                $Ausgabe .= ' 
                <tr>
                    <td>' . $value[$i][$y]["cn"] . '</td>
                    <td>' . $value[$i][$y]["samaccountname"] . '</td>
                    <td>' . $value[$i][$y]["sn"] . '</td>
                    <td>' . $value[$i][$y]["givenname"] . '</td>
                    <td>' . $value[$i][$y]["userprincipalname"] . '</td>
                    <td>' . $value[$i][$y]["mail"] . '</td>
                    <td>' . $value[$i][$y]["papercut"] . '</td>
                </tr>';
            }
            $Ausgabe .= '</table><br>';
        } else {
            $Ausgabe .= '<tr><td colspan="7">Keine Nutzer, die Ihren Angaben entsprechen, gefunden.</td></tr>';
        }
    }
}
include($_SERVER['DOCUMENT_ROOT'] . '/includes/header.php');

echo'
<div class="background">
<div class="seiteninhalt">
<h1 class="center">Zugriff auf die Klassenliste für die Klassenleitung.</h1><hr>
<p class="center"> Ihrem Nutzerprofil konnten '.$value["count"].' Klassen zugewiesen werden.</p>
'.$Ausgabe.'
<p> Es werden Ihnen nicht alle Klassen angezeigt? <br> Bitte gehen Sie in KNE auf Klassen und geben Sie als Beschreibung bei Ihrer Klasse Ihren Nutzernamen ein. Alternativ Fragen Sie Ihren IT-Beauftragten.<br><p>Wird Ihnen in einem Feld N/A angezeigt, wurden diese Daten noch nicht hinterlegt.<br>Bitte fragen Sie Ihren IT-Beauftragten, ob Ihre Schule diesen Dienst bereits einsetzt.</p>


</div></div>
';
include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php');
ob_end_flush();