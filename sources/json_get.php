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
$annee = default_year();

require_once("inc_connect.php");
require_once("utils.php");
require_once("inc_functions.php"); // pour update_servicesreels($id_par);

/**
retourne des données de type $readtype, prises dans la base, sélectionnées par le contexte d'une requête HTTP/POST ou GET.

Les données sont éventuellement calculées par jointures et aggrégats. La sélection dépend soit de l'identifiant de l'entrée fourni par le contexte d'une requête HTTP/POST ou GET, ou bien d'un identifiant de groupe d'entrées ou bien de l'année courante. 
 */
function json_get_php($annee, $readtype) {
    global $link;
    if ($readtype == "utilisateur") {
	$type = "utilisateur";
	$npar = array();
	$order = " ORDER BY nom, prenom ASC";
    } else if ($readtype == "departement") {
	$type = "departement";
	$npar = array();
	$order = " ORDER BY nom_departement ASC";
    } else if ($readtype == "minot") {
	$type = "minot";
	$npar = array("id_enseignant","id_departement");
	$order = " ORDER BY modification DESC";
    } else if ($readtype == "intervention") {
	$type = "intervention";
	$par = "id_minot";
	$order = " ORDER BY id_intervention ASC";
    } else if ($readtype == "annotation") {
	$type = "annotation";
	$par  = "id_minot";
	$order = " ORDER by modification DESC";
    } else if ($readtype == "declaration") {
	$type = "declaration";
	$par = "id_departement";
	$requete = "SELECT 
                    \"$type\" as type,
                    id_enseignant,
                    u.nom,
                    u.prenom,
                    u.id_minot,
                    u.id_minot as id,
                    u.id_minot as id_declaration,
                    u.modification as modification_minot,
                    traitee,
                    w.id_annotation,
                    w.modification as modification_annotation
                    FROM  ((SELECT id_enseignant, max(modification) as modification
                                   FROM minoterie_minot  GROUP BY id_enseignant) as t 
                           NATURAL JOIN (minoterie_minot as u))
                      LEFT JOIN 
                          ((SELECT id_minot, max(modification) as modification 
                                   FROM minoterie_annotation GROUP BY id_minot) as v
                           NATURAL JOIN (minoterie_annotation as w))
                    ON u.id_minot = v.id_minot 
                    WHERE 1 ";
	if (isset($_GET["id_parent"]) || isset($_POST["id_parent"])) {
	    $id_par = getnumeric("id_parent");
	    if (NULL == $id_par) {
		errmsg("$par absent de la requete ou non numerique ($readtype)");
	    }
	    $requete .= " AND id_departement = $id_par ";
	} else 	if (isset($_GET["id"]) || isset($_POST["id"])) {
	    $id = getnumeric("id");
	    if (NULL == $id) {
		errmsg("id absent de la requete ou non numerique ($readtype)");
	    }
	    $requete .= " AND u.id_minot = $id ";
	}
    } else {
	errmsg("erreur de script (type inconnu)");
    }

   if (isset($_GET["id_parent"]) || isset($_POST["id_parent"])) {
       if (isset($npar)) {
	   $filtre = " ";
	   foreach($npar as $colonne) {
	       $id_par = getnumeric($colonne);
	       if (NULL == $id_par) {
		   errmsg("$colonne absent de la requete ou non numerique ($readtype)");
	       }
	       $filtre .= " AND `$colonne` = $id_par ";
	   }
       } else {
	   $id_par = getnumeric("id_parent");
	   if (NULL == $id_par) {
	       errmsg("$champs absent de la requete ou non numerique ($readtype)");
	   }
	   $filtre = " AND `$par` = $id_par ";
       }
       if (!isset($requete)) {
	   $requete = "SELECT 
                      minoterie_$type.*,
                       \"$type\" AS type, 
                      id_$type AS id
             FROM minoterie_$type 
             WHERE 1 ";
	   $requete .= $filtre.$order;
       }
       $resultat = $link->query($requete) 
	   or errmsg("Échec de la requête sur la table $type".$requete."\n".$link->error);
       $arr = array();
       while ($element = $resultat->fetch_object()) {
	   $arr[] = $element;
       }
       return $arr;
   } else if (isset($_GET["id"]) || isset($_POST["id"])) {
       $id = getnumeric("id");
       if (!isset($requete)) {
	   $requete = "SELECT \"$type\" AS type,
                      $id AS id,
                      minoterie_$type.*
             FROM minoterie_$type 
             WHERE `id_$type` = $id ";
       }
       $resultat = $link->query($requete) 
	   or errmsg("Échec de la requête sur la table $type".$requete."\n".$link->error);
       $arr = array();
       while ($element = $resultat->fetch_object()) {
	   $arr[] = $element;
       }
       return $arr;
   } else {
       errmsg("Erreur de script client (ni id ni parent)");
   }
}


if (isset($_GET["type"]) || isset($_POST["type"])) {
    $readtype = getclean("type");
    $out = json_get_php($annee, $readtype);
    print json_encode($out);
} else {
    errmsg("erreur de script (type non renseigné)");
}

?>