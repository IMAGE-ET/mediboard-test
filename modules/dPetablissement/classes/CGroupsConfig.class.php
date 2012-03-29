<?php /* $Id: object_config.class.php 8220 2010-03-05 13:06:52Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage dPetablissement
 * @version $Revision: 8220 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CGroupsConfig extends CMbObject {
  var $groups_config_id = null;
  
  var $object_id = null; // CGroups
  
  // Object configs
  var $max_comp    = null;
  var $max_ambu    = null;
  var $codage_prat = null;
  
  var $dPpatients_CPatient_nom_jeune_fille_mandatory = null;
  
  var $dPplanningOp_COperation_DHE_mode_simple       = null;
  
  var $ecap_CRPU_notes_creation = null;
  
  // SIP
  var $sip_notify_all_actors = null;
  var $sip_idex_generator    = null;
  var $ipp_range_min         = null;
  var $ipp_range_max         = null;
  
  // SMP
  var $smp_notify_all_actors = null;
  var $smp_idex_generator    = null;
  var $nda_range_min         = null;
  var $nda_range_max         = null;
  
  var $dPprescription_CPrescription_show_trash_24h   = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "groups_config";
    $spec->key   = "groups_config_id";
    $spec->uniques["uniques"] = array("object_id");
    return $spec;
  }

  function getProps() {
    $specs = parent::getProps();
    $specs["object_id"]          = "ref class|CGroups";
    
    $specs["max_comp"]    = "num min|0";
    $specs["max_ambu"]    = "num min|0";
    $specs["codage_prat"] = "bool default|0";
    
    $specs["dPpatients_CPatient_nom_jeune_fille_mandatory"] = "bool default|0";
    
    $specs["dPplanningOp_COperation_DHE_mode_simple"]       = "bool default|0";
    
    $specs["ecap_CRPU_notes_creation"] = "bool default|0";
    
    // SIP
    $specs["sip_notify_all_actors"] = "bool default|0";
    $specs["sip_idex_generator"]    = "bool default|0";
    $specs["ipp_range_min"]         = "num min|0";
    $specs["ipp_range_max"]         = "num moreThan|ipp_range_min";
    
    // SMP
    $specs["smp_notify_all_actors"] = "bool default|0";
    $specs["smp_idex_generator"]    = "bool default|0";
    $specs["nda_range_min"]         = "num min|0";
    $specs["nda_range_max"]         = "num moreThan|ipp_range_min";
    
    $specs["dPprescription_CPrescription_show_trash_24h"] = "bool default|0";
    
    return $specs;
  }
}
?>