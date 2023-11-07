<?php
ob_start();
require_once('functions/functions.php');
require_once('functions/LDAPfunctions.php');
include('includes/header.php');
userLoggedin();
if(!islehrer())
{
    header('Location dashboard.php');
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
        <hr><div class="centerflex">
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
                <td>'.$returnarray[$i]["papercut"].'</td>
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
<div class="login">
<h2>Identifizieren Sie Pseudonyme von Schülern.</h2><hr>
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