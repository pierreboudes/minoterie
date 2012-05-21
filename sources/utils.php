<?php
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

function microsecoffset() {
    static $us = 0;
    if (!$us) $us = microtime(true);
    return (microtime(true) - $us);
}

microsecoffset(); /* armee */

function postclean($s) {
    global $link;
    if (isset($_POST[$s])) {
	if(get_magic_quotes_gpc()) {
	    return trim(htmlspecialchars($link->real_escape_string(stripslashes(($_POST[$s]))), ENT_QUOTES));
	}
	else {
	    return trim(htmlspecialchars($link->real_escape_string($_POST[$s]), ENT_QUOTES));
	}
    }
    else return NULL;
}

/** recupere une chaine passee en HTTP/GET
 */
function getclean($s) {
    global $link;
    if (isset($_GET[$s])) {
	if(get_magic_quotes_gpc()) {
	    return trim(htmlspecialchars($link->real_escape_string(stripslashes(($_GET[$s]))), ENT_QUOTES));
	}
	else {
	    return trim(htmlspecialchars($link->real_escape_string($_GET[$s]), ENT_QUOTES));
	}
    }
    else return NULL;
}

/** recupere un nombre passee en HTTP/GET
 */
function getnumeric($s) {
    global $link;
    if (isset($_GET[$s]) && is_numeric($_GET[$s])) {
	return $_GET[$s];
    }
    else return NULL;
}

/** recupere une liste de nombre passee en HTTP/GET
 */
function getlistnumeric($s) {
    global $link;
    if (isset($_GET[$s])) {
	$a = explode(',',$_GET[$s]);
	foreach ($a as $id) {
	    if (!is_numeric(trim($id))) return NULL;
	}
	return $_GET[$s];
    }
    else return NULL;
}


function cookieclean($s) {
    global $link;
    if (isset($_COOKIE[$s])) {
	if(get_magic_quotes_gpc()) {
	    return trim(htmlspecialchars($link->real_escape_string(stripslashes(($_COOKIE[$s]))), ENT_QUOTES));
	}
	else {
	    return trim(htmlspecialchars($link->real_escape_string($_COOKIE[$s]), ENT_QUOTES));
	}
    }
    else return NULL;
}

function ip_client() {
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
    $IP = $_SERVER['HTTP_X_FORWARDED_FOR']; 
    elseif(isset($_SERVER['HTTP_CLIENT_IP']))   
    $IP = $_SERVER['HTTP_CLIENT_IP'];   
    else
    $IP = $_SERVER['REMOTE_ADDR'];  
    return $IP;
}

function postnumclean($s) {
    return str_replace(',','.',str_replace(' ', '',postclean($s)));
}

function pain_log($message, $logname='pain') {
    global $user;
    /* vu qu'on écrit un fichier on ne veut pas de process appelant qui boucle... */
    static $compteur;
    if ($compteur++ > 10) return;

//	$pid = '(pid '.@getmypid().')';
    $message = preg_replace("/\n+/", " ", $message);
//	$message = preg_replace("/\n*$/", "\n", $message);
    $message .= ' -- '.date("M d H:i:s").' '.$user['login']. "\n";
    $logfile = dirname($_SERVER['SCRIPT_FILENAME'])."/painlogs/".$logname .'.log';
/*	echo $logfile;
		if (@is_readable($logfile)) echo "readable";
*/
    if (@is_readable($logfile)
	AND (!$s = @filesize($logfile) OR $s > 100*1024)) {
	$rotate = true;
	$message .= "-- [ rotate ]\n";
    } else $rotate = '';
    $f = @fopen($logfile, "ab");
    if ($f) {
	fputs($f, htmlspecialchars($message));
	fclose($f);
    }
    if ($rotate) {
	$nb = 50;
	@unlink($logfile.".$i");
	for ($i = $nb; $i > 1; --$i) {
	    @rename($logfile.".".($i - 1),$logfile.".$i");
	}
	@rename($logfile,$logfile.'.1');		
    }
}

function ig_statsmysql() {
    global $link;
    echo '<div><pre>';
    $status = implode("\n",explode('  ', $link->stat()));
    echo "MYSQL\n";
    echo $status;
    $us = round(microsecoffset() * 1000);
    echo "\nPage servie en : ".$us."ms ";
    echo '</pre></div>';
}

Class Error {
    public $error = "Erreur";
}

function errmsg($s) {
    $err = new Error;
    $err->error = "Erreur: ".$s;
    print json_encode($err);
    die();
}
?>