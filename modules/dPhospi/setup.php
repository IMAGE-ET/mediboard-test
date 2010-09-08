<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision$
* @author Romain Ollivier
*/

class CSetupdPhospi extends CSetup {
  
  function __construct() {
    parent::__construct();
    
    $this->mod_name = "dPhospi";
    
    $this->makeRevision("all");
    $query = "CREATE TABLE `service` (
      `service_id` INT NOT NULL AUTO_INCREMENT ,
			`nom` VARCHAR( 50 ) NOT NULL ,
			`description` TEXT,
			PRIMARY KEY ( `service_id` )) TYPE=MyISAM;";
    $this->addQuery($query);
		
    $query = "CREATE TABLE `chambre` (
		  `chambre_id` INT NOT NULL AUTO_INCREMENT ,
			`service_id` INT NOT NULL ,
			`nom` VARCHAR( 50 ) ,
			`caracteristiques` TEXT ,
      PRIMARY KEY ( `chambre_id` ) ,
			INDEX ( `service_id` )) TYPE=MyISAM;";
    $this->addQuery($query);
		
    $query = "CREATE TABLE `lit` (
		  `lit_id` INT NOT NULL AUTO_INCREMENT ,
			`chambre_id` INT NOT NULL,
			`nom` VARCHAR( 50 ) NOT NULL ,
			PRIMARY KEY ( `lit_id` ) ,
			INDEX ( `chambre_id` )) TYPE=MyISAM;";
    $this->addQuery($query);
    
    $this->makeRevision("0.1");
    $query = "CREATE TABLE `affectation` (
		  `affectation_id` INT NOT NULL AUTO_INCREMENT,
			`lit_id` INT NOT NULL ,
			`operation_id` INT NOT NULL ,
			`entree` DATETIME NOT NULL ,
			`sortie` DATETIME NOT NULL ,
			PRIMARY KEY ( `affectation_id` ) ,
			INDEX ( `lit_id` , `operation_id` )) TYPE=MyISAM;";
    $this->addQuery($query);
    
    $this->makeRevision("0.11");
    $query = "ALTER TABLE `affectation` 
		  ADD `confirme` TINYINT DEFAULT '0' NOT NULL,
			ADD `effectue` TINYINT DEFAULT '0' NOT NULL ;";
    $this->addQuery($query);
    
    $this->makeRevision("0.12");
    $query = "ALTER TABLE `affectation` 
		  ADD INDEX ( `entree` );";
    $this->addQuery($query);
		
    $query = "ALTER TABLE `affectation` 
		  ADD INDEX ( `sortie` );";
    $this->addQuery($query);
    
    $this->makeRevision("0.13");
    $query = "ALTER TABLE `affectation` 
		  ADD INDEX ( `operation_id` ) ;";
    $this->addQuery($query);
		
    $query = "ALTER TABLE `affectation` 
		  ADD INDEX ( `lit_id` ) ;";
    $this->addQuery($query);
    
    $this->makeRevision("0.14");
    $this->addDependency("dPplanningOp", "0.38");
    $query = "DELETE affectation.* 
		  FROM affectation
		  LEFT JOIN operations ON affectation.operation_id = operations.operation_id
			WHERE operations.operation_id IS NULL;";
    $this->addQuery($query);
		
    $query = "ALTER TABLE `affectation`
		  ADD `sejour_id` INT UNSIGNED DEFAULT '0' NOT NULL AFTER `operation_id`;";
    $this->addQuery($query);
		
    $query = "ALTER TABLE `affectation` 
		  ADD INDEX (`sejour_id`);";
    $this->addQuery($query);
		
    $query = "UPDATE `affectation`,`operations`
		  SET `affectation`.`sejour_id` = `operations`.`sejour_id`
			WHERE `affectation`.`operation_id` = `operations`.`operation_id`;";
    $this->addQuery($query);
    
    $this->makeRevision("0.15");
    $this->addDependency("dPetablissement", "0.1");
    $query = "ALTER TABLE `service` 
		  ADD `group_id` INT UNSIGNED NOT NULL DEFAULT 1 AFTER `service_id`;";
    $this->addQuery($query);
		
    $query = "ALTER TABLE `service` 
		  ADD INDEX ( `group_id` ) ;";
    $this->addQuery($query);
    
    $this->makeRevision("0.16");
    $query = "ALTER TABLE `affectation` DROP `operation_id`";
    $this->addQuery($query);
    
    $this->makeRevision("0.17");
    $query = "ALTER TABLE `affectation`
		  CHANGE `affectation_id` `affectation_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			CHANGE `lit_id` `lit_id` int(11) unsigned NOT NULL DEFAULT '0',
			CHANGE `confirme` `confirme` enum('0','1') NOT NULL DEFAULT '0',
			CHANGE `effectue` `effectue` enum('0','1') NOT NULL DEFAULT '0',
			CHANGE `sejour_id` `sejour_id` int(11) unsigned NOT NULL DEFAULT '0';";
    $this->addQuery($query);
		
    $query = "ALTER TABLE `chambre` 
		  CHANGE `chambre_id` `chambre_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			CHANGE `service_id` `service_id` int(11) unsigned NOT NULL DEFAULT '0',
			CHANGE `nom` `nom` varchar(255) NOT NULL;";
    $this->addQuery($query);
    $query = "ALTER TABLE `lit` 
		  CHANGE `lit_id` `lit_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			CHANGE `chambre_id` `chambre_id` int(11) unsigned NOT NULL,
			CHANGE `nom` `nom` varchar(255) NOT NULL;";
    $this->addQuery($query);
    $query = "ALTER TABLE `service` 
		  CHANGE `service_id` `service_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			CHANGE `group_id` `group_id` int(11) unsigned NOT NULL DEFAULT '1',
			CHANGE `nom` `nom` varchar(255) NOT NULL;";
    $this->addQuery($query);
    
    $this->makeRevision("0.18");
    $query = "ALTER TABLE `affectation` 
		  ADD `rques` text NULL;";
    $this->addQuery($query);
    
    $this->makeRevision("0.19");
    $query = "ALTER TABLE `affectation` 
		  ADD INDEX ( `confirme` )";
    $this->addQuery($query);
    $query = "ALTER TABLE `affectation` 
		  ADD INDEX ( `effectue` )";
    $this->addQuery($query);
    
    $this->makeRevision("0.20");
    $query= "CREATE TABLE `prestation` (
     `prestation_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT, 
     `group_id` INT(11) UNSIGNED NOT NULL, 
     `nom` VARCHAR(255) NOT NULL, 
     `description` TEXT, 
      PRIMARY KEY (`prestation_id`)) TYPE=MYISAM;";
    $this->addQuery($query);
    
    $this->makeRevision("0.21");
    $this->addPrefQuery("ccam_sejour", "0");

    $this->makeRevision("0.22");
    $query = "ALTER TABLE `service`
			ADD `urgence` ENUM('0','1');";
    $this->addQuery($query);
    
    $this->makeRevision("0.23");
    $query = "CREATE TABLE `observation_medicale` (
      `observation_medicale_id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
      `sejour_id` INT( 11 ) UNSIGNED NOT NULL ,
      `user_id` INT( 11 ) UNSIGNED NOT NULL ,
      `degre` ENUM('low','high') NOT NULL DEFAULT 'low',
      `date` DATETIME NOT NULL ,
      `text` TEXT NULL ,
      INDEX ( `sejour_id`, `user_id` , `degre` , `date` )
    ) ENGINE = MYISAM COMMENT = 'Table des observations médicales';";
    $this->addQuery($query);
    
    $query = "CREATE TABLE `transmission_medicale` (
      `transmission_medicale_id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
      `sejour_id` INT( 11 ) UNSIGNED NOT NULL ,
      `user_id` INT( 11 ) UNSIGNED NOT NULL ,
      `degre` ENUM('low','high') NOT NULL DEFAULT 'low',
      `date` DATETIME NOT NULL ,
      `text` TEXT NULL ,
      INDEX ( `sejour_id`, `user_id` , `degre` , `date` )
    ) ENGINE = MYISAM COMMENT = 'Table des transmissions médicales';";
    $this->addQuery($query);
    
    $this->makeRevision("0.24");
    $query = "CREATE TABLE `categorie_cible_transmission` (
      `categorie_cible_transmission_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
      `libelle` VARCHAR (255) NOT NULL,
      `description` TEXT
    ) TYPE=MYISAM COMMENT = 'Table des catégories de cible de transmission médicales';";
    $this->addQuery($query);
    
    $query = "CREATE TABLE `cible_transmission` (
      `cible_transmission_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
      `categorie_cible_transmission_id` INT (11) UNSIGNED NOT NULL,
      `libelle` VARCHAR (255) NOT NULL,
      `description` TEXT,
      INDEX(`categorie_cible_transmission_id`)
    ) TYPE=MYISAM COMMENT = 'Table des cible de transmission médicales';";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `transmission_medicale` 
      ADD `cible_transmission_id` INT (11) UNSIGNED AFTER user_id;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `transmission_medicale` 
      ADD INDEX (`cible_transmission_id`)";
    $this->addQuery($query);
    
    $this->makeRevision("0.25");
    $query = "DROP TABLE `categorie_cible_transmission`";
    $this->addQuery($query);
    $query = "DROP TABLE `cible_transmission`";
    $this->addQuery($query);
    $query = "ALTER TABLE `transmission_medicale`
      DROP `cible_transmission_id`";
    $this->addQuery($query);
    
        
    $this->makeRevision("0.26");
    $query = "ALTER TABLE `transmission_medicale` 
      ADD `object_id` INT (11) UNSIGNED,
      ADD `object_class` ENUM ('CPrescriptionLineElement','CPrescriptionLineMedicament','CPrescriptionLineComment'),
      ADD INDEX (`object_id`),
      ADD INDEX (`object_class`)";
    $this->addQuery($query);
    
    $this->makeRevision("0.27");
    $query = "ALTER TABLE `transmission_medicale` 
      CHANGE `object_class` `object_class` ENUM ('CPrescriptionLineElement','CPrescriptionLineMedicament','CPrescriptionLineComment','CCategoryPrescription','CAdministration');";
    $this->addQuery($query);
    
    $this->makeRevision("0.28");
    $query = "ALTER TABLE `transmission_medicale` 
   	  CHANGE `object_class` `object_class` ENUM ('CPrescriptionLineElement','CPrescriptionLineMedicament','CPrescriptionLineComment','CCategoryPrescription','CAdministration','CPerfusion');";
    $this->addQuery($query);
    
    $this->makeRevision("0.29");
    $query = "ALTER TABLE `chambre` 
		  ADD `annule` ENUM('0','1') DEFAULT '0'";
    $this->addQuery($query);
    
    $this->makeRevision("0.30");
    $query = "ALTER TABLE `observation_medicale` 
		  CHANGE `degre` `degre` ENUM ('low','high','info') NOT NULL;";
    $this->addQuery($query);
    
    $this->makeRevision("0.31");
    $query = "ALTER TABLE `transmission_medicale` 
			ADD `type` ENUM ('data','action','result');";
    $this->addQuery($query);
    
    $this->makeRevision("0.32");
    $query = "ALTER TABLE `transmission_medicale` 
			ADD `libelle_ATC` TEXT;";
    $this->addQuery($query);
    
    $this->makeRevision("0.33");
    $query = "ALTER TABLE `operations` 
      ADD INDEX (`type_anesth`);";
    $this->addQuery($query);
    $query = "ALTER TABLE `prestation` 
      ADD INDEX (`group_id`);";
    $this->addQuery($query);
    
    $this->makeRevision("0.34");
    $query = "ALTER TABLE `service`
      ADD `uhcd` ENUM('0','1') DEFAULT '0';";
    $this->addQuery($query);

    $this->makeRevision("0.35");
    $query = "ALTER TABLE `service` 
      ADD `responsable_id` INT (11) UNSIGNED NOT NULL,
      ADD `type_sejour` ENUM ('comp','ambu','exte','seances','ssr','psy','urg','consult') DEFAULT 'ambu',
      ADD `cancelled` ENUM ('0','1') DEFAULT '0',
      ADD `hospit_jour` ENUM ('0','1') DEFAULT '0'";
    $this->addQuery($query);
		
    $query = "ALTER TABLE `service` 
      ADD INDEX (`responsable_id`);";
    $this->addQuery($query);
    
    $this->mod_version = "0.36";
  }
}
?>