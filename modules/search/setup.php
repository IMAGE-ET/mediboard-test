<?php 
/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage search
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * search Setup class
 */
class CSetupsearch extends CSetup {
  /**
   * @see parent::__construct()
   */
  function __construct() {
    parent::__construct();
    
    $this->mod_name = "search";
    $this->makeRevision("all");

    $this->makeRevision("0.01");
    $query = "CREATE TABLE `search_indexing` (
              `search_indexing_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
              `object_class` VARCHAR (50) NOT NULL,
              `object_id` INT (11) UNSIGNED NOT NULL,
              `type` ENUM('create','store','delete','merge') NOT NULL DEFAULT 'create',
              `date` DATETIME NOT NULL
              ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);

    $this->makeRevision("0.02");
    $query = "ALTER TABLE `search_indexing`
              CHANGE `object_class` `object_class` CHAR(50) NOT NULL;";
    $this->addQuery($query);

    $this->makeRevision("0.03");
    $query = "ALTER TABLE `search_indexing`
              ADD INDEX `index_order` (`object_class`, `type`, `search_indexing_id`);";
    $this->addQuery($query);

   
    $this->makeRevision("0.04");
    $query = "CREATE TABLE `search_thesaurus_entry` (
              `search_thesaurus_entry_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
              `entry` TEXT NOT NULL,
              `types` TEXT,
              `titre` VARCHAR (255),
              `contextes` ENUM('generique','pharmacie','pmsi','bloc') NOT NULL DEFAULT 'generique',
              `agregation` ENUM('0','1') NOT NULL DEFAULT '0',
              `group_id` INT(11) UNSIGNED ,
              `function_id` INT(11) UNSIGNED ,
              `user_id` INT(11) UNSIGNED
              ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);

    $this->makeRevision("0.05");
    $query =  "
        CREATE TABLE IF NOT EXISTS `rss_search_items` (
          `rss_search_item_id` BIGINT NOT NULL AUTO_INCREMENT ,
          `rss_id` BIGINT NOT NULL ,
          `search_id` BIGINT NOT NULL,
          `search_class` char(40),
          `rmq` text,
          PRIMARY KEY ( `rss_search_item_id` ) ,
          INDEX ( `rss_id`, `search_id`, `search_class`)
          ) /*! ENGINE=MyISAM */ COMMENT = 'Table des Search Items';";
    $this->addQuery($query);

    $this->mod_version = "0.06";
  }
}