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
$user =  weak_auth();
require_once("inc_connect.php");
require_once("utils.php");
require_once("inc_functions.php");


/** Modifie une entrée à partir des nouvelles données reçues dans le contexte HTTP/GET ou POST.
 */
function json_modify_php($readtype, $id) {
    global $link;
    if ($readtype == "utilisateur") {
	$type = "utilisateur";
    } else if ($readtype == "departement") {
	$type = "departement";
    } else if ($readtype == "annotation") {
	$type = "annotation";
    } else if (($readtype == "declaration") || ($readtype == "declens")) {
	$type = "minot";
    } else {
	errmsg("erreur de script (type inconnu)");
    }
    if (!peutediter($type, $id, NULL)) {
	errmsg("droits insuffisants.");
    }

    $champs = array(
        "utilisateur" => array(
            "login", "id_departement", "su"
        ),
        "departement" => array(
            "nom_departement", "url_pain"
        ),
        "annotation" => array(
            "id_minot", "jsannot", "commentaire", "complete"
        ),
        "minot" => array(
            "traitee"
        )
    );

    if (!peutediter($type,$id,NULL)) {
	    errmsg("droits insuffisants.");
    }

    $set = array();

    foreach ($champs[$type] as $field) {
	if (NULL != ($val = getclean($field))) {
	    $set[$field] = $val;
	}
    };

    /* formation de la requete */
    $setsql = array();
    foreach ($set as $field => $val) {
	if ("null" != $val) {
	    $setsql[] = '`'.$field.'` = "'.$val.'"';
	} else {
	    $setsql[] = '`'.$field.'` = NULL';
	}
    };
    $strset = implode(", ", $setsql);

    if ($strset != "") { /* il y a de vraies modifs */
	$query = "UPDATE minoterie_${type} ".
	    "SET $strset, modification = NOW() ".
	    "WHERE `id_$type`=".$id;

	if (!$link->query($query)) {
	    errmsg("erreur avec la requete :\n".$query."\n".$link->error);
	}
	minoterie_log($query);
    }

    /* affichage de la nouvelle entree en json */
    unset($_GET["id_parent"]);
    unset($_POST["id_parent"]);
    include("json_get.php");
}

if (NULL == ($readtype = getclean("type"))) {
    errmsg('erreur du script (type manquant).');
}

if (NULL != ($id = getnumeric("id"))) {
    json_modify_php($readtype, $id);
} else {
    errmsg('erreur du script (id manquant).');
}
?>