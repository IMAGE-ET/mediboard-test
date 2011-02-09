<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CSetupsystem extends CSetup {
  
  function __construct() {
    parent::__construct();
    
    $this->mod_type = "core";
    $this->mod_name = "system";
    
    $this->makeRevision("all");
    
    $this->makeRevision("1.0.00");
    $query = "CREATE TABLE `access_log` (
		  `accesslog_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
			`module` VARCHAR( 40 ) NOT NULL ,
			`action` VARCHAR( 40 ) NOT NULL ,
			`period` DATETIME NOT NULL ,
			`hits` TINYINT DEFAULT '0' NOT NULL ,
			`duration` DOUBLE NOT NULL,
			PRIMARY KEY ( `accesslog_id` )) /*! ENGINE=MyISAM */";
    $this->addQuery($query);
    $query = "ALTER TABLE `access_log` 
		  ADD UNIQUE `triplet` (`module` , `action` , `period`)";
    $this->addQuery($query);
    $query = "ALTER TABLE `access_log`
		   ADD INDEX ( `module` )";
    $this->addQuery($query);
    $query = "ALTER TABLE `access_log` 
		  ADD INDEX ( `action` )";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.01");
    $query = "ALTER TABLE `access_log` CHANGE `
		  hits` `hits` INT UNSIGNED DEFAULT '0' NOT NULL ";
    $this->addQuery($query);
    $query = "ALTER TABLE `access_log` 
		  ADD `request` DOUBLE NOT NULL ;";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.02");
    $query = "ALTER TABLE `access_log` 
		  DROP INDEX `action_2` ;";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.03");
    $this->setTimeLimit(300);
    $query = "ALTER TABLE `user_log` 
		  CHANGE `type` `type` ENUM( 'create', 'store', 'delete' ) NOT NULL; ";
    $this->addQuery($query);
    $query = "ALTER TABLE `user_log` 
		  ADD `fields` TEXT;";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.04");
    $this->setTimeLimit(300);
    $query = "ALTER TABLE `access_log`
		  CHANGE `accesslog_id` `accesslog_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			CHANGE `module` `module` VARCHAR(255) NOT NULL,
			CHANGE `action` `action` VARCHAR(255) NOT NULL,
			CHANGE `hits` `hits` int(11) unsigned NOT NULL DEFAULT '0',
			CHANGE `duration` `duration` float NOT NULL DEFAULT '0',
			CHANGE `request` `request` float NOT NULL DEFAULT '0'; ";
    $this->addQuery($query);
    $query = "ALTER TABLE `message` 
		  CHANGE `message_id` `message_id` int(11) unsigned NOT NULL AUTO_INCREMENT; ";
    $this->addQuery($query);
    $query = "ALTER TABLE `user_log`
		  CHANGE `user_log_id` `user_log_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			CHANGE `user_id` `user_id` int(11) unsigned NOT NULL DEFAULT '0',
			CHANGE `object_id` `object_id` int(11) unsigned NOT NULL DEFAULT '0'; ";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.05");
    $this->setTimeLimit(300);
    $query = "DELETE FROM `user_log` 
		  WHERE `object_id` = '0'";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.06");
    $query = "CREATE TABLE `note` (
      `note_id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
      `user_id` INT( 11 ) UNSIGNED NOT NULL ,
      `object_id` INT( 11 ) UNSIGNED NOT NULL ,
      `object_class` VARCHAR( 25 ) NOT NULL ,
      `public` ENUM('0','1') NOT NULL DEFAULT '0',
      `degre` ENUM('low','high') NOT NULL DEFAULT 'low',
      `date` DATETIME NOT NULL ,
      `libelle` VARCHAR( 255 ) NOT NULL ,
      `text` TEXT NULL ,
      INDEX ( `user_id` , `object_id` , `object_class` , `public` , `degre` , `date` )
    ) /*! ENGINE=MyISAM */ COMMENT = 'Table des notes sur les objets';";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.07");
    $this->addPrefQuery("MenuPosition", "top");
    
    $this->makeRevision("1.0.08");
    $query = "ALTER TABLE `message`
      ADD `urgence` ENUM('normal','urgent') DEFAULT 'normal' NOT NULL;";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.09");
    $query = "ALTER TABLE `message`
	    ADD `module_id` INT(11) UNSIGNED;";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.10");
    $query = "ALTER TABLE `user_log`
      CHANGE `object_class` `object_class` VARCHAR(255) NOT NULL;";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.11");
    $query = "ALTER TABLE `user_log` 
			CHANGE `type` `type` ENUM ('create','store','merge','delete') NOT NULL,
			ADD INDEX (`date`);";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.12");
    $query = "ALTER TABLE `access_log` 
			ADD `size` INT (11) UNSIGNED,
			ADD `errors` INT (11) UNSIGNED,
			ADD `warnings` INT (11)  UNSIGNED,
			ADD `notices` INT (11) UNSIGNED;";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.13");
    $this->addPrefQuery("touchscreen", "0");
    
    $this->makeRevision("1.0.14");
    $this->addPrefQuery("tooltipAppearenceTimeout", "medium");
    
    $this->makeRevision("1.0.15");
    $this->addPrefQuery("showLastUpdate", "0");

    $this->makeRevision("1.0.16");
    $query = "ALTER TABLE `message` 
      ADD INDEX (`module_id`),
      ADD INDEX (`deb`),
      ADD INDEX (`fin`);";
    $this->addQuery($query);
    $query = "ALTER TABLE `modules` 
      DROP `mod_directory`,
      DROP `mod_setup_class`,
      DROP `mod_ui_name`,
      DROP `mod_ui_icon`,
      DROP `mod_description`";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.17");
    $this->addPrefQuery("showTemplateSpans", "0");

    $this->makeRevision("1.0.18");
    $query = "ALTER TABLE `message` 
      ADD `group_id` INT (11) UNSIGNED,
      ADD INDEX (`group_id`);";
    $this->addQuery($query);

    $this->makeRevision("1.0.19");
    $this->setTimeLimit(300);
    $query = "ALTER TABLE `user_log` 
      ADD `ip_address` VARBINARY (16) NULL DEFAULT NULL,
      ADD `extra` TEXT,
      ADD INDEX (`ip_address`);";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.20");
    $query = "CREATE TABLE `alert` (
	    `alert_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
	    `tag` VARCHAR (255) NOT NULL,
	    `level` ENUM ('low','medium','high') NOT NULL DEFAULT 'medium',
	    `comments` TEXT,
	    `handled` ENUM ('0','1') NOT NULL DEFAULT '0',
	    `object_id` INT (11) UNSIGNED NOT NULL,
	    `object_class` VARCHAR (255) NOT NULL
	  ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    $query = "ALTER TABLE `alert` 
      ADD INDEX (`object_id`),
      ADD INDEX (`object_class`),
      ADD INDEX (`tag`);";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.21");
    $query = "UPDATE user_preferences 
		  SET value = 'e-cap' 
			WHERE value = 'tonkin';";
    $this->addQuery($query);
    $query = "UPDATE user_preferences 
		  SET value = 'e-cap' 
			WHERE value = 'K-Open';";
    $this->addQuery($query);
    $query = "UPDATE user_preferences 
		  SET value = 'mediboard' 
			WHERE value = 'mediboard_lite';";
    $this->addQuery($query);
    $query = "UPDATE user_preferences 
		  SET value = 'mediboard' 
			WHERE value = 'mediboard_super_lite';";
    $this->addQuery($query);    
    		
    $this->makeRevision("1.0.26");
    $query = "DELETE FROM `modules` 
		  WHERE `mod_name` = 'dPinterop'";
    $this->addQuery($query, true);
    
		$this->makeRevision("1.0.27");
    $query = "DELETE FROM `modules` 
		  WHERE `mod_name` = 'dPmateriel'";
    $this->addQuery($query, true);
		
    $this->makeRevision("1.0.28");
    $query = "CREATE TABLE IF NOT EXISTS `content_html` (
		  `content_id` BIGINT NOT NULL auto_increment PRIMARY KEY,
		  `content` TEXT,
		  `cr_id` INT
		) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $query = "CREATE TABLE `content_xml` (
		  `content_id` BIGINT NOT NULL auto_increment PRIMARY KEY,
		  `content` TEXT,
		  `import_id` INT
		) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.29");
    $query = "ALTER TABLE `content_html`
      CHANGE `content` `content` mediumtext NULL";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.30");
    $this->addPrefQuery("directory_to_watch", "");
    
    $this->makeRevision("1.0.31");
    $this->addPrefQuery("debug_yoplet", "0");
    
    $this->makeRevision("1.0.32");
    $query = "ALTER TABLE `access_log` 
		  ADD INDEX ( `period` )";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.34");
    $query = "CREATE TABLE `source_smtp` (
	    `source_smtp_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
	    `port` INT (11) DEFAULT '25',
	    `email` VARCHAR (50),
	    `ssl` ENUM ('0','1') DEFAULT '0',
	    `name` VARCHAR  (255) NOT NULL,
	    `role` ENUM ('prod','qualif') NOT NULL DEFAULT 'qualif',
	    `host` TEXT NOT NULL,
	    `user` VARCHAR  (255),
	    `password` VARCHAR (50),
	    `type_echange` VARCHAR  (255)
    ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.35");
    $query = "CREATE TABLE `ex_class` (
      `host_class` VARCHAR (255) NOT NULL,
      `event` VARCHAR (255) NOT NULL,
      `ex_class_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY
    ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    $query = "CREATE TABLE `ex_class_field` (
      `ex_class_field_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
      `ex_class_id` INT (11) UNSIGNED NOT NULL,
      `name` VARCHAR (255) NOT NULL,
      `prop` VARCHAR (255) NOT NULL
    ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    $query = "ALTER TABLE `ex_class_field` 
              ADD INDEX (`ex_class_id`);";
    $this->addQuery($query);
    $query = "CREATE TABLE `ex_class_constraint` (
      `ex_class_constraint_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
      `ex_class_id` INT (11) UNSIGNED NOT NULL,
      `field` VARCHAR  (255) NOT NULL,
      `operator` ENUM ('=','!=','>','>=','<','<=','startsWith','endsWith','contains') NOT NULL DEFAULT '=',
      `value` VARCHAR  (255) NOT NULL
    ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    $query = "ALTER TABLE `ex_class_constraint` 
      ADD INDEX (`ex_class_id`);";
    $this->addQuery($query);
    $query = "CREATE TABLE `ex_class_field_translation` (
      `ex_class_field_translation_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
      `ex_class_field_id` INT (11) UNSIGNED NOT NULL,
      `lang` CHAR  (2),
      `std` VARCHAR  (255),
      `desc` VARCHAR  (255),
      `court` VARCHAR  (255)
    ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    $query = "ALTER TABLE `ex_class_field_translation` 
      ADD INDEX (`ex_class_field_id`);";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.36");
    $query = "CREATE TABLE `ex_class_field_enum_translation` (
      `ex_class_field_enum_translation_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
      `ex_class_field_id` INT (11) UNSIGNED NOT NULL,
      `lang` CHAR  (2),
      `key` VARCHAR  (40),
      `value` VARCHAR  (255)
    ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    $query = "ALTER TABLE `ex_class_field_enum_translation` 
	    ADD INDEX (`ex_class_field_id`),
	    ADD INDEX (`lang`),
	    ADD INDEX (`key`);";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.37");
    $query = "ALTER TABLE `ex_class` 
      ADD `name` VARCHAR  (255) NOT NULL;";
    $this->addQuery($query);
    $query = "ALTER TABLE `ex_class_field_translation` 
      ADD INDEX (`lang`)";
    $this->addQuery($query);
		
    $this->makeRevision("1.0.38");
    $query = "ALTER TABLE `ex_class_field` 
      ADD `coord_field_x` TINYINT (4) UNSIGNED,
      ADD `coord_field_y` TINYINT (4) UNSIGNED,
      ADD `coord_label_x` TINYINT (4) UNSIGNED,
      ADD `coord_label_y` TINYINT (4) UNSIGNED;";
    $this->addQuery($query);
		
		$this->makeRevision("1.0.39");
		$query = "CREATE TABLE `ex_class_host_field` (
	    `ex_class_host_field_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
	    `ex_class_id` INT (11) UNSIGNED NOT NULL,
	    `field` VARCHAR (80) NOT NULL,
	    `coord_label_x` TINYINT (4) UNSIGNED,
	    `coord_label_y` TINYINT (4) UNSIGNED,
	    `coord_value_x` TINYINT (4) UNSIGNED,
	    `coord_value_y` TINYINT (4) UNSIGNED
	    ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    $query = "ALTER TABLE `ex_class_host_field` 
      ADD INDEX (`ex_class_id`);";
    $this->addQuery($query);
		
		$this->makeRevision("1.0.40");
		$query = "ALTER TABLE `ex_class_field` 
      CHANGE `ex_class_id` `ex_class_id` INT (11) UNSIGNED,
      ADD `concept_id` INT (11) UNSIGNED;";
    $this->addQuery($query);
    $query = "ALTER TABLE `ex_class_field` 
      ADD INDEX (`concept_id`);";
    $this->addQuery($query);
		
		$this->makeRevision("1.0.41");
		$query = "ALTER TABLE `ex_class` 
      ADD `disabled` ENUM ('0','1') NOT NULL DEFAULT '1';";
    $this->addQuery($query);
      
    $this->makeRevision("1.0.42");
    $query = "CREATE TABLE `content_tabular` (
      `content_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
      `content` TEXT,
      `import_id` INT (11),
      `separator` CHAR (1)
    ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $this->mod_version = "1.0.43";
  }
}
?>