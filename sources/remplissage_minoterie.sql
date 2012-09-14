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
VALUES (5, 'lv', 'http://servens-galilee.univ-paris13.fr/lv/pain/');
INSERT INTO `minoterie_departement` (`id_departement`, `nom_departement`, `url_pain`) 
VALUES (6, 'com', 'http://servens-galilee.univ-paris13.fr/com/pain/');
INSERT INTO `minoterie_departement` (`id_departement`, `nom_departement`, `url_pain`) 
VALUES (7, 'commun', 'http://servens-galilee.univ-paris13.fr/commun/pain/');
INSERT INTO `minoterie_utilisateur` (`login`,`prenom`, `nom`, `id_departement`, `su`) VALUES ('boudes', 'Pierre', 'Boudes', 2, 1);
INSERT INTO `minoterie_utilisateur` (`login`,`prenom`, `nom`, `id_departement`, `su`) VALUES ('boudes', 'Pierre', 'Boudes', 5, 1);
INSERT INTO `minoterie_utilisateur` (`login`,`prenom`, `nom`, `id_departement`, `su`) VALUES ('boudes', 'Pierre', 'Boudes', 7, 1);
