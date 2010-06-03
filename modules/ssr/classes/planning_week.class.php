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
	var $height = null;
	var $large   = null;
	
  var $date_min = null; // Monday
  var $date_max = null; // Sunday
  
  var $date_min_active = null;
  var $date_max_active = null;
  
  var $hour_min = "09";
  var $hour_max = "18";
  var $hour_divider = 6;
  
  var $events = array();
  var $pauses = array("07", "12", "17");
  var $unavailabilities = array();
  var $day_labels = array();
  
	var $_date_min_planning = null;
	var $_date_max_planning = null;
	
  // Periods
  var $hours = array(
    "00", "01", "02", "03", "04", "05", 
    "06", "07", "08", "09", "10", "11", 
    "12", "13", "14", "15", "16", "17", 
    "18", "19", "20", "21", "22", "23", 
  );

  var $days = array();

  function __construct($date, $date_min = null, $date_max = null, $nb_days = 7, $selectable = false, $height = 250, $large = false) {
		$this->date = $date;
    $this->selectable = $selectable;
		$this->height = $height ? $height : 250;
		$this->large = $large;
		$this->nb_days = $nb_days;
		
    $monday = mbDate("last monday", mbDate("+1 day", $this->date));
    $sunday = mbDate("next sunday", mbDate("-1 DAY", $this->date));
		
		$this->date_min_active = $date_min ? max($monday, mbDate($date_min)) : $monday;
		$this->date_max_active = $date_max ? min($sunday, mbDate($date_max)) : $sunday;
    
    $this->date_min = $monday;
    $this->date_max = $sunday;
    
    // Days period
    for ($i = 0; $i < $this->nb_days; $i++) {
      $this->days[mbDate("+$i day", $monday)] = array();
    }
		
		$this->_date_min_planning = reset(array_keys($this->days));
		$this->_date_max_planning = end(array_keys($this->days));
	}
  
  function addEvent(CPlanningEvent $event) {
    if ($event->day < $this->date_min || $event->day > $this->date_max) 
      return;
      
    if ($event->day < $this->date_min_active || $event->day > $this->date_max_active) 
      $event->disabled = true;
    
    $this->events[] = $event;
    $this->days[$event->day][] = $event;
    
    $colliding = array($event);
    foreach($this->days[$event->day] as $_event) {
      if ($_event->collides($event)) {
        $colliding[] = $_event;
      }
    }
    
    $event->offset = 0.0;
    $event->width = 1.0;
    
    $count = count($colliding);
    
    if ($count) {
      foreach($colliding as $_key => $_event) {
        $_event->width = 1 / $count;
        $_event->offset = $_key * $_event->width;
      }
    }
  }
  
  /**
   * @param object $min The min date
   * @param object $max [optional] The max date
   * @return 
   */
  function addUnavailability($min, $max = null) {
    $min = mbDate($min);
    
    $max = $max ? mbDate($max) : $min;
    
    if ($min > $max) {
      list($min, $max) = array($max, $min);
    }
    
    while ($min <= $max) {
      $this->unavailabilities[$min] = true;
      $min = mbDate("+1 DAY", $min);
    }
  }
  
  /**
   * Tell wether given day is active in planning
   * @param date $day ISO date
   * @return bool
   */
  function isDayActive($day) {
  	return in_range($day, $this->date_min_active, $this->date_max_active);
  }

  /**
   * @param object $day The label's day
   * @param object $text The label
   * @param object $detail [optional] Details about the label
   * @param object $color [optional] The label's color
   * @return 
   */
  function addDayLabel($day, $text, $detail = null, $color = null) {
    $this->day_labels[mbDate($day)] = array(
      "text"   => $text, 
      "detail" => $detail, 
      "color"  => $color,
    );
  }
}
