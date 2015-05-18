<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage hl7
 * @version $Revision:$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CSetuphl7 extends CSetup {
  function insertTableEntry($number, $code_hl7_from, $code_hl7_to, $code_mb_from, $code_mb_to, $description, $user = 1) {
    $description = $this->ds->escape($description);
    
    $code_hl7_from = ($code_hl7_from === null) ? "NULL" : "'$code_hl7_from'";
    $code_hl7_to   = ($code_hl7_to === null)   ? "NULL" : "'$code_hl7_to'";
    $code_mb_from  = ($code_mb_from === null)  ? "NULL" : "'$code_mb_from'";
    $code_mb_to    = ($code_mb_to === null)    ? "NULL" : "'$code_mb_to'";
    
    
    $query = "INSERT INTO `table_entry` (
              `table_entry_id`, `number`, `code_hl7_from`, `code_hl7_to`, `code_mb_from`, `code_mb_to`, `description`, `user`
              ) VALUES (
                NULL , '$number', $code_hl7_from, $code_hl7_to, $code_mb_from, $code_mb_to, '$description', '$user'
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
    $query = "UPDATE `table_entry`
              SET ".implode(", ", $set)."
              WHERE `number` = '$number'
              $and;";

    $this->addQuery($query, false, "hl7v2");
    
  }
  
  function deleteTableEntry($number, $where) {
    $and = "";
    foreach ($where as $field => $value) {
      $and .= "AND `$field` = '$value' ";
    }
    
    $query = "DELETE FROM `table_entry`
              WHERE `number` = '$number'
              $and;";

    $this->addQuery($query, false, "hl7v2");
  }

  function updateTableSource($nameTable) {
    $query = "UPDATE $nameTable
                SET $nameTable.name = Replace(name, 'CReceiverIHE', 'CReceiverHL7v2')
                WHERE $nameTable.name LIKE 'CReceiverIHE-%';";
    $this->addQuery($query);
  }

  /**
   * Check HL7v2 tables presence
   *
   * @return bool
   */
  protected function checkHL7v2Tables() {
    $dshl7 = CSQLDataSource::get("hl7v2", true);

    if (!$dshl7 || !$dshl7->loadTable("table_entry")) {
      CAppUI::setMsg("CHL7v2Tables-missing", UI_MSG_ERROR);
      return false;
    }

    return true;
  }
  
  function __construct() {
    parent::__construct();
    
    $this->mod_name = "hl7";
    $this->makeRevision("all");
    
    $this->makeRevision("0.01");

    $this->addMethod("checkHL7v2Tables");
       
    $this->makeRevision("0.02");
  
    $query = "ALTER TABLE `table_description` 
                ADD `user` ENUM ('0','1') NOT NULL DEFAULT '0';";
    $this->addQuery($query, true, "hl7v2");
    
    // Gestion du mode de placement en psychiatrie
    $query = "INSERT INTO `table_description` (
              `table_description_id`, `number`, `description`, `user`
              ) VALUES (
                NULL , '9000', 'Admit Reason (Psychiatrie)', '1'
              );";
    $this->addQuery($query, false, "hl7v2");
    
    $this->makeRevision("0.03");
    
    $query = "ALTER TABLE `table_entry`
                DROP INDEX `number_code_hl7` ,
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
      "code_mb_from"  => "comp",
      "code_mb_to"    => "comp"
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
    // 03 - Hospi. complète
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
    
    $this->makeRevision("0.05");
    
    // Table - 0063
    // Collègue
    $set = array(
      "code_hl7_to"   => "ASC",
      "code_mb_from"  => "collegue",
      "code_mb_to"    => "collegue"
    );
    $and = array(
      "code_hl7_from" => "ASC"
    );
    $this->updateTableEntry("63", $set, $and);
    // Frère
    $set = array(
      "code_hl7_to"   => "BRO",
      "code_mb_from"  => "frere",
      "code_mb_to"    => "frere"
    );
    $and = array(
      "code_hl7_from" => "BRO"
    );
    $this->updateTableEntry("63", $set, $and);
    // Enfant
    $set = array(
      "code_hl7_to"   => "CHD",
      "code_mb_from"  => "enfant",
      "code_mb_to"    => "enfant"
    );
    $and = array(
      "code_hl7_from" => "CHD"
    );
    $this->updateTableEntry("63", $set, $and);
    // Frère
    $set = array(
      "code_hl7_to"   => "DOM",
      "code_mb_from"  => "compagnon",
      "code_mb_to"    => "compagnon"
    );
    $and = array(
      "code_hl7_from" => "DOM"
    );
    $this->updateTableEntry("63", $set, $and);
    // Employé
    $set = array(
      "code_hl7_to"   => "EME",
      "code_mb_from"  => "employe",
      "code_mb_to"    => "employe"
    );
    $and = array(
      "code_hl7_from" => "EME"
    );
    $this->updateTableEntry("63", $set, $and);
    // Employeur
    $set = array(
      "code_hl7_to"   => "EMR",
      "code_mb_from"  => "employeur",
      "code_mb_to"    => "employeur"
    );
    $and = array(
      "code_hl7_from" => "EMR"
    );
    $this->updateTableEntry("63", $set, $and);
    // Proche
    $set = array(
      "code_hl7_to"   => "EXF",
      "code_mb_from"  => "proche",
      "code_mb_to"    => "proche"
    );
    $and = array(
      "code_hl7_from" => "EXF"
    );
    $this->updateTableEntry("63", $set, $and);
    // Enfant adoptif
    $set = array(
      "code_hl7_to"   => "FCH",
      "code_mb_from"  => "enfant_adoptif",
      "code_mb_to"    => "enfant_adoptif"
    );
    $and = array(
      "code_hl7_from" => "FCH"
    );
    $this->updateTableEntry("63", $set, $and);
    // Ami
    $set = array(
      "code_hl7_to"   => "FND",
      "code_mb_from"  => "ami",
      "code_mb_to"    => "ami"
    );
    $and = array(
      "code_hl7_from" => "FND"
    );
    $this->updateTableEntry("63", $set, $and);
    // Père
    $set = array(
      "code_hl7_to"   => "FTH",
      "code_mb_from"  => "pere",
      "code_mb_to"    => "pere"
    );
    $and = array(
      "code_hl7_from" => "FTH"
    );
    $this->updateTableEntry("63", $set, $and);
    // Petits-enfants
    $set = array(
      "code_hl7_to"   => "GCH",
      "code_mb_from"  => "petits_enfants",
      "code_mb_to"    => "petits_enfants"
    );
    $and = array(
      "code_hl7_from" => "GCH"
    );
    $this->updateTableEntry("63", $set, $and);
    // Tuteur
    $set = array(
      "code_hl7_to"   => "GRD",
      "code_mb_from"  => "tuteur",
      "code_mb_to"    => "tuteur"
    );
    $and = array(
      "code_hl7_from" => "GRD"
    );
    $this->updateTableEntry("63", $set, $and);
    // Mère
    $set = array(
      "code_hl7_to"   => "MTH",
      "code_mb_from"  => "mere",
      "code_mb_to"    => "mere"
    );
    $and = array(
      "code_hl7_from" => "MTH"
    );
    $this->updateTableEntry("63", $set, $and);
    // Autre
    $set = array(
      "code_hl7_to"   => "OTH",
      "code_mb_from"  => "autre",
      "code_mb_to"    => "autre"
    );
    $and = array(
      "code_hl7_from" => "OTH"
    );
    $this->updateTableEntry("63", $set, $and);
    // Propriétaire
    $set = array(
      "code_hl7_to"   => "OWN",
      "code_mb_from"  => "proprietaire",
      "code_mb_to"    => "proprietaire"
    );
    $and = array(
      "code_hl7_from" => "OWN"
    );
    $this->updateTableEntry("63", $set, $and);
    // Beau-fils
    $set = array(
      "code_hl7_to"   => "SCH",
      "code_mb_from"  => "beau_fils",
      "code_mb_to"    => "beau_fils"
    );
    $and = array(
      "code_hl7_from" => "SCH"
    );
    $this->updateTableEntry("63", $set, $and);
    // Soeur
    $set = array(
      "code_hl7_to"   => "SIS",
      "code_mb_from"  => "soeur",
      "code_mb_to"    => "soeur"
    );
    $and = array(
      "code_hl7_from" => "SIS"
    );
    $this->updateTableEntry("63", $set, $and);
    // Époux
    $set = array(
      "code_hl7_to"   => "SPO",
      "code_mb_from"  => "epoux",
      "code_mb_to"    => "epoux"
    );
    $and = array(
      "code_hl7_from" => "SPO"
    );
    $this->updateTableEntry("63", $set, $and);
    // Entraineur
    $set = array(
      "code_hl7_to"   => "TRA",
      "code_mb_from"  => "entraineur",
      "code_mb_to"    => "entraineur"
    );
    $and = array(
      "code_hl7_from" => "TRA"
    );
    $this->updateTableEntry("63", $set, $and);
    
    // Table - 0131
    // Personne à prévenir
    $set = array(
      "code_hl7_to"   => "C",
      "code_mb_from"  => "prevenir",
      "code_mb_to"    => "prevenir"
    );
    $and = array(
      "code_hl7_from" => "C"
    );
    $this->updateTableEntry("131", $set, $and);
    // Employeur
    $set = array(
      "code_hl7_to"   => "E",
      "code_mb_from"  => "employeur",
      "code_mb_to"    => "employeur"
    );
    $and = array(
      "code_hl7_from" => "E"
    );
    $this->updateTableEntry("131", $set, $and);
    // Assurance
    $set = array(
      "code_hl7_to"   => "I",
      "code_mb_from"  => "assurance",
      "code_mb_to"    => "assurance"
    );
    $and = array(
      "code_hl7_from" => "I"
    );
    $this->updateTableEntry("131", $set, $and);
    // Autre
    $set = array(
      "code_hl7_to"   => "O",
      "code_mb_from"  => "autre",
      "code_mb_to"    => "autre"
    );
    $and = array(
      "code_hl7_from" => "O"
    );
    $this->updateTableEntry("131", $set, $and);
    // Inconnu
    $set = array(
      "code_hl7_to"   => "U",
      "code_mb_from"  => "inconnu",
      "code_mb_to"    => "inconnu"
    );
    $and = array(
      "code_hl7_from" => "U"
    );
    $this->updateTableEntry("131", $set, $and);
    // Personne de confiance
    $this->insertTableEntry("131", "K", "K", "confiance", "confiance", "Personne de confiance");
    
    $this->makeRevision("0.06");
    $query = "CREATE TABLE `source_mllp` (
              `source_mllp_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
              `port` INT (11) DEFAULT '7001',
              `name` VARCHAR (255) NOT NULL,
              `role` ENUM ('prod','qualif') NOT NULL DEFAULT 'qualif',
              `host` TEXT NOT NULL,
              `user` VARCHAR (255),
              `password` VARCHAR (50),
              `type_echange` VARCHAR (255),
              `active` ENUM ('0','1') NOT NULL DEFAULT '1',
              `loggable` ENUM ('0','1') NOT NULL DEFAULT '1'
    ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);

    $this->makeRevision("0.07");
    
    $this->insertTableEntry("399", "FRA", "FRA", "FRA", "FRA", "Française");
    
    $this->makeRevision("0.08");
    
    // Circonstance de sortie
    $this->insertTableEntry("112", "2", "2", "2", "2", "Mesures disciplinaires");
    $this->insertTableEntry("112", "3", "3", "3", "3", "Décision médicale (valeur par défaut");
    $this->insertTableEntry("112", "4", "4", "4", "4", "Contre avis médicale");
    $this->insertTableEntry("112", "5", "5", "5", "5", "En attente d'examen");
    $this->insertTableEntry("112", "6", "6", "6", "6", "Convenances personnelles");
    $this->insertTableEntry("112", "R", "R", "R", "R", "Essai (contexte psychiatrique)");
    $this->insertTableEntry("112", "E", "E", "E", "E", "Evasion");
    $this->insertTableEntry("112", "F", "F", "F", "F", "Fugue");
    
    $this->makeRevision("0.09");
    
    // Type d'activité, mode de traitement
    // Hospi. complète
    $this->insertTableEntry("32", "CM", "CM", "comp_m", "comp_m", "Hospi. complète en médecine");
    $this->insertTableEntry("32", "CM", "CM", "comp_c", "comp_c", "Hospi. complète en chirurgie");
    $this->insertTableEntry("32", "MATER", "MATER", "comp_o", "comp_o", "Hospi. complète en obstétrique");
    // Ambu
    $this->insertTableEntry("32", "AMBU", "AMBU", "ambu_m", "ambu_m", "Ambulatoire en médecine");
    $this->insertTableEntry("32", "AMBU", "AMBU", "ambu_c", "ambu_c", "Ambulatoire en chirurgie");
    $this->insertTableEntry("32", "MATERAMBU", "MATERAMBU", "ambu_o", "ambu_o", "Ambulatoire en obstétrique");
    // Externe
    $this->insertTableEntry("32", "EXT", "EXT", "exte_m", "exte_m", "Soins externes");
    $this->insertTableEntry("32", "EXT", "EXT", "exte_c", "exte_c", "Soins externes chirugicaux");
    $this->insertTableEntry("32", "MATEREXT", "MATEREXT", "exte_o", "exte_o", "Soins externes ");
    /// Seances
    $this->insertTableEntry("32", "CHIMIO", "CHIMIO", "seances_m", "seances_m", "Séance de chimiothérapie");
    $this->insertTableEntry("32", "CHIMIO", "CHIMIO", "seances_c", "seances_c", "Séance de chimiothérapie");
    $this->insertTableEntry("32", "CHIMIO", "CHIMIO", "seances_o", "seances_o", "Séance de chimiothérapie");
    // Urgence
    $this->insertTableEntry("32", "URGENCE", "URGENCE", "urg_m", "urg_m", "Passage aux ugences médicales sans hosp.");
    $this->insertTableEntry("32", "URGENCE", "URGENCE", "urg_c", "urg_c", "Passage aux ugences chirurgicales sans hosp.");
    $this->insertTableEntry("32", "URGENCE", "URGENCE", "urg_o", "urg_o", "Passage aux ugences");
    
    $this->makeRevision("0.10");
    
    // Ambu
    $set = array(
      "code_hl7_to"   => "I",
      "code_hl7_from" => "I",
      "code_mb_to"    => "ambu"
    );
    $and = array(
      "code_mb_from" => "ambu"
    );
    $this->updateTableEntry("4", $set, $and);
    
    $this->makeRevision("0.11");
    
    // Type d'activité, mode de traitement
    // Hospi. complète
    $this->insertTableEntry("32", "CM", "CM", "comp", "comp", "Hospi. complète en médecine");
    // Ambu
    $this->insertTableEntry("32", "MATERAMBU", "MATERAMBU", "ambu", "ambu", "Ambulatoire en obstétrique");
    // Externe
    $this->insertTableEntry("32", "EXT", "EXT", "exte", "exte", "Soins externes chirugicaux");
    /// Seances
    $this->insertTableEntry("32", "CHIMIO", "CHIMIO", "seances", "seances", "Séance de chimiothérapie");
    // Urgence
    $this->insertTableEntry("32", "URGENCE", "URGENCE", "urg", "urg", "Passage aux ugences chirurgicales sans hosp.");
    
    $this->makeRevision("0.12");
    
    $set = array(
      "code_hl7_to"   => "AMBU",
      "code_hl7_from" => "AMBU",
    );
    $and = array(
      "code_mb_from" => "ambu"
    );
    $this->updateTableEntry("32", $set, $and);
    
    $this->makeRevision("0.13");
    
    $query = "CREATE TABLE `sender_mllp` (
                `sender_mllp_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
                `user_id` INT (11) UNSIGNED,
                `nom` VARCHAR (255) NOT NULL,
                `libelle` VARCHAR (255),
                `group_id` INT (11) UNSIGNED NOT NULL,
                `actif` ENUM ('0','1') NOT NULL DEFAULT '0'
              ) /*! ENGINE=MyISAM */;";
    $this->addQuery($query);
    
    $query = "ALTER TABLE `sender_mllp` 
                ADD INDEX (`user_id`),
                ADD INDEX (`group_id`);";
    $this->addQuery($query);
    
    $this->makeRevision("0.14");
    
    $query = "ALTER TABLE `hl7_config` 
                CHANGE `sender_class` `sender_class` ENUM ('CSenderFTP','CSenderSOAP','CSenderMLLP');";
    $this->addQuery($query);
    
    $this->makeRevision("0.15");
    
    // Character sets
    // UTF-8
    $set = array(
      "code_hl7_to"   => "UNICODE UTF-8",
      "code_mb_from"  => "UTF-8",
      "code_mb_to"    => "UTF-8"
    );
    $and = array(
      "code_hl7_from" => "UNICODE UTF-8"
    );
    $this->updateTableEntry("211", $set, $and);
    // ISO-8859-1
    $set = array(
      "code_hl7_to"   => "8859/1 ",
      "code_mb_from"  => "ISO-8859-1",
      "code_mb_to"    => "ISO-8859-1"
    );
    $and = array(
      "code_hl7_from" => "8859/1 "
    );
    $this->updateTableEntry("211", $set, $and);
    
    $this->makeRevision("0.16");
    
    $this->makeRevision("0.17");
    
    // Externe
    $and = array(
      "code_hl7_to"  => "O",
      "code_mb_from" => "exte"
    );
    $this->deleteTableEntry("4", $and);
    
    $set = array(
      "code_hl7_to"   => "O",
      "code_hl7_from" => "O",
      "code_mb_from"  => "exte",
      "code_mb_to"    => "exte"
    );
    $and = array(
      "code_hl7_from" => "I",
      "code_mb_from"  => "ambu"
    );
    $this->updateTableEntry("4", $set, $and);
    
    // Ambu
    $this->insertTableEntry("4", null, "I", "ambu", null, "Inpatient");
    
    $this->makeRevision("0.18");
    
    // Gestion du mode de placement en psychiatrie
    $query = "INSERT INTO `table_description` (
              `table_description_id`, `number`, `description`, `user`
              ) VALUES (
                NULL , '9001', 'Mode de sortie PMSI', '1'
              );";
    $this->addQuery($query, false, "hl7v2");
    
    // Transfert
    $this->insertTableEntry("9001", "7", "7", "transfert", "transfert", "Transfert");
    // Mutation
    $this->insertTableEntry("9001", "6", "6", "mutation", "mutation", "Mutation (même hopital)");
    // Deces
    $this->insertTableEntry("9001", "9", "9", "deces", "deces", "Décès");
    // Normal
    $this->insertTableEntry("9001", "5", "5", "normal", "normal", "Sorti à l'essai");
    
    $this->makeRevision("0.19");
    
    $query = "ALTER TABLE `hl7_config` 
                ADD `handle_mode` ENUM ('normal','simple') DEFAULT 'normal';";
    $this->addQuery($query);
    
    $this->makeRevision("0.20");
    
    $query = "ALTER TABLE `hl7_config` 
                CHANGE `sender_class` `sender_class` VARCHAR (80);";
    $this->addQuery($query);
    
    $this->makeRevision("0.21");
    
    $query = "ALTER TABLE `hl7_config` 
                ADD `get_NDA` ENUM ('PID_18','PV1_19') DEFAULT 'PID_18';";
    $this->addQuery($query);
    
    $this->makeRevision("0.22");
    
    $query = "ALTER TABLE `hl7_config` 
              ADD `handle_PV1_10` ENUM ('discipline','service') DEFAULT 'discipline',
              CHANGE `get_NDA` `handle_NDA` ENUM ('PID_18','PV1_19') DEFAULT 'PID_18';";
    $this->addQuery($query);
    
    $this->makeRevision("0.23");
    
    $query = "ALTER TABLE `sender_mllp` 
                ADD `save_unsupported_message` ENUM ('0','1') DEFAULT '1',
                ADD `create_ack_file` ENUM ('0','1') DEFAULT '1';";
    $this->addQuery($query);
    
    $this->makeRevision("0.24");
    
    $this->makeRevision("0.25");
    
    $query = "ALTER TABLE `hl7_config` 
                ADD `encoding` ENUM ('UTF-8','ISO-8859-1') DEFAULT 'UTF-8';";
    $this->addQuery($query);
    
    $this->makeRevision("0.26");
    
    // Table - 0063
    // Ascendant
    $this->insertTableEntry("63", "DAN", "DAN", "ascendant", "ascendant", "Ascendant");
    
    // Collatéral
    $this->insertTableEntry("63", "COL", "COL", "colateral", "colateral", "Collatéral");
    
    // Conjoint
    $this->insertTableEntry("63", "CON", "CON", "conjoint", "conjoint", "Conjoint");
    
    // Directeur
    $this->insertTableEntry("63", "DIR", "DIR", "directeur", "directeur", "Directeur");
    
    // Divers
    $this->insertTableEntry("63", "DIV", "DIV", "divers", "divers", "Divers");
    
    // Grand-parent
    $this->insertTableEntry("63", "GRP", "GRP", "grand_parent", "grand_parent", "Grand-parent");
    
    $this->makeRevision("0.27");
    
    $query = "ALTER TABLE `hl7_config` 
                ADD `handle_NSS` ENUM ('PID_3','PID_19') DEFAULT 'PID_3';";
    $this->addQuery($query);
    
    $this->makeRevision("0.28");
    
    $query = "ALTER TABLE `hl7_config` 
                ADD `iti30_option_merge` ENUM ('0','1') DEFAULT '1',
                ADD `iti30_option_link_unlink` ENUM ('0','1') DEFAULT '0',
                ADD `iti31_in_outpatient_emanagement` ENUM ('0','1') DEFAULT '1',
                ADD `iti31_pending_event_management` ENUM ('0','1') DEFAULT '0',
                ADD `iti31_advanced_encounter_management` ENUM ('0','1') DEFAULT '1',
                ADD `iti31_temporary_patient_transfer_tracking` ENUM ('0','1') DEFAULT '0',
                ADD `iti31_historic_movement` ENUM ('0','1') DEFAULT '1';";
    $this->addQuery($query);
    
    $this->makeRevision("0.29");
    $query = "ALTER TABLE `source_mllp` 
              ADD `ssl_enabled` ENUM ('0','1') NOT NULL DEFAULT '0',
              ADD `ssl_certificate` VARCHAR (255),
              ADD `ssl_passphrase` VARCHAR (255);";
    $this->addQuery($query);
    
    $this->makeRevision("0.30");
    $query = "ALTER TABLE `hl7_config` 
              ADD `strict_segment_terminator` ENUM ('0','1') DEFAULT '0';";
    $this->addQuery($query);
    
    $this->makeRevision("0.31");
    $query = "ALTER TABLE `sender_mllp` 
                ADD `delete_file` ENUM ('0','1') DEFAULT '1';";
    $this->addQuery($query);
    
    $this->makeRevision("0.32");
    $query = "ALTER TABLE `hl7_config` 
                ADD `handle_PV1_14` ENUM ('admit_source','ZFM') DEFAULT 'admit_source',
                ADD `handle_PV1_36` ENUM ('discharge_disposition','ZFM') DEFAULT 'discharge_disposition';";
    $this->addQuery($query);
    
    $this->makeRevision("0.33");
    $query = "ALTER TABLE `hl7_config` 
                ADD `purge_idex_movements` ENUM ('0','1') NOT NULL DEFAULT '0';";
    $this->addQuery($query);
    
    $this->makeRevision("0.34");
    
    /* Remise à niveau des types d'hospitalisation */
    
    // Suppression
    $and = array(
      "code_mb_from" => "exte"
    );
    $this->deleteTableEntry("4", $and);
    $and = array(
      "code_mb_from" => "seances"
    );
    $this->deleteTableEntry("4", $and);
    $and = array(
      "code_mb_from" => "comp"
    );
    $this->deleteTableEntry("4", $and);
    $and = array(
      "code_mb_from" => "ambu"
    );
    $this->deleteTableEntry("4", $and);
    $and = array(
      "code_mb_from" => "urg"
    );
    $this->deleteTableEntry("4", $and);
    $and = array(
      "code_mb_from" => "consult"
    );
    $this->deleteTableEntry("4", $and);
    $and = array(
      "code_mb_from" => "psy"
    );
    $this->deleteTableEntry("4", $and);
    $and = array(
      "code_mb_from" => "ssr"
    );
    $this->deleteTableEntry("4", $and);
    
    // Table 0004 - Patient Class
    // E - Emergency - Passage aux Urgences - Arrivée aux urgences
    $this->insertTableEntry("4", "E", "E", "urg", "urg", "Emergency", 0);
    
    // I - Inpatient - Hospitalisation
    $this->insertTableEntry("4", "I" , "I", "comp", "comp", "Inpatient", 0);
    $this->insertTableEntry("4", null, "I", "ssr" , null  , "Inpatient");
    $this->insertTableEntry("4", null, "I", "psy" , null  , "Inpatient");
    $this->insertTableEntry("4", null, "I", "ambu", null  , "Inpatient");
    
    // O - Outpatient - Actes et consultation externe
    $this->insertTableEntry("4", "O" , "O", "exte"   , "exte", "Outpatient", 0);
    $this->insertTableEntry("4", null, "O", "consult", null  , "Outpatient");
    
    // R - Recurring patient - Séances
    $this->insertTableEntry("4", "R", "R", "seances", "seances", "Recurring patient", 0);
    
    $this->makeRevision("0.35");
    $query = "ALTER TABLE `hl7_config` 
                ADD `repair_patient` ENUM ('0','1') DEFAULT '1',
                ADD `control_date` ENUM ('permissif','strict') DEFAULT 'strict';";
    $this->addQuery($query);
    
    $this->makeRevision("0.36");
    $query = "ALTER TABLE `hl7_config` 
              ADD `handle_PV1_3` ENUM ('name','config_value','idex') DEFAULT 'name';";
    $this->addQuery($query);
    
    $this->addDependency("ihe", "0.26");
    
    $this->makeRevision("0.37");
    $query = "ALTER TABLE `receiver_ihe_config`
                ADD `RAD48_HL7_version` ENUM ('2.1','2.2','2.3','2.3.1','2.4','2.5') DEFAULT '2.5';";
    $this->addQuery($query);
    
    $this->makeRevision("0.38");
    
    $query = "ALTER TABLE `exchange_ihe`
                CHANGE `object_class` `object_class` VARCHAR (80);";
    $this->addQuery($query);
    
    $this->makeRevision("0.39");
    
    $query = "ALTER TABLE `exchange_ihe` 
                ADD `reprocess` TINYINT (4) UNSIGNED DEFAULT '0';";
    $this->addQuery($query);
    
    $this->makeRevision("0.40");
    $query = "ALTER TABLE `receiver_ihe_config` 
                ADD `build_telephone_number` ENUM ('XTN_1','XTN_12') DEFAULT 'XTN_12';";
    $this->addQuery($query);
    
    $this->makeRevision("0.41");
    $query = "ALTER TABLE `hl7_config` 
                ADD `handle_telephone_number` ENUM ('XTN_1','XTN_12') DEFAULT 'XTN_12';";
    $this->addQuery($query);
    
    $this->makeRevision("0.42");
    $query = "ALTER TABLE `hl7_config` 
                ADD `handle_PID_31` ENUM ('avs','none') DEFAULT 'none';";
    $this->addQuery($query);
    
    $this->makeRevision("0.43");
    
    $query = "ALTER TABLE `receiver_ihe_config` 
                ADD `build_PID_31` ENUM ('avs','none') DEFAULT 'none';";
    $this->addQuery($query);

    $this->makeRevision("0.44");
    $query = "ALTER TABLE `hl7_config`
                ADD `segment_terminator` ENUM ('CR','LF','CRLF')";
    $this->addQuery($query);
    
    $this->makeRevision("0.45");
    $query = "ALTER TABLE `receiver_ihe_config` 
                ADD `build_PID_34` ENUM ('finess','actor') DEFAULT 'finess';";
    $this->addQuery($query);
    
    $this->makeRevision("0.46");
    $query = "ALTER TABLE `receiver_ihe_config` 
                ADD `build_PV2_45` ENUM ('operation','none') DEFAULT 'none';";
    $this->addQuery($query);
    
    $this->makeRevision("0.47");
    $query = "ALTER TABLE `receiver_ihe_config` 
                ADD `build_cellular_phone` ENUM ('PRN','ORN') DEFAULT 'PRN';";
    $this->addQuery($query);
    
    $this->makeRevision("0.48");
    $query = "ALTER TABLE `receiver_ihe_config` 
                ADD `send_first_affectation` ENUM ('A02','Z99') DEFAULT 'Z99';";
    $this->addQuery($query);

    $this->makeRevision("0.49");

    $query = "ALTER TABLE `receiver_ihe_config`
                ADD `ITI21_HL7_version` ENUM ('2.1','2.2','2.3','2.3.1','2.4','2.5') DEFAULT '2.5',
                ADD `ITI22_HL7_version` ENUM ('2.1','2.2','2.3','2.3.1','2.4','2.5') DEFAULT '2.5';";
    $this->addQuery($query);

    $this->makeRevision("0.50");
    $query = "ALTER TABLE `receiver_ihe_config`
                ADD `build_PV1_26` ENUM ('movement_id','none') DEFAULT 'none';";
    $this->addQuery($query);

    $this->makeRevision("0.51");
    $query = "ALTER TABLE `receiver_ihe_config`
                ADD `send_assigning_authority` ENUM ('0','1') DEFAULT '1';";
    $this->addQuery($query);

    $this->makeRevision("0.52");
    $query = "ALTER TABLE `hl7_config`
                ADD `receiving_application` VARCHAR (255),
                ADD `receiving_facility` VARCHAR (255);";
    $this->addQuery($query);

    $this->makeRevision("0.53");
    $query = "ALTER TABLE `hl7_config`
                ADD `handle_PV2_12` ENUM ('libelle','none') DEFAULT 'libelle';";
    $this->addQuery($query);

    $this->makeRevision("0.54");
    $query = "ALTER TABLE `hl7_config`
                ADD `send_assigning_authority` ENUM ('0','1') DEFAULT '1';";
    $this->addQuery($query);

    $this->makeRevision("0.55");
    $query = "ALTER TABLE `hl7_config`
                ADD `send_self_identifier` ENUM ('0','1') DEFAULT '0';";
    $this->addQuery($query);

    $query = "ALTER TABLE `receiver_ihe_config`
                ADD `send_self_identifier` ENUM ('0','1') DEFAULT '0';";
    $this->addQuery($query);
    
    $this->makeRevision("0.56");
    $query = "ALTER TABLE `hl7_config`
                ADD `send_area_local_number` ENUM ('0','1') DEFAULT '0';";
    $this->addQuery($query);

    $this->makeRevision("0.57");
    $query = "ALTER TABLE `source_mllp`
                CHANGE `password` `password` VARCHAR (255),
                ADD `iv` VARCHAR (16) AFTER `password`,
                ADD `iv_passphrase` VARCHAR (16) AFTER `ssl_passphrase`;";
    $this->addQuery($query);

    $this->makeRevision("0.58");
    $query = "ALTER TABLE `hl7_config`
                ADD `handle_PV1_7` ENUM ('0','1') DEFAULT '1';";
    $this->addQuery($query);

    $this->makeRevision("0.59");
    $query = "ALTER TABLE `hl7_config`
                ADD `check_receiving_application_facility` ENUM ('0','1') DEFAULT '0';";
    $this->addQuery($query);

    $this->makeRevision("0.60");
    $query = "ALTER TABLE `hl7_config`
                ADD `handle_PV1_20` ENUM ('old_presta','none') DEFAULT 'none';";
    $this->addQuery($query);

    $this->makeRevision("0.61");
    $query = "ALTER TABLE `receiver_ihe_config`
                ADD `send_update_patient_information` ENUM ('A08','A31') DEFAULT 'A31';";
    $this->addQuery($query);

    $this->makeRevision("0.62");
    $query = "ALTER TABLE `receiver_ihe_config`
                ADD `modification_admit_code` ENUM ('A08','Z99') DEFAULT 'A08';";
    $this->addQuery($query);

    $this->makeRevision("0.63");
    $query = "ALTER TABLE `receiver_ihe_config`
                CHANGE `build_PID_34` `build_PID_34` ENUM ('finess','actor','domain') DEFAULT 'finess';";
    $this->addQuery($query);

    $this->makeRevision("0.64");

    $query = "ALTER TABLE `receiver_ihe_config`
                ADD `country_code` ENUM ('FRA');";
    $this->addQuery($query);

    $query = "ALTER TABLE `hl7_config`
                ADD `country_code` ENUM ('FRA');";
    $this->addQuery($query);

    $this->makeRevision("0.65");

    $query = "ALTER TABLE `receiver_ihe_config`
                CHANGE `country_code` `country_code` ENUM ('FRA','INT');";
    $this->addQuery($query);

    $query = "ALTER TABLE `hl7_config`
                CHANGE `country_code` `country_code` ENUM ('FRA','INT');";
    $this->addQuery($query);

    $this->makeRevision("0.66");

    $query = "ALTER TABLE `receiver_ihe_config`
                ADD `build_other_residence_number` ENUM ('ORN','WPN') DEFAULT 'ORN';";
    $this->addQuery($query);

    $this->makeRevision("0.67");

    $query = "CREATE TABLE `receiver_hl7_v3` (
                `receiver_hl7_v3_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
                `nom` VARCHAR (255) NOT NULL,
                `libelle` VARCHAR (255),
                `group_id` INT (11) UNSIGNED NOT NULL DEFAULT '0',
                `actif` ENUM ('0','1') NOT NULL DEFAULT '0'
              )/*! ENGINE=MyISAM */;";
    $this->addQuery($query);

    $query = "ALTER TABLE `receiver_hl7_v3`
                ADD INDEX (`group_id`);";
    $this->addQuery($query);

    $this->makeRevision("0.68");

    $query = "CREATE TABLE `exchange_hl7v3` (
                `exchange_hl7v3_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
                `identifiant_emetteur` VARCHAR (255),
                `initiateur_id` INT (11) UNSIGNED,
                `group_id` INT (11) UNSIGNED NOT NULL DEFAULT '0',
                `date_production` DATETIME NOT NULL,
                `sender_id` INT (11) UNSIGNED,
                `sender_class` ENUM ('CSenderFTP','CSenderSOAP','CSenderFileSystem'),
                `receiver_id` INT (11) UNSIGNED,
                `type` VARCHAR (255),
                `sous_type` VARCHAR (255),
                `date_echange` DATETIME,
                `message_content_id` INT (11) UNSIGNED,
                `acquittement_content_id` INT (11) UNSIGNED,
                `statut_acquittement` VARCHAR (255),
                `message_valide` ENUM ('0','1') DEFAULT '0',
                `acquittement_valide` ENUM ('0','1') DEFAULT '0',
                `id_permanent` VARCHAR (255),
                `object_id` INT (11) UNSIGNED,
                `object_class` ENUM ('CPatient','CSejour','COperation','CAffectation','CConsultation'),
                `reprocess` TINYINT (4) UNSIGNED DEFAULT '0'
              )/*! ENGINE=MyISAM */;";
    $this->addQuery($query);

    $query = "ALTER TABLE `exchange_hl7v3`
                ADD INDEX (`initiateur_id`),
                ADD INDEX (`group_id`),
                ADD INDEX (`date_production`),
                ADD INDEX (`sender_id`),
                ADD INDEX (`receiver_id`),
                ADD INDEX (`date_echange`),
                ADD INDEX (`message_content_id`),
                ADD INDEX (`acquittement_content_id`),
                ADD INDEX (`object_id`);";
    $this->addQuery($query);

    $this->makeRevision("0.69");

    $query = "ALTER TABLE `receiver_ihe_config`
                ADD `send_change_after_admit` ENUM ('0','1') DEFAULT '1';";
    $this->addQuery($query);

    $this->makeRevision("0.70");
    $this->addDependency("ihe", "0.28");

    $query = "UPDATE user_log
                SET user_log.object_class = 'CReceiverHL7v2'
                WHERE user_log.object_class = 'CReceiverIHE';";
    $this->addQuery($query);

    $query = "UPDATE user_log
                SET user_log.object_class = 'CReceiverHL7v2Config'
                WHERE user_log.object_class = 'CReceiverIHEConfig';";
    $this->addQuery($query);

    $this->makeRevision("0.71");

    if (CModule::getActive("eai")) {
      $query = "UPDATE message_supported
                SET message_supported.object_class = 'CReceiverHL7v2'
                WHERE message_supported.object_class = 'CReceiverIHE';";
      $this->addQuery($query);
    }

    self::updateTableSource("source_file_system");
    self::updateTableSource("source_mllp");

    $this->makeRevision("0.72");

    $query = "ALTER TABLE `receiver_hl7v2`
                ADD `OID` VARCHAR (255);";
    $this->addQuery($query);

    $query = "RENAME TABLE `receiver_hl7_v3`
                TO `receiver_hl7v3`;";
    $this->addQuery($query);

    $query = "ALTER TABLE `receiver_hl7v3`
                CHANGE `receiver_hl7_v3_id` `receiver_hl7v3_id` INT (11) UNSIGNED NOT NULL AUTO_INCREMENT;";
    $this->addQuery($query);

    $query = "ALTER TABLE `receiver_hl7v3`
                ADD `OID` VARCHAR (255);";
    $this->addQuery($query);

    $this->makeRevision("0.73");

    $query = "ALTER TABLE `hl7_config`
                ADD `ignore_fields` VARCHAR (255);";
    $this->addQuery($query);

    $this->makeRevision("0.74");

    $query = "ALTER TABLE `exchange_hl7v3`
                CHANGE `object_class` `object_class` ENUM ('CPatient','CSejour','COperation','CAffectation','CConsultation','CFile','CCompteRendu');";
    $this->addQuery($query);

    $this->makeRevision("0.75");

    $query = "ALTER TABLE `receiver_hl7v2_config`
                ADD `build_PID_19` ENUM ('matricule','none') DEFAULT 'none';";
    $this->addQuery($query);

    $this->makeRevision("0.76");

    $query = "ALTER TABLE `receiver_hl7v2_config`
                ADD `send_actor_identifier` ENUM ('0','1') DEFAULT '0';";
    $this->addQuery($query);

    $this->makeRevision("0.77");

    $query = "ALTER TABLE `receiver_hl7v2_config`
                ADD `build_PV1_11` ENUM ('uf_medicale','none') DEFAULT 'none';";
    $this->addQuery($query);

    $this->makeRevision("0.78");
    $query = "ALTER TABLE `receiver_hl7v2`
                ADD `synchronous` ENUM ('0','1') NOT NULL DEFAULT '1';";
    $this->addQuery($query);

    $this->makeRevision("0.79");
    $query = "ALTER TABLE `receiver_hl7v3`
                ADD `synchronous` ENUM ('0','1') NOT NULL DEFAULT '1';";
    $this->addQuery($query);

    $this->makeRevision("0.80");
    $query = "ALTER TABLE `receiver_hl7v2_config`
                CHANGE `iti31_in_outpatient_emanagement` `iti31_in_outpatient_management` ENUM ('0','1') DEFAULT '1',
                ADD `create_grossesse` ENUM ('0','1') DEFAULT '1';";
    $this->addQuery($query);

    $this->makeRevision("0.81");
    $query = "ALTER TABLE `hl7_config`
                CHANGE `iti31_in_outpatient_emanagement` `iti31_in_outpatient_management` ENUM ('0','1') DEFAULT '1',
                ADD `create_grossesse` ENUM ('0','1') DEFAULT '1';";
    $this->addQuery($query);

    $this->makeRevision("0.82");
    $query = "ALTER TABLE `receiver_hl7v2_config`
                ADD `build_PID_6` ENUM ('nom_naissance','none') DEFAULT 'none';";
    $this->addQuery($query);

    $this->makeRevision("0.83");

    $query = "ALTER TABLE `receiver_hl7v2_config`
                ADD `RAD3_HL7_version` ENUM ('2.1','2.2','2.3','2.3.1','2.4','2.5') DEFAULT '2.5'";
    $this->addQuery($query);

    $this->makeRevision("0.84");
    $query = "ALTER TABLE `exchange_hl7v2`
                CHANGE `object_class` `object_class` ENUM ('CPatient','CSejour','COperation','CAffectation','COperation','CConsultation','CPrescriptionLineElement');";
    $this->addQuery($query);

    $this->makeRevision("0.85");
    $query = "ALTER TABLE `receiver_hl7v2_config`
                ADD `build_PID_11` ENUM ('simple','multiple') DEFAULT 'multiple',
                ADD `build_PID_13` ENUM ('simple','multiple') DEFAULT 'multiple',
                ADD `build_PV1_5` ENUM ('NPA','None') DEFAULT 'NPA',
                ADD `build_PV1_17` ENUM ('praticien','None') DEFAULT 'praticien',
                ADD `build_PV1_19` ENUM ('normal','simple') DEFAULT 'normal';";
    $this->addQuery($query);

    $this->makeRevision("0.86");

    $query = "ALTER TABLE `receiver_hl7v2_config`
                ADD `modification_before_admit` ENUM ('0','1') DEFAULT '1'";
    $this->addQuery($query);

    $this->makeRevision("0.87");

    $query = "ALTER TABLE `receiver_hl7v2_config`
                ADD `build_PID_18` ENUM ('normal','simple') DEFAULT 'normal',
                ADD `send_patient_with_visit` ENUM ('0','1') DEFAULT '0';";
    $this->addQuery($query);

    $this->makeRevision("0.88");

    $query = "ALTER TABLE `receiver_hl7v2_config`
                ADD `build_identifier_authority` ENUM ('normal','PI_AN') DEFAULT 'normal',
                CHANGE `build_PV1_5` `build_PV1_5` ENUM ('NPA','none') DEFAULT 'NPA',
                CHANGE `build_PV1_17` `build_PV1_17` ENUM ('praticien','none') DEFAULT 'praticien';";
    $this->addQuery($query);

    $this->makeRevision("0.89");

    $query = "ALTER TABLE `hl7_config`
                ADD `search_master_IPP` ENUM ('0','1') DEFAULT '0',
                ADD `search_master_NDA` ENUM ('0','1') DEFAULT '0';";
    $this->addQuery($query);

    $this->makeRevision("0.90");

    $query = "ALTER TABLE `hl7_config`
                ADD `handle_PV1_50` ENUM ('sejour_id','none') DEFAULT 'none';";
    $this->addQuery($query);

    $this->makeRevision("0.91");

    $query = "ALTER TABLE `receiver_hl7v2_config`
               ADD `send_patient_with_current_admit` ENUM ('0','1') DEFAULT '0';";
    $this->addQuery($query);

    $this->makeRevision("0.92");

    $query = "ALTER TABLE `hl7_config`
                ADD `bypass_validating` ENUM ('0','1') DEFAULT '0'";
    $this->addQuery($query);

    $this->makeRevision("0.93");

    $query = "ALTER TABLE `receiver_hl7v2_config`
                ADD `mode_identito_vigilance` ENUM ('light','medium','strict') DEFAULT 'light'";
    $this->addQuery($query);

    $this->makeRevision("0.94");

    $query = "ALTER TABLE `receiver_hl7v2_config`
                ADD `send_no_facturable` ENUM ('0','1') DEFAULT '1'";
    $this->addQuery($query);

    $this->makeRevision("0.95");

    $query = "CREATE TABLE `receiver_hl7v3_config` (
                `receiver_hl7v3_config_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
                `object_id` INT (11) UNSIGNED,
                `use_receiver_oid` ENUM ('0','1') DEFAULT '0'
              )/*! ENGINE=MyISAM */;";
    $this->addQuery($query);

    $query = "ALTER TABLE `receiver_hl7v3_config`
                ADD INDEX (`object_id`);";

    $this->addQuery($query);

    $this->makeRevision("0.96");

    $query = "ALTER TABLE `receiver_hl7v2_config`
                ADD `send_a42_onmerge` ENUM ('0','1') DEFAULT '0';";

    $this->addQuery($query);

    $this->makeRevision("0.97");

    $query = "ALTER TABLE `source_mllp`
                ADD `libelle` VARCHAR (255);";

    $this->addQuery($query);

    $this->makeRevision("0.98");

    $query = "ALTER TABLE `receiver_hl7v2_config`
                ADD `build_ZBE_7` ENUM ('medicale','soins') DEFAULT 'medicale',
                ADD `build_ZBE_8` ENUM ('medicale','soins') DEFAULT 'soins';";
    $this->addQuery($query);

    $this->makeRevision("0.99");

    $query = "ALTER TABLE `hl7_config`
                ADD `handle_ZBE_7` ENUM ('medicale','soins') DEFAULT 'medicale',
                ADD `handle_ZBE_8` ENUM ('medicale','soins') DEFAULT 'soins';";

    $this->addQuery($query);

    $this->makeRevision("1.00");

    $query = "ALTER TABLE `hl7_config`
                ADD `ins_integrated` ENUM ('0','1') DEFAULT '0';";
    $this->addQuery($query);

    $this->makeRevision("1.01");

    $query = "ALTER TABLE `receiver_hl7v2`
                ADD `monitor_sources` ENUM ('0','1') NOT NULL DEFAULT '1';";
    $this->addQuery($query);

    $query = "ALTER TABLE `receiver_hl7v3`
                ADD `monitor_sources` ENUM ('0','1') NOT NULL DEFAULT '1';";
    $this->addQuery($query);

    $this->makeRevision("1.02");
    $query = "ALTER TABLE `receiver_hl7v2_config`
                CHANGE `build_PID_34` `build_PID_3_4` ENUM ('finess','actor','domain') DEFAULT 'finess';";
    $this->addQuery($query);

    $this->makeRevision("1.03");
    $query = "ALTER TABLE `hl7_config`
                ADD `manage_npa` ENUM ('0','1') DEFAULT '0';";
    $this->addQuery($query);

    $query = "ALTER TABLE `receiver_hl7v2_config`
                ADD `build_PV1_19_identifier_authority` ENUM ('AN','RI','VN') DEFAULT 'RI';";
    $this->addQuery($query);

    $this->makeRevision("1.04");
    $query = "ALTER TABLE `hl7_config`
                ADD `handle_PV1_3_null` VARCHAR (255);";
    $this->addQuery($query);

    $query = "ALTER TABLE `receiver_hl7v2_config`
                ADD `build_PV1_3_1_default` VARCHAR (255),
                ADD `build_PV1_3_1` ENUM ('UF','service') DEFAULT 'UF';";
    $this->addQuery($query);

    $this->makeRevision("1.05");

    $query = "ALTER TABLE `hl7_config`
                ADD `change_filler_placer` ENUM ('0','1') DEFAULT '0';";
    $this->addQuery($query);

    $this->makeRevision("1.06");
    $query = "CREATE TABLE `hl7_transformation` (
                `hl7_transformation_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
                `actor_id` INT (11) UNSIGNED,
                `actor_class` VARCHAR (80),
                `profil` VARCHAR (255),
                `message` VARCHAR (255),
                `version` VARCHAR (255),
                `extension` VARCHAR (255),
                `component` VARCHAR (255),
                `action` ENUM ('add','modify','move','delete')
              )/*! ENGINE=MyISAM */;";
    $this->addQuery($query);

    $query = "ALTER TABLE `hl7_transformation`
                ADD INDEX (`actor_id`);";
    $this->addQuery($query);

    $this->makeRevision("1.07");

    $query = "DROP TABLE `hl7_transformation`";
    $this->addQuery($query);

    $this->makeRevision("1.08");

    $query = "ALTER TABLE `receiver_hl7v2_config`
                ADD `send_expected_discharge_with_affectation` ENUM ('0','1') DEFAULT '1';";
    $this->addQuery($query);

    $this->makeRevision("1.09");

    $query = "ALTER TABLE `hl7_config`
                ADD `handle_OBR_identity_identifier` VARCHAR (255);";
    $this->addQuery($query);

    $this->makeRevision("1.10");

    $query = "ALTER TABLE `receiver_hl7v2_config`
                CHANGE `build_PV1_10` `build_PV1_10` ENUM ('discipline','service','finess') DEFAULT 'discipline';";
    $this->addQuery($query);

    $query = "ALTER TABLE `hl7_config`
                CHANGE `handle_PV1_10` `handle_PV1_10` ENUM ('discipline','service','finess') DEFAULT 'discipline';";
    $this->addQuery($query);

    $this->makeRevision("1.11");

    $query = "ALTER TABLE `receiver_hl7v2_config`
                ADD `send_child_admit` ENUM ('0','1') DEFAULT '1';";
    $this->addQuery($query);

    $this->makeRevision("1.12");

    $query = "ALTER TABLE `receiver_hl7v2_config`
                ADD `ITI9_HL7_version` ENUM ('2.1','2.2','2.3','2.3.1','2.4','2.5') DEFAULT '2.5';";
    $this->addQuery($query);

    $this->makeRevision("1.13");

    $query = "ALTER TABLE `hl7_config`
                ADD `control_identifier_type_code` ENUM ('0','1') DEFAULT '1';";
    $this->addQuery($query);

    $this->makeRevision("1.14");
    $query = "ALTER TABLE `exchange_hl7v2`
                ADD `master_idex_missing` ENUM ('0','1') DEFAULT '0',
                ADD INDEX (`master_idex_missing`);";
    $this->addQuery($query);

    $query = "ALTER TABLE `exchange_hl7v3`
                ADD `master_idex_missing` ENUM ('0','1') DEFAULT '0',
                ADD INDEX (`master_idex_missing`);";
    $this->addQuery($query);

    $this->makeRevision("1.15");
    $query = "ALTER TABLE `receiver_hl7v2_config`
                ADD `send_not_master_IPP` ENUM ('0','1') DEFAULT '1',
                ADD `send_not_master_NDA` ENUM ('0','1') DEFAULT '1';";
    $this->addQuery($query);

    $this->makeRevision("1.16");

    $query = "ALTER TABLE `receiver_hl7v2_config`
                CHANGE `build_PID_18` `build_PID_18` ENUM ('normal','simple', 'none') DEFAULT 'normal';";
    $this->addQuery($query);

    $this->makeRevision("1.17");

    $query = "ALTER TABLE `receiver_hl7v2_config`
                CHANGE `send_no_facturable` `send_no_facturable` ENUM ('0','1','2') DEFAULT '1';";
    $this->addQuery($query);

    $this->mod_version = "1.18";

    $query = "SHOW TABLES LIKE 'table_description'";
    $this->addDatasource("hl7v2", $query);
  }
}
