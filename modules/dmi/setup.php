<?php /* $Id: setup.php 5173 2008-11-04 14:53:07Z mytto $ */

/**
 * @package Mediboard
 * @subpackage dPcabinet
 * @version $Revision: 5173 $
 * @author Romain Ollivier
 */

class CSetupdmi extends CSetup {
  
  function __construct() {
    parent::__construct();
    
    $this->mod_name = "dmi";
    
    $this->makeRevision("all");
    $sql = "CREATE TABLE `dmi_category` (
			`category_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
			`nom` VARCHAR (255) NOT NULL,
			`description` VARCHAR (255)
		) TYPE=MYISAM;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.01");
    $sql = "ALTER TABLE `dmi_category` 
			CHANGE `description` `description` TEXT;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.02");
    $sql = "ALTER TABLE `dmi_category` 
			ADD `group_id` INT (11) UNSIGNED NOT NULL;";
    $this->addQuery($sql);

    $sql = "ALTER TABLE `dmi_category`
			ADD INDEX (`group_id`);";
    $this->addQuery($sql);
    
    $this->mod_version = "0.03";
  }
}
?>