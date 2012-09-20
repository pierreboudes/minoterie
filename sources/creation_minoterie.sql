-- -*- coding: utf-8 -*-

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

CREATE TABLE IF NOT EXISTS minoterie_departement (
  id_departement mediumint(8) NOT NULL,
  nom_departement varchar(40) CHARACTER SET utf8 COLLATE utf8_swedish_ci NOT NULL,
  url_pain varchar(256) CHARACTER SET utf8 COLLATE utf8_swedish_ci NOT NULL,
  modification timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_departement)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

CREATE TABLE IF NOT EXISTS minoterie_minot (
  id_minot mediumint(8) NOT NULL AUTO_INCREMENT,
  id_departement mediumint(8) NOT NULL,
  id_enseignant mediumint(8) unsigned NOT NULL, /* id_enseignant + id_departement -> login, prenom, nom */
  login varchar(40) COLLATE utf8_swedish_ci DEFAULT NULL,
  prenom varchar(40) COLLATE utf8_swedish_ci NOT NULL,
  nom varchar(40) COLLATE utf8_swedish_ci NOT NULL,
  statut varchar(40) COLLATE utf8_swedish_ci DEFAULT NULL,
  service float unsigned DEFAULT '192',
  email varchar(60) COLLATE utf8_swedish_ci DEFAULT NULL,
  traitee smallint(1) NOT NULL DEFAULT 0,
  modification timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_minot),
  KEY id_departement (id_departement),
  KEY id_enseignant (id_enseignant),
  KEY login (login),
  KEY nom (nom),
  KEY modification (modification)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;


CREATE TABLE IF NOT EXISTS minoterie_intervention (
  id_intervention mediumint(8) NOT NULL AUTO_INCREMENT,
  id_minot mediumint(8) NOT NULL, 
  nom_formation varchar(256) COLLATE utf8_swedish_ci NOT NULL,
  annee_etude tinyint(3) unsigned NOT NULL,
  parfum varchar(40) COLLATE utf8_swedish_ci NOT NULL,
  id_cours mediumint(8) unsigned NOT NULL, /* attention: id_cours + id_minot -> id_intervention */
  semestre tinyint(3) unsigned NOT NULL,
  nom_cours varchar(256) COLLATE utf8_swedish_ci NOT NULL,
  code_geisha varchar(16) COLLATE utf8_swedish_ci DEFAULT NULL,
  cm double unsigned DEFAULT NULL,
  td double unsigned DEFAULT NULL,
  tp double unsigned DEFAULT NULL,
  alt double unsigned DEFAULT NULL,
  modification timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_intervention),
  KEY id_minot (id_minot),
  KEY id_cours (id_cours)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

CREATE TABLE IF NOT EXISTS minoterie_annotation (
  id_annotation mediumint(8) NOT NULL AUTO_INCREMENT,
  id_minot mediumint(8) NOT NULL,
  jsannot text COLLATE utf8_swedish_ci,
  commentaire text COLLATE utf8_swedish_ci,
  complete smallint(1) NOT NULL DEFAULT 0,
  modification timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_annotation),
  KEY id_minot (id_minot)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

CREATE TABLE IF NOT EXISTS minoterie_utilisateur (
  id_utilisateur mediumint(8) NOT NULL AUTO_INCREMENT,
  login varchar(40) COLLATE utf8_swedish_ci DEFAULT NULL,
  id_departement mediumint(8) DEFAULT NULL,
  prenom varchar(40) COLLATE utf8_swedish_ci NOT NULL,
  nom varchar(40) COLLATE utf8_swedish_ci NOT NULL,
  su tinyint(3) unsigned NOT NULL DEFAULT '0',
  modification timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_utilisateur),
  KEY login (login)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

-- pain_cours.id_section nouveau > minoterie_intervention.section nouveau 
ALTER TABLE  `minoterie_intervention` ADD  `section` INT( 3 ) UNSIGNED NOT NULL DEFAULT  '0' AFTER  `code_geisha`;
