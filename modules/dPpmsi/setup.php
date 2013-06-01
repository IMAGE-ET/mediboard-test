<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage PMSI
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * Setup du module PMSI
 */
class CSetupdPpmsi extends CSetup {
  /**
   * Standard constructor
   */
  function __construct() {
    parent::__construct();
    
    $this->mod_name = "dPpmsi";
    
    $this->makeRevision("all");
    $this->makeRevision("0.1");
    $query = "CREATE TABLE `ghm` (
      `ghm_id` BIGINT NOT NULL AUTO_INCREMENT ,
      `operation_id` BIGINT NOT NULL ,
      `DR` VARCHAR( 10 ) ,
      `DASs` TEXT,
      `DADs` TEXT,
      PRIMARY KEY ( `ghm_id` ) ,
      INDEX ( `operation_id` )
      ) /*! ENGINE=MyISAM */ COMMENT = 'Table des GHM';";
    $this->addQuery($query);
    
    $this->makeRevision("0.11");
    $this->addDependency("dPplanningOp", "0.38");
    $query = "ALTER TABLE `ghm` ADD `sejour_id` INT NOT NULL AFTER `operation_id`;";
    $this->addQuery($query);
    $query = "UPDATE `ghm`, `operations` SET
      `ghm`.`sejour_id` = `operations`.`sejour_id`
      WHERE `ghm`.`operation_id` = `operations`.`operation_id`";
    $this->addQuery($query);
    
    $this->makeRevision("0.12");
    $query = "ALTER TABLE `ghm` DROP `operation_id` ;";
    $this->addQuery($query);
    $query = "ALTER TABLE `ghm` ADD INDEX ( `sejour_id` ) ;";
    $this->addQuery($query);
    
    $this->makeRevision("0.13");
    $query = "ALTER TABLE `ghm`
      CHANGE `ghm_id` `ghm_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
      CHANGE `sejour_id` `sejour_id` int(11) unsigned NOT NULL DEFAULT '0';";
    $this->addQuery($query);
    
    $this->mod_version = "0.14";

    $query = "SHOW TABLES LIKE 'arbre'";
    $this->addDatasource("GHS1010", $query);
  }
}
