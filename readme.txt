Hinweise zur Installation:

1. Im Dockerfile bzw später in dem Docker Umgebungsvariablen anlegen. Folgende sind notwendig:

  MYSQL_HOST= IP-Adresse, des Datenbankservers
  MYSQL_USER= Nutzer, der auf der Datenbank lesen und schreiben darf
  MYSQL_PASSWORD= Passwort für den DB-Nutzer 
  MYSQL_DATENBANK= Name der Datenbank, in der gespeichert wird
  MYSQL_PORT= Port, auf dem der DB-Server läuft
  ldapNutzername= Nutzername, der leserechte auf das lokale LDAP-Verzeichnis hat
  ldapPasswort= Passwort für den LDAP-Nutzer

Bespiel:
 
  MYSQL_HOST=10.20.0.3
  MYSQL_USER=root
  MYSQL_PASSWORD=test123
  MYSQL_DATENBANK=LDAP
  MYSQL_PORT=3333
  ldapNutzername=Administrator
  ldapPasswort=test123

2. Config.php file an das vorhandene LDAP-Verzeichnis anpassen. In der Regel sind hier nur 5 Änderungen notwendig:

    $ldapServer = "home.duentetech.de";
    $ldapPort = 389;
    $ldapDomainName = "HOME";
    $ldapBaseDn = "DC=home,DC=duentetech,DC=de";
    $GlobalServerUrl = "http://localhost:8088/"; //WICHTIG: Mit Slash am Ende!

3. Je nach Verfügbarkeit LDAP Secure auswählen:
    $LDAPS = false; /* Um die LDAPS Verbindung zu aktivieren, setzen Sie die Variable $LDAPS auf true*/