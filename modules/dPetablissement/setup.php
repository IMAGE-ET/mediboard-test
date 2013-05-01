<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage dPetablissement
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

class CSetupdPetablissement extends CSetup {
  function __construct() {
    parent::__construct();
    
    $this->mod_type = "core";
    $this->mod_name = "dPetablissement";
    
    $this->makeRevision("all");
    
    $this->makeRevision("0.19");
    $query = "ALTER TABLE `groups_mediboard` 
                ADD INDEX (`service_urgences_id`);";
    $this->addQuery($query);
    
    $this->makeRevision("0.20");
    $query = "ALTER TABLE `groups_mediboard` 
                ADD `finess` INT (9) UNSIGNED ZEROFILL;";
    $this->addQuery($query);
    
    $this->makeRevision("0.21");
    $query = "ALTER TABLE `groups_mediboard` 
                ADD `pharmacie_id` INT (11) UNSIGNED,
                ADD INDEX (`pharmacie_id`);";
    $this->addQuery($query);
    
    $this->makeRevision("0.22");
    $query = "ALTER TABLE `groups_mediboard` 
                ADD `chambre_particuliere` ENUM ('0','1') NOT NULL DEFAULT '0'";
    $this->addQuery($query);
    
    $this->makeRevision("0.23");
    $query = "CREATE TABLE `groups_config` (
                `groups_config_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
                `object_id` INT (11) UNSIGNED,
                `max_comp` INT (11) UNSIGNED,
                `max_ambu` INT (11) UNSIGNED
              ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `groups_config` 
                ADD INDEX (`object_id`);";
    $this->addQuery($query);
    
    $this->makeRevision("0.24");
    $query = "ALTER TABLE `groups_config` 
                ADD `codage_prat` ENUM ('0','1');";
    $this->addQuery($query);
    $query = "ALTER TABLE `groups_config` 
                CHANGE `codage_prat` `codage_prat` ENUM ('0','1') DEFAULT '0';";
    $this->addQuery($query);
    
    $this->makeRevision("0.25");
    $query = "ALTER TABLE `groups_config` 
                ADD `sip_notify_all_actors` ENUM ('0','1') DEFAULT '0',
                ADD `sip_idex_generator` ENUM ('0','1') DEFAULT '0',
                ADD `smp_notify_all_actors` ENUM ('0','1') DEFAULT '0',
                ADD `smp_idex_generator` ENUM ('0','1') DEFAULT '0';";
    $this->addQuery($query);
    
    $this->makeRevision("0.26");
    $query = "ALTER TABLE `etab_externe` 
                ADD INDEX (`nom`),
                ADD INDEX (`raison_sociale`),
                ADD INDEX (`cp`),
                ADD INDEX (`ville`),
                ADD INDEX (`tel`),
                ADD INDEX (`fax`),
                ADD INDEX (`finess`),
                ADD INDEX (`siret`),
                ADD INDEX (`ape`);";
    $this->addQuery($query);
    
    $this->makeRevision("0.27");
    $query = "ALTER TABLE `groups_config` 
                ADD `ipp_range_min` INT (11) UNSIGNED,
                ADD `ipp_range_max` INT (11),
                ADD `nda_range_min` INT (11) UNSIGNED,
                ADD `nda_range_max` INT (11);";
    $this->addQuery($query);
    
    $this->makeRevision("0.28");
    $query = "ALTER TABLE `etab_externe` 
                CHANGE `tel` `tel` VARCHAR (20),
                CHANGE `fax` `fax` VARCHAR (20);";
    $this->addQuery($query);
    $query = "ALTER TABLE `groups_mediboard` 
                CHANGE `tel` `tel` VARCHAR (20),
                CHANGE `fax` `fax` VARCHAR (20),
                CHANGE `tel_anesth` `tel_anesth` VARCHAR (20);";
    $this->addQuery($query);
    
    $this->makeRevision("0.29");
    $query = "ALTER TABLE `groups_mediboard` 
                ADD `mail_apicrypt` VARCHAR (50);";
    $this->addQuery($query);
    $this->makeRevision("0.30");
    
    $query = "ALTER TABLE `groups_mediboard`
                ADD `ean` VARCHAR (30),
                ADD `rcc` VARCHAR (30);";
    $this->addQuery($query);

    $this->makeRevision("0.31");
    $query = "ALTER TABLE `groups_mediboard`
                CHANGE `cp` `cp` VARCHAR( 10 ) NULL DEFAULT NULL;";
    $this->addQuery($query);

    $this->mod_version = "0.32";
  } 
}
