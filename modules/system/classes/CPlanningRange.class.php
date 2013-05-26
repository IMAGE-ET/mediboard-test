<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage System
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * Weekly planning range
 */
class CPlanningRange {
  public $guid;
  public $internal_id;
  
  public $title;
  public $type;
  
  public $start;
  public $end;
  public $length;
  public $day;
  
  public $hour;
  public $minutes;
  
  public $width;
  public $offset;
  public $color;
  public $important;

  /**
   * Range constructor
   *
   * @param string $guid      GUID
   * @param string $date      Date
   * @param int    $length    Length
   * @param string $title     Title
   * @param null   $color     Color
   * @param null   $css_class CSS class
   */
  function __construct ($guid, $date, $length = 0, $title = "", $color = null, $css_class = null) {
    $this->guid = $guid;
    $this->internal_id = "CPlanningRange-".uniqid();
    
    $this->start = $date;
    $this->length = $length;
    $this->title = CMbString::htmlEntities($title);
    $this->color = $color;
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

  /**
   * Check range collision
   *
   * @param CPlanningRange $range The range to test colission with
   *
   * @return bool
   */
  function collides(self $range) {
    if ($range == $this || $this->length == 0 || $range->length == 0) {
      return false;
    }
    
    return ($range->start <  $this->end   && $range->end >  $this->end  ) ||
           ($range->start <  $this->start && $range->end >  $this->start) ||
           ($range->start >= $this->start && $range->end <= $this->end  );
  }
}
