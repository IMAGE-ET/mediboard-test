<?php 

/**
 * $Id$
 *  
 * @category Drawing
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

/** @see CSetup */
class CSetupDrawing extends CSetup {

  /** @see parent::__construct() */
  function __construct() {
    parent::__construct();
    $this->mod_name = "drawing";
    $this->makeRevision("all");

    $this->addDependency("dPfiles", "0.30");

    $this->makeRevision("0.1");
    $query = "CREATE TABLE `drawing_category` (
                `drawing_category_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
                `name` VARCHAR (255) NOT NULL,
                `description` VARCHAR (255),
                `creation_datetime` DATETIME NOT NULL
              )/*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    $query = "ALTER TABLE `drawing_category`
                ADD INDEX (`creation_datetime`);";
    $this->addQuery($query);


    $this->makeRevision("0.2");
    $this->addPrefQuery("drawing_background", "ffffff");

    $this->makeRevision("0.3");
    $this->addPrefQuery("drawing_advanced_mode", 0);

    $this->mod_version = "0.4";
  }
}