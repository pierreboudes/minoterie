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
$annee = annee_courante();

require_once("inc_connect.php");
require_once("utils.php");
require_once("inc_functions.php"); // pour update_servicesreels($id_par);

/**
retourne des entrées de type $readtype, prises dans la base, sélectionnées par le contexte d'une requête HTTP/GET.

Les entrées sont éventuellement calculées par jointures et aggrégats. La sélection dépend soit de l'identifiant de l'entrée fourni par le contexte d'une requête HTTP/GET, ou bien d'un identifiant de groupe d'entrées ou bien de l'année courante. 
 */
function json_get_php($annee, $readtype) {
    global $link;
    if ($readtype == "sformation") {
	$type = "sformation";
	$par = "annee_universitaire";
	$order = "ORDER BY numero ASC";
    } else if ($readtype == "microsformation") {
	$type = "sformation";
	$par = "annee_universitaire";
	if (isset($_GET["id_parent"])) {
	    $annee = getnumeric("id_parent");
	}
	$requete = "SELECT nom AS nom_sformation,
                           nom AS label,
                           id_sformation,
                           id_sformation AS id
                    FROM pain_sformation
                    WHERE annee_universitaire = $annee
                    ORDER BY numero ASC";
    } else if ($readtype == "formation") {
	$type = "formation";
	$par = "id_sformation";
	$order = "ORDER BY numero ASC";
    } else if ($readtype == "cours") {
	$type = "cours";
	$par = "id_formation";
	$order = "ORDER BY semestre, nom_cours ASC";
    } else if ($readtype == "tranche") {
	$type = "tranche";
	$par  = "id_cours";
	$order = "ORDER by groupe ASC";
    } else if ($readtype == "choix") {
	$type = "choix";
	$par = "id_cours";
	$order = "ORDER by modification ASC";
    } else if ($readtype == "enseignant") {
	$type = "enseignant";
	$par = "id_categorie";
	$requete = "SELECT pain_enseignant.*,
                    pain_categorie.nom_court,
                    \"$type\" AS type,
                    id_$type AS id
                    FROM pain_enseignant, pain_categorie";
	if (isset($_GET['id_parent'])) {
	    $id_par = $_GET['id_parent'];
	    $requete .= " WHERE categorie = $id_par ";	    
        } else if (isset($_GET["id"])) {
	    $id = $_GET['id'];
	    $requete .= " WHERE id_enseignant = $id ";	    
	}
	$requete .= " AND id_categorie = categorie ";
	$requete .= "ORDER BY nom, prenom ASC";
    } else if ($readtype == "longchoix") {
	$type = "choix";
	$requete = "SELECT pain_choix.*,
                           pain_choix.id_choix AS id_longchoix,
                           pain_choix.id_choix AS id,
                           pain_cours.nom_cours, 
                           pain_cours.id_cours,
                           pain_cours.semestre,
                           pain_formation.nom,
                           pain_formation.annee_etude,
                           pain_formation.parfum,
                           pain_sformation.annee_universitaire,
                           \"long$type\" AS type
                     FROM  pain_choix, pain_cours, pain_formation, pain_sformation ";
	if (isset($_GET['id_parent'])) {
	    $id_par = $_GET['id_parent'];
	    $requete .= " WHERE pain_choix.id_enseignant = $id_par ";	    
        } else if (isset($_GET["id"])) {
	    $id = $_GET['id'];
	    $requete .= " WHERE pain_choix.id_choix = $id ";	    
	}
	$requete .="AND pain_cours.id_cours = pain_choix.id_cours
                    AND pain_formation.id_formation = pain_cours.id_formation
                    AND pain_sformation.id_sformation = pain_formation.id_sformation
                    AND pain_sformation.annee_universitaire = $annee
                    ORDER by pain_cours.semestre ASC, pain_formation.numero ASC";
    } else if ($readtype == "longtranche") {
	$type = "tranche";
	$requete = "SELECT pain_tranche.*, 
                           pain_tranche.id_tranche AS id_longtranche,
                           pain_tranche.id_tranche AS id,
                           pain_cours.nom_cours, 
                           pain_cours.id_cours,
                           pain_cours.semestre,
                           pain_formation.nom,
                           pain_formation.annee_etude,
                           pain_formation.parfum,
                           pain_sformation.annee_universitaire,
                           \"long$type\" AS type
                     FROM pain_tranche, pain_cours, pain_formation, pain_sformation ";
	if (isset($_GET['id_parent'])) {
	    $id_par = getnumeric("id_parent");
	    $requete .= " WHERE pain_tranche.id_enseignant = $id_par ";	    
        } else if (isset($_GET["id"])) {
	    $id = getnumeric("id");
	    $requete .= " WHERE pain_tranche.id_tranche = $id ";	    
	}
	$requete .="AND pain_cours.id_cours = pain_tranche.id_cours
                    AND pain_formation.id_formation = pain_cours.id_formation
                    AND pain_sformation.id_sformation = pain_formation.id_sformation
                    AND pain_sformation.annee_universitaire = $annee
                    ORDER by  pain_cours.semestre ASC, pain_formation.numero ASC";
    } else if ($readtype == "service") {
	$type = "service";
	$requete = "SELECT pain_service.*,
                           pain_categorie.nom_court,
                           \"$type\" AS type,
                           CONCAT(id_enseignant,'X',annee_universitaire) AS id_service,
                           CONCAT(id_enseignant,'X',annee_universitaire) AS id
                    FROM pain_service, pain_categorie
                    WHERE ";
	if (isset($_GET['id_parent'])) {
	    $id_par = getnumeric("id_parent");
	    update_servicesreels($id_par);
	    $requete .= " pain_service.id_enseignant = $id_par ";
        } else if (isset($_GET["id"])) {
	    list($id_ens,$an) = explode('X', getnumeric("id"));
	    update_servicesreels($id_ens);
	    $requete .= " id_enseignant = $id_ens AND annee_universitaire = $an ";
	} else {
	    $requete .= " 0 ";
	}
       $requete .= "AND id_categorie = categorie 
                    ORDER BY annee_universitaire ASC";
    } else if ($readtype == "potentiel" and isset($_GET['id_parent'])) {
	$id_par =  getnumeric("id_parent");
	$requete = "select *,
id_cours as id_potentiel,
greatest(ifnull(tranche_cm,0),ifnull(choix_cm,0)) as cm,
greatest(ifnull(tranche_td,0),ifnull(choix_td,0)) as td,
greatest(ifnull(tranche_tp,0),ifnull(choix_tp,0)) as tp,
greatest(ifnull(tranche_alt,0),ifnull(choix_alt,0)) as alt,
greatest(ifnull(tranche_htd,0),ifnull(choix_htd,0)) as htd 
from
((
select
pain_sformation.numero,
pain_cours.id_cours,
pain_cours.nom_cours,
pain_cours.semestre,
pain_formation.nom,
pain_formation.annee_etude,
pain_formation.parfum
from pain_choix, pain_cours, pain_formation, pain_sformation 
where
pain_choix.id_enseignant = ".$id_par." 
and pain_choix.id_cours = pain_cours.id_cours
and pain_cours.id_formation = pain_formation.id_formation
and pain_formation.id_sformation = pain_sformation.id_sformation
and annee_universitaire = ".$annee.")
union
(select
pain_sformation.numero,
pain_cours.id_cours,
pain_cours.nom_cours,
pain_cours.semestre,
pain_formation.nom,
pain_formation.annee_etude,
pain_formation.parfum
from pain_tranche, pain_cours, pain_formation, pain_sformation 
where
pain_tranche.id_enseignant = ".$id_par." 
and pain_tranche.id_cours = pain_cours.id_cours
and pain_cours.id_formation = pain_formation.id_formation
and pain_formation.id_sformation = pain_sformation.id_sformation
and annee_universitaire = ".$annee.")) as t0
left join
(select id_cours, 
sum(cm) as choix_cm,
sum(td) as choix_td,
sum(tp) as choix_tp,
sum(alt) as choix_alt,
sum(htd) as choix_htd
from pain_choix
where
id_enseignant = ".$id_par." 
group by id_cours) as t1 using(id_cours)
left join
(select id_cours,
sum(cm) as tranche_cm,
sum(td) as tranche_td,
sum(tp) as tranche_tp,
sum(alt) as tranche_alt,
sum(htd) as tranche_htd
from pain_tranche
where
id_enseignant = ".$id_par." 
group by id_cours) as t2
 using(id_cours)
order by semestre ASC,
numero ASC, 
annee_etude ASC, 
nom_cours ASC";
    } else if ($readtype == "responsabilite" and isset($_GET['id_parent'])) {
	$id_par =  getnumeric("id_parent");
	$requete = "(select 
concat('cours: ', nom_cours, ', ', pain_formation.nom, ' ', annee_etude) as resp_nom,
concat('c', id_cours) as id_responsabilite,
1 as resp_type_num
from pain_cours, pain_formation, pain_sformation
where pain_cours.id_enseignant = ".$id_par."
and pain_cours.id_formation = pain_formation.id_formation
and pain_formation.id_sformation = pain_sformation.id_sformation
and pain_sformation.annee_universitaire = ".$annee."
) 
union
(select 
concat('année de formation: ', pain_formation.nom, ' ', annee_etude, ' ', pain_formation.parfum) as resp_nom,
concat('f', id_formation) as id_responsabilite,
2 as resp_type_num
from pain_formation, pain_sformation
where pain_formation.id_enseignant = ".$id_par."
and pain_formation.id_sformation = pain_sformation.id_sformation
and pain_sformation.annee_universitaire = ".$annee."
)  
union
(select 
concat('formation: ', pain_sformation.nom) as resp_nom,
concat('s', id_sformation) as id_responsabilite,
3 as resp_type_num
from pain_sformation
where pain_sformation.id_enseignant = ".$id_par."
and pain_sformation.annee_universitaire = ".$annee."
)"; 

    } else if ($readtype == "tag") {
	$type = "tag";
	$requete = "SELECT pain_tag.*,                    
                    \"$type\" AS type,
                    id_$type AS id,
                    (SELECT COUNT(*) FROM pain_tagscours WHERE pain_tagscours.id_tag = pain_tag.id_tag)
                    AS nb_tous_cours,
                    (SELECT COUNT(pain_tagscours.id_cours) 
                     FROM pain_tagscours, pain_cours, pain_formation, pain_sformation 
                     WHERE pain_tagscours.id_tag = pain_tag.id_tag
                     AND pain_tagscours.id_cours = pain_cours.id_cours
                     AND pain_formation.id_formation = pain_cours.id_formation
                     AND pain_sformation.id_sformation = pain_formation.id_sformation
                     AND pain_sformation.annee_universitaire = $annee)
                    AS nb_cours
                    FROM pain_tag";
	if (isset($_GET['id_parent'])) {
	    $requete .= " WHERE 1 ";	    
        } else if (isset($_GET["id"])) {
	    $id = $_GET['id'];
	    $requete .= " WHERE id_tag = $id ";	    
	}
	$requete .= "ORDER BY nom_tag ASC";
    } else if ($readtype == "tags") {
	if (isset($_GET['id_parent'])) {
	    $type = "tag";
	    $id_par =  getnumeric("id_parent");
	    $requete = "SELECT pain_tag.*, 
                        \"$type\" AS type, 
                        pain_tag.id_$type AS id                 
                        FROM pain_tag, pain_tagscours";
	    $requete .= " WHERE  pain_tagscours.id_cours = $id_par 
                          AND pain_tag.id_tag = pain_tagscours.id_tag";	    
	    $requete .= " ORDER BY nom_tag ASC";
        } else {
	    errmsg("le type tags nécessite un id_parent");
	}
    } else if ($readtype == "unusedtags") {
	if (isset($_GET['id_parent'])) {
	    $type = "tag";
	    $id_par =  getnumeric("id_parent");
	    $requete = "SELECT nom_$type as label, 
                        id_$type AS id                 
                        FROM pain_$type";
	    $requete .= " WHERE id_$type NOT IN 
                         (SELECT id_$type FROM pain_tagscours
                          WHERE id_cours = $id_par)";	    
	    $requete .= " ORDER BY nom_tag ASC";
        } else {
	    errmsg("le type unusedtags nécessite un id_parent");	}
    } else if ($readtype == "collection") {
	$type = "collection";
	$requete = "SELECT pain_collection.*,                    
                    \"$type\" AS type,
                    id_$type AS id,                   
                    (SELECT COUNT(*) FROM pain_collectionscours WHERE pain_collectionscours.id_collection = pain_collection.id_collection) AS nb_cours,
                    pain_sformation.nom AS nom_sformation
                    FROM pain_collection LEFT OUTER JOIN pain_sformation
                    ON pain_collection.id_sformation = pain_sformation.id_sformation";
	if (isset($_GET['id_parent'])) {
	    $requete .= " WHERE pain_collection.annee_universitaire = $annee ";	    
        } else if (isset($_GET["id"])) {
	    $id = $_GET['id'];
	    $requete .= " WHERE id_collection = $id ";	    
	}
	$requete .= "ORDER BY nom_sformation, nom_collection ASC";
    } else if ($readtype == "collections") {
	if (isset($_GET['id_parent'])) {
	    $type = "collection";
	    $id_par =  getnumeric("id_parent");
	    $requete = "SELECT pain_collection.*, 
                        \"$type\" AS type, 
                        pain_collection.id_$type AS id                 
                        FROM pain_collection, pain_collectionscours";
	    $requete .= " WHERE  pain_collectionscours.id_cours = $id_par 
                          AND pain_collection.id_collection = pain_collectionscours.id_collection";	    
	    $requete .= " ORDER BY nom_collection ASC";
        } else {
	    errmsg("le type collections nécessite un id_parent");
	}
    } else if ($readtype == "unusedcollections") {
	if (isset($_GET['id_parent'])) {
	    $type = "collection";
	    $id_par =  getnumeric("id_parent");
	    $requete = "SELECT nom_$type as label, 
                        id_$type AS id                 
                        FROM pain_$type";
	    $requete .= " WHERE id_$type NOT IN 
                          (SELECT id_$type FROM pain_collectionscours
                          WHERE id_cours = $id_par)
                          AND annee_universitaire = 
                          (SELECT annee_universitaire 
                          FROM pain_sformation, pain_formation, pain_cours
                          WHERE id_cours = $id_par
                          AND pain_cours.id_formation = pain_formation.id_formation
                          AND pain_formation.id_sformation = pain_sformation.id_sformation)";	    
	    $requete .= " ORDER BY nom_collection ASC";
        } else {
	    errmsg("le type unusedcollections nécessite un id_parent");
	}
    } else {
	errmsg("erreur de script (type inconnu)");
    }

   if (isset($_GET["id_parent"])) {
       $id_par = getnumeric("id_parent");
       if (!isset($requete)) {
	   $requete = "SELECT 
                      pain_$type.*,
                       \"$type\" AS type, 
                      id_$type AS id,
                      pain_enseignant.prenom AS prenom_enseignant,
                      pain_enseignant.nom AS nom_enseignant
             FROM pain_$type, pain_enseignant 
             WHERE `$par` = $id_par
             AND pain_$type.id_enseignant = pain_enseignant.id_enseignant
             $order";
       }
       $resultat = $link->query($requete) 
	   or die("Échec de la requête sur la table $type".$requete."\n".$link->error);
       $arr = array();
       while ($element = $resultat->fetch_object()) {
	   $arr[] = $element;
       }
       return $arr;
   } else if (isset($_GET["id"])) {
       $id = getnumeric("id");
       if (!isset($requete)) {
	   $requete = "SELECT \"$type\" AS type,
                      $id AS id,
                      pain_$type.*,
                      pain_enseignant.prenom AS prenom_enseignant,
                      pain_enseignant.nom AS nom_enseignant
             FROM pain_$type, pain_enseignant 
             WHERE `id_$type` = $id
             AND pain_$type.id_enseignant = pain_enseignant.id_enseignant";
       }
       $resultat = $link->query($requete) 
	   or die("Échec de la requête sur la table $type".$requete."\n".$link->error);
       $arr = array();
       while ($element = $resultat->fetch_object()) {
	   $arr[] = $element;
       }
       return $arr;

   } else {
       errmsg("Erreur de script client (ni id ni parent)");
   }
}



if (isset($_GET["annee_universitaire"])) {
    $annee = getnumeric("annee_universitaire");
}

if (isset($_GET["type"])) {
    $readtype = getclean("type");
     
    if ($readtype == "annee") {
	if (isset($_GET["cetteannee"])) {
	    print "[{\"annee_universitaire\": \"$annee\",\"id\": \"$annee\", \"id_annee\": \"$annee\", \"type\":\"annee\"}]";
	} else {
	    print "[";
	    for ($i = $annee - 3; $i < $annee + 3; $i += 1) {
		print "{\"annee_universitaire\": \"$i\",\"id\": \"$i\", \"id_annee\": \"$i\", \"type\":\"annee\"},";
	    }
	    print "{\"annee_universitaire\": \"$i\",\"id\": \"$i\", \"id_annee\": \"$i\", \"type\":\"annee\"}]";
	}
    } else if ($readtype == "declarations") {
	$ids = getlistnumeric("ids");
	if (!peuttransmettredeclarations($ids)) {
	    errmsg("opération non autorisée");
	}
	$resultat = listedeclarations($ids, $annee);
	$arr = array();
	while ($element = $resultat->fetch_object()) {
	    $arr[] = $element;
	}
	print json_encode($arr);
    } else {
	$out = json_get_php($annee, $readtype);
	print json_encode($out);
    }
} else {
    errmsg("erreur de script (type non renseigné)");
}

?>