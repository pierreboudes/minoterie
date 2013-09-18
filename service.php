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
 * or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public
 * License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Minoterie.  If not, see <http://www.gnu.org/licenses/>.
 */
require_once('authentication.php'); 
$user = authentication();
$annee = annee_courante();
require_once("inc_headers.php"); /* pour en-tete et pied de page */
entete("affichage des services (temporaire)","pain_service.js");
include("menu.php");
require_once("inc_functions.php");
require_once("inc_statsfunc.php");

function service_php($id) {
    global $link;
/* par defaut on sert la feuille de l'utilisateur */
    $id_enseignant = $id; 
/* mais si on a un identifiant dans l'url on utilise plutot celui-ci */
    if (isset($_GET['id_enseignant'])) {
	$id_enseignant = getnumeric("id_enseignant");
    }
/* et si l'identifiant provient du formulaire, c'est plutot celui-la */
    if (isset($_POST['id_enseignant'])) {
	$id_enseignant = postclean('id_enseignant');
    }
    
/* le formulaire */
    echo '<center><div class="infobox" style="width:200px;">';
    echo '<form method="post" id="choixenseignant" class="formcours" name="enseignant" action="#">';
    echo '<select name="id_enseignant" style="display:inline; width:150px;">';
    ig_formselectenseignants($id_enseignant);
    echo '</select>';
    echo '<input type="submit" value="OK" style="display:inline;width:40px;"/>';
    echo '</form>'."\n";
    echo '</div></center>';
    
    if ($id_enseignant != "") {
	/* pour le javascript */
	echo '<div id="formuser" class="hiddenvalue"><span class="id">'.$id_enseignant.'</span></div>';
	
	/* annuaire */
	echo "<h2>Informations d'annuaire</h2>";
	
	echo '<table class="enseignants">';
	ig_legendeenseignant();
	$q = "SELECT * from pain_enseignant 
          WHERE id_enseignant = $id_enseignant";
	($r = $link->query($q)) 
	    or die("Échec de la connexion à la base enseignant");
	if ($ens = $r->fetch_array()) {
	    ig_enseignant($ens);
	}
	echo '</table>';
	
	$totaux = totauxinterventions($id_enseignant);
	
	echo "<h2>Détail des interventions</h2>";
	
	$services = listeinterventions($id_enseignant);
	echo '<table class="interventions noprint">';
	echo '<tr>';
	ig_legendeintervention();
	echo '</tr>';
	while ($service = $services->fetch_array()) {
	    echo '<tr class="intervention">';
	    ig_intervention($service);
	    echo '</tr>';
	}
	echo '<tr>';
	ig_totauxinterventions($totaux);
	echo '</tr>';
	echo '</table>';
    }
}

service_php($user["id_enseignant"]);

/* Choix */
echo "<h2>Choix</h2>";
echo '<div id="choix"></div>';

/* Choix et tranches */
echo "<h2>Bilan des interventions choisies et prévues</h2>";
echo '<div id="potentiel"></div>';

/* Cumul des responsabilités */
echo "<h2>Responsabilités</h2>";
echo '<div id="responsabilite"></div>';

include("skel_index.html");
piedpage();
?>

