<?php

/**
 * maternite
 *  
 * @category maternite
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

class CSetupmaternite extends CSetup {
  
  function __construct() {
    parent::__construct();
    
    $this->mod_name = "maternite";
    
    $this->makeRevision("all");
    $query = "CREATE TABLE `naissance` (
      `naissance_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
      `operation_id` INT (11) UNSIGNED,
      `grossesse_id` INT (11) UNSIGNED,
      `sejour_enfant_id` INT (11) UNSIGNED NOT NULL,
      `hors_etab` ENUM ('0','1') DEFAULT '0',
      `heure` TIME NOT NULL,
      `rang` INT (11) UNSIGNED NOT NULL
    ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `naissance`
      ADD INDEX (`operation_id`),
      ADD INDEX (`grossesse_id`),
      ADD INDEX (`sejour_enfant_id`);";
    $this->addQuery($query);
    
    $query = "CREATE TABLE `grossesse` (
      `grosssesse_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
      `parturiente_id` INT (11) UNSIGNED NOT NULL,
      `terme_prevu` DATE NOT NULL
    ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $this->makeRevision("0.01");
    $query = "ALTER TABLE `grossesse`
      ADD `active` ENUM ('0','1') DEFAULT '1';";
    $this->addQuery($query);
    
    $this->makeRevision("0.02");
    $query = "ALTER TABLE `naissance`
      CHANGE `heure` `heure` TIME,
      CHANGE `rang` `rang` INT (11) UNSIGNED;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `grossesse`
      ADD `date_dernieres_regles` DATE;";
    $this->addQuery($query);
    
    $this->makeRevision("0.03");
    $query = "ALTER TABLE `grossesse`
      CHANGE `grosssesse_id` `grossesse_id` INT (11) UNSIGNED";
    $this->addQuery($query);
    
    $this->makeRevision("0.04");
    $query = "ALTER TABLE `grossesse`
      CHANGE `grossesse_id` `grossesse_id` INT (11) UNSIGNED NOT NULL auto_increment";
    $this->addQuery($query);
    
    $this->makeRevision("0.05");
    $query = "ALTER TABLE `naissance`
      ADD `sejour_maman_id` INT (11) UNSIGNED NOT NULL,
      ADD INDEX (`sejour_maman_id`)";
    $this->addQuery($query);

    $this->makeRevision("0.06");
    $query = "ALTER TABLE `grossesse` 
      ADD `multiple` ENUM ('0','1') DEFAULT '0',
      ADD INDEX (`parturiente_id`),
      ADD INDEX (`terme_prevu`),
      ADD INDEX (`date_dernieres_regles`);";
    $this->addQuery($query);
    
    $this->makeRevision("0.07");
    $query = "ALTER TABLE `grossesse` 
      ADD `allaitement_maternel` ENUM ('0','1') DEFAULT '1';";
    $this->addQuery($query);
    
    $this->mod_version = "0.08";
  }
}
?>