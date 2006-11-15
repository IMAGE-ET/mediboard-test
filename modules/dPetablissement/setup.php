<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPetablissement
* @version $Revision: $
* @author Romain Ollivier
*/

$config = array();
$config["mod_name"]        = "dPetablissement";
$config["mod_version"]     = "0.14";
$config["mod_config"]      = false;

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
    return "Impossible de supprimer le module 'dPetablissement'";
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
        $sql = "ALTER TABLE `groups_mediboard`" .
            "\nADD `raison_sociale` VARCHAR( 50 ) ," .
            "\nADD `adresse` TEXT ," .
            "\nADD `cp` VARCHAR( 5 ) ," .
            "\nADD `ville` VARCHAR( 50 ) ," .
            "\nADD `tel` VARCHAR( 10 ) ," .
            "\nADD `directeur` VARCHAR( 50 ) ," .
            "\nADD `domiciliation` VARCHAR( 9 ) ," .
            "\nADD `siret` VARCHAR( 14 );";
        db_exec( $sql ); db_error();
      case "0.11":
        $sql = "ALTER TABLE `groups_mediboard`" .
            "\nADD `ape` VARCHAR( 4 ) DEFAULT NULL;";
        db_exec( $sql ); db_error();
      case "0.12":
        $sql = "ALTER TABLE `groups_mediboard` " .
               "\nCHANGE `group_id` `group_id` int(11) unsigned NOT NULL AUTO_INCREMENT," .
               "\nCHANGE `cp` `cp` int(5) unsigned zerofill NULL," .
               "\nCHANGE `tel` `tel` bigint(10) unsigned zerofill NULL," .
               "\nCHANGE `text` `text` varchar(255) NOT NULL;";
        db_exec( $sql ); db_error();
      case "0.13":
        $sql = "ALTER TABLE `groups_mediboard`" .
            "\nADD `fax` bigint(10) unsigned zerofill NULL AFTER `tel`,".
            "\nADD `mail` varchar(50) DEFAULT NULL," .
            "\nADD `web` varchar(255) DEFAULT NULL;" ;
        db_exec( $sql ); db_error();
      case "0.14":
        return "0.14";
    }

    return false;
  }
}

?>