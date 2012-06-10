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

/* inclusion du fichier realisant la connexion avec les donnees sensibles en dur : */
require_once("iconnect.php"); 
/* ce fichier contient quelque chose comme :
<?php
$link = new mysqli("localhost", "utilisateur", "mot de passe", "base");
if ($link->connect_errno) {
    printf("Échec de la connexion : %s\n", $mysqli->connect_error);
    die();
}
?>
*/
$link->query("SET NAMES 'utf8'");

require_once('utils.php');

function bkp_base($force = false) {
    $output = "";
    $dir = dirname($_SERVER["SCRIPT_FILENAME"]);
    if (!@file_exists("$dir/../secret/minoteriebkp.sh")) return "pas de script de sauvegarde"; 
    $s = @stat($dir.'/bkp/void_bkp.txt');
    if  (time() > ($s[9] + 604800) || $force) /* weekly */
    {
	@touch($dir.'/bkp/void_bkp.txt');
	minoterie_log("-- backup base start");	
	$output = shell_exec("$dir/../secret/minoteriebkp.sh $dir");
	if (0 == strlen($output)) {
	   minoterie_log("-- Warning: backup problem (no script output)");
	}
	minoterie_log("-- backup base end");
    }
    return $output;
}
/* le script painbkp.sh contient :
mkdir -p ${1}/bkp/;
cd ${1}/bkp/;
touch tata;
if [ -f "pain.2.sql.gz" ]; 
then rm pain.2.sql.gz; 
echo "rm  2"; 
fi
if [ -f "pain.1.sql.gz" ];
then mv pain.1.sql.gz pain.2.sql.gz;
echo "rotate 1->2";
fi
if [ -f "pain.sql.gz" ]; 
then mv pain.sql.gz pain.1.sql.gz; 
echo "rotate 0->1";
fi
mysqldump -p'PASS' -u'USER' BASE pain_cours pain_enseignant pain_tranche pain_formation pain_sformation pain_edt | gzip - > pain.sql.gz;
echo "backuped";
*/
/* BACKUP */
bkp_base();
?>
