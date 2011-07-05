<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage pharmacie
 *	@version $Revision$
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CSetuppharmacie extends CSetup {

  function __construct() {
    parent::__construct();

    $this->mod_name = 'pharmacie';

    $this->makeRevision('all');
    $this->addDependency("dPstock", "1.26");
		
		$this->makeRevision("0.1");
		$query = "CREATE TABLE `prise_dispensation` (
	              `prise_prevue_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
	              `delivery_id` INT (11) UNSIGNED NOT NULL,
                `datetime` DATETIME NOT NULL,
	              `quantite_adm` FLOAT NOT NULL,
	              `unite_adm` VARCHAR (255) NOT NULL,
								`quantite_disp` INT (11) NOT NULL,
	              `object_id` INT (11) UNSIGNED NOT NULL,
	              `object_class` ENUM ('CPrescriptionLineMixItem','CPrescriptionLineMedicament')
              ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
		
    $query = "ALTER TABLE `prise_dispensation` 
	              ADD INDEX (`delivery_id`),
	              ADD INDEX (`datetime`),
                ADD INDEX (`object_id`),
                ADD INDEX (`object_class`);";
    $this->addQuery($query);
    
    $this->mod_version = '0.11';
  }
}

?>