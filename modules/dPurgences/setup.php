<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CSetupdPurgences extends CSetup {
  
  function __construct() {
    parent::__construct();
    
    $this->mod_name = "dPurgences";
    
    $this->makeRevision("all");
    
    $query = "CREATE TABLE `rpu` (
      `rpu_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT, 
      `sejour_id` INT(11) UNSIGNED NOT NULL, 
      `diag_infirmier` TEXT, 
      `mode_entree` ENUM('6','7','8'), 
      `provenance` ENUM('1','2','3','4','5','8'), 
      `transport` ENUM('perso','ambu','vsab','smur','heli','fo'), 
      `prise_en_charge` ENUM('med','paramed','aucun'), 
      `motif` TEXT, 
      `ccmu` ENUM('1','2','3','4','5','P','D') NOT NULL, 
      `sortie` DATETIME, 
      `mode_sortie` ENUM('6','7','8','9'), 
      `destination` ENUM('1','2','3','4','6','7'), 
      `orientation` ENUM('HDT','HO','SC','SI','REA','UHCD','MED','CHIR','OBST','FUGUE','SCAM','PSA','REO'), 
      KEY `sejour_id` (`sejour_id`),
      KEY `ccmu` (`ccmu`),
      KEY `sortie` (`sortie`),
      PRIMARY KEY (`rpu_id`)) TYPE=MYISAM;";
    $this->addQuery($query);
    
    $this->makeRevision("0.1");
    $query = "ALTER TABLE `rpu` 
			CHANGE `ccmu` `ccmu` ENUM( '1', 'P', '2', '3', '4', '5', 'D' )";
    $this->addQuery($query);
    
    $this->makeRevision("0.11");
    $query = "ALTER TABLE `rpu`
      ADD `radio_debut` DATETIME, 
      ADD `radio_fin` DATETIME;";
    $this->addQuery($query);
    
    $this->makeRevision("0.12");
    $query = "ALTER TABLE `rpu`
      DROP `mode_sortie`,
      DROP `sortie`";
    $this->addQuery($query);
    
    $this->makeRevision("0.13");
    $query = "ALTER TABLE `rpu`
      ADD `mutation_sejour_id` INT(11) UNSIGNED;";
    $this->addQuery($query);

    $this->makeRevision("0.14");
    $query = "ALTER TABLE `rpu`
      ADD `gemsa` ENUM('1','2','3','4','5','6');";
    $this->addQuery($query);
    
    $this->makeRevision("0.15");
    $query = "ALTER TABLE `rpu`
      ADD `type_pathologie` ENUM('C','E','M','P','T');";
    $this->addQuery($query);

    $this->makeRevision("0.16");
    $query = "ALTER TABLE `rpu`
			CHANGE `mode_entree` `mode_entree` ENUM('6','7','8') NOT NULL, 
			CHANGE `transport` `transport` ENUM('perso','perso_taxi','ambu','ambu_vsl','vsab','smur','heli','fo') NOT NULL;";
	  $this->addQuery($query);

    $this->makeRevision("0.17");
    $query = "ALTER TABLE `rpu`
			CHANGE `prise_en_charge` `pec_transport` ENUM('med','paramed','aucun')";
    $this->addQuery($query);

    $this->makeRevision("0.18");
    $query = "ALTER TABLE `rpu`
			ADD `urprov` ENUM('AM','AT','DO','EC','MT','OT','RA','RC','SP','VP'), 
			ADD `urmuta` ENUM('A','D','M','P','X'), 
			ADD `urtrau` ENUM('I','S','T');";
    $this->addQuery($query);

    $this->makeRevision("0.19");
    $query = "ALTER TABLE `rpu`
			ADD `box_id` INT(11) UNSIGNED";
    $this->addQuery($query);
    
    $this->makeRevision("0.20");
    $query = "ALTER TABLE `rpu`
      ADD `sortie_autorisee` ENUM ('0','1') DEFAULT '0',
		  ADD INDEX (`radio_debut`),
		  ADD INDEX (`radio_fin`),
		  ADD INDEX (`mutation_sejour_id`),
		  ADD INDEX (`box_id`);";
    $this->addQuery($query);
    
    $this->makeRevision("0.21");
    $query = "ALTER TABLE `rpu`
      ADD `accident_travail` DATE,
      ADD INDEX (`accident_travail`);";
    $this->addQuery($query);
    
    $this->makeRevision("0.22");
    $query = "ALTER TABLE `rpu` 
			ADD `bio_depart` DATETIME,
			ADD `bio_retour` DATETIME,
			ADD INDEX (`bio_depart`),
			ADD INDEX (`bio_retour`);";
    $this->addQuery($query);
    
    $this->makeRevision("0.23");
      
    $sql = "CREATE TABLE `extract_passages` (
      `extract_passages_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
      `date_extract` DATETIME NOT NULL,
      `debut_selection` DATETIME NOT NULL,
      `fin_selection` DATETIME NOT NULL,
      `date_echange` DATETIME,
      `message` MEDIUMTEXT NOT NULL,
      `message_valide` ENUM ('0','1'),
      `nb_tentatives` INT (11)
    ) TYPE=MYISAM;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `extract_passages` 
      ADD INDEX (`date_extract`),
      ADD INDEX (`debut_selection`),
      ADD INDEX (`fin_selection`),
      ADD INDEX (`date_echange`);";
    $this->addQuery($sql);
      
    $sql = "CREATE TABLE `rpu_passage` (
      `rpu_passage_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
      `rpu_id` INT (11) UNSIGNED NOT NULL,
      `extract_passages_id` INT (11) UNSIGNED NOT NULL
    ) TYPE=MYISAM;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `rpu_passage` 
      ADD INDEX (`rpu_id`),
      ADD INDEX (`extract_passages_id`);";
    $this->addQuery($sql);
		
		$this->makeRevision("0.24");
      
    $sql = "ALTER TABLE `rpu` 
      ADD `specia_att` DATETIME,
      ADD `specia_arr` DATETIME;";
    $this->addQuery($sql);
		
		$sql = "ALTER TABLE `rpu` 
      ADD INDEX (`specia_att`),
      ADD INDEX (`specia_arr`);";
    $this->addQuery($sql);
        
    $this->makeRevision("0.25");
    $this->addPrefQuery("defaultRPUSort", "ccmu");

    $this->makeRevision("0.26");
    $this->addPrefQuery("showMissingRPU", "0");

    $this->mod_version = "0.27";
  }  
}

?>