<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Romain Ollivier
*/

// MODULE CONFIGURATION DEFINITION
$config = array();
$config["mod_name"]        = "dPpatients";
$config["mod_version"]     = "0.37";
$config["mod_type"]        = "user";
$config["mod_config"]      = true;

if (@$a == "setup") {
  echo dPshowModuleConfig($config);
}

class CSetupdPpatients {

  function configure() {
    global $AppUI;
    $AppUI->redirect("m=dPpatients&a=configure");
    return true;
  }

  function remove() {
    db_exec("DROP TABLE patients;");   db_error();
    db_exec("DROP TABLE medecin;");    db_error();
    db_exec("DROP TABLE antecedent;"); db_error();
    db_exec("DROP TABLE traitement;"); db_error();
    return null;
  }

  function upgrade($old_version) {
    switch ($old_version) {
      case "all":
        $sql = "CREATE TABLE `patients` (
              `patient_id` INT(11) NOT NULL AUTO_INCREMENT,
              `nom` VARCHAR(50) NOT NULL DEFAULT '',
              `prenom` VARCHAR(50) NOT NULL DEFAULT '',
              `naissance` DATE NOT NULL DEFAULT '0000-00-00',
              `sexe` ENUM('m','f') NOT NULL DEFAULT 'm',
              `adresse` VARCHAR(50) NOT NULL DEFAULT '',
              `ville` VARCHAR(50) NOT NULL DEFAULT '',
              `cp` VARCHAR(5) NOT NULL DEFAULT '',
              `tel` VARCHAR(10) NOT NULL DEFAULT '',
              `medecin_traitant` INT(11) NOT NULL DEFAULT '0',
              `incapable_majeur` ENUM('o','n') NOT NULL DEFAULT 'n',
              `ATNC` ENUM('o','n') NOT NULL DEFAULT 'n',
              `matricule` VARCHAR(15) NOT NULL DEFAULT '',
              `SHS` VARCHAR(10) NOT NULL DEFAULT '',
              PRIMARY KEY  (`patient_id`),
              UNIQUE KEY `patient_id` (`patient_id`),
              KEY `matricule` (`matricule`,`SHS`),
              KEY `nom` (`nom`,`prenom`)
            ) TYPE=MyISAM;";
        db_exec( $sql ); db_error();
      case "0.1":
        $sql = "ALTER TABLE patients" .
            "\nADD tel2 VARCHAR( 10 ) AFTER tel ," .
            "\nADD medecin1 INT( 11 ) AFTER medecin_traitant ," .
            "\nADD medecin2 INT( 11 ) AFTER medecin1 ," .
            "\nADD medecin3 INT( 11 ) AFTER medecin2 ," .
            "\nADD rques TEXT;";
        db_exec( $sql ); db_error();
        
        $sql = "CREATE TABLE medecin (" .
            "\nmedecin_id INT(11) NOT NULL AUTO_INCREMENT," .
            "\nnom VARCHAR(50) NOT NULL DEFAULT ''," .
            "\nprenom VARCHAR(50) NOT NULL DEFAULT ''," .
            "\ntel VARCHAR(10) DEFAULT NULL," .
            "\nfax VARCHAR(10) DEFAULT NULL," .
            "\nemail VARCHAR(50) DEFAULT NULL," .
            "\nadresse VARCHAR(50) DEFAULT NULL," .
            "\nville VARCHAR(50) DEFAULT NULL," .
            "\ncp VARCHAR(5) DFAULT NULL," .
            "\nPRIMARY KEY  (`medecin_id`))" .
            "\nTYPE=MyISAM COMMENT='Table des medecins correspondants';";
        db_exec( $sql ); db_error();
      case "0.2":
        $sql = "ALTER TABLE medecin " .
            "\nADD specialite TEXT AFTER prenom ;";
        db_exec( $sql ); db_error();
  
      case "0.21":
        $sql = "ALTER TABLE medecin " .
            "\nADD disciplines TEXT AFTER prenom ;";
        db_exec( $sql ); db_error();
  
      case "0.22":
          $sql = "ALTER TABLE `medecin`" .
        "\nCHANGE `adresse` `adresse` TEXT DEFAULT NULL ;";
          db_exec( $sql ); db_error();
          
      case "0.23":
        $sql = "ALTER TABLE `medecin` ADD INDEX ( `nom` ) ;";
        db_exec( $sql ); db_error();
        $sql = "ALTER TABLE `medecin` ADD INDEX ( `prenom` ) ;";
        db_exec( $sql ); db_error();
        $sql = "ALTER TABLE `medecin` ADD INDEX ( `cp` ) ;";
        db_exec( $sql ); db_error();
      
      case "0.24":
        $sql = "ALTER TABLE `patients`" .
            "\nADD `nom_jeune_fille` VARCHAR( 50 ) NOT NULL AFTER `nom` ;";
        db_exec( $sql ); db_error();
        $sql = "ALTER TABLE `patients`" .
            "\nCHANGE `sexe` `sexe` ENUM( 'm', 'f', 'j' )" .
            "\nDEFAULT 'm' NOT NULL ";
        db_exec( $sql ); db_error();
      
      case "0.25":
        $sql = "ALTER TABLE `patients`" .
            "\nCHANGE `adresse` `adresse` TEXT" .
            "\nNOT NULL ";
        db_exec( $sql ); db_error();
      
      case "0.26":
        $sql = "CREATE TABLE `antecedent` (
                `antecedent_id` BIGINT NOT NULL AUTO_INCREMENT ,
                `patient_id` BIGINT NOT NULL ,
                `type` ENUM( 'trans', 'obst', 'chir', 'med' ) DEFAULT 'med' NOT NULL ,
                `date` DATE,
                `rques` TEXT,
                PRIMARY KEY ( `antecedent_id` ) ,
                INDEX ( `patient_id` )
                ) TYPE=MyISAM COMMENT = 'antecedents des patients';";
        db_exec( $sql ); db_error();
      
      case "0.27":
        $sql = "ALTER TABLE `antecedent`" .
            "CHANGE `type` `type`" .
            "ENUM( 'trans', 'obst', 'chir', 'med', 'fam' )" .
            "DEFAULT 'med' NOT NULL;";
        db_exec( $sql ); db_error();
        $sql = "ALTER TABLE `patients`" .
            "ADD `listCim10` TEXT DEFAULT NULL ;";
        db_exec( $sql ); db_error();
        $sql = "CREATE TABLE `traitement` (
                `traitement_id` BIGINT NOT NULL AUTO_INCREMENT ,
                `patient_id` BIGINT NOT NULL ,
                `debut` DATE DEFAULT '0000-00-00' NOT NULL ,
                `fin` DATE,
                `traitement` TEXT,
                PRIMARY KEY ( `traitement_id` ) ,
                INDEX ( `patient_id` )
                ) TYPE=MyISAM COMMENT = 'traitements des patients';";
        db_exec( $sql ); db_error();
      
      case "0.28":
        $sql = "ALTER TABLE `patients`" .
            "CHANGE `SHS` `regime_sante` VARCHAR( 40 );";
        db_exec( $sql ); db_error();
        $sql = "ALTER TABLE `patients`" .
            "ADD `SHS` VARCHAR( 8 ) AFTER `matricule`;";
        db_exec( $sql ); db_error();
        $sql = "ALTER TABLE `patients` ADD INDEX ( `SHS` );";
        db_exec( $sql ); db_error();
        
      case "0.29":
        $sql = "ALTER TABLE `patients` DROP INDEX `patient_id` ;";
        db_exec( $sql ); db_error();
        $sql = "ALTER TABLE `patients` DROP INDEX `nom` ;";
        db_exec( $sql ); db_error();
        $sql = "ALTER TABLE `patients` ADD INDEX ( `nom` ) ;";
        db_exec( $sql ); db_error();
        $sql = "ALTER TABLE `patients` ADD INDEX ( `prenom` ) ;";
        db_exec( $sql ); db_error();
      case "0.30":
        $sql = "ALTER TABLE `antecedent` CHANGE `type` `type` ENUM( 'trans', 'obst', 'chir', 'med', 'fam', 'alle' ) NOT NULL DEFAULT 'med';";
        db_exec( $sql ); db_error();
      case "0.31":
        $sql = "ALTER TABLE `patients` ADD `cmu` date NULL AFTER `matricule` ;";
        db_exec( $sql ); db_error();
        $sql = "ALTER TABLE `patients` ADD `ald` text AFTER `rques` ;";
        db_exec( $sql ); db_error();
      case "0.32":
        $sql = "UPDATE `medecin` SET `tel` = NULL WHERE `tel`='NULL' ;";
        db_exec( $sql ); db_error();
        $sql = "UPDATE `medecin` SET `fax` = NULL WHERE `fax`='NULL' ;";
        db_exec( $sql ); db_error();
        $sql = "UPDATE `medecin` SET `email` = NULL WHERE `email`='NULL' ;";
        db_exec( $sql ); db_error();
        $sql = "UPDATE `medecin` SET `specialite` = NULL WHERE `specialite`='NULL' ;";
        db_exec( $sql ); db_error();
        $sql = "UPDATE `medecin` SET `disciplines` = NULL WHERE `disciplines`='NULL' ;";
        db_exec( $sql ); db_error();
        $sql = "UPDATE `medecin` SET `adresse` = NULL WHERE `adresse`='NULL' ;";
        db_exec( $sql ); db_error();
        $sql = "UPDATE `medecin` SET `ville` = NULL WHERE `ville`='NULL' ;";
        db_exec( $sql ); db_error();
        $sql = "UPDATE `medecin` SET `cp` = NULL WHERE `cp` LIKE 'NULL%' ;";
        db_exec( $sql ); db_error();
      case "0.33":
        $sql = "ALTER TABLE `medecin` ADD `jeunefille` VARCHAR( 50 ) AFTER `prenom` ;";
        db_exec( $sql ); db_error();
        $sql = "ALTER TABLE `medecin` ADD `complementaires` TEXT AFTER `disciplines` ;";
        db_exec( $sql ); db_error();
        $sql = "ALTER TABLE `medecin` ADD `orientations` TEXT AFTER `disciplines` ;";
        db_exec( $sql ); db_error();
        $sql = "ALTER TABLE `medecin` DROP `specialite` ;";
        db_exec( $sql ); db_error();
      case "0.34":
        $sql = "ALTER TABLE `patients` ADD `pays` VARCHAR( 50 ) ;";
        db_exec( $sql ); db_error();
        $sql = "ALTER TABLE `patients` ADD `nationalite` ENUM( 'local', 'etranger' ) NOT NULL DEFAULT 'local';";
        db_exec( $sql ); db_error();
        $sql = "ALTER TABLE `patients` ADD `lieu_naissance` VARCHAR( 50 ) ;";
        db_exec( $sql ); db_error();
        $sql = "ALTER TABLE `patients` ADD `profession` VARCHAR( 50 ) ;";
        db_exec( $sql ); db_error();
        $sql = "ALTER TABLE `patients` ADD `employeur_nom` VARCHAR( 50 ) ;";
        db_exec( $sql ); db_error();
        $sql = "ALTER TABLE `patients` ADD `employeur_adresse` TEXT ;";
        db_exec( $sql ); db_error();
        $sql = "ALTER TABLE `patients` ADD `employeur_cp` VARCHAR( 5 ) ;";
        db_exec( $sql ); db_error();
        $sql = "ALTER TABLE `patients` ADD `employeur_ville` VARCHAR( 50 ) ;";
        db_exec( $sql ); db_error();
        $sql = "ALTER TABLE `patients` ADD `employeur_tel` VARCHAR( 10 ) ;";
        db_exec( $sql ); db_error();
        $sql = "ALTER TABLE `patients` ADD `employeur_urssaf` VARCHAR( 11 ) ;";
        db_exec( $sql ); db_error();
        $sql = "ALTER TABLE `patients` ADD `prevenir_nom` VARCHAR( 50 ) ;";
        db_exec( $sql ); db_error();
        $sql = "ALTER TABLE `patients` ADD `prevenir_prenom` VARCHAR( 50 ) ;";
        db_exec( $sql ); db_error();
        $sql = "ALTER TABLE `patients` ADD `prevenir_adresse` TEXT ;";
        db_exec( $sql ); db_error();
        $sql = "ALTER TABLE `patients` ADD `prevenir_cp` VARCHAR( 5 ) ;";
        db_exec( $sql ); db_error();
        $sql = "ALTER TABLE `patients` ADD `prevenir_ville` VARCHAR( 50 ) ;";
        db_exec( $sql ); db_error();
        $sql = "ALTER TABLE `patients` ADD `prevenir_tel` VARCHAR( 10 ) ;";
        db_exec( $sql ); db_error();
        $sql = "ALTER TABLE `patients` ADD `prevenir_parente` ENUM( 'conjoint', 'enfant', 'ascendant', 'colateral', 'divers' ) ;";
        db_exec( $sql ); db_error();
      case "0.35":
        $sql = "ALTER TABLE `antecedent` CHANGE `type` `type` ENUM( 'med', 'alle', 'trans', 'obst', 'chir', 'fam', 'anesth' ) NOT NULL DEFAULT 'med';";
        db_exec( $sql ); db_error();
      case "0.36":
        $sql = "ALTER TABLE `antecedent` " .
               "\nCHANGE `antecedent_id` `antecedent_id` int(11) unsigned NOT NULL AUTO_INCREMENT," .
               "\nCHANGE `patient_id` `patient_id` int(11) unsigned NOT NULL DEFAULT '0';";
        db_exec( $sql ); db_error();
        
        $sql = "ALTER TABLE `medecin` " .
               "\nCHANGE `medecin_id` `medecin_id` int(11) unsigned NOT NULL AUTO_INCREMENT," .
               "\nCHANGE `nom` `nom` varchar(255) NOT NULL," .
               "\nCHANGE `prenom` `prenom` varchar(255) NOT NULL," .
               "\nCHANGE `jeunefille` `jeunefille` varchar(255) NULL," .
               "\nCHANGE `ville` `ville` varchar(255) NULL," .
               "\nCHANGE `cp` `cp` int(5) unsigned zerofill NULL," .
               "\nCHANGE `tel` `tel` bigint(10) unsigned zerofill NULL," .
               "\nCHANGE `fax` `fax` bigint(10) unsigned zerofill NULL," .
               "\nCHANGE `email` `email` varchar(255) NULL;";
        db_exec( $sql ); db_error();
        
        $sql = "ALTER TABLE `patients` " .
               "\nCHANGE `patient_id` `patient_id` int(11) unsigned NOT NULL AUTO_INCREMENT," .
               "\nCHANGE `nom` `nom` varchar(255) NOT NULL," .
               "\nCHANGE `nom_jeune_fille` `nom_jeune_fille` varchar(255) NULL," .
               "\nCHANGE `prenom` `prenom` varchar(255) NOT NULL," .
               "\nCHANGE `ville` `ville` varchar(255) NOT NULL," .
               "\nCHANGE `medecin_traitant` `medecin_traitant` int(11) unsigned NOT NULL DEFAULT '0'," .
               "\nCHANGE `medecin1` `medecin1` int(11) unsigned NULL," .
               "\nCHANGE `medecin2` `medecin2` int(11) unsigned NULL," .
               "\nCHANGE `medecin3` `medecin3` int(11) unsigned NULL," .
               "\nCHANGE `regime_sante` `regime_sante` varchar(255) NULL," .
               "\nCHANGE `pays` `pays` varchar(255) NULL," .
               "\nCHANGE `cp` `cp` int(5) unsigned zerofill NULL," .
               "\nCHANGE `tel` `tel` bigint(10) unsigned zerofill NULL," .
               "\nCHANGE `tel2` `tel2` bigint(10) unsigned zerofill NULL," .
               "\nCHANGE `SHS` `SHS` int(8) unsigned zerofill NULL," .
               "\nCHANGE `employeur_cp` `employeur_cp` int(5) unsigned zerofill NULL," .
               "\nCHANGE `employeur_tel` `employeur_tel` bigint(10) unsigned zerofill NULL," .
               "\nCHANGE `employeur_urssaf` `employeur_urssaf` bigint(11) unsigned zerofill NULL," .
               "\nCHANGE `prevenir_cp` `prevenir_cp` int(5) unsigned zerofill NULL," .
               "\nCHANGE `prevenir_tel` `prevenir_tel` bigint(10) unsigned zerofill NULL," .
               "\nCHANGE `lieu_naissance` `lieu_naissance` varchar(255) NULL," .
               "\nCHANGE `profession` `profession` varchar(255) NULL," .
               "\nCHANGE `employeur_nom` `employeur_nom` varchar(255) NULL," .
               "\nCHANGE `employeur_ville` `employeur_ville` varchar(255) NULL," .
               "\nCHANGE `prevenir_nom` `prevenir_nom` varchar(255) NULL," .
               "\nCHANGE `prevenir_prenom` `prevenir_prenom` varchar(255) NULL," .
               "\nCHANGE `prevenir_ville` `prevenir_ville` varchar(255) NULL;";
        db_exec( $sql ); db_error();
        
        $sql = "ALTER TABLE `traitement` " .
               "\nCHANGE `traitement_id` `traitement_id` int(11) unsigned NOT NULL AUTO_INCREMENT," .
               "\nCHANGE `patient_id` `patient_id` int(11) unsigned NOT NULL DEFAULT '0';";
        db_exec( $sql ); db_error();
              
        $sql = "ALTER TABLE `patients` " .
               "\nCHANGE `ATNC` `ATNC` enum('o','n','0','1') NOT NULL DEFAULT 'n'," .
               "\nCHANGE `incapable_majeur` `incapable_majeur` enum('o','n','0','1') NOT NULL DEFAULT 'n';";
        db_exec( $sql ); db_error();
      
        $sql = "UPDATE `patients` SET `ATNC`='0' WHERE `ATNC`='n';"; db_exec( $sql ); db_error();
        $sql = "UPDATE `patients` SET `ATNC`='1' WHERE `ATNC`='o';"; db_exec( $sql ); db_error();
        $sql = "UPDATE `patients` SET `incapable_majeur`='0' WHERE `incapable_majeur`='n';"; db_exec( $sql ); db_error();
        $sql = "UPDATE `patients` SET `incapable_majeur`='1' WHERE `incapable_majeur`='o';"; db_exec( $sql ); db_error();
      
        $sql = "ALTER TABLE `patients` " .
               "\nCHANGE `ATNC` `ATNC` enum('0','1') NOT NULL DEFAULT '0'," .
               "\nCHANGE `incapable_majeur` `incapable_majeur` enum('0','1') NOT NULL DEFAULT '0';";
        db_exec( $sql ); db_error();
        
      case "0.37":
        return "0.37";
    }
    return false;
  }

}

?>