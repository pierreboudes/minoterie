-- revision 743
ALTER TABLE  `minoterie_intervention` ADD  `declarer` text COLLATE utf8_swedish_ci NOT NULL DEFAULT '' AFTER `alt`;
-- fin revision 743


--- commit 4e29439b1d8e354b7c5c3b9a52139342ba51abbf
CREATE TABLE `codesetape` (
  `id_etape` int(11) NOT NULL AUTO_INCREMENT,
  `code_etape` varchar(7) DEFAULT NULL,
  `intitule` varchar(100) DEFAULT NULL,
  `version` varchar(12) DEFAULT NULL,
  PRIMARY KEY (`id_etape`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=81 ;
--- fin commit
--- commit 0382cfb1089c25bf9a5333b7c444ebf5db7ffc7f
ALTER TABLE  `minoterie_intervention` CHANGE  `code_geisha`  `code_ue` VARCHAR( 16 ) CHARACTER SET utf8 COLLATE utf8_swedish_ci NULL DEFAULT NULL ;
ALTER TABLE  `minoterie_intervention` ADD  `code_etape` VARCHAR( 16 ) CHARACTER SET utf8 COLLATE utf8_swedish_ci NULL DEFAULT NULL  after `code_ue`;
--- fin commit
--- commit  2f5b19e87b7651abfb3757399e666a0719a5b679
CREATE TABLE IF NOT EXISTS minoterie_signature (
  id_signature mediumint(8) NOT NULL AUTO_INCREMENT,
  id_minot mediumint(8) NOT NULL,
  login varchar(40) COLLATE utf8_swedish_ci NOT NULL,
  prenom varchar(40) COLLATE utf8_swedish_ci NOT NULL,
  nom varchar(40) COLLATE utf8_swedish_ci NOT NULL,
  modification timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_signature),
  KEY id_minot (id_minot)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;
--- fin commit
--- commit
ALTER TABLE `minoterie_intervention` ADD
  `referentiel` double unsigned DEFAULT NULL
  AFTER `alt`;
ALTER TABLE `minoterie_intervention` ADD
   `prp` double unsigned DEFAULT NULL
  AFTER `alt`;
--- fin commit
--- commit
ALTER TABLE `minoterie_minot` ADD
      `section` int(3) unsigned NOT NULL DEFAULT '0'
  AFTER `statut`;
--- fin commit


SELECT login, id_enseignant, u.id_minot, t.modification AS
    modification_minot, traitee, definitif, minoterie_departement . *
    , nom, prenom, derniere_signature FROM (( ( SELECT id_enseignant,
    id_departement, max( modification ) AS modification FROM
    minoterie_minot WHERE login LIKE 'boudes' GROUP BY id_enseignant,
    id_departement ) AS t NATURAL JOIN ( minoterie_minot AS u ) ) JOIN
    minoterie_departement ON u.id_departement =
    minoterie_departement.id_departement ) LEFT JOIN ( SELECT
    minoterie_signature.id_minot, max(modification) AS
    derniere_signature FROM minoterie_signature GROUP BY id_minot ) AS
    s ON u.id_minot = s.id_minot
