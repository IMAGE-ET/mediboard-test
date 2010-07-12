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
	var $adapt_range = null;
	
  var $date_min = null; // Monday
  var $date_max = null; // Sunday
  
  var $date_min_active = null;
  var $date_max_active = null;
  
  var $hour_min = "09";
  var $hour_max = "18";
  var $hour_divider = 6;
  var $maximum_load = 6;
  var $has_load = false;
  
  var $events = array();
  var $pauses = array("07", "12", "17");
  var $unavailabilities = array();
  var $day_labels = array();
  var $load_data = array();
  
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

  function __construct($date, $date_min = null, $date_max = null, $nb_days = 7, $selectable = false, $height = "auto", $large = false, $adapt_range = false) {
		$this->date = $date;
    $this->selectable = $selectable;
		$this->height = $height ? $height : "auto";
		$this->large = $large;
		$this->nb_days = $nb_days;
		$this->adapt_range = $adapt_range;
		
		$days = array("monday", "tuesday", "wednesday", "thursday", "friday", "saturday", "sunday");
		$last_day = $days[$this->nb_days - 1];
		
    $monday = mbDate("last monday", mbDate("+1 day", $this->date));
    $sunday = mbDate("next $last_day", mbDate("-1 DAY", $this->date));
    
    if (mbDaysRelative($monday, $sunday) > 7) {
      $sunday = mbDate("-7 DAYS", $sunday);
    }
		
		$this->date_min_active = $date_min ? max($monday, mbDate($date_min)) : $monday;
		$this->date_max_active = $date_max ? min($sunday, mbDate($date_max)) : $sunday;
    
    $this->date_min = $monday;
    $this->date_max = $sunday;
    
    // Days period
    for ($i = 0; $i < $this->nb_days; $i++) {
      $_day = mbDate("+$i day", $monday);
      $this->days[$_day] = array();
      $this->load_data[$_day] = array();
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
    $this->events_sorted[$event->day][$event->hour][] = $event;
    
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
  
  function showNow($color = "red") {
    $this->addEvent(new CPlanningEvent(null, mbDateTime(), null, null, $color, null, "now"));
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
   */
  function addDayLabel($day, $text, $detail = null, $color = null) {
    $this->day_labels[mbDate($day)][] = array(
      "text"   => $text, 
      "detail" => $detail, 
      "color"  => $color,
    );
  }
  
  /**
   * Add a load event
   * @param CPlanningEvent|date $start
   * @param integer $length [optional]
   */
  function addLoad($start, $length = null) {
    $this->has_load = true;
    
    if ($start instanceof CPlanningEvent) {
      $event = $start;
      $day = mbDate($event->day);
    }
    else {
      $day = mbDate($start);
      $event = new CPlanningEvent(null, $start, $length);
    }
    
    $start = $event->start;
    $end   = $event->end;
    
    $div_size = 60 / $this->hour_divider;
    
    for($i = 0; $i < $this->hour_divider * 24; $i++) {
      $div_min = mbDateTime("+".($i*$div_size)." MINUTES", $day);
      $div_max = mbDateTime("+".(($i+1)*$div_size)." MINUTES", $day);
      
      // FIXME: ameliorer ce calcul
      if ($div_min >= $start && $div_min < $end) {
        $hour = mbTransformTime(null, $div_min, "%H");
        $min = mbTransformTime(null, $div_min, "%M");
        
        if (!isset($this->load_data[$day][$hour][$min])) {
          $this->load_data[$day][$hour][$min] = 0;
        }
        $this->load_data[$day][$hour][$min]++;
      }
    }
  }
}
