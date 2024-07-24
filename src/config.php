<?php

/* Hier die Konfiguration des jeweiligen Servers ändern*/
$ldapServer = $_ENV['ldapServer']; 
$ldapPort = $_ENV['ldapPort']; 
$ldapDomainName = $_ENV['ldapDomainName']; 
$ldapBaseDn = $_ENV['ldapBaseDn']; 
$ldapNutzername = $_ENV['ldapNutzername'];                          
$ldapPasswort = $_ENV['ldapPasswort'];
$GlobalServerUrl = $_SERVER['HTTP_HOST']; //WICHTIG: Mit Slash am Ende!


/*LDAPS Verbindung über TLS*/
/*Bitte beachten Sie vor der Aktivierung folgende Infos https://learn.microsoft.com/de-de/troubleshoot/windows-server/identity/enable-ldap-over-ssl-3rd-certification-authority und https://www.php.net/manual/de/function.ldap-start-tls.php */
/* Um die LDAPS Verbindung zu aktivieren, setzen Sie die Variable $LDAPS auf true*/
$LDAPS = false; 

/* HIER NICHTS ÄNDERN - Hier die Konfiguration von Microsoft Azure Active Directory*/
$AADLoginLink = "https://login.microsoftonline.com/common/oauth2/v2.0/authorize?client_id=4765445b-32c6-49b0-83e6-1d93765276ca&redirect_uri=https%3A%2F%2Fwww.office.com%2Flandingv2&response_type=code%20id_token&scope=openid%20profile%20https%3A%2F%2Fwww.office.com%2Fv2%2FOfficeHome.All&response_mode=form_post&nonce=638331437939432868.YWMyOGY0NTEtZWIzOC00NWViLWIzYTAtZjk5YzhlYmY2ZTM4MjA5ZGZjZWMtMTIwOS00M2U5LTg2MWMtYjMyMWEzM2Q2OWZk&ui_locales=de-DE&mkt=de-DE&client-request-id=baef7958-b9f2-43a1-8895-37c00a5494a4&state=LUKZTZm-3OJp_pn1lyXpqFcqZPBDdF-sCpjgS__iwsiEUKHeiqzoJMhGBaHLhc772mOO-WHO_pE6cWrx8Ca0sueYaiUUXfcBdGDEd7WAFt4W7iGkqyHhqyFHenWpO9bnxjX810gbEKvWfjHVSoUD5PuBf8rjW_J8Q5xw8eAjDXoHLA0SZz0sjRA2WaabrtdYnAuw2bJ25pVyZwqRQtfG6Bm73iyLNLi8h-EhWpJh-k3be-UfWwxds-uv5i0nKa_WHxqJ8M_bERXIG_RqMplOjg&x-client-SKU=ID_NET6_0&x-client-ver=6.30.1.0&sso_reload=true&login_hint=";
$AADAnwendungsClient = "";
$AADMandantID = "";

/* Ab hier sind in der Regel keine Änderungen mehr notwendig.*/
$ldapSecurityGroupAAD           = "CN=grpAzureSyncSuS,OU=Security,OU=Group,OU=MTKADMIN,".$ldapBaseDn; //CN=grpAzureSyncSuS,OU=Security,OU=Group,OU=MTKADMIN,DC=bws,DC=mtk,DC=schule
$ldapSecurityGroupDomänenAdmins = "CN=Domänen-Admins,CN=Users,".$ldapBaseDn;
$ldapSecurityGroupITBeauftragte = "CN=IT-Beauftragte,OU=Rollen,OU=Gruppen,".$ldapBaseDn;   //CN=IT-Beauftragte,OU=Rollen,OU=Benutzer,OU=schule,DC=bws,DC=mtk,DC=schule
$ldapSecurityGroupLehrer        = "CN=Alle Lehrer,OU=Benutzer,OU=schule,".$ldapBaseDn; //CN=Alle Lehrer,OU=Benutzer,OU=schule,DC=bws,DC=mtk,DC=schule
$ldapSecurityGroupSuS           = "CN=Alle Schüler,OU=Benutzer,OU=schule,".$ldapBaseDn; //CN=Alle Schüler,OU=Benutzer,OU=schule,DC=bws,DC=mtk,DC=schule
$ldapOUSecurityGroupClasses     = "OU=Klassen,OU=Gruppen,OU=schule,".$ldapBaseDn; //OU=Klassen,OU=Gruppen,OU=schule,DC=bws,DC=mtk,DC=schule
$ldapUserLoginAllowed           = "OU=Benutzer,OU=schule,".$ldapBaseDn;
