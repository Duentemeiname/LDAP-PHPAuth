<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '\LDAP\functions\functions.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '\LDAP\functions\LDAPfunctions.php');
include($_SERVER['DOCUMENT_ROOT'] . '\LDAP\includes\header.php');
userLoggedin();
echo '
<div class="background">
<div class="seiteninhalt">
<div class="login">
<h1 class="center">Erhalten Sie hier Ihre Papercut ID.</h1> <hr>
<p>Der Schulträger stellt eine Druckmanagement-Lösung zur Verwaltung der Anwender (Lehrer und Schüler) und der Drucker im Netzwerk der Schulen zur Verfügung. Der Ausdruck erfolgt mittels unterschiedlicher
 Authentifizierungsmöglichkeiten. <br> Hier erhalten Sie Ihre <b>PaperCut ID</b>. Diese benötigen Sie zur Authentifizierung an den MFP-Großkopierern.</p>
 <p><b>' . getpapercutid($username) . '</b></p>
 <button class="button_redirect" onclick="window.location.href=\'https://wiki.medienzentrum-mtk.de/anleitungen/papercut/druckmanagement\'">Weiter Infos / Anleitung</button>
<button class="button_redirect" onclick="window.location.href=\'https://wood.mtk.schule/\'">Anmeldung am Webportal</button>

</div></div></div>';
include($_SERVER['DOCUMENT_ROOT'] . '\LDAP\includes\footer.php');