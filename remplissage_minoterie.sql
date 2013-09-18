-- -*- coding: utf-8 -*-

INSERT INTO `minoterie_departement` (`id_departement`, `nom_departement`, `url_pain`)
VALUES (1, 'chimie', '/chimie/pain/');
INSERT INTO `minoterie_departement` (`id_departement`, `nom_departement`, `url_pain`)
VALUES (2, 'informatique', '/informatique/pain/');
INSERT INTO `minoterie_departement` (`id_departement`, `nom_departement`, `url_pain`)
VALUES (3, 'mathématiques', '/mathematiques/pain/');
INSERT INTO `minoterie_departement` (`id_departement`, `nom_departement`, `url_pain`)
VALUES (4, 'physique', '/physique/pain/');
INSERT INTO `minoterie_departement` (`id_departement`, `nom_departement`, `url_pain`)
VALUES (5, 'lv', '/lv/pain/');
INSERT INTO `minoterie_departement` (`id_departement`, `nom_departement`, `url_pain`)
VALUES (6, 'com', '/com/pain/');
INSERT INTO `minoterie_departement` (`id_departement`, `nom_departement`, `url_pain`)
VALUES (7, 'commun', '/commun/pain/');
INSERT INTO `minoterie_utilisateur` (`login`,`prenom`, `nom`, `id_departement`, `su`) VALUES ('boudes', 'Pierre', 'Boudes', 2, 1);
INSERT INTO `minoterie_utilisateur` (`login`,`prenom`, `nom`, `id_departement`, `su`) VALUES ('boudes', 'Pierre', 'Boudes', 5, 1);
INSERT INTO `minoterie_utilisateur` (`login`,`prenom`, `nom`, `id_departement`, `su`) VALUES ('boudes', 'Pierre', 'Boudes', 7, 1);

-- Rev > 116
-- Pour dire si les déclarations trasnmises sont prévisionnelles ou
-- définitives
INSERT INTO `minoterie_config` (`configuration`, `valeur`) VALUES ('ETAPE_DECLARATIONS', 'definitives');
INSERT INTO `minoterie_config` (`configuration`, `valeur`) VALUES ('DATE_ETAPE', 'Mai 2013');


--- sous git > e7179ccf50b39ee5d339580a817b761714013cbd
INSERT INTO `minoterie_config` (`configuration`, `valeur`, `aide`) VALUES ('ANNEE_COURANTE', '2013', 'Incrire le debut de l''annee en cours, par exemple inscire 2013 pour l''annee 2013-2014');
UPDATE  `commun`.`minoterie_config` SET  `valeur` =  'previsionnelles',
`aide` =  'inscrire definitives (sans accent) si le traitement actuel concerne les declarations definitives, ou n''importe quoi d''autres sinon') WHERE  `minoterie_config`.`configuration` =  'ETAPE_DECLARATIONS';
UPDATE  `commun`.`minoterie_config` SET  `valeur` =  'Septembre 2013',
`aide` =  'ce texte sera affiche aux utilisateurs (sans autre effet)') WHERE  `minoterie_config`.`configuration` =  'DATE_ETAPE';
