<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dmi
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CSetupdmi extends CSetup {
  
  function __construct() {
    parent::__construct();
    
    $this->mod_name = "dmi";
    
    $this->makeRevision("all");
    $query = "CREATE TABLE `dmi_category` (
			`category_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
			`nom` VARCHAR (255) NOT NULL,
			`description` VARCHAR (255)
		) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $this->makeRevision("0.01");
    $query = "ALTER TABLE `dmi_category` 
			CHANGE `description` `description` TEXT;";
    $this->addQuery($query);
    
    $this->makeRevision("0.02");
    $query = "ALTER TABLE `dmi_category` 
			ADD `group_id` INT (11) UNSIGNED NOT NULL;";
    $this->addQuery($query);

    $query = "ALTER TABLE `dmi_category`
			ADD INDEX (`group_id`);";
    $this->addQuery($query);
    
    $this->makeRevision("0.03");
    $query = "CREATE TABLE `dmi` (
		`dmi_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
		`nom` VARCHAR (255) NOT NULL,
		`en_lot` ENUM ('0','1'),
		`description` TEXT,
		`reference` VARCHAR (255) NOT NULL,
		`lot` VARCHAR (255),
		`dans_livret` ENUM ('0','1'),
		`category_id` INT (11) UNSIGNED NOT NULL
		) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);

    $query = "ALTER TABLE `dmi` 
	  ADD INDEX (`category_id`);";
    $this->addQuery($query);
    
    $this->makeRevision("0.04");
    $query = "ALTER TABLE `dmi` 
    CHANGE `reference` `code` VARCHAR (255) NOT NULL,
    DROP `en_lot`,
    DROP `lot`;";
    $this->addQuery($query);
    
    $this->makeRevision("0.05");
    $query = "CREATE TABLE `dm` (
							`dm_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
							`category_dm_id` INT (11) UNSIGNED NOT NULL,
							`nom` VARCHAR (255) NOT NULL,
							`description` TEXT,
							`code` VARCHAR (255) NOT NULL,
							`in_livret` ENUM ('0','1')
						) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $query = "CREATE TABLE `category_dm` (
							`category_dm_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
							`nom` VARCHAR (255) NOT NULL,
							`description` TEXT,
							`group_id` INT (11) UNSIGNED NOT NULL
						) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `dm` 
						ADD INDEX (`category_dm_id`);";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `category_dm` 
			      ADD INDEX (`group_id`);";
    $this->addQuery($query);

    $query = "ALTER TABLE `dmi` 
	          CHANGE `dans_livret` `in_livret` ENUM ('0','1');";
    $this->addQuery($query);
    
    $this->makeRevision("0.06");
    $query = "ALTER TABLE `dmi` 
              ADD `code_lpp` VARCHAR (255),
              CHANGE `in_livret` `in_livret` ENUM ('0','1') DEFAULT '0';";
    $this->addQuery($query);
    $query = "ALTER TABLE `dmi` 
              ADD INDEX (`code_lpp`),
              ADD INDEX (`code`);";
    $this->addQuery($query);
    
    $this->makeRevision("0.07");
    $query = "ALTER TABLE `dmi` ADD `type` ENUM ('purchase','loan','deposit') NOT NULL DEFAULT 'purchase'";
    $this->addQuery($query);
    
    $this->makeRevision("0.08");
    $query = "ALTER TABLE `dmi` CHANGE `type` `type` ENUM ('purchase','loan','deposit') NOT NULL DEFAULT 'deposit'";
    $this->addQuery($query);
    
    $this->addDependency("dPstock", "1.0");
    
    // ajout du product ID pour faire un lien "dur" plutot que par le code
    $this->makeRevision("0.09");
    $query = "ALTER TABLE `dmi` ADD `product_id` INT (11) UNSIGNED;";
    $this->addQuery($query);
    $query = "ALTER TABLE `dmi` ADD INDEX (`product_id`)";
    $this->addQuery($query);
    $query = "UPDATE `dmi` SET `product_id` = (SELECT `product`.`product_id` FROM `product` WHERE `product`.`code` = `dmi`.`code` LIMIT 1)";
    $this->addQuery($query);
    
    $this->mod_version = "0.10";
  }
}
?>