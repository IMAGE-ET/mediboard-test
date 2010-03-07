<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: 6148 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

/**
 * Activité CdARR
 */
class CActiviteCdARR {  
  var $code = null;
  var $type = null;
	var $libelle = null;
	
	// Prefix code with category
	var $_view = null;
	
	/**
	 * Get an instance from the code
	 * @param $code string
	 * @return CActiviteCdARR
	 **/
	static function get($code) {
		$found = new CActiviteCdARR();
		return $found;
	}
	
	/**
	 * seek instances from needle, in all fields
	 * @param $needle
	 * @param array[CActiviteCdARR]
	 **/
	static function seek($needle) {
		$found = array();
		
		return $found;
	}
}

?>