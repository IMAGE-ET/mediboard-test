<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Romain Ollivier
*/

// MODULE CONFIGURATION DEFINITION
$config = array();
$config["mod_name"]        = "admin";
$config["mod_version"]     = "1.0.5";
$config["mod_type"]        = "core";
$config["mod_config"]      = false;

if (@$a == "setup") {
  echo dPshowModuleConfig($config);
}

class CSetupadmin {

  function configure() {
    global $AppUI;
    $AppUI->redirect("m=admin&a=configure");
    return true;
  }

  function remove() {
    return "Impossible de supprimer le module 'admin'";
  }

  function upgrade($old_version) {
    switch ($old_version) {
      case "all":
      case "1.0.0":
        $sql = "ALTER TABLE `users` CHANGE `user_address1` `user_address1` VARCHAR( 50 );";
        db_exec( $sql ); db_error();
      case "1.0.1":
        $error = "";
        require_once("legacy/permission.class.php");
        $sql = "CREATE TABLE `perm_module` (
                  `perm_module_id` MEDIUMINT NOT NULL AUTO_INCREMENT ,
                  `user_id` MEDIUMINT NOT NULL ,
                  `mod_id` MEDIUMINT NOT NULL ,
                  `permission` TINYINT NOT NULL ,
                  `view` TINYINT NOT NULL ,
                  PRIMARY KEY ( `perm_module_id` ) ,
                  UNIQUE ( `user_id`, `mod_id` )
                ) TYPE=MyISAM COMMENT = 'table des permissions sur les modules';";
        db_exec( $sql ); db_error();
        $sql = "CREATE TABLE `perm_object` (
                  `perm_object_id` MEDIUMINT NOT NULL AUTO_INCREMENT ,
                  `user_id` MEDIUMINT NOT NULL ,
                  `object_id` INT NOT NULL ,
                  `object_class` VARCHAR( 30 ) NOT NULL ,
                  `permission` TINYINT NOT NULL ,
                  PRIMARY KEY ( `perm_object_id` ) ,
                  UNIQUE (
                    `user_id` ,
                    `object_id` ,
                    `object_class`
                  )
                ) TYPE=MyISAM COMMENT = 'Table des permissions sur les objets';";
        db_exec( $sql ); db_error();
        $moduleClasses = array();
        $moduleClasses["dPetablissement"] = "CGroups";
        $moduleClasses["mediusers"] = "CFunctions";
        $perm = new CPermission;
        $listOldPerms = $perm->loadList();
        foreach($listOldPerms as $key => $value) {
          $module = new CModule;
          if($value->permission_grant_on == "all") {
            $module->mod_id = 0;
          } else {
            $module->loadByName($value->permission_grant_on);
          }
          if($module->mod_id !== null) {
          if($value->permission_item == -1) {
            $newPerm = new CPermModule;
            $newPerm->user_id = $value->permission_user;
            $newPerm->mod_id  = $module->mod_id;
            if($value->_module_editall) {
              $newPerm->permission = 2;
            } elseif($value->_module_readall) {
              $newPerm->permission = 1;
            } else {
              $newPerm->permission = 0;
            }
            if($value->_module_visible) {
              $newPerm->view = 1;
            } else {
              $newPerm->view = 0;
            }
          } else {
            $newPerm = new CPermObject;
            $newPerm->user_id      = $value->permission_user;
            $newPerm->object_id    = $value->permission_item;
            $newPerm->object_class = $moduleClasses[$value->permission_grant_on];
            if($value->_item_edit) {
              $newPerm->permission = 2;
            } elseif($value->_item_read) {
              $newPerm->permission = 1;
            } else {
              $newPerm->permission = 0;
            }
          }
          $user = new CUser;
          if($user->load($value->permission_user)) {
            if($msg = $newPerm->store()) {
              $error .= $msg."<br />";
            }
          }
          }
        }
        if($error !== "") {
          return "1.0.1";
        }
        
      case "1.0.2":
        $sql = "DELETE FROM `user_preferences` WHERE (`pref_name`!='LOCALE' && `pref_name`!='UISTYLE');";
        db_exec( $sql ); db_error();
      case "1.0.3":
        $sql = "UPDATE `user_preferences` SET `pref_name`='AFFCONSULT' WHERE `pref_name`='CABCONSULT';";
        db_exec( $sql ); db_error();
      case "1.0.4":
        $sql = "ALTER TABLE `perm_module` " .
               "\nCHANGE `perm_module_id` `perm_module_id` int(11) unsigned NOT NULL AUTO_INCREMENT," .
               "\nCHANGE `user_id` `user_id` int(11) unsigned NOT NULL DEFAULT '0'," .
               "\nCHANGE `mod_id` `mod_id` int(11) unsigned NOT NULL DEFAULT '0'," .
               "\nCHANGE `permission` `permission` tinyint(1) unsigned zerofill NOT NULL DEFAULT '0'," .
               "\nCHANGE `view` `view` tinyint(1) unsigned zerofill NOT NULL DEFAULT '0';";
        db_exec( $sql ); db_error();
        
        $sql = "ALTER TABLE `perm_object` " .
               "\nCHANGE `perm_object_id` `perm_object_id` int(11) unsigned NOT NULL AUTO_INCREMENT," .
               "\nCHANGE `user_id` `user_id` int(11) unsigned NOT NULL DEFAULT '0'," .
               "\nCHANGE `object_id` `object_id` int(11) unsigned NOT NULL DEFAULT '0'," .
               "\nCHANGE `object_class` `object_class` varchar(255) NOT NULL," .
               "\nCHANGE `permission` `permission` tinyint(1) unsigned zerofill NOT NULL DEFAULT '0';";
        db_exec( $sql ); db_error();
        
        $sql = "ALTER TABLE `users` " .
               "\nCHANGE `user_id` `user_id` int(11) unsigned NOT NULL AUTO_INCREMENT," .
               "\nCHANGE `user_password` `user_password` varchar(255) NOT NULL," .
               "\nCHANGE `user_type` `user_type` tinyint(4) NOT NULL DEFAULT '0'," .
               "\nCHANGE `user_last_name` `user_last_name` varchar(50) NOT NULL;";
        db_exec( $sql ); db_error();
        
        $sql = "ALTER TABLE `users` " .
               "\nDROP `user_parent`," .
               "\nDROP `user_company`," .
               "\nDROP `user_department`," .
               "\nDROP `user_home_phone`," .
               "\nDROP `user_address2`," .
               "\nDROP `user_state`," .
               "\nDROP `user_icq`," .
               "\nDROP `user_aol`," .
               "\nDROP `user_owner`;";
        db_exec( $sql ); db_error();
        
      case "1.0.5":
        return "1.0.5";
    }
    

    return false;
  }
}

?>