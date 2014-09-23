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
    if ((NULL != $user) && (NULL != $user["id_utilisateur"])) {
	echo '<div id="user" class="hiddenvalue">';
	echo '<span class="id">'.$user["id_utilisateur"].'</span>';
	echo '<span class="su">'.$user["su"].'</span>';
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
	    echo "Vous avez $ndecl déclarations de services dans différents départements.";
	}
	echo " Vous pouvez visualiser une déclaration en cliquant sur le triangle à gauche du nom de département ci-dessous, puis la valider en utilisant le bouton <em>signer</em>.";
    }
    echo "</p>";
    if (NULL != $user) {
	echo "<p>Vous pouvez accéder à :<ul>";
	if ($user["su"]) {
	    echo "<li>l'<a href=\"admin.php\">administration</a> de la minoterie</li>";
	}
	if (NULL != $user["departements"]) {
        echo "<li>la <a href=\"lecture.php\">liste des déclarations</a> de la minoterie</li>";
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
	$id_e = $dpt["id_enseignant"];
	$id_d = $dpt["id_departement"];
	$id_m = $dpt["id_minot"];
	$nom_d = $dpt["nom_departement"];
	$url_d = $dpt["url_pain"];
	$modif = $dpt["modification"];
	$signee = $dpt["derniere_signature"];
	$traitee = $dpt["traitee"];
    $definitif = $dpt["definitif"];

	echo "<center><div id=\"departement_$id_d\" class=\"departement\">
              <table class=\"departement\">
              <tbody><tr id=\"minot_$id_m\" class=\"minot\">
              <td class=\"laction\">
                <div id=\"basculeminot_$id_m\" class=\"basculeOff\" onclick=\"basculerMinot($id_m, true, $traitee)\">
              </td>
              <td class=\"nom_departement\">Déclaration transmise par le <a href=\"$url_d\">département $nom_d</a></td>
              <td class=\"modification\">$modif</td>";
    echo "<td class=\"etape\">";
	if ($definitif) {
        echo "service fait";
	} else {
	    echo "service prévisionnel";
	}
    echo "<span class=\"hiddenvalue\">$definitif</span></td>";
	if ($signee != NULL) {
	    echo "<td class=\"signee\"><div class=\"traiteOn\">signée<div class=\"sub\">$signee</div></div></td>";
	} else {
	    echo "<td class=\"signee\"><b>non signée</b></td>";
	}
	if ($traitee) {
	    echo "<td class=\"traite\"><div class=\"traiteOn\">traitée</div></td>";
	} else {
	    echo "<td class=\"traite\" style=\"display:none\"></td>";
	}
    echo "<td class=\"enseignant\" style=\"display:none\"><span class=\"hiddenvalue\">$id_e</span></td>";
    echo "<td class=\"departement\" style=\"display:none\"><span class=\"hiddenvalue\">$id_d</span></td>";
	echo "</tr></tbody></table><div></center>";
    }
}

index_php();

echo '<div id="vuecourante"></div>';

include("skel_index.html");
/* include("inc_aide.php"); */
piedpage();
?>
