<?php 
/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage reservation
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

class CSetupreservation extends CSetup {
  function __construct() {
    parent::__construct();
    
    $this->mod_name = "reservation";
    $this->makeRevision("all");
    
    $this->makeRevision("0.01");
    
    $query = "CREATE TABLE `commentaire_planning` (
      `commentaire_planning_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
      `salle_id` INT (11) UNSIGNED,
      `libelle` VARCHAR (255) NOT NULL,
      `commentaire` TEXT,
      `debut` DATETIME NOT NULL,
      `fin` DATETIME NOT NULL
    ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `commentaire_planning` 
      ADD INDEX (`salle_id`),
      ADD INDEX (`debut`),
      ADD INDEX (`fin`);";
    $this->addQuery($query);
    
    $this->makeRevision("0.02");
    $query = "ALTER TABLE `commentaire_planning` 
      ADD `color` CHAR (6) DEFAULT 'DDDDDD' AFTER `commentaire`;";
    $this->addQuery($query);
    
    $this->makeRevision("0.03");
    $query = "CREATE TABLE `examen_operation` (
      `examen_operation_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
      `completed` ENUM ('0','1') DEFAULT '0',
      `acheminement` ENUM ('courrier','fax','autre'),
      `labo` TEXT,
      `groupe_rh` ENUM ('0','1') DEFAULT '0',
      `flacons` INT (11) UNSIGNED,
      `auto_transfusion` ENUM ('0','1') DEFAULT '0',
      `ecg` ENUM ('0','1') DEFAULT '0',
      `radio_thorax` ENUM ('0','1') DEFAULT '0',
      `radios_autres` TEXT,
      `physio_preop` TEXT,
      `physio_postop` TEXT
    ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $this->mod_version = "0.04";
  }
}
