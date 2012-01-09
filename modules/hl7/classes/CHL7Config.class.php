<?php /* $Id:$ */

/**
 * Config HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7Config 
 * Config HL7
 */

class CHL7Config extends CExchangeDataFormatConfig {
  static $config_fields = array(
    "assigning_authority_namespace_id",
    "assigning_authority_universal_id",
    "assigning_authority_universal_type_id"
  );
  
  var $hl7_config_id = null;

  // Object configs
  var $assigning_authority_namespace_id      = null;
  var $assigning_authority_universal_id      = null;
  var $assigning_authority_universal_type_id = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "hl7_config";
    $spec->key   = "hl7_config_id";
    $spec->uniques["uniques"] = array("sender_id", "sender_class");
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();

    $props["assigning_authority_namespace_id"]      = "str";
    $props["assigning_authority_universal_id"]      = "str";
    $props["assigning_authority_universal_type_id"] = "str";
    
    return $props;
  }
  
  function getConfigFields() {
    return $this->_config_fields = self::$config_fields;
  }
}
?>