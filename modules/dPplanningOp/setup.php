<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPplanningOp
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI;
require_once($AppUI->getModuleClass("dPcompteRendu", "compteRendu"));

// MODULE CONFIGURATION DEFINITION
$config = array();
$config["mod_name"]        = "dPplanningOp";
$config["mod_version"]     = "0.53";
$config["mod_directory"]   = "dPplanningOp";
$config["mod_setup_class"] = "CSetupdPplanningOp";
$config["mod_type"]        = "user";
$config["mod_ui_name"]     = "Planning Chir.";
$config["mod_ui_icon"]     = "dPplanningOp.png";
$config["mod_description"] = "Gestion des plannings opératoires des chirurgiens";
$config["mod_config"]      = true;

if (@$a == "setup") {
  echo dPshowModuleConfig($config);
}

class CSetupdPplanningOp {

  function configure() {
    global $AppUI;
    $AppUI->redirect("m=dPplanningOp&a=configure");
    return true;
  }

  function remove() {
    db_exec("DROP TABLE operations;");
    db_exec("DROP TABLE sejour;");
    db_exec("DROP TABLE protocole;");
    db_exec("DELETE FROM sysval WHERE  sysval_title = 'AnesthType';");
    return null;
  }

  function upgrade( $old_version ) {
    switch ($old_version) {
      case "all":
        $sql = "CREATE TABLE operations ( " .
          "  operation_id bigint(20) unsigned NOT NULL auto_increment" .
          ", pat_id bigint(20) unsigned NOT NULL default '0'" .
          ", chir_id bigint(20) unsigned NOT NULL default '0'" .
          ", plageop_id bigint(20) unsigned NOT NULL default '0'" .
          ", CIM10_code varchar(5) default NULL" .
          ", CCAM_code varchar(7) default NULL" .
          ", cote enum('droit','gauche','bilatéral','total') NOT NULL default 'total'" .
          ", temp_operation time NOT NULL default '00:00:00'" .
          ", time_operation time NOT NULL default '00:00:00'" .
          ", examen text" .
          ", materiel text" .
          ", commande_mat enum('o', 'n') NOT NULL default 'n'" .
          ", info enum('o','n') NOT NULL default 'n'" .
          ", date_anesth date NOT NULL default '0000-00-00'" .
          ", time_anesth time NOT NULL default '00:00:00'" .
          ", type_anesth tinyint(4) default NULL" .
          ", date_adm date NOT NULL default '0000-00-00'" .
          ", time_adm time NOT NULL default '00:00:00'" .
          ", duree_hospi tinyint(4) unsigned NOT NULL default '0'" .
          ", type_adm enum('comp','ambu','exte') default 'comp'" .
          ", chambre enum('o','n') NOT NULL default 'o'" .
          ", ATNC enum('o','n') NOT NULL default 'n'" .
          ", rques text" .
          ", rank tinyint(4) NOT NULL default '0'" .
          ", admis enum('n','o') NOT NULL default 'n'" .
          ", PRIMARY KEY  (operation_id)" .
          ", UNIQUE KEY operation_id (operation_id)" .
          ") TYPE=MyISAM;";
        db_exec( $sql ); db_error();

        $sql = "INSERT INTO sysvals" .
          "\nVALUES ('', '1', 'AnesthType', '1|Rachi\n2|Rachi + bloc\n3|Anesthésie loco-régionnale\n4|Anesthésie locale\n5|Neurolept\n6|Anesthésie générale\n7|Anesthesie generale + bloc\n8|Anesthesie peribulbaire\n0|Non définie')";
        db_exec( $sql ); db_error();
      
      case "0.1":
        $sql = "ALTER TABLE operations " .
          "\nADD entree_bloc TIME AFTER temp_operation ," .
          "\nADD sortie_bloc TIME AFTER entree_bloc ," .
          "\nADD saisie ENUM( 'n', 'o' ) DEFAULT 'n' NOT NULL ," .
          "\nCHANGE plageop_id plageop_id BIGINT( 20 ) UNSIGNED";
        db_exec( $sql ); db_error();
          
      case "0.2":
        $sql = "ALTER TABLE `operations` " .
          "\nADD `convalescence` TEXT AFTER `materiel` ;";
        db_exec( $sql ); db_error();
      
      case "0.21":
        $sql = "ALTER TABLE `operations` " .
          "\nADD `depassement` INT( 4 );";
        db_exec( $sql ); db_error();
    
      case "0.22":
        $sql = "ALTER TABLE `operations` " .
          "\nADD `CCAM_code2` VARCHAR( 7 ) AFTER `CCAM_code`," .
          "\nADD INDEX ( `CCAM_code2` )," .
          "\nADD INDEX ( `CCAM_code` )," .
          "\nADD INDEX ( `pat_id` )," .
          "\nADD INDEX ( `chir_id` )," .
          "\nADD INDEX ( `plageop_id` );";
        db_exec( $sql ); db_error();
    
      case "0.23" :
        $sql = "ALTER TABLE `operations` " .
         "\nADD `modifiee` TINYINT DEFAULT '0' NOT NULL AFTER `saisie`," .
         "\nADD `annulee` TINYINT DEFAULT '0' NOT NULL ;";
        db_exec( $sql ); db_error();
    
      case "0.24" :
        $sql = "ALTER TABLE `operations` " .
          "\nADD `compte_rendu` TEXT," .
          "\nADD `cr_valide` TINYINT( 4 ) DEFAULT '0' NOT NULL ;";
        db_exec( $sql ); db_error();
    
      case "0.25" :
        $sql = "ALTER TABLE `operations` " .
          "\nADD `pathologie` VARCHAR( 8 ) DEFAULT NULL," .
          "\nADD `septique` TINYINT DEFAULT '0' NOT NULL ;";
              db_exec($sql); db_error();
     
            case "0.26":
        $sql = "ALTER TABLE `operations` " .
          "\nADD `libelle` TEXT DEFAULT NULL AFTER `CCAM_code2` ;";
        db_exec($sql); db_error();
    
      case "0.27":
        $document_types = array (
          array ("name" => "compte_rendu", "valide" => "cr_valide"));
          
        set_time_limit( 1800 );
        //@todo : IMPORTANT passer tout en SQL
        foreach ($document_types as $document_type) {
          $document_name = $document_type["name"];
          $document_valide = $document_type["valide"];
    
          $sql = "SELECT *" .
              "\nFROM `operations`" .
              "\nWHERE `$document_name` IS NOT NULL" .
              "\nAND `$document_name` != ''";
          $res = db_exec( $sql );
    
          while ($obj = db_fetch_object($res)) {
            $document = new CCompteRendu;
            $document->type = "operation";
            $document->nom = $document_name;
            $document->object_id = $obj->operation_id;
            $document->source = $obj->$document_name;
            $document->valide = $obj->$document_valide;
            $document->store();
          }
        }
        
      case "0.28":
        $sql = "ALTER TABLE `operations` " .
          "\nADD `codes_ccam` VARCHAR( 160 ) AFTER `CIM10_code`";
        db_exec($sql); db_error();
    
        $sql = "ALTER TABLE `operations` " .
          "\nADD INDEX ( `codes_ccam` )";
        db_exec($sql); db_error();
    
      case "0.29":
        $sql = "SELECT `operation_id` , `CCAM_code` , `CCAM_code2`" .
          "\nFROM `operations`";
        
        $res = db_exec( $sql );
    
        while ($obj = db_fetch_object($res)) {
          $obj->codes_ccam = $obj->CCAM_code;
          if ($obj->CCAM_code2) {
            $obj->codes_ccam .= "|$obj->CCAM_code2";
          }
            
          $sql2 = "UPDATE `operations` " .
            "\nSET `codes_ccam` = '$obj->codes_ccam' " .
            "\nWHERE `operation_id` = $obj->operation_id";
          db_exec($sql2); db_error();
        };
        
      case "0.30":
        $sql = "ALTER TABLE `operations`
          ADD `pose_garrot` TIME AFTER `entree_bloc` ,
          ADD `debut_op` TIME AFTER `pose_garrot` ,
          ADD `fin_op` TIME AFTER `debut_op` ,
          ADD `retrait_garrot` TIME AFTER `fin_op` ;";
        db_exec($sql); db_error();
      
      case "0.31":
        $sql = "ALTER TABLE `operations`" .
          "\nADD `salle_id` BIGINT AFTER `plageop_id` ," .
          "\nADD `date` DATE AFTER `salle_id` ;";
        db_exec($sql); db_error();
      
      case "0.32":
        $sql = "ALTER TABLE `operations`" .
          "\nADD `venue_SHS` VARCHAR( 8 ) AFTER `chambre`;";
        db_exec( $sql ); db_error();
        $sql = "ALTER TABLE `operations` ADD INDEX ( `venue_SHS` );";
        db_exec( $sql ); db_error();
      
      case "0.33":
        $sql = "ALTER TABLE `operations`" .
          "\nADD `code_uf` VARCHAR( 3 ) AFTER `venue_SHS`;";
        db_exec( $sql ); db_error();
        $sql = "ALTER TABLE `operations`" .
          "\nADD `libelle_uf` VARCHAR( 40 ) AFTER `code_uf`;";
        db_exec( $sql ); db_error();
    
      case "0.34":
        $sql = "ALTER TABLE `operations`" .
            "\nADD `entree_reveil` TIME AFTER `sortie_bloc` ," .
            "\nADD `sortie_reveil` TIME AFTER `entree_reveil` ;";
        db_exec($sql); db_error();
        
      case "0.35":
        $sql = "ALTER TABLE `operations`" .
            "\nADD `entree_adm` DATETIME AFTER `admis`;";
        db_exec($sql); db_error();
        
        $sql = "UPDATE `operations` SET" .
            "\n`entree_adm` = ADDTIME(date_adm, time_adm)" .
            "\nWHERE `admis` = 'o'";
        db_exec($sql); db_error();
    
      case "0.36":
        $module = @CMbModule::getInstalled("dPbloc");
        if (!$module || $module->mod_version < "0.1") {
          return "0.36";
        }
      
        // Réparation des opérations avec `duree_hospi` = '255'
        $sql = "UPDATE `operations`, `plagesop` SET" .
            "\n`operations`.`date_adm` = `plagesop`.`date`," .
            "\n`operations`.`duree_hospi` = '1'" .
            "\nWHERE `operations`.`duree_hospi` = '255'" .
            "\nAND `operations`.`plageop_id` = `plagesop`.`id`";
        db_exec($sql); db_error();
      
        // Création de la table
        $sql = "CREATE TABLE `sejour` (" .
            "\n`sejour_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ," .
            "\n`patient_id` INT UNSIGNED NOT NULL ," .
            "\n`praticien_id` INT UNSIGNED NOT NULL ," .
            "\n`entree_prevue` DATETIME NOT NULL ," .
            "\n`sortie_prevue` DATETIME NOT NULL ," .
            "\n`entree_reelle` DATETIME," .
            "\n`sortie_reelle` DATETIME," .
            "\n`chambre_seule` ENUM('o','n') NOT NULL DEFAULT 'o'," .
            "\nPRIMARY KEY ( `sejour_id` )) TYPE=MyISAM";
        db_exec($sql); db_error();
        
        $sql = "ALTER TABLE `sejour` ADD INDEX ( `patient_id` )";
        db_exec($sql); db_error();
              
        $sql = "ALTER TABLE `sejour` ADD INDEX ( `praticien_id` )";
        db_exec($sql); db_error();
              
        $sql = "ALTER TABLE `sejour` ADD INDEX ( `entree_prevue` )";
        db_exec($sql); db_error();
              
        $sql = "ALTER TABLE `sejour` ADD INDEX ( `sortie_prevue` )";
        db_exec($sql); db_error();
              
        $sql = "ALTER TABLE `sejour` ADD INDEX ( `entree_reelle` )";
        db_exec($sql); db_error();
              
        $sql = "ALTER TABLE `sejour` ADD INDEX ( `sortie_reelle` )";
        db_exec($sql); db_error();
        
        // Migration de l'ancienne table
        $sql = "ALTER TABLE `sejour`" .
            "\nADD `tmp_operation_id` INT UNSIGNED NOT NULL AFTER `sejour_id`";
        db_exec($sql); db_error();
        
        $sql = "INSERT INTO `sejour` ( " .
            "\n  `sejour_id` , " .
            "\n  `tmp_operation_id` , " .
            "\n  `patient_id` , " .
            "\n  `praticien_id` , " .
            "\n  `entree_prevue` , " .
            "\n  `sortie_prevue` , " .
            "\n  `entree_reelle` , " .
            "\n  `sortie_reelle` , " .
            "\n  `chambre_seule` ) " .
            "\nSELECT " .
            "\n  '', " .
            "\n  `operation_id`, " .
            "\n  `pat_id`, " .
            "\n  `chir_id`, " .
            "\n  ADDTIME(`date_adm`, `time_adm`), " .
            "\n  ADDDATE(ADDTIME(`date_adm`, `time_adm`), `duree_hospi`), " .
            "\n  `entree_adm` , " .
            "\n  NULL , " .
            "\n `chambre` " .
            "\nFROM `operations`" .
            "\nWHERE `operations`.`pat_id` != 0";
        db_exec($sql); db_error();

        // Ajout d'une référence vers les sejour
        $sql = "ALTER TABLE `operations` " .
            "\nADD `sejour_id` INT UNSIGNED NOT NULL AFTER `operation_id`";
        db_exec($sql); db_error();

        $sql = "ALTER TABLE `operations` ADD INDEX ( `sejour_id` )";
        db_exec($sql); db_error();

        $sql = "UPDATE `operations`, `sejour` " .
            "\nSET `operations`.`sejour_id` = `sejour`.`sejour_id`" .
            "\nWHERE `sejour`.`tmp_operation_id` = `operations`.`operation_id`";
        db_exec($sql); db_error();

        $sql = "ALTER TABLE `sejour` DROP `tmp_operation_id` ";
        db_exec($sql); db_error();
        
      case "0.37":
        // Migration de nouvelles propriétés
        $sql = "ALTER TABLE `sejour` " .
            "\nADD `type` ENUM( 'comp', 'ambu', 'exte' ) DEFAULT 'comp' NOT NULL AFTER `praticien_id` ," .
            "\nADD `annule` TINYINT DEFAULT '0' NOT NULL AFTER `type` ," .
            "\nADD `venue_SHS` VARCHAR( 8 ) AFTER `annule` ," .
            "\nADD `saisi_SHS` ENUM( 'o', 'n' ) DEFAULT 'n' NOT NULL AFTER `venue_SHS` ," .
            "\nADD `modif_SHS` TINYINT DEFAULT '0' NOT NULL AFTER `saisi_SHS`";
        db_exec($sql); db_error();

        $sql = "ALTER TABLE `sejour` ADD INDEX ( `type` )";
        db_exec($sql); db_error();

        $sql = "ALTER TABLE `sejour` ADD INDEX ( `annule` )";
        db_exec($sql); db_error();

        $sql = "ALTER TABLE `sejour` ADD INDEX ( `venue_SHS` )";
        db_exec($sql); db_error();

        $sql = "ALTER TABLE `sejour` ADD INDEX ( `saisi_SHS` )";
        db_exec($sql); db_error();

        $sql = "ALTER TABLE `sejour` ADD INDEX ( `modif_SHS` )";
        db_exec($sql); db_error();
        
        $sql = "UPDATE `sejour`, `operations` SET" .
            "\n`sejour`.`type` = `operations`.`type_adm`," .
            "\n`sejour`.`annule` = `operations`.`annulee`," .
            "\n`sejour`.`venue_SHS` = `operations`.`venue_SHS`," .
            "\n`sejour`.`saisi_SHS` = `operations`.`saisie`," .
            "\n`sejour`.`modif_SHS` = `operations`.`modifiee`" .
            "\nWHERE `operations`.`sejour_id` = `sejour`.`sejour_id`";
        db_exec($sql); db_error();
            
      case "0.38":
        $sql = "UPDATE `sejour`, `operations` SET" .
            "\n`sejour`.`entree_reelle` = NULL" .
            "\nWHERE `operations`.`sejour_id` = `sejour`.`sejour_id`" .
            "\nAND `operations`.`admis` = 'n'";
        db_exec($sql); db_error();

        $sql = "ALTER TABLE `operations`" .
            "\nCHANGE `date_anesth` `date_anesth` DATE";
        db_exec($sql); db_error();
        
        $sql = "UPDATE `operations` " .
            "\nSET `date_anesth` = NULL" .
            "\nWHERE `date_anesth` = '0000-00-00'";
        db_exec($sql); db_error();
        
      case "0.39":
        $sql = "ALTER TABLE sejour" .
            "\nADD rques text;";
        db_exec($sql); db_error();
      
      case "0.40":
        $sql = "ALTER TABLE operations" .
            "\nADD pause time NOT NULL default '00:00:00' AFTER temp_operation";
        db_exec($sql); db_error();
      
      case "0.41":
        $sql = "ALTER TABLE `sejour` " .
          "\nADD `pathologie` VARCHAR( 8 ) DEFAULT NULL," .
          "\nADD `septique` TINYINT DEFAULT '0' NOT NULL ;";
        db_exec($sql); db_error();
        
        $sql = "UPDATE `sejour`, `operations` SET" .
            "\n`sejour`.`pathologie` = `operations`.`pathologie`," .
            "\n`sejour`.`septique` = `operations`.`septique`" .
            "\nWHERE `operations`.`sejour_id` = `sejour`.`sejour_id`";
        db_exec($sql); db_error();
      case "0.42":
        $sql = "ALTER TABLE `sejour` " .
          "\nADD `code_uf` VARCHAR( 8 ) DEFAULT NULL AFTER venue_SHS," .
          "\nADD `libelle_uf` TINYINT DEFAULT '0' NOT NULL AFTER code_uf;";
        db_exec($sql); db_error();
        
        $sql = "UPDATE `sejour`, `operations` SET" .
            "\n`sejour`.`code_uf` = `operations`.`code_uf`," .
            "\n`sejour`.`libelle_uf` = `operations`.`libelle_uf`" .
            "\nWHERE `operations`.`sejour_id` = `sejour`.`sejour_id`";
        db_exec($sql); db_error();
      case "0.43" :
        $sql = "ALTER TABLE `sejour` " .
          "\nADD `convalescence` TEXT DEFAULT NULL;";
        db_exec($sql); db_error();
        
        $sql = "UPDATE `sejour`, `operations` SET" .
            "\n`sejour`.`convalescence` = `operations`.`convalescence`" .
            "\nWHERE `operations`.`sejour_id` = `sejour`.`sejour_id`";
        db_exec($sql); db_error();
        
      case "0.44" :
        $sql = "ALTER TABLE `sejour` DROP `code_uf`;";
        db_exec($sql); db_error();

        $sql = "ALTER TABLE `sejour` DROP `libelle_uf`;";
        db_exec($sql); db_error();
        
        $sql = " ALTER TABLE `sejour` " .
            "\nADD `modalite_hospitalisation` ENUM( 'office', 'libre', 'tiers' ) NOT NULL DEFAULT 'libre' AFTER `type`;";
        db_exec($sql); db_error();

      case "0.45" :
        $sql = "ALTER TABLE `operations` DROP `entree_adm`;";
        db_exec($sql); db_error();

        $sql = "ALTER TABLE `operations` DROP `admis`;";
        db_exec($sql); db_error();

      case "0.46" :
        $sql = "ALTER TABLE `sejour` " .
          "\nADD `DP`  varchar(5) default NULL AFTER `rques`;";
        db_exec($sql); db_error();
        
        $sql = "UPDATE `sejour`, `operations` SET" .
            "\n`sejour`.`DP` = `operations`.`CIM10_code`" .
            "\nWHERE `operations`.`sejour_id` = `sejour`.`sejour_id`";
        db_exec($sql); db_error();
        
      case "0.47" :
        $sql = "CREATE TABLE protocole ( " .
          "  protocole_id INT UNSIGNED NOT NULL auto_increment" .
          ", chir_id INT UNSIGNED NOT NULL DEFAULT '0'" .
          ", type ENUM('comp','ambu','exte') DEFAULT 'comp'" .
          ", DP VARCHAR(5) DEFAULT NULL" .
          ", convalescence TEXT DEFAULT NULL" .
          ", rques_sejour TEXT DEFAULT NULL" .
          ", pathologie VARCHAR(8) DEFAULT NULL" .
          ", septique TINYINT DEFAULT '0' NOT NULL" .
          ", codes_ccam VARCHAR(160) DEFAULT NULL" .
          ", libelle TEXT DEFAULT NULL" .
          ", temp_operation TIME NOT NULL DEFAULT '00:00:00'" .
          ", examen TEXT DEFAULT NULL" .
          ", materiel TEXT DEFAULT NULL" .
          ", duree_hospi TINYINT(4) UNSIGNED NOT NULL DEFAULT '0'" .
          ", rques_operation TEXT DEFAULT NULL" .
          ", depassement TINYINT DEFAULT NULL" .
          ", PRIMARY KEY  (protocole_id)" .
          ") TYPE=MyISAM;";
        db_exec( $sql ); db_error();
    
        $sql = "ALTER TABLE `protocole` ADD INDEX (`chir_id`)";
        db_exec($sql); db_error();
        
        $sql = "INSERT INTO `protocole` (" .
            " `protocole_id`, `chir_id`," .
            " `type`, `DP`, `convalescence`, `rques_sejour`, `pathologie`, `septique`," .
            " `codes_ccam`, `libelle`, `temp_operation`, `examen`, `materiel`," .
            " `duree_hospi`, `rques_operation`,  `depassement`)" .
            "\nSELECT '', `operations`.`chir_id`," .
            " `operations`.`type_adm`, `operations`.`CIM10_code`, `operations`.`convalescence`," .
            " '', '', '', `operations`.`codes_ccam`, `operations`.`libelle`," .
            " `operations`.`temp_operation`, `operations`.`examen`," .
            " `operations`.`materiel`, `operations`.`duree_hospi`, `operations`.`rques`," .
            " `operations`.`depassement`" .
            "\nFROM `operations`" .
            "\nWHERE `operations`.`pat_id` = 0";
        db_exec($sql); db_error();
        
        $sql = "DELETE FROM `operations` WHERE `pat_id` = 0";
        db_exec($sql); db_error();
      
      case "0.48":
        $sql = "ALTER TABLE `sejour` CHANGE `modalite_hospitalisation` " .
            "\n`modalite` ENUM( 'office', 'libre', 'tiers' ) DEFAULT 'libre' NOT NULL";
        db_exec($sql); db_error();

      case "0.49":
        $sql = "UPDATE `operations` SET `date` = NULL;";
        db_exec($sql); db_error();
      case "0.50":
        $sql = "ALTER TABLE `operations` ADD `anesth_id` INT UNSIGNED DEFAULT NULL AFTER `chir_id`";
        db_exec($sql); db_error();
      case "0.51":
        $module = @CMbModule::getInstalled("dPetablissement");
        if (!$module || $module->mod_version < "0.1") {
          return "0.51";
        }
        $sql = "ALTER TABLE `sejour` ADD `group_id` INT UNSIGNED NOT NULL DEFAULT 1 AFTER `praticien_id`";
        db_exec($sql); db_error();
        $sql = "ALTER TABLE `sejour` ADD INDEX ( `group_id` ) ;";
        db_exec( $sql ); db_error();
      case "0.52":
        $sql = "ALTER TABLE `operations` DROP INDEX `operation_id` ;";
        db_exec($sql); db_error();
        $sql = "ALTER TABLE `operations` ADD INDEX ( `anesth_id` ) ;";
        db_exec($sql); db_error();
        $sql = "ALTER TABLE `operations`" .
            "\nDROP `pat_id`, DROP `CCAM_code`, DROP `CCAM_code2`," .
            "\nDROP `compte_rendu`, DROP `cr_valide`, DROP `date_adm`," .
            "\nDROP `time_adm`, DROP `chambre`, DROP `type_adm`," .
            "\nDROP `venue_SHS`, DROP `saisie`, DROP `modifiee`," .
            "\nDROP `CIM10_code`, DROP `convalescence`, DROP `pathologie`," .
            "\nDROP `septique` ;";
        db_exec($sql); db_error();
      case "0.53":
        return "0.53";
    }
    return false;
  }
}

?>