<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: 6148 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

/**
 * Catégorie d'activité CdARR
 */
class CTypeActiviteCdARR {  
  var $code = null;
	var $libelle = null;
	
	/**
	 * Get an instance from the code
	 * @param $code string
	 * @return CTypeActiviteCdARR
	 **/
	static function get($code) {
    $found = new CTypeActiviteCdARR();
    return $found;
	}
	
	/**
	 * seek instances from needle, in all fields
	 * @param $needle
	 * @param array[CTypeActiviteCdARR]
	 **/
	static function seek($needle) {
		$found = array();
		
		return $found;
	}
}

?>