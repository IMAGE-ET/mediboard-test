<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CConfigServiceAbstract extends CMbObject {
  var $service_id  = null;
  var $group_id = null;
  
  function getProps() {
  	$specs = parent::getProps();
    $specs["service_id"]  = "ref class|CService";
    $specs["group_id"] = "ref class|CGroups";
    return $specs;
  }
  
  static function setConfigService($name, $config){
		SHM::put($name, $config);
  }  
  
  static function getConfigService($name){
    return SHM::get($name);
  }
}

?>