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
--- commit
ALTER TABLE  `minoterie_intervention` CHANGE  `code_geisha`  `code_ue` VARCHAR( 16 ) CHARACTER SET utf8 COLLATE utf8_swedish_ci NULL DEFAULT NULL ;
ALTER TABLE  `minoterie_intervention` ADD  `code_etape` VARCHAR( 16 ) CHARACTER SET utf8 COLLATE utf8_swedish_ci NULL DEFAULT NULL  after `code_ue`;
