<?php
require_once("functions/LDAPfunctions.php");
require_once("functions/functions.php");
include("config.php");

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
    <link rel="stylesheet" href="style.css">
</head>
<body>
<nav>
<h2 class="header"> Schuldashboard '.$ldapDomainName.'</h2>
<ul>';
    if(basename($_SERVER['PHP_SELF']) !== 'login.php' && basename($_SERVER['PHP_SELF']) !== 'dashboard.php')
    { echo'
        <li><a href="dashboard.php">Zurück zum Dashboard</a></li>';
    }
        if(!empty($_SESSION["userid"]))
        {echo'
            <li><a href="#">'.returnfirstLetters($_SESSION["userid"]).'</a>
                <ul>
                    <li><a href="login.php?logout=true">Logout</a></li>
                    <li><a href="profil.php">Profil</a></li>
                </ul>
            </li>
        ';
        }
echo'
</ul>
</nav>
';