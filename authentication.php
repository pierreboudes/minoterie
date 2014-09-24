<?php /* -*- coding: utf-8 -*-*/
/* Minoterie - outil de gestion des services d'enseignement
 *
 * Copyright 2009-2012 Pierre Boudes,
 * département d'informatique de l'institut Galilée.
 *
 * This file is part of Minoterie.
 *
 * Minoterie is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Minoterie is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
 * or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public
 * License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Minoterie.  If not, see <http://www.gnu.org/licenses/>.
 */
error_reporting(E_ALL);
require_once('CAS.php');
// error_reporting(E_ALL & ~E_NOTICE);
phpCAS::client(CAS_VERSION_2_0,'cas.univ-paris13.fr',443,'/cas/',true);
// phpCAS::setDebug();
phpCAS::setNoCasServerValidation();

require_once('inc_connect.php');
require_once('inc_config.php');

date_default_timezone_set('Europe/Paris'); /* pour strtotime() */

/**  changement d'annee dans la table de configuration  */
function default_year() {
    $an = intval(get_configuration("DATE_ETAPE"));
    if ($an <= 0) {
      $an = date('Y', strtotime('-8 month'));
    }
    return $an;
}

/** retourne l'utilisateur enregistré correspondant au login CAS et le cas échéant les départements qu'il administre */
function minoterie_getuser() {
    global $link;
    $login = phpCAS::getUser();
    $query = "SELECT id_utilisateur, login, su, nom, prenom, minoterie_departement.*
                 FROM minoterie_utilisateur LEFT JOIN minoterie_departement
                 ON minoterie_utilisateur.id_departement = minoterie_departement.id_departement
                 WHERE login LIKE '$login'";
    $result = $link->query($query) or die("Échec de la requête ".$query."\n".$link->error);
    if ($listuser = $result->fetch_array()) {
	$user = Array("id_utilisateur" => $listuser["id_utilisateur"],
		      "login" => $listuser["login"],
		      "su" => $listuser["su"],
		      "nom" => $listuser["nom"],
		      "prenom" => $listuser["prenom"],
		      "departements" => Array(Array(
						 "id_departement" => $listuser["id_departement"],
						 "nom_departement" => $listuser["nom_departement"],
						 "url_pain" => $listuser["url_pain"]
						 ))
	    );
	while ($listuser = $result->fetch_array()) {
	    $user["departements"][] = Array(
		"id_departement" => $listuser["id_departement"],
		"nom_departement" => $listuser["nom_departement"],
		"url_pain" => $listuser["url_pain"]
		);
	}
	return $user;
    } else {
	return NULL;
    }
}
/** retourne la liste des departements dans lesquels l'utlisateur CAS a une declaration */
function minoterie_getens() {
    global $link;
    $login = phpCAS::getUser();
    $query = "SELECT login, id_enseignant, u.id_minot, t.modification AS
    modification_minot, traitee, definitif, minoterie_departement . *
    , nom, prenom, derniere_signature FROM (( ( SELECT id_enseignant,
    id_departement, max( modification ) AS modification FROM
    minoterie_minot WHERE login LIKE '$login' GROUP BY id_enseignant,
    id_departement ) AS t NATURAL JOIN ( minoterie_minot AS u ) ) JOIN
    minoterie_departement ON u.id_departement =
    minoterie_departement.id_departement ) LEFT JOIN ( SELECT
    minoterie_signature.id_minot, max(modification) AS
    derniere_signature FROM minoterie_signature GROUP BY id_minot ) AS
    s ON u.id_minot = s.id_minot";
    $result = $link->query($query)  or die("Échec de la requête ".$query."\n".$link->error);
    $ens = array();
    while ($dep = $result->fetch_assoc()) {
	$ens[] = $dep;
    }
    return $ens;
}

function authentication() {
    phpCAS::forceAuthentication();
    $user = minoterie_getuser();
    if (NULL == $user) {
	$login = phpCAS::getUser();
	die("D&eacute;sol&eacute; votre login ($login) n'est pas enregistr&eacute; dans la minoterie. Pour sortir c'est par ici : <a href='logout.php'>logout</a>.");
    }
    return $user;
}

function weak_auth() {
    phpCAS::forceAuthentication();
    return minoterie_getuser();
}

function weak_auth_login() {
    phpCAS::forceAuthentication();
    return  phpCAS::getUser();
}

function authrequired() {
    if (!(phpCAS::isAuthenticated())) {
	header("Location: http://perdu.com");
	die('Die in terror, picnic boy');
    }
}

function minoterie_logout() {
    phpCAS::logout();
}
?>
