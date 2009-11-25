<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPressources
 * @version $Revision$
 * @author Romain Ollivier
 */

class CSetupdPressources extends CSetup {
  
  function __construct() {
    parent::__construct();
    
    $this->mod_name = "dPressources";
    
    $this->makeRevision("all");
    $sql = "CREATE TABLE `plageressource` (
                 `plageressource_id` BIGINT NOT NULL AUTO_INCREMENT ,
                 `prat_id` BIGINT,
                 `date` DATE NOT NULL ,
                 `debut` TIME NOT NULL ,
                 `fin` TIME NOT NULL ,
                 `tarif` FLOAT DEFAULT '0' NOT NULL ,
                 `paye` TINYINT DEFAULT '0' NOT NULL ,
                 `libelle` VARCHAR( 50 ) ,
                 PRIMARY KEY ( `plageressource_id` )
               ) TYPE=MyISAM COMMENT = 'Table des plages de ressource';";
    $this->addQuery($sql);
    
    $this->makeRevision("0.1");
    $sql = "ALTER TABLE `plageressource` ADD INDEX ( `prat_id` ) ;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.11");
    $sql = "ALTER TABLE `plageressource` " .
               "\nCHANGE `plageressource_id` `plageressource_id` int(11) unsigned NOT NULL AUTO_INCREMENT," .
               "\nCHANGE `prat_id` `prat_id` int(11) unsigned NULL," .
               "\nCHANGE `paye` `paye` enum('0','1') NOT NULL DEFAULT '0'," .
               "\nCHANGE `libelle` `libelle` varchar(255) NULL;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.12");
    $sql = "UPDATE `plageressource` SET prat_id = NULL WHERE prat_id='0';";
    $this->addQuery($sql);
    
    $this->makeRevision("0.13");
    $sql = "ALTER TABLE `plageressource` 
              ADD INDEX (`date`);";
    $this->addQuery($sql);
    
    $this->mod_version = "0.14";
    
  }
}
?>