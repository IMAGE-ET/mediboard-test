<?php

/**
 * CPlanningWeek class
 *
 * @category Ssr
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:\$
 * @link     http://www.mediboard.org
 */


/**
 * Class CPlanningWeek
 */
class CPlanningWeek {
  var $guid = null;
  var $title = null;
  
  var $date        = null;
  var $selectable  = null;
  var $height      = null;
  var $large       = null;
  var $adapt_range = null;
  
  var $date_min    = null; // Monday
  var $date_max    = null; // Sunday
  
  var $date_min_active = null;
  var $date_max_active = null;

  var $allow_superposition = false;
  
  var $hour_min = "09";
  var $hour_max = "16";
  var $hour_divider = 6;
  var $maximum_load = 6;
  var $has_load  = false;
  var $has_range = false;
  var $show_half = false;
  var $dragndrop = 0;
  var $resizable = 0;
  var $no_dates  = 0;
  
  var $events = array();
  var $events_sorted = array();
  var $ranges = array();
  
  var $pauses = array("08", "12", "16");
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

  /**
   * constructor
   *
   * @param string $date        current date in the planning
   * @param null   $date_min    min date of the planning
   * @param null   $date_max    max
   * @param int    $nb_days     nb of day in the planning
   * @param bool   $selectable  is the planning selectable
   * @param string $height      [optional] height of the planning, default : auto
   * @param bool   $large       [optional] is the planning a large one
   * @param bool   $adapt_range [optional] can the planning adapt the range
   */
  function __construct($date, $date_min = null, $date_max = null, $nb_days = 7, $selectable = false, $height = "auto", $large = false, $adapt_range = false) {
    $this->date = $date;
    $this->selectable = $selectable;
    $this->height = $height ? $height : "auto";
    $this->large = $large;
    $this->nb_days = $nb_days;
    $this->adapt_range = $adapt_range;
    
    if (is_int($date) || is_int($date_min) || is_int($date_max)) {
      $this->no_dates = true;
      $this->date_min = $this->date_min_active = $this->_date_min_planning = $date_min;
      $this->date_max = $this->date_max_active = $this->_date_max_planning = $date_max;
      
      for ($i = 0 ; $i < $this->nb_days ; $i++) {
        $this->days[$i] = array();
        $this->load_data[$i] = array();
      }
    }
    else {
      $days = array("monday", "tuesday", "wednesday", "thursday", "friday", "saturday", "sunday");
      
      $last_day = $days[$this->nb_days - 1];
      
      
      $monday = CMbDT::date("last monday", CMbDT::date("+1 day", $this->date));
      $sunday = CMbDT::date("next $last_day", CMbDT::date("-1 DAY", $this->date));
      
      if (CMbDT::daysRelative($monday, $sunday) > 7) {
        $sunday = CMbDT::date("-7 DAYS", $sunday);
      }
      
      $this->date_min_active = $date_min ? max($monday, CMbDT::date($date_min)) : $monday;
      $this->date_max_active = $date_max ? min($sunday, CMbDT::date($date_max)) : $sunday;
      
      $this->date_min = $monday;
      $this->date_max = $sunday;
      
      // Days period
      for ($i = 0; $i < $this->nb_days; $i++) {
        $_day = CMbDT::date("+$i day", $monday);
        $this->days[$_day] = array();
        $this->load_data[$_day] = array();
      }
      
      $this->_date_min_planning = reset(array_keys($this->days));
      $this->_date_max_planning = end(array_keys($this->days));
    }
  }

  /**
   * add an event to the present planning
   *
   * @param CPlanningEvent $event an event
   *
   * @return null
   */
  function addEvent(CPlanningEvent $event) {
    if ($event->day < $this->date_min || $event->day > $this->date_max) {
      return;
    }
      
    if ( $event->day < $this->date_min_active || $event->day > $this->date_max_active) {
      $event->disabled = true;
    }
    
    $this->events[] = $event;
    $this->days[$event->day][] = $event;
    $this->events_sorted[$event->day][$event->hour][] = $event;
    
    $colliding = array($event);
    foreach ($this->days[$event->day] as $_event) {
      if ($_event->collides($event)) {
        $colliding[] = $_event;
        if (count($this->events_sorted[$_event->day][$_event->hour])) {
          foreach ($this->events_sorted[$_event->day][$_event->hour] as $__event) {
            if ($__event === $_event || $__event === $event) {
              continue;
            }
            $min = min($event->start, $_event->start);
            $max = max($event->end  , $_event->end);
            
            if (($__event->start < $min && $__event->end <= $min) || ($__event->start >= $max && $__event->end > $max)) {
              continue;
            } 
            
            $colliding[] = $__event;
          }
        }
      }
    }
    
    $event->offset = 0.0;
    $event->width = 1.0;
    
    $count = count($colliding);
    
    if ($count) {
      foreach ($colliding as $_key => $_event) {
        $_event->width = 1 / $count;
        $_event->offset = $_key * $_event->width;
      }
    }
  }

  /**
   * rearrange the current list of events in a optimized way
   *
   * @return null
   */
  function rearrange() {
    $events = array();
    //days
    foreach ($this->events_sorted as $_events_by_day) {
      $intervals = array();
      // tab
      foreach ($_events_by_day as $_events_by_hour) {
        foreach ($_events_by_hour as $_event) {

          //used as background, skip the rearrange
          if ($_event->below) {
            $_event->width = .9;
            $_event->offset = .1;
            continue;
          }

          $intervals[$_event->internal_id] = array(
            "lower" => $_event->start,
            "upper" => $_event->end
          );
          $events[$_event->internal_id] = $_event;
        }
      }
      $uncollided = array();
      $lines = CMbRange::rearrange($intervals, true, $uncollided);

      $lines_count = count($lines);
      foreach ($lines as $_line_number => $_line) {
        foreach ($_line as $_event_id) {
          $event = $events[$_event_id]; //get the event
          $event->height = $_line_number;
          //global = first line
          $event->width = (1 / $lines_count);
          $event->offset = ($_line_number / $lines_count);
          if ($this->allow_superposition) {
            $event->offset+=.05;
          }

          if ($lines_count == 1 && $this->allow_superposition) {
            $event->width =  $event->width-.1;
          }

          //the line is not the first
          if ($_line_number >= 1 && $this->allow_superposition) {
            $event->width = (1 / ($lines_count))+0.05;
            $event->offset = ($_line_number / $lines_count)-.1;
          }

          // lines uncollided
          //TODO: fix collisions problems
          if ((in_array($event->internal_id, array_keys($uncollided))) && ($_line_number < ($lines_count-1)) && !$event->below) {
            //$event->width = (($lines_count - ($_line_number)) / $lines_count);
            //$event->width = ($_line_number == 0) ? $event->width-0.1 :$event->width +.05;
          }
        }
      }
    }
  }

  /**
   * Add a range to the planning
   *
   * @param CPlanningRange $range a range
   *
   * @return null
   */
  function addRange(CPlanningRange $range) {
    if ($range->day < $this->date_min || $range->day > $this->date_max) {
      return;
    }
    
    $this->has_range = true;
    
    $this->ranges[] = $range;
    $this->ranges_sorted[$range->day][$range->hour][] = $range;
    
    $range->offset = 0.0;
    $range->width = 1.0;
  }

  /**
   * Show the actual time in the planning
   *
   * @param string $color show the actual time
   *
   * @return null
   */
  function showNow($color = "red") {
    $this->addEvent(new CPlanningEvent(null, CMbDT::dateTime(), null, null, $color, null, "now"));
  }
  
  /**
   * Add an unavailability event to the planning
   *
   * @param object $min The min date
   * @param object $max [optional] The max date
   *
   * @return void
   */
  function addUnavailability($min, $max = null) {
    $min = CMbDT::date($min);
    
    $max = $max ? CMbDT::date($max) : $min;
    
    if ($min > $max) {
      list($min, $max) = array($max, $min);
    }
    
    while ($min <= $max) {
      $this->unavailabilities[$min] = true;
      $min = CMbDT::date("+1 DAY", $min);
    }
  }
  
  /**
   * Tell wether given day is active in planning
   *
   * @param string|object $day ISO date
   *
   * @return bool
   */
  function isDayActive($day) {
    return CMbRange::in($day, $this->date_min_active, $this->date_max_active);
  }

  /**
   * Add a label to a day
   *
   * @param object $day     The label's day
   * @param object $text    The label
   * @param object $detail  [optional] Details about the label
   * @param object $color   [optional] The label's color
   * @param string $onclick [optional] a function for the onclick event
   *
   * @return void
   */
  function addDayLabel($day, $text, $detail = null, $color = null, $onclick = null) {
    $this->day_labels[$this->no_dates ? $day : CMbDT::date($day)][] = array(
      "text"   => $text, 
      "detail" => $detail, 
      "color"  => $color,
      "onclick" => $onclick
    );
  }
  
  /**
   * Add a load event
   *
   * @param CPlanningEvent|string $start  an event
   * @param integer               $length [optional] length of the load
   *
   * @return null
   */
  function addLoad($start, $length = null) {
    $this->has_load = true;
    
    if ($start instanceof CPlanningEvent) {
      $event = $start;
      if ($this->no_dates) {
        $day = $event->day;
      }
      else {
        $day = CMbDT::date($event->day);
      }
    }
    else {
      if ($this->no_dates) {
        $day = $start;
      }
      else {
        $day = CMbDT::date($start);
      }
      $event = new CPlanningEvent(null, $start, $length);
    }
    
    $start = $event->start;
    $end   = $event->end;
    
    $div_size = 60 / $this->hour_divider;
    
    $min = round(CMbDT::minutesRelative($day, $start) / $div_size) - 1;
    $max = round(CMbDT::minutesRelative($day, $end)   / $div_size) + 1;

    for ($i = $min; $i <= $max; $i++) {
      $div_min = CMbDT::dateTime("+".($i*$div_size)." MINUTES", $day);
      $div_max = CMbDT::dateTime("+".(($i+1)*$div_size)." MINUTES", $day);
      
      // FIXME: ameliorer ce calcul
      if ($div_min >= $start && $div_min < $end) {
        $hour = CMbDT::transform(null, $div_min, "%H");
        $min = CMbDT::transform(null, $div_min, "%M");
        
        if (!isset($this->load_data[$day][$hour][$min])) {
          $this->load_data[$day][$hour][$min] = 0;
        }
        
        $this->load_data[$day][$hour][$min]++;
      }
    }
  }
}
