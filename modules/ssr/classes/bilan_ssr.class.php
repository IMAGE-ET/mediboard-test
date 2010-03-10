<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

/**
 * Bilan d'entrée SSR
 */
class CBilanSSR extends CMbObject {
  // DB Table key
  var $bilan_id = null;
  
  // DB Fields
  var $sejour_id = null;
  var $kine_id = null;
  var $entree = null;
  var $sortie = null;
//  var $kine   = null;
//  var $ergo   = null;
//  var $psy    = null;
//  var $ortho  = null;
//  var $diet   = null;
//  var $social = null;
//  var $apa    = null;
  var $_activites = array();
	
  // References

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "bilan_ssr";
    $spec->key   = "bilan_id";
    $spec->uniques["sejour_id"] = array("sejour_id");
    return $spec;
  }

  function getProps() {
    $specs = parent::getProps();
    $specs["sejour_id"] = "ref notNull class|CSejour show|0";
		$specs["kine_id"]   = "ref class|CMediusers";
    $specs["entree"] = "text helped";
    $specs["sortie"] = "text helped";
    return $specs;
  }
	
	/**
	 * Load Sejour for kine at a date
	 **/ 
	static function loadSejoursSSRfor($kine_id, $date) {
		$group = CGroups::loadCurrent();
    $where["type"] = "= 'ssr'";
    $where["group_id"] = "= '$group->_id'";
		$where["kine_id"] = $kine_id ? "= '$kine_id'" : "IS NULL";
    $leftjoin["bilan_ssr"] = "bilan_ssr.sejour_id = sejour.sejour_id";
		return CSejour::loadListForDate($date, $where, "entree_reelle", null, null, $leftjoin);
	}
	
	
}

?>