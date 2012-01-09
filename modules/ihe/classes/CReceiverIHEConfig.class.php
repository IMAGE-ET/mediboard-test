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
  var $send_all_patients        = null;
  var $send_default_affectation = null;
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
    $props["send_all_patients"]        = "bool default|0";
    $props["send_default_affectation"] = "bool default|0";
    
    $props["assigning_authority_namespace_id"]      = "str";
    $props["assigning_authority_universal_id"]      = "str";
    $props["assigning_authority_universal_type_id"] = "str";
    
    return $props;
  }
}
?>