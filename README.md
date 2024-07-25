# Schuldashboard Setup

Dieses Repository enthält alle notwendigen Dateien und Skripte zur Bereitstellung des Schuldashboards als Docker-Container. Bitte folgen Sie den untenstehenden Schritten, um das Schuldashboard zu konfigurieren und zu starten.

## Voraussetzungen

- Docker und Docker Compose müssen installiert sein.
- Eine Anpassung der Traefik-Konfiguration in der `docker-compose.yml` Datei ist erforderlich. Dort muss bei der Verwendung von treafik auch der Port verändert werden und die Netzwerke anders verwaltet werden.


## Setup

1. **Clone das Repository:**

    ```sh
    git clone <repository-url>
    cd <repository-name>
    ```

2. **Ausführung des Setup-Skripts:**

    Führen Sie das Setup-Skript aus, um die notwendigen Umgebungsvariablen zu erstellen und zu konfigurieren.

    ```sh
    chmod +x setup.sh
    ./setup.sh
    ```

    Das Skript führt Sie durch verschiedene Konfigurationsschritte:

    - Erstellung der `.envs` Verzeichnisse und Dateien
    - Datenbankkonfiguration
    - LDAP-Konfiguration

    **Hinweis:** Während des Setups werden Sie aufgefordert, verschiedene Eingaben zu machen, wie z.B. Datenbank- und LDAP-Informationen. 
    Der Eingegebene LDAP-Nutzer benötigt Leserechte auf das gesamte Userverzeichnis. 

3. **Überprüfung der Konfiguration:**

    Nach Abschluss des Setup-Skripts werden die gesetzten Umgebungsvariablen angezeigt. Überprüfen Sie diese, um sicherzustellen, dass alle Angaben korrekt sind.

    ```sh
    Die folgenden Umgebungsvariablen wurden in .envs/.mariadb gesetzt:
    MARIADB_ROOT_PASSWORD=**********
    MARIADB_DATABASE=**********
    MARIADB_USER=**********
    MARIADB_PASSWORD=**********
    MYSQL_HOST=**********
    MYSQL_PORT=3306

    Die folgenden Umgebungsvariablen wurden in .envs/.app gesetzt:
    ldapNutzername=**********
    ldapPasswort=**********
    ldapServer=**********
    ldapPort=**********
    ldapDomainName=**********
    ldapBaseDn=**********
    ldaps=**********
    ldapSecurityGroupDomainenAdmin=**********
    ldapSecurityGroupITBeauftragte=**********
    ldapSecurityGroupLehrer=**********
    ldapSecurityGroupSuS=**********
    ldapOUSecurityGroupClasses=**********
    ldapUserLoginAllowed=**********
    ```

4. **Starten des Docker-Containers:**

    Das Skript löscht gegebenenfalls vorhandene alte Datenbankdateien und startet anschließend den Docker-Container.

    ```sh
    sudo docker compose up --build --force-recreate -d
    ```

    Dies wird den Docker-Container bauen und starten.

## Nutzung

Nach erfolgreichem Start des Containers sollte das Schuldashboard über die konfigurierte URL erreichbar sein. Weitere Anpassungen und Konfigurationen können direkt in den entsprechenden Docker- und Applikationsdateien vorgenommen werden.

## Fehlersuche

Falls Probleme auftreten:

- Überprüfen Sie die Docker-Logs, um detaillierte Fehlermeldungen zu erhalten:

    ```sh
    sudo docker compose logs
    ```

- Stellen Sie sicher, dass alle Konfigurationsdateien korrekt sind und dass alle notwendigen Ports geöffnet und nicht von anderen Diensten blockiert sind.
- Aktivieren Sie PHP display_errors in dem Sie in der Datei /dockerfiles/app/Dockerfile die Zeile RUN echo "display_errors = Off" > /usr/local/etc/php/php.ini löschen.
## Lizenz

Dieses Projekt ist unter der Creative Commons Namensnennung MIT License lizenziert – siehe die [LICENSE](LICENSE) Datei für Details.

---

Für weitere Fragen oder Unterstützung, wenden Sie sich bitte an den Projektverantwortlichen oder erstellen Sie ein Issue im Repository.
