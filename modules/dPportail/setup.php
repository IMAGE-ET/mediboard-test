<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Portail
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * Portail setup
 */
class CSetupdPportail extends CSetup {
  /**
   * @see parent::__construct()
   */
  function __construct() {
    parent::__construct();

    $this->mod_name = 'dPportail';
    $this->makeRevision('all');

    $query = 'CREATE TABLE `forum_theme` (
              `forum_theme_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT, 
              `title` TEXT NOT NULL, 
              `desc` TEXT, 
              PRIMARY KEY (`forum_theme_id`)) /*! ENGINE=MyISAM */;';
    $this->addQuery($query);
    
    $query = 'CREATE TABLE `forum_message` (
              `forum_message_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT, 
              `forum_thread_id` INT(11) UNSIGNED NOT NULL, 
              `body` TEXT NOT NULL, 
              `date` DATETIME NOT NULL, 
              `user_id` INT(11) UNSIGNED NOT NULL,
              PRIMARY KEY (`forum_message_id`)) /*! ENGINE=MyISAM */;';
    $this->addQuery($query);
    
    $query = 'CREATE TABLE `forum_thread` (
              `forum_thread_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT, 
              `forum_theme_id` INT(11) UNSIGNED NOT NULL, 
              `title` TEXT NOT NULL, 
              `body` TEXT NOT NULL, 
              `date` DATETIME NOT NULL, 
              `user_id` INT(11) UNSIGNED NOT NULL,
              PRIMARY KEY (`forum_thread_id`)) /*! ENGINE=MyISAM */;';
    $this->addQuery($query);
    
    $this->makeRevision("0.10");
    $query = "ALTER TABLE `forum_message` 
              ADD INDEX (`date`),
              ADD INDEX (`user_id`),
              ADD INDEX (`forum_thread_id`);";
    $this->addQuery($query);
    $query = "ALTER TABLE `forum_thread` 
              ADD INDEX (`forum_theme_id`),
              ADD INDEX (`date`),
              ADD INDEX (`user_id`);";
    $this->addQuery($query);
    
    $this->mod_version = '0.11';
  }
}
