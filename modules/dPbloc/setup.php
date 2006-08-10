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
$config["mod_version"]     = "0.15";
$config["mod_directory"]   = "dPbloc";
$config["mod_setup_class"] = "CSetupdPbloc";
$config["mod_type"]        = "user";
$config["mod_ui_name"]     = "Planning Bloc";
$config["mod_ui_icon"]     = "dPbloc.png";
$config["mod_description"] = "Gestion du bloc opératoire";
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
        return "0.15";
    }
    return false;
  }

  function swapPratIds() {
    global $AppUI;
    set_time_limit(1800);
    ignore_user_abort(1);
    require_once($AppUI->getModuleClass("admin"));
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