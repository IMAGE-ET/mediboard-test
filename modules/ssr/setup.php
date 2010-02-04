<?php /* $Id: setup.php 6144 2009-04-21 14:22:50Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage soins
 * @version $Revision: 6144 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CSetupssr extends CSetup {

  function __construct() {
    parent::__construct();

    $this->mod_name = "ssr";

    $this->makeRevision("all");
    
		// Plateau technique
    $this->makeRevision("0.01");
		$query = "CREATE TABLE `plateau_technique` (
      `plateau_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
      `group_id` INT (11) UNSIGNED,
      `nom` VARCHAR (255) NOT NULL
    ) TYPE=MYISAM;";
    $this->addQuery($query);
		$query = "ALTER TABLE `plateau_technique` 
      ADD INDEX (`group_id`);";
		$this->addQuery($query);

    // Equipement
    $this->makeRevision("0.02");
    $query = "CREATE TABLE `equipement` (
	    `equipement_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
	    `plateau_id` INT (11) UNSIGNED NOT NULL,
	    `nom` VARCHAR (255) NOT NULL
    ) TYPE=MYISAM;";
    $this->addQuery($query);
    $query = "ALTER TABLE `equipement` 
      ADD INDEX (`plateau_id`);";
    $this->addQuery($query);

    // Technicien
    $this->makeRevision("0.03");
    $this->addDependency("mediusers", "0.1");
    $query = "CREATE TABLE `technicien` (
      `technicien_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
      `plateau_id` INT (11) UNSIGNED NOT NULL,
      `kine_id` INT (11) UNSIGNED NOT NULL
    ) TYPE=MYISAM;";
    $this->addQuery($query);
    $query = "ALTER TABLE `technicien` 
      ADD INDEX (`plateau_id`),
      ADD INDEX (`kine_id`);";
    $this->addQuery($query);

    $this->mod_version = "0.04";
  }
}

?>