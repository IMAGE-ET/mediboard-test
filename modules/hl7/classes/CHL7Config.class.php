<?php
/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage hl7
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */

/**
 * Class CHL7Config 
 * Config HL7
 */
class CHL7Config extends CExchangeDataFormatConfig {
  /**
   * @var array Config fields
   */
  static $config_fields = array(
    // Options
    // => ITI-30
    "iti30_option_merge",
    "iti30_option_link_unlink",
    // => ITI-31
    "iti31_in_outpatient_emanagement",
    "iti31_pending_event_management",
    "iti31_advanced_encounter_management",
    "iti31_temporary_patient_transfer_tracking",
    "iti31_historic_movement",
    
    // Assigning authority
    "check_receiving_application_facility",
    "receiving_application",
    "receiving_facility",
    "assigning_authority_namespace_id",
    "assigning_authority_universal_id",
    "assigning_authority_universal_type_id",
    
    // Format
    "encoding",
    "strict_segment_terminator",
    "segment_terminator",
    
    // Handle
    "handle_mode",
    "handle_NDA",
    "handle_telephone_number",
    "handle_PID_31",
    "handle_PV1_3",
    "handle_PV1_7",
    "handle_PV1_10",
    "handle_PV1_14",
    "handle_PV1_20",
    "handle_PV1_36",
    "handle_PV2_12",
    "handle_NSS",

    // Send
    "send_assigning_authority",
    "send_self_identifier",
    "send_area_local_number",
    
    // Purge
    "purge_idex_movements",
    
    // Auto repair
    "repair_patient",
    "control_date"
  );
  
  public $hl7_config_id;

  // Actor Options
  public $iti30_option_merge;
  public $iti30_option_link_unlink;
  public $iti31_in_outpatient_emanagement;
  public $iti31_pending_event_management;
  public $iti31_advanced_encounter_management;
  public $iti31_temporary_patient_transfer_tracking;
  public $iti31_historic_movement;

  // Application
  public $check_receiving_application_facility;
  public $receiving_application;
  public $receiving_facility;
  public $assigning_authority_namespace_id;
  public $assigning_authority_universal_id;
  public $assigning_authority_universal_type_id;

  // Format
  public $encoding;
  public $strict_segment_terminator;
  public $segment_terminator;

  // Handle
  public $handle_mode;
  public $handle_NDA;
  public $handle_telephone_number;
  public $handle_PID_31;
  public $handle_PV1_3;
  public $handle_PV1_7;
  public $handle_PV1_10;
  public $handle_PV1_14;
  public $handle_PV1_20;
  public $handle_PV1_36;
  public $handle_PV2_12;
  public $handle_NSS;

  // Send
  public $send_assigning_authority;
  public $send_self_identifier;
  public $send_area_local_number;

  // Purge
  public $purge_idex_movements;

  // Auto repair
  public $repair_patient;
  public $control_date;

  /**
   * @var array Categories
   */
  public $_categories = array(
    "format" => array(
      "encoding", 
      "strict_segment_terminator",
      "segment_terminator",
    ),
    
    "application" => array(
      "check_receiving_application_facility",
      "receiving_application",
      "receiving_facility",
      "assigning_authority_namespace_id",
      "assigning_authority_universal_id",
      "assigning_authority_universal_type_id",
    ),
    
    "actor options" => array(
      "iti30_option_merge",
      "iti30_option_link_unlink",
      "iti31_in_outpatient_emanagement",
      "iti31_pending_event_management",
      "iti31_advanced_encounter_management",
      "iti31_temporary_patient_transfer_tracking",
      "iti31_historic_movement",
    ),
    
    "handle" => array(
      "handle_mode",
      "handle_NDA",
      "handle_telephone_number",
      "handle_PID_31",
      "handle_PV1_3",
      "handle_PV1_7",
      "handle_PV1_10",
      "handle_PV1_14",
      "handle_PV1_20",
      "handle_PV1_36",
      "handle_PV2_12",
      "handle_NSS",
    ),

    "send" => array(
      "send_assigning_authority",
      "send_self_identifier",
      "send_area_local_number",
    ),

    "purge" => array(
      "purge_idex_movements"
    ),
    
    "auto-repair" => array(
      "repair_patient",
      "control_date"
    )
  );

  /**
   * Initialize object specification
   *
   * @return CMbObjectSpec the spec
   */
  function getSpec() {
    $spec = parent::getSpec();

    $spec->table = "hl7_config";
    $spec->key   = "hl7_config_id";
    $spec->uniques["uniques"] = array("sender_id", "sender_class");

    return $spec;
  }

  /**
   * Get properties specifications as strings
   *
   * @return array
   */
  function getProps() {
    $props = parent::getProps();
    
    // Options
    // => ITI-30
    $props["iti30_option_merge"]                        = "bool default|1";
    $props["iti30_option_link_unlink"]                  = "bool default|0";
    // => ITI-31
    $props["iti31_in_outpatient_emanagement"]           = "bool default|1";
    $props["iti31_pending_event_management"]            = "bool default|0";
    $props["iti31_advanced_encounter_management"]       = "bool default|1";
    $props["iti31_temporary_patient_transfer_tracking"] = "bool default|0";
    $props["iti31_historic_movement"]                   = "bool default|1";

    $props["check_receiving_application_facility"]  = "bool default|0";
    $props["receiving_application"]                 = "str";
    $props["receiving_facility"]                    = "str";
    $props["assigning_authority_namespace_id"]      = "str";
    $props["assigning_authority_universal_id"]      = "str";
    $props["assigning_authority_universal_type_id"] = "str";
    
    // Encoding
    $props["encoding"] = "enum list|UTF-8|ISO-8859-1 default|UTF-8";
    $props["strict_segment_terminator"] = "bool default|0";
    $props["segment_terminator"] = "enum list|CR|LF|CRLF";
    
    // Handle
    $props["handle_mode"]             = "enum list|normal|simple default|normal";
    $props["handle_telephone_number"] = "enum list|XTN_1|XTN_12 default|XTN_12";
    
    // => PID
    $props["handle_NDA"]    = "enum list|PID_18|PV1_19 default|PID_18";
    $props["handle_NSS"]    = "enum list|PID_3|PID_19 default|PID_3";
    $props["handle_PID_31"] = "enum list|avs|none default|none";
    
     // =>PV1
    $props["handle_PV1_3"]  = "enum list|name|config_value|idex default|name";
    $props["handle_PV1_7"]  = "bool default|1";
    $props["handle_PV1_10"] = "enum list|discipline|service default|discipline";
    $props["handle_PV1_14"] = "enum list|admit_source|ZFM default|admit_source";
    $props["handle_PV1_20"] = "enum list|old_presta|none default|none";
    $props["handle_PV1_36"] = "enum list|discharge_disposition|ZFM default|discharge_disposition";

    // => PV2
    $props["handle_PV2_12"] = "enum list|libelle|none default|libelle";

    // Send
    $props["send_assigning_authority"] = "bool default|1";
    $props["send_self_identifier"]     = "bool default|0";
    // => XTN
    $props["send_area_local_number"]   = "bool default|0";
    
    // Purge
    $props["purge_idex_movements"] = "bool default|0";
    
    // Auto repair
    $props["repair_patient"] = "bool default|1";
    $props["control_date"]   = "enum list|permissif|strict default|strict";
    
    return $props;
  }

  /**
   * Get config fields
   *
   * @return array
   */
  function getConfigFields() {
    return $this->_config_fields = self::$config_fields;
  }
}
