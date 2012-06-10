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
require_once("authentication.php"); 
$user = authentication();
require_once("utils.php");

function inserer_interventions($val) {
    global $link;
    $q = "INSERT INTO minoterie_intervention 
                      (id_minot, nom_formation, annee_etude, parfum,
                      id_cours, semestre, nom_cours, code_geisha, cm, td, tp, alt)
          VALUES ".implode(',', $val);
    if (!$link->query($q)) {
	errmsg("erreur avec la requete :\n".$q."\n".$link->error);
    }
}

function json_importer_declarations_php() {
    global $link;
    global $user;
    $id_departement = getnumeric("id_departement");
    if (NULL == $id_departement) {
	errmsg("il manque un id_departement");
    }
    $o = getjsonaofaa("interventions");
    $id_enseignant = -1;
    $val = array();
    foreach ($o as $ligne) {
	if ($ligne["id_enseignant"] != $id_enseignant) {
	    $id_enseignant = $ligne["id_enseignant"];
	    /* inserer la liste des valeurs precedentes */
	    if (0 < count($val)) {
		inserer_interventions($val);
		/* repartir sur de nouvelles valeurs */
		$val = array();
	    }

	    /* faire un nouveau minot */
	    $q = "INSERT INTO minoterie_minot (id_departement, id_enseignant, login, prenom, nom, email, statut, service)".
		"VALUES (".$id_departement.", ".$id_enseignant.", ".$ligne["login"].
		", ".$ligne["prenom"].", ".$ligne["nom"].", ".$ligne["email"].
                ", ".$ligne["statut"].", ".$ligne["service"].");";
	    if (!$link->query($q)) {
		errmsg("erreur avec la requete :\n".$q."\n".$link->error);
	    }
	    $id_minot = $link->insert_id;
	}
	$val[] = " (".$id_minot.", ".$ligne["nom_formation"].", ".$ligne["annee_etude"].", ".$ligne["parfum"].", ".
	    $ligne["id_cours"].", ".$ligne["semestre"].", ".$ligne["nom_cours"].", ".$ligne["code_geisha"].", ".
	    $ligne["cm"].", ".$ligne["td"].", ".$ligne["tp"].", ".$ligne["alt"].")";
    }
    if (0 < count($val)) {
	inserer_interventions($val);
    }
    echo '{"ok": "ok"}';
}

json_importer_declarations_php();
?>
