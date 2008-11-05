<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPpatients
 * @version $Revision$
 * @author Romain Ollivier
 */

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
      while (count($listPat)) {
        foreach ($listPat as &$pat) {
          if ($msg = $pat->store()) {
            trigger_error("Erreur store [$pat->_id] : $msg");
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

    $this->makeRevision("0.49");
    $sql = "ALTER TABLE `patients`
						CHANGE `rang_beneficiaire` `rang_beneficiaire` ENUM('01','02','09','11','12','13','14','15','16','31');";
    $this->addQuery($sql);
    
    
    // Creation de la table dossier medical
    $this->makeRevision("0.50");
    
    set_time_limit(60);
    
    $this->addDependency("dPcabinet", "0.78");
    
    $sql = "CREATE TABLE `dossier_medical` (
            `dossier_medical_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT, 
            `listCim10` TEXT, 
            `object_id` INT(11) UNSIGNED NOT NULL, 
            `object_class` VARCHAR(25) NOT NULL, 
            PRIMARY KEY (`dossier_medical_id`)) TYPE=MYISAM;";
    $this->addQuery($sql);

    
    
    // Insertion des patients dans la table dossier_medical
    $sql = "INSERT INTO `dossier_medical`
            SELECT '', patients.listCim10, patients.patient_id, 'CPatient' 
            FROM `patients`;";
    $this->addQuery($sql);
    
    
    // Insertion des sejours dans la table dossier_medical
    $sql = "INSERT INTO `dossier_medical`
            SELECT '', GROUP_CONCAT(consultation_anesth.listCim10 SEPARATOR '|'), sejour.sejour_id, 'CSejour'
            FROM `consultation_anesth`, `operations`,`sejour`   
            WHERE consultation_anesth.operation_id = operations.operation_id
            AND operations.sejour_id = sejour.sejour_id
            GROUP BY sejour.sejour_id;";
    $this->addQuery($sql);

    
    // Suppression des '|' en debut de liste
    $sql = "UPDATE `dossier_medical` SET `listCim10` = TRIM(LEADING '|' FROM listCim10)
            WHERE listCim10 LIKE '|%'";
    $this->addquery($sql);

   
    // Ajout du champ dossier_medical_id aux tables addiction/antecedent/traitement
    $sql = "ALTER TABLE `addiction`
            ADD `dossier_medical_id` INT(11) UNSIGNED;";
    $this->addQuery($sql);
  
    $sql = "ALTER TABLE `addiction` ADD INDEX ( `dossier_medical_id` ) ;";
    $this->addQuery($sql);
    
    
    $sql = "ALTER TABLE `antecedent`
            ADD `dossier_medical_id` INT(11) UNSIGNED;";
    $this->addQuery($sql);

    $sql = "ALTER TABLE `antecedent` ADD INDEX ( `dossier_medical_id` ) ;";
    $this->addQuery($sql);
    
    
    $sql = "ALTER TABLE `traitement`
            ADD `dossier_medical_id` INT(11) UNSIGNED;";
    $this->addQuery($sql);
    
    $sql = "ALTER TABLE `traitement` ADD INDEX ( `dossier_medical_id` ) ;";
    $this->addQuery($sql);

    
    // Mise a jour du champ dossier_medical_id dans le cas du Patient
    // Table addiction
    $sql = "ALTER TABLE `addiction` ADD INDEX ( `object_id` ) ;";
    $this->addQuery($sql);
    
    $sql = "UPDATE `addiction`, `dossier_medical` SET addiction.dossier_medical_id = dossier_medical.dossier_medical_id
            WHERE dossier_medical.object_class = 'CPatient'
            AND dossier_medical.object_id = addiction.object_id
            AND addiction.object_class = 'CPatient'";
    $this->addQuery($sql);
  
    // Table antecedent
    $sql = "UPDATE `antecedent`, `dossier_medical` SET antecedent.dossier_medical_id = dossier_medical.dossier_medical_id
            WHERE dossier_medical.object_class = 'CPatient'
            AND dossier_medical.object_id = antecedent.object_id
            AND antecedent.object_class = 'CPatient'";
    $this->addQuery($sql);
    
    
    // Table Traitement
    $sql = "UPDATE `traitement`, `dossier_medical` SET traitement.dossier_medical_id = dossier_medical.dossier_medical_id
            WHERE dossier_medical.object_class = 'CPatient'
            AND dossier_medical.object_id = traitement.object_id
            AND traitement.object_class = 'CPatient'";
    $this->addQuery($sql);
    
    
    
    // Mise a jour du champs dossier_medical_id dans le cas du Sejour
    // Table addiction
    $sql = "UPDATE `addiction`, `dossier_medical`, `consultation_anesth`, `sejour`, `operations` 
            SET addiction.dossier_medical_id = dossier_medical.dossier_medical_id
            WHERE addiction.object_id = consultation_anesth.consultation_anesth_id
            AND addiction.object_class = 'CConsultAnesth'
            AND consultation_anesth.operation_id = operations.operation_id
            AND operations.sejour_id = sejour.sejour_id
            AND dossier_medical.object_class = 'CSejour' 
            AND dossier_medical.object_id = sejour.sejour_id;";
    $this->addQuery($sql);    

    
    // Table antecedent
    $sql = "UPDATE `antecedent`, `dossier_medical`, `consultation_anesth`, `sejour`, `operations` 
            SET antecedent.dossier_medical_id = dossier_medical.dossier_medical_id
            WHERE antecedent.object_id = consultation_anesth.consultation_anesth_id
            AND antecedent.object_class = 'CConsultAnesth'
            AND consultation_anesth.operation_id = operations.operation_id
            AND operations.sejour_id = sejour.sejour_id
            AND dossier_medical.object_class = 'CSejour' 
            AND dossier_medical.object_id = sejour.sejour_id;";
    $this->addQuery($sql);    
    
    
    // Table traitement
    $sql = "UPDATE `traitement`, `dossier_medical`, `consultation_anesth`, `sejour`, `operations` 
            SET traitement.dossier_medical_id = dossier_medical.dossier_medical_id
            WHERE traitement.object_id = consultation_anesth.consultation_anesth_id
            AND traitement.object_class = 'CConsultAnesth'
            AND consultation_anesth.operation_id = operations.operation_id
            AND operations.sejour_id = sejour.sejour_id
            AND dossier_medical.object_class = 'CSejour' 
            AND dossier_medical.object_id = sejour.sejour_id;";
    $this->addQuery($sql);    
    
    
    // Mise a jour du champ examen de la consultation dans le cas d'antecendent sans operation_id
    $sql = "CREATE TEMPORARY TABLE ligneAntecedent (
             consultation_id INT( 11 ) ,
             ligne_antecedent TEXT
            ) AS 
              SELECT consultation_anesth.consultation_id, CONCAT_WS(' - ', antecedent.type, antecedent.date, antecedent.rques ) AS ligne_antecedent
              FROM `antecedent`, `consultation_anesth`
              WHERE antecedent.object_id = consultation_anesth.consultation_anesth_id
              AND antecedent.dossier_medical_id IS NULL;";
    $this->addQuery($sql);    
    
    $sql = "CREATE TEMPORARY TABLE blocAntecedent (
             consultation_id INT( 11 ) ,
             bloc_antecedent TEXT
            ) AS
              SELECT consultation_id, GROUP_CONCAT(ligne_antecedent SEPARATOR '\n') AS bloc_antecedent
              FROM `ligneAntecedent`
              GROUP BY consultation_id;";
    $this->addQuery($sql);
    
    $sql = "UPDATE `consultation`, `blocAntecedent`
            SET consultation.examen = CONCAT_WS('\n', consultation.examen, blocAntecedent.bloc_antecedent)
            WHERE consultation.consultation_id = blocAntecedent.consultation_id;";
    $this->addQuery($sql);    
    

    // Mise a jour du champ examen de la consultation dans le cas d'une addiction sans operation_id
    $sql = "CREATE TEMPORARY TABLE ligneAddiction (
             consultation_id INT( 11 ) ,
             ligne_addiction TEXT
            ) AS 
              SELECT consultation_anesth.consultation_id, CONCAT_WS(' - ', addiction.type, addiction.addiction ) AS ligne_addiction
              FROM `addiction`, `consultation_anesth`
              WHERE addiction.object_id = consultation_anesth.consultation_anesth_id
              AND addiction.dossier_medical_id IS NULL;";
    $this->addQuery($sql);
            
    $sql = "CREATE TEMPORARY TABLE blocAddiction (
             consultation_id INT( 11 ) ,
             bloc_addiction TEXT
            ) AS
              SELECT consultation_id, GROUP_CONCAT(ligne_addiction SEPARATOR '\n') AS bloc_addiction
              FROM `ligneAddiction`
              GROUP BY consultation_id;";
    $this->addQuery($sql);
    
    $sql = "UPDATE `consultation`, `blocAddiction`
            SET consultation.examen = CONCAT_WS('\n', consultation.examen, blocAddiction.bloc_addiction)
            WHERE consultation.consultation_id = blocAddiction.consultation_id;";
    $this->addQuery($sql);    

  
    // Mise a jour du champ examen de la consultation dans le cas d'un traitement sans operation_id
    $sql = "CREATE TEMPORARY TABLE ligneTraitement (
             consultation_id INT( 11 ) ,
             ligne_traitement TEXT
            ) AS 
              SELECT consultation_anesth.consultation_id, CONCAT_WS(' - ', traitement.debut, traitement.fin, traitement.traitement ) AS ligne_traitement
              FROM `traitement`, `consultation_anesth`
              WHERE traitement.object_id = consultation_anesth.consultation_anesth_id
              AND traitement.dossier_medical_id IS NULL;";
    $this->addQuery($sql);
    
    
    $sql = "CREATE TEMPORARY TABLE blocTraitement (
             consultation_id INT( 11 ) ,
             bloc_traitement TEXT
            ) AS
              SELECT consultation_id, GROUP_CONCAT(ligne_traitement SEPARATOR '\n') AS bloc_traitement
              FROM `ligneTraitement`
              GROUP BY consultation_id;";
    $this->addQuery($sql);
    
    $sql = "UPDATE `consultation`, `blocTraitement`
            SET consultation.examen = CONCAT_WS('\n', consultation.examen, blocTraitement.bloc_traitement)
            WHERE consultation.consultation_id = blocTraitement.consultation_id;";
    $this->addQuery($sql); 

    $sql = "ALTER TABLE `addiction`
            DROP `object_id`, 
            DROP `object_class`;";
    $this->addQuery($sql);
        
    $sql = "ALTER TABLE `antecedent`
            DROP `object_id`, 
            DROP `object_class`;";
    $this->addQuery($sql);
    
    $sql = "ALTER TABLE `traitement`
            DROP `object_id`, 
            DROP `object_class`;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.51");
    $sql = "ALTER TABLE `patients`
            DROP `listCim10`;";
    $this->addQuery($sql);
    
    
    $this->makeRevision("0.52");
    $sql = "ALTER TABLE `patients` 
           CHANGE `naissance` `naissance` CHAR( 10 ) NULL DEFAULT NULL ";
    $this->addQuery($sql);
    
    $sql = "ALTER TABLE `patients` 
           CHANGE `assure_naissance` `assure_naissance` CHAR( 10 ) NULL DEFAULT NULL ";
    $this->addQuery($sql);

    
    $this->makeRevision("0.53");
    $sql = "ALTER TABLE `dossier_medical` CHANGE `listCim10` `codes_cim` TEXT;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.54");
    $sql = "ALTER TABLE `patients` ADD INDEX ( `nom_soundex2` );";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `patients` ADD INDEX ( `prenom_soundex2` );";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `patients` ADD INDEX ( `naissance` );";
    $this->addQuery($sql);
    
    
    $this->makeRevision("0.55");
    $sql = "ALTER TABLE `patients` ADD INDEX ( `nom_jeune_fille` );";
    $this->addQuery($sql);
    
    $this->makeRevision("0.56");
    $sql = "ALTER TABLE `dossier_medical` ADD INDEX ( `object_id` );";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `dossier_medical` ADD INDEX ( `object_class` );";
    $this->addQuery($sql);
    
    $this->makeRevision("0.57");
    $sql = "ALTER TABLE `antecedent`
            CHANGE `type` `type`
            ENUM('med','alle','trans','obst','chir','fam','anesth','gyn','cardio','pulm','stomato','plast','ophtalmo','digestif','gastro','stomie','uro','ortho','traumato','amput','neurochir','greffe','thrombo','cutane','hemato','rhumato','neuropsy','infect','endocrino','carcino')
            NOT NULL;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.58");
    $sql = "ALTER TABLE `patients` ADD INDEX ( `medecin_traitant` );";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `patients` ADD INDEX ( `medecin1` );";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `patients` ADD INDEX ( `medecin2` );";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `patients` ADD INDEX ( `medecin3` );";
    $this->addQuery($sql);
    
    $this->makeRevision("0.59");
    $sql = "ALTER TABLE `patients` CHANGE `ald` `notes_amo` TEXT DEFAULT NULL";
    $this->addQuery($sql);
    
    $this->makeRevision("0.60");
    $sql = "ALTER TABLE `patients`
						ADD `ald` ENUM('0','1'), 
						ADD `code_exo` ENUM('0','5','9') DEFAULT '0', 
						ADD `deb_amo` DATE, 
						ADD `fin_amo` DATE;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.61");
    $sql = "UPDATE `patients`
            SET `fin_amo` = `cmu`";
    $this->addQuery($sql);
    
    $sql = "ALTER TABLE `patients`
            CHANGE `cmu` `cmu` ENUM('0','1') DEFAULT '0';";
    $this->addQuery($sql);
    
    $sql = "UPDATE `patients`
            SET `cmu` = '1'
            WHERE `fin_amo` IS NOT NULL;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.62");
    $sql = "ALTER TABLE `antecedent`
            CHANGE `type` `type`
            ENUM('med','alle','trans','obst','chir','fam','anesth','gyn','cardio','pulm','stomato','plast','ophtalmo','digestif','gastro','stomie','uro','ortho','traumato','amput','neurochir','greffe','thrombo','cutane','hemato','rhumato','neuropsy','infect','endocrino','carcino','orl');";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `addiction`
            CHANGE `type` `type`
            ENUM('tabac', 'oenolisme', 'cannabis');";
    $this->addQuery($sql);
    
    $this->makeRevision("0.63");
    $sql = "CREATE TABLE `etat_dent` (
            `etat_dent_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
            `dossier_medical_id` INT NOT NULL ,
            `dent` TINYINT UNSIGNED NOT NULL ,
            `etat` ENUM('bridge', 'pivot', 'mobile', 'appareil') NULL
            ) ENGINE = MYISAM ;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.64");
    $this->addDependency("dPsante400", "0.1");
    $sql = "INSERT INTO `id_sante400` (id_sante400_id, object_class, object_id, tag, last_update, id400)
						SELECT NULL, 'CPatient', `patient_id`, 'SHS group:1', NOW(), `SHS`
						FROM `patients` 
						WHERE `SHS` IS NOT NULL 
						AND `SHS` != 0";
    $this->addQuery($sql);
    
    $this->makeRevision("0.65");
    $sql = "ALTER TABLE `patients` DROP `SHS";
    $this->addQuery($sql);
    
    $this->makeRevision("0.66");
    $this->addDependency("dPcabinet", "0.30");
    $sql = "CREATE TABLE `constantes_medicales` (
      `constantes_medicales_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
      `patient_id` INT (11) UNSIGNED NOT NULL,
      `datetime` DATETIME NOT NULL,
      `context_class` VARCHAR (255),
      `context_id` INT (11) UNSIGNED,
      `poids` FLOAT UNSIGNED,
      `taille` FLOAT,
      `ta` VARCHAR (10),
      `pouls` INT (11) UNSIGNED,
      `spo2` FLOAT
    ) TYPE=MYISAM;";
    $this->addQuery($sql);
    
    $sql = "ALTER TABLE `constantes_medicales` 
      ADD INDEX (`patient_id`),
      ADD INDEX (`datetime`),
      ADD INDEX (`context_id`);";
    $this->addQuery($sql);
    
    $sql = "INSERT INTO `constantes_medicales` (
        `context_class`, 
        `context_id`, 
        `patient_id`, 
        `datetime`, 
        `poids`, 
        `taille`, 
        `ta`, 
        `pouls`, 
        `spo2`
      )
      SELECT 
        'CConsultation', 
        `consultation`.`consultation_id`, 
        `consultation`.`patient_id`,
        CONCAT(`plageconsult`.`date`, ' ', `consultation`.`heure`), 
        `consultation_anesth`.`poid`, 
        `consultation_anesth`.`taille`, 
        IF(`consultation_anesth`.`tasys`, CONCAT(`consultation_anesth`.`tasys`, '|', `consultation_anesth`.`tadias`), NULL),
        `consultation_anesth`.`pouls`,
        `consultation_anesth`.`spo2`
      FROM 
        `consultation_anesth`, `consultation`, `plageconsult`
      WHERE 
        `consultation`.`consultation_id` = `consultation_anesth`.`consultation_id` AND
        `plageconsult`.`plageconsult_id` = `consultation`.`plageconsult_id`";
    $this->addQuery($sql);
    
    /*$sql = "ALTER TABLE `consultation_anesth` 
      DROP `poid`
      DROP `taille`
      DROP `tasys`
      DROP `tadias`
      DROP `pouls`
      DROP `spo2`;";
    $this->addQuery($sql);*/
    
    $repl = array("Patient - poids",
                  "Patient - taille",
                  "Patient - Pouls",
                  "Patient - IMC",
                  "Patient - TA");
    
    $find = array("Anesthésie - poids",
                  "Anesthésie - taille",
                  "Anesthésie - Pouls",
                  "Anesthésie - IMC",
                  "Anesthésie - TA");
    $count = count($repl);
    for ($i = 0; $i < $count; $i++) {
      $sql = CSetupdPcompteRendu::getTemplateReplaceQuery($find[$i], $repl[$i]);
      $this->addQuery($sql);
    }
    
    $this->makeRevision("0.67");
    $sql = 'ALTER TABLE `constantes_medicales` ADD `temperature` FLOAT';
    $this->addQuery($sql);
    
    $this->makeRevision("0.68");
    $sql = "ALTER TABLE `patients` 
	  				ADD `code_sit` MEDIUMINT (4) UNSIGNED ZEROFILL,
  					ADD `regime_am` ENUM ('0','1');";
    $this->addQuery($sql);
  	
    $this->makeRevision("0.69");
    $this->addQuery(CSetupdPcompteRendu::getTemplateReplaceQuery("Patient - antécédents", "Patient - Antécédents -- tous"));
    $this->addQuery(CSetupdPcompteRendu::getTemplateReplaceQuery("Patient - traitements", "Patient - Traitements"));
    $this->addQuery(CSetupdPcompteRendu::getTemplateReplaceQuery("Patient - addictions" , "Patient - Addictions -- toutes"));
    $this->addQuery(CSetupdPcompteRendu::getTemplateReplaceQuery("Patient - diagnostics", "Patient - Diagnotics" ));
    
    $this->addQuery(CSetupdPcompteRendu::getTemplateReplaceQuery("Sejour - antécédents", "Sejour - Antécédents -- tous"));
    $this->addQuery(CSetupdPcompteRendu::getTemplateReplaceQuery("Sejour - traitements", "Sejour - Traitements"));
    $this->addQuery(CSetupdPcompteRendu::getTemplateReplaceQuery("Sejour - addictions" , "Sejour - Addictions -- toutes"));
    $this->addQuery(CSetupdPcompteRendu::getTemplateReplaceQuery("Sejour - diagnostics", "Sejour - Diagnotics" ));
    
    $this->makeRevision("0.70");
    $sql = "ALTER TABLE `patients` ADD `email` VARCHAR (255) AFTER tel2;";
    $this->addQuery($sql);

    $this->makeRevision("0.71");
    $sql = "CREATE TABLE IF NOT EXISTS `correspondant` (
			`correspondant_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
			`medecin_id` INT (11) UNSIGNED NOT NULL,
			`patient_id` INT (11) UNSIGNED NOT NULL,
		  KEY (`medecin_id`),
		  KEY (`patient_id`)
			) TYPE=MYISAM;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.72");
    $sql = "ALTER TABLE `antecedent`
            CHANGE `type` `type`
            ENUM('med','alle','trans','obst','chir','fam','anesth','gyn','cardio','pulm','stomato','plast','ophtalmo','digestif','gastro','stomie','uro','ortho','traumato','amput','neurochir','greffe','thrombo','cutane','hemato','rhumato','neuropsy','infect','endocrino','carcino','orl','addiction','habitus');";
    $this->addQuery($sql);
    
    // If there is a type
    $sql = "INSERT INTO `antecedent` (`type`, `rques`, `dossier_medical_id`)
            SELECT 'addiction', CONCAT(UPPER(LEFT(`type`, 1)), LOWER(SUBSTRING(`type`, 2)), ': ', `addiction`), `dossier_medical_id`
            FROM `addiction`
            WHERE `type` IS NOT NULL AND `type` <> '0'";
    $this->addQuery($sql);
    
    // If there is no type
    $sql = "INSERT INTO `antecedent` (`type`, `rques`, `dossier_medical_id`)
            SELECT 'addiction', `addiction`, `dossier_medical_id`
            FROM `addiction`
            WHERE `type` IS NULL OR `type` = '0'";
    $this->addQuery($sql);
    
    // If there is a type
    $sql = "UPDATE `aide_saisie` SET 
              `class` = 'CAntecedent', 
              `field` = 'rques', 
              `name` = CONCAT(UPPER(LEFT(`depend_value`, 1)), LOWER(SUBSTRING(`depend_value`, 2)), ': ', `name`),
              `text` = CONCAT(UPPER(LEFT(`depend_value`, 1)), LOWER(SUBSTRING(`depend_value`, 2)), ': ', `text`),
              `depend_value` = 'addiction'
            WHERE 
              `class` = 'CAddiction'
               AND `depend_value` IS NOT NULL";
    $this->addQuery($sql);
    
    // If there is no type
    $sql = "UPDATE `aide_saisie` SET 
              `class` = 'CAntecedent', 
              `field` = 'rques', 
              `depend_value` = 'addiction'
            WHERE 
              `class` = 'CAddiction'
               AND `depend_value` IS NULL";
    $this->addQuery($sql);
    
    $this->addQuery(CSetupdPcompteRendu::getTemplateReplaceQuery("Sejour - Addictions -- toutes", "Sejour - Antécédents - Addictions"));
    $this->addQuery(CSetupdPcompteRendu::getTemplateReplaceQuery("Patient - Addictions -- toutes", "Patient - Antécédents - Addictions"));
    
    $addiction_types = array('tabac', 'oenolisme', 'cannabis');
    foreach ($addiction_types as $type) {
      $typeTrad = CAppUI::tr("CAddiction.type.$type");
      $this->addQuery(CSetupdPcompteRendu::getTemplateReplaceQuery("Sejour - Addictions - $typeTrad", "Sejour - Antécédents - Addictions"));
      $this->addQuery(CSetupdPcompteRendu::getTemplateReplaceQuery("Patient - Addictions - $typeTrad", "Patient - Antécédents - Addictions"));
    }
    
    /*$sql = "DROP TABLE `addiction`";
    $this->addQuery($sql);*/

    $this->makeRevision("0.73");
    $sql = "ALTER TABLE `constantes_medicales` 
						ADD `score_sensibilite` FLOAT,
						ADD `score_motricite` FLOAT,
						ADD `EVA` FLOAT,
						ADD `score_sedation` FLOAT,
						ADD `frequence_respiratoire` FLOAT;";
    $this->addQuery($sql);
    
    
    $this->makeRevision("0.74");
    for ($i = 1; $i <= 3; $i++) {
	    $sql = "INSERT INTO `correspondant` (`medecin_id`, `patient_id`)
	            SELECT `medecin$i`, `patient_id`
	            FROM `patients`
	            WHERE `medecin$i` IS NOT NULL";
	    $this->addQuery($sql);
    }
    $sql = "ALTER TABLE `patients`
					  DROP `medecin1`,
					  DROP `medecin2`,
					  DROP `medecin3`";
    $this->addQuery($sql);
    
    $this->mod_version = "0.75";
  }
}

?>