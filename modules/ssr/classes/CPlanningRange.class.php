<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CPlanningRange  {
  var $guid        = null;
  var $internal_id = null;
  
  var $title     = null;
  
  var $type      = null;
  
  var $start     = null;
  var $end       = null;
  var $length    = null;
  var $day       = null;
  
  var $hour      = null;
  var $minutes   = null;
  
  var $width     = null;
  var $offset    = null;
  var $color     = null;
  var $important = null;
  
  function __construct ($guid, $date, $length = 0, $title = "", $color = null, $css_class = null) {
    $this->guid = $guid;
    $this->internal_id = "CPlanningRange-".uniqid();
    
    $this->start = $date;
    $this->length = $length;
    $this->title = htmlentities($title);
    $this->color = $color;
    $this->css_class = is_array($css_class) ? implode(" ", $css_class) : $css_class;
    
    if (preg_match("/[0-9]+ /", $this->start)) {
      $parts = split(" ", $this->start);
      $this->end = "{$parts[0]} ".mbTime("+{$this->length} MINUTES", $parts[1]);
      $this->day = $parts[0];
      $this->hour = mbTransformTime(null, $parts[1], "%H");
      $this->minutes = mbTransformTime(null, $parts[1], "%M");
    }
    else {
      $this->day = mbDate($date);
      $this->end = mbDateTime("+{$this->length} MINUTES", $date);
      $this->hour = mbTransformTime(null, $date, "%H");
      $this->minutes = mbTransformTime(null, $date, "%M");
    }
  }
  
  function collides(self $event) {
    if ($event == $this || $this->length == 0 || $event->length == 0) return false;
    
    return ($event->start <  $this->end   && $event->end >  $this->end  ) || 
           ($event->start <  $this->start && $event->end >  $this->start) || 
           ($event->start >= $this->start && $event->end <= $this->end  );
  }
}
