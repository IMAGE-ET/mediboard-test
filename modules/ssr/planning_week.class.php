<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CPlanningWeek  {
	var $title = null;
	
	// Periods
	var $hours = array("08", "09", "10", "11", "", "14", "15", "16", "17");
	var $minutes = array("00", "10", "20", "30", "40", "50");
  var $days = array();

  // Plages
	var $plages = array();

  function __construct() {
		// Days period
		$today = mbDate();
		$debut = mbDate("+1 day", mbDate("last sunday", $today));
		$fin   = mbDate("next sunday", $today);
		for ($i = 0; $i < 7; $i++) {
		  $this->days[] = mbDate("+$i day", $debut);
		}
  }
}

?>