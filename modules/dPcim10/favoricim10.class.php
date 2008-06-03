<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcim10
* @version $Revision$
* @author Romain Ollivier
*/

/**
 * The CFavoricim10 Class
 */
class CFavoricim10 extends CMbObject {
	var $favoris_id   = null;
	var $favoris_code = null;
	var $favoris_user = null;
	
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'cim10favoris';
    $spec->key   = 'favoris_id';
    return $spec;
  }
  
  function getSpecs() {
  	$specsParent = parent::getSpecs();
    $specs = array (
      "favoris_user" => "notNull ref class|CUser",
      "favoris_code" => "notNull str maxLength|16"
    );
    return array_merge($specsParent, $specs);
  }
  
  function getSeeks() {
    return array (
      "favoris_code" => "equal"
    );
  }
}
?>