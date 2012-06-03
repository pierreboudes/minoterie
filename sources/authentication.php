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
require_once('CAS.php');
// error_reporting(E_ALL & ~E_NOTICE);
phpCAS::client(CAS_VERSION_2_0,'cas.univ-paris13.fr',443,'/cas/',true);
// phpCAS::setDebug();
phpCAS::setNoCasServerValidation();

require_once('inc_connect.php');

function minoterie_getuser() {
    global $link;
    $login = phpCAS::getUser();
    $query = "SELECT id_utilisateur, login, id_departement, su 
                 FROM minoterie_enseignant 
                 WHERE login LIKE '$login' LIMIT 1";
    $result = $link->query($query);
    if ($user = $result->fetch_array()) {
	return $user;
    } else {
	return NULL;
    }
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
