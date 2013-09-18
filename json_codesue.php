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
$user = weak_auth();

require_once("inc_connect.php");

function lister_codesue()
{
    global $link;
    $qcat = "SELECT code_ue AS `id`, 
                   CONCAT(code_ue, ' ',TRIM(intitule_cours), ' — ', TRIM(nom_annee)) AS `label`
             FROM codesue 
             WHERE `intitule_cours` LIKE '%".$_GET["term"]."%'
             OR `nom_annee` LIKE '%".$_GET["term"]."%'
             OR `code_ue` LIKE '%".$_GET["term"]."%'
	     ORDER BY nom_annee, intitule_cours  ASC";
    $rcat = $link->query($qcat) 
	or die("Échec de la requête sur la table codesue: $qens mysql a repondu: ".$link->error);
    return $rcat;
}


/**
 */
function json_codesue_php() {
    $rens = lister_codesue();
    $arr = array();
    while ($ens = $rens->fetch_object()) {
	$arr[] = $ens;
    }
    return $arr;
}


if (isset($_GET["term"])) {
    $arr = json_codesue_php();
    print json_encode($arr);
}
?>