<?php /* $Id: setup.php 1390 2006-12-13 09:55:29Z maskas $ */

/**
* @package Mediboard
* @subpackage sherpa
* @version $Revision: 1390 $
* @author sherpa
*/

// MODULE CONFIGURATION DEFINITION
$config = array();
$config["mod_name"]        = "sherpa";
$config["mod_version"]     = "0.14";
$config["mod_type"]        = "user";

class CSetupsherpa extends CSetup {
  
  function __construct() {
    parent::__construct();
    
    $this->mod_name = "sherpa";
    
    $this->makeRevision("all");
    $sql = "CREATE TABLE `t_malade` (" .
        "\n `malade_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT," .
        "\n `malnum` INT(6) UNSIGNED ZEROFILL," .
        "\n `malnom` CHAR(20)," .
        "\n `malpre` CHAR(10)," .
        "\n `datnai` INT(8) UNSIGNED ZEROFILL," .
        "\nPRIMARY KEY (`malade_id`)) TYPE=MYISAM";
    $this->addQuery($sql);
    
    $this->makeRevision("0.10");
    $sql = "CREATE TABLE `sp_etablissement` (" .
        "\n `sp_etab_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT," .
        "\n `group_id` INT(11) UNSIGNED NOT NULL," .
        "\n `increment_year` TINYINT(1) UNSIGNED ZEROFILL," .
        "\n `increment_patient` INT," .
        "\nPRIMARY KEY (`sp_etab_id`)) TYPE=MYISAM";
    $this->addQuery($sql);
    
    $this->makeRevision("0.11");
    $sql = "ALTER TABLE `t_malade` " .
        "\nDROP `malade_id`," .
        "\nCHANGE `malnum` `malnum` INT( 6 ) UNSIGNED ZEROFILL NOT NULL DEFAULT '0'," .
        "\nADD PRIMARY KEY ( `malnum` ) ;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.12");
    
    $this->makeRevision("0.13");
    $sql = "ALTER TABLE `t_malade` " .
          "\nCHANGE `malnom` `malnom` VARCHAR(20), " .
          "\nCHANGE `malpre` `malpre` VARCHAR(10), " .
          "\nCHANGE `datnai` `datnai` CHAR(10);";
    $this->addQuery($sql);

    $this->mod_version = "0.14";
    
  }
}
?>