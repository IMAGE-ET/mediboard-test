<?php

/**
 * Receiver HL7v2 Config
 *
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

/**
 * Hl7v2 receiver config
 */
class CReceiverHL7v2Config extends CMbObjectConfig {
  public $receiver_hl7v2_config_id;

  public $object_id; // CReceiverHL7v2

  // Format
  public $encoding;
  public $ER7_segment_terminator;

  // Version
  public $ITI30_HL7_version;
  public $ITI31_HL7_version;

  public $RAD3_HL7_version;
  public $RAD48_HL7_version;

  public $ITI21_HL7_version;
  public $ITI22_HL7_version;

  public $ITI9_HL7_version;

  // Application
  public $receiving_application;
  public $receiving_facility;
  public $assigning_authority_namespace_id;
  public $assigning_authority_universal_id;
  public $assigning_authority_universal_type_id;
  public $country_code;

  // Actor Options
  public $iti30_option_merge;
  public $iti30_option_link_unlink;
  public $iti31_in_outpatient_management;
  public $iti31_pending_event_management;
  public $iti31_advanced_encounter_management;
  public $iti31_temporary_patient_transfer_tracking;
  public $iti31_historic_movement;

  // Send
  public $modification_admit_code;
  public $modification_before_admit;
  public $send_assigning_authority;
  public $send_all_patients;
  public $send_default_affectation;
  public $send_change_medical_responsibility;
  public $send_change_nursing_ward;
  public $send_change_attending_doctor;
  public $send_first_affectation;
  public $send_provisional_affectation;
  public $send_transfer_patient;
  public $send_own_identifier;
  public $send_self_identifier;
  public $send_actor_identifier;
  public $send_update_patient_information;
  public $send_change_after_admit;
  public $send_patient_with_visit;
  public $send_patient_with_current_admit;
  public $mode_identito_vigilance;
  public $send_no_facturable;
  public $send_a42_onmerge;
  public $send_expected_discharge_with_affectation;
  public $send_child_admit;
  public $send_not_master_IPP;
  public $send_not_master_NDA;

  // Build
  public $build_mode;
  public $build_NDA;
  public $build_telephone_number;
  public $build_cellular_phone;
  public $build_other_residence_number;
  public $build_identifier_authority;

  // PID
  public $build_PID_3_4; // PID.3.4
  public $build_PID_6;
  public $build_PID_11;
  public $build_PID_13;
  public $build_PID_18;
  public $build_PID_19;
  public $build_PID_31;

  // PV1
  public $build_PV1_3_1;
  public $build_PV1_3_1_default;
  public $build_PV1_3_2;
  public $build_PV1_3_3;
  public $build_PV1_3_5;
  public $build_PV1_5;
  public $build_PV1_7;
  public $build_PV1_10;
  public $build_PV1_11;
  public $build_PV1_14;
  public $build_PV1_17;
  public $build_PV1_19;
  public $build_PV1_19_identifier_authority;
  public $build_PV1_26;
  public $build_PV1_36;

  // PV2
  public $build_PV2_45;

  // ZBE
  public $build_ZBE_7;
  public $build_ZBE_8;

  public $_categories = array(
    "format" => array(
      "encoding",
      "ER7_segment_terminator",
    ),

    "version" => array(
      // PAM
      "ITI30_HL7_version",
      "ITI31_HL7_version",

      // SWF
      "RAD3_HL7_version",
      "RAD48_HL7_version",

      // PDQ
      "ITI21_HL7_version",
      "ITI22_HL7_version",

      // PIX
      "ITI9_HL7_version",
    ),

    "application" => array(
      "receiving_application",
      "receiving_facility",
      "assigning_authority_namespace_id",
      "assigning_authority_universal_id",
      "assigning_authority_universal_type_id",
      "country_code"
    ),

    "actor options" => array(
      "iti30_option_merge",
      "iti30_option_link_unlink",
      "iti31_in_outpatient_management",
      "iti31_pending_event_management",
      "iti31_advanced_encounter_management",
      "iti31_temporary_patient_transfer_tracking",
      "iti31_historic_movement",
    ),

    "build" => array(
      "build_mode",
      "build_NDA",
      "build_telephone_number",
      "build_cellular_phone",
      "build_other_residence_number",
      "build_identifier_authority"
    ),

    "send" => array(
      "modification_admit_code",
      "modification_before_admit",
      "send_assigning_authority",
      "send_change_medical_responsibility",
      "send_change_nursing_ward",
      "send_change_attending_doctor",
      "send_all_patients",
      "send_default_affectation",
      "send_first_affectation",
      "send_provisional_affectation",
      "send_transfer_patient",
      "send_own_identifier",
      "send_self_identifier",
      "send_actor_identifier",
      "send_update_patient_information",
      "send_change_after_admit",
      "send_patient_with_visit",
      "send_patient_with_current_admit",
      "mode_identito_vigilance",
      "send_no_facturable",
      "send_a42_onmerge",
      "send_expected_discharge_with_affectation",
      "send_child_admit",
      "send_not_master_IPP",
      "send_not_master_NDA",
    ),

    "PID" => array(
      "build_PID_3_4",
      "build_PID_6",
      "build_PID_11",
      "build_PID_13",
      "build_PID_18",
      "build_PID_19",
      "build_PID_31",
    ),

    "PV1" => array(
      "build_PV1_3_1",
      "build_PV1_3_1_default",
      "build_PV1_3_2",
      "build_PV1_3_3",
      "build_PV1_3_5",
      "build_PV1_5",
      "build_PV1_7",
      "build_PV1_10",
      "build_PV1_11",
      "build_PV1_14",
      "build_PV1_17",
      "build_PV1_19",
      "build_PV1_19_identifier_authority",
      "build_PV1_26",
      "build_PV1_36",
    ),

    "PV2" => array(
      "build_PV2_45",
    ),

    "ZBE" => array(
      "build_ZBE_7",
      "build_ZBE_8",
    ),
  );

  /**
   * Initialize object specification
   *
   * @return CMbObjectSpec the spec
   */
  function getSpec() {
    $spec = parent::getSpec();

    $spec->table = "receiver_hl7v2_config";
    $spec->key   = "receiver_hl7v2_config_id";
    $spec->uniques["uniques"] = array("object_id");

    return $spec;
  }

  /**
   * Get properties specifications as strings
   *
   * @return array
   */
  function getProps() {
    $props = parent::getProps();
    $props["object_id"]              = "ref class|CReceiverHL7v2";

    // Format
    $props["encoding"]               = "enum list|UTF-8|ISO-8859-1 default|UTF-8";
    $props["ER7_segment_terminator"] = "enum list|CR|LF|CRLF";

    // Version
    $props["ITI30_HL7_version"] = "enum list|2.1|2.2|2.3|2.3.1|2.4|2.5|FR_2.1|FR_2.2|FR_2.3 default|2.5";
    $props["ITI31_HL7_version"] = "enum list|2.1|2.2|2.3|2.3.1|2.4|2.5|FR_2.1|FR_2.2|FR_2.3 default|2.5";
    $props["RAD3_HL7_version"]  = "enum list|2.1|2.2|2.3|2.3.1|2.4|2.5 default|2.5";
    $props["RAD48_HL7_version"] = "enum list|2.1|2.2|2.3|2.3.1|2.4|2.5 default|2.5";
    $props["ITI9_HL7_version"]  = "enum list|2.1|2.2|2.3|2.3.1|2.4|2.5 default|2.5";
    $props["ITI21_HL7_version"] = "enum list|2.1|2.2|2.3|2.3.1|2.4|2.5 default|2.5";
    $props["ITI22_HL7_version"] = "enum list|2.1|2.2|2.3|2.3.1|2.4|2.5 default|2.5";

    // Application
    $props["receiving_application"] = "str";
    $props["receiving_facility"]    = "str";
    $props["assigning_authority_namespace_id"]      = "str";
    $props["assigning_authority_universal_id"]      = "str";
    $props["assigning_authority_universal_type_id"] = "str";
    $props["country_code"] = "enum list|FRA|INT";

    // Actor options
    $props["iti30_option_merge"]                        = "bool default|1";
    $props["iti30_option_link_unlink"]                  = "bool default|0";
    $props["iti31_in_outpatient_management"]            = "bool default|1";
    $props["iti31_pending_event_management"]            = "bool default|0";
    $props["iti31_advanced_encounter_management"]       = "bool default|1";
    $props["iti31_temporary_patient_transfer_tracking"] = "bool default|0";
    $props["iti31_historic_movement"]                   = "bool default|1";

    // Send
    $props["modification_admit_code"]                  = "enum list|A08|Z99 default|Z99";
    $props["modification_before_admit"]                = "bool default|1";
    $props["send_assigning_authority"]                 = "bool default|1";
    $props["send_change_medical_responsibility"]       = "enum list|A02|Z80|Z99 default|Z80";
    $props["send_change_nursing_ward"]                 = "enum list|A02|Z84|Z99 default|Z84";
    $props["send_change_attending_doctor"]             = "enum list|A54|Z99 default|A54";
    $props["send_all_patients"]                        = "bool default|0";
    $props["send_default_affectation"]                 = "bool default|0";
    $props["send_first_affectation"]                   = "enum list|A02|Z99 default|Z99";
    $props["send_provisional_affectation"]             = "bool default|0";
    $props["send_transfer_patient"]                    = "enum list|A02|Z99 default|A02";
    $props["send_own_identifier"]                      = "bool default|1";
    $props["send_self_identifier"]                     = "bool default|0";
    $props["send_actor_identifier"]                    = "bool default|0";
    $props["send_update_patient_information"]          = "enum list|A08|A31 default|A31";
    $props["send_change_after_admit"]                  = "bool default|1";
    $props["send_patient_with_visit"]                  = "bool default|0";
    $props["send_patient_with_current_admit"]          = "bool default|0";
    $props["mode_identito_vigilance"]                  = "enum list|light|medium|strict default|light";
    $props["send_no_facturable"]                       = "bool default|1";
    $props["send_a42_onmerge"]                         = "bool default|0";
    $props["send_expected_discharge_with_affectation"] = "bool default|1";
    $props["send_child_admit"]                         = "bool default|1";
    $props["send_not_master_IPP"]                      = "bool default|1";
    $props["send_not_master_NDA"]                      = "bool default|1";

    // Build
    $props["build_mode"]                   = "enum list|normal|simple default|normal";
    $props["build_NDA"]                    = "enum list|PID_18|PV1_19 default|PID_18";
    $props["build_telephone_number"]       = "enum list|XTN_1|XTN_12 default|XTN_12";
    $props["build_cellular_phone"]         = "enum list|PRN|ORN default|PRN";
    $props["build_other_residence_number"] = "enum list|ORN|WPN default|ORN";
    $props["build_identifier_authority"]   = "enum list|normal|PI_AN default|normal";

    // PID
    $props["build_PID_3_4"]  = "enum list|finess|actor|domain default|finess";
    $props["build_PID_6"]    = "enum list|nom_naissance|none default|none";
    $props["build_PID_11"]   = "enum list|simple|multiple default|multiple";
    $props["build_PID_13"]   = "enum list|simple|multiple default|multiple";
    $props["build_PID_18"]   = "enum list|normal|simple|none default|normal";
    $props["build_PID_19"]   = "enum list|matricule|none default|none";
    $props["build_PID_31"]   = "enum list|avs|none default|none";

    // PV1
    $props["build_PV1_3_1"]                     = "enum list|UF|service default|UF";
    $props["build_PV1_3_1_default"]             = "str";
    $props["build_PV1_3_2"]                     = "enum list|name|config_value|idex default|name";
    $props["build_PV1_3_3"]                     = "enum list|name|config_value|idex default|name";
    $props["build_PV1_3_5"]                     = "enum list|bed_status|null default|bed_status";
    $props["build_PV1_5"]                       = "enum list|NPA|none default|NPA";
    $props["build_PV1_7"]                       = "enum list|unique|repeatable default|unique";
    $props["build_PV1_10"]                      = "enum list|discipline|service|finess default|discipline";
    $props["build_PV1_11"]                      = "enum list|uf_medicale|none default|none";
    $props["build_PV1_14"]                      = "enum list|admit_source|ZFM default|admit_source";
    $props["build_PV1_17"]                      = "enum list|praticien|none default|praticien";
    $props["build_PV1_19"]                      = "enum list|normal|simple default|normal";
    $props["build_PV1_19_identifier_authority"] = "enum list|AN|RI|VN default|RI";
    $props["build_PV1_26"]                      = "enum list|movement_id|none default|none";
    $props["build_PV1_36"]                      = "enum list|discharge_disposition|ZFM default|discharge_disposition";

    // PV2
    $props["build_PV2_45"]   = "enum list|operation|none default|none";

    // ZBE
    $props["build_ZBE_7"] = "enum list|medicale|soins default|medicale";
    $props["build_ZBE_8"] = "enum list|medicale|soins default|soins";

    return $props;
  }
}
