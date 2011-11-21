<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage hl7
 * @version $Revision:$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CSetuphl7 extends CSetup {
  function insertTableEntry($number, $code_hl7_from, $code_hl7_to, $code_mb_from, $code_mb_to, $description) {
    $description = $this->ds->escape($description);
    
    $query = "INSERT INTO `hl7v2`.`table_entry` (
              `table_entry_id`, `number`, `code_hl7_from`, `code_hl7_to`, `code_mb_from`, `code_mb_to`, `description`, `user`
              ) VALUES (
                NULL , '$number', '$code_hl7_from', '$code_hl7_to', '$code_mb_from', '$code_mb_to', '$description', '1'
              );";
    
    $this->addQuery($query, false, "hl7v2");
  }
  
  function updateTableEntry($number, $update, $where) {
    foreach ($update as $field => $value) {
      $set[] = "`$field` = '$value'";
    }
    
    $and = "";
    foreach ($where as $field => $value) {
      $and .= "AND `$field` = '$value' ";
    }
    $query = "UPDATE `hl7v2`.`table_entry`
              SET ".implode(", ", $set)."
              WHERE `number` = '$number'
              $and;";

    $this->addQuery($query, false, "hl7v2");
    
  }
  
  function __construct() {
    parent::__construct();
    
    $this->mod_name = "hl7";
    $this->makeRevision("all");
    
    $this->makeRevision("0.01");
       
    function checkHL7v2Tables() {
      $dshl7 = CSQLDataSource::get("hl7v2", true);
    
      if (!$dshl7 || !$dshl7->loadTable("table_entry")) {
        CAppUI::setMsg("CHL7v2Tables-missing", UI_MSG_ERROR);
        return false;
      }
      
      return true;
    }
    $this->addFunction("checkHL7v2Tables");
       
    $this->makeRevision("0.02");
  
    $query = "ALTER TABLE `table_description` 
              ADD `user` ENUM ('0','1') NOT NULL DEFAULT '0';";
    $this->addQuery($query, false, "hl7v2");
    
    // Gestion du mode de placement en psychiatrie
    $query = "INSERT INTO `hl7v2`.`table_description` (
              `table_description_id`, `number`, `description`, `user`
              ) VALUES (
                NULL , '9000', 'Admit Reason (Psychiatrie)', '1'
              );";
    $this->addQuery($query, false, "hl7v2");
    
    $this->makeRevision("0.03");
    
    $query = "ALTER TABLE `hl7v2`.`table_entry` DROP INDEX `number_code_hl7` ,
                ADD INDEX `number_code_hl7` ( `number` , `code_hl7_from` );";
    $this->addQuery($query, false, "hl7v2");
    
    // Table - 0001
    // F - Female
    $set = array( 
      "code_hl7_to"   => "F",
      "code_mb_from"  => "f",
      "code_mb_to"    => "f"
    );
    $and = array(
      "code_hl7_from" => "F"
    );
    $this->updateTableEntry("1", $set, $and);
    // M - Male
    $set = array( 
      "code_hl7_to"   => "M",
      "code_mb_from"  => "m",
      "code_mb_to"    => "m"
    );
    $and = array(
      "code_hl7_from" => "M"
    );
    $this->updateTableEntry("1", $set, $and);
    // O - Other
    $set = array(
      "code_mb_to"    => "m"
    );
    $and = array(
      "code_hl7_from" => "O"
    );
    $this->updateTableEntry("1", $set, $and);
    // U - Unknown
    $set = array(
      "code_mb_to"    => "m"
    );
    $and = array(
      "code_hl7_from" => "U"
    );
    $this->updateTableEntry("1", $set, $and);
    // A - Ambiguous  
    $set = array(
      "code_mb_to"    => "m"
    );
    $and = array(
      "code_hl7_from" => "A"
    );
    $this->updateTableEntry("1", $set, $and);
    // N - Not applicable
    $set = array(
      "code_mb_to"    => "m"
    );
    $and = array(
      "code_hl7_from" => "N"
    );
    $this->updateTableEntry("1", $set, $and);

    // Table 0004 - Patient Class
    // E - Emergency - Passage aux Urgences - Arrivée aux urgences
    $set = array(
      "code_hl7_to"   => "E",
      "code_mb_from"  => "urg",
      "code_mb_to"    => "urg"
    );
    $and = array(
      "code_hl7_from" => "E"
    );
    $this->updateTableEntry("4", $set, $and);
    // I - Inpatient - Hospitalisation
    $set = array(
      "code_hl7_to"   => "I",
      "code_mb_from"  => "urg",
      "code_mb_to"    => "urg"
    );
    $and = array(
      "code_hl7_from" => "I"
    );
    $this->updateTableEntry("4", $set, $and);
    $this->insertTableEntry("4", null, "I", "ssr", null, "Inpatient");
    $this->insertTableEntry("4", null, "I", "psy", null, "Inpatient");
    // O - Outpatient - Actes et consultation externe
    $set = array(
      "code_hl7_to"   => "O",
      "code_mb_from"  => "ambu",
      "code_mb_to"    => "ambu"
    );
    $and = array(
      "code_hl7_from" => "O"
    );
    $this->updateTableEntry("4", $set, $and);
    $this->insertTableEntry("4", null, "O", "exte", null, "Outpatient");
    $this->insertTableEntry("4", null, "O", "consult", null, "Outpatient");
    // R - Recurring patient - Séances
    $set = array(
      "code_hl7_to"   => "R",
      "code_mb_from"  => "seances",
      "code_mb_to"    => "seances"
    );
    $and = array(
      "code_hl7_from" => "R"
    );
    $this->updateTableEntry("4", $set, $and);
    
    // Table 0032 - Charge price indicator
    // 03 - Hospitalisation complète
    $set = array(
      "code_hl7_to"   => "03",
      "code_mb_from"  => "comp",
      "code_mb_to"    => "comp"
    );
    $and = array(
      "code_hl7_from" => "03"
    );
    $this->updateTableEntry("32", $set, $and);
    // 07 - Consultations, soins externes
    $set = array(
      "code_hl7_to"   => "07",
      "code_mb_from"  => "consult",
      "code_mb_to"    => "consult"
    );
    $and = array(
      "code_hl7_from" => "07"
    );
    $this->updateTableEntry("32", $set, $and);
    // 10 - Accueil des urgences
    $set = array(
      "code_hl7_to"   => "10",
      "code_mb_from"  => "urg",
      "code_mb_to"    => "urg"
    );
    $and = array(
      "code_hl7_from" => "10"
    );
    $this->updateTableEntry("32", $set, $and);

    // Table - 9000
    // HL  - Hospitalisation libre
    $this->insertTableEntry("9000", "HL", "HL", "libre", "libre", "Hospitalisation libre");
    // HO  - Placement d'office
    $this->insertTableEntry("9000", "HO", "HO", "office", "office", "Placement d'office");
    // HDT - Hospitalisation à la demande d'un tiers
    $this->insertTableEntry("9000", "HDT", "HDT", "tiers", "tiers", "Hospitalisation à la demande d'un tiers");
    
    // Table - 0430
    // 0 - Police
    $this->insertTableEntry("430", "0", "0", "fo", "fo", "Police");
    // 1 - SAMU, SMUR terrestre
    $this->insertTableEntry("430", "1", "1", "smur", "smur", "SAMU, SMUR terrestre");
    // 2 - Ambulance publique
    $this->insertTableEntry("430", "2", "2", "ambu", "ambu", "Ambulance publique");
    // 3 - Ambulance privée
    $this->insertTableEntry("430", null, "3", "ambu", null, "Ambulance privée");
    // 4 - Taxi
    $this->insertTableEntry("430", "4", "4", "perso_taxi", "perso_taxi", "Taxi");
    // 5 - Moyens personnels
    $this->insertTableEntry("430", "5", "5", "perso", "perso", "Moyens personnels");
    // 6 - SAMU, SMUR hélicoptère
    $this->insertTableEntry("430", "6", "6", "heli", "heli", "SAMU, SMUR hélicoptère");
    // 7 - Pompier
    $this->insertTableEntry("430", "7", "7", "vsab", "vsab", "Pompier");
    // 8 - VSL
    $this->insertTableEntry("430", null, "8", "ambu_vsl", null, "VSL");
    // 9 - Autre
    $this->insertTableEntry("430", null, "9", "perso", null, "Autre");

    $this->makeRevision("0.04");
    
    $query = "CREATE TABLE `hl7_config` (
                `hl7_config_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
                `assigning_authority_namespace_id` VARCHAR (255),
                `assigning_authority_universal_id` VARCHAR (255),
                `assigning_authority_universal_type_id` VARCHAR (255),
                `sender_id` INT (11) UNSIGNED,
                `sender_class` ENUM ('CSenderFTP','CSenderSOAP')
              ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `hl7_config` 
              ADD INDEX (`sender_id`);";
    $this->addQuery($query);
    
    $this->mod_version = "0.05";
    
    
    $query = "SHOW TABLES LIKE 'table_description'";
    $this->addDatasource("hl7v2", $query);
  }
}

?>