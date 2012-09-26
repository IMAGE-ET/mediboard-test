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
    "assigning_authority_namespace_id",
    "assigning_authority_universal_id",
    "assigning_authority_universal_type_id",
    
    // Encoding
    "encoding",
    
    // Handle
    "handle_mode",
    "handle_NDA",
    "handle_PV1_10",
    "handle_PV1_14",
    "handle_PV1_36",
    "handle_NSS",
    
    // Purge
    "purge_idex_movements",
  );
  
  var $hl7_config_id = null;

  // Object configs
  var $iti30_option_merge       = null;
  var $iti30_option_link_unlink = null;
  
  var $iti31_in_outpatient_emanagement           = null;
  var $iti31_pending_event_management            = null;
  var $iti31_advanced_encounter_management       = null;
  var $iti31_temporary_patient_transfer_tracking = null;
  var $iti31_historic_movement                   = null;
  
  var $assigning_authority_namespace_id      = null;
  var $assigning_authority_universal_id      = null;
  var $assigning_authority_universal_type_id = null;
  
  var $encoding      = null;
  var $strict_segment_terminator = null;
  
  var $handle_mode   = null;
  var $handle_NDA    = null;
  var $handle_PV1_10 = null;
  var $handle_PV1_14 = null;
  var $handle_PV1_36 = null;
  var $handle_NSS    = null;
  
  var $purge_idex_movements = null;
  
  var $_categories = array(
    "format" => array(
      "encoding", 
      "strict_segment_terminator",
    ),
    
    "application" => array(
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
      "handle_PV1_10",
      "handle_PV1_14",
      "handle_PV1_36",
      "handle_NSS",
    ),
    
    "purge" => array(
      "purge_idex_movements"
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
    
    $props["assigning_authority_namespace_id"]      = "str";
    $props["assigning_authority_universal_id"]      = "str";
    $props["assigning_authority_universal_type_id"] = "str";
    
    // Encoding
    $props["encoding"]      = "enum list|UTF-8|ISO-8859-1 default|UTF-8";
    $props["strict_segment_terminator"] = "bool default|0";
    
    // Handle
    $props["handle_mode"]   = "enum list|normal|simple default|normal";
    // => PID
    $props["handle_NDA"]    = "enum list|PID_18|PV1_19 default|PID_18";
    $props["handle_NSS"]    = "enum list|PID_3|PID_19 default|PID_3";
    // => PV1
    $props["handle_PV1_10"] = "enum list|discipline|service default|discipline";
    $props["handle_PV1_14"]  = "enum list|admit_source|ZFM default|admit_source";
    $props["handle_PV1_36"]  = "enum list|discharge_disposition|ZFM default|discharge_disposition";
    
    // Purge
    $props["purge_idex_movements"] = "bool default|0";
    
    return $props;
  }
  
  function getConfigFields() {
    return $this->_config_fields = self::$config_fields;
  }
}
