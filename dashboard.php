<?php
require_once("functions/functions.php");
require_once("functions/connect.php");
include("includes/header.php");
userLoggedin();
echo '
<div class="background">
<div class="seiteninhalt">
<h1 class="center">Hallo, '.returnCN($_SESSION["userid"]).'. Willkommen in Deinem Dashboard!</h1> <hr>
<h2> Folgende Dienste stehen Ihnen zu Verfügung: </h2>

<div class="container">
    <div class="parentL">
        <a href="office365.php">
            <img src="o365.png" width="auto">
                <div class="text">
                    <h2 class="colorblue header365">Office 365</h2>
                    <p class="colorblue absatz365">Office 365 ermöglicht die effiziente Nutzung von Microsoft Office-Anwendungen lokal als auch in der Cloud.</p>
                </div>
        </a>
    </div>

    <div class="parentR">
        <a href="papercut.php">
            <img src="papercut.png" width="auto">
                <p class="PaperCutHead colorblue zeromargin center" > Erhalten Sie Ihre PaperCut ID und Drucken Sie mobil!<p>

        </a>
    </div>
</div>

</div></div>';







include("includes/footer.php");