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
    "handle_PV1_10",
    "handle_PV1_14",
    "handle_PV1_36",
    "handle_NSS",
    
    // Purge
    "purge_idex_movements",
    
    // Auto repair
    "repair_patient",
    "control_date"
  );
  
  var $hl7_config_id = null;

  // Actor Options
  var $iti30_option_merge       = null;
  var $iti30_option_link_unlink = null;
  var $iti31_in_outpatient_emanagement           = null;
  var $iti31_pending_event_management            = null;
  var $iti31_advanced_encounter_management       = null;
  var $iti31_temporary_patient_transfer_tracking = null;
  var $iti31_historic_movement                   = null;

  // Application
  var $receiving_application    = null;
  var $receiving_facility       = null;
  var $assigning_authority_namespace_id      = null;
  var $assigning_authority_universal_id      = null;
  var $assigning_authority_universal_type_id = null;

  // Format
  var $encoding                  = null;
  var $strict_segment_terminator = null;
  var $segment_terminator        = null;

  // Handle
  var $handle_mode             = null;
  var $handle_NDA              = null;
  var $handle_telephone_number = null;
  var $handle_PID_31           = null;
  var $handle_PV1_3            = null;
  var $handle_PV1_10           = null;
  var $handle_PV1_14           = null;
  var $handle_PV1_36           = null;
  var $handle_NSS              = null;

  // Purge
  var $purge_idex_movements = null;

  // Auto repair
  var $repair_patient       = null;
  var $control_date         = null;
  
  var $_categories = array(
    "format" => array(
      "encoding", 
      "strict_segment_terminator",
      "segment_terminator",
    ),
    
    "application" => array(
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
      "handle_PV1_10",
      "handle_PV1_14",
      "handle_PV1_36",
      "handle_NSS",
    ),
    
    "purge" => array(
      "purge_idex_movements"
    ),
    
    "auto-repair" => array(
      "repair_patient",
      "control_date"
    )
  );

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "hl7_config";
    $spec->key   = "hl7_config_id";
    $spec->uniques["uniques"] = array("sender_id", "sender_class");
    return $spec;
  }

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
    
    // => PV1
    $props["handle_PV1_3"]  = "enum list|name|config_value|idex default|name";
    $props["handle_PV1_10"] = "enum list|discipline|service default|discipline";
    $props["handle_PV1_14"] = "enum list|admit_source|ZFM default|admit_source";
    $props["handle_PV1_36"] = "enum list|discharge_disposition|ZFM default|discharge_disposition";
    
    // Purge
    $props["purge_idex_movements"] = "bool default|0";
    
    // Auto repair
    $props["repair_patient"] = "bool default|1";
    $props["control_date"]   = "enum list|permissif|strict default|strict";
    
    return $props;
  }
  
  function getConfigFields() {
    return $this->_config_fields = self::$config_fields;
  }
}
