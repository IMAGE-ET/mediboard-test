<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage mediusers
* @version $Revision$
* @author Romain Ollivier
*/

// MODULE CONFIGURATION DEFINITION
$config = array();
$config['mod_name'] = 'Mediusers';
$config['mod_version'] = '0.13';
$config['mod_directory'] = 'mediusers';
$config['mod_setup_class'] = 'CSetupMediusers';
$config['mod_type'] = 'user';
$config['mod_ui_name'] = 'Mediusers';
$config['mod_ui_icon'] = 'mediusers.png';
$config['mod_description'] = 'Gestion des utilisateurs';
$config['mod_config'] = true;

if (@$a == 'setup') {
	echo dPshowModuleConfig( $config );
}

class CSetupMediusers {

	function configure() {
		global $AppUI;
		$AppUI->redirect( 'm=mediusers&a=configure' );
		
  		return true;
	}

	function remove() {
		db_exec( "DROP TABLE users_mediboard;" ); db_error();
		db_exec( "DROP TABLE functions_mediboard;" ); db_error();
		db_exec( "DROP TABLE groups_mediboard;" ); db_error();
		
		return null;
	}


	function upgrade( $old_version ) {

		switch ( $old_version )
		{
			case "all":
			case "0.1":
        $sql = "ALTER TABLE `users_mediboard` ADD `remote` TINYINT DEFAULT NULL;";
        db_exec($sql);  db_error();
        
      case "0.11":
        $sql = "ALTER TABLE `users_mediboard` ADD `adeli` int(9) DEFAULT NULL;";
        db_exec($sql);  db_error();

      case "0.12":
        $sql = "ALTER TABLE `users_mediboard` CHANGE `adeli` `adeli` VARCHAR( 9 );";
        db_exec($sql);  db_error();

      case "0.13": 
        return true;
		}

		return false;
	}

	function install() {
		$sql = "CREATE TABLE users_mediboard ( " .
			"  user_id int(11) unsigned NOT NULL" .
			", function_id tinyint(4) unsigned NOT NULL default '0'" .
			", PRIMARY KEY  (user_id)" .
			", UNIQUE KEY user_id (user_id)" .
			") TYPE=MyISAM;";

		db_exec($sql); db_error();
		
		$sql = "CREATE TABLE functions_mediboard ( " .
			"  function_id tinyint(4) unsigned NOT NULL auto_increment" .
			", group_id tinyint(4) unsigned NOT NULL default '0'" .
			", text varchar(50) NOT NULL" .
			", color varchar(6) NOT NULL default 'ffffff'" .
			", PRIMARY KEY  (function_id)" .
			", UNIQUE KEY function_id (function_id)" .
			") TYPE=MyISAM;";

		db_exec($sql); db_error();
		
		$sql = "CREATE TABLE groups_mediboard ( " .
			"  group_id tinyint(4) unsigned NOT NULL auto_increment" .
			", text varchar(50) NOT NULL" .
			", PRIMARY KEY  (group_id)" .
			", UNIQUE KEY group_id (group_id)" .
			") TYPE=MyISAM;";

		db_exec($sql); db_error();
		
		$sql = "INSERT INTO groups_mediboard(text) VALUES ('Chirurgie');
				INSERT INTO groups_mediboard(text) VALUES ('Anesthésie');
				INSERT INTO groups_mediboard(text) VALUES ('Administration');";

		db_exec($sql); db_error();
		
		$sql = "INSERT INTO functions_mediboard (group_id, text, color) VALUES (2, Chirurgie orthopédique et traumatologique', '99FF66');
				INSERT INTO functions_mediboard (group_id, text, color) VALUES (2, 'Anesthésie - Réanimation', 'FFFFFF');
				INSERT INTO functions_mediboard (group_id, text, color) VALUES (3, 'Direction', 'CCFFFF');
				INSERT INTO functions_mediboard (group_id, text, color) VALUES (3, 'PMSI', 'FF3300');";

		db_exec($sql); 	db_error();
		
		$this->upgrade("all");

		return null;
	}

}

?>