<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision$
* @author Romain Ollivier
*/

// MODULE CONFIGURATION DEFINITION
$config = array();
$config["mod_name"]        = "dPcompteRendu";
$config["mod_version"]     = "0.31";
$config["mod_type"]        = "user";

class CSetupdPcompteRendu extends CSetup {
  
  function __construct() {
    parent::__construct();
    
    $this->mod_name = "dPcompteRendu";
    
    $this->makeRevision("all");
    $sql = "CREATE TABLE compte_rendu (" .
            "\ncompte_rendu_id BIGINT NOT NULL AUTO_INCREMENT ," .
            "\nchir_id BIGINT DEFAULT '0' NOT NULL ," .
            "\nnom VARCHAR(50) ," .
            "\nsource TEXT," .
            "\ntype ENUM('consultation', 'operation', 'hospitalisation', 'autre') DEFAULT 'autre' NOT NULL ," .
            "\nPRIMARY KEY (compte_rendu_id) ," .
            "\nINDEX (chir_id)" .
            "\n) TYPE=MyISAM COMMENT = 'Table des modeles de compte-rendu';";
    $this->addQuery($sql);
    $sql = "ALTER TABLE permissions" .
            "\nCHANGE permission_grant_on permission_grant_on VARCHAR(25) NOT NULL";
    $this->addQuery($sql);
    
    $this->makeRevision("0.1");
    $sql = "CREATE TABLE `aide_saisie` (" .
            "\n`aide_id` INT NOT NULL AUTO_INCREMENT ," .
            "\n`user_id` INT NOT NULL ," .
            "\n`module` VARCHAR(20) NOT NULL ," .
            "\n`class` VARCHAR(20) NOT NULL ," .
            "\n`field` VARCHAR(20) NOT NULL ," .
            "\n`name` VARCHAR(40) NOT NULL ," .
            "\n`text` TEXT NOT NULL ," .
            "\nPRIMARY KEY (`aide_id`)) TYPE=MyISAM;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.11");
    $sql = "CREATE TABLE `liste_choix` (
                  `liste_choix_id` BIGINT NOT NULL AUTO_INCREMENT ,
                  `chir_id` BIGINT NOT NULL ,
                  `nom` VARCHAR(50) NOT NULL ,
                  `valeurs` TEXT,
                  PRIMARY KEY (`liste_choix_id`) ,
                  INDEX (`chir_id`)
                ) TYPE=MyISAM COMMENT = 'table des listes de choix personnalisées';";
    $this->addQuery($sql);
    
    $this->makeRevision("0.12");
    $sql = "CREATE TABLE `pack` (
                  `pack_id` BIGINT NOT NULL AUTO_INCREMENT ,
                  `chir_id` BIGINT NOT NULL ,
                  `nom` VARCHAR(50) NOT NULL ,
                  `modeles` TEXT,
                  PRIMARY KEY (`pack_id`) ,
                  INDEX (`chir_id`)
                ) TYPE=MyISAM COMMENT = 'table des packs post hospitalisation';";
    $this->addQuery($sql);
    
    $this->makeRevision("0.13");
    $sql = "ALTER TABLE `liste_choix` ADD `compte_rendu_id` BIGINT DEFAULT '0' NOT NULL AFTER `chir_id` ;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `liste_choix` ADD INDEX (`compte_rendu_id`) ;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.14");
    $sql = "ALTER TABLE `compte_rendu` ADD `object_id` BIGINT DEFAULT NULL AFTER `chir_id` ;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `compte_rendu` ADD INDEX (`object_id`) ;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.15");
    $sql = "ALTER TABLE `compte_rendu` ADD `valide` TINYINT DEFAULT 0;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.16");
    $sql = "ALTER TABLE `compte_rendu` ADD `function_id` BIGINT DEFAULT NULL AFTER `chir_id` ;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `compte_rendu` ADD INDEX (`function_id`) ;";
    $this->addQuery($sql);
    $sql = " ALTER TABLE `compte_rendu` CHANGE `chir_id` `chir_id` BIGINT(20) DEFAULT NULL";
    $this->addQuery($sql);
    
    $this->makeRevision("0.17");
    $sql = "ALTER TABLE `liste_choix` ADD `function_id` BIGINT DEFAULT NULL AFTER `chir_id` ;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `liste_choix` ADD INDEX (`function_id`) ;";
    $this->addQuery($sql);
    $sql = " ALTER TABLE `liste_choix` CHANGE `chir_id` `chir_id` BIGINT(20) DEFAULT NULL";
    $this->addQuery($sql);
    
    $this->makeRevision("0.18");
    $sql = "ALTER TABLE `aide_saisie` DROP `module` ";
    $this->addQuery($sql);
    
    $this->makeRevision("0.19");
    $this->setTimeLimit(1800);
    $sql = "ALTER TABLE `compte_rendu` CHANGE `type` `type` ENUM('consultation', 'consultAnesth', 'operation', 'hospitalisation', 'autre') DEFAULT 'autre' NOT NULL";
    $this->addQuery($sql);
    
    $this->makeRevision("0.20");
    $this->setTimeLimit(1800);
    $sql = "ALTER TABLE `compte_rendu` CHANGE `type` `type` ENUM('patient', 'consultation', 'consultAnesth', 'operation', 'hospitalisation', 'autre') DEFAULT 'autre' NOT NULL";
    $this->addQuery($sql);
    
    $this->makeRevision("0.21");
    $sql = "UPDATE `aide_saisie` SET `class`=CONCAT(\"C\",`class`);";
    $this->addQuery($sql);
    
    $this->makeRevision("0.22");
    $this->setTimeLimit(1800);
    $sql = "ALTER TABLE `compte_rendu` CHANGE `type` `type`  VARCHAR(30) NOT NULL DEFAULT 'autre'";
    $this->addQuery($sql);
    
    $this->makeRevision("0.23");
    $this->setTimeLimit(1800);
    $this->addDependency("dPfiles","0.14");
    $sql = "ALTER TABLE `compte_rendu` CHANGE `type` `object_class` VARCHAR(30) DEFAULT NULL;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `compte_rendu` ADD `file_category_id` INT(11) DEFAULT 0;";
    $this->addQuery($sql);
    function setup_category(){
      $aConversion = array();
      $aConversion["operation"]       = array("class"=>"COperation",    "nom"=>"Opération");
      $aConversion["hospitalisation"] = array("class"=>"COperation",    "nom"=>"Hospitalisation");
      $aConversion["consultation"]    = array("class"=>"CConsultation", "nom"=>null);
      $aConversion["consultAnesth"]   = array("class"=>"CConsultAnesth","nom"=>null);
      $aConversion["patient"]         = array("class"=>"CPatient",      "nom"=>null);
      foreach($aConversion as $sKey=>$aValue){
        $category = new CFilesCategory();
        if($aValue["nom"]){
          $category->nom    = $aValue["nom"];
          $category->class = $aValue["class"];
          $category->store();
        }else{
          $category->file_category_id = 0;
        }
        $sql = "UPDATE `compte_rendu` SET `file_category_id`='".$category->file_category_id."', 
               `object_class`='".$aValue["class"]."' WHERE `object_class`='$sKey'";
        db_exec($sql); db_error();          
      }
      return true;
    }
    $this->addFunctions("setup_category");
    
    $this->makeRevision("0.24");
    $sql = "ALTER TABLE `aide_saisie` ADD `function_id` int(10) unsigned NULL AFTER `user_id` ;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.25");
    $this->setTimeLimit(1800);
    $sql = "ALTER TABLE `aide_saisie` " .
               "\nCHANGE `aide_id` `aide_id` int(11) unsigned NOT NULL AUTO_INCREMENT," .
               "\nCHANGE `user_id` `user_id` int(11) unsigned NOT NULL DEFAULT '0'," .
               "\nCHANGE `function_id` `function_id` int(11) unsigned NULL," .
               "\nCHANGE `class` `class` varchar(255) NOT NULL," .
               "\nCHANGE `field` `field` varchar(255) NOT NULL," .
               "\nCHANGE `name` `name` varchar(255) NOT NULL;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `compte_rendu` " .
               "\nCHANGE `compte_rendu_id` `compte_rendu_id` int(11) unsigned NOT NULL AUTO_INCREMENT," .
               "\nCHANGE `chir_id` `chir_id` int(11) unsigned NULL," .
               "\nCHANGE `function_id` `function_id` int(11) unsigned NULL," .
               "\nCHANGE `object_id` `object_id` int(11) unsigned NULL," .
               "\nCHANGE `nom` `nom` varchar(255) NOT NULL," .
               "\nCHANGE `source` `source` mediumtext NULL," .
               "\nCHANGE `object_class` `object_class` enum('CPatient','CConsultAnesth','COperation','CConsultation') NOT NULL DEFAULT 'CPatient'," .
               "\nCHANGE `valide` `valide` tinyint(1) unsigned zerofill NOT NULL DEFAULT '0'," .
               "\nCHANGE `file_category_id` `file_category_id` int(11) unsigned NOT NULL DEFAULT '0';";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `liste_choix` " .
               "\nCHANGE `liste_choix_id` `liste_choix_id` int(11) unsigned NOT NULL AUTO_INCREMENT," .
               "\nCHANGE `chir_id` `chir_id` int(11) unsigned NULL," .
               "\nCHANGE `function_id` `function_id` int(11) unsigned NULL," .
               "\nCHANGE `nom` `nom` varchar(255) NOT NULL," .
               "\nCHANGE `compte_rendu_id` `compte_rendu_id` int(11) unsigned NOT NULL DEFAULT '0';";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `pack` " .
               "\nCHANGE `pack_id` `pack_id` int(11) unsigned NOT NULL AUTO_INCREMENT," .
               "\nCHANGE `chir_id` `chir_id` int(11) unsigned NOT NULL DEFAULT '0'," .
               "\nCHANGE `nom` `nom` varchar(255) NOT NULL;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.26");
    $this->setTimeLimit(1800);
    $sql = "ALTER TABLE `compte_rendu` ADD INDEX ( `object_class` );";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `compte_rendu` ADD INDEX ( `file_category_id` );";
    $this->addQuery($sql);
    
    $this->makeRevision("0.27");
    $this->setTimeLimit(1800);
    $sql = "UPDATE `liste_choix` SET function_id = NULL WHERE function_id='0';";
    $this->addQuery($sql);
    $sql = "UPDATE `liste_choix` SET chir_id = NULL WHERE chir_id='0';";
    $this->addQuery($sql);
    
    $this->makeRevision("0.28");
    $this->setTimeLimit(1800);
    $sql = "DELETE FROM `pack` WHERE chir_id='0';";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `compte_rendu` CHANGE `file_category_id` `file_category_id` int(11) unsigned NULL DEFAULT NULL;";
    $this->addQuery($sql);
    $sql = "UPDATE `compte_rendu` SET `file_category_id` = NULL WHERE `file_category_id` = '0';";
    $this->addQuery($sql);
    
    $this->makeRevision("0.29");
    $this->setTimeLimit(1800);
    $sql = "UPDATE `compte_rendu` SET `function_id` = NULL WHERE `function_id` = '0';";
    $this->addQuery($sql);
    $sql = "UPDATE `compte_rendu` SET `chir_id` = NULL WHERE `chir_id` = '0';";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `liste_choix` CHANGE `compte_rendu_id` `compte_rendu_id` int(11) unsigned NULL DEFAULT NULL;";
    $this->addQuery($sql);
    $sql = "UPDATE `liste_choix` SET compte_rendu_id = NULL WHERE compte_rendu_id='0';";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `aide_saisie` CHANGE `user_id` `user_id` int(11) unsigned NULL DEFAULT NULL;";
    $this->addQuery($sql);
    $sql = "UPDATE `aide_saisie` SET function_id = NULL WHERE function_id='0';";
    $this->addQuery($sql);
    $sql = "UPDATE `aide_saisie` SET user_id = NULL WHERE user_id='0';";
    $this->addQuery($sql);
    
    $this->makeRevision("0.30");
    $sql = "ALTER TABLE `aide_saisie` ADD `depend_value` varchar(255) DEFAULT NULL;";
    $this->addQuery($sql);
    
    $this->mod_version = "0.31";
  }
}
?>