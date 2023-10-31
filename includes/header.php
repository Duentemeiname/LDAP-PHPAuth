<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/functions/LDAPfunctions.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/functions/functions.php');
include($_SERVER['DOCUMENT_ROOT'] . '/config.php');
if(islehrer())
{
    if(basename($_SERVER['PHP_SELF']) !== 'lehrende.php')
    $showlehrer = '<li><a href='.$GlobalServerUrl.'lehrende.php>Meine Klasse</a></li>';
}

echo'
<!DOCTYPE html>
<head>
    <html lang="de">
    <title>Dashboard - Schulen MTK</title>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <link rel="stylesheet" href="/style.css">
</head>
<body>
<nav>
<h2 class="header"> Schuldashboard '.$ldapDomainName.'</h2>
<ul>';
    if(basename($_SERVER['PHP_SELF']) !== 'login.php' && basename($_SERVER['PHP_SELF']) !== 'dashboard.php')
    { echo'
        <li><a href='.$GlobalServerUrl.'dashboard.php>Zurück zum Dashboard</a></li>';
    }
    echo $showlehrer;
        if(!empty($_SESSION["userid"]))
        {
            echo'
            <li><a href="#">'.returnfirstLetters($_SESSION["userid"]).'</a>
                <ul>
                    <li><a href='.$GlobalServerUrl.'login.php?logout=true>Logout</a></li>
                    <li><a href='.$GlobalServerUrl.'profil.php>Profil</a></li>
                </ul>
            </li>
        ';
        }
        if($_SESSION["userid"] == "Administrator" || $_SESSION["userid"] == "administrator")
        {
            echo '</li><li><a href='.$GlobalServerUrl.'profil.php>Profil</a></li><li><a href='.$GlobalServerUrl.'login.php?logout=true>Logout</a>';
        }
echo'
</ul>
</nav>
';
