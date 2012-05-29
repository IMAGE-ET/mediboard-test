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
			PRIMARY KEY ( `service_id` )) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
		
    $query = "CREATE TABLE `chambre` (
		  `chambre_id` INT NOT NULL AUTO_INCREMENT ,
			`service_id` INT NOT NULL ,
			`nom` VARCHAR( 50 ) ,
			`caracteristiques` TEXT ,
      PRIMARY KEY ( `chambre_id` ) ,
			INDEX ( `service_id` )) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
		
    $query = "CREATE TABLE `lit` (
		  `lit_id` INT NOT NULL AUTO_INCREMENT ,
			`chambre_id` INT NOT NULL,
			`nom` VARCHAR( 50 ) NOT NULL ,
			PRIMARY KEY ( `lit_id` ) ,
			INDEX ( `chambre_id` )) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $this->makeRevision("0.1");
    $query = "CREATE TABLE `affectation` (
		  `affectation_id` INT NOT NULL AUTO_INCREMENT,
			`lit_id` INT NOT NULL ,
			`operation_id` INT NOT NULL ,
			`entree` DATETIME NOT NULL ,
			`sortie` DATETIME NOT NULL ,
			PRIMARY KEY ( `affectation_id` ) ,
			INDEX ( `lit_id` , `operation_id` )) /*! ENGINE=MyISAM */;";
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
      PRIMARY KEY (`prestation_id`)) /*! ENGINE=MyISAM */;";
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
    ) /*! ENGINE=MyISAM */ COMMENT = 'Table des observations médicales';";
    $this->addQuery($query);
    
    $query = "CREATE TABLE `transmission_medicale` (
      `transmission_medicale_id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
      `sejour_id` INT( 11 ) UNSIGNED NOT NULL ,
      `user_id` INT( 11 ) UNSIGNED NOT NULL ,
      `degre` ENUM('low','high') NOT NULL DEFAULT 'low',
      `date` DATETIME NOT NULL ,
      `text` TEXT NULL ,
      INDEX ( `sejour_id`, `user_id` , `degre` , `date` )
    ) /*! ENGINE=MyISAM */ COMMENT = 'Table des transmissions médicales';";
    $this->addQuery($query);
    
    $this->makeRevision("0.24");
    $query = "CREATE TABLE `categorie_cible_transmission` (
      `categorie_cible_transmission_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
      `libelle` VARCHAR (255) NOT NULL,
      `description` TEXT
    ) /*! ENGINE=MyISAM */ COMMENT = 'Table des catégories de cible de transmission médicales';";
    $this->addQuery($query);
    
    $query = "CREATE TABLE `cible_transmission` (
      `cible_transmission_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
      `categorie_cible_transmission_id` INT (11) UNSIGNED NOT NULL,
      `libelle` VARCHAR (255) NOT NULL,
      `description` TEXT,
      INDEX(`categorie_cible_transmission_id`)
    ) /*! ENGINE=MyISAM */ COMMENT = 'Table des cible de transmission médicales';";
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
    
    
    $this->makeRevision("0.36");
    $query = "CREATE TABLE `modele_etiquette` (
              `modele_etiquette_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
              `nom` VARCHAR (255),
              `texte` TEXT,
              `largeur_page` FLOAT DEFAULT '21',
              `hauteur_page` FLOAT DEFAULT '29.7',
              `nb_lignes` INT (11) DEFAULT '8',
              `nb_colonnes` INT (11) DEFAULT '4',
              `marge_horiz` FLOAT DEFAULT '0.3',
              `marge_vert` FLOAT DEFAULT '1.3',
              `hauteur_ligne` FLOAT DEFAULT '8',
              `object_id` INT (11) DEFAULT NULL,
              `object_class` VARCHAR (255) DEFAULT NULL,
              `font` TEXT DEFAULT NULL
            ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);

    $this->makeRevision("0.37");
    $query = "ALTER TABLE `service` 
      CHANGE `responsable_id` `responsable_id` INT (11) UNSIGNED";
    $this->addQuery($query);
    
    $this->makeEmptyRevision("0.38");
    
    $this->makeRevision("0.39");
    $query = "ALTER TABLE `chambre`
              ADD `lits_alpha` ENUM ('0','1') DEFAULT '0' AFTER `caracteristiques`;";
    $this->addQuery($query);
    
		$this->makeRevision("0.40");
		$query = "ALTER TABLE `modele_etiquette`
		  ADD texte_2 TEXT AFTER texte,
			ADD texte_3 TEXT AFTER texte_2,
			ADD texte_4 TEXT AFTER texte_3;";
	  $this->addQuery($query);
		
	  $this->makeRevision("0.41");
	  $query = "ALTER TABLE `modele_etiquette`
	    ADD `group_id` INT (11) UNSIGNED;";
	  $this->addQuery($query);
	  
	  $query = "UPDATE `modele_etiquette`
	    SET `group_id` = '".CGroups::loadCurrent()->_id."'
	    WHERE `group_id` IS NULL;";
	  $this->addQuery($query);
	  
	  $this->makeRevision("0.42");
	  $query = "ALTER TABLE `modele_etiquette`
	    ADD `show_border` ENUM ('0','1') DEFAULT '0';";
	  $this->addQuery($query);
    
    $this->makeRevision("0.43");
    $query = "ALTER TABLE `service` 
      CHANGE `urgence` `urgence` ENUM ('0','1') DEFAULT '0',
      ADD `externe` ENUM ('0','1') DEFAULT '0';";
    $this->addQuery($query);

    $this->makeRevision("0.44");
    $query = "ALTER TABLE  `chambre` ADD  `plan_x` INT(11) NULL ,
      ADD  `plan_y` INT(11) NULL;";
    $this->addQuery($query);

    $this->makeRevision("0.45");
    $query = "CREATE TABLE `uf` (
              `uf_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
              `group_id` INT (11) UNSIGNED NOT NULL,
              `code` VARCHAR (255) NOT NULL,
              `libelle` VARCHAR (255) NOT NULL,
              `description` TEXT
               ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    $query = "ALTER TABLE `uf` 
              ADD INDEX (`group_id`);";
    $this->addQuery($query);
    $query = "CREATE TABLE `affectation_uf` (
              `affectation_uf_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
              `uf_id` INT (11) UNSIGNED NOT NULL,
              `debut` DATETIME,
              `fin` DATETIME,
              `object_id` INT (11) UNSIGNED NOT NULL,
              `object_class` ENUM ('CSejour','Clit','CMediuser') NOT NULL
              ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    $query = "ALTER TABLE `affectation_uf` 
              ADD INDEX (`uf_id`),
              ADD INDEX (`debut`),
              ADD INDEX (`fin`),
              ADD INDEX (`object_id`);";
    $this->addQuery($query);
     
    $this->makeRevision("0.46");
    $query = "ALTER TABLE  `prestation`   
                ADD  `code` VARCHAR(12)";
    $this->addQuery($query);
    
    $this->makeRevision("0.47");
    
    $query = "ALTER TABLE  `affectation`   
                ADD `uf_hebergement_id` INT (11) UNSIGNED,
                ADD `uf_medicale_id` INT (11) UNSIGNED,
                ADD `uf_soins_id` INT (11) UNSIGNED;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE  `affectation`   
                ADD INDEX (`uf_hebergement_id`),
                ADD INDEX (`uf_medicale_id`),
                ADD INDEX (`uf_soins_id`);";
    $this->addQuery($query);
    
    $this->makeRevision("0.48");
    
    $query = "ALTER TABLE `affectation_uf`
                DROP `debut`,
                DROP `fin`,
                CHANGE `object_class` `object_class` ENUM ('CService','CChambre','CLit','CMediusers','CFunctions') NOT NULL;";
    $this->addQuery($query);
    
    $this->makeRevision("0.49");
    
    $query = "CREATE TABLE `movement` (
                `movement_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
                `movement_type` ENUM ('PADM','ADMI','MUTA','SATT','SORT') NOT NULL,
                `original_trigger_code` CHAR (3),
                `last_update` DATETIME NOT NULL,
                `object_id` INT (11) UNSIGNED NOT NULL,
                `object_class` VARCHAR (80) NOT NULL,
                `cancel` ENUM ('0','1') DEFAULT '0'
              ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `movement` 
                ADD INDEX (`movement_type`),
                ADD INDEX (`original_trigger_code`),
                ADD INDEX (`last_update`),
                ADD INDEX (`object_id`),
                ADD INDEX (`object_class`);";
    $this->addQuery($query);
    
    $this->makeRevision("0.50");
    
    $query = "ALTER TABLE `lit` 
              ADD `nom_complet` VARCHAR (255);";
    $this->addQuery($query);
    
    $this->makeRevision("0.51");
    
    $query = "CREATE TABLE `prestation_journaliere` (
               `prestation_journaliere_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
               `nom` VARCHAR (255) NOT NULL,
               `group_id` INT (11) UNSIGNED NOT NULL
              ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `prestation_journaliere` 
      ADD INDEX (`group_id`);";
    $this->addQuery($query);
    
    $query = "CREATE TABLE `prestation_ponctuelle` (
                `prestation_ponctuelle_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
                `nom` VARCHAR (255) NOT NULL,
                `group_id` INT (11) UNSIGNED NOT NULL
              ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `prestation_ponctuelle` 
      ADD INDEX (`group_id`);";
    $this->addQuery($query);
    
    $query = "CREATE TABLE `item_prestation` (
                `item_prestation_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
                `nom` VARCHAR (255) NOT NULL,
                `rank` INT (11) UNSIGNED DEFAULT '1',
                `object_id` INT (11) UNSIGNED NOT NULL,
                `object_class` ENUM ('CPrestationPonctuelle','CPrestationJournaliere')
              ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `item_prestation` 
      ADD INDEX (`object_id`);";
    $this->addQuery($query);
    
    $query = "CREATE TABLE `item_liaison` (
               `item_liaison_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
               `affectation_id` INT (11) UNSIGNED NOT NULL,
               `item_prestation_id` INT (11) UNSIGNED NOT NULL
              ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `item_liaison` 
      ADD INDEX (`affectation_id`),
      ADD INDEX (`item_prestation_id`);";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `item_liaison` 
      ADD `item_prestation_realise_id` INT (11) UNSIGNED,
      ADD `date` DATE,
      ADD `quantite` INT (11) DEFAULT '0';";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `item_liaison` 
      ADD INDEX (`item_prestation_realise_id`),
      ADD INDEX (`date`);";
    $this->addQuery($query);
    
    $this->makeRevision("0.52");
    
    $query = "ALTER TABLE `movement` 
                ADD `sejour_id` INT (11) UNSIGNED NOT NULL,
                ADD `affectation_id` INT (11) UNSIGNED,
                DROP `object_id`,
                DROP `object_class`;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `movement` 
                ADD INDEX (`sejour_id`),
                ADD INDEX (`affectation_id`);";
    $this->addQuery($query);
    
    $this->makeRevision("0.53");
    $query = "ALTER TABLE `affectation` 
      ADD `parent_affectation_id` INT (11) UNSIGNED AFTER `sortie`;";
    $this->addQuery($query);
    
    $this->makeRevision("0.54");
    $query = "CREATE TABLE `secteur` (
              `secteur_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
              `group_id` INT (11) UNSIGNED NOT NULL,
              `nom` VARCHAR (255) NOT NULL,
              `description` TEXT
              ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `secteur` 
      ADD INDEX (`group_id`);";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `service` 
      ADD `secteur_id` INT (11) UNSIGNED AFTER `service_id`;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `service` 
      ADD INDEX (`secteur_id`);";
    $this->addQuery($query);
    
    $this->makeRevision("0.55");
    $query = "CREATE TABLE `lit_liaison_item` (
              `lit_liaison_item_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
              `lit_id` INT (11) UNSIGNED NOT NULL,
              `item_prestation_id` INT (11) UNSIGNED NOT NULL
              ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `lit_liaison_item` 
      ADD INDEX (`lit_id`),
      ADD INDEX (`item_prestation_id`);";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `item_liaison` 
      ADD `sejour_id` INT (11) UNSIGNED NOT NULL;";
    $this->addQuery($query);
    
    $query = "UPDATE `item_liaison`
      SET `sejour_id` = ( SELECT `sejour_id`
                          FROM `affectation`
                          WHERE `affectation`.`affectation_id` = `item_liaison`.`affectation_id`
                          LIMIT 1 );";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `item_liaison`
      DROP `affectation_id`";
    $this->addQuery($query);
    
    $this->makeRevision("0.56");
    $this->addDependency("dPplanningOp", "1.33");
    
    $query = "UPDATE `sejour`, `affectation`
                SET `sejour`.`confirme` = '1'
                WHERE `affectation`.`sejour_id` = `sejour`.`sejour_id`
                AND `affectation`.`confirme` = '1';";
    
    $this->makeRevision("0.57");
    $query = "ALTER TABLE `item_liaison`
      CHANGE `item_prestation_id` `item_prestation_id` INT (11) UNSIGNED DEFAULT NULL";
    
    $this->makeRevision("0.58");
    $query = "ALTER TABLE `modele_etiquette`
      ADD `text_align` ENUM ('top','middle','bottom') DEFAULT 'top'";
    $this->addQuery($query);
    
    $this->makeRevision("0.59");
    $query = "ALTER TABLE `movement` 
              ADD `start_of_movement` DATETIME;";
    $this->addQuery($query);           
              
    $query = "ALTER TABLE `movement` 
              ADD INDEX (`start_of_movement`);";
    $this->addQuery($query);
    
    $this->makeRevision("0.60");
    $query = "ALTER TABLE `service`
      ADD `neonatalogie` ENUM ('0','1') DEFAULT '0';";
    $this->addQuery($query);
    
    $this->makeRevision("0.61");
    
    $query = "ALTER TABLE `affectation`
      ADD `function_id` INT (11) UNSIGNED;";
    $this->addQuery($query);
    
    $this->makeRevision("0.62");
    $query = "ALTER TABLE `observation_medicale`
              ADD `object_id` INT (11) UNSIGNED,
              ADD `object_class` ENUM ('CPrescriptionLineElement','CPrescriptionLineMedicament','CPrescriptionLineMix');";
    $this->addQuery($query);
    
    $this->makeRevision("0.63");
    $query = "ALTER TABLE `affectation` 
      ADD `service_id` INT (11) UNSIGNED NOT NULL AFTER `affectation_id`,
      CHANGE `lit_id` `lit_id` INT (11) UNSIGNED";
    $this->addQuery($query);
    
    $query = "UPDATE `affectation`
      LEFT JOIN `lit` ON `affectation`.`lit_id` = `lit`.`lit_id`
      LEFT JOIN `chambre` ON `lit`.`chambre_id` = `chambre`.`chambre_id`
      SET `affectation`.`service_id` = `chambre`.`service_id`";
    $this->addQuery($query);
    
    $this->makeRevision("0.64");
    $query = "ALTER TABLE `transmission_medicale` 
              ADD `date_max` DATETIME;";
    $this->addQuery($query);
    
    $this->makeRevision("0.65");
    $query = "ALTER TABLE `movement` 
                CHANGE `movement_type` `movement_type` VARCHAR (4) NOT NULL";
    $this->addQuery($query);
    
    $this->mod_version = "0.66";
  }
}
?>