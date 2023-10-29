<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '\LDAP\functions\functions.php');
if(userLoggedin())
{
    header('Location: dashboard.php');
}

