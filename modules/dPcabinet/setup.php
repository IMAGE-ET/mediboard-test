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
$config["mod_version"]     = "0.51";
$config["mod_type"]        = "user";
$config["mod_config"]      = true;

if (@$a == "setup") {
  echo dPshowModuleConfig($config);
}

class CSetupdPcabinet {

  function configure() {
    global $AppUI;
    $AppUI->redirect("m=dPcabinet&a=configure");
    return true;
  }

  function remove() {
    db_exec("DROP TABLE consultation;");          db_error();
    db_exec("DROP TABLE consultation_anesth;");   db_error();
    db_exec("DROP TABLE plageconsult;");          db_error();
    db_exec("DROP TABLE tarifs;");                db_error();
    db_exec("DROP TABLE examaudio;");             db_error();
    db_exec("DROP TABLE techniques_anesth;");     db_error();
    db_exec("DROP TABLE exams_comp;");            db_error();
    return null;
  }

  function upgrade( $old_version ) {
    global $utypes;
    switch ($old_version) {
      case "all":
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
        db_exec( $sql ); db_error();
        $sql = "CREATE TABLE plageconsult (
                    plageconsult_id bigint(20) NOT NULL auto_increment,
                    chir_id bigint(20) NOT NULL default '0',
                    date date NOT NULL default '0000-00-00',
                    debut time NOT NULL default '00:00:00',
                    fin time NOT NULL default '00:00:00',
                    PRIMARY KEY  (plageconsult_id),
                    KEY chir_id (chir_id)
                    ) TYPE=MyISAM COMMENT='Table des plages de consultation des médecins';";
        db_exec( $sql ); db_error();
      case "0.1":
        $sql = "ALTER TABLE plageconsult ADD freq TIME DEFAULT '00:15:00' NOT NULL AFTER date ;";
        db_exec( $sql ); db_error();

      case "0.2":
        $sql = "ALTER TABLE consultation ADD compte_rendu TEXT DEFAULT NULL";
        db_exec( $sql ); db_error();

      case "0.21":
        $sql = "ALTER TABLE consultation CHANGE duree duree TINYINT DEFAULT '1' NOT NULL ";
        db_exec( $sql ); db_error();
        $sql = "UPDATE consultation SET duree='1' ";
        db_exec( $sql ); db_error();

      case "0.22":
        $sql = "ALTER TABLE `consultation` " .
            "\nADD `chrono` TINYINT DEFAULT '16' NOT NULL," .
            "\nADD `annule` TINYINT DEFAULT '0' NOT NULL," .
            "\nADD `paye` TINYINT DEFAULT '0' NOT NULL," .
            "\nADD `cr_valide` TINYINT DEFAULT '0' NOT NULL," .
            "\nADD `examen` TEXT," .
            "\nADD `traitement` TEXT";
        db_exec( $sql ); db_error();

      case "0.23":
        $sql = "ALTER TABLE `consultation` ADD `premiere` TINYINT NOT NULL";
        db_exec( $sql ); db_error();

      case "0.24":
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
        db_exec( $sql ); db_error();
        $sql = "ALTER TABLE `consultation` ADD `tarif` TINYINT,
                ADD `type_tarif` ENUM( 'cheque', 'CB', 'especes', 'tiers', 'autre' ) ;";
        db_exec( $sql ); db_error();

      case "0.25":
        $sql = "ALTER TABLE `tarifs` CHANGE `valeur` `secteur1` FLOAT( 6 ) DEFAULT NULL;";
        db_exec( $sql ); db_error();
        $sql = "ALTER TABLE `tarifs` ADD `secteur2` FLOAT( 6 ) NOT NULL;";
        db_exec( $sql ); db_error();
        $sql = "ALTER TABLE `consultation` CHANGE `secteur1` `secteur1` FLOAT( 6 ) DEFAULT '0' NOT NULL;";
        db_exec( $sql ); db_error();
        $sql = "ALTER TABLE `consultation` CHANGE `secteur2` `secteur2` FLOAT( 6 ) DEFAULT '0' NOT NULL;";
        db_exec( $sql ); db_error();
        $sql = "ALTER TABLE `consultation` CHANGE `tarif` `tarif` VARCHAR( 50 ) DEFAULT NULL;";
        db_exec( $sql ); db_error();
        $sql = "ALTER TABLE `plageconsult` ADD `libelle` VARCHAR( 50 ) DEFAULT NULL AFTER `chir_id` ;";
        db_exec( $sql ); db_error();

      case "0.26":
        $sql = "ALTER TABLE `consultation` " .
            "\nADD `ordonnance` TEXT DEFAULT NULL," .
            "\nADD `or_valide` TINYINT DEFAULT '0' NOT NULL"; 
        db_exec( $sql ); db_error();
        
      case "0.27":
        $sql = "ALTER TABLE `consultation` " .
            "\nADD `courrier1` TEXT DEFAULT NULL," .
            "\nADD `c1_valide` TINYINT DEFAULT '0' NOT NULL"; 
        db_exec( $sql ); db_error();
        $sql = "ALTER TABLE `consultation` " .
            "\nADD `courrier2` TEXT DEFAULT NULL," .
            "\nADD `c2_valide` TINYINT DEFAULT '0' NOT NULL"; 
        db_exec( $sql ); db_error();
        
      case "0.28":
        $sql = "ALTER TABLE `consultation`" .
            "\nADD `date_paiement` DATE AFTER `paye` ;";
        db_exec( $sql ); db_error();

        $sql = "UPDATE consultation, plageconsult
          SET consultation.date_paiement = plageconsult.date
          WHERE consultation.plageconsult_id = plageconsult.plageconsult_id
          AND consultation.paye = 1";
        db_exec( $sql ); db_error();

      case "0.29":
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
        db_exec( $sql ); db_error();
        
      case "0.30":
        //@todo : IMPORTANT : passer tout en sql
        $document_types = array (
          array ("name" => "compte_rendu", "valide" => "cr_valide"),
          array ("name" => "ordonnance", "valide" => "or_valide"),
          array ("name" => "courrier1", "valide" => "c1_valide"),
          array ("name" => "courrier2", "valide" => "c2_valide"));
          
        set_time_limit( 1800 );

        foreach ($document_types as $document_type) {
          $document_name = $document_type["name"];
          $document_valide = $document_type["valide"];

          $sql = "SELECT *" .
            "\nFROM `consultation`" .
            "\nWHERE `$document_name` IS NOT NULL" .
            "\nAND `$document_name` != ''";
          $res = db_exec( $sql );
  
          while ($obj = db_fetch_object($res)) {
            $document = new CCompteRendu;
            $document->type = "consultation";
            $document->nom = $document_name;
            $document->object_id = $obj->consultation_id;
            $document->source = $obj->$document_name;
            $document->valide = $obj->$document_valide;
            $document->store();
          }
        }

      case "0.31":
        $sql = "CREATE TABLE `examaudio` (" .
          "\n`examaudio_id` INT NOT NULL AUTO_INCREMENT ," .
          "\n`consultation_id` INT NOT NULL ," .
          "\n`gauche_aerien` VARCHAR( 64 ) ," .
          "\n`gauche_osseux` VARCHAR( 64 ) ," .
          "\n`droite_aerien` VARCHAR( 64 ) ," .
          "\n`droite_osseux` VARCHAR( 64 ) ," .
          "\nPRIMARY KEY ( `examaudio_id` ) ," .
          "\nINDEX ( `consultation_id` )) TYPE=MyISAM";
          
        db_exec( $sql ); db_error();
      case "0.32":
        $sql = "ALTER TABLE `examaudio` " .
            "\nADD UNIQUE (`consultation_id`)";

      case "0.33":
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
        db_exec( $sql ); db_error();

      case "0.34":
        $sql = "ALTER TABLE `consultation_anesth`
          CHANGE `groupe` `groupe` ENUM( '?', '0', 'A', 'B', 'AB' ) DEFAULT '?' NOT NULL ,
          CHANGE `rhesus` `rhesus` ENUM( '?', '+', '-' ) DEFAULT '?' NOT NULL ,
          CHANGE `tabac` `tabac` ENUM( '?', '-', '+', '++' ) DEFAULT '?' NOT NULL ,
          CHANGE `oenolisme` `oenolisme` ENUM( '?', '-', '+', '++' ) DEFAULT '?' NOT NULL ,
          CHANGE `transfusions` `transfusions` ENUM( '?', '-', '+' ) DEFAULT '?' NOT NULL ,
          CHANGE `intubation` `intubation` ENUM( '?', 'dents', 'bouche', 'cou' ) DEFAULT '?' NOT NULL ,
          CHANGE `biologie` `biologie` ENUM( '?', 'NF', 'COAG', 'IONO' ) DEFAULT '?' NOT NULL ,
          CHANGE `commande_sang` `commande_sang` ENUM( '?', 'clinique', 'CTS', 'autologue' ) DEFAULT '?' NOT NULL ;";
        db_exec( $sql ); db_error();

        $sql = "ALTER TABLE `consultation_anesth`
          CHANGE `tasys` `tasys` INT( 5 ) DEFAULT NULL ,
          CHANGE `tadias` `tadias` INT( 5 ) DEFAULT NULL;";
        db_exec( $sql ); db_error();
      
      case "0.35":
        $sql = "ALTER TABLE `consultation` ADD `arrivee` DATETIME AFTER `type_tarif` ;";
        db_exec( $sql ); db_error();

      case "0.36":
        $sql = "ALTER TABLE `consultation_anesth`" .
          "CHANGE `groupe` `groupe` ENUM( '?', 'O', 'A', 'B', 'AB' )" .
          "DEFAULT '?' NOT NULL ;";
        db_exec( $sql ); db_error();

      case "0.37":
      case "0.38":

      case "0.39":
        $sql = "ALTER TABLE `consultation_anesth`
        	    ADD `mallampati` ENUM( 'classe1', 'classe2', 'classe3', 'classe4' ),
        	    ADD `bouche` ENUM( 'm20', 'm35', 'p35' ),
        	    ADD `distThyro` ENUM( 'm65', 'p65' ),
        	    ADD `etatBucco` VARCHAR(50),
        	    ADD `conclusion` VARCHAR(50),
        	    ADD `position` ENUM( 'DD', 'DV', 'DL', 'GP', 'AS', 'TO' );";
        db_exec( $sql ); db_error();
      
      case "0.40":
      case "0.41":
        
      case "0.42":
        set_time_limit(1800);
        $sql = "ALTER TABLE `consultation` DROP INDEX `plageconsult_id`  ;";
         db_exec($sql); db_error();
        $sql = "ALTER TABLE `consultation` ADD INDEX ( `plageconsult_id` ) ;";
         db_exec($sql); db_error();
        $sql = "ALTER TABLE `consultation` ADD INDEX ( `patient_id` ) ;";
         db_exec($sql); db_error();
        $sql = "ALTER TABLE `tarifs` DROP INDEX `chir_id` ;";
         db_exec($sql); db_error();
        $sql = "ALTER TABLE `tarifs` ADD INDEX ( `chir_id` ) ;";
         db_exec($sql); db_error();
        $sql = "ALTER TABLE `tarifs` ADD INDEX ( `function_id` ) ;";
         db_exec($sql); db_error();
      case "0.43":
        $sql = "ALTER TABLE `consultation_anesth`" .
          "CHANGE `position` `position` ENUM( 'DD', 'DV', 'DL', 'GP', 'AS', 'TO', 'GYN');";
        db_exec($sql); db_error();  
        $sql = "CREATE TABLE `techniques_anesth` (
               `technique_id` INT NOT NULL AUTO_INCREMENT ,
               `consultAnesth_id` INT NOT NULL ,
               `technique` TEXT NOT NULL ,
               PRIMARY KEY ( `technique_id` )) TYPE=MyISAM";
        db_exec( $sql ); db_error();
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
        		ADD `spo2` float default NULL
        		;";
        db_exec( $sql ); db_error(); 
        $sql = "ALTER TABLE `consultation_anesth` CHANGE `operation_id` `operation_id` BIGINT( 20 ) NULL DEFAULT NULL;";
        db_exec( $sql ); db_error();         
        $sql = "ALTER TABLE `consultation_anesth` " .
        		"\nCHANGE `etatBucco` `etatBucco` TEXT DEFAULT NULL ," .
        		"\nCHANGE `conclusion` `conclusion` TEXT DEFAULT NULL ";
        db_exec( $sql ); db_error(); 
        $sql = "ALTER TABLE `consultation_anesth` " .
        		"\nCHANGE `tabac` `tabac` TEXT DEFAULT NULL ," .
        		"\nCHANGE `oenolisme` `oenolisme` TEXT DEFAULT NULL ";
        db_exec( $sql ); db_error(); 
        $sql = "CREATE TABLE `exams_comp` (
               `exam_id` INT NOT NULL AUTO_INCREMENT ,
               `consult_id` INT NOT NULL ,
               `examen` TEXT NOT NULL ,
               `fait` tinyint(1) NOT NULL default 0,
               PRIMARY KEY ( `exam_id` )) TYPE=MyISAM";
        db_exec( $sql ); db_error();
      case "0.44":
        $module = @CModule::getInstalled("mediusers");
        if (!$module) {
          return "0.44";
        }
       
        $utypes_flip = array_flip($utypes);
        $id_anesth = $utypes_flip["Anesthésiste"];
        $sql = "SELECT users.user_id" .
               "\nFROM users, users_mediboard" .
               "\nWHERE users.user_id = users_mediboard.user_id" .
               "\nAND users.user_type='$id_anesth'";
        $result = db_loadList($sql);
        $listAnesthid = array();
        foreach($result as $keyresult => $resultAnesth){
          $listAnesthid[$keyresult] = $result[$keyresult]["user_id"];
        } 
         
        $sql = "SELECT consultation.consultation_id FROM consultation" .
               "\nLEFT JOIN consultation_anesth ON consultation.consultation_id = consultation_anesth.consultation_id" .
               "\nLEFT JOIN plageconsult ON consultation.plageconsult_id = plageconsult.plageconsult_id" .
               "\nWHERE plageconsult.chir_id " . db_prepare_in($listAnesthid) .
               "\nAND consultation_anesth.consultation_anesth_id IS NULL" ;  
        $result = db_loadList($sql);

        foreach($result as $keyresult => $resultAnesth){
          $consultAnesth = new CConsultAnesth;
          $consultAnesth->consultation_anesth_id = 0;
          $consultAnesth->consultation_id = $result[$keyresult]["consultation_id"];
          $consultAnesth->store();
        }
      case "0.45": 
        $sql = "ALTER TABLE `exams_comp` " .
            "\nCHANGE `consult_id` `consultation_id` INT NOT NULL ;";
        db_exec( $sql ); db_error();
        $sql = "ALTER TABLE `techniques_anesth` " .
            "\nCHANGE `consultAnesth_id` `consultation_anesth_id` INT NOT NULL ;";
        db_exec( $sql ); db_error();
        
      case "0.46":
        $sql = "ALTER TABLE `consultation_anesth` " .
            "\nCHANGE `tca` `tca` TINYINT(2) NULL ;";
        db_exec( $sql ); db_error();
        $sql = "ALTER TABLE `consultation_anesth` " .
            "\nADD `tca_temoin` TINYINT(2) NULL AFTER `tca`," .
            "\nADD `ht_final` FLOAT DEFAULT NULL AFTER `ht`;" ;
        db_exec( $sql ); db_error();
        $sql = "ALTER TABLE `consultation_anesth` DROP `transfusions`";
        db_exec( $sql ); db_error();
      case "0.47":
        $sql = "ALTER TABLE `consultation_anesth` CHANGE `rhesus` `rhesus` ENUM( '?', '+', '-', 'POS', 'NEG') DEFAULT '?' NOT NULL ;";
        db_exec( $sql ); db_error();
        $sql = "UPDATE `consultation_anesth` SET `rhesus`='POS' WHERE `rhesus`='+';";
        db_exec( $sql ); db_error();
        $sql = "UPDATE `consultation_anesth` SET `rhesus`='NEG' WHERE `rhesus`='-';";
        db_exec( $sql ); db_error();
        $sql = "ALTER TABLE `consultation_anesth` CHANGE `rhesus` `rhesus` ENUM( '?', 'POS', 'NEG') DEFAULT '?' NOT NULL ;";
        db_exec( $sql ); db_error();
        $sql = "ALTER TABLE `consultation_anesth` CHANGE `rai` `rai` ENUM( '?', 'POS', 'NEG') DEFAULT '?' NOT NULL ;";
        db_exec( $sql ); db_error();
        $sql = "ALTER TABLE `consultation_anesth` DROP `ecbu_detail`";
        db_exec( $sql ); db_error();
        $sql = "ALTER TABLE `consultation_anesth` ".
               "\nADD `premedication` TEXT," .
               "\nADD `prepa_preop` TEXT;" ;
        db_exec( $sql ); db_error();
      case "0.48":
        set_time_limit(1800);
        
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
        db_exec( $sql ); db_error();
        
        $sql = "ALTER TABLE `consultation_anesth` " .
               "\nDROP `listCim10`;";
        db_exec( $sql ); db_error();
        
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
        db_exec( $sql ); db_error();
        
        $sql = "ALTER TABLE `consultation` " .
               "\nDROP `compte_rendu`," .
               "\nDROP `cr_valide`," .
               "\nDROP `ordonnance`," .
               "\nDROP `or_valide`," .
               "\nDROP `courrier1`," .
               "\nDROP `c1_valide`," .
               "\nDROP `courrier2`," .
               "\nDROP `c2_valide`;";
        db_exec( $sql ); db_error();
        
        $sql = "ALTER TABLE `examaudio` " .
               "\nCHANGE `examaudio_id` `examaudio_id` int(11) unsigned NOT NULL AUTO_INCREMENT," .
               "\nCHANGE `consultation_id` `consultation_id` int(11) unsigned NOT NULL DEFAULT '0';";
        db_exec( $sql ); db_error();
        
        $sql = "ALTER TABLE `exams_comp` " .
               "\nCHANGE `exam_id` `exam_id` int(11) unsigned NOT NULL AUTO_INCREMENT," .
               "\nCHANGE `consultation_id` `consultation_id` int(11) unsigned NOT NULL DEFAULT '0'," .
               "\nCHANGE `fait` `fait` tinyint(4) NOT NULL DEFAULT '0';";
        db_exec( $sql ); db_error();
        
        $sql = "ALTER TABLE `plageconsult` " .
               "\nCHANGE `plageconsult_id` `plageconsult_id` int(11) unsigned NOT NULL AUTO_INCREMENT," .
               "\nCHANGE `chir_id` `chir_id` int(11) unsigned NOT NULL DEFAULT '0'," .
               "\nCHANGE `libelle` `libelle` varchar(255) NULL;";
        db_exec( $sql ); db_error();
        
        $sql = "ALTER TABLE `tarifs` " .
               "\nCHANGE `tarif_id` `tarif_id` int(11) unsigned NOT NULL AUTO_INCREMENT," .
               "\nCHANGE `chir_id` `chir_id` int(11) unsigned NOT NULL DEFAULT '0'," .
               "\nCHANGE `function_id` `function_id` int(11) unsigned NOT NULL DEFAULT '0'," .
               "\nCHANGE `description` `description` varchar(255) NOT NULL," .
               "\nCHANGE `secteur1` `secteur1` float NOT NULL;";
        db_exec( $sql ); db_error();
        
        $sql = "ALTER TABLE `techniques_anesth` " .
               "\nCHANGE `technique_id` `technique_id` int(11) unsigned NOT NULL AUTO_INCREMENT," .
               "\nCHANGE `consultation_anesth_id` `consultation_anesth_id` int(11) unsigned NOT NULL DEFAULT '0';";
        db_exec( $sql ); db_error();
      case "0.49":
        $sql = "ALTER TABLE `consultation_anesth` " .
               "\nCHANGE `tasys` `tasys` TINYINT(4) NULL," .
               "\nCHANGE `tadias` `tadias` TINYINT(4) NULL;";
        db_exec( $sql ); db_error();
      
      case "0.50":
        $sql = "ALTER TABLE `consultation` " .
               "\nCHANGE `patient_id` `patient_id` int(11) unsigned NULL;";
        db_exec( $sql ); db_error();
      
      case "0.51":
        return "0.51";
    }
    return false;
  }
}

?>