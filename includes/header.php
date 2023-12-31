<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/functions/LDAPfunctions.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/functions/functions.php');
include($_SERVER['DOCUMENT_ROOT'] . '/config.php');
if(islehrer())
{
    $showlehrer = '
    <li><a href="#">Lehrende</a>
        <ul>
            <li><a href=/meineklasse.php>Meine Klasse</a></li>
            <li><a href=/find.php>SuS-ID</a></li>
        </ul>
    </li>';
}

if(isitbeauftragter())
{
    $itbeauftragte = '<li><a href=/admin.php>Admin</a></li>';
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
    <link rel="icon" href="/img/favicon-96x96.png" type="image/x-icon">
</head>
<body>
<nav>
<h2 class="header"> Schuldashboard '.$ldapDomainName.'</h2>
<ul>';
    if(basename($_SERVER['PHP_SELF']) !== 'login.php' && basename($_SERVER['PHP_SELF']) !== 'dashboard.php')
    { echo'
        <li><a href=/dashboard.php>Zurück zum Dashboard</a></li>';
    }
    echo $showlehrer;
        if(!empty($_SESSION["userid"]))
        {
            echo'
            <li><a href="#">'.returnfirstLetters($_SESSION["userid"]).'</a>
                <ul>
                    <li><a href=/login.php?logout=true>Logout</a></li>
                    <li><a href=/profil.php>Profil</a></li>
                    '.$itbeauftragte.'
                </ul>
            </li>
        ';
        }
        if($_SESSION["userid"] == "Administrator" || $_SESSION["userid"] == "administrator")
        {
            echo '</li><li><a href=/profil.php>Profil</a></li><li><a href=/login.php?logout=true>Logout</a>';
        }
echo'
</ul>
</nav>
';
