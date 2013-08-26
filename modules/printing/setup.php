<?php 
/**
 * Setup PRINTING
 *  
 * @category PRINTING
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Printing module setup class
 */
class CSetupprinting extends CSetup {
  /**
   * @see parent::__construct()
   */
  function __construct() {
    parent::__construct();
  
    $this->mod_name = "printing";
    $this->makeRevision("all");
      
    $query = "CREATE TABLE `source_lpr` (
      `source_lpr_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
      `name` VARCHAR (255) NOT NULL,
      `object_id` BIGINT DEFAULT NULL,
      `object_class` VARCHAR(30) DEFAULT NULL,
      `host` TEXT NOT NULL,
      `port` INT (11) DEFAULT NULL,
      `user` VARCHAR (255),
      `printer_name` VARCHAR (50)
    ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $this->makeRevision("0.01");
    $query = "CREATE TABLE `source_smb` (
      `source_smb_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
      `name` VARCHAR (255) NOT NULL,
      `object_id` BIGINT DEFAULT NULL,
      `object_class` VARCHAR(30) DEFAULT NULL,
      `host` TEXT NOT NULL,
      `port` INT (11) DEFAULT NULL,
      `user` VARCHAR (255),
      `password` VARCHAR (50),
      `workgroup` VARCHAR (50), 
      `printer_name` VARCHAR (50)
    ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $this->makeRevision("0.02");
    $query = "CREATE TABLE IF NOT EXISTS `printer` (
      `printer_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
      `function_id` INT (11) DEFAULT NULL,
      `object_id` INT (11) DEFAULT NULL,
      `object_class` VARCHAR (255) DEFAULT NULL
    ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $this->mod_version = "0.03";
  }
}
