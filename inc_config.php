<?php /* -*- coding: utf-8 -*- */
/* Minoterie - outil de gestion des services d'enseignement
 *
 * Copyright 2009-2013 Pierre Boudes,
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
require_once('utils.php');
authrequired();

function get_configuration($chaine) {
    global $link;
    $query = "SELECT valeur
              FROM minoterie_config
              WHERE configuration LIKE '$chaine'";
    $result = $link->query($query)  or die("Échec de la requête ".$query."\n".$link->error);
    $res = "";
    if ($conf = $result->fetch_assoc()) {
        $res = $conf["valeur"];
    }
    return $res;
}

function declarationsdefinitives() {
    $definitives = getnumeric("definitives");
    if ($definitives == NULL) {
        $definitives = (get_configuration("ETAPE_DECLARATIONS") == "definitives");
    }
    return (boolean) $definitives;
}


?>