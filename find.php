<?php
ob_start();
require_once('functions/functions.php');
require_once('functions/LDAPfunctions.php');
include('includes/header.php');
userLoggedin();
if(!islehrer())
{
    header('Location: dashboard.php');
    exit;
}

$upn = checkLDAPInjektion($_GET["upn"]);

if(!empty($upn))
{
    $result = searchupn($upn);
    if(!$result)
    {
        echo "<div class='fehler centerflex'>Es ist ein unbekannter Fehler bei der Suche aufgetreten.</div>" ;
    }
    else
    {
        $sizearray = $result["count"];

        $Ausgabe .= ' 
        <hr>
        <h2>Suchergebnisse für: '.$upn.' </h2>
        <div class="centerflex">
        <table class="Tabelle"> 
        <tr> 
            <th>Name:</th> 
            <th>Benutzername:</th>
            <th>Nachname:</th>
            <th>Vorname:</th>
            <th>UPN-Lokal:</th>
            <th>UPN-Microsoft:</th>
            <th>Papercut:</th>
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

        $Ausgabe .= '</table></div><p>Wird Ihnen in einem Feld N/A angezeigt, wurden diese Daten noch nicht hinterlegt.<br>Bitte fragen Sie Ihren IT-Beauftragten, ob Ihre Schule diesen Dienst bereits einsetzt.</p>';
    }
}


echo'
<div class="background">
<div class="seiteninhalt">
<div class="login">
<h1>Identifizieren Sie Pseudonyme von Schülern.</h1><hr>
<p>Bitte geben Sie das Pseudonym ein, um den Schüler zu identifizieren.</p>
<form method="GET" class="login_form">
    <input class="status_input" type="text" name="upn" placeholder="Pseudonym"> </br>
    <button class="status_button" type="submit">Suchen</button>
</form></div>
'.$Ausgabe.'

</div></div>
';
include('includes/footer.php');
ob_end_flush();