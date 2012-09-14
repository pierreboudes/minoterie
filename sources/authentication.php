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

date_default_timezone_set('Europe/Paris'); /* pour strtotime() */

/**  changement d'annee le 1er septembre  */
function default_year() {
    $an = date('Y', strtotime('-8 month'));
    return $an;    
}


/** retourne l'utilisateur enregistré correspondant au login CASet le cas échéant son département */
function minoterie_getuser() {
    global $link;
    $login = phpCAS::getUser();
    $query = "SELECT id_utilisateur, login, su, nom, prenom, minoterie_departement.*
                 FROM minoterie_utilisateur LEFT JOIN minoterie_departement 
                 ON minoterie_utilisateur.id_departement = minoterie_departement.id_departement 
                 WHERE login LIKE '$login' LIMIT 1";
    $result = $link->query($query) or die("Échec de la requête ".$query."\n".$link->error);
    if ($user = $result->fetch_array()) {
	return $user;
    } else {
	return NULL;
    }
}
/** retourne la liste des departements dans lesquels l'utlisateur CAS a une declaration */
function minoterie_getens() {
    global $link;
    $login = phpCAS::getUser();
    $query = "SELECT login, id_enseignant, id_minot, t.modification as modification_minot, traitee, 
                      minoterie_departement.*, nom, prenom
                 FROM ((SELECT id_enseignant, max(modification) as modification 
                                   FROM minoterie_minot WHERE login LIKE '$login'  GROUP BY id_enseignant) as t 
                           NATURAL JOIN (minoterie_minot as u)) JOIN minoterie_departement 
                 ON u.id_departement = minoterie_departement.id_departement";
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
