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

    $this->mod_version = "0.04";
  }
}
