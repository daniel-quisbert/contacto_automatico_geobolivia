<?php
/*
 *	Autor: Daniel Quisbert
 */
//include ("config.php");
/** Devuelve la conexion al servidor LDAP**/
function conectLdap($ldapconfig) {
	$ldap_conn = ldap_connect($ldapconfig['host'], $ldapconfig['port']);
	ldap_set_option($ldap_conn, LDAP_OPT_PROTOCOL_VERSION, 3);
	if ($ldap_conn)
		return $ldap_conn;
	else
		return FALSE;
}

/**Devuelve TRUE si el usuario existe**/
/*
 * Retorna 1 si sólo el usuario ya Existe
 * Retorna 2 si sólo el correo ya existe
 * Retorna 3 si los dos ya existen
 * retorna 0 si no existe ninguno
 * */
function verifica($ldapconfig, $infoUser) {
	$con = conectLdap($ldapconfig);
	$resp = 0;
	if ($con) {
		$search1 = ldap_search($con, trim($ldapconfig['ldap_dn']), "(&(uid=" . $infoUser['usuario'] . "))") or exit("Unable to search LDAP server");
		$search2 = ldap_search($con, trim($ldapconfig['ldap_dn']), "(&(mail=" . $infoUser['email'] . "))") or exit("Unable to search LDAP server");
		$entries_user = ldap_count_entries($con, $search1);
		$entries_mail = ldap_count_entries($con, $search2);

		if ($entries_user == 1) {
			if ($entries_mail == 1)
				$resp = 3;
			else
				$resp = 1;
		}
		else {
			if ($entries_mail == 1)
				$resp = 2;			
		}

	} else {
		echo "No se pueder conectar al servidor LDAP " . $ldapconfig['host'];
	}
	ldap_close($con);
	return $resp;
}

function addUser($ldapconfig, $_POST) {
	$con = conectLdap($ldapconfig);
	if ($con) {
		$auth = ldap_bind($con, "cn=admin,dc=geo,dc=gob,dc=bo", "root");
	}
}

function formulaires_contacto_automatico_geobolivia_charger($adresse, $url = '', $sujet = '') {
	include_spip('inc/texte');

	$valeurs = array('sujet_message' => $sujet, 'nom_message' => '',
	//	nuevos campos para el formulario de registro
	'usuario_message' => '', 'telefono_message' => '', 'grado_message' => '', 'pais_message' => '', 'depprovest_message' => '', 'institucion_message' => '', 'nominstitucion_message' => '', 'ipvisita_message' => 'IP: ' . $_SERVER['REMOTE_ADDR'], 'ipvisitaProxy_message' => 'IP: ' . $_SERVER['HTTP_X_FORWARDED_FOR'],
	//	Fin nuevos campos
	'categorie_message' => '', 'texte_message' => '', 'email_message' => $GLOBALS['visiteur_session']['email']);

	// id du formulaire (pour en avoir plusieurs sur une meme page)
	$valeurs['id'] = rand(1, 100);
	return $valeurs;
}

function formulaires_contacto_automatico_geobolivia_verifier_dist($adresse, $url = '', $sujet = '') {
	$erreurs = array();
	include_spip('inc/filtres');
	include_spip('inc/texte');

	if (!$adres = _request('email_message'))
		$erreurs['email_message'] = _T("info_obligatoire");
	elseif (!email_valide($adres))
		$erreurs['email_message'] = _T('contacto_automatico_geobolivia:form_prop_email_incorrecto_automatico_geobolivia');

	if ("- - - - - - - - - -" == $grado = _request('pais_message'))
		$erreurs['pais_message'] = _T("info_obligatoire");

	if (!$nom = _request('nom_message'))
		$erreurs['nom_message'] = _T("info_obligatoire");
	elseif (!(strlen($nom) > 2))
		$erreurs['nom_message'] = _T('contacto_automatico_geobolivia:forum_attention_trois_caracteres', '', array('force' => false)) ? _T('contacto_automatico_geobolivia:forum_attention_trois_caracteres') : _T('forum:forum_attention_trois_caracteres');

	$telefono = _request('telefono_message');
	if ((strlen($telefono)) > 0) {
		if (!(strlen($telefono) > 7))
			$erreurs['telefono_message'] = _T('contacto_automatico_geobolivia:forum_attention_telefono_caracteres', '', array('force' => false)) ? _T('contacto_automatico_geobolivia:forum_attention_telefono_caracteres') : _T('forum:forum_attention_telefono_caracteres');
	}
	if ("- - - - - - - - - -" == $grado = _request('grado_message'))
		$erreurs['grado_message'] = _T("info_obligatoire");

	if (!$dpe = _request('depprovest_message'))
		$erreurs['depprovest_message'] = _T("info_obligatoire");
	elseif (!(strlen($dpe) > 2))
		$erreurs['depprovest_message'] = _T('contacto_automatico_geobolivia:forum_attention_trois_caracteres', '', array('force' => false)) ? _T('contacto_automatico_geobolivia:forum_attention_trois_caracteres') : _T('forum:forum_attention_trois_caracteres');

	if ("- - - - - - - - - -" == $tipoinst = _request('institucion_message'))
		$erreurs['institucion_message'] = _T("info_obligatoire");

	if (!$nominst = _request('nominstitucion_message'))
		$erreurs['nominstitucion_message'] = _T("info_obligatoire");
	elseif (!(strlen($dpe) > 2))
		$erreurs['nominstitucion_message'] = _T('contacto_automatico_geobolivia:forum_attention_trois_caracteres', '', array('force' => false)) ? _T('contacto_automatico_geobolivia:forum_attention_trois_caracteres') : _T('forum:forum_attention_trois_caracteres');

	if (_request("nobot"))
		$erreurs['nobot'] = true;

	if (!_request('confirmer') AND !count($erreurs))
		$erreurs['previsu'] = ' ';
	return $erreurs;
}

function formulaires_contacto_automatico_geobolivia_traiter_dist($adresse, $url = '', $sujet = '') {
	$adres = _request('email_message');
	$nom = _request('nom_message');
	// nuevos atributos para el registro de usuarios
	$telefono = _request('telefono_message');
	$usuario = _request('usuario_message');
	$grado = _request('grado_message');
	$pais = _request('pais_message');
	$dpe = _request('depprovest_message');
	$tipoinst = _request('institucion_message');
	$nominst = _request('nominstitucion_message');

	$ipvisita = _request('ipvisita_message');
	$ipvisitaProxy = _request('ipvisitaProxy_message');
	//--------------------------------------------

	$datos_registro = $datos_registro . "Teléfono: " . $telefono . "\nNivel Académico: " . $grado . "\nPaís: " . $pais . "\nDep-Prov-Est: " . $dpe . "\nTipo de Institución: " . $tipoinst . "\nNombre Institución: " . $nominst;

	$sujet = _request('sujet_message');
	$categorie = _request('categorie_message');
	$texte = _request('texte_message');

	$texte = _T('contacto_automatico_geobolivia:mail_asunto') . $sujet . "\n\n" . $texte;
	$texte = _T('contacto_automatico_geobolivia:mail_categoria') . $categorie . "\n" . $texte;
	$texte = "\n" . $datos_registro . "\n" . $texte;

	if ($nom)
		$texte = _T('contacto_automatico_geobolivia:mail_nombre') . $nom . "\n" . $texte;
	$texte .= "\n\n-- " . _T('envoi_via_le_site') . " " . supprimer_tags(extraire_multi($GLOBALS['meta']['nom_site'])) . " (" . $GLOBALS['meta']['adresse_site'] . "/) --\n";
	if ($url)
		$texte .= "\n\n-- " . _T('contacto_automatico_geobolivia:desde_pagina') . supprimer_tags($url) . " --\n";

	if ($ipvisitaProxy != '')
		$texte = $texte . "\n" . $ipvisitaProxy;
	else
		$texte = $texte . "\n" . $ipvisita;

	$sujet = _T('contacto_automatico_geobolivia:mail_prefijo_asunto');

	$envoyer_mail = charger_fonction('envoyer_mail', 'inc');

	$envoyer_mail($adresse, $sujet, $texte, $adres, $nom, "X-Originating-IP: " . $GLOBALS['ip']);
	$message = _T("form_prop_message_envoye");

	return array('message_ok' => $message);
}
?>
