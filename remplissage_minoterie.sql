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
