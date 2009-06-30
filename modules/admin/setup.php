<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage admin
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CSetupadmin extends CSetup {
  function __construct() {
    parent::__construct();
    
    $this->mod_type = "core";
    $this->mod_name = "admin";
    
    $this->makeRevision("all");
    
    $this->makeRevision("1.0.0");
    $sql = "ALTER TABLE `users` CHANGE `user_address1` `user_address1` VARCHAR( 50 );";
    $this->addQuery($sql);
    
    $this->makeRevision("1.0.1");
    $sql = "CREATE TABLE `perm_module` (
                  `perm_module_id` MEDIUMINT NOT NULL AUTO_INCREMENT ,
                  `user_id` MEDIUMINT NOT NULL ,
                  `mod_id` MEDIUMINT NOT NULL ,
                  `permission` TINYINT NOT NULL ,
                  `view` TINYINT NOT NULL ,
                  PRIMARY KEY ( `perm_module_id` ) ,
                  UNIQUE ( `user_id`, `mod_id` )
                ) TYPE=MyISAM COMMENT = 'table des permissions sur les modules';";
    $this->addQuery($sql);
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
    $this->addQuery($sql);
    
    function setup_changePerm(){
      CAppUI::requireLegacyClass("permission");
      $error = "";
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
          $newPerm = new CPermModuleLegacy;
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
          $newPerm = new CPermObjectLegacy;
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
      // Ajout des droits d'administration généraux
      $editPerm = new CPermModule;
      $where = array("user_id" =>"= '1'", "mod_id" =>"= '0'");
      $editPerm->loadObject($where);
      $editPerm->view = 2;
      $editPerm->store();
      if($error !== "") {
        trigger_error($error);
        return false;
      }else{
        return true;
      }
    }
    $this->addFunctions("setup_changePerm");
    
                
    $this->makeRevision("1.0.2");
    $sql = "DELETE FROM `user_preferences` WHERE (`pref_name`!='LOCALE' && `pref_name`!='UISTYLE');";
    $this->addQuery($sql);
    
    $this->makeRevision("1.0.3");
    $sql = "UPDATE `user_preferences` SET `pref_name`='AFFCONSULT' WHERE `pref_name`='CABCONSULT';";
    $this->addQuery($sql);
    
    $this->makeRevision("1.0.4");
    $sql = "ALTER TABLE `perm_module` " .
               "\nCHANGE `perm_module_id` `perm_module_id` int(11) unsigned NOT NULL AUTO_INCREMENT," .
               "\nCHANGE `user_id` `user_id` int(11) unsigned NOT NULL DEFAULT '0'," .
               "\nCHANGE `mod_id` `mod_id` int(11) unsigned NOT NULL DEFAULT '0'," .
               "\nCHANGE `permission` `permission` tinyint(1) unsigned zerofill NOT NULL DEFAULT '0'," .
               "\nCHANGE `view` `view` tinyint(1) unsigned zerofill NOT NULL DEFAULT '0';";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `perm_object` " .
               "\nCHANGE `perm_object_id` `perm_object_id` int(11) unsigned NOT NULL AUTO_INCREMENT," .
               "\nCHANGE `user_id` `user_id` int(11) unsigned NOT NULL DEFAULT '0'," .
               "\nCHANGE `object_id` `object_id` int(11) unsigned NOT NULL DEFAULT '0'," .
               "\nCHANGE `object_class` `object_class` varchar(255) NOT NULL," .
               "\nCHANGE `permission` `permission` tinyint(1) unsigned zerofill NOT NULL DEFAULT '0';";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `users` " .
               "\nCHANGE `user_id` `user_id` int(11) unsigned NOT NULL AUTO_INCREMENT," .
               "\nCHANGE `user_password` `user_password` varchar(255) NOT NULL," .
               "\nCHANGE `user_type` `user_type` tinyint(4) NOT NULL DEFAULT '0'," .
               "\nCHANGE `user_last_name` `user_last_name` varchar(50) NOT NULL;";
    $this->addQuery($sql);
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
    $this->addQuery($sql);
    
    $this->makeRevision("1.0.5");
    $sql = "ALTER TABLE `user_preferences` " .
        "\nCHANGE `pref_name` `pref_name` VARCHAR( 40 ) NOT NULL";
    $this->addQuery($sql);
    
    $this->makeRevision("1.0.6");
    $sql = "ALTER TABLE `perm_object` CHANGE `object_id` `object_id` int(11) unsigned NULL DEFAULT NULL;";
    $this->addQuery($sql);
    $sql = "UPDATE `perm_object` SET object_id = NULL WHERE object_id='0';";
    $this->addQuery($sql);
    
    $sql = "ALTER TABLE `perm_module` CHANGE `mod_id` `mod_id` int(11) unsigned NULL DEFAULT NULL;";
    $this->addQuery($sql);
    $sql = "UPDATE `perm_module` SET mod_id = NULL WHERE mod_id='0';";
    $this->addQuery($sql);
    $sql = "DELETE FROM `perm_module` WHERE user_id='0';";
    $this->addQuery($sql);
    
    $this->makeRevision("1.0.7");
    $sql = "ALTER TABLE `users` ADD `user_last_login` DATETIME NULL;";
    $this->addQuery($sql);

    $this->makeRevision("1.0.8");
    $sql = "UPDATE `perm_module` SET `view` = '2' WHERE `user_id` = 1 AND `mod_id`IS NULL;";
    $this->addQuery($sql);
    
    $this->makeRevision("1.0.9");
    $sql = "ALTER TABLE `users` ADD `template` enum('0','1') NOT NULL default '0';";
    $this->addQuery($sql);
    $sql = "UPDATE `users` SET `template` = '1' WHERE `user_username` like '>>%';";
    $this->addQuery($sql);
    
    $this->makeRevision("1.0.10");
    $sql = "ALTER TABLE `users`
            ADD `profile_id` INT(11) UNSIGNED;";
    $this->addQuery($sql);
    
    $this->makeRevision("1.0.11");
    $sql = "UPDATE `users` 
            SET `user_username` = TRIM(LEADING '>> ' FROM `user_username`)
            WHERE `user_username` LIKE '>>%';";
    $this->addQuery($sql);
    
    $this->makeRevision("1.0.12");
    $sql = "ALTER TABLE `users` 
            ADD `user_login_errors` TINYINT NOT NULL DEFAULT '0' AFTER `user_last_login`;";
    $this->addQuery($sql);
    
    $this->makeRevision("1.0.13");
    $sql = "ALTER TABLE `users` CHANGE `user_login_errors` `user_login_errors` TINYINT( 4 ) NULL DEFAULT '0'";
    $this->addQuery($sql);
    
    $this->makeRevision("1.0.14");
    $sql = "ALTER TABLE `user_preferences` CHANGE `pref_value` `pref_value` VARCHAR( 255 ) NOT NULL";
    $this->addQuery($sql);
    
    $this->mod_version = "1.0.15";
  }
}
?>