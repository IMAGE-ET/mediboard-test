<?php
/**
 * $Id$
 *
 * @category Files
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

/**
 * Files module setup class
 */
class CSetupdPfiles extends CSetup {

  /**
   * @see parent::__construct()
   */
  function __construct() {
    parent::__construct();
    
    $this->mod_name = "dPfiles";
    
    $this->makeRevision("all");
    $this->setTimeLimit(120);
    if (!$this->ds->loadTable("files_mediboard")) {
      $query = "CREATE TABLE files_mediboard (
                  file_id int(11) NOT NULL auto_increment,
                  file_real_filename varchar(255) NOT NULL default '',
                  file_consultation bigint(20) NOT NULL default '0',
                  file_operation bigint(20) NOT NULL default '0',
                  file_name varchar(255) NOT NULL default '',
                  file_parent int(11) default '0',
                  file_description text,
                  file_type varchar(100) default NULL,
                  file_owner int(11) default '0',
                  file_date datetime default NULL,
                  file_size int(11) default '0',
                  file_version float NOT NULL default '0',
                  file_icon varchar(20) default 'obj/',
                  PRIMARY KEY  (file_id),
                  KEY idx_file_consultation (file_consultation),
                  KEY idx_file_operation (file_operation),
                  KEY idx_file_parent (file_parent)
                ) /*! ENGINE=MyISAM */;";
      $this->addQuery($query);
      $query = "CREATE TABLE files_index_mediboard (
                  file_id int(11) NOT NULL default '0',
                  word varchar(50) NOT NULL default '',
                  word_placement int(11) default '0',
                  PRIMARY KEY  (file_id,word),
                  KEY idx_fwrd (word),
                  KEY idx_wcnt (word_placement)
                ) /*! ENGINE=MyISAM */;";
      $this->addQuery($query);
      $query = "ALTER TABLE `files_mediboard`
                  DROP `file_parent`,
                  DROP `file_description`,
                  DROP `file_version`,
                  DROP `file_icon`;";
      $this->addQuery($query);
      $query = "ALTER TABLE `files_mediboard`
                  ADD `file_object_id` INT(11) NOT NULL DEFAULT '0' AFTER `file_real_filename`,
                  ADD `file_class` VARCHAR(30) NOT NULL DEFAULT 'CPatients' AFTER `file_object_id`;";
      $this->addQuery($query);
      $query = "UPDATE `files_mediboard`
                  SET `file_object_id` = `file_consultation`,
                  `file_class` = 'CConsultation'
                  WHERE `file_consultation` != 0;";
      $this->addQuery($query);
      $query = "UPDATE `files_mediboard`
                  SET `file_object_id` = `file_operation`,
                  `file_class` = 'COperation'
                  WHERE `file_operation` != 0;";
      $this->addQuery($query);
      $query = "ALTER TABLE `files_mediboard`
                  DROP `file_consultation`,
                  DROP `file_operation`;";
      $this->addQuery($query);
      $query = "ALTER TABLE `files_mediboard`
                  ADD INDEX (`file_real_filename`);";
      $this->addQuery($query);
      $query = "ALTER TABLE `files_mediboard`
                  ADD UNIQUE (`file_real_filename`);";
      $this->addQuery($query);
    }
    else {
      $this->addTable("files_mediboard");
      $this->addTable("files_index_mediboard");
    }
    
    $this->makeRevision("0.1");
    $query = "ALTER TABLE `files_mediboard`
                ADD `file_category_id` INT(11) NOT NULL DEFAULT '1' AFTER `file_type`";
    $this->addQuery($query);
    $query = "ALTER TABLE `files_mediboard`
                ADD INDEX (`file_category_id`);";
    $this->addQuery($query);
    $query = "CREATE TABLE `files_category` (
                `file_category_id` INT(11) NOT NULL auto_increment,
                `nom` VARCHAR(50) NOT NULL DEFAULT '',
                `class` VARCHAR(30) DEFAULT NULL,
                PRIMARY KEY (file_category_id)
              ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    $query = "INSERT INTO `files_category` VALUES('1', 'Divers', NULL)";
    $this->addQuery($query);
    
    $this->makeRevision("0.11");
    $query = "ALTER TABLE `files_mediboard`
                CHANGE `file_category_id` `file_category_id` INT( 11 ) NULL ";
    $this->addQuery($query);
    
    $this->makeRevision("0.12");
    $query = "ALTER TABLE `files_mediboard`
                CHANGE `file_category_id` `file_category_id` INT( 11 ) NOT NULL DEFAULT '0'";
    $this->addQuery($query);
    
    $this->makeRevision("0.13");
    $query = "ALTER TABLE `files_mediboard`
                DROP INDEX `file_real_filename`,
                DROP INDEX `file_real_filename_2`,
                ADD UNIQUE ( `file_real_filename` ),
                ADD INDEX ( `file_class` ) ;";
    $this->addQuery($query);
    $query = "ALTER TABLE `files_category`
                ADD INDEX ( `class` ) ;";
    $this->addQuery($query);
    
    $this->makeRevision("0.14");
    $query = "ALTER TABLE `files_mediboard`
                CHANGE `file_id` `file_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                CHANGE `file_object_id` `file_object_id` int(11) unsigned NOT NULL DEFAULT '0',
                CHANGE `file_class` `file_class` varchar(255) NOT NULL DEFAULT 'CPatients',
                CHANGE `file_type` `file_type` varchar(255) NULL,
                CHANGE `file_category_id` `file_category_id` int(11) unsigned NOT NULL DEFAULT '0',
                CHANGE `file_owner` `file_owner` int(11) unsigned NOT NULL DEFAULT '0',
                CHANGE `file_date` `file_date` datetime NOT NULL,
                CHANGE `file_size` `file_size` int(11) unsigned NOT NULL DEFAULT '0';";
    $this->addQuery($query);
    $query = "ALTER TABLE `files_category`
                CHANGE `file_category_id` `file_category_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                CHANGE `nom` `nom` varchar(255) NOT NULL,
                CHANGE `class` `class` varchar(255) NULL;";
    $this->addQuery($query);
    
    $this->makeRevision("0.15");
    $query = "ALTER TABLE `files_mediboard`
                ADD INDEX ( `file_object_id` );";
    $this->addQuery($query);
    
    $this->makeRevision("0.16");
    $query = "ALTER TABLE `files_mediboard`
                CHANGE `file_category_id` `file_category_id` int(11) unsigned NULL DEFAULT NULL;";
    $this->addQuery($query);
    $query = "UPDATE `files_mediboard`
                SET `file_category_id` = NULL WHERE `file_category_id` = '0';";
    $this->addQuery($query);
    $query = "UPDATE `files_mediboard`
                SET `file_owner` = 1 WHERE `file_owner` = '0';";
    $this->addQuery($query);
    
    $this->makeRevision("0.17");
    $query = "ALTER TABLE `files_mediboard`
                CHANGE `file_object_id` `object_id` int(11) unsigned NOT NULL DEFAULT '0',
                CHANGE `file_class` `object_class` varchar(255) NOT NULL DEFAULT 'CPatients';";
    $this->addQuery($query);
    
    $this->makeRevision("0.18");
    $query = "UPDATE `files_category` 
                SET `class` = 'CSejour'
                WHERE `file_category_id` = 3;";
    $this->addQuery($query);

    $this->makeRevision("0.19");
    $query = "ALTER TABLE `files_category` 
                ADD `validation_auto` ENUM ('0','1');";
    $this->addQuery($query);
    
    $this->makeRevision("0.20");
    $query = "ALTER TABLE `files_mediboard` 
                ADD `etat_envoi` ENUM ('oui','non','obsolete') NOT NULL default 'non';";
    $this->addQuery($query);
    
    $this->makeRevision("0.21");
    $query = "ALTER TABLE `files_category` 
                CHANGE `validation_auto` `send_auto` ENUM( '0', '1' ) NOT NULL DEFAULT '0'";
    $this->addQuery($query);
    
    $this->makeRevision("0.22");
    $query = "ALTER TABLE `files_mediboard` 
                ADD INDEX (`file_owner`),
                ADD INDEX (`file_date`);";
    $this->addQuery($query);
    
    $this->makeRevision("0.23");
    $query = "ALTER TABLE `files_mediboard` 
                ADD `private` ENUM ('0','1') NOT NULL DEFAULT '0'";
    $this->addQuery($query);
    
    $this->makeRevision("0.24");
    $query = "ALTER TABLE `files_mediboard`
                ADD `rotate` INT (11) NOT NULL DEFAULT '0'";
    $this->addQuery($query);
    
    $this->makeRevision("0.25");
    $query = "ALTER TABLE `files_mediboard`
                CHANGE `rotate` `rotation` ENUM ('0','90','180','270')";
    $this->addQuery($query);
    
    $this->makeRevision("0.26");
    $this->addPrefQuery("directory_to_watch", "");
    $this->addPrefQuery("debug_yoplet"      , "0");
    $this->addPrefQuery("extensions_yoplet" , "gif jpeg jpg pdf png");
    
    $this->makeRevision("0.27");
    $this->delPrefQuery("extensions_yoplet");
    
    $this->makeRevision("0.28");
    $query = "ALTER TABLE `files_mediboard`
      CHANGE `file_owner` `author_id` INT(11);";
    $this->addQuery($query);

    $this->makeRevision("0.29");
    $query = "ALTER TABLE `files_mediboard`
      ADD `annule` ENUM ('0','1') DEFAULT '0';";
    $this->addQuery($query);

    $this->makeRevision("0.30");
    $query = "ALTER TABLE `files_mediboard`
      CHANGE `rotation` `rotation` INT (11) DEFAULT '0';";
    $this->addQuery($query);

    $query = "UPDATE `files_mediboard`
      SET `rotation` = '0' WHERE `rotation` = '1';";
    $this->addQuery($query);

    $query = "UPDATE `files_mediboard`
      SET `rotation` = '90' WHERE `rotation` = '2';";
    $this->addQuery($query);

    $query = "UPDATE `files_mediboard`
      SET `rotation` = '180' WHERE `rotation` = '3';";
    $this->addQuery($query);

    $query = "UPDATE `files_mediboard`
      SET `rotation` = '270' WHERE `rotation` = '4';";
    $this->addQuery($query);

    $this->makeRevision("0.31");
    $query = "ALTER TABLE `files_mediboard`
      ADD `language` ENUM ('en-EN','es-ES','fr-CH','fr-FR') DEFAULT 'fr-FR' AFTER `file_type`";
    $this->addQuery($query);

    $this->makeRevision("0.32");
    $query = "ALTER TABLE `files_mediboard`
      ADD `type_doc` VARCHAR(128);";
    $this->addQuery($query);

    $this->makeRevision("0.33");
    $this->addPrefQuery("mozaic_disposition", "2x2");

    $this->makeRevision("0.34");
    $query = "ALTER TABLE `files_mediboard`
                ADD `type_doc_sisra` VARCHAR(10);";
    $this->addQuery($query);

    $this->makeRevision("0.35");
    $query = "ALTER TABLE `files_category`
                ADD `eligible_file_view` ENUM ('0','1') NOT NULL DEFAULT '0';";
    $this->addQuery($query);

    $this->makeRevision("0.36");
    $this->addPrefQuery("show_file_view", "0");

    $this->makeRevision("0.37");
    $query = "CREATE TABLE `files_user_view` (
                `view_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
                `user_id` INT (11) UNSIGNED NOT NULL DEFAULT '0',
                `file_id` INT (11) UNSIGNED NOT NULL DEFAULT '0',
                `read_datetime` DATETIME NOT NULL
              )/*! ENGINE=MyISAM */;";
    $this->addQuery($query);

    $query = "ALTER TABLE `files_user_view`
                ADD INDEX (`user_id`),
                ADD INDEX (`file_id`),
                ADD INDEX (`read_datetime`);";
    $this->addQuery($query);

    $this->makeRevision("0.38");
    $query = "ALTER TABLE `files_mediboard`
      CHANGE `file_size` `doc_size` int(11) unsigned DEFAULT '0',
      ADD `compression` VARCHAR (255),
      ADD INDEX(`compression`),
      ADD INDEX(`compression`, `object_class`);";
    $this->addQuery($query);

    $this->makeRevision("0.39");
    $this->addPrefQuery("upload_mbhost", "0");

    $this->mod_version = "0.40";
  }
}
