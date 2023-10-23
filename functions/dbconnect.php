<?php
define ( 'MYSQL_HOST', 'localhost' );

define ( 'MYSQL_BENUTZER',  'LDAP_USER' );

define ( 'MYSQL_KENNWORT',  'mU7XP2HF2HKgwjN' );

define ( 'MYSQL_DATENBANK', 'LDAP' );

$db_link = new mysqli  (MYSQL_HOST, 
                        MYSQL_BENUTZER, 
                        MYSQL_KENNWORT, 
                        MYSQL_DATENBANK);

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
        //Später eintrag in die DB
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

