<?php


$ldap_password = "Gomez2047";
$ldap_username = "afdb\\MRK4305";
$ldap_base="dc=afdb,dc=local";
//Base utilisateurs de l'arbre LDAP
$ldap_base_users="ou=people,".$ldap_base;
//Adresse du serveur LDAP
$ldap_server="192.168.140.42";
 //Port d'ecoute du serveur LDAP
$ldap_port="389";

//Login
$ldap_connection = ldap_connect("192.168.140.42",389) or die("Serveur ".$serveur." introuvable");
echo $ldap_connection;
if (FALSE === $ldap_connection){
    die("<p>Failed to connect to the LDAP server: ". LDAP_HOSTNAME ."</p>");
	header("location: login.php");
}

ldap_set_option($ldap_connection, LDAP_OPT_PROTOCOL_VERSION, 3) or die('Unable to set LDAP protocol version');
ldap_set_option($ldap_connection, LDAP_OPT_REFERRALS, 0); // We need this for doing an LDAP search.

if (TRUE !== ldap_bind($ldap_connection, "afdb\\MRK4305", "Gomez2047")){
   die('<p>Failed to bind to LDAP server.</p>');
	header("location: index.html");
}else{
   echo '<p>Failed to bind to LDAP server.</p>';

}




?>