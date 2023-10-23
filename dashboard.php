<?php
require_once("functions/functions.php");
require_once("functions/connect.php");
include("includes/header.php");
userLoggedin();

echo "Hallo ".$_SESSION['userid'];



include("includes/footer.php");