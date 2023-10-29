<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '\LDAP\functions\functions.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '\LDAP\functions\LDAPfunctions.php');
include($_SERVER['DOCUMENT_ROOT'] . '\LDAP\includes\header.php');
userLoggedin();
if(!isitbeauftragter())
{
    header('Location dashboard.php');
    exit;
}
$username = checkLDAPInjektion($_GET["nutzername"]);
$vorname = checkLDAPInjektion($_GET["vorname"]);
$nachname = checkLDAPInjektion($_GET["nachname"]);
$group = checkLDAPInjektion($_GET["group"]);

$kurzel_select = getsecuritygroups();

if (!empty($username) || !empty($vorname) || !empty($nachname))
{

    $result = searchuser($username, $vorname, $nachname);
    if(!$result)
    {
        echo "<div class='fehler centerflex'>Es ist ein unbekannter Fehler bei der Suche aufgetreten.</div>" ;
    }
    else
    {
        $sizearray = $result["count"];

        $Ausgabe .= ' 
        <div class="centerflex">
        <table class="Tabelle"> 
        <tr> 
            <th>Name:</th> 
            <th>Benutzername:</th>
            <th>Nachname:</th>
            <th>Vorname:</th>
            <th>UPN:</th>
            <th>Microsoft:</th>
            <th>PaperCut:</th>
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
                <td>PaperCutID</td>
                </tr>
                ';                          
            }
        }   
        else
        {
            $Ausgabe .= '<td colspan="6">Keine Nutzer, die Ihren Angaben entsprechen, gefunden.</td>';
        }

        $Ausgabe .= '</table></div>';
    }
}

if (!empty($group))
{

    $result = getmembersecuritygroups($group);
    if(!$result)
    {
        echo "<div class='fehler centerflex'>Es ist ein unbekannter Fehler bei der Suche aufgetreten.</div>" ;
    }
    else
    {
        $sizearray = $result["count"];

        $Ausgabe .= ' 
        <div class="centerflex">
        <table class="Tabelle"> 
        <tr> 
            <th>Name:</th> 
            <th>Benutzername:</th>
            <th>Nachname:</th>
            <th>Vorname:</th>
            <th>UPN:</th>
            <th>Microsoft:</th>
            <th>PaperCut:</th>
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
                <td>PaperCutID</td>
                </tr>
                ';                          
            }
        }   
        else
        {
            $Ausgabe .= '<td colspan="6">Keine Nutzer, die Ihren Angaben entsprechen, gefunden.</td>';
        }

        $Ausgabe .= '</table></div>';
    }


}

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
include($_SERVER['DOCUMENT_ROOT'] . '\LDAP\includes\footer.php');