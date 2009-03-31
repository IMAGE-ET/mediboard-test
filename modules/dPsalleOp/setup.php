<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPsalleOp
 * @version $Revision$
 * @author Romain Ollivier
 */

class CSetupdPsalleOp extends CSetup {
  
  function __construct() {
    parent::__construct();
    
    $this->mod_name = "dPsalleOp";
    
    $this->makeRevision("all");
    
    $this->makeRevision("0.1");
    $sql = "CREATE TABLE `acte_ccam` (" .
            "\n`acte_id` INT NOT NULL ," .
            "\n`code_activite` VARCHAR( 2 ) NOT NULL ," .
            "\n`code_phase` VARCHAR( 1 ) NOT NULL ," .
            "\n`execution` DATETIME NOT NULL ," .
            "\n`modificateurs` VARCHAR( 4 ) ," .
            "\n`montant_depassement` FLOAT," .
            "\n`commentaire` TEXT," .
            "\n`operation_id` INT NOT NULL ," .
            "\n`executant_id` INT NOT NULL ," .
            "\nPRIMARY KEY ( `acte_id` )) TYPE=MyISAM";
    $this->addQuery($sql);
    
    $this->makeRevision("0.11");
    $sql = "ALTER TABLE `acte_ccam` ADD `code_acte` CHAR( 7 ) NOT NULL AFTER `acte_id`";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `acte_ccam` " .
            "ADD UNIQUE (" .
              "`code_acte` ," .
              "`code_activite` ," .
              "`code_phase` ," .
              "`operation_id`)";
    $this->addQuery($sql);
    
    $this->makeRevision("0.12");
    $sql =  "ALTER TABLE `acte_ccam` CHANGE `acte_id` `acte_id` INT( 11 ) NOT NULL AUTO_INCREMENT";
    $this->addQuery($sql);
    
    $this->makeRevision("0.13");
    $sql =  "ALTER TABLE `acte_ccam` DROP INDEX `code_acte`";
    $this->addQuery($sql);
    
    $this->makeRevision("0.14");
    $sql = "ALTER TABLE `acte_ccam` " .
               "\nCHANGE `acte_id` `acte_id` int(11) unsigned NOT NULL AUTO_INCREMENT," .
               "\nCHANGE `operation_id` `operation_id` int(11) unsigned NOT NULL DEFAULT '0'," .
               "\nCHANGE `executant_id` `executant_id` int(11) unsigned NOT NULL DEFAULT '0'," .
               "\nCHANGE `code_activite` `code_activite` tinyint(2) unsigned zerofill NOT NULL," .
               "\nCHANGE `code_phase` `code_phase` tinyint(1) unsigned zerofill NOT NULL;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.15");
    $sql = "ALTER TABLE `acte_ccam` CHANGE `code_acte` `code_acte` varchar(7) NOT NULL;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.16");
    $sql = "ALTER TABLE `acte_ccam`" .
        "\nCHANGE `code_activite` `code_activite` TINYINT(4) NOT NULL," .
        "\nCHANGE `code_phase` `code_phase` TINYINT(4) NOT NULL;";
    $this->addQuery($sql);

    $this->makeRevision("0.17");
    $sql = "ALTER TABLE `acte_ccam`" .
        "\nCHANGE `operation_id` `subject_id` int(11) unsigned NOT NULL DEFAULT '0'," .
        "\nADD `subject_class` VARCHAR(25) NOT NULL;";
    $this->addQuery($sql);
    $sql = "UPDATE `acte_ccam` SET `subject_class` = 'COperation';";
    $this->addQuery($sql); 
    
    $this->makeRevision("0.18");
    $sql = "ALTER TABLE `acte_ccam`" .
        "\nCHANGE `subject_id` `object_id` int(11) unsigned NOT NULL DEFAULT '0'," .
        "\nCHANGE `subject_class` `object_class` VARCHAR(25) NOT NULL;";
    $this->addQuery($sql); 
    
    $this->makeRevision("0.19");
    $sql = "ALTER TABLE `acte_ccam`
            ADD `code_association` TINYINT(4)";
    $this->addQuery($sql);
    
    $this->makerevision("0.20");
    $sql = "ALTER TABLE `acte_ccam`
            ADD `regle` ENUM('0','1');";
    $this->addQuery($sql);
    
    $this->makerevision("0.21");
    $sql = "ALTER TABLE `acte_ccam`
              ADD INDEX ( `code_acte` ),
              ADD INDEX ( `code_activite` ),
              ADD INDEX ( `code_phase` ),
              ADD INDEX ( `object_id` ),
              ADD INDEX ( `executant_id` ),
              ADD INDEX ( `object_class` )";
    $this->addQuery($sql);
    
    $this->makeRevision("0.22");
    $sql = "ALTER TABLE `acte_ccam`
            ADD `montant_base` FLOAT;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.23");
    $sql = "ALTER TABLE `acte_ccam`
            ADD `signe` ENUM('0','1') DEFAULT '0';";
    $this->addQuery($sql);
    
    $this->makeRevision("0.24");
    $sql = "ALTER TABLE `acte_ccam`
						ADD `rembourse` ENUM('0','1'), 
						CHANGE `object_class` `object_class` ENUM('COperation','CSejour','CConsultation') NOT NULL;";
    $this->addQuery($sql);
		
		$this->makeRevision("0.25");
		$sql = "CREATE TABLE `daily_check_item` (
						  `daily_check_item_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
						  `list_id` INT (11) UNSIGNED NOT NULL,
						  `item_type_id` INT (11) UNSIGNED NOT NULL,
						  `checked` ENUM ('0','1') NOT NULL
						) TYPE=MYISAM;";
		$this->addQuery($sql);
		$sql = "ALTER TABLE `daily_check_item` 
						  ADD INDEX (`list_id`),
						  ADD INDEX (`item_type_id`);";
		$this->addQuery($sql);
		
		$sql = "CREATE TABLE `daily_check_item_type` (
						  `daily_check_item_type_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
						  `title` VARCHAR (255) NOT NULL,
						  `desc` TEXT,
						  `active` ENUM ('0','1') NOT NULL,
						  `group_id` INT (11) UNSIGNED NOT NULL
						) TYPE=MYISAM;";
		$this->addQuery($sql);
    $sql = "ALTER TABLE `daily_check_item_type` ADD INDEX (`group_id`)";
		$this->addQuery($sql);

    $sql = "CREATE TABLE `daily_check_list` (
						  `daily_check_list_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
						  `date` DATE NOT NULL,
						  `room_id` INT (11) UNSIGNED NOT NULL,
						  `validator_id` INT (11) UNSIGNED
						) TYPE=MYISAM;";
    $this->addQuery($sql);
		$sql = "ALTER TABLE `daily_check_list` 
						  ADD INDEX (`date`),
						  ADD INDEX (`room_id`),
						  ADD INDEX (`validator_id`);";
    $this->addQuery($sql);
    
    $this->mod_version = "0.26";
  }
}
?>