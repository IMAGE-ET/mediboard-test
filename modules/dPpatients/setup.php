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
$config["mod_version"]     = "0.49";
$config["mod_type"]        = "user";

class CSetupdPpatients extends CSetup {
  
  function __construct() {
    parent::__construct();
    
    $this->mod_name = "dPpatients";
    $this->makeRevision("all");
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
    $this->addQuery($sql);
    
    $this->makeRevision("0.1");
    $sql = "ALTER TABLE patients" .
            "\nADD tel2 VARCHAR( 10 ) AFTER tel ," .
            "\nADD medecin1 INT( 11 ) AFTER medecin_traitant ," .
            "\nADD medecin2 INT( 11 ) AFTER medecin1 ," .
            "\nADD medecin3 INT( 11 ) AFTER medecin2 ," .
            "\nADD rques TEXT;";
    $this->addQuery($sql);
    $sql = "CREATE TABLE medecin (" .
            "\nmedecin_id INT(11) NOT NULL AUTO_INCREMENT," .
            "\nnom VARCHAR(50) NOT NULL DEFAULT ''," .
            "\nprenom VARCHAR(50) NOT NULL DEFAULT ''," .
            "\ntel VARCHAR(10) DEFAULT NULL," .
            "\nfax VARCHAR(10) DEFAULT NULL," .
            "\nemail VARCHAR(50) DEFAULT NULL," .
            "\nadresse VARCHAR(50) DEFAULT NULL," .
            "\nville VARCHAR(50) DEFAULT NULL," .
            "\ncp VARCHAR(5) DEFAULT NULL," .
            "\nPRIMARY KEY  (`medecin_id`))" .
            "\nTYPE=MyISAM COMMENT='Table des medecins correspondants';";
    $this->addQuery($sql);
    
    $this->makeRevision("0.2");
    $sql = "ALTER TABLE medecin ADD specialite TEXT AFTER prenom ;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.21");
    $sql = "ALTER TABLE medecin ADD disciplines TEXT AFTER prenom ;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.22");
    $sql = "ALTER TABLE `medecin` CHANGE `adresse` `adresse` TEXT DEFAULT NULL ;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.23");
    $sql = "ALTER TABLE `medecin` ADD INDEX ( `nom` ) ;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `medecin` ADD INDEX ( `prenom` ) ;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `medecin` ADD INDEX ( `cp` ) ;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.24");
    $sql = "ALTER TABLE `patients` ADD `nom_jeune_fille` VARCHAR( 50 ) NOT NULL AFTER `nom` ;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `patients`" .
            "\nCHANGE `sexe` `sexe` ENUM( 'm', 'f', 'j' )" .
            "\nDEFAULT 'm' NOT NULL ";
    $this->addQuery($sql);
    
    $this->makeRevision("0.25");
    $sql = "ALTER TABLE `patients` CHANGE `adresse` `adresse` TEXT NOT NULL ";
    $this->addQuery($sql);
    
    $this->makeRevision("0.26");
    $sql = "CREATE TABLE `antecedent` (
                `antecedent_id` BIGINT NOT NULL AUTO_INCREMENT ,
                `patient_id` BIGINT NOT NULL ,
                `type` ENUM( 'trans', 'obst', 'chir', 'med' ) DEFAULT 'med' NOT NULL ,
                `date` DATE,
                `rques` TEXT,
                PRIMARY KEY ( `antecedent_id` ) ,
                INDEX ( `patient_id` )
                ) TYPE=MyISAM COMMENT = 'antecedents des patients';";
    $this->addQuery($sql);
    
    $this->makeRevision("0.27");
    $sql = "ALTER TABLE `antecedent`" .
            "CHANGE `type` `type`" .
            "ENUM( 'trans', 'obst', 'chir', 'med', 'fam' )" .
            "DEFAULT 'med' NOT NULL;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `patients` ADD `listCim10` TEXT DEFAULT NULL ;";
    $this->addQuery($sql);
    $sql = "CREATE TABLE `traitement` (
                `traitement_id` BIGINT NOT NULL AUTO_INCREMENT ,
                `patient_id` BIGINT NOT NULL ,
                `debut` DATE DEFAULT '0000-00-00' NOT NULL ,
                `fin` DATE,
                `traitement` TEXT,
                PRIMARY KEY ( `traitement_id` ) ,
                INDEX ( `patient_id` )
                ) TYPE=MyISAM COMMENT = 'traitements des patients';";
    $this->addQuery($sql);
    
    $this->makeRevision("0.28");
    $sql = "ALTER TABLE `patients` CHANGE `SHS` `regime_sante` VARCHAR( 40 );";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `patients` ADD `SHS` VARCHAR( 8 ) AFTER `matricule`;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `patients` ADD INDEX ( `SHS` );";
    $this->addQuery($sql);
    
    $this->makeRevision("0.29");
    $sql = "ALTER TABLE `patients` DROP INDEX `patient_id` ;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `patients` DROP INDEX `nom` ;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `patients` ADD INDEX ( `nom` ) ;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `patients` ADD INDEX ( `prenom` ) ;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.30");
    $sql = "ALTER TABLE `antecedent` CHANGE `type` `type` ENUM( 'trans', 'obst', 'chir', 'med', 'fam', 'alle' ) NOT NULL DEFAULT 'med';";
    $this->addQuery($sql);
    
    $this->makeRevision("0.31");
    $sql = "ALTER TABLE `patients` ADD `cmu` date NULL AFTER `matricule` ;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `patients` ADD `ald` text AFTER `rques` ;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.32");
    $sql = "UPDATE `medecin` SET `tel` = NULL WHERE `tel`='NULL' ;";
    $this->addQuery($sql);
    $sql = "UPDATE `medecin` SET `fax` = NULL WHERE `fax`='NULL' ;";
    $this->addQuery($sql);
    $sql = "UPDATE `medecin` SET `email` = NULL WHERE `email`='NULL' ;";
    $this->addQuery($sql);
    $sql = "UPDATE `medecin` SET `specialite` = NULL WHERE `specialite`='NULL' ;";
    $this->addQuery($sql);
    $sql = "UPDATE `medecin` SET `disciplines` = NULL WHERE `disciplines`='NULL' ;";
    $this->addQuery($sql);
    $sql = "UPDATE `medecin` SET `adresse` = NULL WHERE `adresse`='NULL' ;";
    $this->addQuery($sql);
    $sql = "UPDATE `medecin` SET `ville` = NULL WHERE `ville`='NULL' ;";
    $this->addQuery($sql);
    $sql = "UPDATE `medecin` SET `cp` = NULL WHERE `cp` LIKE 'NULL%' ;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.33");
    $sql = "ALTER TABLE `medecin` ADD `jeunefille` VARCHAR( 50 ) AFTER `prenom` ;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `medecin` ADD `complementaires` TEXT AFTER `disciplines` ;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `medecin` ADD `orientations` TEXT AFTER `disciplines` ;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `medecin` DROP `specialite` ;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.34");
    $sql = "ALTER TABLE `patients` " .
           "\nADD `pays` VARCHAR( 50 )," .
           "\nADD `nationalite` ENUM( 'local', 'etranger' ) NOT NULL DEFAULT 'local'," .
           "\nADD `lieu_naissance` VARCHAR( 50 )," .
           "\nADD `profession` VARCHAR( 50 )," .
           "\nADD `employeur_nom` VARCHAR( 50 )," .
           "\nADD `employeur_adresse` TEXT," .
           "\nADD `employeur_cp` VARCHAR( 5 )," .
           "\nADD `employeur_ville` VARCHAR( 50 )," .
           "\nADD `employeur_tel` VARCHAR( 10 )," .
           "\nADD `employeur_urssaf` VARCHAR( 11 )," .
           "\nADD `prevenir_nom` VARCHAR( 50 )," .
           "\nADD `prevenir_prenom` VARCHAR( 50 )," .
           "\nADD `prevenir_adresse` TEXT," .
           "\nADD `prevenir_cp` VARCHAR( 5 )," .
           "\nADD `prevenir_ville` VARCHAR( 50 )," .
           "\nADD `prevenir_tel` VARCHAR( 10 )," .
           "\nADD `prevenir_parente` ENUM( 'conjoint', 'enfant', 'ascendant', 'colateral', 'divers' ) ;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.35");
    $sql = "ALTER TABLE `antecedent` CHANGE `type` `type` ENUM( 'med', 'alle', 'trans', 'obst', 'chir', 'fam', 'anesth' ) NOT NULL DEFAULT 'med';";
    $this->addQuery($sql);
    
    $this->makeRevision("0.36");
    $this->setTimeLimit(1800);
    $sql = "ALTER TABLE `antecedent` " .
               "\nCHANGE `antecedent_id` `antecedent_id` int(11) unsigned NOT NULL AUTO_INCREMENT," .
               "\nCHANGE `patient_id` `patient_id` int(11) unsigned NOT NULL DEFAULT '0';";
    $this->addQuery($sql);
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
    $this->addQuery($sql);
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
    $this->addQuery($sql);
    $sql = "ALTER TABLE `traitement` " .
               "\nCHANGE `traitement_id` `traitement_id` int(11) unsigned NOT NULL AUTO_INCREMENT," .
               "\nCHANGE `patient_id` `patient_id` int(11) unsigned NOT NULL DEFAULT '0';";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `patients` " .
               "\nCHANGE `ATNC` `ATNC` enum('o','n','0','1') NOT NULL DEFAULT 'n'," .
               "\nCHANGE `incapable_majeur` `incapable_majeur` enum('o','n','0','1') NOT NULL DEFAULT 'n';";
    $this->addQuery($sql);
    $sql = "UPDATE `patients` SET `ATNC`='0' WHERE `ATNC`='n';";                             $this->addQuery($sql);
    $sql = "UPDATE `patients` SET `ATNC`='1' WHERE `ATNC`='o';";                         $this->addQuery($sql);
    $sql = "UPDATE `patients` SET `incapable_majeur`='0' WHERE `incapable_majeur`='n';"; $this->addQuery($sql);
    $sql = "UPDATE `patients` SET `incapable_majeur`='1' WHERE `incapable_majeur`='o';"; $this->addQuery($sql);
    $sql = "ALTER TABLE `patients` " .
               "\nCHANGE `ATNC` `ATNC` enum('0','1') NOT NULL DEFAULT '0'," .
               "\nCHANGE `incapable_majeur` `incapable_majeur` enum('0','1') NOT NULL DEFAULT '0';";
    $this->addQuery($sql);
    
    $this->makeRevision("0.37");
    $this->setTimeLimit(1800);
    $sql = "ALTER TABLE `patients` " .
               "\nADD `nom_soundex2`    VARCHAR(255) DEFAULT NULL AFTER `nom_jeune_fille`," .
               "\nADD `prenom_soundex2` VARCHAR(255) DEFAULT NULL AFTER `nom_soundex2`," .
               "\nADD `nomjf_soundex2`  VARCHAR(255) DEFAULT NULL AFTER `prenom_soundex2`;";
    $this->addQuery($sql);
    function setup_soundex(){
      $where = array("nom_soundex2" => "IS NULL", "nom" => "!= ''");
      $limit = "0,1000";
      $pat = new CPatient;
      $listPat = $pat->loadList($where, null, $limit);
      while(count($listPat)) {
        foreach($listPat as $key => $pat) {
          if($msg = $listPat[$key]->store(false)) {
            trigger_error("Erreur store [".$listPat[$key]->_id."] : ".$msg);
            return false;
          }
        }
        $listPat = $pat->loadList($where, null, $limit);
      }
      return true;
    }
    $this->addFunctions("setup_soundex");
    
    $this->makeRevision("0.38");
    $sql = "ALTER TABLE `patients` ADD `rang_beneficiaire` enum('1','2','11','12','13') NULL AFTER `ald`;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.39");
    $sql = "ALTER TABLE `traitement` CHANGE `debut` `debut` date NULL;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.40");
    $sql = "ALTER TABLE `antecedent` " .
           "\nCHANGE `patient_id` `object_id` int(11) unsigned NOT NULL DEFAULT '0'," .
           "\nADD `object_class` enum('CPatient','CConsultAnesth') NOT NULL DEFAULT 'CPatient';";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `traitement` " .
           "\nCHANGE `patient_id` `object_id` int(11) unsigned NOT NULL DEFAULT '0'," .
           "\nADD `object_class` enum('CPatient','CConsultAnesth') NOT NULL DEFAULT 'CPatient';";
    $this->addQuery($sql);
    
    $this->makeRevision("0.41");
    $sql = "ALTER TABLE `patients` CHANGE `medecin_traitant` `medecin_traitant` int(11) unsigned NULL DEFAULT NULL;";
    $this->addQuery($sql);
    $sql = "UPDATE `patients` SET `medecin_traitant` = NULL WHERE `medecin_traitant`='0';";
    $this->addQuery($sql);
    $sql = "UPDATE `patients` SET `medecin1` = NULL WHERE `medecin1`='0';";
    $this->addQuery($sql);
    $sql = "UPDATE `patients` SET `medecin2` = NULL WHERE `medecin2`='0';";
    $this->addQuery($sql);
    $sql = "UPDATE `patients` SET `medecin3` = NULL WHERE `medecin3`='0';";
    $this->addQuery($sql);
    
    $this->makeRevision("0.42");
    $this->addDependency("dPcabinet", "0.60");
    $sql = "ALTER TABLE `addiction` CHANGE `object_class` `object_class` enum('CPatient','CConsultAnesth') NOT NULL DEFAULT 'CPatient';";
    $this->addQuery($sql);
    
    $this->makeRevision("0.43");
    $sql = "ALTER TABLE `antecedent` CHANGE `type` `type` enum('med','alle','trans','obst','chir','fam','anesth','gyn') NOT NULL DEFAULT 'med';";
    $this->addQuery($sql);
    
    $this->makeRevision("0.44");
    $sql = "ALTER TABLE `patients`
						ADD `assure_nom` VARCHAR(255), 
						ADD `assure_nom_jeune_fille` VARCHAR(255), 
						ADD `assure_prenom` VARCHAR(255), 
						ADD `assure_naissance` DATE, 
						ADD `assure_sexe` ENUM('m','f','j'), 
						ADD `assure_adresse` TEXT, 
						ADD `assure_ville` VARCHAR(255), 
						ADD `assure_cp` INT(5) UNSIGNED ZEROFILL, 
						ADD `assure_tel` BIGINT(10) UNSIGNED ZEROFILL, 
						ADD `assure_tel2` BIGINT(10) UNSIGNED ZEROFILL, 
						ADD `assure_pays` VARCHAR(255), 
						ADD `assure_nationalite` ENUM('local','etranger') NOT NULL, 
						ADD `assure_lieu_naissance` VARCHAR(255), 
						ADD `assure_profession` VARCHAR(255),
            ADD `assure_rques` TEXT;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.45");
    $sql = "ALTER TABLE `patients`
            CHANGE `rang_beneficiaire` `rang_beneficiaire` ENUM('01','02','11','12','13');";
    $this->addQuery($sql);
    
    $this->makeRevision("0.46");
    $sql = "ALTER TABLE `patients`
            ADD `assure_matricule` VARCHAR(15);";
    $this->addQuery($sql);

    $this->makeRevision("0.47");
    $sql = "ALTER TABLE `patients`
            ADD `rang_naissance` ENUM('1','2','3','4','5','6');";
    $this->addQuery($sql);

    $this->makeRevision("0.48");
    $sql = "ALTER TABLE `patients`
						ADD `code_regime` TINYINT(2) UNSIGNED ZEROFILL, 
						ADD `caisse_gest` MEDIUMINT(3) UNSIGNED ZEROFILL, 
						ADD `centre_gest` MEDIUMINT(4) UNSIGNED ZEROFILL, 
						ADD `fin_validite_vitale` DATE;";
    $this->addQuery($sql);
    
    $this->mod_version = "0.49";
  }
}

?>