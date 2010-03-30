<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CPlanningWeek  {
  var $guid = null;
  var $title = null;
  
  var $date_min = null;
  var $date_max = null;
  var $hour_min = "09";
  var $hour_max = "18";
  
  var $events = array();
  var $pauses = array("07", "12", "17");
  
  // Periods
  var $hours = array(
    "00", "01", "02", "03", "04", "05", 
    "06", "07", "08", "09", "10", "11", 
    "12", "13", "14", "15", "16", "17", 
    "18", "19", "20", "21", "22", "23", 
  );

  var $days = array();

  function __construct() {
    // Days period
    $today = mbDate();
    $start = mbDate("+1 day", mbDate("last sunday", $today));
    for ($i = 0; $i < 7; $i++) {
      $this->days[mbDate("+$i day", $start)] = array();
    }
  }
  
  function addEvent(CPlanningEvent $event) {
    $this->events[] = $event;
    $this->days[$event->day][] = $event;
  }
}
