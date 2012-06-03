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
require_once('authentication.php'); 
$user = authentication();

require_once("inc_connect.php");
require_once("utils.php");
require_once("inc_functions.php");
require_once("inc_functions_rm.php");

function json_rm_php($readtype, $id) {
    global $link;
    if ($readtype == "utilisateur") {
	$type = "utilisateur";
    } else if ($readtype == "departement") {
	$type = "departement";
    } else if ($readtype == "annotation") {
	$type = "annotation";
    } else {
	errmsg("erreur de script (type inconnu)");
    }
    if (!peutediter($type,$id,NULL)) {
	errmsg("droits insuffisants.");
    }
    $q = "DELETE FROM minoterie_$type WHERE `id_$type` = $id LIMIT 1";
    minoterie_log("-- supprimer($type, $id)");
    if ($link->query($q)) {
	echo '{"ok": "ok"}';
		minoterie_log("$q");
    } else {
	errmsg("échec de la requête sur la table $type.");
    }
}

if (NULL == ($id = getnumeric("id"))) {    
    errmsg("erreur de script (id non renseigné)");
}

if (NULL == ($readtype = getclean("type"))) {
    errmsg("erreur de script (type non renseigné)");
}

json_rm_php($readtype, $id);
?>