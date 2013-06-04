<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage soins
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CSetupsoins extends CSetup {

  function __construct() {
    parent::__construct();

    $this->mod_name = 'soins';

    $this->makeRevision('all');
    
    $this->makeRevision("0.1");
    
    $query = "CREATE TABLE `sejour_task` (
      `sejour_task_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
      `sejour_id` INT (11) UNSIGNED NOT NULL,
      `description` TEXT NOT NULL,
      `realise` ENUM ('0','1') DEFAULT '0',
      `resultat` TEXT
      ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `sejour_task` ADD INDEX (`sejour_id`);";
    $this->addQuery($query);  
    
    $this->makeRevision("0.11");
    $query = "CREATE TABLE `ressource_soin` (
              `ressource_soin_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
              `libelle` TEXT NOT NULL,
              `cout` FLOAT
            ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $query = "CREATE TABLE `indice_cout` (
              `indice_cout_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
              `nb` INT (11) NOT NULL,
              `ressource_soin_id` INT (11) UNSIGNED NOT NULL,
              `element_prescription_id` INT (11) UNSIGNED NOT NULL
             ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `indice_cout` 
              ADD INDEX (`ressource_soin_id`),
              ADD INDEX (`element_prescription_id`);";
    $this->addQuery($query);
    
    $this->makeRevision("0.12");
    $query = "ALTER TABLE `ressource_soin` 
              CHANGE `libelle` `libelle` VARCHAR (255) NOT NULL;";
    $this->addQuery($query);
    
    $this->makeRevision("0.13");
    $query = "ALTER TABLE `ressource_soin`
              ADD `code` VARCHAR (255) NOT NULL;";
    $this->addQuery($query);
    
    $this->makeRevision("0.14");
    $this->moveConf("dPprescription CPrescription max_time_modif_suivi_soins", "soins max_time_modif_suivi_soins");

    $this->makeRevision("0.15");
    $query = "ALTER TABLE `sejour_task`
      ADD `consult_id` INT (11) UNSIGNED,
      ADD INDEX (`consult_id`)";
    $this->addQuery($query);
    
    $this->mod_version = '0.16';
  }
}
