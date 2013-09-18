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
$user = weak_auth();
require_once("inc_headers.php"); /* pour en-tete et pied de page */
entete("déclaration des services", "minoterie_importer.js");
require_once("utils.php");
include("menu.php");


function importer_php() {
    global $link;
    global $user;
    $login = phpCAS::getUser(); 
    
    if (NULL != $user) {
	echo '<div id="user" class="hiddenvalue">';
	echo '<span class="id">'.$user["id_utilisateur"].'</span>';
	echo '<span class="su">'.$user["su"].'</span>';
	$dept = $user["departements"][0];
	echo '<ul id="userdept">';
	echo '<li class="departement">';
	echo '<span class="id_departement">'.$dept["id_departement"].'</span>';
	echo '<span class="nom_departement">'.$dept["nom_departement"].'</span>';
	echo '<span class="url_pain">'.$dept["url_pain"].'</span>';
	echo '</li></ul>';
	echo '</div>';
	echo '<div id="choixdept">Département :';
	echo '<ul id="departements">';
	$first = true;
	foreach($user["departements"] as $dept)
	{
	    echo '<li class="departement'.($first?' selected':'').'">';
	    $first = false;
	    echo '<span class="id_departement">'.$dept["id_departement"].'</span>';
	    echo '<span class="nom_departement">'.$dept["nom_departement"].'</span>';
	    echo '<span class="url_pain">'.$dept["url_pain"].'</span>';
	    echo '</li>';
	}
	echo '</ul></div>';

	echo "<div id=\"annee\" class=\"hiddenvalue\">".default_year()."</div>";

	$nom = $user["prenom"]." ".$user["nom"];
	echo "<center><div class=\"infobox\"><p>Importer des services depuis votre pain de département : dérouler les catégories et faire une sélection d'enseignants, dans la colonne sélection.</p></div></center>";
    } else {
	return;
    }
    echo '<div id="vuecourante"></div>';
}

importer_php();

include("skel_importer.html");

piedpage();
?>
