<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI;
require_once($AppUI->getModuleClass("dPcompteRendu", "compteRendu"));

// MODULE CONFIGURATION DEFINITION
$config = array();
$config["mod_name"]        = "dPcabinet";
$config["mod_version"]     = "0.39";
$config["mod_directory"]   = "dPcabinet";
$config["mod_setup_class"] = "CSetupdPcabinet";
$config["mod_type"]        = "user";
$config["mod_ui_name"]     = "Cabinet";
$config["mod_ui_icon"]     = "dPcabinet.png";
$config["mod_description"] = "Gestion de cabinet de consultation";
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
    db_exec("DROP TABLE files_mediboard;");       db_error();
    db_exec("DROP TABLE files_index_mediboard;"); db_error();
    db_exec("DROP TABLE tarifs;");                db_error();
    db_exec("DROP TABLE examaudio;");             db_error();
    return null;
  }

  function upgrade( $old_version ) {
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
        $sql = "CREATE TABLE files_mediboard (
                    file_id int(11) NOT NULL auto_increment,
                    file_real_filename varchar(255) NOT NULL default '',
                    file_consultation bigint(20) NOT NULL default '0',
                    file_operation bigint(20) NOT NULL default '0',
                    file_name varchar(255) NOT NULL default '',
                    file_parent int(11) default '0',
                    file_description text,
                    file_type varchar(100) default NULL,
                    file_owner int(11) default '0',
                    file_date datetime default NULL,
                    file_size int(11) default '0',
                    file_version float NOT NULL default '0',
                    file_icon varchar(20) default 'obj/',
                    PRIMARY KEY  (file_id),
                    KEY idx_file_consultation (file_consultation),
                    KEY idx_file_operation (file_operation),
                    KEY idx_file_parent (file_parent)
                  ) TYPE=MyISAM;";
        db_exec( $sql ); db_error();
            $sql = "CREATE TABLE files_index_mediboard (
                    file_id int(11) NOT NULL default '0',
                    word varchar(50) NOT NULL default '',
                    word_placement int(11) default '0',
                    PRIMARY KEY  (file_id,word),
                    KEY idx_fwrd (word),
                    KEY idx_wcnt (word_placement)
                    ) TYPE=MyISAM;";
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
        $sql = "ALTER TABLE `files_mediboard`" .
          "\nDROP `file_parent`," .
          "\nDROP `file_description`," .
          "\nDROP `file_version`," .
          "\nDROP `file_icon`;";
        db_exec( $sql ); db_error();
            
      case "0.38":
        $sql = "ALTER TABLE `files_mediboard`" .
            "\nADD `file_object_id` INT(11) NOT NULL DEFAULT '0' AFTER `file_real_filename`," .
            "\nADD `file_class` VARCHAR(30) NOT NULL DEFAULT 'CPatients' AFTER `file_object_id`;";
        db_exec( $sql ); db_error();
        $sql = "UPDATE `files_mediboard`" .
            "SET `file_object_id` = `file_consultation`," .
            "\n`file_class` = 'CConsultation'" .
            "\nWHERE `file_consultation` != 0;";
        db_exec( $sql ); db_error();
        $sql = "UPDATE `files_mediboard`" .
            "SET `file_object_id` = `file_consultation_anesth`," .
            "\n`file_class` = 'CConsultAnesth'" .
            "\nWHERE `file_consultation_anesth` != 0;";
        db_exec( $sql ); db_error();
        $sql = "UPDATE `files_mediboard`" .
            "SET `file_object_id` = `file_operation`," .
            "\n`file_class` = 'COperation'" .
            "\nWHERE `file_operation` != 0;";
        db_exec( $sql ); db_error();
        $sql = "ALTER TABLE `files_mediboard`" .
          "\nDROP `file_consultation`," .
          "\nDROP `file_consultation_anesth`," .
          "\nDROP `file_operation`;";
        db_exec( $sql ); db_error();
      case "0.39":
        return "0.39";
    }
    return false;
  }
}

?>