<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPbloc
* @version $Revision$
* @author Romain Ollivier
*/

// MODULE CONFIGURATION DEFINITION
$config = array();
$config["mod_name"]        = "dPbloc";
$config["mod_version"]     = "0.18";
$config["mod_type"]        = "user";
$config["mod_config"]      = true;

if (@$a == "setup") {
  echo dPshowModuleConfig( $config );
}

class CSetupdPbloc {

  function configure() {
    global $AppUI;
    $AppUI->redirect("m=dPbloc&a=configure");
    return true;
  }

  function remove() {
    db_exec("DROP TABLE plagesop;");
    db_exec("DROP TABLE sallesbloc;");
    return null;
  }

  function upgrade($old_version) {
    switch ($old_version) {
      case "all":
        $sql = "CREATE TABLE plagesop (
              id bigint(20) NOT NULL auto_increment,
              id_chir varchar(20) NOT NULL default '0',
              id_anesth varchar(20) default NULL,
              id_spec tinyint(4) default NULL,
              id_salle tinyint(4) NOT NULL default '0',
              date date NOT NULL default '0000-00-00',
              debut time NOT NULL default '00:00:00',
              fin time NOT NULL default '00:00:00',
              PRIMARY KEY  (id)
              ) TYPE=MyISAM COMMENT='Table des plages d\'opération';";
        db_exec( $sql ); db_error();
        $sql = "CREATE TABLE sallesbloc (
                id tinyint(4) NOT NULL auto_increment,
                nom varchar(50) NOT NULL default '',
                PRIMARY KEY  (id)
                ) TYPE=MyISAM COMMENT='Table des salles d\'opération du bloc';";
        db_exec( $sql ); db_error();
      case "0.1":
        $sql = "ALTER TABLE `plagesop` ADD INDEX ( `id_chir` );";
        db_exec( $sql ); db_error();
        $sql = "ALTER TABLE `plagesop` ADD INDEX ( `id_anesth` )";
        db_exec( $sql ); db_error();
        $sql = "ALTER TABLE `plagesop` ADD INDEX ( `id_spec` )";
        db_exec( $sql ); db_error();
        $sql = "ALTER TABLE `plagesop` ADD INDEX ( `id_salle` )";
        db_exec( $sql ); db_error();
        $sql = "ALTER TABLE `plagesop` ADD INDEX ( `date` )";
        db_exec( $sql ); db_error();
      case "0.11":
        $sql = "ALTER TABLE `plagesop` ADD `chir_id` BIGINT DEFAULT '0' NOT NULL AFTER `id` ;";
        db_exec( $sql ); db_error();
        $sql = "ALTER TABLE `plagesop` ADD INDEX ( `chir_id` ) ;";
        db_exec( $sql ); db_error();
        $sql = "ALTER TABLE `plagesop` ADD `anesth_id` BIGINT DEFAULT '0' NOT NULL AFTER `chir_id` ;";
        db_exec( $sql ); db_error();
        $sql = "ALTER TABLE `plagesop` ADD INDEX ( `anesth_id` ) ;";
        db_exec( $sql ); db_error();
        $this->swapPratIds();
      case "0.12":
        $sql = "ALTER TABLE `sallesbloc` ADD `stats` TINYINT DEFAULT '0' NOT NULL AFTER `nom` ;";
        db_exec( $sql ); db_error();
      case "0.13":
        $module = @CModule::getInstalled("dPetablissement");
        if (!$module || $module->mod_version < "0.1") {
          return "0.13";
        }
        $sql = "ALTER TABLE `sallesbloc` ADD `group_id` INT UNSIGNED NOT NULL DEFAULT 1 AFTER `id`;";
        db_exec($sql); db_error();
        $sql = "ALTER TABLE `sallesbloc` ADD INDEX ( `group_id` ) ;";
        db_exec( $sql ); db_error();
      case "0.14":
        $sql = "ALTER TABLE `plagesop` DROP `id_chir` ;";
        db_exec( $sql ); db_error();
        $sql = "ALTER TABLE `plagesop` DROP `id_anesth` ;";
        db_exec( $sql ); db_error();
      case "0.15":
        $sql = "ALTER TABLE `plagesop` CHANGE `id` `plageop_id` BIGINT( 20 ) NOT NULL AUTO_INCREMENT ;";
        db_exec( $sql ); db_error();
        $sql = "ALTER TABLE `sallesbloc` CHANGE `id` `salle_id` BIGINT( 20 ) NOT NULL AUTO_INCREMENT ;";
        db_exec( $sql ); db_error();
        $sql = "ALTER TABLE `plagesop` CHANGE `id_spec` `spec_id` INT( 10 ) DEFAULT NULL ;";
        db_exec( $sql ); db_error();
        $sql = "ALTER TABLE `plagesop` CHANGE `id_salle` `salle_id` INT( 10 ) DEFAULT '0' NOT NULL ;";
        db_exec( $sql ); db_error();
      case "0.16":
        $sql = "ALTER TABLE `plagesop` ADD `temps_inter_op` TIME NOT NULL DEFAULT '00:15:00' ;";
        db_exec( $sql ); db_error();
      case "0.17":
        $sql = "ALTER TABLE `plagesop` " .
               "\nCHANGE `plageop_id` `plageop_id` int(11) unsigned NOT NULL AUTO_INCREMENT," .
               "\nCHANGE `chir_id` `chir_id` int(11) unsigned NOT NULL DEFAULT '0'," .
               "\nCHANGE `anesth_id` `anesth_id` int(11) unsigned NOT NULL DEFAULT '0'," .
               "\nCHANGE `spec_id` `spec_id` int(11) unsigned NULL," .
               "\nCHANGE `salle_id` `salle_id` int(11) unsigned NOT NULL DEFAULT '0';";
        db_exec( $sql ); db_error();
        
        $sql = "ALTER TABLE `sallesbloc` " .
               "\nCHANGE `salle_id` `salle_id` int(11) unsigned NOT NULL AUTO_INCREMENT," .
               "\nCHANGE `group_id` `group_id` int(11) unsigned NOT NULL DEFAULT '1'," .
               "\nCHANGE `stats` `stats` enum('0','1') NOT NULL DEFAULT '0'," .
               "\nCHANGE `nom` `nom` varchar(255) NOT NULL;";
        db_exec( $sql ); db_error();
      case "0.18":
        return "0.18";
    }
    return false;
  }

  function swapPratIds() {
    global $AppUI;
    set_time_limit(1800);
    ignore_user_abort(1);
    $user = new CUser;
    
    // Changement des chirurgiens
    $sql = "SELECT id_chir" .
        "\nFROM plagesop" .
        "\nGROUP BY id_chir";
    $listPlages = db_loadList($sql);
    foreach($listPlages as $key => $plage) {
      $where["user_username"] = "= '".$plage["id_chir"]."'";
      $user->loadObject($where);
      if($user->user_id) {
        $sql = "UPDATE plagesop" .
            "\nSET chir_id = '$user->user_id'" .
            "\nWHERE id_chir = '$user->user_username'";
        db_exec( $sql ); db_error();
      }
    }
    
    //Changement des anesthésistes
    $sql = "SELECT id_anesth" .
        "\nFROM plagesop" .
        "\nGROUP BY id_anesth";
    $listPlages = db_loadList($sql);
    foreach($listPlages as $key => $plage) {
      $where["user_username"] = "= '".$plage["id_anesth"]."'";
      $user->loadObject($where);
      if($user->user_id) {
        $sql = "UPDATE plagesop" .
            "\nSET anesth_id = '$user->user_id'" .
            "\nWHERE id_anesth = '$user->user_username'";
        db_exec( $sql ); db_error();
      }
    }
  }
}

?>