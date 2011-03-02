<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage soins
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CSetupsoins extends CSetup {

  function __construct() {
    parent::__construct();

    $this->mod_name = 'soins';

    $this->makeRevision('all');
		
		$this->makeRevision("0.1");
		
		$query = "CREATE TABLE `sejour_task` (
	    `sejour_task_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
	    `sejour_id` INT (11) UNSIGNED NOT NULL,
	    `description` TEXT NOT NULL,
	    `realise` ENUM ('0','1') DEFAULT '0',
	    `resultat` TEXT
			) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
		
		$query = "ALTER TABLE `sejour_task` ADD INDEX (`sejour_id`);";
		$this->addQuery($query);	
						
    $this->mod_version = '0.11';
		
  }
}

?>