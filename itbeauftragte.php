<?php
ob_start();
require_once($_SERVER['DOCUMENT_ROOT'] . '/functions/functions.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/functions/LDAPfunctions.php');
userLoggedin();
if(!isitbeauftragter())
{
    header('Location: dashboard.php');
    exit;
}
$username = checkLDAPInjektion($_GET["nutzername"]);
$vorname = checkLDAPInjektion($_GET["vorname"]);
$nachname = checkLDAPInjektion($_GET["nachname"]);
$group = checkLDAPInjektion($_GET["group"]);
$export = $_GET["export"];

$currentURL = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

$kurzel_select = getsecuritygroups();

if (!empty($username) || !empty($vorname) || !empty($nachname))
{

    $result = searchuser($username, $vorname, $nachname);
    if(!$result)
    {
        echo "<div class='fehler centerflex'>Es ist ein unbekannter Fehler bei der Suche aufgetreten.</div>" ;
        exit;
    }
    else
    {
        if($export == true)
        {
            export($result);
            exit;
        }

        $sizearray = $result["count"];
        $Ausgabe .= ' 
        <h2>Suchergebnis für: '.$username.' '.$vorname.' '.$nachname.'  </h2>
        <a href="' . $currentURL . '&export=true" class="exportbutton">Exportieren</a>
        <div class="centerflex">
        <table class="Tabelle"> 
            <tr> 
                <th>Name:</th> 
                <th>Benutzername:</th>
                <th>Nachname:</th>
                <th>Vorname:</th>
                <th>UPN-AD:</th>
                <th>UPN-AAD:</th>
                <th>PaperCut-ID:</th>
            </tr>
        ';
    

        if($sizearray > 0)
        {
            for($i = 0; $i < $sizearray; $i++)
            {
                $Ausgabe .= ' 
                <tr>
                <td>'.$result[$i]["cn"].'</td>
                <td>'.$result[$i]["samaccountname"].'</td>
                <td>'.$result[$i]["sn"]  .'</td>
                <td>'.$result[$i]["givenname"]  .'</td>
                <td>'.$result[$i]["userprincipalname"]  .'</td>
                <td>'.$result[$i]["mail"].'</td>
                <td>'.$result[$i]["papercut"].'</td>
                </tr>
                ';                          
            }
        }   
        else
        {
            $Ausgabe .= '<td colspan="7">Keine Nutzer, die Ihren Angaben entsprechen, gefunden.</td>';
        }

        $Ausgabe .= '</table></div><p>Wird Ihnen in einem Feld N/A angezeigt, wurden diese Daten noch nicht hinterlegt.<br>Bitte wenden Sie sich bei Bedarf an das Schulteam.</p>';
    }
}

if (!empty($group))
{

    $result = getmembersecuritygroups($group);

    if(!$result)
    {
        echo "<div class='fehler centerflex'>Es ist ein unbekannter Fehler bei der Suche aufgetreten.</div>" ;
        exit;
    }
    else
    {
        if($export == true)
        {
            export($result);
            exit;
        }

        $sizearray = $result["count"];

        $Ausgabe .= ' 
        <h2>Mitglieder der Gruppe: '.$group.' </h2>
        <a href="' . $currentURL . '&export=true" class="exportbutton">Exportieren</a>
        <div class="centerflex">
        <table class="Tabelle"> 
        <tr> 
            <th>Name:</th> 
            <th>Benutzername:</th>
            <th>Nachname:</th>
            <th>Vorname:</th>
            <th>UPN-AD:</th>
            <th>UPN-AAD:</th>
            <th>PaperCut-ID:</th>
        </tr>
        ';

        if($sizearray > 0)
        {
            for($i = 0; $i < $sizearray; $i++)
            {
                $Ausgabe .= ' 
                <tr>
                <td>'.$result[$i]["cn"].'</td>
                <td>'.$result[$i]["samaccountname"].'</td>
                <td>'.$result[$i]["sn"]  .'</td>
                <td>'.$result[$i]["givenname"]  .'</td>
                <td>'.$result[$i]["userprincipalname"]  .'</td>
                <td>'.$result[$i]["mail"].'</td>
                <td>'.$result[$i]["papercut"].'</td>
                </tr>
                ';                          
            }
        }   
        else
        {
            $Ausgabe .= '<td colspan="7">Keine Nutzer, die Ihren Angaben entsprechen, gefunden.</td>';
        }

        $Ausgabe .= '</table></div><p>Wird Ihnen in einem Feld N/A angezeigt, wurden diese Daten noch nicht hinterlegt.<br>Bitte wenden Sie sich bei Bedarf an das Schulteam.</p>';
    }


}
include($_SERVER['DOCUMENT_ROOT'] . '/includes/header.php');

echo'
<div class="background">
<div class="seiteninhalt">
<h1 class="center">Zugriff auf Nutzerprofile für IT-Beauftrage.</h1><hr>
<div class="centerflex">
<div class="it">
<h2>Suche nach einzelnen Benutzern</h2>
<p>Sie müssen mindestens ein Feld ausfüllen, um die Suche zu starten.</p>
<form method="GET" class="login_form">
    <input class="status_input" type="text" name="nutzername" placeholder="Benutzername"> </br>
    <input class="status_input" type="text" name="vorname" placeholder="Vorname"> </br>
    <input class="status_input" type="text" name="nachname" placeholder="Nachname"> </br>
    <button class="status_button" type="submit">Suchen</button></form></div>
    <div class="it">
<h2>Suche nach gesamten Benutzergruppen</h2>
<p>Wählen Sie eine Benutzergruppe aus dem DropDownMenü aus.</p>
<form method="GET" class="login_form">
    <div class="select-container">
    <select  class="select-box" name="group" required>
    <option value=""selected disabled>- Bitte wählen -</option>
    '.$kurzel_select .' 
    </select>
    </div></br>
    <button class="status_button" type="submit">Suchen</button><br></div></div>
    <hr>






    '.$Ausgabe.'
</div></div>

';
include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php');
ob_end_flush();