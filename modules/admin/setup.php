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
    
    $this->makeRevision("1.0.00");
    $query = "ALTER TABLE `users` 
      CHANGE `user_address1` `user_address1` VARCHAR( 50 );";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.01");
    $query = "CREATE TABLE `perm_module` (
      `perm_module_id` MEDIUMINT NOT NULL AUTO_INCREMENT ,
      `user_id` MEDIUMINT NOT NULL ,
      `mod_id` MEDIUMINT NOT NULL ,
      `permission` TINYINT NOT NULL ,
      `view` TINYINT NOT NULL ,
      PRIMARY KEY ( `perm_module_id` ) ,
      UNIQUE ( `user_id`, `mod_id` )
    ) /*! ENGINE=MyISAM */ COMMENT = 'table des permissions sur les modules';";
    $this->addQuery($query);
    $query = "CREATE TABLE `perm_object` (
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
    ) /*! ENGINE=MyISAM */ COMMENT = 'Table des permissions sur les objets';";
    $this->addQuery($query);
    
    function setup_changePerm(){
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
    $this->addFunction("setup_changePerm");
    
                
    $this->makeRevision("1.0.02");
    $query = "DELETE FROM `user_preferences` 
      WHERE (`pref_name`!='LOCALE' && `pref_name`!='UISTYLE');";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.03");
    $query = "UPDATE `user_preferences` 
      SET `pref_name`='AFFCONSULT' WHERE `pref_name`='CABCONSULT';";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.04");
    $query = "ALTER TABLE `perm_module`
      CHANGE `perm_module_id` `perm_module_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
      CHANGE `user_id` `user_id` int(11) unsigned NOT NULL DEFAULT '0',
      CHANGE `mod_id` `mod_id` int(11) unsigned NOT NULL DEFAULT '0',
      CHANGE `permission` `permission` tinyint(1) unsigned zerofill NOT NULL DEFAULT '0',
      CHANGE `view` `view` tinyint(1) unsigned zerofill NOT NULL DEFAULT '0';";
    $this->addQuery($query);
    $query = "ALTER TABLE `perm_object`
      CHANGE `perm_object_id` `perm_object_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
      CHANGE `user_id` `user_id` int(11) unsigned NOT NULL DEFAULT '0',
      CHANGE `object_id` `object_id` int(11) unsigned NOT NULL DEFAULT '0',
      CHANGE `object_class` `object_class` varchar(255) NOT NULL,
      CHANGE `permission` `permission` tinyint(1) unsigned zerofill NOT NULL DEFAULT '0';";
    $this->addQuery($query);
    $query = "ALTER TABLE `users`
      CHANGE `user_id` `user_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
      CHANGE `user_password` `user_password` varchar(255) NOT NULL,
      CHANGE `user_type` `user_type` tinyint(4) NOT NULL DEFAULT '0',
      CHANGE `user_last_name` `user_last_name` varchar(50) NOT NULL;";
    $this->addQuery($query);
    $query = "ALTER TABLE `users`
      DROP `user_parent`,
      DROP `user_company`,
      DROP `user_department`,
      DROP `user_home_phone`,
      DROP `user_address2`,
      DROP `user_state`,
      DROP `user_icq`,
      DROP `user_aol`,
      DROP `user_owner`;";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.06");
    $query = "ALTER TABLE `perm_object` 
      CHANGE `object_id` `object_id` int(11) unsigned NULL DEFAULT NULL;";
    $this->addQuery($query);
    $query = "UPDATE `perm_object` 
      SET object_id = NULL WHERE object_id='0';";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `perm_module` 
      CHANGE `mod_id` `mod_id` int(11) unsigned NULL DEFAULT NULL;";
    $this->addQuery($query);
    $query = "UPDATE `perm_module` 
      SET mod_id = NULL WHERE mod_id='0';";
    $this->addQuery($query);
    $query = "DELETE FROM `perm_module` 
      WHERE user_id='0';";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.07");
    $query = "ALTER TABLE `users` 
      ADD `user_last_login` DATETIME NULL;";
    $this->addQuery($query);

    $this->makeRevision("1.0.08");
    $query = "UPDATE `perm_module` 
      SET `view` = '2' 
      WHERE `user_id` = 1 
      AND `mod_id`IS NULL;";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.09");
    $query = "ALTER TABLE `users` 
      ADD `template` enum('0','1') NOT NULL default '0';";
    $this->addQuery($query);
    $query = "UPDATE `users` 
      SET `template` = '1' WHERE `user_username` like '>>%';";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.10");
    $query = "ALTER TABLE `users`
      ADD `profile_id` INT(11) UNSIGNED;";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.11");
    $query = "UPDATE `users` 
      SET `user_username` = TRIM(LEADING '>> ' FROM `user_username`)
      WHERE `user_username` LIKE '>>%';";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.12");
    $query = "ALTER TABLE `users` 
      ADD `user_login_errors` TINYINT NOT NULL DEFAULT '0' AFTER `user_last_login`;";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.13");
    $query = "ALTER TABLE `users`
      CHANGE `user_login_errors` `user_login_errors` TINYINT( 4 ) NULL DEFAULT '0'";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.14");
    $query = "ALTER TABLE `user_preferences` 
      DROP PRIMARY KEY;";
    $this->addQuery($query);
    $query = "ALTER TABLE `user_preferences` 
      ADD `pref_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
      CHANGE `pref_user` `user_id` INT (11) UNSIGNED NOT NULL DEFAULT '0',
      CHANGE `pref_name` `key` VARCHAR (255) NOT NULL,
      CHANGE `pref_value` `value` VARCHAR (255);";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.15");
    $query = "ALTER TABLE `users` 
      ADD INDEX (`user_birthday`),
      ADD INDEX (`user_last_login`),
      ADD INDEX (`profile_id`);";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.16");
    $query = "ALTER TABLE `users` 
      CHANGE `user_address1` `user_address1` VARCHAR( 255 );";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.17");
    $query = "ALTER TABLE `users` 
      ADD `dont_log_connection` ENUM ('0','1') DEFAULT '0';";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.18");
    $query = "CREATE TABLE `source_ldap` (
      `source_ldap_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
      `name` VARCHAR (255) NOT NULL,
      `host` TEXT NOT NULL,
      `port` INT (11) DEFAULT '389',
      `rootdn` VARCHAR (255) NOT NULL,
      `ldap_opt_protocol_version` INT (11) DEFAULT '3',
      `ldap_opt_referrals` ENUM ('0','1') DEFAULT '0'
     ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.19");
    $query = "ALTER TABLE `source_ldap` 
                ADD `bind_rdn_suffix` VARCHAR (255);";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.20");
    $query = "ALTER TABLE `source_ldap` 
              ADD `priority` INT (11);";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.21");
    $query = "ALTER TABLE `source_ldap` 
              ADD `secured` ENUM ('0','1') DEFAULT '0';";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.22");
    $query = "ALTER TABLE `users` 
      CHANGE `user_phone`  `user_phone`  VARCHAR (20),
      CHANGE `user_mobile` `user_mobile` VARCHAR (20)";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.23");
    $query = "ALTER TABLE `users` 
      DROP `user_pic`,
      DROP `user_signature`,
      CHANGE `user_password`     `user_password`     VARCHAR(255),
      CHANGE `user_login_errors` `user_login_errors` TINYINT( 4 ) UNSIGNED NOT NULL DEFAULT '0',
      CHANGE `user_type`         `user_type`         TINYINT( 4 ) UNSIGNED NOT NULL DEFAULT '0';";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.24");
    $query = "ALTER TABLE `users`
      ADD `user_salt` CHAR(64) AFTER `user_password`,
      MODIFY `user_password` CHAR(64);";
    $this->addQuery($query);
    
    $this->makeRevision("1.0.25");
    $this->addDependency("system", "1.1.12");
    
    $query = "CREATE TABLE `view_access_token` (
              `view_access_token_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
              `user_id` INT (11) UNSIGNED NOT NULL,
              `datetime_start` DATETIME NOT NULL,
              `ttl_hours` INT (11) UNSIGNED NOT NULL,
              `first_use` DATETIME,
              `params` VARCHAR (255) NOT NULL,
              `hash` CHAR (40) NOT NULL
             ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    $query = "ALTER TABLE `view_access_token` 
              ADD INDEX (`user_id`),
              ADD INDEX (`datetime_start`),
              ADD INDEX (`first_use`),
              ADD INDEX (`hash`);";
    $this->addQuery($query);
    
    $this->mod_version = "1.0.26";
  }
}
?>