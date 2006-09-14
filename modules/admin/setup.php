<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Romain Ollivier
*/

require_once($AppUI->getModuleClass("admin"));
require_once($AppUI->getModuleClass("admin", "permModule"));
require_once($AppUI->getModuleClass("admin", "permObject"));
require_once($AppUI->getModuleClass("system"));

// MODULE CONFIGURATION DEFINITION
$config = array();
$config["mod_name"]        = "admin";
$config["mod_version"]     = "1.0.2";
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
        $sql = "CREATE TABLE `perm_module` (
                  `perm_module_id` MEDIUMINT NOT NULL AUTO_INCREMENT ,
                  `user_id` MEDIUMINT NOT NULL ,
                  `mod_id` MEDIUMINT NOT NULL ,
                  `permission` TINYINT NOT NULL ,
                  `show` TINYINT NOT NULL ,
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
        $moduleClasses["mediusers"] = "CMediusers";
        $listOldPerms = new CPermission;
        $listOldPerms = $listOldPerms->loadList();
        foreach($listOldPerms as $key => $value) {
          $module = new CModule;
          $where = array();
          $where["mod_name"] = "= '$value->permission_grant_on'";
          $module->loadObject($where);
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
              $newPerm->show = 1;
            } else {
              $newPerm->show = 0;
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
          $error = "";
          $user = new CUser;
          if($user->load($value->permission_user)) {
            if($msg = $newPerm->store()) {
              $error .= $msg."<br />";
            }
          }
        }
        if($error !== "") {
          return "1.0.1";
        }
        
      case "1.0.2":
        return "1.0.2";
    }
    

    return false;
  }
}

?>