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
  
  var $object_id              = null; // CReceiverIHE
  
  // Object configs
  var $ITI30_HL7_version      = null; 
  var $ITI31_HL7_version      = null; 
  var $send_all_patients      = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "receiver_ihe_config";
    $spec->key   = "receiver_ihe_config_id";
    $spec->uniques["uniques"] = array("object_id");
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();
    $props["object_id"]         = "ref class|CReceiverIHE";
    
    $props["ITI30_HL7_version"] = "enum list|2.1|2.2|2.3|2.3.1|2.4|2.5 default|2.5";
    $props["ITI31_HL7_version"] = "enum list|2.1|2.2|2.3|2.3.1|2.4|2.5 default|2.5";
    $props["send_all_patients"] = "bool default|0";
    
    return $props;
  }
}
?>