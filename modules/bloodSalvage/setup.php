<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage bloodSalvage
 *	@version $Revision: $
 *  @author Alexandre Germonneau
 */

global $AppUI;


class CSetupbloodSalvage extends CSetup {

  function __construct() {
    parent::__construct();

    $this->mod_name = 'bloodSalvage';
    $this->makeRevision('all');
    $sql ='CREATE TABLE `blood_salvage` (
			`blood_salvage_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
			`operation_id` INT (11) UNSIGNED NOT NULL,
			`cell_saver_id` INT (11) UNSIGNED NOT NULL,
			`incident_file_id` INT (11) UNSIGNED,
			`wash_volume` INT (11),
			`saved_volume` INT (11),
			`hgb_pocket` INT (11),
			`hgb_patient` INT (11),
			`transfused_volume`INT(11),
			`anticoagulant_cip` VARCHAR(7),
			`recuperation_start` DATETIME,
			`recuperation_end` DATETIME,
			`transfusion_start` DATETIME,
			`transfusion_end` DATETIME
		) TYPE=MYISAM;';
		$this->addQuery($sql);
		
		$sql='ALTER TABLE `blood_salvage` 
			ADD INDEX (`operation_id`),
			ADD INDEX (`cell_saver_id`),
			ADD INDEX (`incident_file_id`),
			ADD INDEX (`recuperation_start`),
			ADD INDEX (`recuperation_end`),
			ADD INDEX (`transfusion_start`),
			ADD INDEX (`transfusion_end`)
		;';
		$this->addQuery($sql);
		
    $this->mod_version = '0.01';
  }
}
?>