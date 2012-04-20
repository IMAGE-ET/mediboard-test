<?php

/**
 * Receiver IHE Config
 *  
 * @category IHE
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CReceiverIHEConfig 
 * Receiver IHE Config
 */

class CReceiverIHEConfig extends CMbObject {
  var $receiver_ihe_config_id = null;
  
  var $object_id                = null; // CReceiverIHE
  
  // Object configs
  var $encoding                 = null;
  
  var $ITI30_HL7_version        = null; 
  var $ITI31_HL7_version        = null; 
  
  var $build_mode               = null;
  var $build_NDA                = null;
  
  var $ER7_segment_terminator   = null;
  
  // PV1
  var $build_PV1_3_2            = null;
  var $build_PV1_3_3            = null;
  var $build_PV1_3_5            = null;
  var $build_PV1_10             = null;
  var $build_PV1_14             = null;
  var $build_PV1_36             = null;
  
  var $send_all_patients        = null;
  var $send_default_affectation = null;
  
  var $send_change_medical_responsibility = null;
  var $send_change_nursing_ward           = null;
  
  var $receiving_application    = null;
  var $receiving_facility       = null;
              
  var $assigning_authority_namespace_id      = null;
  var $assigning_authority_universal_id      = null;
  var $assigning_authority_universal_type_id = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "receiver_ihe_config";
    $spec->key   = "receiver_ihe_config_id";
    $spec->uniques["uniques"] = array("object_id");
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();
    $props["object_id"]                = "ref class|CReceiverIHE";
    
    $props["encoding"]                 = "enum list|UTF-8|ISO-8859-1 default|UTF-8";
    
    $props["ITI30_HL7_version"]        = "enum list|2.1|2.2|2.3|2.3.1|2.4|2.5|FR_2.1|FR_2.2|FR_2.3 default|2.5";
    $props["ITI31_HL7_version"]        = "enum list|2.1|2.2|2.3|2.3.1|2.4|2.5|FR_2.1|FR_2.2|FR_2.3 default|2.5";
    
    
    $props["send_change_medical_responsibility"] = "enum list|Z80|Z99 default|Z80";
    $props["send_change_nursing_ward"]           = "enum list|Z84|Z99 default|Z84";
    
    $props["build_mode"]               = "enum list|normal|simple default|normal";
    $props["build_NDA"]                = "enum list|PID_18|PV1_19 default|PID_18";
    
    $props["ER7_segment_terminator"]   = "enum list|CR|LF|CRLF";
    
    // PV1
    $props["build_PV1_3_2"]            = "enum list|name|config_value default|name";
    $props["build_PV1_3_3"]            = "enum list|name|config_value default|name";
    $props["build_PV1_3_5"]            = "enum list|bed_status|null default|bed_status";
    $props["build_PV1_10"]             = "enum list|discipline|service default|discipline";
    $props["build_PV1_14"]             = "enum list|admit_source|ZFM default|admit_source";
    $props["build_PV1_36"]             = "enum list|discharge_disposition|ZFM default|discharge_disposition";
    
    $props["receiving_application"]    = "str";
    $props["receiving_facility"]       = "str";
    
    $props["send_all_patients"]        = "bool default|0";
    $props["send_default_affectation"] = "bool default|0";
    
    $props["assigning_authority_namespace_id"]      = "str";
    $props["assigning_authority_universal_id"]      = "str";
    $props["assigning_authority_universal_type_id"] = "str";
    
    return $props;
  }
}
?>