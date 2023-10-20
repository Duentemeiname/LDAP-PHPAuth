<?php

function checkLDAPInjektion($string)
{
    return ldap_escape($string, "", LDAP_ESCAPE_FILTER);
}