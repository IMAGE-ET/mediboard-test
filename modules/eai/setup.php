<?php 
/**
 * Setup EAI
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

class CSetupeai extends CSetup {
  
  function __construct() {
    parent::__construct();
    
    $this->mod_name = "eai";
    $this->makeRevision("all");
    $this->makeRevision("0.01");
    
    $sql = "CREATE TABLE `message_supported` (
              `message_supported_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
              `object_id` INT (11) UNSIGNED NOT NULL,
              `object_class` VARCHAR (80) NOT NULL,
              `message` VARCHAR (255) NOT NULL,
              `active` ENUM ('0','1') DEFAULT '0'
            ) /*! ENGINE=MyISAM */;";
    $this->addQuery($sql);
    
    $sql = "ALTER TABLE `message_supported` 
              ADD INDEX (`object_id`),
              ADD INDEX (`object_class`);";
    $this->addQuery($sql);
    
    $this->mod_version = "0.02";
  }
}

?>