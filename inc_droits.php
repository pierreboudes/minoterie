<?php /* -*- coding: utf-8 -*- */
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

/* gestion des droits (temporaire) */
require_once('authentication.php');
authrequired();
$user = weak_auth();
$login = phpCAS::getUser();

function peuttoutfaire() {
    global $user;
    if ($user["su"]) return true;
    return false;
}

function peutimporterdeclarations($id_departement) {
    global $user;
    return peuttoutfaire()
	|| ($id_departement == $user["id_departement"]);
}

function peutediter($type, $id, $id_parent) {
    if ($id != NULL) {
	if ($type == "annotation") return peutediterannotation($id, NULL);
	if ($type == "minot") return peutediterminot($id);
	if ($type == "cours") return peuteditercours($id);
	if ($type == "tranche") return peuteditertranche($id);
	if ($type == "enseignant") return peutediterenseignant($id);
	if ($type == "service") return peutediterservice($id);
	if ($type == "choix") return peutediterchoix($id);
	if ($type == "tag") return peuteditertag($id);
	if ($type == "collection") return peuteditercollection($id);
    }
    if ($id_parent != NULL) {
	if ($type == "annotation") return peutediterannotation(NULL,$id_parent);
	if ($type == "formation") return peutediterformationdelasformation($id_parent);
	if ($type == "cours") return peuteditercoursdelaformation($id_parent);
	if ($type == "tranche") return peuteditertrancheducours($id_parent);
	if ($type == "service") return peutediterservicedeenseignant($id_parent);
	if ($type == "choix") return peutchoisir();
	if ($type == "tag") return peuteditertag($id);
	if ($type == "tagscours") return peuteditercours($id_parent);
	if ($type == "collection") return peuteditercollection($id);
	if ($type == "collectionscours") return peuteditercours($id_parent);
    }
    if ($type == "enseignant") return peutproposerenseignant();
    return false;
}

function peutediterminot($id) {
    return peuttoutfaire();
}

function peutediterannotation($id, $id_parent) {
    global $link;
    global $login;
    global $user;
    if ($user["su"]) return true;
    if ( (NULL == $id) && (NULL == $id_parent) ) return false;
    if ( NULL != $id_parent) {
	$query = "SELECT login FROM minoterie_minot
                  WHERE id_minot = $id_parent AND login = '$login'";
    }
    else if ( NULL != $id) {
	$query = "SELECT login FROM minoterie_annotation, minoterie_minot
                  WHERE id_annotation = $id
                  AND minoterie_annotation.id_minot = minoterie_minot.id_minot
                  AND login = '$login'";
    }
    $res = $link->query($query) or die("ERREUR peutediterannotation($id_formation): $query");
    if ($r = $res->fetch_array()) {
	return true;
    }
    return false;
}

function peutchoisir() {
    global $user;
    global $link;
    $query = "SELECT id_enseignant
              FROM pain_enseignant
              WHERE id_enseignant = ".$user["id_enseignant"]." LIMIT 1";
    $result = $link->query($query) or die("ERREUR peutchoisir(): $query ".$link->error);
    if ($result->fetch_array()) {
	return true;
    }
    return false;
}

function selectenseignantschoix($id_choix) {
    global $link;
    $query = "SELECT pain_choix.id_enseignant AS enseignant,
                     pain_cours.id_enseignant AS respcours,
                     pain_formation.id_enseignant AS respannee,
                     pain_sformation.id_enseignant AS respformation
              FROM pain_choix, pain_cours, pain_formation, pain_sformation
              WHERE pain_choix.id_choix = $id_choix
              AND pain_cours.id_cours = pain_choix.id_cours
              AND pain_formation.id_formation = pain_cours.id_formation
              AND pain_sformation.id_sformation =
                  pain_formation.id_sformation";
    $res = $link->query($query) or die("ERREUR selectenseignantschoix($id_choix): ".$link->error);
    $r = $res->fetch_array();
    return $r;
}

function peutediterchoix($id_choix) {
    global $user;
    if ($user["su"]) return true;
    $r = selectenseignantschoix($id_choix);
    /* l'intervenant peut editer sa propre intervention :
    if ($user["id_enseignant"] == $r["enseignant"]) return true;
    */
    /* le responsable du cours peut editer :
    if ($user["id_enseignant"] == $r["respcours"]) return true;
    */
    if ($user["id_enseignant"] == $r["respannee"]) return true;
    if ($user["id_enseignant"] == $r["respformation"]) return true;
    return false;
}


function peutsupprimersformation($id) {
    return peuttoutfaire();
}

function peutsupprimerformation($id) {
    return peuttoutfaire();
}

function peutsupprimerchoix($id_choix) {
    global $user;
    if ($user["su"]) return true;
    $r = selectenseignantschoix($id_choix);
    /* l'intervenant peut supprimer son choix : */
    if ($user["id_enseignant"] == $r["enseignant"]) return true;
    /* le responsable du cours ne peut pas
     if ($user["id_enseignant"] == $r["respcours"]) return false; */
    if ($user["id_enseignant"] == $r["respannee"]) return true;
    if ($user["id_enseignant"] == $r["respformation"]) return true;
    return false;
}


function peutvoirstatsservices() {
    global $user;
    return 1 == $user["stats"];
}


function peuteditercours($id_cours) {
    global $link;
    global $user;
    if ($user["su"]) return true;
    $query = "SELECT pain_cours.id_enseignant AS respcours,
                     pain_formation.id_enseignant AS respannee,
                     pain_sformation.id_enseignant AS respformation
              FROM pain_cours, pain_formation, pain_sformation
              WHERE pain_cours.id_cours = $id_cours
              AND pain_formation.id_formation = pain_cours.id_formation
              AND pain_sformation.id_sformation =
                  pain_formation.id_sformation";
    $res = $link->query($query) or die("ERREUR peuteditercours($id_cours)");
    $r = $res->fetch_array();
/* le responsable du cours ne peut plus !
    if ($user["id_enseignant"] == $r["respcours"]) return true; */
    if ($user["id_enseignant"] == $r["respannee"]) return true;
    if ($user["id_enseignant"] == $r["respformation"]) return true;
    return false;
}


function peutmajcours($id_cours) {
    global $link;
    global $user;
    if ($user["su"]) return true;
    $query = "SELECT pain_cours.id_enseignant AS respcours,
                     pain_formation.id_enseignant AS respannee,
                     pain_sformation.id_enseignant AS respformation
              FROM pain_cours, pain_formation, pain_sformation
              WHERE pain_cours.id_cours = $id_cours
              AND pain_formation.id_formation = pain_cours.id_formation
              AND pain_sformation.id_sformation =
                  pain_formation.id_sformation";
    $res = $link->query($query) or die("ERREUR peutmajcours($id_cours)");
    $r = $res->fetch_array();
    if ($user["id_enseignant"] == $r["respcours"]) return true;
    if ($user["id_enseignant"] == $r["respannee"]) return true;
    if ($user["id_enseignant"] == $r["respformation"]) return true;
    return false;
}


function peutediterformationducours($id_cours) {
    global $link;
    global $user;
    if ($user["su"]) return true;
    $query = "SELECT pain_formation.id_enseignant AS respannee,
                     pain_sformation.id_enseignant AS respformation
              FROM pain_cours, pain_formation, pain_sformation
              WHERE pain_cours.id_cours = $id_cours
              AND pain_formation.id_formation = pain_cours.id_formation
              AND pain_sformation.id_sformation =
                  pain_formation.id_sformation";
    $res = $link->query($query) or die("ERREUR peutediterformationducours($idcours)");
    $r = $res->fetch_array();
    if ($user["id_enseignant"] == $r["respannee"]) return true;
    if ($user["id_enseignant"] == $r["respformation"]) return true;
    return false;
}

function peuteditercoursdelaformation($id_formation) {
    return  peutediterformation($id_formation);
}

function peuteditertranche($id_tranche) {
    global $link;
    global $user;
    if ($user["su"]) return true;
    $query = "SELECT pain_tranche.id_enseignant AS enseignant,
                     pain_cours.id_enseignant AS respcours,
                     pain_formation.id_enseignant AS respannee,
                     pain_sformation.id_enseignant AS respformation
              FROM pain_tranche, pain_cours, pain_formation, pain_sformation
              WHERE pain_tranche.id_tranche = $id_tranche
              AND pain_cours.id_cours = pain_tranche.id_cours
              AND pain_formation.id_formation = pain_cours.id_formation
              AND pain_sformation.id_sformation =
                  pain_formation.id_sformation";
    $res = $link->query($query) or die("ERREUR peuteditertranche($id_tranche)");
    $r = $res->fetch_array();
    /*  if ($user["id_enseignant"] == $r["enseignant"]) return false;
     if ($user["id_enseignant"] == $r["respcours"]) return false; */
    if ($user["id_enseignant"] == $r["respannee"]) return true;
    if ($user["id_enseignant"] == $r["respformation"]) return true;
    return false;
}

function peuteditertrancheducours($id_cours) {
    return peuteditercours($id_cours); /* le responsable du cours ne peut pas */
}

function peuteditersformation($id_sformation) {
    return peuttoutfaire();
}

function peuteditersformationdelannee($annee) {
    return peuttoutfaire();
}


function peutediterformation($id_formation) {
    global $link;
    global $user;
    if ($user["su"]) return true;
    $query = "SELECT pain_formation.id_enseignant AS respannee,
                     pain_sformation.id_enseignant AS respformation
              FROM pain_formation, pain_sformation
              WHERE pain_formation.id_formation = $id_formation
              AND pain_sformation.id_sformation =
                  pain_formation.id_sformation";
    $res = $link->query($query) or die("ERREUR peutediterformation($id_formation)");
    $r = $res->fetch_array();
    if ($user["id_enseignant"] == $r["respannee"]) return true;
    if ($user["id_enseignant"] == $r["respformation"]) return true;
    return false;
}

function peutediterformationdelasformation($id_sformation) {
    global $link;
    global $user;
    if ($user["su"]) return true;
    $query = "SELECT  pain_sformation.id_enseignant AS respformation
              FROM pain_sformation
              WHERE pain_sformation.id_sformation = $id_sformation";
    $res = $link->query($query) or die("ERREUR peutediterformationdelasformation($id_sformation)");
    $r = $res->fetch_array();
    if ($user["id_enseignant"] == $r["respformation"]) return true;
    return false;
}

function peutediterenseignant($id_enseignant = 0) {
    global $user;
    return ($user["su"] == 1) or ($id_enseignant == $user["id_enseignant"]);
}

function peutediterservice($id_serv = 0X0) {
    global $user;
    list($id_enseignant,$an) = explode('X', $id_serv);
    return ($user["su"] == 1) or ($id_enseignant == $user["id_enseignant"]);
}

function peutsupprimerservice($id_enseignant, $an) {
    global $user;
    return ($user["su"] == 1) or ($id_enseignant == $user["id_enseignant"]);
}

function peutediterservicedeenseignant($id_enseignant) {
    global $user;
    return ($user["su"] == 1) or ($id_enseignant == $user["id_enseignant"]);
}

function peutproposerenseignant() {
    global $link;
    global $user;
    if ($user["su"]) return true;
    $id = $user["id_enseignant"];
    $q = "SELECT
          ((SELECT COUNT(id_cours) FROM pain_cours
                 WHERE id_enseignant = $id) +
          (SELECT COUNT(id_formation) FROM pain_formation
                 WHERE id_enseignant = $id) +
          (SELECT COUNT(id_sformation) FROM pain_sformation
                 WHERE id_enseignant = $id)) AS resp";
    $res = mysql_result($q) or ("ERREUR peutproposerenseignant()");
    $r = $res->fetch_array();
    return 0 < $r["resp"];
}

function peutsupprimerenseignant($id_enseignant = 0) {
    global $user;
    return ($user["su"] == 1);
}

function peuteditertag($id_tag) {
    return peuttoutfaire();
}

function peuteditercollection($id_collection) {
    return peuttoutfaire();
}


function peuttransmettredeclarations($ids) {
    global $user;
    return ($user["su"] == 1);
}


function peutliredeclarationsduminot($id_minot) {
    global $login;
    global $link;
    if ($user["su"] == 1) return true;
    $q = "(
           SELECT id_minot FROM minoterie_minot
           WHERE id_minot = ".$id_minot."
           AND login LIKE '".$login."'
          )
          UNION
          (
           SELECT id_minot FROM minoterie_minot, minoterie_utilisateur
           WHERE id_minot = ".$id_minot."
           AND minoterie_utilisateur.id_departement = minoterie_minot.id_departement
           AND minoterie_utilisateur.login LIKE '".$login."'
          )";
    $result = $link->query($q) or die("ERREUR peutliredeclarationsduminot()".$link->error);
    if ($result->fetch_array()) {
        return true;
    }

    return false;
}

function peutsignerdeclarationsduminot($id_minot) {
    return peutliredeclarationduminot($id_minot);
}
?>