<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '\LDAP\functions\functions.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '\LDAP\functions\connect.php');
include($_SERVER['DOCUMENT_ROOT'] . '\LDAP\includes\header.php');
userLoggedin();
if(isitbeauftragter())
{
    $showitbeauftragter = '<br>
    <hr>
    <h2>Weitere Infos für IT-Beauftragte: </h2><br>
    <div class="center">
    <button class="button_redirect" onclick="window.location.href=\' itbeauftragte.php \'">Informationen zu Nutzern abrufen</button><br>
    <button class="button_redirect" onclick="window.location.href=\'https://wiki.medienzentrum-mtk.de/\'">Wiki Medienzentrum</button>
    <button class="button_redirect" onclick="window.location.href=\'https://helpdesk-schulen.mtk.org/helpLinePortalAmt38/de-DE\'">Ticketsystem Schulteam</button>
    <button class="button_redirect" onclick="window.location.href=\'https://www.mtk.org/Schulteam-4328.htm\'">Kontakt zum Schulteam</button>
    <button class="button_redirect" onclick="window.location.href=\'https://github.com/Duentemeiname/LDAP-PHPAuth/issues\'">Fragen zum Dashboard</button>
    </div>';
}
echo '
<div class="background">
<div class="seiteninhalt">
<h1 class="center">Hallo, '.returnCN($_SESSION["userid"]).'. Willkommen in Deinem Dashboard!</h1> <hr>
<h2> Folgende Dienste stehen Ihnen zu Verfügung: </h2>

<div class="container">
    <div class="parentL">
        <a href="office365.php">
            <img src="img\o365.png" width="auto">
                <div class="text">
                    <h2 class="colorblue header365">Office 365</h2>
                    <p class="colorblue absatz365">Office 365 ermöglicht die effiziente Nutzung von Microsoft Office-Anwendungen lokal als auch in der Cloud.</p>
                </div>
        </a>
    </div>

    <div class="parentR">
        <a href="papercut.php">
            <img src="img\papercut.png" width="auto">
                <p class="PaperCutHead colorblue zeromargin center" > Erhalten Sie Ihre PaperCut ID und Drucken Sie mobil!<p>

        </a>
    </div>
</div>
'.$showitbeauftragter.'
<br>
<hr>
<h2> Weitere hilfreiche Links: </h2><br>
<div class="center">
<button class="button_redirect" onclick="window.location.href=\'https://start.schulportal.hessen.de\'">Schulportal Hessen</button>
<button class="button_redirect" onclick="window.location.href=\'https://login.microsoftonline.com\'">Login Microsoft</button>
<button class="button_redirect" onclick="window.location.href=\'https://owa.hessen.de/\'">E-Mail-OWA</button>
<button class="button_redirect" onclick="window.location.href=\'https://stundenplan.bwshofheim.de/daVinci-timetable.html\'">Stundenplan</button>
</div>


</div></div>';







include($_SERVER['DOCUMENT_ROOT'] . '\LDAP\includes\footer.php');