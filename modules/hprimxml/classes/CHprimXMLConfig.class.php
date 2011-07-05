<?php /* $Id: object_config.class.php 8220 2010-03-05 13:06:52Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 8220 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CHprimXMLConfig extends CExchangeDataFormatConfig {
  static $config_fields = array(
    "type_sej_hospi",
    "type_sej_ambu",
    "type_sej_urg",
    "type_sej_exte",
    "type_sej_scanner",
    "type_sej_chimio",
    "type_sej_dialyse",
    "type_sej_pa",
    "use_sortie_matching",
    "fully_qualified",
  );
  
  var $hprimxml_config_id   = null;

  // Object configs
  var $type_sej_hospi       = null;
  var $type_sej_ambu        = null;
  var $type_sej_urg         = null;
  var $type_sej_exte        = null;
  var $type_sej_scanner     = null;
  var $type_sej_chimio      = null;
  var $type_sej_dialyse     = null;
  var $type_sej_pa          = null;
  var $use_sortie_matching  = null;
  var $fully_qualified      = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "hprimxml_config";
    $spec->key   = "hprimxml_config_id";
    $spec->uniques["uniques"] = array("sender_id");
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();

    $props["type_sej_hospi"]      = "str";
    $props["type_sej_ambu"]       = "str";
    $props["type_sej_urg"]        = "str";
    $props["type_sej_exte"]       = "str";
    $props["type_sej_scanner"]    = "str";
    $props["type_sej_chimio"]     = "str";
    $props["type_sej_dialyse"]    = "str";
    $props["type_sej_pa"]         = "str";
    $props["use_sortie_matching"] = "bool default|1";
    $props["fully_qualified"]     = "bool default|1";
    
    return $props;
  }
  
  function getConfigFields() {
    return $this->_config_fields = self::$config_fields;
  }
}
?>