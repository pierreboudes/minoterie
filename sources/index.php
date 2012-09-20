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
entete("déclaration des services", "minoterie_index.js");
require_once("utils.php");
include("menu.php");
function index_php() {
    global $link;
    global $user;
    $ens = minoterie_getens();
    $login = phpCAS::getUser(); 
    if (NULL != $user) {
	echo '<div id="user" class="hiddenvalue">';
	echo '<span class="id">'.$user["id_utilisateur"].'</span>';
	echo '<span class="su">'.$user["su"].'</span>';
	echo '<span class="id_departement">'.$user["id_departement"].'</span>';
	echo '<span class="nom_departement">'.$user["nom_departement"].'</span>';
	echo '<span class="url_pain">'.$user["url_pain"].'</span>';
	echo '</div>';
	$nom = $user["prenom"]." ".$user["nom"];
    } else {
	$nom = $login;
    }
    /* message d'accueil */
    echo "<center><div class=\"infobox\"><p>Bonjour $nom,</p><p>";
    $ndecl = count($ens);
    if (0 == $ndecl) {
	echo "Vous n'avez pas de service déclaré dans la minoterie.";
    } else {
	if (1 == $ndecl){
	    echo "Vous avez un service déclaré au département ".$ens[0]["nom_departement"].".";
	} else {
	    echo "Vous avez $n déclarations de services dans différents départements.";
	}
	echo " Vous pouvez visualiser une déclaration en cliquant sur le triangle à gauche du nom de département ci-dessous, puis l'annoter et la valider en utilisant les boutons.";
    }
    echo "</p>";
    if (NULL != $user) {
	echo "<p>Vous pouvez accéder à :<ul>";
	if ($user["su"]) {
	    echo "<li>la <a href=\"lecture.php\">liste des déclarations</a> de la minoterie</li>";
	    echo "<li>l'<a href=\"admin.php\">administration</a> de la minoterie</li>";
	}
	if (NULL != $user["departements"]) {
	    echo "<li>l'<a href=\"importer.php\">importation de déclarations</a> depuis le pain du département ";
	    echo '<ul>';
	    $first = true;
	    foreach($user["departements"] as $dept)
	    {
		echo '<li>'.($first?'':'ou du département ');
		$first = false;
		echo '<span class="nom_departement">'.$dept["nom_departement"].'</span>';
		echo '</li>';
	    }
	    echo '</ul>';
	}
	echo '</ul></p>';
    }
    echo "</div></center>"; /* fin infobox */

    foreach ($ens as $dpt) {
	$id_d = $dpt["id_departement"];
	$id_m = $dpt["id_minot"];
	$nom_d = $dpt["nom_departement"];
	$url_d = $dpt["url_pain"];
	$modif = $dpt["modification"];
	$traitee = $dpt["traitee"];

	echo "<center><div id=\"departement_$id_d\" class=\"departement\">
              <table class=\"departement\">
              <tbody><tr id=\"minot_$id_m\" class=\"minot\">
              <td class=\"laction\">
                <div id=\"basculeminot_$id_m\" class=\"basculeOff\" onclick=\"basculerMinot($id_m, true, $traitee)\">
              </td>
              <td class=\"nom_departement\">Déclaration transmise par le département $nom_d (depuis <a href=\"$url_d\">pain</a>)</td>
              <td class=\"modification\">$modif</td>";
	if ($traite) {
	    echo "<td class=\"traite\"><div class=\"traiteOn\">déclaration traitée</div></td>";
	} else {
	    echo "<td class=\"traite\"></td>";
	}
	echo "</tr></tbody></table><div></center>"; 
    }
}

index_php();

echo '<div id="vuecourante"></div>';

include("skel_index.html");
/* include("inc_aide.php"); */
?>
<p>
<a href="http://validator.w3.org/check?uri=referer"><img
    src="http://www.w3.org/Icons/valid-xhtml10-blue"
    alt="Valid XHTML 1.0 Transitional" height="31" width="88" /></a>
    </p>
<?php
piedpage();
?>
