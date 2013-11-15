<?php 
/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage xds
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * XDS Setup class
 */
class CSetupxds extends CSetup {
  /**
   * @see parent::__construct()
   */
  function __construct() {
    parent::__construct();
    
    $this->mod_name = "xds";

    $this->makeRevision("all");

    $this->makeRevision("0.01");
    $this->addDependency("cda", "0.01");
    $query = "CREATE TABLE `cxds_submissionlot` (
    `cxds_submissionlot_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
                `title` VARCHAR (255),
                `comments` VARCHAR (255),
                `date` DATETIME,
                `type` VARCHAR (255) NOT NULL
              )/*! ENGINE=MyISAM */;";
    $this->addQuery($query);

    $this->makeRevision("0.02");

    $query = "ALTER TABLE `cxds_submissionlot`
                ADD INDEX (`date`);";
    $this->addQuery($query);

    $this->makeRevision("0.03");

    $query = "CREATE TABLE `cxds_submissionlot_document` (
                `cxds_submissionlot_document_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
                `submissionlot_id` INT (11) UNSIGNED,
                `object_id` INT (11) UNSIGNED NOT NULL DEFAULT '0',
                `object_class` ENUM ('CCompteRendu','CFile') NOT NULL
              )/*! ENGINE=MyISAM */;";
    $this->addQuery($query);

    $this->makeRevision("0.04");

    $query = "ALTER TABLE `cxds_submissionlot_document`
                ADD INDEX (`submissionlot_id`),
                ADD INDEX (`object_id`);";
    $this->addQuery($query);

    $this->mod_version = "0.05";
  }
}
