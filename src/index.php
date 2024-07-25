<?php
ob_start();
echo "index.php was loaded successfully, it should be redirected immediately...";
require_once('functions/functions.php');
checkie();
if(userLoggedin())
{
   header('Location: dashboard.php');
}
ob_end_flush();
