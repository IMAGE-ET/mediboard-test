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
class CActiviteCdARR {  
  var $code = null;
  var $type = null;
	var $libelle = null;
	
	/**
	 * Get an instance from the code
	 * @param $code string
	 * @return CActiviteCdARR|null, null if not found
	 **/
	static function get($code) {
		return;
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