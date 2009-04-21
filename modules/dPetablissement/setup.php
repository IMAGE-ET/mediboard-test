<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPetablissement
 * @version $Revision$
 * @author Romain Ollivier
 */

class CSetupdPetablissement extends CSetup {
  function __construct() {
    parent::__construct();
    
    $this->mod_type = "core";
    $this->mod_name = "dPetablissement";
    
    $this->makeRevision("all");
    
    $this->makeRevision("0.1");
    $sql = "ALTER TABLE `groups_mediboard`" .
            "\nADD `raison_sociale` VARCHAR( 50 ) ," .
            "\nADD `adresse` TEXT ," .
            "\nADD `cp` VARCHAR( 5 ) ," .
            "\nADD `ville` VARCHAR( 50 ) ," .
            "\nADD `tel` VARCHAR( 10 ) ," .
            "\nADD `directeur` VARCHAR( 50 ) ," .
            "\nADD `domiciliation` VARCHAR( 9 ) ," .
            "\nADD `siret` VARCHAR( 14 );";
    $this->addQuery($sql);
    $sql = "INSERT INTO `groups_mediboard` ( `group_id` , `text` )
						VALUES (NULL , 'Etablissement');";
    $this->addQuery($sql);

    $this->makeRevision("0.11");
    $sql = "ALTER TABLE `groups_mediboard` ADD `ape` VARCHAR( 4 ) DEFAULT NULL;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.12");
    $sql = "ALTER TABLE `groups_mediboard` " .
               "\nCHANGE `group_id` `group_id` int(11) unsigned NOT NULL AUTO_INCREMENT," .
               "\nCHANGE `cp` `cp` int(5) unsigned zerofill NULL," .
               "\nCHANGE `tel` `tel` bigint(10) unsigned zerofill NULL," .
               "\nCHANGE `text` `text` varchar(255) NOT NULL;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.13");
    $sql = "ALTER TABLE `groups_mediboard`" .
            "\nADD `fax` bigint(10) unsigned zerofill NULL AFTER `tel`,".
            "\nADD `mail` varchar(50) DEFAULT NULL," .
            "\nADD `web` varchar(255) DEFAULT NULL;" ;
    $this->addQuery($sql);

    
    $this->makeRevision("0.14");
    $sql = "ALTER TABLE `groups_mediboard`  
            ADD `tel_anesth` BIGINT(10) UNSIGNED ZEROFILL;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.15");
    $sql = "CREATE TABLE `etab_externe` (
           `etab_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT, 
           `nom` VARCHAR(255) NOT NULL, 
           `raison_sociale` VARCHAR(50), 
           `adresse` TEXT, 
           `cp` INT(5) UNSIGNED ZEROFILL, 
           `ville` VARCHAR(50),  
           `tel` BIGINT(10) UNSIGNED ZEROFILL, 
           `fax` BIGINT(10) UNSIGNED ZEROFILL, 
           `finess` INT(9) UNSIGNED ZEROFILL, 
           PRIMARY KEY (`etab_id`)) TYPE=MYISAM;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.16");
    $sql = "ALTER TABLE `etab_externe`
            ADD `siret` CHAR(14), 
            ADD `ape` CHAR(4);";
    $this->addQuery($sql);
    
    $this->makeRevision("0.17");
    $sql = "ALTER TABLE `groups_mediboard`
            ADD `service_urgences_id` INT(11) UNSIGNED;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.18");
    $sql = "ALTER TABLE `etab_externe`
            CHANGE `ape` `ape` VARCHAR(6);";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `groups_mediboard`
            CHANGE `ape` `ape` VARCHAR(6);";
    $this->addQuery($sql);
    
    $this->mod_version = "0.19";
    
  } 
  
}

?>