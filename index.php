<?php
ob_start();
require_once($_SERVER['DOCUMENT_ROOT'] . '/functions/functions.php');
checkie();
if(userLoggedin())
{
   header('Location: dashboard.php');
}
ob_end_flush();
