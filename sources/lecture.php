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
require_once("inc_headers.php"); /* pour en-tete et pied de page */
entete("déclaration des services", "minoterie_lecture.js");
require_once("utils.php");

function lecture_php() {
    global $link;
    global $user;
    echo '<div id="user" class="hiddenvalue">';
    echo '<span class="id">'.$user["id_utilisateur"].'</span>';
    echo '<span class="su">'.$user["su"].'</span>';
    echo '<span class="id_departement">'.$user["id_departement"].'</span>';
    echo '<span class="nom_departement">'.$user["nom_departement"].'</span>';
    echo '<span class="url_pain">'.$user["url_pain"].'</span>';
    echo '</div>';
    $nom = $user["prenom"]." ".$user["nom"];
    /* message d'accueil */
    echo "<center><div class=\"infobox\"><p>Bonjour $nom,</p><p>";

    echo "</div></center>"; /* fin infobox */
}

lecture_php();

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