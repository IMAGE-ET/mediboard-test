<?php /* $Id: object_config.class.php 8220 2010-03-05 13:06:52Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 8220 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CDestinataireHprimConfig extends CMbObject {
  var $dest_hprim_config_id = null;
  
  var $object_id          = null; // CDestinataireHprim
  // Object configs
  var $send_sortie_prevue = null; 
  var $type_sej_hospi     = null;
  var $type_sej_ambu      = null;
  var $type_sej_urg       = null;
  var $type_sej_scanner   = null;
  var $type_sej_chimio    = null;
  var $type_sej_dialyse   = null;
  var $receive_ack        = null;
  var $send_all_patients  = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "destinataire_hprim_config";
    $spec->key   = "dest_hprim_config_id";
    $spec->uniques["uniques"] = array("object_id");
    return $spec;
  }

  function getProps() {
    $specs = parent::getProps();
    $specs["object_id"]          = "ref class|CDestinataireHprim";
    
    $specs["send_sortie_prevue"] = "bool default|1";
    $specs["type_sej_hospi"]     = "str";
    $specs["type_sej_ambu"]      = "str";
    $specs["type_sej_urg"]       = "str";
    $specs["type_sej_scanner"]   = "str";
    $specs["type_sej_chimio"]    = "str";
    $specs["type_sej_dialyse"]   = "str";
    $specs["receive_ack"]        = "bool default|1";
    $specs["send_all_patients"]  = "bool default|1";
    
    return $specs;
  }
}
?>