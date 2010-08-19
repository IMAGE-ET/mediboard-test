<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPplanningOp
 * @version $Revision$
 * @author Romain Ollivier
 */

class CSetupdPplanningOp extends CSetup {
  
  function __construct() {
    parent::__construct();
    
    $this->mod_name = "dPplanningOp";
    
    $this->makeRevision("all");
    $query = "CREATE TABLE operations ( 
		  operation_id BIGINT(20) UNSIGNED NOT NULL auto_increment, 
			pat_id BIGINT(20) UNSIGNED NOT NULL DEFAULT '0',
			chir_id BIGINT(20) UNSIGNED NOT NULL DEFAULT '0', 
			plageop_id BIGINT(20) UNSIGNED NOT NULL DEFAULT '0',
			CIM10_code VARCHAR(5) DEFAULT NULL,
			CCAM_code VARCHAR(7) DEFAULT NULL,
			cote ENUM('droit','gauche','bilatéral','total') NOT NULL DEFAULT 'total', 
			temp_operation TIME NOT NULL DEFAULT '00:00:00', 
			time_operation TIME NOT NULL DEFAULT '00:00:00', 
			examen TEXT, 
			materiel TEXT, 
			commande_mat ENUM('o', 'n') NOT NULL DEFAULT 'n', 
			info ENUM('o','n') NOT NULL DEFAULT 'n', 
			date_anesth date NOT NULL DEFAULT '0000-00-00', 
			time_anesth TIME NOT NULL DEFAULT '00:00:00', 
			type_anesth tinyint(4) DEFAULT NULL, 
			date_adm date NOT NULL DEFAULT '0000-00-00', 
			time_adm TIME NOT NULL DEFAULT '00:00:00', 
			duree_hospi tinyint(4) UNSIGNED NOT NULL DEFAULT '0', 
			type_adm ENUM('comp','ambu','exte') DEFAULT 'comp', 
			chambre ENUM('o','n') NOT NULL DEFAULT 'o', 
			ATNC ENUM('o','n') NOT NULL DEFAULT 'n', 
			rques TEXT, 
			rank tinyint(4) NOT NULL DEFAULT '0', 
			admis ENUM('n','o') NOT NULL DEFAULT 'n', 
			PRIMARY KEY  (operation_id), 
			UNIQUE KEY operation_id (operation_id)
		) TYPE=MyISAM;";
    $this->addQuery($query);
    
    $this->makeRevision("0.1");
    $query = "ALTER TABLE operations 
		  ADD entree_bloc TIME AFTER temp_operation ,
			ADD sortie_bloc TIME AFTER entree_bloc ,
			ADD saisie ENUM( 'n', 'o' ) DEFAULT 'n' NOT NULL ,
			CHANGE plageop_id plageop_id BIGINT( 20 ) UNSIGNED";
    $this->addQuery($query);
    
    $this->makeRevision("0.2");
    $query = "ALTER TABLE `operations` 
		  ADD `convalescence` TEXT AFTER `materiel` ;";
    $this->addQuery($query);
    
    $this->makeRevision("0.21");
    $query = "ALTER TABLE `operations` 
		  ADD `depassement` INT( 4 );";
    $this->addQuery($query);
    
    $this->makeRevision("0.22");
    $query = "ALTER TABLE `operations` 
		  ADD `CCAM_code2` VARCHAR( 7 ) AFTER `CCAM_code`,
			ADD INDEX ( `CCAM_code2` ),
			ADD INDEX ( `CCAM_code` ),
			ADD INDEX ( `pat_id` ),
			ADD INDEX ( `chir_id` ),
			ADD INDEX ( `plageop_id` );";
    $this->addQuery($query);
    
    $this->makeRevision("0.23");
    $query = "ALTER TABLE `operations`
		  ADD `modifiee` TINYINT DEFAULT '0' NOT NULL AFTER `saisie`,
			ADD `annulee` TINYINT DEFAULT '0' NOT NULL ;";
    $this->addQuery($query);
    
    $this->makeRevision("0.24");
    $query = "ALTER TABLE `operations` 
		  ADD `compte_rendu` TEXT,
			ADD `cr_valide` TINYINT( 4 ) DEFAULT '0' NOT NULL ;";
    $this->addQuery($query);
    
    $this->makeRevision("0.25");
    $query = "ALTER TABLE `operations`
		  ADD `pathologie` VARCHAR( 8 ) DEFAULT NULL,
			ADD `septique` TINYINT DEFAULT '0' NOT NULL ;";
    $this->addQuery($query);
    
    $this->makeRevision("0.26");
    $query = "ALTER TABLE `operations`
		  ADD `libelle` TEXT DEFAULT NULL AFTER `CCAM_code2`;";
    $this->addQuery($query);
    
    // CR passage des champs à enregistrements supprimé car regressif
//    $this->makeRevision("0.27");
    
    $this->makeRevision("0.28");
    $query = "ALTER TABLE `operations` 
		  ADD `codes_ccam` VARCHAR( 160 ) AFTER `CIM10_code`";
    $this->addQuery($query);
    $query = "ALTER TABLE `operations` 
		  ADD INDEX ( `codes_ccam` )";
    $this->addQuery($query);
    
    $this->makeRevision("0.29");
    $query = "UPDATE `operations` 
		  SET `codes_ccam` = CONCAT_WS('|', `CCAM_code`, `CCAM_code2`)";
    $this->addQuery($query);
    
    $this->makeRevision("0.30");
    $query = "ALTER TABLE `operations`
      ADD `pose_garrot` TIME AFTER `entree_bloc` ,
      ADD `debut_op` TIME AFTER `pose_garrot` ,
      ADD `fin_op` TIME AFTER `debut_op` ,
      ADD `retrait_garrot` TIME AFTER `fin_op` ;";
    $this->addQuery($query);
    
    $this->makeRevision("0.31");
    $query = "ALTER TABLE `operations`
		  ADD `salle_id` BIGINT AFTER `plageop_id` ,
			ADD `date` DATE AFTER `salle_id`;";
    $this->addQuery($query);
    
    $this->makeRevision("0.32");
    $query = "ALTER TABLE `operations` 
		  ADD `venue_SHS` VARCHAR( 8 ) AFTER `chambre`;";
    $this->addQuery($query);
    $query = "ALTER TABLE `operations` 
		  ADD INDEX ( `venue_SHS` );";
    $this->addQuery($query);
    
    $this->makeRevision("0.33");
    $query = "ALTER TABLE `operations` 
		  ADD `code_uf` VARCHAR( 3 ) AFTER `venue_SHS`;";
    $this->addQuery($query);
    $query = "ALTER TABLE `operations` 
		  ADD `libelle_uf` VARCHAR( 40 ) AFTER `code_uf`;";
    $this->addQuery($query);
    
    $this->makeRevision("0.34");
    $query = "ALTER TABLE `operations`
		  ADD `entree_reveil` TIME AFTER `sortie_bloc` ,
			ADD `sortie_reveil` TIME AFTER `entree_reveil` ;";
    $this->addQuery($query);
    
    $this->makeRevision("0.35");
    $query = "ALTER TABLE `operations` 
		  ADD `entree_adm` DATETIME AFTER `admis`;";
    $this->addQuery($query);
    $query = "UPDATE `operations` SET
		  `entree_adm` = ADDTIME(date_adm, time_adm)
			WHERE `admis` = 'o'";
    $this->addQuery($query);
    
    $this->makeRevision("0.36");
    $this->addDependency("dPbloc", "0.15");
    // Réparation des opérations avec `duree_hospi` = '255'
    $query = "UPDATE `operations`, `plagesop` SET
		  `operations`.`date_adm` = `plagesop`.`date`,
			`operations`.`duree_hospi` = '1'
			WHERE `operations`.`duree_hospi` = '255'
			AND `operations`.`plageop_id` = `plagesop`.`plageop_id`";
    $this->addQuery($query);
		
    // Création de la table
    $query = "CREATE TABLE `sejour` (
		  `sejour_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
			`patient_id` INT UNSIGNED NOT NULL ,
			`praticien_id` INT UNSIGNED NOT NULL ,
			`entree_prevue` DATETIME NOT NULL ,
			`sortie_prevue` DATETIME NOT NULL ,
			`entree_reelle` DATETIME,
			`sortie_reelle` DATETIME,
			`chambre_seule` ENUM('o','n') NOT NULL DEFAULT 'o',
			PRIMARY KEY ( `sejour_id` )
		) TYPE=MyISAM";
    $this->addQuery($query);
    $query = "ALTER TABLE `sejour` 
		  ADD INDEX ( `patient_id` )";
    $this->addQuery($query);
    $query = "ALTER TABLE `sejour` 
		  ADD INDEX ( `praticien_id` )";
    $this->addQuery($query);
    $query = "ALTER TABLE `sejour` 
		  ADD INDEX ( `entree_prevue` )";
    $this->addQuery($query);
    $query = "ALTER TABLE `sejour` 
		  ADD INDEX ( `sortie_prevue` )";
    $this->addQuery($query);
    $query = "ALTER TABLE `sejour` 
		  ADD INDEX ( `entree_reelle` )";
    $this->addQuery($query);
    $query = "ALTER TABLE `sejour` 
		  ADD INDEX ( `sortie_reelle` )";
    $this->addQuery($query);
		
    // Migration de l'ancienne table
    $query = "ALTER TABLE `sejour` 
		  ADD `tmp_operation_id` INT UNSIGNED NOT NULL AFTER `sejour_id`";
    $this->addQuery($query);
    $query = "INSERT INTO `sejour` (
			  `sejour_id` ,
				`tmp_operation_id` ,
				`patient_id` ,
				`praticien_id` ,
				`entree_prevue` , 
				`sortie_prevue` , 
				`entree_reelle` , 
				`sortie_reelle` ,
				`chambre_seule` 
			)
      SELECT 
		    '',
        `operation_id`,
				`pat_id`,
				`chir_id`,
				ADDTIME(`date_adm`, `time_adm`),
				ADDDATE(ADDTIME(`date_adm`, `time_adm`), `duree_hospi`),
				`entree_adm` ,
				NULL ,
				`chambre`
			FROM `operations`
			WHERE `operations`.`pat_id` != 0";
    $this->addQuery($query);

    // Ajout d'une référence vers les sejour
    $query = "ALTER TABLE `operations` 
		  ADD `sejour_id` INT UNSIGNED NOT NULL AFTER `operation_id`";
    $this->addQuery($query);
    $query = "ALTER TABLE `operations` 
		  ADD INDEX ( `sejour_id` )";
    $this->addQuery($query);
    $query = "UPDATE `operations`, `sejour`
		  SET `operations`.`sejour_id` = `sejour`.`sejour_id`
			WHERE `sejour`.`tmp_operation_id` = `operations`.`operation_id`";
    $this->addQuery($query);
    $query = "ALTER TABLE `sejour` 
		  DROP `tmp_operation_id` ";
    $this->addQuery($query);
    
    $this->makeRevision("0.37");
    // Migration de nouvelles propriétés
    $query = "ALTER TABLE `sejour`
		  ADD `type` ENUM( 'comp', 'ambu', 'exte' ) DEFAULT 'comp' NOT NULL AFTER `praticien_id` ,
			ADD `annule` TINYINT DEFAULT '0' NOT NULL AFTER `type` ,
			ADD `venue_SHS` VARCHAR( 8 ) AFTER `annule` ,
			ADD `saisi_SHS` ENUM( 'o', 'n' ) DEFAULT 'n' NOT NULL AFTER `venue_SHS` ,
			ADD `modif_SHS` TINYINT DEFAULT '0' NOT NULL AFTER `saisi_SHS`";
    $this->addQuery($query);
    $query = "ALTER TABLE `sejour` 
		  ADD INDEX ( `type` )";
    $this->addQuery($query);
    $query = "ALTER TABLE `sejour` 
		  ADD INDEX ( `annule` )";
    $this->addQuery($query);
    $query = "ALTER TABLE `sejour` 
		  ADD INDEX ( `venue_SHS` )";
    $this->addQuery($query);
    $query = "ALTER TABLE `sejour` 
		  ADD INDEX ( `saisi_SHS` )";
    $this->addQuery($query);
    $query = "ALTER TABLE `sejour` 
		  ADD INDEX ( `modif_SHS` )";
    $this->addQuery($query);
    $query = "UPDATE `sejour`, `operations` SET
		  `sejour`.`type` = `operations`.`type_adm`,
			`sejour`.`annule` = `operations`.`annulee`,
			`sejour`.`venue_SHS` = `operations`.`venue_SHS`,
			`sejour`.`saisi_SHS` = `operations`.`saisie`,
			`sejour`.`modif_SHS` = `operations`.`modifiee`
			WHERE `operations`.`sejour_id` = `sejour`.`sejour_id`";
    $this->addQuery($query);
    
    $this->makeRevision("0.38");
    $query = "UPDATE `sejour`, `operations` SET
		  `sejour`.`entree_reelle` = NULL
			WHERE `operations`.`sejour_id` = `sejour`.`sejour_id`
			AND `operations`.`admis` = 'n'";
    $this->addQuery($query);
    $query = "ALTER TABLE `operations` 
		  CHANGE `date_anesth` `date_anesth` DATE";
    $this->addQuery($query);
    $query = "UPDATE `operations`
		  SET `date_anesth` = NULL
			WHERE `date_anesth` = '0000-00-00'";
    $this->addQuery($query);
    
    $this->makeRevision("0.39");
    $query = "ALTER TABLE sejour 
		  ADD rques TEXT;";
    $this->addQuery($query);
    
    $this->makeRevision("0.40");
    $query = "ALTER TABLE operations
		  ADD pause TIME NOT NULL DEFAULT '00:00:00' AFTER temp_operation";
    $this->addQuery($query);
    
    $this->makeRevision("0.41");
    $query = "ALTER TABLE `sejour`
		  ADD `pathologie` VARCHAR( 8 ) DEFAULT NULL,
			ADD `septique` TINYINT DEFAULT '0' NOT NULL;";
    $this->addQuery($query);
    $query = "UPDATE `sejour`, `operations` SET
		  `sejour`.`pathologie` = `operations`.`pathologie`,
			`sejour`.`septique` = `operations`.`septique`
			WHERE `operations`.`sejour_id` = `sejour`.`sejour_id`";
    $this->addQuery($query);
    
    $this->makeRevision("0.42");
    $query = "ALTER TABLE `sejour`
		  ADD `code_uf` VARCHAR( 8 ) DEFAULT NULL AFTER venue_SHS,
			ADD `libelle_uf` TINYINT DEFAULT '0' NOT NULL AFTER code_uf;";
    $this->addQuery($query);
    $query = "UPDATE `sejour`, `operations` SET
		  `sejour`.`code_uf` = `operations`.`code_uf`,
			`sejour`.`libelle_uf` = `operations`.`libelle_uf`
			WHERE `operations`.`sejour_id` = `sejour`.`sejour_id`";
    $this->addQuery($query);
    
    $this->makeRevision("0.43");
    $query = "ALTER TABLE `sejour` 
		  ADD `convalescence` TEXT DEFAULT NULL;";
    $this->addQuery($query);
    $query = "UPDATE `sejour`, `operations` SET
		  `sejour`.`convalescence` = `operations`.`convalescence`
			WHERE `operations`.`sejour_id` = `sejour`.`sejour_id`";
    $this->addQuery($query);
    
    $this->makeRevision("0.44");
    $query = "ALTER TABLE `sejour` 
		  DROP `code_uf`;";
    $this->addQuery($query);
    $query = "ALTER TABLE `sejour` 
		  DROP `libelle_uf`;";
    $this->addQuery($query);
    $query = " ALTER TABLE `sejour`
		  ADD `modalite_hospitalisation` ENUM( 'office', 'libre', 'tiers' ) NOT NULL DEFAULT 'libre' AFTER `type`;";
    $this->addQuery($query);
    
    $this->makeRevision("0.45");
    $query = "ALTER TABLE `operations` 
		  DROP `entree_adm`;";
    $this->addQuery($query);
    $query = "ALTER TABLE `operations` 
		  DROP `admis`;";
    $this->addQuery($query);
    
    $this->makeRevision("0.46");
    $query = "ALTER TABLE `sejour` 
		  ADD `DP`  VARCHAR(5) DEFAULT NULL AFTER `rques`;";
    $this->addQuery($query);
    $query = "UPDATE `sejour`, `operations` SET
		  `sejour`.`DP` = `operations`.`CIM10_code`
			WHERE `operations`.`sejour_id` = `sejour`.`sejour_id`";
    $this->addQuery($query);
    
    $this->makeRevision("0.47");
    $query = "CREATE TABLE protocole ( 
		  protocole_id INT UNSIGNED NOT NULL auto_increment, 
			chir_id INT UNSIGNED NOT NULL DEFAULT '0', 
			type ENUM('comp','ambu','exte') DEFAULT 'comp', 
			DP VARCHAR(5) DEFAULT NULL, 
			convalescence TEXT DEFAULT NULL, 
			rques_sejour TEXT DEFAULT NULL, 
			pathologie VARCHAR(8) DEFAULT NULL, 
			septique TINYINT DEFAULT '0' NOT NULL, 
			codes_ccam VARCHAR(160) DEFAULT NULL, 
			libelle TEXT DEFAULT NULL, 
			temp_operation TIME NOT NULL DEFAULT '00:00:00', 
			examen TEXT DEFAULT NULL, 
			materiel TEXT DEFAULT NULL, 
			duree_hospi TINYINT(4) UNSIGNED NOT NULL DEFAULT '0', 
			rques_operation TEXT DEFAULT NULL, 
			depassement TINYINT DEFAULT NULL, 
			PRIMARY KEY  (protocole_id)
		) TYPE=MyISAM;";
    $this->addQuery($query);
    $query = "ALTER TABLE `protocole` 
		  ADD INDEX (`chir_id`)";
    $this->addQuery($query);
    $query = "INSERT INTO `protocole` (
			  `protocole_id`, `chir_id`,
				`type`,
				`DP`, 
				`convalescence`, 
				`rques_sejour`, 
				`pathologie`, 
				`septique`, 
				`codes_ccam`, 
				`libelle`, 
				`temp_operation`, 
				`examen`, 
				`materiel`,
				`duree_hospi`, 
				`rques_operation`,  
				`depassement`
			)
			SELECT 
			  '', 
			  `operations`.`chir_id`,
			  `operations`.`type_adm`,
			  `operations`.`CIM10_code`,
			  `operations`.`convalescence`,
			  '',
			  '', 
			  '', 
			  `operations`.`codes_ccam`, 
			  `operations`.`libelle`,
			  `operations`.`temp_operation`, 
			  `operations`.`examen`,
			  `operations`.`materiel`, 
			  `operations`.`duree_hospi`, 
			  `operations`.`rques`,
			  `operations`.`depassement`
			 FROM `operations`
			 WHERE `operations`.`pat_id` = 0";
    $this->addQuery($query);
    $query = "DELETE FROM `operations` 
		  WHERE `pat_id` = 0";
    $this->addQuery($query);
    
    $this->makeRevision("0.48");
    $query = "ALTER TABLE `sejour` 
	    CHANGE `modalite_hospitalisation` `modalite` ENUM( 'office', 'libre', 'tiers' ) DEFAULT 'libre' NOT NULL";
    $this->addQuery($query);
    
    $this->makeRevision("0.49");
    $query = "UPDATE `operations` 
		  SET `date` = NULL;";
    $this->addQuery($query);
    
    $this->makeRevision("0.50");
    $query = "ALTER TABLE `operations` 
		  ADD `anesth_id` INT UNSIGNED DEFAULT NULL AFTER `chir_id`";
    $this->addQuery($query);
    
    $this->makeRevision("0.51");
    $this->addDependency("dPetablissement", "0.1");
    $query = "ALTER TABLE `sejour` 
		  ADD `group_id` INT UNSIGNED NOT NULL DEFAULT 1 AFTER `praticien_id`";
    $this->addQuery($query);
    $query = "ALTER TABLE `sejour` 
		  ADD INDEX ( `group_id` ) ;";
    $this->addQuery($query);
    
    $this->makeRevision("0.52");
    $query = "ALTER TABLE `operations` 
		  DROP INDEX `operation_id` ;";
    $this->addQuery($query);
    $query = "ALTER TABLE `operations` 
		  ADD INDEX ( `anesth_id` ) ;";
    $this->addQuery($query);
    $query = "ALTER TABLE `operations`
		  DROP `pat_id`, 
			DROP `CCAM_code`, 
			DROP `CCAM_code2`,
			DROP `compte_rendu`, 
			DROP `cr_valide`, 
			DROP `date_adm`,
			DROP `time_adm`, 
			DROP `chambre`, 
			DROP `type_adm`,
			DROP `venue_SHS`, 
			DROP `saisie`, 
			DROP `modifiee`,
			DROP `CIM10_code`, 
			DROP `convalescence`, 
			DROP `pathologie`,
			DROP `septique` ;";
    $this->addQuery($query);
    
    $this->makeRevision("0.53");
    $query = "CREATE TABLE `type_anesth` (
		  `type_anesth_id` INT UNSIGNED NOT NULL auto_increment,
			`name` VARCHAR(50) DEFAULT NULL,
			PRIMARY KEY  (type_anesth_id)
		) TYPE=MyISAM;";
    $this->addQuery($query);
    $query = "INSERT INTO `type_anesth` 
		  VALUES ('1', 'Non définie');";
    $this->addQuery($query);
    $query = "INSERT INTO `type_anesth` 
		  VALUES ('2', 'Rachi');";
    $this->addQuery($query);
    $query = "INSERT INTO `type_anesth` 
		  VALUES ('3', 'Rachi + bloc');";
    $this->addQuery($query);
    $query = "INSERT INTO `type_anesth` 
		  VALUES ('4', 'Anesthésie loco-régionale');";
    $this->addQuery($query);
    $query = "INSERT INTO `type_anesth` 
		  VALUES ('5', 'Anesthésie locale');";
    $this->addQuery($query);
    $query = "INSERT INTO `type_anesth` 
		  VALUES ('6', 'Neurolept');";
    $this->addQuery($query);
    $query = "INSERT INTO `type_anesth` 
		  VALUES ('7', 'Anesthésie générale');";
    $this->addQuery($query);
    $query = "INSERT INTO `type_anesth` 
		  VALUES ('8', 'Anesthesie generale + bloc');";
    $this->addQuery($query);
    $query = "INSERT INTO `type_anesth` 
		  VALUES ('9', 'Anesthesie peribulbaire');";
    $this->addQuery($query);
    $query = "UPDATE `operations` 
		  SET `type_anesth`=`type_anesth`+1;";
    $this->addQuery($query);
    
    $this->makeRevision("0.54");
    $query = "ALTER TABLE `operations`
		  ADD `induction` TIME AFTER `sortie_reveil`";
    $this->addQuery($query);
    
    $this->makeRevision("0.55");
    $query = "CREATE TABLE `naissance` (
		  `naissance_id` INT UNSIGNED NOT NULL auto_increment,
			`operation_id` INT UNSIGNED NOT NULL ,
			`nom_enfant` VARCHAR( 50 ) ,
			`prenom_enfant` VARCHAR( 50 ) ,
			`date_prevue` DATE,
			`date_reelle` DATETIME,
			`debut_grossesse` DATE,
			PRIMARY KEY ( `naissance_id` ) ,
			INDEX ( `operation_id` ))";
    $this->addQuery($query);
    
    $this->makeRevision("0.56");
    $query = "ALTER TABLE `naissance`
		  CHANGE `naissance_id` `naissance_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
			CHANGE `operation_id` `operation_id` int(11) UNSIGNED NOT NULL DEFAULT '0',
			CHANGE `nom_enfant` `nom_enfant` VARCHAR(255) NOT NULL,
			CHANGE `prenom_enfant` `prenom_enfant` VARCHAR(255) NULL;";
    $this->addQuery($query);
    $query = "ALTER TABLE `operations`
		  CHANGE `operation_id` `operation_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
			CHANGE `sejour_id` `sejour_id` int(11) UNSIGNED NOT NULL DEFAULT '0',
			CHANGE `chir_id` `chir_id` int(11) UNSIGNED NOT NULL DEFAULT '0',
			CHANGE `anesth_id` `anesth_id` int(11) UNSIGNED NULL,
			CHANGE `plageop_id` `plageop_id` int(11) UNSIGNED NULL,
			CHANGE `code_uf` `code_uf` VARCHAR(3) NULL,
			CHANGE `libelle_uf` `libelle_uf` VARCHAR(35) NULL,
			CHANGE `salle_id` `salle_id` int(11) UNSIGNED NULL,
			CHANGE `codes_ccam` `codes_ccam` VARCHAR(255) NULL,
			CHANGE `libelle` `libelle` VARCHAR(255) NULL,
			CHANGE `type_anesth` `type_anesth` int(11) UNSIGNED NULL,
			CHANGE `rank` `rank` tinyint NOT NULL DEFAULT '0',
			CHANGE `annulee` `annulee` ENUM('0','1') NOT NULL DEFAULT '0',
			CHANGE `depassement` `depassement` float NULL;";
    $this->addQuery($query);
    $query = "ALTER TABLE `operations` DROP `duree_hospi`;";
    $this->addQuery($query);
    $query = "ALTER TABLE `protocole`
		  CHANGE `protocole_id` `protocole_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
			CHANGE `chir_id` `chir_id` int(11) UNSIGNED NOT NULL DEFAULT '0',
			CHANGE `pathologie` `pathologie` VARCHAR(3) NULL,
			CHANGE `codes_ccam` `codes_ccam` VARCHAR(255) NULL,
			CHANGE `libelle` `libelle` VARCHAR(255) NULL,
			CHANGE `duree_hospi` `duree_hospi` mediumint NOT NULL DEFAULT '0',
			CHANGE `septique` `septique` ENUM('0','1') NOT NULL DEFAULT '0',
			CHANGE `depassement` `depassement` float NULL;";
    $this->addQuery($query);
    $query = "ALTER TABLE `sejour`
		  CHANGE `sejour_id` `sejour_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
			CHANGE `patient_id` `patient_id` int(11) UNSIGNED NOT NULL DEFAULT '0',
			CHANGE `praticien_id` `praticien_id` int(11) UNSIGNED NOT NULL DEFAULT '0',
      CHANGE `group_id` `group_id` int(11) UNSIGNED NOT NULL DEFAULT '1',
			CHANGE `venue_SHS` `venue_SHS` int(8) UNSIGNED zerofill NULL,
			CHANGE `annule` `annule` ENUM('0','1') NOT NULL DEFAULT '0',
			CHANGE `modif_SHS` `modif_SHS` ENUM('0','1') NOT NULL DEFAULT '0',
			CHANGE `septique` `septique` ENUM('0','1') NOT NULL DEFAULT '0',
			CHANGE `pathologie` `pathologie` VARCHAR(3) NULL;";
    $this->addQuery($query);
    $query = "ALTER TABLE `type_anesth`
		  CHANGE `type_anesth_id` `type_anesth_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
			CHANGE `name` `name` VARCHAR(255) NOT NULL;";
    $this->addQuery($query);
    $query = "ALTER TABLE `sejour`
	    CHANGE `saisi_SHS` `saisi_SHS` ENUM('o','n','0','1') NOT NULL DEFAULT 'n',
			CHANGE `chambre_seule` `chambre_seule` ENUM('o','n','0','1') NOT NULL DEFAULT 'o';";
    $this->addQuery($query);
    $query = "UPDATE `sejour` 
		  SET `saisi_SHS`='0' 
			WHERE `saisi_SHS`='n';"; 
		$this->addQuery($query);
    $query = "UPDATE `sejour` 
		  SET `saisi_SHS`='1' 
			WHERE `saisi_SHS`='o';"; 
		$this->addQuery($query);
    $query = "UPDATE `sejour` 
		  SET `chambre_seule`='0' 
			WHERE `chambre_seule`='n';"; 
		$this->addQuery($query);
    $query = "UPDATE `sejour` 
		  SET `chambre_seule`='1' 
			WHERE `chambre_seule`='o';"; 
		$this->addQuery($query);
    $query = "ALTER TABLE `sejour`
  		CHANGE `saisi_SHS` `saisi_SHS` ENUM('0','1') NOT NULL DEFAULT '0',
			CHANGE `chambre_seule` `chambre_seule` ENUM('0','1') NOT NULL DEFAULT '1';";
    $this->addQuery($query);
    $query = "ALTER TABLE `operations` 
		  CHANGE `ATNC` `ATNC` ENUM('o','n','0','1') NOT NULL DEFAULT 'n',
			CHANGE `commande_mat` `commande_mat` ENUM('o','n','0','1') NOT NULL DEFAULT 'n',
			CHANGE `info` `info` ENUM('o','n','0','1') NOT NULL DEFAULT 'n';";
    $this->addQuery($query);
    $query = "UPDATE `operations` 
		  SET `ATNC`='0' 
			WHERE `ATNC`='n';"; 
		$this->addQuery($query);
    $query = "UPDATE `operations` 
		  SET `ATNC`='1' 
			WHERE `ATNC`='o';"; 
		$this->addQuery($query);
    $query = "UPDATE `operations` 
		  SET `info`='0' 
			WHERE `info`='n';"; 
		$this->addQuery($query);
    $query = "UPDATE `operations` 
		  SET `info`='1' 
			WHERE `info`='o';"; 
		$this->addQuery($query);
    $query = "UPDATE `operations` 
		  SET `commande_mat`='0' 
			WHERE `commande_mat`='n';";
	  $this->addQuery($query);
    $query = "UPDATE `operations` 
		  SET `commande_mat`='1' 
			WHERE `commande_mat`='o';"; 
		$this->addQuery($query);
		
    $query = "ALTER TABLE `operations`
		  CHANGE `ATNC` `ATNC` ENUM('0','1') NOT NULL DEFAULT '0',
			CHANGE `commande_mat` `commande_mat` ENUM('0','1') NOT NULL DEFAULT '0',
			CHANGE `info` `info` ENUM('0','1') NOT NULL DEFAULT '0';";
    $this->addQuery($query);
    
    $this->makeRevision("0.57");
    $query = "ALTER TABLE `operations` 
		  DROP `date_anesth`,
			DROP `time_anesth`;";
    $this->addQuery($query);
    $query = "ALTER TABLE `operations`
		  CHANGE `entree_bloc` `entree_salle` TIME NULL,
			CHANGE `sortie_bloc` `sortie_salle` TIME NULL,
			ADD `entree_bloc` TIME NULL;";
    $this->addQuery($query);
    
    $this->makeRevision("0.58");
    $query = "ALTER TABLE `sejour`
		  ADD `ATNC` ENUM('0','1') NOT NULL DEFAULT '0',
			ADD `hormone_croissance` ENUM('0','1') NOT NULL DEFAULT '0',
			ADD `lit_accompagnant` ENUM('0','1') NOT NULL DEFAULT '0',
			ADD `isolement` ENUM('0','1') NOT NULL DEFAULT '0',
			ADD `television` ENUM('0','1') NOT NULL DEFAULT '0',
			ADD `repas_diabete` ENUM('0','1') NOT NULL DEFAULT '0',
			ADD `repas_sans_sel` ENUM('0','1') NOT NULL DEFAULT '0',
			ADD `repas_sans_residu` ENUM('0','1') NOT NULL DEFAULT '0',
			CHANGE `type` `type` ENUM('comp','ambu','exte','seances','ssr','psy') NOT NULL DEFAULT 'comp';";
    $this->addQuery($query);
    $query = "UPDATE sejour SET ATNC = '1' WHERE sejour_id IN (SELECT sejour_id FROM `operations` WHERE ATNC = '1');";
    $this->addQuery($query);
    $query = "ALTER TABLE `operations` DROP `ATNC`;";
    $this->addQuery($query);
    $query = "ALTER TABLE `protocole` CHANGE `type` `type` ENUM('comp','ambu','exte','seances','ssr','psy') NOT NULL DEFAULT 'comp';";
    $this->addQuery($query);
    
    $this->makeRevision("0.59");
    $query = "UPDATE `operations` SET annulee = 0 WHERE annulee = ''";
    $this->addQuery($query);
    $query = "UPDATE `sejour` SET annule = 0 WHERE annule = ''";
    $this->addQuery($query);
    
    $this->makeRevision("0.60");
    $query = "ALTER TABLE `operations` 
		  ADD INDEX ( `salle_id` )";
    $this->addQuery($query);
    $query = "ALTER TABLE `operations` 
		  ADD INDEX ( `date` )";
    $this->addQuery($query);
    $query = "ALTER TABLE `operations` 
		  ADD INDEX ( `time_operation` )";
    $this->addQuery($query);
    $query = "ALTER TABLE `operations` 
		  ADD INDEX ( `annulee` )";
    $this->addQuery($query);
    
    $this->makeRevision("0.61");
    $query = "ALTER TABLE `operations`
		  CHANGE `induction` `induction_debut` TIME";
    $this->addQuery($query);
    $query = "ALTER TABLE `operations`
		  ADD `induction_fin` TIME AFTER `induction_debut`";
    $this->addQuery($query);
    
    $this->makeRevision("0.62");
    $query = "ALTER TABLE `operations`
		  ADD `anapath` ENUM('0','1') NOT NULL DEFAULT '0',
			ADD `labo` ENUM('0','1') NOT NULL DEFAULT '0';";
    $this->addQuery($query);
    
    $this->makeRevision("0.63");
    $query = "UPDATE `operations` 
		  SET `anesth_id` = NULL WHERE `anesth_id` = '0';";
    $this->addQuery($query);
    
    $this->makeRevision("0.64");
    $query = "ALTER TABLE `operations`
		  ADD `forfait` FLOAT NULL AFTER `depassement`,
			ADD `fournitures` FLOAT NULL AFTER `forfait`;";
    $this->addQuery($query);
    $query = "ALTER TABLE `protocole`
      ADD `forfait` FLOAT NULL AFTER `depassement`,
			ADD `fournitures` FLOAT NULL AFTER `forfait`;";
    $this->addQuery($query);
    
    $this->makeRevision("0.65");
    $query = "ALTER TABLE `sejour` 
		  ADD `codes_ccam` VARCHAR(255);";
    $this->addQuery($query);
    
    $this->makeRevision("0.66");
    $this->addPrefQuery("mode", "1");
        
    $this->makeRevision("0.67");
    $query = "UPDATE `user_preferences` 
		  SET `pref_name` = 'mode_dhe' WHERE `pref_name` = 'mode';";
    $this->addQuery($query, true);
    $query = "UPDATE `user_preferences` 
		  SET `key` = 'mode_dhe' WHERE `key` = 'mode';";
    $this->addQuery($query, true);
    
    $this->makeRevision("0.68");
    $query = "ALTER TABLE `sejour` 
		  ADD `mode_sortie` ENUM( 'normal', 'transfert', 'deces' );";
    $this->addQuery($query);
    
    $this->makeRevision("0.69");
    $query = "ALTER TABLE `sejour` 
		  ADD `prestation_id` INT(11) UNSIGNED;";
    $this->addQuery($query);

    $this->makeRevision("0.70");
    $query = "ALTER TABLE `sejour` 
		  ADD `facturable` ENUM('0','1') NOT NULL DEFAULT '1';"; 
    $this->addQuery($query);
    
    $this->makeRevision("0.71");
    $query = "ALTER TABLE `sejour` 
		  ADD `reanimation` ENUM('0','1') NOT NULL DEFAULT '1' AFTER `chambre_seule`;";
    $this->addQuery($query);
    
    $this->makeRevision("0.72");
    $query = "ALTER TABLE `sejour` 
		  ADD `zt` ENUM('0','1') NOT NULL DEFAULT '1' AFTER `reanimation`;";
    $this->addQuery($query);
    
    $this->makeRevision("0.73");
    $query = "ALTER TABLE `sejour`
      CHANGE `reanimation` `reanimation` ENUM('0','1') NOT NULL DEFAULT '0',
      CHANGE `zt` `zt` ENUM('0','1') NOT NULL DEFAULT '0';";
    $this->addQuery($query);
    $query = "UPDATE `sejour` SET `sejour`.`reanimation` = 0, `sejour`.`zt` = 0;";
    $this->addQuery($query);
    
    $this->makeRevision("0.74");
    $query = "ALTER TABLE `operations` 
		  CHANGE `cote` `cote` ENUM('droit','gauche','bilatéral','total','inconnu') NOT NULL;";
    $this->addQuery($query);
    
    $this->makeRevision("0.75");
    $query = "ALTER TABLE `sejour`
      ADD `etablissement_transfert_id` INT(11) UNSIGNED;";
    $this->addQuery($query);

    $this->makeRevision("0.76");
    $query = "ALTER TABLE `operations`
      ADD `horaire_voulu` TIME;";
    $this->addQuery($query);
    
    $this->makeRevision("0.77");
    $query = "ALTER TABLE `sejour`
      CHANGE `type` `type` ENUM('comp','ambu','exte','seances','ssr','psy','urg') NOT NULL;";
    $this->addQuery($query);
    
    $this->makeRevision("0.78");
    $query = "ALTER TABLE `type_anesth`
      ADD `ext_doc` ENUM('1','2','3','4','5','6');";
    $this->addQuery($query);
    
    $this->makeRevision("0.79");
    $query = "ALTER TABLE `sejour`
		  ADD `DR` VARCHAR(5),
			CHANGE `pathologie` `pathologie` CHAR(3)";
    $this->addQuery($query);
    
    $this->makeRevision("0.80");
    $query = "UPDATE operations, plagesop
      SET operations.salle_id = plagesop.salle_id
      WHERE operations.salle_id IS NULL
      AND operations.plageop_id = plagesop.plageop_id;";
    $this->addQuery($query);
    $query = "ALTER TABLE `operations`
      CHANGE `salle_id` `salle_id` INT( 11 ) UNSIGNED NOT NULL;";
    $this->addQuery($query);
    
    $this->makeRevision("0.81");
    $query = "ALTER TABLE `operations`
      CHANGE `salle_id` `salle_id` INT( 11 ) UNSIGNED DEFAULT NULL;";
    $this->addQuery($query);
    $query = "UPDATE `operations` 
		  SET salle_id = NULL WHERE salle_id = 0;";
    $this->addQuery($query);
    
    $this->makeRevision("0.82");
    $this->addDependency("dPsante400", "0.1");
    $query = "INSERT INTO `id_sante400` (id_sante400_id, object_class, object_id, tag, last_update, id400)
			SELECT NULL , 'CSejour', `sejour_id` , 'SHS group:1', NOW( ) , `venue_SHS`
			FROM `sejour`
			WHERE `venue_SHS` IS NOT NULL	
			AND `venue_SHS` != 0";
    $this->addQuery($query);
    
    $this->makeRevision("0.83");
    $query = "ALTER TABLE `sejour` 
		  DROP `venue_SHS";
    $this->addQuery($query);
    
    $this->makeRevision("0.84");
    $query = "ALTER TABLE `sejour`
		  ADD `repas_sans_porc` ENUM('0','1') NOT NULL DEFAULT '0';";
    $this->addQuery($query);
    
    $this->makeRevision("0.85");
    $query = "ALTER TABLE `protocole`
		  ADD `protocole_prescription_chir_id` INT (11) UNSIGNED,
		  ADD `protocole_prescription_anesth_id` INT (11) UNSIGNED;";
    $this->addQuery($query);

    $query = "ALTER TABLE `protocole`
		  ADD INDEX (`protocole_prescription_chir_id`),
		  ADD INDEX (`protocole_prescription_anesth_id`);";
    $this->addQuery($query);
    
    $this->makeRevision("0.86");
    $query = "ALTER TABLE `operations` ADD `depassement_anesth` FLOAT NULL AFTER `fournitures`;";
    $this->addQuery($query);
    
    $this->makeRevision("0.87");
    $this->addDependency("dPcompteRendu", "0.1");
    $query = CSetupdPcompteRendu::getTemplateReplaceQuery("Opération - CCAM - code",        "Opération - CCAM1 - code");
    $this->addQuery($query);
    
    $query = CSetupdPcompteRendu::getTemplateReplaceQuery("Opération - CCAM - description", "Opération - CCAM1 - description");
    $this->addQuery($query);
    
    $query = CSetupdPcompteRendu::getTemplateReplaceQuery("Opération - CCAM complet", "Opération - CCAM - codes");
    $this->addQuery($query);
    
    $this->makeRevision("0.88");
    $query = "ALTER TABLE `operations` 
        CHANGE `anapath` `anapath` ENUM ('1','0','?') DEFAULT '?',
        CHANGE `labo` `labo` ENUM ('1','0','?') DEFAULT '?';";
    $this->addQuery($query);
    $query = "UPDATE `operations` 
		  SET `anapath` = '?' 
			WHERE `anapath` = '0'";
    $this->addQuery($query);
    $query = "UPDATE `operations` 
		  SET `labo` = '?' 
			WHERE `labo` = '0'";
    $this->addQuery($query);
    
    $this->makeRevision("0.89");
    $query = "ALTER TABLE `protocole` 
		  ADD `for_sejour` ENUM ('0','1') NOT NULL DEFAULT '0'";
    $this->addQuery($query);
    
    $this->makeRevision("0.90");
    $query = "ALTER TABLE `sejour` 
    ADD `adresse_par_prat_id` INT (11),
    ADD `adresse_par_etab_id` INT (11),
    ADD `libelle` VARCHAR (255)";
    $this->addQuery($query);
    
    $this->makeRevision("0.91");
    $query = "ALTER TABLE `protocole` 
      ADD `libelle_sejour` VARCHAR (255)";
    $this->addQuery($query);
    
    $this->makeRevision("0.92");
    $query = "ALTER TABLE `operations` 
	    ADD `cote_admission` ENUM ('droit','gauche') AFTER `horaire_voulu`,
	    ADD `cote_consult_anesth` ENUM ('droit','gauche') AFTER `cote_admission`,
	    ADD `cote_hospi` ENUM ('droit','gauche') AFTER `cote_consult_anesth`,
	    ADD `cote_bloc` ENUM ('droit','gauche') AFTER `cote_hospi`;";
    $this->addQuery($query);
    
    $this->makeRevision("0.93");
    $query = "ALTER TABLE `operations`
      ADD `prothese` ENUM ('1','0','?')  DEFAULT '?' AFTER `labo`,
      ADD `date_visite_anesth` DATETIME,
      ADD `prat_visite_anesth_id` INT (11) UNSIGNED,
      ADD `rques_visite_anesth` TEXT,
      ADD `autorisation_anesth` ENUM ('0','1');";
    $this->addQuery($query);
    $query = "ALTER TABLE `operations`
      ADD INDEX (`date_visite_anesth`),
      ADD INDEX (`prat_visite_anesth_id`);";
    $this->addQuery($query);
  
    $this->makeRevision("0.94");
    $this->addPrefQuery("dPplanningOp_listeCompacte", "1");
    
    $this->makeRevision("0.95");
    $query = "ALTER TABLE `sejour`
      ADD `service_id` INT (11) UNSIGNED AFTER `zt`,
      ADD INDEX (`etablissement_transfert_id`),
      ADD INDEX (`service_id`),
      ADD INDEX (`prestation_id`);";
    $this->addQuery($query);
    
    $this->makeRevision("0.96");
    $query = "ALTER TABLE `protocole`
      ADD `service_id_sejour` INT (11) UNSIGNED,
      ADD INDEX (`temp_operation`),
      ADD INDEX (`service_id_sejour`);";
    $this->addQuery($query);
    
    $this->makeRevision("0.97");
    $query = "ALTER TABLE `sejour` 
      ADD `etablissement_entree_transfert_id` INT (11) UNSIGNED,
      ADD INDEX (`etablissement_entree_transfert_id`);";
    $this->addQuery($query);    
    
		$this->makeRevision("0.98");
    $query = "ALTER TABLE `sejour` 
      ADD `facture` ENUM ('0','1') DEFAULT '0';";
    $this->addQuery($query);  
		    
    $this->makeRevision("0.99");
    $query = "ALTER TABLE `operations` 
      ADD `facture` ENUM ('0','1') DEFAULT '0';";
    $this->addQuery($query);
    
    $this->makeRevision("1.00");
    $query = "ALTER TABLE `sejour` 
      CHANGE `type` `type` ENUM ('comp','ambu','exte','seances','ssr','psy','urg','consult') NOT NULL;";
    $this->addQuery($query);
    
    $this->makeRevision("1.01");
    $query = "ALTER TABLE `naissance` 
      ADD INDEX (`date_prevue`),
      ADD INDEX (`date_reelle`),
      ADD INDEX (`debut_grossesse`);";
    $this->addQuery($query);
    $query = "ALTER TABLE `operations` 
      ADD INDEX (`type_anesth`);";
    $this->addQuery($query);
    $query = "ALTER TABLE `sejour` 
      ADD INDEX (`adresse_par_prat_id`),
      ADD INDEX (`adresse_par_etab_id`);";
    $this->addQuery($query);
    
    $this->makeRevision("1.02");
    $query = "ALTER TABLE `sejour` 
      CHANGE `chambre_seule` `chambre_seule` ENUM ('0','1') DEFAULT '0';";
    $this->addQuery($query);
    
    $this->makeRevision("1.03");
    $query = "UPDATE sejour 
		  SET sortie_prevue = entree_reelle 
			WHERE entree_reelle IS NOT NULL 
			AND sortie_prevue < entree_reelle";
    $this->addQuery($query);
		
		$this->makeRevision("1.04");
    $query = "ALTER TABLE `sejour`
      CHANGE `mode_sortie` `mode_sortie` ENUM ('normal','transfert','mutation','deces') DEFAULT 'normal',
      ADD `service_mutation_id` INT (11) UNSIGNED;";
    $this->addQuery($query);
		
    $query = "ALTER TABLE `sejour` 
      ADD INDEX (`service_mutation_id`);";
    $this->addQuery($query);

    $this->makeRevision("1.05");
    $query = "ALTER TABLE `sejour`
      ADD `entree` DATETIME AFTER `sortie_reelle`,
      ADD `sortie` DATETIME AFTER `entree`";
    $this->addQuery($query);
    $query = "UPDATE `sejour` SET
      `sejour`.`entree` = IF(`sejour`.`entree_reelle`,`sejour`.`entree_reelle`,`sejour`.`entree_prevue`),
      `sejour`.`sortie` = IF(`sejour`.`sortie_reelle`,`sejour`.`sortie_reelle`,`sejour`.`sortie_prevue`)";
    $this->addQuery($query);
    
    $this->makeRevision("1.06");
    $query = "ALTER TABLE `sejour` 
      ADD INDEX (`entree`),
      ADD INDEX (`sortie`);";
    $this->addQuery($query);
		
		$this->makeRevision("1.07");
    $query = "ALTER TABLE `sejour`
      ADD `service_entree_mutation_id` INT (11) UNSIGNED;";
    $this->addQuery($query);
    
    $this->makeRevision("1.08");
    $query = "ALTER TABLE `sejour`
      ADD `forfait_se` ENUM ('0','1') DEFAULT '0';";
    $this->addQuery($query);
		 
    $this->makeRevision("1.09");
    $query = "ALTER TABLE `sejour`
      ADD `recuse` ENUM ('0','1') DEFAULT '0';";
    $this->addQuery($query);
    
    $this->makeRevision("1.10");
    $query = "ALTER TABLE `protocole` 
      CHANGE `service_id_sejour` `service_id` INT (11) UNSIGNED  NOT NULL;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `protocole` 
      ADD INDEX (`service_id`);";
    $this->addQuery($query);

    $this->makeRevision("1.11");
    $query = "ALTER TABLE `protocole` 
      CHANGE `service_id` `service_id` INT (11) UNSIGNED;";
    $this->addQuery($query);
    $query = "UPDATE `protocole` 
			SET service_id = NULL
			WHERE service_id = '0'";
    $this->addQuery($query);

    $this->makeRevision("1.12");
    $query = "CREATE TABLE `color_libelle_sejour` (
      `color_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
      `libelle` VARCHAR (255) NOT NULL,
      `color` CHAR (6) NOT NULL DEFAULT 'ffffff'
    ) TYPE=MYISAM;";
    $this->addQuery($query);
		
    $this->mod_version = "1.13";
  }
}
?>