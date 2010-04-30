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
  
  var $date = null;
  var $selectable = null;
	
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

  function __construct($date, $date_min = null, $date_max = null, $selectable = false) {
    $this->date = $date;
    $this->selectable = $selectable;
		
    $monday = mbDate("last monday", mbDate("+1 day", $this->date));
    $sunday = mbDate("next sunday", $this->date);
    
    if ($date_min) 
      $this->date_min = max($monday, mbDate($date_min));
    else
      $this->date_min = $monday;
    
    if ($date_max)
      $this->date_max = min($sunday, mbDate($date_max));
    else 
      $this->date_max = $sunday;
    
    // Days period
    for ($i = 0; $i < 7; $i++) {
      $this->days[mbDate("+$i day", $monday)] = array();
    }
  }
  
  function addEvent(CPlanningEvent $event) {
    if ($event->day < $this->date_min || $event->day > $this->date_max) 
      return;
      
    $this->events[] = $event;
    $this->days[$event->day][] = $event;
    
    $colliding = array($event);
    foreach($this->days[$event->day] as $_event) {
      if ($_event->collides($event)) {
        $colliding[] = $_event;
      }
    }
    
    $_event->offset = 0.0;
    $_event->width = 1.0;
      
    $count = count($colliding);
    
    if ($count) {
      foreach($colliding as $_key => $_event) {
        $_event->width = 1 / $count;
        $_event->offset = $_key * $_event->width;
      }
    }
  }
}
