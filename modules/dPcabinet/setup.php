<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPcabinet
 * @version $Revision$
 * @author Romain Ollivier
 */

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
    
    // CR passage des champs à enregistrements supprimé car regressifs
//    $this->makeRevision("0.30");
    
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
 
      $utypes_flip = array_flip(CUser::$types);
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
    $this->addPrefQuery("DefaultPeriod", "month");
    
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
    $this->addPrefQuery("simpleCabinet", "0");
    
    $this->makeRevision("0.64");
    $this->addPrefQuery("GestionFSE", "0");
    
    $this->makeRevision("0.65");
    $this->addPrefQuery("DossierCabinet", "dPcabinet");
    
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
    $this->addPrefQuery("ccam", "0");
    
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
    // Ne pas supprimer le champs listCim10 de la consultAnesth afin d'avoir fait l'import dans dPpatient
    $this->addDependency("dPpatients", "0.51");
    $sql = "ALTER TABLE `consultation_anesth`
            DROP `listCim10`;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.80");
    $sql = "CREATE TABLE `acte_ngap` (
            `acte_ngap_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT, 
            `code` VARCHAR(3) NOT NULL, 
            `quantite` INT(11) NOT NULL, 
            `coefficient` FLOAT NOT NULL, 
            `consultation_id` INT(11) UNSIGNED NOT NULL, 
            PRIMARY KEY (`acte_ngap_id`)) TYPE=MYISAM;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.81");
    $sql = "ALTER TABLE `tarifs` ADD `codes_ngap` VARCHAR(255);";
    $this->addQuery($sql);
    
    $this->makeRevision("0.82");
    $sql = "ALTER TABLE `acte_ngap`
            ADD `montant_depassement` FLOAT, 
            ADD `montant_base` FLOAT;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.83");
    $sql = "ALTER TABLE `consultation`
            ADD `valide` ENUM('0','1') NOT NULL DEFAULT '0';";
    $this->addQuery($sql);
    
    $sql = "UPDATE `consultation`
			      SET `valide` = '1'
		     	  WHERE `tarif` IS NOT NULL;";
    $this->addQuery($sql);
     
    
    $this->makeRevision("0.84");
    $sql = "ALTER TABLE `consultation`
            CHANGE `type_tarif` `mode_reglement` ENUM( 'cheque', 'CB', 'especes', 'tiers', 'autre' ),
            CHANGE `paye` `patient_regle` ENUM('0','1');";
    $this->addQuery($sql);
         
    $sql = "ALTER TABLE `consultation`
            ADD `total_amc` FLOAT,
            ADD `total_amo` FLOAT,
            ADD `total_assure` FLOAT,
            ADD `facture_acquittee` ENUM('0','1'), 
            ADD `a_regler` FLOAT DEFAULT '0.0';"; 
    $this->addQuery($sql); 
       
    $sql = "UPDATE `consultation`
            SET `a_regler` = `secteur1` + `secteur2`
            WHERE `mode_reglement` <> 'tiers'
						OR `mode_reglement` IS NULL;";
    $this->addQuery($sql);
    
    $sql = "UPDATE `consultation`
            SET `patient_regle` = '1'
            WHERE `mode_reglement` = 'tiers';";
    $this->addQuery($sql);
    
    $sql = "UPDATE `consultation`
            SET `facture_acquittee` = '1'
            WHERE `a_regler` = `secteur1` + `secteur2`
            AND `patient_regle` = '1';";
    $this->addQuery($sql);
    
    $this->makeRevision("0.85");
    $sql = "ALTER TABLE `consultation`
            ADD `sejour_id` INT(11) UNSIGNED;";
    $this->addQuery($sql);

    $this->makeRevision("0.86");
    $this->setTimeLimit(300);
    $sql = "UPDATE `consultation`
            SET `patient_regle` = '1'
            WHERE ROUND(`a_regler`,2) = ROUND(0,2);";
    $this->addQuery($sql);
     
    $sql = "UPDATE `consultation`
            SET `facture_acquittee` = '1'
            WHERE ROUND(`a_regler`,2) = ROUND(`secteur1` + `secteur2`, 2)
            AND `patient_regle` = '1'
            AND (`facture_acquittee` <> '1'
                  OR `facture_acquittee` IS NULL);";
    $this->addQuery($sql);
    
    $sql = "UPDATE `consultation`, `plageconsult`
            SET `date_paiement` = `plageconsult`.`date`  
            WHERE `patient_regle` = '1'
            AND `date_paiement` IS NULL
            AND `consultation`.`plageconsult_id` = `plageconsult`.`plageconsult_id`;";
    $this->addQuery($sql);
    
    $sql = "UPDATE `consultation`
            SET date_paiement = NULL
            WHERE date_paiement IS NOT NULL
            AND patient_regle <> '1';";
    $this->addQuery($sql);
    
    $this->makeRevision("0.87");
    $sql = "ALTER TABLE `consultation` 
            CHANGE `date_paiement` `date_reglement` DATE,
            DROP `patient_regle`;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.88");
    $sql = "ALTER TABLE `acte_ngap`
            CHANGE `consultation_id` `object_id` INT(11) UNSIGNED NOT NULL, 
            ADD `object_class` ENUM('COperation','CSejour','CConsultation') NOT NULL default 'CConsultation';";
    $this->addQuery($sql);
    
    $this->makeRevision("0.89");
    $sql = "UPDATE `consultation`
            SET `date_reglement` = NULL, `facture_acquittee` = NULL
            WHERE `a_regler` = '0'
            AND (`mode_reglement` IS NULL OR `mode_reglement` = '');";
    $this->addQuery($sql);
    
    $this->makeRevision("0.90");
    $sql = "ALTER TABLE `consultation` 
            CHANGE `facture_acquittee` `reglement_AM` ENUM('0','1');";
    $this->addQuery($sql);
    
    $sql = "UPDATE `consultation`
            SET `reglement_AM` = '1'
            WHERE ROUND(`a_regler`,2) = ROUND(`secteur1` + `secteur2`, 2)
            AND `valide` = '1';";
    $this->addQuery($sql);
    
    $this->makeRevision("0.91");
    $sql = "DELETE FROM `user_preferences` WHERE `pref_name` = 'ccam_consultation'";
    $this->addQuery($sql);
    $sql = "UPDATE `user_preferences`
            SET `pref_name` = 'ccam_consultation'
            WHERE `pref_name` = 'ccam'";
    $this->addQuery($sql);
    
    $this->makeRevision("0.92");
    $sql = "ALTER TABLE `acte_ngap` 
            ADD `demi` ENUM('0','1') DEFAULT '0';";
    $this->addQuery($sql);

    $this->makeRevision("0.93");
    $sql = "CREATE TABLE `examigs` (
					 `examigs_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT, 
					 `consultation_id` INT(11) UNSIGNED NOT NULL, 
					 `age` ENUM('0','7','12','15','16','18'), 
					 `FC` ENUM('11','2','0','4','7'), 
					 `TA` ENUM('13','5','0','2'), 
					 `temperature` ENUM('0','3'), 
					 `PAO2_FIO2` ENUM('11','9','6'), 
					 `diurese` ENUM('12','4','0'), 
					 `uree` ENUM('0','6','10'), 
					 `globules_blancs` ENUM('12','0','3'), 
					 `kaliemie` ENUM('3a','0','3b'), 
					 `natremie` ENUM('5','0','1'), 
					 `HCO3` ENUM('6','3','0'), 
					 `billirubine` ENUM('0','4','9'), 
					 `glascow` ENUM('26','13','7','5','0'), 
					 `maladies_chroniques` ENUM('9','10','17'), 
					 `admission` ENUM('0','6','8'), 
					 `scoreIGS` INT(11), 
 					  PRIMARY KEY (`examigs_id`)) TYPE=MYISAM;";
    $this->addQuery($sql);
    
    
    
    $this->makeRevision("0.94");
    set_time_limit(180);
    
    // Ajout de du_tiers
    $sql = "ALTER TABLE `consultation`
            ADD `du_tiers` FLOAT DEFAULT 0.0";
    $this->addQuery($sql);
    
    // Calcul de du_tiers
    $sql = "UPDATE `consultation`
            SET `du_tiers` = ROUND(`secteur1` + `secteur2` - `a_regler`, 2);";
    $this->addQuery($sql);
    
    // mode_reglement à NULL quand mode_reglement = tiers
    $sql = "UPDATE `consultation`
            SET `mode_reglement` = ''
            WHERE `mode_reglement` = 'tiers';";
    $this->addQuery($sql);
    
    // Modification de l'enum de mode_reglement
    $sql = "ALTER TABLE `consultation`
            CHANGE `mode_reglement` `mode_reglement` ENUM('cheque','CB','especes','virement','autre');";
    $this->addQuery($sql);
    
    // date_reglement => patient_date_reglement
    $sql = "ALTER TABLE `consultation` 
            CHANGE `date_reglement` `patient_date_reglement` DATE;";
    $this->addQuery($sql);
    
    // mode_reglement => patient_mode_reglement
    $sql = "ALTER TABLE `consultation` 
            CHANGE `mode_reglement` `patient_mode_reglement` ENUM('cheque','CB','especes','virement','autre');";
    $this->addQuery($sql);
    
    // a_regler => du_patient
    $sql = "ALTER TABLE `consultation`
            CHANGE `a_regler` `du_patient` FLOAT DEFAULT '0.0';";
    $this->addQuery($sql);
    
    // Creation d'un tiers_mode_reglement
    $sql = "ALTER TABLE `consultation`
            ADD `tiers_mode_reglement` ENUM('cheque','CB','especes','virement','autre');";
    $this->addQuery($sql);
    
    // Creation d'un tiers_date_reglement
    $sql = "ALTER TABLE `consultation`
            ADD `tiers_date_reglement` DATE;";
    $this->addQuery($sql);
     
    // On considere que toutes les anciennes consultations ont reglement_AM à 1
    $sql = "UPDATE `consultation`, `plageconsult`
            SET `reglement_AM` = '1'  
            WHERE `consultation`.`plageconsult_id` = `plageconsult`.`plageconsult_id`
            AND `plageconsult`.`date` < '2007-12-01';";
    $this->addQuery($sql);
         
    // On met à jour reglement_AM (reglement_AM à 0 si pas de du_tiers)
    $sql = "UPDATE `consultation`
            SET `reglement_AM` = '0'
            WHERE ROUND(`du_tiers`,2)  = ROUND(0,2);";
    $this->addQuery($sql);
    
    // Mise à jour des reglements AM à 1
    $sql = "UPDATE `consultation`, `plageconsult`
            SET `tiers_mode_reglement` = 'virement',
                `tiers_date_reglement` = `plageconsult`.`date`
            WHERE `consultation`.`plageconsult_id` = `plageconsult`.`plageconsult_id`
            AND `consultation`.`reglement_AM` = '1';";
    $this->addQuery($sql);
   
    // Suppression du champ reglement_AM
    $sql = "ALTER TABLE `consultation`
            DROP `reglement_AM`;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.95");
    $sql = "UPDATE consultation
            SET patient_date_reglement = NULL, patient_mode_reglement = NULL
            WHERE du_patient = 0;";
    $this->addQuery($sql);

    $this->makeRevision("0.96");
    $sql = "UPDATE consultation 
            SET valide = '0' 
            WHERE valide = '';";
    $this->addQuery($sql);
    
    $this->makeRevision("0.97");
    $sql = "ALTER TABLE `consultation`
            ADD `accident_travail` ENUM('0','1') DEFAULT '0';";
    $this->addQuery($sql);
    
    $this->makeRevision("0.98");
    $this->addPrefQuery("view_traitement", "1");
    
    $this->makeRevision("0.99");
    // Table temporaire contenant les consultation_id des accident_travail à 1
    $sql = "CREATE TEMPORARY TABLE tbl_accident_travail (
             consultation_id INT( 11 )
            ) AS 
              SELECT consultation_id
              FROM `consultation`
              WHERE accident_travail = '1';";
    $this->addQuery($sql);
    
    $sql = "ALTER TABLE `consultation`
            CHANGE `accident_travail` `accident_travail` DATE DEFAULT NULL";
    $this->addQuery($sql);
    
    $sql = "UPDATE `consultation`, `plageconsult`, `tbl_accident_travail`
            SET `consultation`.`accident_travail` = `plageconsult`.`date`
            WHERE `consultation`.`plageconsult_id` = `plageconsult`.`plageconsult_id`
            AND `consultation`.`consultation_id` = `tbl_accident_travail`.`consultation_id`;";
    $this->addQuery($sql);
    
    $sql = "UPDATE `consultation`
            SET accident_travail = NULL
            WHERE accident_travail = '0000-00-00';";
    $this->addQuery($sql);
    
    $this->makeRevision("1.00");
    $this->addPrefQuery("autoCloseConsult", "0");
    
    $this->makeRevision("1.01");
    $sql = "ALTER TABLE `acte_ngap` 
            ADD `complement` ENUM('N','F','U');";
    $this->addQuery($sql);
    
    $this->makeRevision("1.02");
    $sql = "ALTER TABLE `consultation_anesth`
            ADD `sejour_id` INT(11) UNSIGNED AFTER `operation_id`";
    $this->addQuery($sql);
    
    $this->makeRevision("1.03");
    $sql = "ALTER TABLE `consultation_anesth` 
      ADD `examenCardio` TEXT NULL AFTER `etatBucco` ,
      ADD `examenPulmo` TEXT NULL AFTER `examenCardio` ;";
    $this->addQuery($sql);
    
    $this->makeRevision("1.04");
    $sql = "CREATE TABLE `reglement` (
      `reglement_id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
      `consultation_id` INT( 11 ) UNSIGNED NOT NULL ,
      `banque_id` INT( 11 ) UNSIGNED ,
      `date` DATETIME NOT NULL ,
      `montant` FLOAT NOT NULL ,
      `emetteur` ENUM( 'patient', 'tiers' ) ,
      `mode` ENUM( 'cheque', 'CB', 'especes', 'virement', 'autre' ) ,
      PRIMARY KEY ( `reglement_id` )
      ) TYPE = MYISAM ;";
    $this->addQuery($sql);
    
    // On crée les règlements des patients
    $sql = "INSERT INTO `reglement` (
      `emetteur`,
      `consultation_id`, 
      `banque_id`, 
      `date`, 
      `montant`,
      `mode`)
      
      SELECT 
        'patient',
        `consultation_id`, 
        `banque_id`, 
        `patient_date_reglement`, 
        `du_patient`, 
        `patient_mode_reglement`
      FROM 
        `consultation`
      WHERE 
        `patient_date_reglement` IS NOT NULL;";
    $this->addQuery($sql);
    
    
    // On crée les règlements des tiers
    $sql = "INSERT INTO `reglement` (
      `emetteur`,
      `consultation_id`, 
      `date`, 
      `montant`,
      `mode`)
      
      SELECT 
        'tiers',
        `consultation_id`, 
        `tiers_date_reglement`, 
        `du_tiers`, 
        `tiers_mode_reglement`
      FROM 
        `consultation`
      WHERE 
        `tiers_date_reglement` IS NOT NULL;";
    $this->addQuery($sql);
    
    // On supprime les champs inutiles des consultations
    /*$sql = 'ALTER TABLE `consultation` 
      DROP `tiers_mode_reglement`,
      DROP `patient_mode_reglement`,
      DROP `banque_id`;';
    $this->addQuery($sql);*/
    
    $this->makeRevision("1.05");
    $sql = "ALTER TABLE `acte_ngap` 
      ADD `executant_id` int(11) unsigned NOT NULL DEFAULT '0',
      ADD INDEX (`executant_id`)";
    $this->addQuery($sql);
    
    // COperation : executant_id = operations -> chir_id
    // CSejour : executant_id = sejour -> praticien_id
    // CConsultation : executant_id = consultation -> plageconsult -> chir_id
    $sql = "UPDATE `acte_ngap` 
       SET `acte_ngap`.`executant_id` = 
        (SELECT `chir_id` 
         FROM `operations` 
         WHERE `operations`.`operation_id` = `acte_ngap`.`object_id`
         LIMIT 1)
       WHERE 
        `acte_ngap`.`object_class` = 'COperation' AND 
        `acte_ngap`.`executant_id` = 0";
    $this->addQuery($sql);
    
    $sql = "UPDATE `acte_ngap` 
       SET `acte_ngap`.`executant_id` = 
        (SELECT `praticien_id` 
         FROM `sejour` 
         WHERE `sejour`.`sejour_id` = `acte_ngap`.`object_id`
         LIMIT 1)
       WHERE 
        `acte_ngap`.`object_class` = 'CSejour' AND 
        `acte_ngap`.`executant_id` = 0";
    $this->addQuery($sql);
    
    $sql = "UPDATE `acte_ngap` 
       SET `acte_ngap`.`executant_id` = 
        (SELECT `plageconsult`.`chir_id` 
         FROM `plageconsult`, `consultation`
         WHERE 
           `consultation`.`consultation_id` = `acte_ngap`.`object_id` AND
           `plageconsult`.`plageconsult_id` = `consultation`.`plageconsult_id`
         LIMIT 1)
       WHERE 
        `acte_ngap`.`object_class` = 'CConsultation' AND 
        `acte_ngap`.`executant_id` = 0";
    $this->addQuery($sql);
    
    $this->makeRevision("1.07");
    $this->addPrefQuery("resumeCompta", "1");
    
    $this->makeRevision("1.08");
    $this->addPrefQuery("VitaleVisionDir", "");
    $this->addPrefQuery("VitaleVision", "0");
    
    $this->makeRevision("1.09");
    $sql = "UPDATE `consultation` 
			SET du_tiers = ROUND(secteur1 + secteur2 - du_patient, 2)
			WHERE ROUND(secteur1 + secteur2 - du_tiers - du_patient, 2) != 0
			AND ABS(ROUND(secteur1 + secteur2 - du_tiers - du_patient, 2)) > 1
			AND valide = '1'";
    $this->addQuery($sql);
    
    $this->makeRevision("1.10");
    $this->addPrefQuery("showDatesAntecedents", "1");
    
    $this->makeRevision("1.11");
    $sql = "ALTER TABLE `consultation_anesth` 
              ADD `chir_id` INT (11) UNSIGNED AFTER `sejour_id`,
              ADD `date_interv` DATE AFTER `chir_id`,
              ADD `libelle_interv` VARCHAR (255) AFTER `date_interv`;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `consultation_anesth` 
              ADD INDEX (`sejour_id`),
              ADD INDEX (`chir_id`),
              ADD INDEX (`date_interv`);";
    $this->addQuery($sql);
        
    $this->mod_version = "1.12";
  }
}
?>