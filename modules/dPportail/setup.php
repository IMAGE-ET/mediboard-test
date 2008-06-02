<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPportail
 * @version $Revision: $
 * @author Fabien
 */

global $AppUI;

// MODULE CONFIGURATION DEFINITION
$config = array();
$config["mod_name"]        = "dPportail";
$config["mod_version"]     = "0.10";
$config["mod_type"]        = "user";

class CSetupdPportail extends CSetup {

    function __construct() {
        parent::__construct();

        $this->mod_name = 'dPportail';
        $this->makeRevision('all');

        $sql = 'CREATE TABLE `forum_theme` (
            `forum_theme_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT, 
            `title` TEXT NOT NULL, 
            `desc` TEXT, 
            PRIMARY KEY (`forum_theme_id`)) TYPE=MYISAM;';
        $this->addQuery($sql);
        
        $sql = 'CREATE TABLE `forum_message` (
            `forum_message_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT, 
            `forum_thread_id` INT(11) UNSIGNED NOT NULL, 
            `body` TEXT NOT NULL, 
            `date` DATETIME NOT NULL, 
            `user_id` INT(11) UNSIGNED NOT NULL,
            PRIMARY KEY (`forum_message_id`)) TYPE=MYISAM;';
        $this->addQuery($sql);
        
        $sql = 'CREATE TABLE `forum_thread` (
            `forum_thread_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT, 
            `forum_theme_id` INT(11) UNSIGNED NOT NULL, 
            `title` TEXT NOT NULL, 
            `body` TEXT NOT NULL, 
            `date` DATETIME NOT NULL, 
            `user_id` INT(11) UNSIGNED NOT NULL,
            PRIMARY KEY (`forum_thread_id`)) TYPE=MYISAM;';
        $this->addQuery($sql);
        
        /*$sql = 'CREATE TABLE `forum_message` (
            `forum_message_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT, 
            `forum_thread_id` INT(11) UNSIGNED NOT NULL, 
            `body` TEXT NOT NULL, 
            `date` DATETIME NOT NULL, 
            `user_id` INT(11) UNSIGNED NOT NULL,
            PRIMARY KEY (`forum_message_id`)) TYPE=MYISAM;';
        $this->addQuery($sql);*/
        
        $this->mod_version = '0.10';
    }
}
?>
