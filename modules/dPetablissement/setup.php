<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPetablissement
* @version $Revision: $
* @author Romain Ollivier
*/

$config = array();
$config["mod_name"]        = "dPetablissement";
$config["mod_version"]     = "0.1";
$config["mod_directory"]   = "dPetablissement";
$config["mod_setup_class"] = "CSetupdPetablissement";
$config["mod_type"]        = "user";
$config["mod_ui_name"]     = "Etablissements";
$config["mod_ui_icon"]     = "etablissements.png";
$config["mod_description"] = "Gestion des établissements";
$config["mod_config"]      = true;

if(@$a == "setup") {
  echo dPshowModuleConfig($config);
}

class CSetupdPetablissement {

  function configure() {
    global $AppUI;
    $AppUI->redirect( "m=dPetablissement&a=configure" );
    return true;
  }

  function remove() {
    db_exec("DROP TABLE `groups_mediboard`;")   ; db_error();
    return null;
  }


  function upgrade($old_version) {
    switch ($old_version) {
      case "all":
        $sql = "SHOW TABLE STATUS LIKE 'groups_mediboard'";
        $result = db_loadResult($sql);
        if(!$result) {
          $sql = "CREATE TABLE `groups_mediboard` (" .
            "\n`group_id` TINYINT(4) UNSIGNED NOT NULL AUTO_INCREMENT," .
            "\n`text` VARCHAR(50) NOT NULL," .
            "\nPRIMARY KEY  (`group_id`)" .
            "\n) TYPE=MyISAM;";
          db_exec($sql); db_error();
          $sql = "ALTER TABLE `groups_mediboard` DROP INDEX `group_id` ;";
          db_exec($sql); db_error();
        }
      case "0.1":
        return "0.1";
    }

    return false;
  }
}

?>