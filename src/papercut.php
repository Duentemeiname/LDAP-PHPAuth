<?php
ob_start();
require_once($_SERVER['DOCUMENT_ROOT'] . '/functions/functions.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/functions/LDAPfunctions.php');
include($_SERVER['DOCUMENT_ROOT'] . '/includes/header.php');
userLoggedin();

$result = checkifpapercutavailable($_SESSION["userid"]);
if($result === FALSE)
{
    echo "<div class='centerflex'><div class='fehler'> Entweder PaperCut ist an Ihrer Schule nicht verfügbar, oder Ihr Profil wurde noch nicht freigeschaltet. Bitte versuchen Sie es später erneut. <br> Wenden Sie sich bei Fragen bitte an den IT-Beauftragten. <br> Fehlercode: employeenumber-is-empty</div></div><br>" ;
}
else
{
    $entries = returnuserarray($_SESSION["userid"]);
    echo '
    <div class="background">
    <div class="seiteninhalt">
    <div class="login">
    <h1 class="center">Zugangsmöglichkeiten zu PaperCut</h1> <hr>
    <p>Der Schulträger stellt eine Druckmanagement-Lösung zur Verwaltung der Anwender (Lehrer und Schüler) und der Drucker im Netzwerk der Schulen zur Verfügung. <br>
    Der Ausdruck erfolgt mittels unterschiedlicher Authentifizierungsmöglichkeiten. <br>
    Sie erhalten hier (1) Ihren Nutzernamen für wood.mtk.schule und (2) Ihre PaperCut-ID für die  Authentifizierung an den MFP-Großkopierern.</p>
    <p><b>(1) Nutzername: ' . $entries["userprincipalname"] . '</b></p>
    <p><b>(2) PaperCut-ID: ' . $result . '</b></p>
    <button class="button_redirect" onclick="window.location.href=\'https://wiki.medienzentrum-mtk.de/anleitungen/papercut/druckmanagement\'">Weiter Infos / Anleitung</button>
    <button class="button_redirect" onclick="window.location.href=\'https://wood.mtk.schule/\'">Anmeldung am Webportal</button>

    <p> Hinweis: Mit Hilfe Ihres Nutzernamens (1) können Sie sich bei wood.mtk.schule anmelden und mobil auf den Druckern im Schulnetzwerk drucken. <br>
    Mit Hilfe der PaperCut-ID (2) können Sie sich an den Kopieren anmelden. Dies ermöglicht u.A. das Abrufen der FindME Warteschlange. 
    </p>

    </div></div></div>';
}

include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php');
ob_end_flush();