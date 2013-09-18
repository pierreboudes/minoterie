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
authrequired();

function entete() {
    /* premier argument : titre de la page */
    /* arguments suivants : des noms de fichiers javascripts à inclure */
    $narg = func_num_args();
    $titre = func_get_arg(0);
    echo <<<EOD
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr" dir="ltr">

<head>
<title>Minoterie -- $titre</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel='stylesheet' media='all' href='css/general.css' type='text/css' />
<link type="text/css" href="css/custom-theme/jquery-ui-1.8.1.custom.css" rel="stylesheet" />
<script type="text/javascript" src="js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.8.1.custom.min.js"></script>
<script type="text/javascript" src="js/jquery.json-2.3.min.js"></script>
<script type="text/javascript" src="js/dyntab.js"></script>
EOD;
    for($i = 1; $i < $narg; $i++){
	$arg = func_get_arg($i);
	$extension = substr($arg, -3); /* si $opt = blabla.css alors $extension = css*/
	if (0 == strcasecmp($extension, "css")) {
	    echo "<link rel='stylesheet' href='css/".$arg."' type='text/css' media='all'/>\n";
	} else if (0 == strcasecmp($extension, ".js")) {
	    echo "<script type='text/javascript' src='js/".$arg."'></script>\n";
	} else {
	    echo $opt."\n";
	};	
    }
/* ------ tweaks --------

<link rel='stylesheet' media='projection, screen, tv' href='affichage.css' type='text/css' />
<link rel="stylesheet" href="impression.css" type="text/css" media="print" />
<!-- FIREBUG DU PAUVRE !
<script type='text/javascript' src='http://getfirebug.com/releases/lite/1.2/firebug-lite-compressed.js'></script>
-->
<script type="text/javascript" src="http://dev.jquery.com/view/trunk/plugins/autocomplete/jquery.autocomplete.js"></script>
<!--
<link href="bubbletip/bubbletip/bubbletip.css" rel="stylesheet" type="text/css" />
<script src="bubbletip/jQuery.bubbletip-1.0.1.js" type="text/javascript"></script>
-->
<script type="text/javascript" src="js/jquery.highlight-3.js"></script>
*/
    echo <<<EOD
<meta name="description" content="Système de gestion des services du département d'informatique" />
</head>
<body>
EOD;
}

function ig_versionsvn() {
    $xmlstr = `svn log -r BASE --xml`;
    $xml = new SimpleXMLElement($xmlstr);
    echo "<pre>";
    echo "Dernière révision: ".$xml->logentry[0]['revision']."\n";
    echo "Par: ".$xml->logentry[0]->author."\n";
    $date = explode('T', $xml->logentry[0]->date);
    echo "Date: ".($date[0])."\n";
    echo "Message: ".$xml->logentry[0]->msg."\n";
    echo "</pre>";
}

function piedpage() {
//    ig_versionsvn(); trop lent.
    ig_statsmysql();
    echo <<<EOD
</body>
</html>
EOD;
}
?>