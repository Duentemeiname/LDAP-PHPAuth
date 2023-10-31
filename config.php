<?php

/* Hier die Konfiguration des jeweiligen Servers ändern*/
$ldapServer = "home.duentetech.de";
$ldapPort = 389;
$ldapDomainName = "HOME";
$ldapBaseDn = "DC=home,DC=duentetech,DC=de";
$ldapNutzername = $_ENV['ldapNutzername'];                              //getenv(?string $name = null, bool $local_only = false): string|array|false -> getenv('REMOTE_ADDR', true) -> https://www.php.net/manual/de/function.getenv.php
$ldapPasswort = $_ENV['ldapPasswort'];
$GlobalServerUrl = "http://localhost:8088/"; //WICHTIG: Mit Slash am Ende!


/*LDAPS Verbindung über TLS*/
/*Bitte beachten Sie vor der Aktivierung folgende Infos https://learn.microsoft.com/de-de/troubleshoot/windows-server/identity/enable-ldap-over-ssl-3rd-certification-authority und https://www.php.net/manual/de/function.ldap-start-tls.php */
/* Um die LDAPS Verbindung zu aktivieren, setzen Sie die Variable $LDAPS auf true*/
$LDAPS = false; 

/* HIER NICHTS ÄNDERN - Hier die Konfiguration von Microsoft Azure Active Directory*/
$AADLoginLink = "https://login.microsoftonline.com/common/oauth2/v2.0/authorize?client_id=4765445b-32c6-49b0-83e6-1d93765276ca&redirect_uri=https%3A%2F%2Fwww.office.com%2Flandingv2&response_type=code%20id_token&scope=openid%20profile%20https%3A%2F%2Fwww.office.com%2Fv2%2FOfficeHome.All&response_mode=form_post&nonce=638331437939432868.YWMyOGY0NTEtZWIzOC00NWViLWIzYTAtZjk5YzhlYmY2ZTM4MjA5ZGZjZWMtMTIwOS00M2U5LTg2MWMtYjMyMWEzM2Q2OWZk&ui_locales=de-DE&mkt=de-DE&client-request-id=baef7958-b9f2-43a1-8895-37c00a5494a4&state=LUKZTZm-3OJp_pn1lyXpqFcqZPBDdF-sCpjgS__iwsiEUKHeiqzoJMhGBaHLhc772mOO-WHO_pE6cWrx8Ca0sueYaiUUXfcBdGDEd7WAFt4W7iGkqyHhqyFHenWpO9bnxjX810gbEKvWfjHVSoUD5PuBf8rjW_J8Q5xw8eAjDXoHLA0SZz0sjRA2WaabrtdYnAuw2bJ25pVyZwqRQtfG6Bm73iyLNLi8h-EhWpJh-k3be-UfWwxds-uv5i0nKa_WHxqJ8M_bERXIG_RqMplOjg&x-client-SKU=ID_NET6_0&x-client-ver=6.30.1.0&sso_reload=true&login_hint=";
$AADAnwendungsClient = "";
$AADMandantID = "";

/* Ab hier sind in der Regel keine Änderungen mehr notwendig.*/
$ldapSecurityGroupAAD           = "CN=LDAP-Allow,OU=Security,".$ldapBaseDn;
$ldapSecurityGroupDomänenAdmins = "CN=Domänen-Admins,CN=Users,".$ldapBaseDn;
$ldapSecurityGroupITBeauftragte = "CN=IT-Test,OU=Security,".$ldapBaseDn;
$ldapSecurityGroupLehrer        = "CN=Lehrende-Test,OU=Security,".$ldapBaseDn;
$ldapSecurityGroupSuS           = "CN=SuS-Test,OU=Security,".$ldapBaseDn;
$ldapOUSecurityGroupClasses     = "OU=Security,".$ldapBaseDn;
