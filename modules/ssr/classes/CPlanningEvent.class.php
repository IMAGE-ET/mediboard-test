<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CPlanningEvent  {
  var $guid        = null;
  var $internal_id = null;
  
  var $title     = null;
  var $icon      = null;
  var $icon_desc = null;
  
  var $type      = null;
  var $plage     = array();
  var $menu      = array();
  
  var $start     = null;
  var $end       = null;
  var $length    = null;
  var $day       = null;
  var $below     = false;
  var $draggable = false;
  var $resizable = false;
  var $disabled  = false;
  
  var $hour      = null;
  var $minutes   = null;
  var $hour_divider = null;
  var $width     = null;
  var $offset    = null;
  var $color     = null;
  var $height    = null;
  var $useHeight = null;
  var $important = null;
  
  function __construct ($guid, $date, $length = 0, $title = "", $color = null, $important = true, $css_class = null, $draggable_guid = null, $html_escape = true) {
    if (!$color) {
      $color = "#999";
    }
    
    $this->guid = $guid;
    $this->draggable_guid = $draggable_guid;
    
    $this->internal_id = "CPlanningEvent-".uniqid();
    
    $this->start = $date;
    $this->length = $length;
    $this->title = $html_escape ? CMbString::htmlEntities($title) : $title;
    $this->color = $color;
    $this->important = $important;
    $this->css_class = is_array($css_class) ? implode(" ", $css_class) : $css_class;
    
    if (preg_match("/[0-9]+ /", $this->start)) {
      $parts = split(" ", $this->start);
      $this->end = "{$parts[0]} ".CMbDT::time("+{$this->length} MINUTES", $parts[1]);
      $this->day = $parts[0];
      $this->hour = CMbDT::transform(null, $parts[1], "%H");
      $this->minutes = CMbDT::transform(null, $parts[1], "%M");
    }
    else {
      $this->day = CMbDT::date($date);
      $this->end = CMbDT::dateTime("+{$this->length} MINUTES", $date);
      $this->hour = CMbDT::transform(null, $date, "%H");
      $this->minutes = CMbDT::transform(null, $date, "%M");
    }
  }
  
  function collides(self $event) {
    if ($event == $this || $this->length == 0 || $event->length == 0) {
      return false;
    }
    
    return ($event->start < $this->end && $event->end > $this->start);
  }
  
  function addMenuItem($type, $title){
    $this->menu[] = array(
      "class" => $type, 
      "title" => $title,
    );
  }
}
