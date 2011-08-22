<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPcim10
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
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
  
  function getProps() {
  	$specs = parent::getProps();
    $specs["favoris_user"] = "ref notNull class|CUser";
    $specs["favoris_code"] = "str notNull maxLength|16 seekable";
    return $specs;
  }
}
?>