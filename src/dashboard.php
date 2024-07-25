<?php
ob_start();
require_once('functions/functions.php');
require_once('functions/connect.php');
include($_SERVER['DOCUMENT_ROOT'] . '/includes/header.php');
userLoggedin();
if(isitbeauftragter())
{
    $showitbeauftragter = '<br>
    <hr>
    <h2>Weitere Infos für IT-Beauftragte: </h2><br>
    <div class="center">
    <button class="button_redirect" onclick="window.location.href=\' itbeauftragte.php \'">Informationen zu Nutzern abrufen</button><br>
    <button class="button_redirect" onclick="window.open(\'https://wiki.medienzentrum-mtk.de/\', \'_blank\')">Wiki Medienzentrum</button>
    <button class="button_redirect" onclick="window.open(\'https://helpdesk-schulen.mtk.org/helpLinePortalAmt38/de-DE\', \'_blank\')">Ticketsystem Schulteam</button>
    <button class="button_redirect" onclick="window.open(\'https://www.mtk.org/Schulteam-4328.htm\', \'_blank\')">Kontakt zum Schulteam</button>
    <button class="button_redirect" onclick="window.open(\'https://github.com/Duentemeiname/LDAP-PHPAuth/issues\', \'_blank\')">Fragen zum Dashboard</button>
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
                    <p class="colorblue absatz365">Office 365 ermöglicht Ihnen eine Nutzung auf Mobilgeräten und auf lokalen Rechnern. Die Cloud ist gesperrt.</p>
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
<button class="button_redirect" onclick="window.open(\'https://start.schulportal.hessen.de\', \'_blank\')">Schulportal Hessen</button>
<button class="button_redirect" onclick="window.open(\'https://login.microsoftonline.com\', \'_blank\')">Login Microsoft</button>
<button class="button_redirect" onclick="window.open(\'https://owa.hessen.de/\', \'_blank\')">E-Mail-OWA</button>
</div>


</div></div>';







include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php');
ob_end_flush();
