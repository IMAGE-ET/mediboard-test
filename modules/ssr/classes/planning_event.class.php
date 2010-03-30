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
  
  function __construct ($guid, $date, $length, $title = "") {
    $this->guid = $guid;
    
    $this->start = $date;
    $this->length = $length;
    $this->end = mbDateTime("+{$this->length} MINUTES", $date);
    
    $this->title = htmlentities($title);
    
    //$iso_date = mbTransformTime(null, $date, "%c");
    //preg_match("/^(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2}):(\d{2})/i", $iso_date, $match);
    
    $this->day = mbDate($date);
    $this->hour = mbTransformTime(null, $date, "%H");
    $this->minutes = mbTransformTime(null, $date, "%M");
  }
  
  function __toString() {
    $str = mbDateToLocale($this->start);
    $str .= " - ".mbDateToLocale(mbDateTime("+{$this->length} SECONDS", $this->start));
    if ($this->title) {
      $str .= " - ".$this->title;
    }
    return $str;
  }
}
