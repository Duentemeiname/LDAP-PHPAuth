#!/bin/bash

echo "Dieses Script vereinheitlicht die Bereitstellung des Schuldashboards. Es fragt alle nötigen Daten ab und baut das Schuldashboard als Docker.
Die Konfiguration von Traefik muss vor dem Ausführen dieses Scripts in der docker-compose.yml angepasst werden."

echo "Erstelle Umgebungsvariablen"
mkdir -p .envs

# Datei .app mit dem gewünschten Inhalt erstellen
cat <<EOL > .envs/.app
# Dieses File wird automatisch durch das Setupscript erstellt

# Information about the Domaine
ldapNutzername=
ldapPasswort=
ldapServer=
ldapPort=
ldapDomainName=
ldapBaseDn=
ldaps=

# LDAP Paths which are needed
ldapSecurityGroupDomainenAdmin=
ldapSecurityGroupITBeauftragte=
ldapSecurityGroupLehrer=
ldapSecurityGroupSuS=
ldapOUSecurityGroupClasses=
ldapUserLoginAllowed=
EOL

# Datei .mariadb mit dem gewünschten Inhalt erstellen
cat <<EOL > .envs/.mariadb
MARIADB_ROOT_PASSWORD=
MARIADB_DATABASE=
MARIADB_USER=
MARIADB_PASSWORD=
MYSQL_HOST=
MYSQL_PORT=
EOL

echo "Der Ordner .envs und die Dateien .app und .mariadb wurden erfolgreich erstellt."

echo "Datenbankkonfiguration:"
read -p "Der default Host für die Datenbank ist ldap-phpauth-dashboard-db-1, möchten Sie diesen ändern? (y/n) " choosehost

if [ "$choosehost" == "y" ]; then
    read -p "Bitte geben Sie den Host für die Datenbank ein: " dbhost
elif [ "$choosehost" == "n" ]; then
    dbhost="ldap-phpauth-dashboard-db-1"
else 
    echo "Ungültige Eingabe"
    exit 1
fi

read -p "Möchten Sie die Zugangsdaten für die Maria DB selber konfigurieren oder sollen Zufallswerte gesetzt werden? (y/n) " choosemariadb

if [ "$choosemariadb" == "y" ]; then
    read -p "Bitte geben Sie das Root-Passwort für die Datenbank ein: " dbrootpassword
    read -p "Bitte geben Sie den Datenbanknamen ein: " dbname
    read -p "Bitte geben Sie den Datenbankbenutzer ein: " dbuser
    read -p "Bitte geben Sie das Datenbankpasswort ein: " dbpassword
elif [ "$choosemariadb" == "n" ]; then
    dbrootpassword=$(cat /dev/urandom | tr -dc 'a-zA-Z0-9' | fold -w 10 | head -n 1)
    dbname=$(cat /dev/urandom | tr -dc 'a-zA-Z0-9' | fold -w 10 | head -n 1)
    dbuser=$(cat /dev/urandom | tr -dc 'a-zA-Z0-9' | fold -w 10 | head -n 1)
    dbpassword=$(cat /dev/urandom | tr -dc 'a-zA-Z0-9' | fold -w 10 | head -n 1)
else 
    echo "Ungültige Eingabe"
    exit 1
fi

echo "LDAP-Konfiguration:"

read -p "Verwenden Sie SSL/TLS? (y/n) " usessl
if [ "$usessl" == "y" ]; then
    echo "Bitte beachten Sie, dass der LDAP-Server SSL/TLS unterstützen muss."
    echo "Bitte beachten Sie: https://learn.microsoft.com/de-de/troubleshoot/windows-server/identity/enable-ldap-over-ssl-3rd-certification-authority und https://www.php.net/manual/de/function.ldap-start-tls.php"
    read -p "Möchten Sie SSL/TLS aktivieren? (y/n) " usessl
    if [ "$usessl" == "y" ]; then
        echo "Bitte passen Sie entsprechend den verwendeten Port an."
        usessl="true"
    elif [ "$usessl" == "n" ]; then
        usessl="false"
    else 
        echo "Ungültige Eingabe"
        exit 1
    fi
elif [ "$usessl" == "n" ]; then
    usessl="false"
else 
    echo "Ungültige Eingabe"
    exit 1
fi

echo "Bitte stellen Sie sicher, wenn Sie LDAP-Pfade eingeben, diese immer mit Anführungszeichen eingeben. (Bsp: 'OU=Benutzer,OU=schule,')"
read -p "Bitte geben Sie den LDAP-Nutzernamen ein: (Bsp: Administrator) " ldapNutzername
read -p "Bitte geben Sie das LDAP-Passwort ein: " ldapPasswort
read -p "Bitte geben Sie den LDAP-Server ein: (Bsp: s1001.local.duentetech.de) " ldapServer
read -p "Bitte geben Sie den LDAP-Port ein: (Bsp: 389) " ldapPort
read -p "Bitte geben Sie den LDAP-Domainnamen ein: (Bsp: HOME) " ldapDomainName
read -p "Bitte geben Sie den LDAP-Base DN ein: (Bsp: \"DC=local,DC=duentetech,DC=de\") " ldapBaseDn

echo "Bitte prüfen Sie, ob die angegebenen LDAP-Pfade korrekt sind. Bitte beachten Sie, dass die Base DN nicht mit eingegeben wird, da diese aus der vorherigen Eingabe übernommen wird. Am Ende jeder Eingabe muss ein Komma gesetzt werden und die Pfade müssen mit Anführungszeichen eingegeben werden."

ldapSecurityGroupDomainenAdmin="\"CN=Domänen-Admins,CN=Users,\""
ldapSecurityGroupITBeauftragte="\"CN=IT-Beauftragte,OU=Rollen,OU=Gruppen,\""
ldapSecurityGroupLehrer="\"CN=Alle Lehrer,OU=Benutzer,OU=schule,\""
ldapSecurityGroupSuS="\"CN=Alle Schüler,OU=Benutzer,OU=schule,\""
ldapOUSecurityGroupClasses="\"OU=Klassen,OU=Gruppen,OU=schule,\""
ldapUserLoginAllowed="\"OU=Benutzer,OU=schule,\""


read -p "Der default Pfad für die Domänen Admins ist $ldapSecurityGroupDomainenAdmin, möchten Sie diesen ändern? (y/n) " choosepath
if [ "$choosepath" == "y" ]; then
    read -p "Bitte geben Sie den Pfad für die Domänen Admins ein: " input
    ldapSecurityGroupDomainenAdmin=$input
elif [ "$choosepath" == "n" ]; then
    :
else 
    echo "Ungültige Eingabe"
    exit 1
fi

read -p "Der default Pfad für die IT-Beauftragten ist $ldapSecurityGroupITBeauftragte, möchten Sie diesen ändern? (y/n) " choosepath
if [ "$choosepath" == "y" ]; then
    read -p "Bitte geben Sie den Pfad für die IT-Beauftragten ein: " input
    ldapSecurityGroupITBeauftragte=$input
elif [ "$choosepath" == "n" ]; then
    :
else 
    echo "Ungültige Eingabe"
    exit 1
fi

read -p "Der default Pfad für die Lehrer ist $ldapSecurityGroupLehrer, möchten Sie diesen ändern? (y/n) " choosepath
if [ "$choosepath" == "y" ]; then
    read -p "Bitte geben Sie den Pfad für die Lehrer ein: " input
    ldapSecurityGroupLehrer=$input
elif [ "$choosepath" == "n" ]; then
    :
else 
    echo "Ungültige Eingabe"
    exit 1
fi

read -p "Der default Pfad für die Schüler ist $ldapSecurityGroupSuS, möchten Sie diesen ändern? (y/n) " choosepath
if [ "$choosepath" == "y" ]; then
    read -p "Bitte geben Sie den Pfad für die Schüler ein: " input
    ldapSecurityGroupSuS=$input
elif [ "$choosepath" == "n" ]; then
    :
else 
    echo "Ungültige Eingabe"
    exit 1
fi

read -p "Der default Pfad für die Klassen ist $ldapOUSecurityGroupClasses, möchten Sie diesen ändern? (y/n) " choosepath    
if [ "$choosepath" == "y" ]; then
    read -p "Bitte geben Sie den Pfad für die Klassen ein: " input
    ldapOUSecurityGroupClasses=$input
elif [ "$choosepath" == "n" ]; then
    :
else 
    echo "Ungültige Eingabe"
    exit 1
fi

read -p "Der default Pfad für die Benutzer ist $ldapUserLoginAllowed, möchten Sie diesen ändern? (y/n) " choosepath
if [ "$choosepath" == "y" ]; then
    read -p "Bitte geben Sie den Pfad für die Benutzer ein: " input
    ldapUserLoginAllowed=$input
elif [ "$choosepath" == "n" ]; then
    :
else 
    echo "Ungültige Eingabe"
    exit 1
fi

# Ausgabe der neuen Werte zur Überprüfung
echo "Neue Werte:"
echo "ldapSecurityGroupDomainenAdmin=$ldapSecurityGroupDomainenAdmin"
echo "ldapSecurityGroupITBeauftragte=\"$ldapSecurityGroupITBeauftragte"
echo "ldapSecurityGroupLehrer=$ldapSecurityGroupLehrer"
echo "ldapSecurityGroupSuS=$ldapSecurityGroupSuS"
echo "ldapOUSecurityGroupClasses=\"$ldapOUSecurityGroupClasses"
echo "ldapUserLoginAllowed=$ldapUserLoginAllowed"

# Werte in die .envs/.mariadb Datei schreiben
echo "MARIADB_ROOT_PASSWORD=$dbrootpassword" > .envs/.mariadb
echo "MARIADB_DATABASE=$dbname" >> .envs/.mariadb
echo "MARIADB_USER=$dbuser" >> .envs/.mariadb
echo "MARIADB_PASSWORD=$dbpassword" >> .envs/.mariadb
echo "MYSQL_HOST=$dbhost" >> .envs/.mariadb
echo "MYSQL_PORT=3306" >> .envs/.mariadb

# Werte in die .envs/.app Datei schreiben
echo "ldapNutzername=$ldapNutzername" > .envs/.app
echo "ldapPasswort=$ldapPasswort" >> .envs/.app
echo "ldapServer=$ldapServer" >> .envs/.app
echo "ldapPort=$ldapPort" >> .envs/.app
echo "ldapDomainName=$ldapDomainName" >> .envs/.app
echo "ldapBaseDn=$ldapBaseDn" >> .envs/.app
echo "ldaps=$usessl" >> .envs/.app
echo "ldapSecurityGroupDomainenAdmin=$ldapSecurityGroupDomainenAdmin" >> .envs/.app
echo "ldapSecurityGroupITBeauftragte=$ldapSecurityGroupITBeauftragte" >> .envs/.app
echo "ldapSecurityGroupLehrer=$ldapSecurityGroupLehrer" >> .envs/.app
echo "ldapSecurityGroupSuS=$ldapSecurityGroupSuS" >> .envs/.app
echo "ldapOUSecurityGroupClasses=$ldapOUSecurityGroupClasses" >> .envs/.app
echo "ldapUserLoginAllowed=$ldapUserLoginAllowed" >> .envs/.app

# Bestätigung der gesetzten Umgebungsvariablen
echo "Die folgenden Umgebungsvariablen wurden in .envs/.mariadb gesetzt:"
echo "MARIADB_ROOT_PASSWORD=$dbrootpassword"
echo "MARIADB_DATABASE=$dbname"
echo "MARIADB_USER=$dbuser"
echo "MARIADB_PASSWORD=$dbpassword"
echo "MYSQL_HOST=$dbhost"
echo "MYSQL_PORT=3306"

echo "Die folgenden Umgebungsvariablen wurden in .envs/.app gesetzt:"
echo "ldapNutzername=$ldapNutzername"
echo "ldapPasswort=$ldapPasswort"
echo "ldapServer=$ldapServer"
echo "ldapPort=$ldapPort"
echo "ldapDomainName=$ldapDomainName"
echo "ldapBaseDn=$ldapBaseDn"
echo "ldaps=$usessl"
echo "ldapSecurityGroupDomainenAdmin=$ldapSecurityGroupDomainenAdmin"
echo "ldapSecurityGroupITBeauftragte=$ldapSecurityGroupITBeauftragte"
echo "ldapSecurityGroupLehrer=$ldapSecurityGroupLehrer"
echo "ldapSecurityGroupSuS=$ldapSecurityGroupSuS"
echo "ldapOUSecurityGroupClasses=$ldapOUSecurityGroupClasses"
echo "ldapUserLoginAllowed=$ldapUserLoginAllowed"

echo "Prüfe auf alte Datenbank Dateien"
if [ -d "data-mariadb" ]; then
    sudo rm -rf "data-mariadb"
    echo "Das Verzeichnis 'data-mariadb' wurde gelöscht."
else
    echo "Das Verzeichnis 'data-mariadb' existiert nicht."
fi

# Abschlussnachricht ausgeben
echo "Setup abgeschlossen. Die Umgebungsvariablen wurden erfolgreich gesetzt. Der Docker wird nun gebaut!"

# Docker Compose ausführen, um den Container zu starten
sudo docker compose up --build --force-recreate -d
