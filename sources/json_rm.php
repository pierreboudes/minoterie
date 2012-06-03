<?php /* -*- coding: utf-8 -*-*/
/* Pain - outil de gestion des services d'enseignement        
 *
 * Copyright 2009-2012 Pierre Boudes,
 * département d'informatique de l'institut Galilée.
 *
 * This file is part of Pain.
 *
 * Pain is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Pain is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
 * or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public
 * License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Pain.  If not, see <http://www.gnu.org/licenses/>.
 */
require_once('authentication.php'); 
$user = authentication();

require_once("inc_connect.php");
require_once("utils.php");
require_once("inc_functions.php");
require_once("inc_functions_rm.php");

function json_rm_php($id, $readtype) {
    if ($readtype == "sformation") {
	supprimer_sformation($id);
    } else if ($readtype == "formation") {
	supprimer_formation($id);
    } else if ($readtype == "cours") {
	supprimer_cours($id);
    } else if (($readtype == "tranche") || ($readtype == "longtranche")) {
	supprimer_tranche($id);
    } else if ($readtype == "enseignant") {
	supprimer_enseignant($id);
    } else if ( ($readtype == "choix") || ($readtype == "longchoix")) {
	supprimer_choix($id);
    } else if ($readtype == "service") {
	list($id_ens,$an) = explode('X',$id);
	supprimer_service($id_ens, $an);
    } else if ($readtype == "tag") {
	supprimer_tag($id);
    } else if ($readtype == "tagcours") {
	$id_par = getnumeric("id_parent");
	supprimer_tagcours($id, $id_par);
    } else if ($readtype == "collection") {
	supprimer_collection($id);
    } else if ($readtype == "collectioncours") {
	$id_par = getnumeric("id_parent");
	supprimer_collectioncours($id, $id_par);
    } else {
	errmsg("erreur de script (type inconnu)");
    }
}

if (isset($_GET["id"])) {
    $id = getnumeric("id");
} else {
    errmsg("erreur de script (id non renseigné)");
}

if (isset($_GET["type"])) {
    $readtype = getclean("type");
} else {
    errmsg("erreur de script (type non renseigné)");
}

json_rm_php($id, $readtype);
?>