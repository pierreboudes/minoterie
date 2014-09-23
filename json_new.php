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


/** réalise l'insertion d'un nouvel élément fourni par le contexte HTTP/GET
    et renvoie son id.
 */
function json_new_php() {
    global $link;
    global $user;
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
        "signature" => array(
            "id_minot", "nom", "prenom", "login"
        )
	);

if (NULL != ($readtype = getclean("type"))) {
    if ($readtype == "utilisateur") {
        $type = "utilisateur";
    } else if ($readtype == "departement") {
        $type = "departement";
    } else if ($readtype == "annotation") {
        $type = "annotation";
        $par = "id_minot";
    } else if ($readtype == "signature") {
        $type = "signature";
        $par = "id_minot";
    } else {
	errmsg("type indéfini");
    }
} else {
    errmsg('erreur du script (type manquant).');
}

if (NULL != ($id_parent = getnumeric("id_parent"))) {

    if (!peutediter($type,NULL,$id_parent)) {
	errmsg("droits insuffisants.");
    }
    $set = array();
    if (isset($par))  {
	$set[$par] = $id_parent;
    }

    foreach ($champs[$type] as $field) {
	if (NULL != ($val = getclean($field))) {
	    $set[$field] = $val;
	}
    };

    /* formation de la requete */
    $setsql = array();
    foreach ($set as $field => $val) {
	$setsql[] = '`'.$field.'` = "'.$val.'"';
    };
    $strset = implode(", ", $setsql);
    $query = "INSERT INTO minoterie_${type} SET $strset, modification = NOW()";

    /* execution de la requete */

    if (!$link->query($query)) {
	errmsg("erreur avec la requete :\n".$query."\n".$link->error);
    }

    $id = $link->insert_id;
    minoterie_log($query." -- insert_id = $id");
    /* logs */

    /* mises à jour annexes */

    return $id;
}
}

$id = json_new_php();

if (NULL == $id) {
    echo '{"ok": "ok"}';
} else {
    /* affichage de la nouvelle entree en json */
    $_GET["id"] = $id;
    unset($_GET["id_parent"]);
    unset($_POST["id_parent"]);
    include("json_get.php");
}
?>