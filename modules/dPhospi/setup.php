<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI;

// MODULE CONFIGURATION DEFINITION
$config = array();
$config["mod_name"]        = "dPhospi";
$config["mod_version"]     = "0.22";
$config["mod_type"]        = "user";


class CSetupdPhospi extends CSetup {
  
  function __construct() {
    parent::__construct();
    
    $this->mod_name = "dPhospi";
    
    $this->makeRevision("all");
    $sql = "CREATE TABLE `service` (" .
          "\n`service_id` INT NOT NULL AUTO_INCREMENT ," .
          "\n`nom` VARCHAR( 50 ) NOT NULL ," .
          "\n`description` TEXT," .
          "\nPRIMARY KEY ( `service_id` )) TYPE=MyISAM;";
    $this->addQuery($sql);
    $sql = "CREATE TABLE `chambre` (" .
          "\n`chambre_id` INT NOT NULL AUTO_INCREMENT ," .
          "\n`service_id` INT NOT NULL ," .
          "\n`nom` VARCHAR( 50 ) ," .
          "\n`caracteristiques` TEXT," .
          "\nPRIMARY KEY ( `chambre_id` ) ," .
          "\nINDEX ( `service_id` )) TYPE=MyISAM;";
    $this->addQuery($sql);
    $sql = "CREATE TABLE `lit` (" .
          "\n`lit_id` INT NOT NULL AUTO_INCREMENT ," .
          "\n`chambre_id` INT NOT NULL," .
          "\n`nom` VARCHAR( 50 ) NOT NULL ," .
          "\nPRIMARY KEY ( `lit_id` ) ," .
          "\nINDEX ( `chambre_id` )) TYPE=MyISAM;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.1");
    $sql = "CREATE TABLE `affectation` (" .
            "\n`affectation_id` INT NOT NULL AUTO_INCREMENT," .
            "\n`lit_id` INT NOT NULL ," .
            "\n`operation_id` INT NOT NULL ," .
            "\n`entree` DATETIME NOT NULL ," .
            "\n`sortie` DATETIME NOT NULL ," .
            "\nPRIMARY KEY ( `affectation_id` ) ," .
            "\nINDEX ( `lit_id` , `operation_id` )) TYPE=MyISAM;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.11");
    $sql = "ALTER TABLE `affectation` " .
            "\nADD `confirme` TINYINT DEFAULT '0' NOT NULL," .
            "\nADD `effectue` TINYINT DEFAULT '0' NOT NULL ;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.12");
    $sql = "ALTER TABLE `affectation` ADD INDEX ( `entree` );";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `affectation` ADD INDEX ( `sortie` );";
    $this->addQuery($sql);
    
    $this->makeRevision("0.13");
    $sql = "ALTER TABLE `affectation` ADD INDEX ( `operation_id` ) ;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `affectation` ADD INDEX ( `lit_id` ) ;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.14");
    $this->addDependency("dPplanningOp", "0.38");
    $sql = "DELETE affectation.* FROM affectation" .
            "\nLEFT JOIN operations" .
            "\nON affectation.operation_id = operations.operation_id" .
            "\nWHERE operations.operation_id IS NULL;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `affectation`" .
            "\nADD `sejour_id` INT UNSIGNED DEFAULT '0' NOT NULL AFTER `operation_id`;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `affectation` ADD INDEX (`sejour_id`);";
    $this->addQuery($sql);
    $sql = "UPDATE `affectation`,`operations`" .
            "\nSET `affectation`.`sejour_id` = `operations`.`sejour_id`" .
            "\nWHERE `affectation`.`operation_id` = `operations`.`operation_id`;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.15");
    $this->addDependency("dPetablissement", "0.1");
    $sql = "ALTER TABLE `service` ADD `group_id` INT UNSIGNED NOT NULL DEFAULT 1 AFTER `service_id`;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `service` ADD INDEX ( `group_id` ) ;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.16");
    $sql = "ALTER TABLE `affectation` DROP `operation_id`";
    $this->addQuery($sql);
    
    $this->makeRevision("0.17");
    $sql = "ALTER TABLE `affectation` " .
               "\nCHANGE `affectation_id` `affectation_id` int(11) unsigned NOT NULL AUTO_INCREMENT," .
               "\nCHANGE `lit_id` `lit_id` int(11) unsigned NOT NULL DEFAULT '0'," .
               "\nCHANGE `confirme` `confirme` enum('0','1') NOT NULL DEFAULT '0'," .
               "\nCHANGE `effectue` `effectue` enum('0','1') NOT NULL DEFAULT '0'," .
               "\nCHANGE `sejour_id` `sejour_id` int(11) unsigned NOT NULL DEFAULT '0';";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `chambre` " .
               "\nCHANGE `chambre_id` `chambre_id` int(11) unsigned NOT NULL AUTO_INCREMENT," .
               "\nCHANGE `service_id` `service_id` int(11) unsigned NOT NULL DEFAULT '0'," .
               "\nCHANGE `nom` `nom` varchar(255) NOT NULL;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `lit` " .
               "\nCHANGE `lit_id` `lit_id` int(11) unsigned NOT NULL AUTO_INCREMENT," .
               "\nCHANGE `chambre_id` `chambre_id` int(11) unsigned NOT NULL," .
               "\nCHANGE `nom` `nom` varchar(255) NOT NULL;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `service` " .
               "\nCHANGE `service_id` `service_id` int(11) unsigned NOT NULL AUTO_INCREMENT," .
               "\nCHANGE `group_id` `group_id` int(11) unsigned NOT NULL DEFAULT '1'," .
               "\nCHANGE `nom` `nom` varchar(255) NOT NULL;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.18");
    $sql = "ALTER TABLE `affectation` ADD `rques` text NULL;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.19");
    $sql = "ALTER TABLE `affectation` ADD INDEX ( `confirme` )";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `affectation` ADD INDEX ( `effectue` )";
    $this->addQuery($sql);
    
    
    $this->makeRevision("0.20");
    $sql= "CREATE TABLE `prestation` (
           `prestation_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT, 
           `group_id` INT(11) UNSIGNED NOT NULL, 
           `nom` VARCHAR(255) NOT NULL, 
           `description` TEXT, 
            PRIMARY KEY (`prestation_id`)) TYPE=MYISAM;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.21");
    $sql = "INSERT INTO `user_preferences` ( `pref_user` , `pref_name` , `pref_value` ) VALUES ('0', 'ccam_sejour', '0');";
    $this->addQuery($sql);
    
    $this->mod_version = "0.22";
  }
}
?>