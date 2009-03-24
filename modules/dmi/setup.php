<?php /* $Id: setup.php 5173 2008-11-04 14:53:07Z mytto $ */

/**
 * @package Mediboard
 * @subpackage dPcabinet
 * @version $Revision: 5173 $
 * @author Romain Ollivier
 */

class CSetupdmi extends CSetup {
  
  function __construct() {
    parent::__construct();
    
    $this->mod_name = "dmi";
    
    $this->makeRevision("all");
    $sql = "CREATE TABLE `dmi_category` (
			`category_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
			`nom` VARCHAR (255) NOT NULL,
			`description` VARCHAR (255)
		) TYPE=MYISAM;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.01");
    $sql = "ALTER TABLE `dmi_category` 
			CHANGE `description` `description` TEXT;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.02");
    $sql = "ALTER TABLE `dmi_category` 
			ADD `group_id` INT (11) UNSIGNED NOT NULL;";
    $this->addQuery($sql);

    $sql = "ALTER TABLE `dmi_category`
			ADD INDEX (`group_id`);";
    $this->addQuery($sql);
    
    $this->makeRevision("0.03");
    $sql = "CREATE TABLE `dmi` (
		`dmi_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
		`nom` VARCHAR (255) NOT NULL,
		`en_lot` ENUM ('0','1'),
		`description` TEXT,
		`reference` VARCHAR (255) NOT NULL,
		`lot` VARCHAR (255),
		`dans_livret` ENUM ('0','1'),
		`category_id` INT (11) UNSIGNED NOT NULL
		) TYPE=MYISAM;";
    $this->addQuery($sql);

    $sql = "ALTER TABLE `dmi` 
	  ADD INDEX (`category_id`);";
    $this->addQuery($sql);
    
    $this->makeRevision("0.04");
    $sql = "ALTER TABLE `dmi` 
    CHANGE `reference` `code` VARCHAR (255) NOT NULL,
    DROP `en_lot`,
    DROP `lot`;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.05");
    $sql = "CREATE TABLE `dm` (
							`dm_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
							`category_dm_id` INT (11) UNSIGNED NOT NULL,
							`nom` VARCHAR (255) NOT NULL,
							`description` TEXT,
							`code` VARCHAR (255) NOT NULL,
							`in_livret` ENUM ('0','1')
						) TYPE=MYISAM;";
    $this->addQuery($sql);
    
    $sql = "CREATE TABLE `category_dm` (
							`category_dm_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
							`nom` VARCHAR (255) NOT NULL,
							`description` TEXT,
							`group_id` INT (11) UNSIGNED NOT NULL
						) TYPE=MYISAM;";
    $this->addQuery($sql);
    
    $sql = "ALTER TABLE `dm` 
						ADD INDEX (`category_dm_id`);";
    $this->addQuery($sql);
    
    $sql = "ALTER TABLE `category_dm` 
			      ADD INDEX (`group_id`);";
    $this->addQuery($sql);

    $sql = "ALTER TABLE `dmi` 
	          CHANGE `dans_livret` `in_livret` ENUM ('0','1');";
    $this->addQuery($sql);
    
    $this->mod_version = "0.06";
  }
}
?>