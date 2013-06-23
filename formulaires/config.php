<?php
/** Configuracion LDAP **/
//$ldapconfig['host'] = "prod.geo.gob.bo";
$ldapconfig['host'] = "192.168.7.54";
$ldapconfig['port'] = "389";
$ldapconfig['basedn'] = "ou=usuarios,dc=geo,dc=gob,dc=bo";
$ldapconfig['ldap_dn'] = "ou=grupos,dc=geo,dc=gob,dc=bo";
//$ldapconfig['filter'] = "(&(cn=GS_ADMIN))";
$ldapconfig['filter'] = "(&(cn=informaticos))";
?>
