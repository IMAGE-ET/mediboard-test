<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $utypes;

// MODULE CONFIGURATION DEFINITION
$config = array();
$config["mod_name"]        = "dPcabinet";
$config["mod_version"]     = "0.80";
$config["mod_type"]        = "user";


class CSetupdPcabinet extends CSetup {
  
  function __construct() {
    parent::__construct();
    
    $this->mod_name = "dPcabinet";
    
    $this->makeRevision("all");
    $sql = "CREATE TABLE consultation (
                    consultation_id bigint(20) NOT NULL auto_increment,
                    plageconsult_id bigint(20) NOT NULL default '0',
                    patient_id bigint(20) NOT NULL default '0',
                    heure time NOT NULL default '00:00:00',
                    duree time NOT NULL default '00:00:00',
                    motif text,
                    secteur1 smallint(6) NOT NULL default '0',
                    secteur2 smallint(6) NOT NULL default '0',
                    rques text,
                    PRIMARY KEY  (consultation_id),
                    KEY plageconsult_id (plageconsult_id,patient_id)
                    ) TYPE=MyISAM COMMENT='Table des consultations';";
    $this->addQuery($sql);
    $sql = "CREATE TABLE plageconsult (
                    plageconsult_id bigint(20) NOT NULL auto_increment,
                    chir_id bigint(20) NOT NULL default '0',
                    date date NOT NULL default '0000-00-00',
                    debut time NOT NULL default '00:00:00',
                    fin time NOT NULL default '00:00:00',
                    PRIMARY KEY  (plageconsult_id),
                    KEY chir_id (chir_id)
                    ) TYPE=MyISAM COMMENT='Table des plages de consultation des médecins';";
    $this->addQuery($sql);
    
    
    $this->makeRevision("0.1");
    $sql = "ALTER TABLE plageconsult ADD freq TIME DEFAULT '00:15:00' NOT NULL AFTER date ;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.2");
    $sql = "ALTER TABLE consultation ADD compte_rendu TEXT DEFAULT NULL";
    $this->addQuery($sql);
    
    $this->makeRevision("0.21");
    $sql = "ALTER TABLE consultation CHANGE duree duree TINYINT DEFAULT '1' NOT NULL ";
    $this->addQuery($sql);
    $sql = "UPDATE consultation SET duree='1' ";
    $this->addQuery($sql);
    
    $this->makeRevision("0.22");
    $sql = "ALTER TABLE `consultation` " .
            "\nADD `chrono` TINYINT DEFAULT '16' NOT NULL," .
            "\nADD `annule` TINYINT DEFAULT '0' NOT NULL," .
            "\nADD `paye` TINYINT DEFAULT '0' NOT NULL," .
            "\nADD `cr_valide` TINYINT DEFAULT '0' NOT NULL," .
            "\nADD `examen` TEXT," .
            "\nADD `traitement` TEXT";
    $this->addQuery($sql);
    
    $this->makeRevision("0.23");
    $sql = "ALTER TABLE `consultation` ADD `premiere` TINYINT NOT NULL";
    $this->addQuery($sql);
    
    $this->makeRevision("0.24");
    $sql = "CREATE TABLE `tarifs` (
                `tarif_id` BIGINT NOT NULL AUTO_INCREMENT ,
                `chir_id` BIGINT DEFAULT '0' NOT NULL ,
                `function_id` BIGINT DEFAULT '0' NOT NULL ,
                `description` VARCHAR( 50 ) ,
                `valeur` TINYINT,
                PRIMARY KEY ( `tarif_id` ) ,
                INDEX ( `chir_id`),
                INDEX ( `function_id` )
                ) TYPE=MyISAM COMMENT = 'table des tarifs de consultation';";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `consultation` ADD `tarif` TINYINT,
            ADD `type_tarif` ENUM( 'cheque', 'CB', 'especes', 'tiers', 'autre' ) ;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.25");
    $sql = "ALTER TABLE `tarifs` CHANGE `valeur` `secteur1` FLOAT( 6 ) DEFAULT NULL;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `tarifs` ADD `secteur2` FLOAT( 6 ) NOT NULL;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `consultation` CHANGE `secteur1` `secteur1` FLOAT( 6 ) DEFAULT '0' NOT NULL;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `consultation` CHANGE `secteur2` `secteur2` FLOAT( 6 ) DEFAULT '0' NOT NULL;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `consultation` CHANGE `tarif` `tarif` VARCHAR( 50 ) DEFAULT NULL;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `plageconsult` ADD `libelle` VARCHAR( 50 ) DEFAULT NULL AFTER `chir_id` ;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.26");
    $sql = "ALTER TABLE `consultation` " .
            "\nADD `ordonnance` TEXT DEFAULT NULL," .
            "\nADD `or_valide` TINYINT DEFAULT '0' NOT NULL"; 
    $this->addQuery($sql);
    
    $this->makeRevision("0.27");
    $sql = "ALTER TABLE `consultation` " .
            "\nADD `courrier1` TEXT DEFAULT NULL," .
            "\nADD `c1_valide` TINYINT DEFAULT '0' NOT NULL"; 
    $this->addQuery($sql);
    $sql = "ALTER TABLE `consultation` " .
            "\nADD `courrier2` TEXT DEFAULT NULL," .
            "\nADD `c2_valide` TINYINT DEFAULT '0' NOT NULL"; 
    $this->addQuery($sql);
    
    $this->makeRevision("0.28");
    $sql = "ALTER TABLE `consultation` ADD `date_paiement` DATE AFTER `paye` ;";
    $this->addQuery($sql);
    $sql = "UPDATE consultation, plageconsult
          SET consultation.date_paiement = plageconsult.date
          WHERE consultation.plageconsult_id = plageconsult.plageconsult_id
          AND consultation.paye = 1"; 
    $this->addQuery($sql);
    
    $this->makeRevision("0.29");
    $sql = "CREATE TABLE `consultation_anesth` (
          `consultation_anesth_id` BIGINT NOT NULL AUTO_INCREMENT ,
          `consultation_id` BIGINT DEFAULT '0' NOT NULL ,
          `operation_id` BIGINT DEFAULT '0' NOT NULL ,
          `poid` FLOAT,
          `taille` FLOAT,
          `groupe` ENUM( '0', 'A', 'B', 'AB' ) ,
          `rhesus` ENUM( '+', '-' ) ,
          `antecedents` TEXT,
          `traitements` TEXT,
          `tabac` ENUM( '-', '+', '++' ) ,
          `oenolisme` ENUM( '-', '+', '++' ) ,
          `transfusions` ENUM( '-', '+' ) ,
          `tasys` TINYINT,
          `tadias` TINYINT,
          `listCim10` TEXT,
          `intubation` ENUM( 'dents', 'bouche', 'cou' ) ,
          `biologie` ENUM( 'NF', 'COAG', 'IONO' ) ,
          `commande_sang` ENUM( 'clinique', 'CTS', 'autologue' ) ,
          `ASA` TINYINT,
          PRIMARY KEY ( `consultation_anesth_id` ) ,
          INDEX ( `consultation_id`) ,
          INDEX ( `operation_id` )
          ) TYPE=MyISAM COMMENT = 'Consultations d\'anesthésie';";
    $this->addQuery($sql);
    
    $this->makeRevision("0.30");
    $this->setTimeLimit(1800);
    function setup_moveDocs(){
      $ds = CSQLDataSource::get("std");

      $document_types = array (
      array ("name" => "compte_rendu", "valide" => "cr_valide"),
      array ("name" => "ordonnance", "valide" => "or_valide"),
      array ("name" => "courrier1", "valide" => "c1_valide"),
      array ("name" => "courrier2", "valide" => "c2_valide"));
  
      foreach ($document_types as $document_type) {
        $document_name = $document_type["name"];
        $document_valide = $document_type["valide"];
  
        $sql = "SELECT *" .
          "\nFROM `consultation`" .
          "\nWHERE `$document_name` IS NOT NULL" .
          "\nAND `$document_name` != ''";
        $res = $ds->exec( $sql );
  
        while ($obj = $ds->fetchObject($res)) {
          $document = new CCompteRendu;
          $document->type = "consultation";
          $document->nom = $document_name;
          $document->object_id = $obj->consultation_id;
          $document->source = $obj->$document_name;
          $document->valide = $obj->$document_valide;
          $document->store();
        }
      }
      return true;
    }
    $this->addFunctions("setup_moveDocs");
    
    $this->makeRevision("0.31");
    $sql = "CREATE TABLE `examaudio` (" .
          "\n`examaudio_id` INT NOT NULL AUTO_INCREMENT ," .
          "\n`consultation_id` INT NOT NULL ," .
          "\n`gauche_aerien` VARCHAR( 64 ) ," .
          "\n`gauche_osseux` VARCHAR( 64 ) ," .
          "\n`droite_aerien` VARCHAR( 64 ) ," .
          "\n`droite_osseux` VARCHAR( 64 ) ," .
          "\nPRIMARY KEY ( `examaudio_id` ) ," .
          "\nINDEX ( `consultation_id` )) TYPE=MyISAM";
    $this->addQuery($sql);
    
    $this->makeRevision("0.32");
    $sql = "ALTER TABLE `examaudio` ADD UNIQUE (`consultation_id`)";
    $this->addQuery($sql);
    
    $this->makeRevision("0.33");
    $sql = "ALTER TABLE `examaudio` " .
          "\nADD `remarques` TEXT AFTER `consultation_id`," .
          "\nADD `gauche_conlat` VARCHAR( 64 ) ," .
          "\nADD `gauche_ipslat` VARCHAR( 64 ) ," .
          "\nADD `gauche_pasrep` VARCHAR( 64 ) ," .
          "\nADD `gauche_vocale` VARCHAR( 64 ) ," .
          "\nADD `gauche_tympan` VARCHAR( 64 ) ," .
          "\nADD `droite_conlat` VARCHAR( 64 ) ," .
          "\nADD `droite_ipslat` VARCHAR( 64 ) ," .
          "\nADD `droite_pasrep` VARCHAR( 64 ) ," .
          "\nADD `droite_vocale` VARCHAR( 64 ) ," .
          "\nADD `droite_tympan` VARCHAR( 64 )";
    $this->addQuery($sql);
    
    $this->makeRevision("0.34");
    $sql = "ALTER TABLE `consultation_anesth`
          CHANGE `groupe` `groupe` ENUM( '?', '0', 'A', 'B', 'AB' ) DEFAULT '?' NOT NULL ,
          CHANGE `rhesus` `rhesus` ENUM( '?', '+', '-' ) DEFAULT '?' NOT NULL ,
          CHANGE `tabac` `tabac` ENUM( '?', '-', '+', '++' ) DEFAULT '?' NOT NULL ,
          CHANGE `oenolisme` `oenolisme` ENUM( '?', '-', '+', '++' ) DEFAULT '?' NOT NULL ,
          CHANGE `transfusions` `transfusions` ENUM( '?', '-', '+' ) DEFAULT '?' NOT NULL ,
          CHANGE `intubation` `intubation` ENUM( '?', 'dents', 'bouche', 'cou' ) DEFAULT '?' NOT NULL ,
          CHANGE `biologie` `biologie` ENUM( '?', 'NF', 'COAG', 'IONO' ) DEFAULT '?' NOT NULL ,
          CHANGE `commande_sang` `commande_sang` ENUM( '?', 'clinique', 'CTS', 'autologue' ) DEFAULT '?' NOT NULL ;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `consultation_anesth`
          CHANGE `tasys` `tasys` INT( 5 ) DEFAULT NULL ,
          CHANGE `tadias` `tadias` INT( 5 ) DEFAULT NULL;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.35");
    $sql = "ALTER TABLE `consultation` ADD `arrivee` DATETIME AFTER `type_tarif` ;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.36");
    $sql = "ALTER TABLE `consultation_anesth`" .
          "CHANGE `groupe` `groupe` ENUM( '?', 'O', 'A', 'B', 'AB' )" .
          "DEFAULT '?' NOT NULL ;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.37");
    $this->makeRevision("0.38");
    
    $this->makeRevision("0.39");
    $sql = "ALTER TABLE `consultation_anesth`
              ADD `mallampati` ENUM( 'classe1', 'classe2', 'classe3', 'classe4' ),
              ADD `bouche` ENUM( 'm20', 'm35', 'p35' ),
              ADD `distThyro` ENUM( 'm65', 'p65' ),
              ADD `etatBucco` VARCHAR(50),
              ADD `conclusion` VARCHAR(50),
              ADD `position` ENUM( 'DD', 'DV', 'DL', 'GP', 'AS', 'TO' );";
    $this->addQuery($sql);
    
    $this->makeRevision("0.40");
    $this->makeRevision("0.41");
    
    $this->makeRevision("0.42");
    $this->setTimeLimit(1800);
    $sql = "ALTER TABLE `consultation` DROP INDEX `plageconsult_id`  ;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `consultation` ADD INDEX ( `plageconsult_id` ) ;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `consultation` ADD INDEX ( `patient_id` ) ;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `tarifs` DROP INDEX `chir_id` ;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `tarifs` ADD INDEX ( `chir_id` ) ;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `tarifs` ADD INDEX ( `function_id` ) ;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.43");
    $sql = "ALTER TABLE `consultation_anesth`" .
          "CHANGE `position` `position` ENUM( 'DD', 'DV', 'DL', 'GP', 'AS', 'TO', 'GYN');";
    $this->addQuery($sql);
    $sql = "CREATE TABLE `techniques_anesth` (
               `technique_id` INT NOT NULL AUTO_INCREMENT ,
               `consultAnesth_id` INT NOT NULL ,
               `technique` TEXT NOT NULL ,
               PRIMARY KEY ( `technique_id` )) TYPE=MyISAM";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `consultation_anesth`
            ADD `rai` float default NULL,
            ADD `hb` float default NULL,
            ADD `tp` float default NULL,
            ADD `tca` time NOT NULL default '00:00:00',
            ADD `creatinine` float default NULL,
            ADD `na` float default NULL,
            ADD `k` float default NULL,
            ADD `tsivy` time NOT NULL default '00:00:00',
            ADD `plaquettes` INT(7) default NULL,
            ADD `ht` float default NULL,
            ADD `ecbu` ENUM( '?', 'NEG', 'POS' ) DEFAULT '?' NOT NULL,
            ADD `ecbu_detail` TEXT,
            ADD `pouls` INT(4) default NULL,
            ADD `spo2` float default NULL;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `consultation_anesth` CHANGE `operation_id` `operation_id` BIGINT( 20 ) NULL DEFAULT NULL;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `consultation_anesth` " .
            "\nCHANGE `etatBucco` `etatBucco` TEXT DEFAULT NULL ," .
            "\nCHANGE `conclusion` `conclusion` TEXT DEFAULT NULL ";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `consultation_anesth` " .
            "\nCHANGE `tabac` `tabac` TEXT DEFAULT NULL ," .
            "\nCHANGE `oenolisme` `oenolisme` TEXT DEFAULT NULL ";
    $this->addQuery($sql);
    $sql = "CREATE TABLE `exams_comp` (
               `exam_id` INT NOT NULL AUTO_INCREMENT ,
               `consult_id` INT NOT NULL ,
               `examen` TEXT NOT NULL ,
               `fait` tinyint(1) NOT NULL default 0,
               PRIMARY KEY ( `exam_id` )) TYPE=MyISAM";
    $this->addQuery($sql);
    
    $this->makeRevision("0.44");
    $this->addDependency("mediusers", "0.1");
    function setup_consultAnesth(){
      $ds = CSQLDataSource::get("std");
 
      global $utypes;
      $utypes_flip = array_flip($utypes);
      $id_anesth = $utypes_flip["Anesthésiste"];
      $sql = "SELECT users.user_id" .
             "\nFROM users, users_mediboard" .
             "\nWHERE users.user_id = users_mediboard.user_id" .
             "\nAND users.user_type='$id_anesth'";
      $result = $ds->loadList($sql);
      $listAnesthid = array();
      foreach($result as $keyresult => $resultAnesth){
        $listAnesthid[$keyresult] = $result[$keyresult]["user_id"];
      } 
       
      $sql = "SELECT consultation.consultation_id FROM consultation" .
             "\nLEFT JOIN consultation_anesth ON consultation.consultation_id = consultation_anesth.consultation_id" .
             "\nLEFT JOIN plageconsult ON consultation.plageconsult_id = plageconsult.plageconsult_id" .
             "\nWHERE plageconsult.chir_id " . $ds->prepareIn($listAnesthid) .
             "\nAND consultation_anesth.consultation_anesth_id IS NULL" ;  
      $result = $ds->loadList($sql);

      foreach($result as $keyresult => $resultAnesth){
        $consultAnesth = new CConsultAnesth;
        $consultAnesth->consultation_anesth_id = 0;
        $consultAnesth->consultation_id = $result[$keyresult]["consultation_id"];
        $consultAnesth->store();
      }
      return true;
    }
    $this->addFunctions("setup_consultAnesth");
    
    $this->makeRevision("0.45");
    $sql = "ALTER TABLE `exams_comp` CHANGE `consult_id` `consultation_id` INT NOT NULL ;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `techniques_anesth` CHANGE `consultAnesth_id` `consultation_anesth_id` INT NOT NULL ;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.46");
    $sql = "ALTER TABLE `consultation_anesth` CHANGE `tca` `tca` TINYINT(2) NULL ;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `consultation_anesth` " .
            "\nADD `tca_temoin` TINYINT(2) NULL AFTER `tca`," .
            "\nADD `ht_final` FLOAT DEFAULT NULL AFTER `ht`;" ;
    $this->addQuery($sql);
    $sql = "ALTER TABLE `consultation_anesth` DROP `transfusions`";
    $this->addQuery($sql);
    
    $this->makeRevision("0.47");
    $sql = "ALTER TABLE `consultation_anesth` CHANGE `rhesus` `rhesus` ENUM( '?', '+', '-', 'POS', 'NEG') DEFAULT '?' NOT NULL ;";
    $this->addQuery($sql);
    $sql = "UPDATE `consultation_anesth` SET `rhesus`='POS' WHERE `rhesus`='+';";
    $this->addQuery($sql);
    $sql = "UPDATE `consultation_anesth` SET `rhesus`='NEG' WHERE `rhesus`='-';";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `consultation_anesth` CHANGE `rhesus` `rhesus` ENUM( '?', 'POS', 'NEG') DEFAULT '?' NOT NULL ;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `consultation_anesth` CHANGE `rai` `rai` ENUM( '?', 'POS', 'NEG') DEFAULT '?' NOT NULL ;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `consultation_anesth` DROP `ecbu_detail`";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `consultation_anesth` ".
               "\nADD `premedication` TEXT," .
               "\nADD `prepa_preop` TEXT;" ;
    $this->addQuery($sql);
    
    $this->makeRevision("0.48");
    $this->setTimeLimit(1800);
    $sql = "ALTER TABLE `consultation_anesth` " .
               "\nCHANGE `consultation_anesth_id` `consultation_anesth_id` int(11) unsigned NOT NULL AUTO_INCREMENT," .
               "\nCHANGE `consultation_id` `consultation_id` int(11) unsigned NOT NULL DEFAULT '0'," .
               "\nCHANGE `operation_id` `operation_id` int(11) unsigned NULL," .
               "\nCHANGE `poid` `poid` float unsigned NULL," .
               "\nCHANGE `rhesus` `rhesus` enum('?','NEG','POS') NOT NULL DEFAULT '?'," .
               "\nCHANGE `rai` `rai` enum('?','NEG','POS') NOT NULL DEFAULT '?'," .
               "\nCHANGE `tasys` `tasys` int(5) unsigned zerofill NULL," .
               "\nCHANGE `tadias` `tadias` int(5) unsigned zerofill NULL," .
               "\nCHANGE `plaquettes` `plaquettes` int(7) unsigned zerofill NULL," .
               "\nCHANGE `pouls` `pouls` mediumint(4) unsigned zerofill NULL," .
               "\nCHANGE `ASA` `ASA` enum('1','2','3','4','5') NULL," .
               "\nCHANGE `tca` `tca` tinyint(2) unsigned zerofill NULL," .
               "\nCHANGE `tca_temoin` `tca_temoin` tinyint(2) unsigned zerofill NULL;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `consultation_anesth` " .
               "\nDROP `listCim10`;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `consultation` " .
               "\nCHANGE `consultation_id` `consultation_id` int(11) unsigned NOT NULL AUTO_INCREMENT," .
               "\nCHANGE `plageconsult_id` `plageconsult_id` int(11) unsigned NOT NULL DEFAULT '0'," .
               "\nCHANGE `patient_id` `patient_id` int(11) unsigned NOT NULL DEFAULT '0'," .
               "\nCHANGE `duree` `duree` tinyint(1) unsigned zerofill NOT NULL DEFAULT '1'," .
               "\nCHANGE `annule` `annule` enum('0','1') NOT NULL DEFAULT '0'," .
               "\nCHANGE `chrono` `chrono` enum('16','32','48','64') NOT NULL DEFAULT '16'," .
               "\nCHANGE `paye` `paye` enum('0','1') NOT NULL DEFAULT '0'," .
               "\nCHANGE `premiere` `premiere` enum('0','1') NOT NULL DEFAULT '0'," .
               "\nCHANGE `tarif` `tarif` varchar(255) NULL;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `consultation` " .
               "\nDROP `compte_rendu`," .
               "\nDROP `cr_valide`," .
               "\nDROP `ordonnance`," .
               "\nDROP `or_valide`," .
               "\nDROP `courrier1`," .
               "\nDROP `c1_valide`," .
               "\nDROP `courrier2`," .
               "\nDROP `c2_valide`;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `examaudio` " .
               "\nCHANGE `examaudio_id` `examaudio_id` int(11) unsigned NOT NULL AUTO_INCREMENT," .
               "\nCHANGE `consultation_id` `consultation_id` int(11) unsigned NOT NULL DEFAULT '0';";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `exams_comp` " .
               "\nCHANGE `exam_id` `exam_id` int(11) unsigned NOT NULL AUTO_INCREMENT," .
               "\nCHANGE `consultation_id` `consultation_id` int(11) unsigned NOT NULL DEFAULT '0'," .
               "\nCHANGE `fait` `fait` tinyint(4) NOT NULL DEFAULT '0';";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `plageconsult` " .
               "\nCHANGE `plageconsult_id` `plageconsult_id` int(11) unsigned NOT NULL AUTO_INCREMENT," .
               "\nCHANGE `chir_id` `chir_id` int(11) unsigned NOT NULL DEFAULT '0'," .
               "\nCHANGE `libelle` `libelle` varchar(255) NULL;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `tarifs` " .
               "\nCHANGE `tarif_id` `tarif_id` int(11) unsigned NOT NULL AUTO_INCREMENT," .
               "\nCHANGE `chir_id` `chir_id` int(11) unsigned NOT NULL DEFAULT '0'," .
               "\nCHANGE `function_id` `function_id` int(11) unsigned NOT NULL DEFAULT '0'," .
               "\nCHANGE `description` `description` varchar(255) NOT NULL," .
               "\nCHANGE `secteur1` `secteur1` float NOT NULL;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `techniques_anesth` " .
               "\nCHANGE `technique_id` `technique_id` int(11) unsigned NOT NULL AUTO_INCREMENT," .
               "\nCHANGE `consultation_anesth_id` `consultation_anesth_id` int(11) unsigned NOT NULL DEFAULT '0';";
    $this->addQuery($sql);
    
    $this->makeRevision("0.49");
    $sql = "ALTER TABLE `consultation_anesth` " .
               "\nCHANGE `tasys` `tasys` TINYINT(4) NULL," .
               "\nCHANGE `tadias` `tadias` TINYINT(4) NULL;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.50");
    $sql = "ALTER TABLE `consultation` CHANGE `patient_id` `patient_id` int(11) unsigned NULL;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.51");
    $sql = "UPDATE `consultation` SET `annule` = '0' WHERE (`annule` = '' OR `annule` IS NULL );";
    $this->addQuery($sql);
    
    $this->makeRevision("0.52");
    $sql = "UPDATE `consultation` SET `patient_id` = NULL WHERE (`patient_id` = 0 );";
    $this->addQuery($sql);
    
    $this->makeRevision("0.53");
    $sql = "CREATE TABLE `exampossum` (
                    `exampossum_id` int(11) unsigned NOT NULL auto_increment,
                    `consultation_id` int(11) unsigned NOT NULL DEFAULT '0',
                    `age` enum('inf60','61','sup71') NULL,
                    `ouverture_yeux` enum('spontane','bruit','douleur','jamais') NULL,
                    `rep_verbale` enum('oriente','confuse','inapproprie','incomprehensible','aucune') NULL,
                    `rep_motrice` enum('obeit','oriente','evitement','decortication','decerebration','rien') NULL,
                    `signes_respiratoires` enum('aucun','dyspnee_effort','bpco_leger','dyspnee_inval','bpco_modere','dyspnee_repos','fibrose') NULL,
                    `uree` enum('inf7.5','7.6','10.1','sup15.1') NULL,
                    `freq_cardiaque` enum('inf39','40','50','81','101','sup121') NULL,
                    `signes_cardiaques` enum('aucun','diuretique','antiangineux','oedemes','cardio_modere','turgescence','cardio') NULL,
                    `hb` enum('inf9.9','10','11.5','13','16.1','17.1','sup18.1') NULL,
                    `leucocytes` enum('inf3000','3100','4000','10100','sup20100') NULL,
                    `ecg` enum('normal','fa','autre','sup5','anomalie') NULL,
                    `kaliemie` enum('inf2.8','2.9','3.2','3.5','5.1','5.4','sup6.0') NULL,
                    `natremie` enum('inf125','126','131','sup136') NULL,
                    `pression_arterielle` enum('inf89','90','100','110','131','sup171') NULL,
                    `gravite` enum('min','moy','maj','maj+') NULL,
                    `nb_interv` enum('1','2','sup2') NULL,
                    `pertes_sanguines` enum('inf100','101','501','sup1000') NULL,
                    `contam_peritoneale` enum('aucune','mineure','purulente','diffusion') NULL,
                    `cancer` enum('absense','tumeur','ganglion','metastases') NULL,
                    `circonstances_interv` enum('reglee','urg','prgm','sansdelai') NULL,
                    PRIMARY KEY  (`exampossum_id`),
                    KEY `consultation_id` (`consultation_id`)
                    ) TYPE=MyISAM COMMENT='Table pour le calcul possum';";
    $this->addQuery($sql);
    
    $this->makeRevision("0.54");
    $sql = "CREATE TABLE `examnyha` (
                    `examnyha_id` int(11) unsigned NOT NULL auto_increment,
                    `consultation_id` int(11) unsigned NOT NULL DEFAULT '0',
                    `q1` enum('0','1') NULL,
                    `q2a` enum('0','1') NULL,
                    `q2b` enum('0','1') NULL,
                    `q3a` enum('0','1') NULL,
                    `q3b` enum('0','1') NULL,
                    `hesitation` enum('0','1') NOT NULL DEFAULT '0',
                    PRIMARY KEY  (`examnyha_id`),
                    KEY `consultation_id` (`consultation_id`)
                    ) TYPE=MyISAM COMMENT='Table pour la classe NYHA';";
    $this->addQuery($sql);
    
    $this->makeRevision("0.55");
    $sql = "ALTER TABLE `consultation_anesth` ADD `listCim10` TEXT DEFAULT NULL ;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.56");
    $this->addDependency("dPplanningOp", "0.63");
    function setup_cleanOperationIdError(){
      $ds = CSQLDataSource::get("std");
      $where = array();
      $where["consultation_anesth.operation_id"] = "!= 0";
      $where[] = "consultation_anesth.operation_id IS NOT NULL";
      $where[] = "(SELECT COUNT(operations.operation_id) FROM operations WHERE operation_id=consultation_anesth.operation_id)=0";
      
      $sql = new CRequest();
      $sql->addSelect("consultation_anesth_id");
      $sql->addTable("consultation_anesth");
      $sql->addWhere($where);
      $aKeyxAnesth = $ds->loadColumn($sql->getRequest());
      if($aKeyxAnesth === false){
        return false;
      }
      if(count($aKeyxAnesth)) {
        $sql = "UPDATE consultation_anesth SET operation_id = NULL WHERE (consultation_anesth_id ".$ds->prepareIn($aKeyxAnesth).")";
        if (!$ds->exec($sql)) {
          return false;
        }
        return true;
      }
      return true;
    }
    $this->addFunctions("setup_cleanOperationIdError");
    
    $this->makeRevision("0.57");
    $this->setTimeLimit(1800);
    $sql = "ALTER TABLE `consultation` ADD INDEX ( `heure` )";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `consultation` ADD INDEX ( `annule` )";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `consultation` ADD INDEX ( `paye` )";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `consultation` ADD INDEX ( `date_paiement` )";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `plageconsult` ADD INDEX ( `date` )";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `plageconsult` ADD INDEX ( `debut` )";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `plageconsult` ADD INDEX ( `fin` )";
    $this->addQuery($sql);
    
    $this->makeRevision("0.58");
    $this->setTimeLimit(1800);
    $this->addDependency("dPpatients", "0.41");
    $sql = "INSERT INTO antecedent
            SELECT '', consultation_anesth.consultation_anesth_id, antecedent.type, antecedent.date, antecedent.rques, 'CConsultAnesth' 
            FROM antecedent, consultation_anesth, consultation
            WHERE antecedent.object_class = 'CPatient'
              AND antecedent.object_id = consultation.patient_id
              AND consultation.consultation_id = consultation_anesth.consultation_id";
    $this->addQuery($sql);
    $sql = "INSERT INTO traitement
            SELECT '', consultation_anesth.consultation_anesth_id, traitement.debut, traitement.fin, traitement.traitement, 'CConsultAnesth' 
            FROM traitement, consultation_anesth, consultation
            WHERE traitement.object_class = 'CPatient'
              AND traitement.object_id = consultation.patient_id
              AND consultation.consultation_id = consultation_anesth.consultation_id";
    $this->addQuery($sql);
    $sql = "UPDATE consultation_anesth, consultation, patients
            SET consultation_anesth.listCim10 = patients.listCim10
            WHERE consultation_anesth.consultation_id = consultation.consultation_id
              AND consultation.patient_id = patients.patient_id";
    $this->addQuery($sql);
    
    $this->makeRevision("0.59");
    $sql = "ALTER TABLE `exams_comp` ADD `realisation` ENUM( 'avant', 'pendant' ) NOT NULL DEFAULT 'avant' AFTER `consultation_id`;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.60");
    $sql = "CREATE TABLE `addiction` (
            `addiction_id` int(11) unsigned NOT NULL auto_increment,
            `object_id` int(11) unsigned NOT NULL default '0',
            `object_class` enum('CConsultAnesth') NOT NULL default 'CConsultAnesth',
            `type` enum('tabac', 'oenolisme', 'cannabis') NOT NULL default 'tabac',
            `addiction` text,
            PRIMARY KEY  (`addiction_id`)
            ) TYPE=MyISAM COMMENT = 'Addictions pour le dossier anesthésie';";
    $this->addQuery($sql);
    
    $this->makeRevision("0.61");
    $sql = "INSERT INTO `user_preferences` ( `pref_user` , `pref_name` , `pref_value` )" .
        "\nVALUES ('0', 'DefaultPeriod', 'month');";
    $this->addQuery($sql);
    
    $this->makeRevision("0.62");
    $sql = "ALTER TABLE `tarifs` " .
           "\nCHANGE `chir_id` `chir_id` int(11) unsigned NULL DEFAULT NULL," .
           "\nCHANGE `function_id` `function_id` int(11) unsigned NULL DEFAULT NULL;";
    $this->addQuery($sql);
    $sql = "UPDATE `tarifs` SET function_id = NULL WHERE function_id='0';";
    $this->addQuery($sql);
    $sql = "UPDATE `tarifs` SET chir_id = NULL WHERE chir_id='0';";
    $this->addQuery($sql);
    $sql = "DELETE FROM `consultation_anesth` WHERE `consultation_id`= '0'";
    $this->addQuery($sql);
    $sql = "UPDATE `consultation_anesth` SET operation_id = NULL WHERE operation_id='0';";
    $this->addQuery($sql);
    $sql = "DELETE FROM `exams_comp` WHERE `consultation_id`= '0'";
    $this->addQuery($sql);
    
    $this->makeRevision("0.63");
    $sql = "INSERT INTO `user_preferences` ( `pref_user` , `pref_name` , `pref_value` )" .
        "\nVALUES ('0', 'simpleCabinet', '0');";
    $this->addQuery($sql);
    
    $this->makeRevision("0.64");
    $sql = "INSERT INTO `user_preferences` ( `pref_user` , `pref_name` , `pref_value` )" .
        "\nVALUES ('0', 'GestionFSE', '0');";
    $this->addQuery($sql);
    
    $this->makeRevision("0.65");
    $sql = "INSERT INTO `user_preferences` ( `pref_user` , `pref_name` , `pref_value` )" .
        "\nVALUES ('0', 'DossierCabinet', 'dPcabinet');";
    $this->addQuery($sql);
    
    $this->makeRevision("0.66");
    $sql = "UPDATE `consultation` SET  `rques` = NULL  WHERE `rques` = 'NULL'";
    $this->addQuery($sql);
    $sql = "UPDATE `consultation` SET  `motif` = NULL  WHERE `motif` = 'NULL'";
    $this->addQuery($sql);
    $sql = "UPDATE `consultation` SET  `traitement` = NULL  WHERE `traitement` = 'NULL'";
    $this->addQuery($sql);
    $sql = "UPDATE `consultation` SET  `examen` = NULL  WHERE `examen` = 'NULL'";
    $this->addQuery($sql);

    $this->makeRevision("0.67");
    $sql = "ALTER TABLE `consultation` ADD `codes_ccam` VARCHAR(255);";
    $this->addQuery($sql);
    
    $this->makeRevision("0.68");
    $sql = "INSERT INTO `user_preferences` ( `pref_user` , `pref_name` , `pref_value` )" .
        "\nVALUES ('0', 'ccam', '0');";
    $this->addQuery($sql);
    
    $this->makeRevision("0.69");
    $sql = "ALTER TABLE `tarifs` ADD `codes_ccam` VARCHAR(255);";
    $this->addQuery($sql);
    
    $this->makeRevision("0.70");
    $sql = "UPDATE `consultation_anesth` SET  `plaquettes` = `plaquettes`/1000";
    $this->addQuery($sql);
    
    $this->makeRevision("0.71");
    $sql = "ALTER TABLE `consultation_anesth` " .
           "CHANGE `plaquettes` `plaquettes` int(4) unsigned zerofill NULL";
    $this->addQuery($sql);
    
    $this->makeRevision("0.72");
    $sql = "CREATE TABLE `banque` (
             `banque_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT, 
             `nom` VARCHAR(255) NOT NULL, 
             `description` VARCHAR(255), 
              PRIMARY KEY (`banque_id`)) TYPE=MYISAM;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.73");
    $sql = "ALTER TABLE `consultation` ADD `banque_id` INT(11) UNSIGNED;";
    $this->addQuery($sql);

    $this->makeRevision("0.74");
    $sql = " INSERT INTO `banque` ( `banque_id` , `nom` , `description` ) VALUES ( '' , 'AXA Banque', '') ;";
    $this->addQuery($sql);
    $sql = " INSERT INTO `banque` ( `banque_id` , `nom` , `description` ) VALUES ( '' , 'Banque accord', '') ;";
    $this->addQuery($sql);
    $sql = " INSERT INTO `banque` ( `banque_id` , `nom` , `description` ) VALUES ( '' , 'LCL', '') ;";
    $this->addQuery($sql);
    $sql = " INSERT INTO `banque` ( `banque_id` , `nom` , `description` ) VALUES ( '' , 'Banque Populaire', '') ;";
    $this->addQuery($sql);
    $sql = " INSERT INTO `banque` ( `banque_id` , `nom` , `description` ) VALUES ( '' , 'Natexis', '') ;";
    $this->addQuery($sql);
    $sql = " INSERT INTO `banque` ( `banque_id` , `nom` , `description` ) VALUES ( '' , 'La banque Postale', '') ;";
    $this->addQuery($sql);
    $sql = " INSERT INTO `banque` ( `banque_id` , `nom` , `description` ) VALUES ( '' , 'BNP Paribas', '') ;";
    $this->addQuery($sql);
    $sql = " INSERT INTO `banque` ( `banque_id` , `nom` , `description` ) VALUES ( '' , 'Caisse d\'epargne', '') ;";
    $this->addQuery($sql);
    $sql = " INSERT INTO `banque` ( `banque_id` , `nom` , `description` ) VALUES ( '' , 'Ixis', '') ;";
    $this->addQuery($sql);
    $sql = " INSERT INTO `banque` ( `banque_id` , `nom` , `description` ) VALUES ( '' , 'Océor', '') ;";
    $this->addQuery($sql);
    $sql = " INSERT INTO `banque` ( `banque_id` , `nom` , `description` ) VALUES ( '' , 'Banque Palatine', '') ;";
    $this->addQuery($sql);
    $sql = " INSERT INTO `banque` ( `banque_id` , `nom` , `description` ) VALUES ( '' , 'Crédit Foncier', '') ;";
    $this->addQuery($sql);
    $sql = " INSERT INTO `banque` ( `banque_id` , `nom` , `description` ) VALUES ( '' , 'Compagnie 1818', '') ;";
    $this->addQuery($sql);
    $sql = " INSERT INTO `banque` ( `banque_id` , `nom` , `description` ) VALUES ( '' , 'Caisse des dépôts', 'Caisse des dépôts et consignations') ;";
    $this->addQuery($sql);
    $sql = " INSERT INTO `banque` ( `banque_id` , `nom` , `description` ) VALUES ( '' , 'Crédit Agricole', '') ;";
    $this->addQuery($sql);
    $sql = " INSERT INTO `banque` ( `banque_id` , `nom` , `description` ) VALUES ( '' , 'HSBC', '') ;";
    $this->addQuery($sql);
    $sql = " INSERT INTO `banque` ( `banque_id` , `nom` , `description` ) VALUES ( '' , 'Crédit coopératif', '') ;";
    $this->addQuery($sql);
    $sql = " INSERT INTO `banque` ( `banque_id` , `nom` , `description` ) VALUES ( '' , 'Crédit Mutuel', '') ;";
    $this->addQuery($sql);
    $sql = " INSERT INTO `banque` ( `banque_id` , `nom` , `description` ) VALUES ( '' , 'CIC', 'Crédit Industriel et Commercial') ;";
    $this->addQuery($sql);
    $sql = " INSERT INTO `banque` ( `banque_id` , `nom` , `description` ) VALUES ( '' , 'Dexia', '') ;";
    $this->addQuery($sql);
    $sql = " INSERT INTO `banque` ( `banque_id` , `nom` , `description` ) VALUES ( '' , 'Société générale', '') ;";
    $this->addQuery($sql);
    $sql = " INSERT INTO `banque` ( `banque_id` , `nom` , `description` ) VALUES ( '' , 'Groupama Banque', '') ;";
    $this->addQuery($sql); 
    $sql = " INSERT INTO `banque` ( `banque_id` , `nom` , `description` ) VALUES ( '' , 'Crédit du Nord', '') ;"; 
    $this->addQuery($sql);
    $sql = " INSERT INTO `banque` ( `banque_id` , `nom` , `description` ) VALUES ( '' , 'Banque Courtois', '') ;";
    $this->addQuery($sql);
    $sql = " INSERT INTO `banque` ( `banque_id` , `nom` , `description` ) VALUES ( '' , 'Banque Tarneaud', '') ;";
    $this->addQuery($sql);
    $sql = " INSERT INTO `banque` ( `banque_id` , `nom` , `description` ) VALUES ( '' , 'Banque Kolb', '') ;";
    $this->addQuery($sql);
    $sql = " INSERT INTO `banque` ( `banque_id` , `nom` , `description` ) VALUES ( '' , 'Banque Laydernier', '') ;";
    $this->addQuery($sql);
    $sql = " INSERT INTO `banque` ( `banque_id` , `nom` , `description` ) VALUES ( '' , 'Banque Nuger', '') ;";
    $this->addQuery($sql);
    $sql = " INSERT INTO `banque` ( `banque_id` , `nom` , `description` ) VALUES ( '' , 'Banque Rhône-Alpes', '') ;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.75");
    $sql = "CREATE TABLE `consultation_cat` (
            `categorie_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT, 
            `function_id` INT(11) UNSIGNED NOT NULL, 
            `nom_categorie` VARCHAR(255) NOT NULL, 
            `nom_icone` VARCHAR(255) NOT NULL, 
             PRIMARY KEY (`categorie_id`)) TYPE=MYISAM;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.76");
    $sql = "ALTER TABLE `consultation`
      ADD `categorie_id` INT(11) UNSIGNED;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.77");
    // Tranfert des addictions tabac vers la consultation d'anesthésie
    $sql = "INSERT INTO `addiction` ( `addiction_id` , `object_id` , `object_class` , `type` , `addiction` )
			SELECT null,`consultation_anesth_id`, 'CConsultAnesth', 'tabac', `tabac` 
			FROM `consultation_anesth` 
			WHERE `tabac` IS NOT NULL
			AND `tabac` <> ''";
    $this->addQuery($sql);

    // Tranfert des addictions tabac vers le dossier patient
    $sql = "INSERT INTO `addiction` ( `addiction_id` , `object_id` , `object_class` , `type` , `addiction` )
			SELECT null,`patient_id`, 'CPatient', 'tabac', `tabac`
			FROM `consultation_anesth`, `consultation`
			WHERE `tabac` IS NOT NULL
			AND `tabac` <> ''
			AND `consultation`.`consultation_id` = `consultation_anesth`.`consultation_id`";
    $this->addQuery($sql);

    // Tranfert des addictions oenolisme vers la consultation d'anesthésie
    $sql = "INSERT INTO `addiction` ( `addiction_id` , `object_id` , `object_class` , `type` , `addiction` )
			SELECT null,`consultation_anesth_id`, 'CConsultAnesth', 'oenolisme', `oenolisme` 
			FROM `consultation_anesth` 
			WHERE `oenolisme` IS NOT NULL
			AND `oenolisme` <> ''";
    $this->addQuery($sql);

    // Tranfert des addictions oenolisme vers le dossier patient
    $sql = "INSERT INTO `addiction` ( `addiction_id` , `object_id` , `object_class` , `type` , `addiction` )
			SELECT null,`patient_id`, 'CPatient', 'oenolisme', `oenolisme`
			FROM `consultation_anesth`, `consultation`
			WHERE `oenolisme` IS NOT NULL
			AND `oenolisme` <> ''
			AND `consultation`.`consultation_id` = `consultation_anesth`.`consultation_id`";
    $this->addQuery($sql);

    // Transfert des aides à la saisie
    $this->addDependency("dPcompteRendu", "0.31");
    $sql = "UPDATE `aide_saisie`
			SET `class`='CAddiction',`depend_value`=`field`, `field`='addiction'
			WHERE `class` = 'CConsultAnesth'
			AND `field` IN('oenolisme','tabac')";
    $this->addQuery($sql);
    
    $this->makeRevision("0.78");
    $sql = "ALTER TABLE `consultation` ADD `adresse` enum('0','1') NOT NULL DEFAULT '0'";
    $this->addQuery($sql);
    
    $this->makeRevision("0.79");
    $sql = "ALTER TABLE `consultation_anesth`
            DROP `listCim10`;";
    $this->addQuery($sql);
    
    $this->mod_version = "0.80";
  }
}
?>