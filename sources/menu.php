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
require_once('inc_connect.php');
function ig_formselectannee($annee)
{
    global $link;
    $qans = "SELECT DISTINCT `annee_universitaire` 
             FROM pain_sformation WHERE 1 ORDER BY `annee_universitaire` ASC";
    $rans = $link->query($qans) 
	  or die("Échec de la requête sur la table sformation");
    while ($an =$rans->fetch_array()) {
	echo '<option ';
	if ($an["annee_universitaire"] == $annee) {
	    echo 'selected="selected" ';
	}
	echo  'value="'.$an["annee_universitaire"].'">';
	echo trim($an["annee_universitaire"].'-'.($an["annee_universitaire"] + 1));
	echo '</option>';
    }
}
?>
<ul id="menu">
      <li><a href="./">accueil</a></li>
      <li><a href="departement.php">departement</a></li>
<?php
    if (1 == $user["su"]) {
	echo '<li><a href="admin.php">admin</a></li>';
    }
?>
      <li><a href="logout.php">logout</a></li>
      <li>
</li>
</ul>
