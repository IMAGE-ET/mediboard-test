<?php

/**
 * $Id$
 *
 * @category Hprimsante
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

/**
 * Classe de setup du module HprimSante
 * Class CSetuphprimsante
 */
class CSetuphprimsante extends CSetup {
  /**
   * @see parent::__construct
   */
  function __construct() {
    parent::__construct();

    $this->mod_name = "hprimsante";

    $this->makeRevision("all");
    $this->makeRevision("0.1");

    $query = "CREATE TABLE `exchange_hprimsante` (
              `exchange_hprimsante_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
              `version` VARCHAR(255),
              `nom_fichier` VARCHAR(255),
              `group_id` INT (11) UNSIGNED NOT NULL DEFAULT '0',
              `date_production` DATETIME NOT NULL,
              `sender_id` INT (11) UNSIGNED,
              `sender_class` ENUM ('CSenderFTP','CSenderSOAP','CSenderMLLP','CSenderFileSystem'),
              `receiver_id` INT (11) UNSIGNED,
              `type` VARCHAR (255),
              `sous_type` VARCHAR (255),
              `date_echange` DATETIME,
              `message_content_id` INT (11) UNSIGNED,
              `acquittement_content_id` INT (11) UNSIGNED,
              `statut_acquittement` VARCHAR (255),
              `message_valide` ENUM ('0','1') DEFAULT '0',
              `acquittement_valide` ENUM ('0','1') DEFAULT '0',
              `id_permanent` VARCHAR (255),
              `object_id` INT (11) UNSIGNED,
              `object_class` ENUM ('CPatient','CSejour','CMedecin'),
              `code` VARCHAR(255) NOT NULL,
              `identifiant_emetteur` VARCHAR (255),
              `reprocess` TINYINT (4) UNSIGNED DEFAULT '0'
          ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);

    $query = "ALTER TABLE `exchange_hprimsante`
                ADD INDEX (`group_id`),
                ADD INDEX (`date_production`),
                ADD INDEX (`sender_id`),
                ADD INDEX (`receiver_id`),
                ADD INDEX (`date_echange`),
                ADD INDEX (`message_content_id`),
                ADD INDEX (`acquittement_content_id`),
                ADD INDEX (`object_id`);";
    $this->addQuery($query);

    $this->makeRevision("0.2");

    $query = "CREATE TABLE `receiver_hprimsante` (
              `receiver_hprimsante_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
              `nom` VARCHAR (255) NOT NULL,
              `libelle` VARCHAR (255),
              `group_id` INT (11) UNSIGNED NOT NULL DEFAULT '0',
              `actif` ENUM ('0','1') NOT NULL DEFAULT '0',
              `OID` VARCHAR (255),
              `synchronous` ENUM ('0','1') NOT NULL DEFAULT '1'
            ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);

    $query = "ALTER TABLE `receiver_hprimsante`
              ADD INDEX (`group_id`);";
    $this->addQuery($query);

    $this->makeRevision("0.3");

    $query = "CREATE TABLE `hprimsante_config` (
                `hprimsante_config_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
                `encoding` ENUM ('UTF-8','ISO-8859-1') DEFAULT 'UTF-8',
                `strict_segment_terminator` ENUM ('0','1') DEFAULT '0',
                `segment_terminator` ENUM ('CR','LF','CRLF'),
                `sender_id` INT (11) UNSIGNED,
                `sender_class` ENUM ('CSenderFTP','CSenderSOAP','CSenderMLLP','CSenderFileSystem'),
                `action` ENUM ('IPP_NDA','Patient','Sejour','Patient_Sejour') DEFAULT 'IPP_NDA'
              )/*! ENGINE=MyISAM */;";
    $this->addQuery($query);

    $query = "ALTER TABLE `hprimsante_config`
                ADD INDEX (`sender_id`);";
    $this->addQuery($query);

    $this->makeRevision("0.4");

    $query = "CREATE TABLE `receiver_hprimsante_config` (
                `receiver_hprimsante_config_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
                `object_id` INT (11) UNSIGNED,
                `ADM_version` ENUM ('2.1','2.2','2.3','2.4') DEFAULT '2.1',
                `ADM_sous_type` ENUM ('C','L', 'R') DEFAULT 'C'
              )/*! ENGINE=MyISAM */;";
    $this->addQuery($query);

    $query = "ALTER TABLE `receiver_hprimsante_config`
                ADD INDEX (`object_id`);";
    $this->addQuery($query);

    $this->mod_version = "0.5";
  }
}
