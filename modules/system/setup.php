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
    
    $this->makeRevision("1.0.0");
    $query = "CREATE TABLE `access_log` (
		  `accesslog_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
			`module` VARCHAR( 40 ) NOT NULL ,
			`action` VARCHAR( 40 ) NOT NULL ,
			`period` DATETIME NOT NULL ,
			`hits` TINYINT DEFAULT '0' NOT NULL ,
			`duration` DOUBLE NOT NULL,
			PRIMARY KEY ( `accesslog_id` )) TYPE=MyISAM";
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
    
    $this->makeRevision("1.0.1");
    $query = "ALTER TABLE `access_log` CHANGE `
		  hits` `hits` INT UNSIGNED DEFAULT '0' NOT NULL ";
    $this->addQuery($query);
    $query = "ALTER TABLE `access_log` 
		  ADD `request` DOUBLE NOT NULL ;";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.2");
    $query = "ALTER TABLE `access_log` 
		  DROP INDEX `action_2` ;";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.3");
    $this->setTimeLimit(300);
    $query = "ALTER TABLE `user_log` 
		  CHANGE `type` `type` ENUM( 'create', 'store', 'delete' ) NOT NULL; ";
    $this->addQuery($query);
    $query = "ALTER TABLE `user_log` 
		  ADD `fields` TEXT;";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.4");
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
    
    $this->makeRevision("1.0.5");
    $this->setTimeLimit(300);
    $query = "DELETE FROM `user_log` 
		  WHERE `object_id` = '0'";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.6");
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
    ) ENGINE = MYISAM COMMENT = 'Table des notes sur les objets';";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.7");
    $this->addPrefQuery("MenuPosition", "top");
    
    $this->makeRevision("1.0.8");
    $query = "ALTER TABLE `message`
      ADD `urgence` ENUM('normal','urgent') DEFAULT 'normal' NOT NULL;";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.9");
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
	  ) TYPE=MYISAM;";
    $this->addQuery($query);
    $query = "ALTER TABLE `alert` 
      ADD INDEX (`object_id`),
      ADD INDEX (`object_class`),
      ADD INDEX (`tag`);";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.21");
    $query = "UPDATE user_preferences 
		  SET pref_value = 'e-cap' 
			WHERE pref_value = 'tonkin';";
    $this->addQuery($query);
    $query = "UPDATE user_preferences 
		  SET pref_value = 'e-cap' 
			WHERE pref_value = 'K-Open';";
    $this->addQuery($query);
    $query = "UPDATE user_preferences 
		  SET pref_value = 'mediboard' 
			WHERE pref_value = 'mediboard_lite';";
    $this->addQuery($query);
    $query = "UPDATE user_preferences 
		  SET pref_value = 'mediboard' 
			WHERE pref_value = 'mediboard_super_lite';";
    $this->addQuery($query);

    $this->makeRevision("1.0.22");
    $query = "CREATE TABLE `source_ftp` (
      `source_ftp_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
      `port` INT (11) DEFAULT '21',
      `timeout` INT (11) DEFAULT '90',
      `pasv` ENUM ('0','1') DEFAULT '0',
      `mode` ENUM ('FTP_ASCII','FTP_BINARY') DEFAULT 'FTP_ASCII',
      `fileprefix` VARCHAR (255),
      `fileextension` VARCHAR (255),
      `filenbroll` ENUM ('1','2','3','4'),
      `fileextension_write_end` VARCHAR (255),
      `counter` VARCHAR (255),
      `name` VARCHAR (255) NOT NULL,
      `host` TEXT NOT NULL,
      `user` VARCHAR (255),
      `password` VARCHAR (50)
    ) TYPE=MYISAM;";
    $this->addQuery($query);
    
    $query = "CREATE TABLE `source_soap` (
      `source_soap_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
      `wsdl_mode` ENUM ('0','1') DEFAULT '1',
      `name` VARCHAR (255) NOT NULL,
      `host` TEXT NOT NULL,
      `user` VARCHAR (255),
      `password` VARCHAR (50)
    ) TYPE=MYISAM;";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.23");
    $query = "ALTER TABLE `user_preferences` DROP PRIMARY KEY;";
    $this->addQuery($query, true);
    $query = "ALTER TABLE `user_preferences` 
      ADD `pref_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
      CHANGE `pref_user` `user_id` INT (11) UNSIGNED NOT NULL DEFAULT '0',
      CHANGE `pref_name` `key` VARCHAR (40) NOT NULL,
      CHANGE `pref_value` `value` VARCHAR (255);";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.24");
    $query = "ALTER TABLE `source_ftp` 
      ADD `role` ENUM ('prod','qualif') NOT NULL DEFAULT 'qualif';";
    $this->addQuery($query);
    $query = "ALTER TABLE `source_soap` 
      ADD `role` ENUM ('prod','qualif') NOT NULL DEFAULT 'qualif';";
    $this->addQuery($query);
		
		$this->makeRevision("1.0.25");
    $query = "ALTER TABLE `source_soap` 
      ADD `evenement_name` VARCHAR (255),
      ADD `type_echange` VARCHAR (255);";
    $this->addQuery($query);
    $query = "ALTER TABLE `source_ftp` 
      ADD `type_echange` VARCHAR (255);";
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
		) TYPE=MYISAM;";
    $this->addQuery($query);
    
    $query = "CREATE TABLE `content_xml` (
		  `content_id` BIGINT NOT NULL auto_increment PRIMARY KEY,
		  `content` TEXT,
		  `import_id` INT
		) TYPE=MYISAM;";
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
    
    $this->makeRevision("1.0.33");
    $query = "ALTER TABLE `source_soap` 
                ADD `single_parameter` VARCHAR (255);";
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
              ) TYPE=MYISAM;";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.35");
    $sql = "CREATE TABLE `ex_class` (
              `host_class` VARCHAR (255) NOT NULL,
              `event` VARCHAR (255) NOT NULL,
              `ex_class_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY
            ) TYPE=MYISAM;";
    $this->addQuery($sql);
    $sql = "CREATE TABLE `ex_class_field` (
              `ex_class_field_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
              `ex_class_id` INT (11) UNSIGNED NOT NULL,
              `name` VARCHAR (255) NOT NULL,
              `prop` VARCHAR (255) NOT NULL
            ) TYPE=MYISAM;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `ex_class_field` 
              ADD INDEX (`ex_class_id`);";
    $this->addQuery($sql);
    $sql = "CREATE TABLE `ex_class_constraint` (
              `ex_class_constraint_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
              `ex_class_id` INT (11) UNSIGNED NOT NULL,
              `field` VARCHAR  (255) NOT NULL,
              `operator` ENUM ('=','!=','>','>=','<','<=','startsWith','endsWith','contains') NOT NULL DEFAULT '=',
              `value` VARCHAR  (255) NOT NULL
            ) TYPE=MYISAM;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `ex_class_constraint` 
              ADD INDEX (`ex_class_id`);";
    $this->addQuery($sql);
    $sql = "CREATE TABLE `ex_class_field_translation` (
              `ex_class_field_translation_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
              `ex_class_field_id` INT (11) UNSIGNED NOT NULL,
              `lang` CHAR  (2),
              `std` VARCHAR  (255),
              `desc` VARCHAR  (255),
              `court` VARCHAR  (255)
            ) TYPE=MYISAM;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `ex_class_field_translation` 
              ADD INDEX (`ex_class_field_id`);";
    $this->addQuery($sql);
    
    $this->mod_version = "1.0.36";
    
  }
}
?>