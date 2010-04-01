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
  
  var $title  = null;
  var $start  = null;
  var $end    = null;
  var $length = null;
  var $day    = null;
  
  var $hour   = null;
  var $minutes = null;
  
  var $width = null;
  var $offset = null;
  var $color = null;
  
  function __construct ($guid, $date, $length, $title = "", $color = "grey") {
    $this->guid = $guid;
    
    $this->start = $date;
    $this->length = $length;
    $this->end = mbDateTime("+{$this->length} MINUTES", $date);
    
    $this->title = htmlentities($title);
    $this->color = $color;
    
    $this->day = mbDate($date);
    $this->hour = mbTransformTime(null, $date, "%H");
    $this->minutes = mbTransformTime(null, $date, "%M");
  }
  
  function collides(self $event) {
    if ($event == $this) return false;
    
    return ($event->start <  $this->end   && $event->end >  $this->end  ) || 
           ($event->start <  $this->start && $event->end >  $this->start) || 
           ($event->start >= $this->start && $event->end <= $this->end  );
  }
}
