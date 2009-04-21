<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPurgences
 *	@version $Revision$
 *  @author Sébastien Fillonneau
 */

class CSetupdPurgences extends CSetup {
  
  function __construct() {
    parent::__construct();
    
    $this->mod_name = "dPurgences";
    
    $this->makeRevision("all");
    
    $sql = "CREATE TABLE `rpu` (
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
    $this->addQuery($sql);
    
    $this->makeRevision("0.1");
    $sql = "ALTER TABLE `rpu` CHANGE `ccmu` `ccmu` ENUM( '1', 'P', '2', '3', '4', '5', 'D' )";
    $this->addQuery($sql);
    
    $this->makeRevision("0.11");
    $sql = "ALTER TABLE `rpu`
            ADD `radio_debut` DATETIME, 
            ADD `radio_fin` DATETIME;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.12");
    $sql = "ALTER TABLE `rpu`
            DROP `mode_sortie`,
            DROP `sortie`";
    $this->addQuery($sql);
    
    $this->makeRevision("0.13");
    $sql = "ALTER TABLE `rpu`
            ADD `mutation_sejour_id` INT(11) UNSIGNED;";
    $this->addQuery($sql);

    $this->makeRevision("0.14");
    $sql = "ALTER TABLE `rpu`
            ADD `gemsa` ENUM('1','2','3','4','5','6');";
    $this->addQuery($sql);
    
    $this->makeRevision("0.15");
    $sql = "ALTER TABLE `rpu`
            ADD `type_pathologie` ENUM('C','E','M','P','T');";
    $this->addQuery($sql);

    $this->makeRevision("0.16");
    $sql = "ALTER TABLE `rpu`
						CHANGE `mode_entree` `mode_entree` ENUM('6','7','8') NOT NULL, 
						CHANGE `transport` `transport` ENUM('perso','perso_taxi','ambu','ambu_vsl','vsab','smur','heli','fo') NOT NULL;";
    $this->addQuery($sql);

    $this->makeRevision("0.17");
    $sql = "ALTER TABLE `rpu`
						CHANGE `prise_en_charge` `pec_transport` ENUM('med','paramed','aucun')";
    $this->addQuery($sql);

    $this->makeRevision("0.18");
    $sql = "ALTER TABLE `rpu`
						ADD `urprov` ENUM('AM','AT','DO','EC','MT','OT','RA','RC','SP','VP'), 
						ADD `urmuta` ENUM('A','D','M','P','X'), 
						ADD `urtrau` ENUM('I','S','T');";
    $this->addQuery($sql);

    $this->makeRevision("0.19");
    $sql = "ALTER TABLE `rpu`
						ADD `box_id` INT(11) UNSIGNED";
    $this->addQuery($sql);
    
    $this->mod_version = "0.20";
  }  
}

?>