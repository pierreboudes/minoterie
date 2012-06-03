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
$annee = annee_courante();


/** Modifie une entrée à partir des nouvelles données reçues dans le contexte HTTP/GET.
 */ 
function json_modify_php($annee, $readtype, $id) {
    global $user;
    global $link;
    if ($readtype == "sformation") {	
	$type = "sformation";
	$par = "annee";
    } else if ($readtype == "formation") {	
	$type = "formation";
	$par = "sformation";	
    } else if ($readtype == "cours") {	
	$type = "cours";
	$par = "formation";
    } else if ($readtype == "tranche") {
	$type = "tranche";
	$par  = "cours";
    } else if ($readtype == "enseignant") {
	$type = "enseignant";
    } else if ( ($readtype == "choix") || ($readtype == "longchoix")) {
	$type = "choix";
	$par = "cours";
    } else if ($readtype == "service") {
	$type = "service";
    } else if ($readtype == "tag") {
	$type = "tag";
    } else if ($readtype == "collection") {
	$type = "collection";
    } else {
	errmsg("type indéfini");
    }

    $champs = array(
	"sformation" => array(
	    "id_enseignant", "nom", "numero"
	    ),
	"formation" => array(
	    "id_enseignant", "nom", "annee_etude", "parfum", "numero"
	    ),
	"cours"=> array(
	    "semestre", "nom_cours", "credits", "id_enseignant",
	    "cm", "td", "tp", "alt", "descriptif", "code_geisha",
	    "debut", "fin", "inscrits", "presents", "tirage", "mcc"
	    ),
	"tranche"=> array(
	    "id_enseignant", "groupe", "cm", "td", "tp",
	    "alt", "type_conversion", "remarque", "htd", "descriptif"
	    ),
	"choix" => array(
	    "id_enseignant", "choix", "htd", "cm", "td", "tp", "alt"
	    ),
	"longchoix" => array(
	    "choix", "htd", "cm", "td", "tp", "alt"
	    ),
	"enseignant" => array(
	    "prenom", "nom", "email", "telephone", "bureau",
	    "debut", "fin", "responsabilite"
	    ),
	"tag" => array(
	    "nom_tag", "descriptif"
	    ),
	"collection" => array(
	    "nom_collection", "id_sformation", "descriptif"
	    ),
	"service" => array(
	    )
	);

    if (peuttoutfaire()) {
	$champs["enseignant"][] = "login";
	$champs["enseignant"][] = "statut";
	$champs["enseignant"][] ="service";
	$champs["enseignant"][] = "categorie";
	$champs["enseignant"][] = "su";
	$champs["enseignant"][] = "stats";    
	$champs["service"][] = "annee_universitaire";
	$champs["service"][] = "categorie";
	$champs["service"][] = "service_annuel";
    }


    if (!peutediter($type,$id,NULL)) {
	if (($type == "cours") && peutmajcours($id)) {
	    /* on restreint l'edition */
	    $champs["cours"] = array(
		"debut", "fin", "inscrits", "presents", "tirage", "mcc"
		);
	} else {
	    errmsg("droits insuffisants.");
	}
    }
    $set = array();
    foreach ($champs[$type] as $field) {
	if (isset($_GET[$field])) {
	    $set[$field] = getclean($field);
	}
    };
    /* Champs particuliers a controler */
    if (isset($set["nom_cours"]) && ("" == trim($set["nom_cours"]))) {
	errmsg("donner un nom au cours.");
    }
    /* semestre inutile? */
    /* ancienne entree */
    if ($type == "cours") {
	$old = selectionner_cours($id);
    }
    if ($type == "tranche") {
	$old = selectionner_tranche($id);
    }
    if ($type == "choix") {
	$old = selectionner_choix($id);
    }
    if (($type == "collection") && (isset($set["id_sformation"]))) {
	$q = "SELECT annee_universitaire FROM pain_sformation WHERE id_sformation = ".$set["id_sformation"];
	$r = $link->query($q);
	if (!$r) {
	    errmsg("erreur avec la requete :\n".$q."\n".$link->error);
	}
	$rr = $r->fetch_assoc();
	$set["annee_universitaire"] = $rr["annee_universitaire"];
    }
    /* calcul de l'équivalent TD, nul admis */
    if (($type == "tranche") || ($type == "choix")) {
	$cm = isset($set["cm"])?$set["cm"]:$old["cm"];
	if ($cm < 0) errmsg("CM doit être positif.");
	$td = isset($set["td"])?$set["td"]:$old["td"];
	if ($td < 0) errmsg("TD doit être positif.");
	$tp = isset($set["tp"])?$set["tp"]:$old["tp"];
	if ($tp < 0) errmsg("TP doit être positif.");
	$alt = isset($set["alt"])?$set["alt"]:$old["alt"];
	if ($alt < 0) errmsg("alt doit être positif.");
	$set["htd"] = 1.5 * $cm + $td + $tp + $alt;
	if ($set["htd"] < 0) {
	    errmsg("le total des heures ne peut pas être négatif");
	}
    }    

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
	$query = "UPDATE pain_${type} ".
	    "SET $strset, modification = NOW() ".
	    "WHERE `id_$type`=".$id;

	if ($type == "service") {
	    list($id_enseignant,$an) = explode('X', $id);

	    $query = "UPDATE pain_service SET $strset ". 
		"WHERE id_enseignant = $id_enseignant AND annee_universitaire = $an";
	}
    
	/* log et requete a moderniser (loguer le json) TODO */

	if (!$link->query($query)) {
	    errmsg("erreur avec la requete :\n".$query."\n".$link->error);
	}
	pain_log($query); // LOG DE LA REQUETE !
	if ($type == "cours") {
	    $coursnew = selectionner_cours($id);
	    historique_par_cmp(1, $old, $coursnew);
	}
	if ($type == "tranche") {
	    $tranchenew = selectionner_tranche($id);
	    historique_par_cmp(2, $old, $tranchenew);	        
	}
	if ($type == "choix") {
	    $choixnew = selectionner_choix($id);
	    historique_par_cmp(3, $old, $choixnew);	        
	}   
    }
} 

if (isset($_GET["type"])) {
    $readtype = getclean("type");
} else {
    errmsg('erreur du script (type manquant).');
}

if (isset($_GET["id"])) {
    $id = getnumeric("id");

    json_modify_php($annee, $readtype, $id);

    /* affichage de la nouvelle entree en json */
    unset($_GET["id_parent"]);
    include("json_get.php");
}
?>