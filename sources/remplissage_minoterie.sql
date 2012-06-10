-- -*- coding: utf-8 -*-

INSERT INTO `minoterie_departement` (`id_departement`, `nom_departement`, `url_pain`) 
VALUES (1, 'chimie', 'http://servens-galilee.univ-paris13.fr/chimie/pain/');
INSERT INTO `minoterie_departement` (`id_departement`, `nom_departement`, `url_pain`) 
VALUES (2, 'informatique', 'http://servens-galilee.univ-paris13.fr/informatique/pain/');
INSERT INTO `minoterie_departement` (`id_departement`, `nom_departement`, `url_pain`) 
VALUES (3, 'mathématiques', 'http://servens-galilee.univ-paris13.fr/mathematiques/pain/');
INSERT INTO `minoterie_departement` (`id_departement`, `nom_departement`, `url_pain`) 
VALUES (4, 'physique', 'http://servens-galilee.univ-paris13.fr/physique/pain/');
INSERT INTO `minoterie_departement` (`id_departement`, `nom_departement`, `url_pain`) 
VALUES (5, 'un testeur', 'http://localhost:8888/mathpain/');

INSERT INTO `minoterie_utilisateur` (`login`,`prenom`, `nom`, `id_departement`, `su`) VALUES ('boudes', 'Pierre', 'Boudes', 2, 1);
