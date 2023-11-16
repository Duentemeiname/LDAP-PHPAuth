<?php
define ( 'MYSQL_HOST', $_ENV['MYSQL_HOST'] );

define ( 'MYSQL_BENUTZER',  $_ENV['MYSQL_USER'] );

define ( 'MYSQL_KENNWORT',  $_ENV['MYSQL_PASSWORD'] );

define ( 'MYSQL_DATENBANK', $_ENV['MYSQL_DATENBANK'] );

define ( 'MYSQL_PORT', $_ENV['MYSQL_PORT'] );




$db_link = new mysqli  (MYSQL_HOST, 
                        MYSQL_BENUTZER, 
                        MYSQL_KENNWORT, 
                        MYSQL_DATENBANK,
                        MYSQL_PORT,
                        );

// Verbindung prüfen
if ($db_link->connect_error) 
{
    die('Datenbankverbindung fehlgeschlagen' . $db_link->connect_error);
}

function SQLtoDB($Anfrage)
{
    global $db_link;
    try
    {
        return $db_link->query($Anfrage);
    }
    catch(Exception $e)
    {
        insertlog("datenbank", $e->getMessage());
        return false;
    }
}

$createDatabaseSQL = "CREATE TABLE IF NOT EXISTS `securitytokens`(
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `user_id` varchar(255) NOT NULL,
    `identifier` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
    `securitytoken` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";

if ($db_link->query($createDatabaseSQL) !== TRUE) 
{
    die( "Fehler beim Erstellen der Datenbank: " . $conn->error);
} 

$createDatabaseSQL = "CREATE TABLE IF NOT EXISTS `errorlog`(
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `typ` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
    `ip` varchar(255) COLLATE utf8_unicode_ci,
    `vorfall` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
    `user` varchar(255) COLLATE utf8_unicode_ci,
    `userdevice` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";

if ($db_link->query($createDatabaseSQL) !== TRUE) 
{
    die( "Fehler beim Erstellen der Datenbank: " . $conn->error);
} 

$createDatabaseSQL = "CREATE TABLE IF NOT EXISTS `lockeduser`(
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `userid` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
    `tries` int(10) COLLATE utf8_unicode_ci NOT NULL,
    `lasttry` timestamp NOT NULL 
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";

if ($db_link->query($createDatabaseSQL) !== TRUE) 
{
    die( "Fehler beim Erstellen der Datenbank: " . $conn->error);
} 
