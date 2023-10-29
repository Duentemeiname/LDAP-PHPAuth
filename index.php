<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '\functions\functions.php');
if(userLoggedin())
{
    header('Location: dashboard.php');
}

