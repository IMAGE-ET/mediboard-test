<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPlabo
* @version $Revision: $
* @author Romain Ollivier
*/

global $AppUI;

$config = array();
$config["mod_name"]        = "dPlabo";
$config["mod_version"]     = "0.14";
$config["mod_type"]        = "user";

class CSetupdPlabo extends CSetup {
  
  function __construct() {
    parent::__construct();
    
    $this->mod_name = "dPlabo";
    
    $this->makeRevision("all");

    $sql = "CREATE TABLE `catalogue_labo` (" .
          "\n`catalogue_labo_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ," .
          "\n`pere_id` INT(11) UNSIGNED DEFAULT NULL ," .
          "\n`identifiant` VARCHAR(255) NOT NULL ," .
          "\n`libelle` VARCHAR(255) NOT NULL," .
          "\nPRIMARY KEY (`catalogue_labo_id`) ," .
          "\nINDEX ( `pere_id` )) TYPE=MyISAM;";
    $this->addQuery($sql);
    $sql = "CREATE TABLE `examen_labo` (" .
          "\n`examen_labo_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ," .
          "\n`catalogue_labo_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 ," .
          "\n`identifiant` VARCHAR(255) NOT NULL ," .
          "\n`libelle` VARCHAR(255) NOT NULL," .
          "\n`type` ENUM('bool','num','str') NOT NULL DEFAULT 'num' ," .
          "\n`unite` VARCHAR(255) DEFAULT NULL," .
          "\n`min` FLOAT DEFAULT NULL," .
          "\n`max` FLOAT DEFAULT NULL," .
          "\nPRIMARY KEY ( `examen_labo_id` ) ," .
          "\nINDEX ( `catalogue_labo_id` )) TYPE=MyISAM;";
    $this->addQuery($sql);

    $this->makeRevision("0.1");
    $sql = "CREATE TABLE `pack_examens_labo` (" .
          "\n`pack_examens_labo_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ," .
          "\n`function_id` INT(11) UNSIGNED DEFAULT NULL ," .
          "\n`libelle` VARCHAR(255) NOT NULL," .
          "\nPRIMARY KEY ( `pack_examens_labo_id` ) ," .
          "\nINDEX ( `function_id` )) TYPE=MyISAM;";
    $this->addQuery($sql);
    $sql = "CREATE TABLE `pack_item_examen_labo` (" .
          "\n`pack_item_examen_labo_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ," .
          "\n`catalogue_labo_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 ," .
          "\n`pack_examens_labo_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 ," .
          "\n`examen_labo_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 ," .
          "\nPRIMARY KEY ( `pack_item_examen_labo_id` ) ," .
          "\nINDEX ( `pack_examens_labo_id` ) ," .
          "\nINDEX ( `examen_labo_id` )" .
          ") TYPE=MyISAM;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.11");
    $sql = "ALTER TABLE `pack_item_examen_labo` DROP `catalogue_labo_id`";
    $this->addQuery($sql);
    $sql = "CREATE TABLE `prescription_labo` (" .
          "\n`prescription_labo_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ," .
          "\n`consultation_id` INT(11) UNSIGNED DEFAULT NULL ," .
          "\nPRIMARY KEY ( `prescription_labo_id` ) ," .
          "\nINDEX ( `consultation_id` )) TYPE=MyISAM;";
    $this->addQuery($sql);
    $sql = "CREATE TABLE `prescription_labo_examen` (" .
          "\n`prescription_labo_examen_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ," .
          "\n`prescription_labo_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 ," .
          "\n`examen_labo_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 ," .
          "\nPRIMARY KEY ( `prescription_labo_examen_id` ) ," .
          "\nINDEX ( `prescription_labo_id` ) ," .
          "\nINDEX ( `examen_labo_id` )" .
          ") TYPE=MyISAM;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.12");
    $sql = "ALTER TABLE `prescription_labo`" .
            "\nCHANGE `consultation_id` `patient_id` INT( 11 ) UNSIGNED NOT NULL DEFAULT 0;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `prescription_labo`" .
            "\nADD `praticien_id` INT( 11 ) UNSIGNED NOT NULL DEFAULT 0 AFTER `patient_id`;";
    $this->addQuery($sql);
    $sql = "ALTER TABLE `prescription_labo`" .
            "\nADD `date` DATETIME DEFAULT NULL;";
    $this->addQuery($sql);
    
    $this->makeRevision("0.13");
    $sql = "ALTER TABLE `prescription_labo_examen`" .
            "\nADD `resultat` VARCHAR( 255 ) DEFAULT NULL," .
            "\nADD `date` DATETIME DEFAULT NULL," .
            "\nADD `commentaire` TEXT DEFAULT NULL;";
    $this->addQuery($sql);
    
    $this->mod_version = "0.14";
  }
}
?>