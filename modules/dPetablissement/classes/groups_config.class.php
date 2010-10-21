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
  var $max_comp   = null;
  var $max_ambu   = null; 
  var $imeds_cidc = null;

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
    
    $specs["max_comp"]   = "num min|0";
    $specs["max_ambu"]   = "num min|0";
    $specs["imeds_cidc"] = "str";

    return $specs;
  }
}
?>