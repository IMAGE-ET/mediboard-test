<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CPlanningEvent  {
  var $guid   = null;
  var $internal_id = null;
  
  var $title  = null;
  var $start  = null;
  var $end    = null;
  var $length = null;
  var $day    = null;
  var $draggable = false;
  
  var $hour   = null;
  var $minutes = null;
  
  var $width = null;
  var $offset = null;
  var $color = null;
	var $important = null;
  
  function __construct ($guid, $date, $length = 0, $title = "", $color = null, $important = true, $css_class = null) {
    if(!$color){
    	 $color = "#999";
    }
		$this->guid = $guid;
		
		$this->internal_id = "CPlanningEvent-".uniqid();
    
    $this->start = $date;
    $this->length = $length;
    $this->end = mbDateTime("+{$this->length} MINUTES", $date);
    
    $this->title = htmlentities($title);
    $this->color = $color;
    $this->important = $important;
		$this->css_class = is_array($css_class) ? join(" ", $css_class) : $css_class;
		
		
		$this->day = mbDate($date);
    $this->hour = mbTransformTime(null, $date, "%H");
    $this->minutes = mbTransformTime(null, $date, "%M");
  }
  
  function collides(self $event) {
    if ($event == $this || $this->length == 0 || $event->length == 0) return false;
    
    return ($event->start <  $this->end   && $event->end >  $this->end  ) || 
           ($event->start <  $this->start && $event->end >  $this->start) || 
           ($event->start >= $this->start && $event->end <= $this->end  );
  }
}
